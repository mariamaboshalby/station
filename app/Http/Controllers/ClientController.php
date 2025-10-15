<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Pump;
use App\Models\Transaction;
use Illuminate\Http\Request;

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

        // ✅ لو المستخدم اختار طلمبة
        if ($request->filled('pump_id')) {
            $pump = Pump::with('tank.fuel')->findOrFail($request->pump_id);

            // 🟢 آخر عملية على الطلمبة
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

        // الباقي
        $rest = $request->amount_paid - $totalPrice;

        // إنشاء العميل
        Client::create([
            'pump_id' => $pump?->id,   // nullable
            'name' => $request->name,
            'liters_drawn' => $litersDrawn,
            'total_price' => $totalPrice,
            'amount_paid' => $request->amount_paid,
            'rest' => $rest,
        ]);

        return redirect()->route('clients.index')
            ->with('success', 'تم إضافة العميل بنجاح');
    }
    public function transactions($id)
    {
        $client = Client::findOrFail($id);

        $transactions = $client->transactions()
            ->with(['pump', 'shift'])
            ->latest()
            ->get();

        // إجماليات
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
        // تحديث المبلغ المدفوع
        $client->amount_paid += $request->added_amount;
        // إعادة حساب الباقي
        $client->rest = $client->amount_paid - $client->total_price;
        $client->save();

        return redirect()->route('clients.index')->with('success', 'تمت إضافة المبلغ بنجاح.');
    }
public function search(Request $request)
{
    $term = $request->get('term');

    $clients = Client::query()
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

}
