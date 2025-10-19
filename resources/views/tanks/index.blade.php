@can('show tanks')
    @extends('layouts.app')

    @section('content')
        <div class="container" dir="rtl">
            <div class="row justify-content-center">
                <div class="col-12">

                    <x-card title="قائمة التانكات">

                        <div class="d-flex justify-content-between mb-3">
                            <span></span>
                            @can('add tank')
                                <x-button type="link" color="success" size="sm" icon="plus-circle" label="إضافة تانك جديد"
                                    :href="route('tanks.create')" />
                            @endcan
                        </div>

                        <x-alert-success />

                        <table class="table table-hover table-striped text-center " >
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>الاسم</th>
                                    <th>السعر للعميل</th>
                                    <th>السعر الاصلي</th>
                                    <th>السعة</th>
                                    <th>اللترات المخزنة</th>
                                    <th>اللترات المسحوبه</th>
                                    @can('edit tank')
                                        <th>إجراءات</th>
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
                                        <td>{{ $tank->liters_drawn ??0}}</td>
                                        @can('edit tank')
                                            <td>
                                                <x-button type="link" color="primary" size="sm" icon="edit"
                                                    label="تعديل" :href="route('tanks.edit', $tank->id)" />
                                                <x-button type="link" color="success" size="sm" icon="plus"
                                                    label="إضافة" :href="route('tanks.addCapacityForm', $tank->id)" />
                                            </td>
                                        @endcan

                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">لا يوجد تانكات حالياً</td>
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
