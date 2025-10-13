@can('add transaction')
    @extends('layouts.app')

    @section('content')
        <div class="container" dir="rtl">
            <div class="row justify-content-center">
                <div class="col-md-8">

                    <div class="card">
                        <div class="card-header bg-success text-white fw-bold ">
                            إضافة عملية جديدة
                        </div>
                        <a href="{{ route('shifts.closeForm', $shift->id) }}" class="col-md-3 col-12 ms-auto btn btn-danger m-1">
                            <i class="fas fa-lock me-2"></i> إغلاق شيفت
                        </a>

                        <div class="card-body">

                            {{-- رسالة نجاح --}}
                            @if (session('success'))
                                <div class="alert alert-success">
                                    {{ session('success') }}
                                </div>
                            @endif

                            <form action="{{ route('transactions.store') }}" method="POST" class="p-3"
                                enctype="multipart/form-data">
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
                                    <label for="operation_type" class="form-label fw-bold">نوع العملية</label>
                                    <input type="text" step="0.01" name="credit" id="credit_liters"
                                        class="form-control text-center" value="اجل" disabled>
                                </div>

                                {{-- اختيار الطلمبة --}}
                                <div class="mb-3">
                                    <label for="pump_id" class="form-label fw-bold">الطلمبة</label>
                                    <select name="pump_id" id="pump_id" class="form-select" required>
                                        <option value="">-- اختر طلمبة --</option>
                                        @foreach ($pumps as $pump)
                                            <option value="{{ $pump->id }}">
                                                {{ $pump->name }} - تانك {{ $pump->tank->name }} -
                                                {{ $pump->tank->fuel->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- اختيار العميل (يظهر فقط لو بيع آجل) --}}
                                <div class="mb-3" id="client_field">
                                    <label for="client_id" class="form-label fw-bold">اسم العميل</label>
                                    <select name="client_id" id="client_id" class="form-select select2">
                                        <option value="">-- اختر العميل --</option>
                                        @foreach (\App\Models\Client::all() as $client)
                                            <option value="{{ $client->id }}">{{ $client->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- عدد اللترات --}}
                                <div class="mb-3">
                                    <label for="credit_liters" class="form-label fw-bold">عدد اللترات المسحوبة</label>
                                    <input type="number" step="0.01" name="credit_liters" id="credit_liters"
                                        class="form-control text-center" required>
                                </div>

                                {{-- صورة العداد --}}
                                <div class="mb-3">
                                    <label for="image" class="form-label fw-bold">صورة العداد</label>
                                    <input type="file" name="image" id="image" class="form-control" accept="image/*"
                                        required>
                                </div>

                                {{-- ملاحظات --}}
                                <div class="mb-3">
                                    <label for="notes" class="form-label fw-bold">ملاحظات</label>
                                    <textarea name="notes" id="notes" rows="3" class="form-control"></textarea>
                                </div>

                                <div class="d-flex justify-content-center mt-4">
                                    <button type="submit" class="btn btn-success btn-lg">
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
@endcan
