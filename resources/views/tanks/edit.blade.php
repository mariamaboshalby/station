@can('edit tank')
    @extends('layouts.app')

    @section('content')
        <div class="container" dir="rtl">
            <div class="row justify-content-center">
                <div class="col-md-8">

                    <x-card title="تعديل السعة الحالية لتانك: {{ $tank->name }} ({{ $tank->fuel->name }})"
                        headerClass="bg-success" textClass="text-white">

                        @if (session('success'))
                            <x-alert-success />
                        @endif

                        <form action="{{ route('tanks.update', $tank->id) }}" method="POST" class="p-3">
                            @csrf
                            @method('PUT')

                            <x-form.input type="number" name="current_level" id="current_level" label="السعة الحالية (لتر)"
                                :value="$tank->current_level" required />

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
