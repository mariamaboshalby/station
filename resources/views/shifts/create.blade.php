@can('open shift')
    @extends('layouts.app')

    @section('content')
        <div class="container" dir="rtl">
            <div class="row justify-content-center">
                <div class="col-md-8">

                    {{-- كارد --}}
                    <x-card title="فتح شيفت جديد" headerClass="bg-success" textClass="text-white">

                        {{-- رسالة نجاح --}}
                        <x-alert-success />

                        <form method="POST" action="{{ route('shifts.store') }}" class="p-3" enctype="multipart/form-data">
                            @csrf

                            @role('admin')
                                <div class="mb-3">
                                    <label for="user_id" class="form-label">اختر الموظف</label>
                                    <select name="user_id" id="user_id" class="form-select" required>
                                        <option value="">-- اختر --</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endrole

                            {{-- استلام العداد --}}
                            <div class="mb-3">
                                <label for="meter_reading" class="form-label">قراءة/استلام العداد</label>
                                <input type="number" name="meter_reading" id="meter_reading" class="form-control"
                                    placeholder="اكتب قراءة العداد" required>
                            </div>

                            {{-- صورة العداد --}}
                            <div class="mb-3">
                                <label for="meter_image" class="form-label">صورة العداد</label>
                                <input type="file" name="meter_image" id="meter_image" class="form-control" accept="image/*"
                                    required>
                            </div>

                
                            {{-- نوع العملية مخفي --}}
                            <input type="hidden" name="operation_type" value="فتح شيفت">

                            {{-- زرار الحفظ --}}
                            <div class="d-flex justify-content-center mt-4">
                                <x-button type="submit" color="success" size="lg" icon="play-circle" label="فتح الشيفت" />
                            </div>
                        </form>

                    </x-card>

                </div>
            </div>
        </div>
    @endsection
@endcan
