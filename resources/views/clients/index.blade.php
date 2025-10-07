{{-- @can('show clients') --}}
    @extends('layouts.app')

    @section('content')
        <div class="container" dir="rtl">
            <div class="row justify-content-center">
                <div class="col-12">

                    <x-card title="قائمة العملاء">

                        <div class="d-flex justify-content-between mb-3">
                            <span></span>
                            {{-- @can('add client') --}}
                                <x-button type="link" color="success" size="sm" icon="plus-circle" label="إضافة عميل جديد"
                                    :href="route('clients.create')" />
                            {{-- @endcan --}}
                        </div>

                        <x-alert-success />

                        <table class="table table-hover table-striped text-center">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>الاسم</th>
                                    {{-- <th>الطلمبه</th> --}}
                                    <th>اللترات المسحوبه</th>
                                    <th>المبلغ الكلي</th>
                                    <th>المبلغ المدفوع</th>
                                    <th>المبلغ المتبقي</th>
                                   
                                    {{-- @can('edit client') --}}
                                        <th>إجراءات</th>
                                    {{-- @endcan --}}
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($clients as $client)
                                    <tr>
                                        <td>{{ $client->id }}</td>
                                        <td>{{ $client->name }}</td>
                                        {{-- <td>{{ $client->pump->name }}</td> --}}
                                          <td>{{ $client->liters_drawn }}</td>
                                        <td>{{ $client->total_price}}</td>
                                        <td>{{ $client->amount_paid}}</td>
                                        <td>{{ $client->rest}}</td>
                                      
                                        {{-- @can('edit client') --}}
                                            <td>
                                                <x-button type="link" color="primary" size="sm" icon="edit"
                                                    label="تعديل" :href="route('clients.edit', $client->id)" />
                                                {{-- <x-button type="link" color="success" size="sm" icon="plus"
                                                    label="إضافة" :href="route('clients.addlitters', $client->id)" /> --}}
                                            </td>
                                        {{-- @endcan --}}

                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">لا يوجد عملاء حالياً</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                    </x-card>

                </div>
            </div>
        </div>
    @endsection
{{-- @endcan --}}
