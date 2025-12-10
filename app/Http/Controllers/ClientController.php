<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Pump;
use App\Models\Transaction;
use App\Models\TreasuryTransaction;
use Illuminate\Http\Request;
use Mpdf\Mpdf;

class ClientController extends Controller
{
    // عرض كل العملاء
    public function index()
    {
        $clients = Client::with('pump')->latest()->paginate(10);
        return view('clients.index', compact('clients'));
    }

    // صفحة إضافة عميل جديد
    public function create()
    {
        $pumps = Pump::with('tank.fuel')->get(); // نجيب الطلمبات مع السعر
        return view('clients.create', compact('pumps'));
    }

    // حفظ العميل الجديد
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'amount_paid' => 'required|numeric|min:0',
            'pump_id' => 'nullable|exists:pumps,id',
        ]);

        $pump = null;
        $litersDrawn = 0;
        $pricePerLiter = 0;
        $totalPrice = 0;

        if ($request->filled('pump_id')) {
            $pump = Pump::with('tank.fuel')->findOrFail($request->pump_id);

            $lastTransaction = Transaction::where('pump_id', $pump->id)
                ->latest()
                ->first();

            if (!$lastTransaction) {
                return back()->withErrors(['pump_id' => 'لا توجد عملية مسجلة لهذه الطلمبة بعد.']);
            }

            $litersDrawn = $lastTransaction->liters_dispensed;
            $pricePerLiter = $pump->tank->fuel->price_per_liter;
            $totalPrice = $litersDrawn * $pricePerLiter;
        }

        $rest = $request->amount_paid - $totalPrice;

        Client::create([
            'pump_id' => $pump?->id,
            'name' => $request->name,
            'liters_drawn' => $litersDrawn,
            'total_price' => $totalPrice,
            'amount_paid' => $request->amount_paid,
            'rest' => $rest,
        ]);

        return redirect()->route('clients.index')
            ->with('success', 'تم إضافة العميل بنجاح');
    }

    // تعديل بيانات العميل
    public function edit($id)
    {
        $client = Client::findOrFail($id);
        $pumps = Pump::with('tank.fuel')->get();
        return view('clients.edit', compact('client', 'pumps'));
    }

    // حفظ التعديلات
    // حفظ التعديلات (تعديل الاسم فقط)
public function update(Request $request, $id)
{
    $client = Client::findOrFail($id);

    $request->validate([
        'name' => 'required|string|max:255',
    ]);

    $client->update([
        'name' => $request->name,
    ]);

    return redirect()->route('clients.index')->with('success', 'تم تعديل اسم العميل بنجاح');
}

    // حذف العميل
    public function destroy($id)
    {
        $client = Client::findOrFail($id);
        $client->delete();

        return redirect()->route('clients.index')->with('success', 'تم حذف العميل بنجاح');
    }

    public function transactions($id)
    {
        $client = Client::findOrFail($id);

        $transactions = $client->transactions()
            ->with(['pump', 'shift'])
            ->latest()
            ->get();

        $totalLiters = $transactions->sum(fn($t) => $t->cash_liters + $t->credit_liters);
        $totalAmount = $transactions->sum('total_amount');

        return view('clients.transactions', compact('client', 'transactions', 'totalLiters', 'totalAmount'));
    }

    public function addPaymentForm($id)
    {
        $client = Client::findOrFail($id);
        return view('clients.add-payment', compact('client'));
    }

    public function addPayment(Request $request, $id)
    {
        $client = Client::findOrFail($id);

        $request->validate([
            'added_amount' => 'required|numeric|min:0.01',
        ]);

        $client->amount_paid += $request->added_amount;
        $client->rest = $client->amount_paid - $client->total_price;
        $client->save();

        // تسجيل الدفعة كإيراد في الخزنة
        TreasuryTransaction::create([
            'type' => 'income',
            'category' => 'دفعة عميل',
            'amount' => $request->added_amount,
            'description' => 'دفعة من العميل: ' . $client->name,
            'transaction_date' => now(),
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('clients.index')->with('success', 'تمت إضافة المبلغ بنجاح.');
    }

    public function search(Request $request)
    {
        $term = $request->get('term');

        $clients = Client::query()
            ->where('is_active', true)
            ->where('name', 'LIKE', "%{$term}%")
            ->take(10)
            ->get(['id', 'name']);

        return response()->json($clients);
    }

    public function show($id)
    {
        $client = Client::findOrFail($id);
        return response()->json($client);
    }

    // تفعيل/تعطيل حساب العميل
    public function toggleStatus($id)
    {
        $client = Client::findOrFail($id);
        
        // تبديل الحالة
        $client->is_active = !$client->is_active;
        $client->save();

        $status = $client->is_active ? 'تم تفعيل' : 'تم تعطيل';
        
        return redirect()->route('clients.index')
            ->with('success', $status . ' حساب العميل بنجاح');
    }

    public function transactionsPdf($id)
    {
        $client = Client::findOrFail($id);
        $transactions = $client->refuelings()
            ->with(['shift.user', 'transaction.nozzle'])
            ->get();
        
        $html = view('clients.transactions-pdf', compact('client', 'transactions'))->render();
        
        $mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4']);
        $mpdf->WriteHTML($html);
        
        return $mpdf->Output('client_' . $client->id . '_' . now()->format('Y-m-d') . '.pdf', 'D');
    }
}
