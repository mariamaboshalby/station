@extends('layouts.app')

@section('content')
<div class="container" dir="rtl">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow border-0 mb-4">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fa fa-user me-2"></i> بيانات المستخدم: {{ $user->name }}</h5>
                </div>

                <div class="card-body bg-light">
                    <div class="row">
                        {{-- بيانات المستخدم --}}
                        <div class="col-md-6 mb-3">
                            <div class="border rounded p-3 bg-white shadow-sm">
                                <h6 class="fw-bold text-success mb-3">
                                    <i class="fa fa-id-card me-2"></i> البيانات الأساسية
                                </h6>
                                <p><strong>الاسم:</strong> {{ $user->name }}</p>
                                <p><strong>رقم الهاتف:</strong> {{ $user->phone }}</p>
                            </div>
                        </div>

                        {{-- صلاحيات المستخدم --}}
                        <div class="col-md-6 mb-3">
                            <div class="border rounded p-3 bg-white shadow-sm">
                                <h6 class="fw-bold text-success mb-3">
                                    <i class="fa fa-key me-2"></i> صلاحيات المستخدم
                                </h6>

                                @php
                                    $permLabels = [
                                        'add user' => 'إضافة مستخدم',
                                        'show users' => 'عرض المستخدمين',
                                        'open shift' => 'فتح شيفت',
                                        'show shifts' => 'عرض الشيفتات',
                                        'close shift' => 'إغلاق شيفت',
                                        'show report' => 'عرض التقارير',
                                        'add transaction' => 'إضافة معاملة',
                                        'show transaction' => 'عرض المعاملات',
                                        'add tank' => 'إضافة تانك',
                                        'edit tank' => 'تعديل تانك',
                                        'show tanks' => 'عرض التانكات',
                                        'add client' => 'إضافة عميل',
                                        'edit client' => 'تعديل عميل',
                                        'show clients' => 'عرض العملاء',
                                        'view dashboard' => 'عرض لوحة التحكم',
                                    ];
                                @endphp

                                @if ($user->permissions->count())
                                    <div class="d-flex flex-wrap gap-2 mb-3">
                                        @foreach ($user->permissions as $perm)
                                            @if (!str_starts_with($perm->name, 'use_pump') && !str_starts_with($perm->name, 'use_tank'))
                                                <span class="badge bg-success">
                                                    {{ $permLabels[$perm->name] ?? $perm->name }}
                                                </span>
                                            @endif
                                        @endforeach
                                    </div>

                                    {{-- الطلمبات المسموح بها --}}
                                    @php
                                        $userPumps = [];
                                        foreach ($user->permissions as $perm) {
                                            if (str_starts_with($perm->name, 'use_pump_')) {
                                                $pumpId = str_replace('use_pump_', '', $perm->name);
                                                $pump = \App\Models\Pump::with('tank.fuel')->find($pumpId);
                                                if ($pump) {
                                                    $userPumps[] = $pump;
                                                }
                                            }
                                        }
                                    @endphp

                                    @if (count($userPumps) > 0)
                                        <div class="mt-3 border-top pt-3">
                                            <h6 class="fw-bold text-success mb-3">
                                                <i class="fa fa-gas-pump me-2"></i> الطلمبات المسموح باستخدامها
                                            </h6>
                                            <div class="row">
                                                @foreach ($userPumps as $pump)
                                                    <div class="col-md-6 mb-3">
                                                        <div class="border rounded p-3 bg-white shadow-sm">
                                                            <h6 class="text-success mb-1">
                                                                <i class="fa fa-tachometer-alt me-1"></i> طلمبة {{ $pump->name }}
                                                            </h6>
                                                            <p class="mb-1"><strong>نوع الوقود:</strong> {{ $pump->tank->fuel->name ?? 'غير محدد' }}</p>
                                                            <p class="mb-0"><strong>التانك:</strong> {{ $pump->tank->name ?? 'غير مرتبط' }}</p>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                @else
                                    <p class="text-muted">لا توجد صلاحيات مخصصة</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer bg-white border-top text-end">
                    <a href="{{ route('users.index') }}" class="btn btn-outline-success">
                        <i class="fa fa-arrow-right"></i> رجوع
                    </a>
                    <a href="{{ route('users.edit', $user->id) }}" class="btn btn-success">
                        <i class="fa fa-edit"></i> تعديل
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
