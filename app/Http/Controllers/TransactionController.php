<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Shift;
use App\Models\Pump;
use App\Models\Tank;
use App\Models\Client;
use App\Models\ClientRefueling;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    // عرض كل العمليات
    public function index(Request $request)
    {
        $query = Transaction::with(['shift.user', 'pump.tank.fuel', 'nozzle', 'client.fuelPrices', 'media', 'clientRefuelings'])->latest();

        // تصفية حسب العميل
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        // تصفية حسب الموظف
        if ($request->filled('user_id')) {
            $query->whereHas('shift', function ($q) use ($request) {
                $q->where('user_id', $request->user_id);
            });
        }

        // تصفية حسب التاريخ من
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        // تصفية حسب التاريخ إلى
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $transactions = $query->get();

        // حساب المبلغ الإجمالي والسعر الفعلي
        $transactions->transform(function ($t) {
            $fuel = $t->pump->tank->fuel;
            $fuelId = $fuel->id ?? null;
            $price = $fuel->price_per_liter ?? 0;
            $totalAmount = 0;

            // لو فيه client_refueling (آجل)، ناخد السعر والمبلغ منه
            $clientRefueling = $t->clientRefuelings->first();
            if ($clientRefueling) {
                $price = $clientRefueling->price_per_liter;
                $totalAmount = $clientRefueling->total_amount;
            } else {
            // تحديد السعر المبدئي: سعر الوقود أو سعر الموظف
            $shiftUserPrice = $t->shift->user->fuel_price ?? null;
            if ($shiftUserPrice) {
                 $price = $shiftUserPrice;
            }

            // لو عميل بس مش آجل، نستخدم سعر العميل المخصص
            if ($t->client && !is_null($fuelId)) {
                $customPrice = $t->client->fuelPrices->firstWhere('fuel_id', $fuelId);
                if ($customPrice) {
                    $price = $customPrice->price_per_liter;
                } elseif (!is_null($t->client->fuel_price_per_liter)) {
                    // هنا ممكن نقرر هل سعر العميل يغلب سعر الموظف؟ غالباً آه
                    $price = $t->client->fuel_price_per_liter;
                }
            }
            $totalAmount = ($t->credit_liters + $t->cash_liters) * $price;
            }

            $t->effective_price_per_liter = $price;
            $t->total_amount = $totalAmount;
            return $t;
        });

        $clients = Client::all();
        $users = \App\Models\User::all();

        return view('transactions.index', compact('transactions', 'clients', 'users'));
    }

    // نموذج إنشاء عملية جديدة
    public function create()
    {
        $user = Auth::user();

        // 🔹 التانكات المتاحة (نوع الوقود) حسب صلاحيات المستخدم
        if ($user->hasRole('admin')) {
            $tanks = Tank::with('fuel')->get();
        } else {
            $userPumpIds = $user->getPermissionNames()
                ->filter(fn($perm) => str_starts_with($perm, 'use_pump_'))
                ->map(fn($perm) => (int) str_replace('use_pump_', '', $perm));

            $tankIds = Pump::whereIn('id', $userPumpIds)->pluck('tank_id')->unique();
            $tanks = Tank::with('fuel')->whereIn('id', $tankIds)->get();
        }

        // 🔹 الشيفتات
        if ($user->hasRole('admin')) {
            $shifts = Shift::with('user')->latest()->get();
        } else {
            $shifts = Shift::with('user')
                ->where('user_id', Auth::id())
                ->whereNull('end_time')
                ->latest()
                ->get();
        }

        // 🔹 العملاء (النشطين فقط)
        $clients = Client::where('is_active', true)->get();

        // 🔹 تحديد الشيفت الحالي
        $shift = $shifts->first();

        return view('transactions.create', compact('clients', 'tanks', 'shift', 'shifts'));
    }

    // حفظ العملية
    public function store(Request $request)
    {
        $validated = $request->validate([
            'shift_id' => 'required|exists:shifts,id',
            'tank_id' => 'required|exists:tanks,id',
            'credit_liters' => 'required|numeric|min:0.01',
            'vehicle_number' => 'nullable|string|max:50',
            'captured_images_data' => 'required|string',
            'notes' => 'nullable|string|max:500',
            'client_id' => 'nullable|exists:clients,id',
        ]);

        // 🔹 جلب بيانات التانك ونوع الوقود وسعر اللتر
        $tank = Tank::with(['fuel', 'pumps'])->findOrFail($validated['tank_id']);
        $fuel = $tank->fuel;
        $pump = $tank->pumps->first();
        
        // جلب الشيفت والمستخدم لتحديد السعر
        $shift = Shift::with('user')->findOrFail($validated['shift_id']);
        $userFuelPrice = $shift->user->fuel_price;

        // تحديد السعر الأساسي: سعر الموظف لو موجود، وإلا سعر الوقود الأصلي
        $fuelPrice = $userFuelPrice ?? ($fuel->price_per_liter ?? 0);
        $fuelId = $fuel->id ?? null;

        $client = null;
        $pricePerLiter = $fuelPrice;

        if (!empty($validated['client_id'])) {
            $client = Client::with('fuelPrices')->findOrFail($validated['client_id']);

            if (!is_null($fuelId)) {
                $customPrice = $client->fuelPrices->firstWhere('fuel_id', $fuelId);
                if ($customPrice) {
                    $pricePerLiter = $customPrice->price_per_liter;
                }
            }

            if ($pricePerLiter === $fuelPrice && !is_null($client->fuel_price_per_liter)) {
                $pricePerLiter = $client->fuel_price_per_liter;
            }
        }

        // 🔹 حساب المجموع الكلي
        $totalAmount = $validated['credit_liters'] * $pricePerLiter;

        // 🔹 إنشاء العملية
        $transaction = Transaction::create([
            'shift_id' => $validated['shift_id'],
            'pump_id' => $pump ? $pump->id : null,
            'nozzle_id' => $pump ? optional($pump->nozzles()->first())->id : null,
            'client_id' => $validated['client_id'] ?? null,
            'vehicle_number' => $validated['vehicle_number'] ?? null,
            'credit_liters' => $validated['credit_liters'],
            'notes' => $validated['notes'] ?? null,
            'operation_type' => 'آجل',
            'total_amount' => $totalAmount,
        ]);

        // 🔹 حفظ الصور الملتقطة بالكاميرا
        if ($request->filled('captured_images_data')) {
            $capturedImages = json_decode($request->input('captured_images_data'), true);
            
            if (is_array($capturedImages)) {
                foreach ($capturedImages as $index => $imageData) {
                    try {
                        $imageData = preg_replace('/^data:image\/\w+;base64,/', '', $imageData);
                        $imageData = base64_decode($imageData);
                        
                        if ($imageData === false) {
                            continue; // Skip if base64 decode fails
                        }
                        
                        $tempFile = tempnam(sys_get_temp_dir(), 'captured_transaction_image_' . $index . '_');
                        
                        if ($tempFile && file_put_contents($tempFile, $imageData) !== false) {
                            $transaction->addMedia($tempFile)
                                ->usingFileName('captured_transaction_photo_' . ($index + 1) . '_' . time() . '.jpg')
                                ->toMediaCollection('transactions', 'public');
                                
                            // Clean up temporary file if it exists
                            if (file_exists($tempFile)) {
                                unlink($tempFile);
                            }
                        }
                    } catch (\Exception $e) {
                        // Log error but continue with other images
                        Log::error('Error processing captured image: ' . $e->getMessage());
                        continue;
                    }
                }
            }
        }

        // 🔹 لو العملية تخص عميل آجل
        if (!empty($validated['client_id'])) {
            ClientRefueling::create([
                'client_id' => $client->id,
                'shift_id' => $validated['shift_id'],
                'transaction_id' => $transaction->id,
                'liters' => $validated['credit_liters'],
                'price_per_liter' => $pricePerLiter,
                'total_amount' => $totalAmount,
            ]);

            $client->update([
                'liters_drawn' => $client->liters_drawn + $validated['credit_liters'],
                'total_price' => $client->total_price + $totalAmount,
                'rest' => $client->amount_paid - ($client->total_price + $totalAmount),
            ]);
        }

        return redirect()->route('transactions.create')
            ->with('success', 'تم حفظ العملية والصورة بنجاح ✅');
    }

    // حذف العملية
    public function destroy(Transaction $transaction)
    {
        // حذف الصور تلقائيًا من Spatie
        $transaction->clearMediaCollection('transactions');

        $transaction->delete();

        return redirect()->back()->with('success', 'تم حذف العملية بنجاح 🗑');
    }
}