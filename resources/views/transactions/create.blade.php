@can('add transaction')
    @extends('layouts.app')

    @section('content')
        <div class="container" dir="rtl">
            <div class="row justify-content-center">
                <div class="col-md-8">

                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-success text-white fw-bold">
                            <i class="fas fa-plus-circle me-2"></i> إضافة عملية جديدة
                        </div>

                        {{-- زر إغلاق الشيفت --}}
                        @isset($shift)
                            <a href="{{ route('shifts.closeForm', $shift->id) }}" class="btn btn-danger m-2 col-md-3 col-12 ms-auto">
                                <i class="fas fa-lock me-2"></i> إغلاق شيفت
                            </a>
                        @endisset

                        <div class="card-body">

                            {{-- رسالة نجاح --}}
                            @if (session('success'))
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
                                </div>
                            @endif

                            {{-- رسالة خطأ --}}
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            {{-- فورم إضافة العملية --}}
                            <form action="{{ route('transactions.store') }}" method="POST" enctype="multipart/form-data"
                                class="p-3">
                                @csrf

                                {{-- الشيفت --}}
                                @role('admin')
                                    <div class="mb-3">
                                        <label for="shift_id" class="form-label fw-bold">الشيفت</label>
                                        <select name="shift_id" id="shift_id" class="form-select" required>
                                            <option value="">-- اختر الشيفت --</option>
                                            @foreach ($shifts as $shift)
                                                <option value="{{ $shift->id }}">
                                                    {{ $shift->user->name }} - {{ $shift->start_time }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @else
                                    <input type="hidden" name="shift_id" value="{{ $shifts->first()->id ?? '' }}">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">الشيفت الحالي</label>
                                        <input type="text" class="form-control text-center"
                                            value="{{ $shifts->first()->user->name . ' - ' . $shifts->first()->start_time }}"
                                            readonly>
                                    </div>
                                @endrole

                                {{-- نوع العملية --}}
                                <div class="mb-3">
                                    <label class="form-label fw-bold">نوع العملية</label>
                                    <input type="text" class="form-control text-center bg-light" value="آجل" readonly>
                                </div>

                                {{-- المسدس --}}
                                <div class="mb-3">
                                    <label for="nozzle_id" class="form-label fw-bold">المسدس (الطلمبة)</label>
                                    <select name="nozzle_id" id="nozzle_id" class="form-select" required>
                                        <option value="">-- اختر المسدس المستخدم --</option>
                                        @foreach ($nozzles as $nozzle)
                                            <option value="{{ $nozzle->id }}">
                                                {{ $nozzle->name }} - {{ $nozzle->pump->name }}
                                                ({{ $nozzle->pump->tank->fuel->name }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="client_id" class="form-label fw-bold">اسم العميل</label>
                                    <select id="client_id" name="client_id" class="form-select" placeholder="ابحث عن العميل...">
                                        <option value="">-- اختر العميل --</option>
                                    </select>
                                </div>

                                {{-- رقم العربية --}}
                                <div class="mb-3">
                                    <label for="vehicle_number" class="form-label fw-bold">رقم العربية</label>
                                    <input type="text" name="vehicle_number" id="vehicle_number"
                                        class="form-control text-center" placeholder="مثال: أ ب ج 1234">
                                </div>


                                {{-- عدد اللترات --}}
                                <div class="mb-3">
                                    <label for="credit_liters" class="form-label fw-bold">عدد اللترات المسحوبة</label>
                                    <input type="number" step="0.01" name="credit_liters" id="credit_liters"
                                        class="form-control text-center" required min="0.01">
                                </div>

                                {{-- صورة العداد --}}
                                <div class="mb-3">
                                    <label for="image" class="form-label fw-bold">صورة العداد</label>
                                    <input type="file" name="image" id="image" class="form-control" accept="image/*"
                                        required>
                                    <small class="text-muted">يُفضل رفع صورة واضحة بحجم لا يتجاوز 4 ميجا.</small>

                                    {{-- عرض الصورة قبل الحفظ --}}
                                    <div class="mt-3 text-center">
                                        <img id="preview" src="#" alt="معاينة الصورة" class="img-thumbnail rounded"
                                            style="display:none; max-height: 250px;">
                                    </div>
                                </div>

                                {{-- الملاحظات --}}
                                <div class="mb-3">
                                    <label for="notes" class="form-label fw-bold">ملاحظات</label>
                                    <textarea name="notes" id="notes" rows="3" class="form-control" placeholder="اكتب أي ملاحظات إضافية..."></textarea>
                                </div>

                                {{-- زر الحفظ --}}
                                <div class="d-flex justify-content-center mt-4">
                                    <button type="submit" class="btn btn-success btn-lg px-5">
                                        <i class="fas fa-save me-1"></i> حفظ العملية
                                    </button>
                                </div>
                            </form>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    @endsection
    @push('script')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                new TomSelect("#client_id", {
                    valueField: 'id',
                    labelField: 'name',
                    searchField: 'name',
                    load: function(query, callback) {
                        if (!query.length) return callback();
                        fetch(`/clients/search?term=${encodeURIComponent(query)}`)
                            .then(response => response.json())
                            .then(json => {
                                callback(json);
                            })
                            .catch(() => {
                                callback();
                            });
                    },
                    placeholder: 'ابحث عن العميل...',
                    create: false,
                    maxOptions: 20,
                    render: {
                        option: function(item, escape) {
                            return `<div><strong>${escape(item.name)}</strong></div>`;
                        }
                    }
                });
            });
            // ✅ عرض الصورة قبل الحفظ
            document.getElementById('image').addEventListener('change', function(e) {
                const preview = document.getElementById('preview');
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        preview.src = event.target.result;
                        preview.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                } else {
                    preview.src = '#';
                    preview.style.display = 'none';
                }
            });
        </script>
    @endpush
@endcan
