@can('show tanks')
    @extends('layouts.app')

    @section('content')
        <div class="container" dir="rtl">
            <div class="row justify-content-center">
                <div class="col-12">

                    <x-card title="قائمة التانكات" >

                        <div class="d-flex justify-content-between mb-3">
                            <span></span>
                            @can('add tank')
                                <x-button type="link" color="success" size="sm" icon="plus-circle" label="إضافة تانك جديد"
                                    :href="route('tanks.create')" />
                            @endcan
                        </div>

                        <x-alert-success />

                        <table class="table table-hover table-striped text-center">
                            <thead class="table-light">
                                <tr>
                                    <th><i class="fas fa-hashtag"></i> #</th>
                                    <th><i class="fas fa-gas-pump"></i> الاسم</th>
                                    <th><i class="fas fa-money-bill"></i> السعر للعميل</th>
                                    <th><i class="fas fa-dollar-sign"></i> السعر الأصلي</th>
                                    <th><i class="fas fa-database"></i> السعة</th>
                                    <th><i class="fas fa-tint"></i> اللترات المخزنة</th>
                                    <th><i class="fas fa-arrow-down"></i> اللترات المسحوبة</th>
                                    @can('edit tank')
                                        <th><i class="fas fa-cogs"></i> إجراءات</th>
                                    @endcan
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tanks as $tank)
                                    <tr>
                                        <td>{{ $tank->id }}</td>
                                        <td>تانك : {{ $tank->name }} - {{ $tank->fuel->name }}</td>
                                        <td>{{ $tank->fuel->price_per_liter }}</td>
                                        <td>{{ $tank->fuel->price_for_owner }}</td>
                                        <td>{{ $tank->capacity }}</td>
                                        <td>{{ $tank->current_level }}</td>
                                        <td>{{ $tank->liters_drawn ?? 0 }}</td>

                                        @can('edit tank')
                                            <td class="d-flex justify-content-center gap-1">
                                                {{-- زر التقرير --}}
                                                <x-button type="link" color="info" size="sm" icon="chart-bar"
                                                    label="تقرير" :href="route('tanks.report', $tank->id)" />

                                                {{-- زر تعديل --}}
                                                <x-button type="link" color="primary" size="sm" icon="edit"
                                                    label="تعديل" :href="route('tanks.edit', $tank->id)" />

                                                {{-- زر إضافة --}}
                                                <x-button type="link" color="success" size="sm" icon="plus"
                                                    label="إضافة" :href="route('tanks.addCapacityForm', $tank->id)" />

                                                {{-- زر حذف --}}
                                                <form action="{{ route('tanks.destroy', $tank->id) }}" method="POST" class="d-inline"
                                                    onsubmit="return confirm('هل أنت متأكد من حذف هذا التانك؟');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-trash"></i> 
                                                    </button>
                                                </form>
                                            </td>
                                        @endcan
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">لا يوجد تانكات حالياً</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                    </x-card>

                </div>
            </div>
        </div>
    @endsection
@endcan
