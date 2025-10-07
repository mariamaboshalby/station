@can('close shift')
    @extends('layouts.app')

    @section('content')
        <div class="container" dir="rtl">
            <div class="row justify-content-center">
                <div class="col-md-8">

                    {{-- كارد --}}
                    <x-card title="إغلاق الشيفت" headerClass="bg-danger" textClass="text-white">

                        {{-- رسالة نجاح --}}
                        <x-alert-success />
                        <form method="POST" action="{{ route('shifts.close', $shift->id) }}" class="p-3"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PATCH')

                            {{-- نوع العملية مخفي --}}
                            <input type="hidden" name="operation_type" value="إغلاق شيفت">

                            {{-- قراءة العداد عند بداية الشيفت --}}
                            <div class="mb-3">
                                <label class="form-label">قراءة العداد عند بداية الشيفت</label>
                                <input type="number" class="form-control" value="{{ $shift->meter_reading }}" readonly>
                            </div>
                            {{-- قراءة العداد عند نهاية الشيفت --}}
                            <div class="mb-3">
                                <label for="end_meter_reading" class="form-label">قراءة العداد عند نهاية الشيفت</label>
                                <input type="number" name="end_meter_reading" id="end_meter_reading" class="form-control"
                                    placeholder="اكتب القراءة النهائية" required>
                            </div>

                            {{-- صورة العداد --}}
                            <div class="mb-3">
                                <label for="end_meter_image" class="form-label">صورة العداد عند الإغلاق</label>
                                <input type="file" name="end_meter_image" id="end_meter_image" class="form-control"
                                    accept="image/*" required>
                            </div>

                            {{-- ملاحظات --}}
                            <div class="mb-3">
                                <label for="notes" class="form-label">ملاحظات</label>
                                <textarea name="notes" id="notes" class="form-control" rows="3" placeholder="أدخل ملاحظات إن وجدت"></textarea>
                            </div>

                            {{-- زرار الحفظ --}}
                            <div class="d-flex justify-content-center mt-4">
                                <x-button type="submit" color="danger" size="lg" icon="lock" label="إغلاق الشيفت" />
                            </div>
                        </form>

                    </x-card>

                </div>
            </div>
        </div>
    @endsection
@endcan
