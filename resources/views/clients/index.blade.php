@can('show clients')
    @extends('layouts.app')

    @section('content')
        <div class="row justify-content-center" dir="rtl">
            <div class="col-12">

                <x-card title="قائمة العملاء">

                    <div class="d-flex justify-content-between mb-3">
                        <span></span>
                        @can('add client')
                            <x-button type="link" color="success" size="sm" icon="plus-circle" label="إضافة عميل جديد"
                                :href="route('clients.create')" />
                        @endcan
                    </div>

                    <x-alert-success />

                    <table class="table table-hover table-striped text-center">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>الاسم</th>
                                @foreach ($fuels as $fuel)
                                    <th>سعر {{ $fuel->name }}</th>
                                @endforeach
                                <th>اللترات المسحوبه</th>
                                <th>المبلغ الكلي</th>
                                <th>المبلغ المدفوع</th>
                                <th>المبلغ المتبقي</th>
                                <th>الحالة</th>

                                @can('edit client')
                                    <th>إجراءات</th>
                                @endcan
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($clients as $client)
                                <tr>
                                    <td>{{ $client->id }}</td>
                                    <td>{{ $client->name }}</td>
                                    @php
                                        $fuelPrices = $client->fuelPrices->keyBy('fuel_id');
                                    @endphp
                                    @foreach ($fuels as $fuel)
                                        <td>{{ $fuelPrices->get($fuel->id)?->price_per_liter ?? $fuel->price_per_liter }}</td>
                                    @endforeach
                                    <td>{{ $client->liters_drawn }}</td>
                                    <td>{{ $client->total_price }}</td>
                                    <td>{{ $client->amount_paid }}</td>
                                    <td>{{ $client->rest }}</td>
                                    <td>
                                        @if($client->is_active)
                                            <span class="badge bg-success">
                                                <i class="fas fa-check-circle"></i> نشط
                                            </span>
                                        @else
                                            <span class="badge bg-danger">
                                                <i class="fas fa-ban"></i> معطل
                                            </span>
                                        @endif
                                    </td>

                                    @can('edit client')
                                        <td class="d-flex justify-content-center gap-2">
                                            {{-- زر تفعيل/تعطيل الحساب --}}
                                            <form action="{{ route('clients.toggleStatus', $client->id) }}" method="POST"
                                                onsubmit="return confirm('هل أنت متأكد من {{ $client->is_active ? 'تعطيل' : 'تفعيل' }} هذا الحساب؟');">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-{{ $client->is_active ? 'warning' : 'success' }} btn-sm">
                                                    <i class="fas fa-{{ $client->is_active ? 'ban' : 'check-circle' }}"></i>
                                                </button>
                                            </form>

                                            {{-- زر إضافة دفعة --}}
                                            <x-button type="link" color="primary" size="sm" icon="plus"
                                                label="إضافة دفعة" :href="route('clients.addPaymentForm', $client->id)" />

                                            {{-- زر عرض التقرير --}}
                                            <x-button type="link" color="info" size="sm" icon="file-alt" label=" "
                                                :href="route('clients.transactions', $client->id)" />

                                            {{-- زر تعديل الاسم --}}
                                            <x-button type="link" color="success" size="sm" icon="edit" label=" "
                                                :href="route('clients.edit', $client->id)" />

                                            {{-- زر الحذف --}}
                                            <form action="{{ route('clients.destroy', $client->id) }}" method="POST"
                                                onsubmit="return confirm('هل أنت متأكد من حذف هذا العميل؟');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>

                                        </td>
                                    @endcan
                                </tr>
                            @empty
                                @php
                                    $colspan = 2 + $fuels->count() + 5;
                                @endphp
                                @can('edit client')
                                    @php
                                        $colspan++;
                                    @endphp
                                @endcan
                                <tr>
                                    <td colspan="{{ $colspan }}" class="text-center">لا يوجد عملاء حالياً</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{-- Modern Pagination --}}
                    <div class="d-flex justify-content-center mt-4">
                        {{ $clients->links('pagination::bootstrap-5') }}
                    </div>

                </x-card>

            </div>
        </div>
    @endsection
@endcan

<style>
    /* Modern Pagination Styles */
    .pagination {
        justify-content: center;
        margin: 0;
    }

    .page-link {
        color: #667eea;
        border: none;
        margin: 0 2px;
        border-radius: 8px;
        padding: 8px 12px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .page-link:hover {
        color: #fff;
        background-color: #667eea;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }

    .page-item.active .page-link {
        color: #fff;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }

    .page-item.disabled .page-link {
        color: #adb5bd;
        background-color: #f8f9fa;
        cursor: not-allowed;
    }
</style>
