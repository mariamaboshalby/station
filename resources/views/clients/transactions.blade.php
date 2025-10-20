@can('show clients')
    @extends('layouts.app')

    @section('content')
        <div class="" dir="rtl">
            <h3 class="mb-4 text-center text-primary fw-bold">
                حساب العميل: {{ $client->name }}
            </h3>

            <div class="card shadow">
                <div class="card-header bg-success text-white fw-bold">
                    سجل التفويلات
                </div>
                <div class="card-body">
                    <table class="table table-bordered text-center align-middle">
                        <thead class="table-success">
                            <tr>
                                <th>التاريخ</th>
                                <th>الشيفت</th>
                                <th>عدد اللترات</th>
                                <th>سعر اللتر</th>
                                <th>الإجمالي</th>
                                <th>الإجراء</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($client->refuelings as $r)
                                <tr>
                                    <td>{{ $r->created_at->format('Y-m-d H:i') }}</td>
                                    <td>{{ $r->shift->user->name }}</td>
                                    <td>{{ $r->liters }}</td>
                                    <td>{{ $r->price_per_liter }}</td>
                                    <td>{{ $r->total_amount }}</td>
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
