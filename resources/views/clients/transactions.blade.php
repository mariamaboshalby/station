@can('show clients')
    @extends('layouts.app')

    @section('content')
        <div class="" dir="rtl">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="text-primary fw-bold mb-0">
                    حساب العميل: {{ $client->name }}
                </h3>
                <a href="{{ route('clients.transactions.pdf', $client->id) }}" class="btn btn-danger">
                    <i class="fas fa-file-pdf me-2"></i> تصدير PDF
                </a>
            </div>

            <div class="card shadow overflow-x-auto">
                <div class="card-header bg-success text-white fw-bold">
                    سجل التفويلات
                </div>
                <div class="card-body">
                    <table class="table table-bordered text-center align-middle">
                        <thead class="table-success">
                            <tr>
                                <th>التاريخ</th>
                                <th>الشيفت</th>
                                <th>رقم العربية</th>
                                <th>المسدس</th>
                                <th>عدد اللترات</th>
                                <th>سعر اللتر</th>
                                <th>الإجمالي</th>
                                <th>صورة الإيصال</th>
                                <th>الإجراء</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($client->refuelings as $r)
                                <tr>
                                    <td>{{ $r->created_at->format('Y-m-d H:i') }}</td>
                                    <td>{{ $r->shift->user->name }}</td>
                                    <td class="fw-bold">{{ $r->transaction->vehicle_number ?? '-' }}</td>
                                    <td>
                                        @if($r->transaction && $r->transaction->nozzle)
                                            <small class="d-block text-muted">{{ $r->transaction->nozzle->pump->tank->fuel->name ?? '' }} 
                                                <br> تانك: {{ $r->transaction->nozzle->pump->tank->name ?? '' }}
                                                <br>{{ $r->transaction->nozzle->pump->name ?? '' }}
                                                <br>{{ $r->transaction->nozzle->name ?? '' }}
                                            </small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $r->liters }}</td>
                                    <td>{{ $r->price_per_liter }}</td>
                                    <td>{{ $r->total_amount }}</td>
                                    <td>
                                        @if($r->transaction && $r->transaction->hasMedia('transactions'))
                                            <a href="{{ $r->transaction->getFirstMediaUrl('transactions') }}" target="_blank">
                                                <img src="{{ $r->transaction->getFirstMediaUrl('transactions') }}" 
                                                     alt="إيصال" 
                                                     class="img-thumbnail" 
                                                     style="width: 50px; height: 50px; object-fit: cover;">
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <form action="{{ route('client_refuelings.destroy', $r->id) }}" method="POST"
                                            onsubmit="return confirm('هل أنت متأكد من حذف هذه العملية؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="fa fa-trash"></i> 
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endsection
@endcan
