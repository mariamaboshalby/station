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

                            {{-- عرض المسدسات المتاحة --}}
                            <div class="mb-4">
                                <label class="form-label fw-bold mb-2">
                                    <i class="fas fa-gas-pump me-1"></i> العدادات التي سيتم استلامها:
                                </label>
                                <div class="row g-2">
                                    @foreach($nozzles as $nozzle)
                                        <div class="col-md-6">
                                            <div class="p-3 border rounded bg-light d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="mb-1 text-dark fw-bold">{{ $nozzle->name }}</h6>
                                                    <small class="text-muted d-block">{{ $nozzle->pump->tank->fuel->name ?? '-' }}</small>
                                                </div>
                                                <div class="text-end">
                                                    <span class="badge bg-secondary mb-1">القراءة الحالية</span>
                                                    <h5 class="mb-0 text-primary fw-bold">{{ $nozzle->meter_reading }}</h5>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="meter_reading" class="form-label">قراءة/استلام العداد</label>
                                <input type="number" name="meter_reading" id="meter_reading" class="form-control"
                                    value="{{ $totalLitersDrawn }}" readonly>
                            </div>

                            <div class="mb-3">
                                <label for="meter_image" class="form-label">صورة العداد</label>
                                <input type="file" name="meter_image" id="meter_image" class="form-control" accept="image/*"
                                    required onchange="previewImage(event)">
                                <div class="mt-2 text-center">
                                    <img id="image_preview" src="#" alt="معاينة الصورة"
                                        style="display:none; max-width:200px;" class="rounded shadow-sm border border-light">
                                </div>
                            </div>
                            {{-- نوع العملية مخفي --}}
                            <input type="hidden" name="operation_type" value="فتح شيفت">

                            <div class="mb-3">
                                <label class="form-label">حالة المطابقة</label>
                                <div class="d-flex gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="meter_match" id="match_yes"
                                            value="1" required checked>
                                        <label class="form-check-label" for="match_yes">مطابق</label>
                                    </div>

                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="meter_match" id="match_no"
                                            value="0">
                                        <label class="form-check-label" for="match_no">غير مطابق</label>
                                    </div>
                                </div>
                            </div>

                            {{-- زرار الحفظ --}}
                            <div class="d-flex justify-content-center mt-4">
                                <x-button type="submit" color="success" size="lg" icon="play-circle" label="فتح الشيفت" />
                            </div>
                        </form>

                    </x-card>

                </div>
            </div>
        </div>
        <script>
            function previewImage(event) {
                const reader = new FileReader();
                reader.onload = function() {
                    const output = document.getElementById('image_preview');
                    output.src = reader.result;
                    output.style.display = 'block';
                };
                reader.readAsDataURL(event.target.files[0]);
            }
        </script>
    @endsection
@endcan
