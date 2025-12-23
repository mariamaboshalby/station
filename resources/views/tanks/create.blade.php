@can('add tank')
    @extends('layouts.app')

    @section('content')
        <div class="container" dir="rtl">
            <div class="row justify-content-center">
                <div class="col-md-8">

                    <x-card title="إضافة تانك جديد" headerClass="bg-success" textClass="text-white">

                        @if (session('success'))
                            <x-alert-success />
                        @endif

                        <form action="{{ route('tanks.store') }}" method="POST" class="p-3">
                            @csrf

                            <x-form.input type="select" name="fuel_id" id="fuel_id" label="نوع الوقود" required>
                                <option value="">-- اختر نوع الوقود --</option>
                                @foreach ($fuels as $fuel)
                                    <option value="{{ $fuel->id }}">{{ $fuel->name }}</option>
                                @endforeach
                            </x-form.input>

                            <x-form.input type="text" name="tank_name" id="tank_name" label="اسم التانك" required />

                            <x-form.input type="number" name="capacity" id="capacity" label="السعة (لتر)" required />

                            <x-form.input type="number" name="pump_count" id="pump_count" label="عدد الطلمبات" required
                                min="1" />



                            <div class="d-flex justify-content-center mt-4">
                                <x-button type="submit" color="success" icon="plus-circle" label="إضافة التانك" />
                            </div>
                        </form>

                    </x-card>

                </div>
            </div>
        </div>
    @endsection
@endcan
