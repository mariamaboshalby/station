@can('show tanks')
    @extends('layouts.app')

    @section('content')
        <div class="container" dir="rtl">
            <div class="row justify-content-center">
                <div class="col-12">

                    <x-card title="تقرير تفصيلي - {{ $tank->name }}">

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">
                                <i class="fas fa-gas-pump text-primary"></i>
                                تانك: {{ $tank->name }} - {{ $tank->fuel->name }}
                            </h5>
                            <a href="{{ route('tanks.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-arrow-right"></i> العودة للقائمة
                            </a>
                        </div>

                        <x-alert-success />

                        <!-- Tank Info Table -->
                        <div class="table-responsive mb-4">
                            <table class="table table-bordered text-center">
                                <thead class="table-light">
                                    <tr>
                                        <th><i class="fas fa-hashtag"></i> رقم التانك</th>
                                        <th><i class="fas fa-database"></i> السعة الكلية</th>
                                        <th><i class="fas fa-tint"></i> المخزون الحالي</th>
                                        <th><i class="fas fa-arrow-down"></i> اللترات المسحوبة</th>
                                        <th><i class="fas fa-money-bill"></i> السعر للعميل</th>
                                        <th><i class="fas fa-dollar-sign"></i> السعر الأصلي</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong>{{ $tank->id }}</strong></td>
                                        <td>{{ number_format($tank->capacity) }} لتر</td>
                                        <td>{{ number_format($tank->current_level ?? 0) }} لتر</td>
                                        <td>{{ number_format($tank->liters_drawn ?? 0) }} لتر</td>
                                        <td>{{ $tank->fuel->price_per_liter }} ج.م</td>
                                        <td>{{ $tank->fuel->price_for_owner }} ج.م</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pumps and Nozzles Table -->
                        <h5 class="mb-3">
                            <i class="fas fa-cog text-warning"></i>
                            الطلمبات والمسدسات ({{ $tank->pumps->count() }} طلمبة)
                        </h5>

                        @foreach ($tank->pumps as $pump)
                            <div class="mb-4">
                                <h6 class="bg-primary text-white p-2 rounded d-flex justify-content-between align-items-center">
                                    <span>
                                        <i class="fas fa-cog"></i>
                                        {{ $pump->name }} - عدد المسدسات: {{ $pump->nozzles->count() }}
                                    </span>
                                    <button class="btn btn-sm btn-light text-primary" data-bs-toggle="modal"
                                        data-bs-target="#addNozzleModal{{ $pump->id }}">
                                        <i class="fas fa-plus"></i> إضافة مسدس
                                    </button>
                                </h6>

                                <div class="table-responsive">
                                    <table class="table table-hover table-striped table-bordered text-center">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 10%;"><i class="fas fa-hashtag"></i> #</th>
                                                <th style="width: 30%;"><i class="fas fa-fill-drip"></i> اسم المسدس</th>
                                                <th style="width: 30%;"><i class="fas fa-tachometer-alt"></i> قراءة العداد (لتر)
                                                </th>
                                                <th style="width: 30%;"><i class="fas fa-cogs"></i> إجراءات</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($pump->nozzles as $nozzle)
                                                <tr>
                                                    <td>{{ $nozzle->id }}</td>
                                                    <td><strong>{{ $nozzle->name }}</strong></td>
                                                    <td>
                                                        <span class="badge bg-info fs-6">
                                                            {{ number_format($nozzle->meter_reading ?? 0, 2) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-sm btn-primary"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#updateMeterModal{{ $nozzle->id }}">
                                                            <i class="fas fa-edit"></i> تحديث
                                                        </button>
                                                        <form action="{{ route('tanks.destroyNozzle', $nozzle->id) }}"
                                                            method="POST" class="d-inline"
                                                            onsubmit="return confirm('هل أنت متأكد من حذف هذا المسدس؟')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger">
                                                                <i class="fas fa-trash"></i> حذف
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>

                                                <!-- Update Meter Reading Modal -->
                                                <div class="modal fade" id="updateMeterModal{{ $nozzle->id }}"
                                                    tabindex="-1">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header bg-primary text-white">
                                                                <h5 class="modal-title">
                                                                    <i class="fas fa-tachometer-alt"></i>
                                                                    تحديث قراءة العداد - {{ $nozzle->name }}
                                                                </h5>
                                                                <button type="button" class="btn-close btn-close-white"
                                                                    data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <form action="{{ route('nozzles.updateMeter', $nozzle->id) }}"
                                                                method="POST">
                                                                @csrf
                                                                @method('PATCH')
                                                                <div class="modal-body">
                                                                    <div class="alert alert-info">
                                                                        <strong>الطلمبة:</strong> {{ $pump->name }}<br>
                                                                        <strong>المسدس:</strong> {{ $nozzle->name }}
                                                                    </div>

                                                                    <div class="mb-3">
                                                                        <label class="form-label">القراءة الحالية:</label>
                                                                        <input type="text" class="form-control"
                                                                            value="{{ number_format($nozzle->meter_reading ?? 0, 2) }} لتر"
                                                                            readonly>
                                                                    </div>

                                                                    <div class="mb-3">
                                                                        <label class="form-label">القراءة الجديدة: <span
                                                                                class="text-danger">*</span></label>
                                                                        <input type="number" step="0.01"
                                                                            name="meter_reading" class="form-control"
                                                                            value="{{ $nozzle->meter_reading ?? 0 }}" required>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary"
                                                                        data-bs-dismiss="modal">
                                                                        <i class="fas fa-times"></i> إلغاء
                                                                    </button>
                                                                    <button type="submit" class="btn btn-primary">
                                                                        <i class="fas fa-save"></i> حفظ التحديث
                                                                    </button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted">
                                                        لا توجد مسدسات في هذه الطلمبة
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                        <tfoot class="table-light">
                                            <tr>
                                                <td colspan="2"><strong>الإجمالي</strong></td>
                                                <td colspan="2">
                                                    <strong>{{ number_format($pump->nozzles->sum('meter_reading'), 2) }}
                                                        لتر</strong>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>

                            <!-- Add Nozzle Modal -->
                            <div class="modal fade" id="addNozzleModal{{ $pump->id }}" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header bg-success text-white">
                                            <h5 class="modal-title">
                                                <i class="fas fa-plus-circle"></i> إضافة مسدس جديد - {{ $pump->name }}
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white"
                                                data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="{{ route('tanks.storeNozzle', $pump->id) }}" method="POST">
                                            @csrf
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">اسم المسدس <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" name="nozzle_name" class="form-control"
                                                        placeholder="مثال: مسدس 1" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">قراءة العداد الحالية <span
                                                            class="text-danger">*</span></label>
                                                    <input type="number" step="0.01" name="meter_reading"
                                                        class="form-control" value="0" min="0" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">إلغاء</button>
                                                <button type="submit" class="btn btn-success">
                                                    <i class="fas fa-save"></i> حفظ المسدس
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <!-- Overall Summary Table -->
                        <div class="table-responsive mt-4">
                            <table class="table table-bordered text-center">
                                <thead class="table-primary">
                                    <tr>
                                        <th colspan="3">
                                            <i class="fas fa-chart-pie"></i>
                                            الملخص الإجمالي
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <strong>إجمالي الطلمبات</strong><br>
                                            <span class="badge bg-primary fs-5">{{ $tank->pumps->count() }}</span>
                                        </td>
                                        <td>
                                            <strong>إجمالي المسدسات</strong><br>
                                            <span class="badge bg-success fs-5">
                                                {{ $tank->pumps->sum(function ($p) {return $p->nozzles->count();}) }}
                                            </span>
                                        </td>
                                        <td>
                                            <strong>إجمالي قراءات العدادات</strong><br>
                                            <span class="badge bg-info fs-5">
                                                {{ number_format($tank->pumps->sum(function ($p) {return $p->nozzles->sum('meter_reading');}),2) }}
                                                لتر
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </x-card>

                </div>
            </div>
        </div>
    @endsection
@endcan
