@can('edit tank')
@extends('layouts.app')

@section('content')
    <div class="container" dir="rtl">
        <div class="row justify-content-center">
            <div class="col-md-8">

                {{-- ✅ كارد تعديل السعة والأسعار معًا --}}
                <x-card title="تعديل بيانات التانك: {{ $tank->name }} ({{ $tank->fuel->name }})"
                    headerClass="bg-success" textClass="text-white">

                    @if (session('success'))
                        <x-alert-success />
                    @endif

                    {{-- ✅ فورم موحد لتعديل السعة والأسعار --}}
                    <form action="{{ route('tanks.updateAll', $tank->id) }}" method="POST" class="p-3">
                        @csrf
                        @method('PUT')

                        <x-form.input type="number" name="current_level" id="current_level" 
                            label="السعة الحالية (لتر)" :value="$tank->current_level" required />

                        <hr>

                        <x-form.input type="number" step="0.01" name="price_per_liter" 
                            label="سعر اللتر للعميل (جنيه)" id="price_per_liter" 
                            :value="$tank->fuel->price_per_liter" required />

                        <x-form.input type="number" step="0.01" name="price_for_owner" 
                            label="سعر اللتر لصاحب المحطة (جنيه)" id="price_for_owner" 
                            :value="$tank->fuel->price_for_owner" required />

                        <div class="d-flex justify-content-center mt-4">
                            <x-button type="submit" color="success" icon="save" label="حفظ التعديلات" />
                        </div>
                    </form>

                </x-card>

            </div>
        </div>
    </div>
@endsection
@endcan
