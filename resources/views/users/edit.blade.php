@extends('layouts.app')

@section('content')
<div class="container card p-3">
    <h2 class="mb-4 text-center fw-bold text-primary">
        <i class="fa fa-user-edit me-2"></i> تعديل المستخدم: {{ $user->name }}
    </h2>

    <form action="{{ route('users.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Tabs -->
        <ul class="nav nav-tabs mb-4" id="userTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-bold" id="basic-tab" data-bs-toggle="tab" data-bs-target="#basic"
                    type="button" role="tab">
                    <i class="fa fa-info-circle me-1"></i> البيانات الأساسية
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold" id="permissions-tab" data-bs-toggle="tab"
                    data-bs-target="#permissions" type="button" role="tab">
                    <i class="fa fa-lock me-1"></i> الصلاحيات
                </button>
            </li>
        </ul>

        <div class="tab-content" id="userTabsContent">
            <!-- Basic Info Tab -->
            <div class="tab-pane fade show active" id="basic" role="tabpanel">
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold"><i class="fa fa-user me-1"></i> الاسم</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold"><i class="fa fa-phone me-1"></i> رقم التليفون</label>
                        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="form-control" required pattern="[0-9]{11}" maxlength="11">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold"><i class="fa fa-key me-1"></i> كلمة المرور الجديدة (اختياري)</label>
                        <input type="password" name="password" class="form-control" placeholder="اتركها فارغة إذا لا تريد التغيير">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold"><i class="fa fa-check-circle me-1"></i> تأكيد كلمة المرور</label>
                        <input type="password" name="password_confirmation" class="form-control">
                    </div>
                </div>
            </div>

            <!-- Permissions Tab -->
            <div class="tab-pane fade" id="permissions" role="tabpanel">
                <h4 class="text-primary mb-3"><i class="fa fa-lock me-2"></i> الصلاحيات</h4>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="selectAllPermissions">
                    <label class="form-check-label fw-bold" for="selectAllPermissions">
                        اختيار الكل
                    </label>
                </div>

                <div class="row">
                    @php
                        $generalPermissions = [
                            'users' => [
                                ['value' => 'add user', 'label' => 'إضافة مستخدم'],
                                ['value' => 'show users', 'label' => 'عرض المستخدمين'],
                            ],
                            'shifts' => [
                                ['value' => 'open shift', 'label' => 'فتح شيفت'],
                                ['value' => 'show shifts', 'label' => 'عرض الشيفتات'],
                                ['value' => 'close shift', 'label' => 'إغلاق شيفت'],
                                ['value' => 'show report', 'label' => 'عرض التقارير'],
                            ],
                            'transactions' => [
                                ['value' => 'add transaction', 'label' => 'إضافة معاملة'],
                                ['value' => 'show transaction', 'label' => 'عرض المعاملات'],
                            ],
                            'tanks' => [
                                ['value' => 'add tank', 'label' => 'إضافة تانك'],
                                ['value' => 'edit tank', 'label' => 'تعديل تانك'],
                                ['value' => 'show tanks', 'label' => 'عرض التانكات'],
                            ],
                            'clients' => [
                                ['value' => 'add client', 'label' => 'إضافه عميل'],
                                ['value' => 'edit client', 'label' => 'تعديل عميل'],
                                ['value' => 'show clients', 'label' => 'عرض العملاء'],
                            ],
                            'dashboard' => [['value' => 'view dashboard', 'label' => 'عرض الداشبورد']],
                        ];

                        $fuelCategories = [
                            '80' => ['name' => 'بنزين 80', 'icon' => 'fa-gas-pump'],
                            '92' => ['name' => 'بنزين 92', 'icon' => 'fa-gas-pump'],
                            '95' => ['name' => 'بنزين 95', 'icon' => 'fa-gas-pump'],
                            'solar' => ['name' => 'سولار', 'icon' => 'fa-oil-can'],
                        ];

                        $permissionCategories = [
                            'users' => ['icon' => 'fa-users', 'label' => 'المستخدمين'],
                            'shifts' => ['icon' => 'fa-clock', 'label' => 'الشيفتات'],
                            'transactions' => ['icon' => 'fa-exchange-alt', 'label' => 'المعاملات'],
                            'tanks' => ['icon' => 'fa-oil-can', 'label' => 'التانكات'],
                            'clients' => ['icon' => 'fa-handshake', 'label' => 'العملاء'],
                            'dashboard' => ['icon' => 'fa-tachometer-alt', 'label' => 'الداشبورد'],
                        ];
                    @endphp

                    <!-- General Categories -->
                    @foreach ($generalPermissions as $category => $permissions)
                        <div class="col-md-4 mb-3">
                            <div class="card shadow-sm mb-4 border-0 rounded-3">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center py-2 px-3 rounded-top">
                                    <h6 class="mb-0 fw-bold text-primary">
                                        <i class="fa {{ $permissionCategories[$category]['icon'] }} me-2 text-secondary"></i>
                                        {{ $permissionCategories[$category]['label'] }}
                                    </h6>

                                    <div class="form-check m-0">
                                        <input class="form-check-input category-checkbox" 
                                               type="checkbox" 
                                               id="category_{{ $category }}"
                                               data-category="{{ $category }}">
                                        <label class="form-check-label small fw-bold text-muted" for="category_{{ $category }}">
                                            اختر الكل
                                        </label>
                                    </div>
                                </div>

                                <div class="card-body bg-white">
                                    @foreach($permissions as $permission)
                                        <div class="form-check mb-2 ms-3">
                                            <input class="form-check-input permission-checkbox" 
                                                   type="checkbox"
                                                   name="permissions[]" 
                                                   value="{{ $permission['value'] }}"
                                                   data-category="{{ $category }}"
                                                   id="perm_{{ str_replace(' ', '_', $permission['value']) }}"
                                                   {{ $user->permissions->contains('name', $permission['value']) ? 'checked' : '' }}>
                                            <label class="form-check-label small fw-semibold" for="perm_{{ str_replace(' ', '_', $permission['value']) }}">
                                                {{ $permission['label'] }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <!-- Fuel Categories -->
                    @foreach ($fuelCategories as $key => $fuelData)
                        <div class="col-md-4 mb-3">
                            @php
                                $query = \App\Models\Fuel::query();
                                if(!empty($fuelData['name'])) {
                                    $query->where('name', 'like', "%{$fuelData['name']}%");
                                }
                                $fuel = $query->first();
                                $tanks = $fuel ? $fuel->tanks()->with(['pumps'])->get() : collect();
                            @endphp

                            <div class="card shadow-sm mb-4 border-0 rounded-3">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center py-2 px-3 rounded-top">
                                    <h6 class="mb-0 fw-bold text-primary">
                                        <i class="fa {{ $fuelData['icon'] ?? 'fa-gas-pump' }} me-2 text-secondary"></i>
                                        {{ $fuelData['name'] ?? 'وقود' }}
                                    </h6>

                                    <div class="form-check m-0">
                                        <input class="form-check-input category-checkbox" 
                                               type="checkbox" 
                                               id="category_{{ $key }}"
                                               data-category="{{ $key }}">
                                        <label class="form-check-label small fw-bold text-muted" for="category_{{ $key }}">
                                            اختر الكل
                                        </label>
                                    </div>
                                </div>

                                <div class="card-body bg-white">
                                    @if($tanks->count() > 0)
                                        @foreach($tanks as $tank)
                                            <div class="tank-section mb-4 p-3 border rounded bg-light-subtle">
                                                <h6 class="fw-bold mb-3 text-secondary">
                                                    <i class="fa fa-oil-can me-2 text-info"></i> تانك: {{ $tank->name }}
                                                    <small class="text-muted">(سعة: {{ number_format($tank->capacity, 2) }} لتر)</small>
                                                </h6>

                                                @if($tank->pumps->count() > 0)
                                                    @foreach($tank->pumps as $pump)
                                                        <div class="pump-item mb-2 ms-3">
                                                            <div class="form-check">
                                                                <input class="form-check-input permission-checkbox" 
                                                                       type="checkbox"
                                                                       name="permissions[]" 
                                                                       value="use_pump_{{ $pump->id }}"
                                                                       data-category="{{ $key }}"
                                                                       id="pump_{{ $pump->id }}"
                                                                       {{ $user->permissions->contains('name', 'use_pump_' . $pump->id) ? 'checked' : '' }}>
                                                                <label class="form-check-label fw-semibold" for="pump_{{ $pump->id }}">
                                                                    <i class="fa fa-tachometer-alt me-1 text-primary"></i> طلمبة: {{ $pump->name }}
                                                                </label>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <div class="alert alert-info py-1 mb-0">
                                                        <small><i class="fa fa-info-circle me-1"></i> لا توجد طلمبات لهذا التانك</small>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="alert alert-warning text-center mb-0">
                                            <i class="fa fa-exclamation-triangle me-2"></i> لا توجد تانكات {{ $fuelData['name'] ?? '' }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="mt-4 text-center">
            <button type="submit" class="btn btn-success px-4">
                <i class="fa fa-save me-1"></i> حفظ التعديلات
            </button>
            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary px-4">
                <i class="fa fa-arrow-left me-1"></i> رجوع
            </a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        const selectAllCheckbox = document.getElementById('selectAllPermissions');
        const permissionCheckboxes = document.querySelectorAll('.permission-checkbox');
        const categoryCheckboxes = document.querySelectorAll('.category-checkbox');

        // دالة تحديث حالة المجموعة
        function updateCategoryState(category) {
            const categoryPerms = document.querySelectorAll(
                `.permission-checkbox[data-category="${category}"]`);
            const categoryCheckbox = document.querySelector(
                `.category-checkbox[data-category="${category}"]`);
            if (categoryCheckbox) {
                const allChecked = [...categoryPerms].every(c => c.checked);
                const anyChecked = [...categoryPerms].some(c => c.checked);
                categoryCheckbox.checked = allChecked;
                categoryCheckbox.indeterminate = anyChecked && !allChecked;
            }
        }

        // دالة تحديث حالة اختيار الكل
        function updateSelectAllState() {
            const allChecked = [...permissionCheckboxes].every(cb => cb.checked);
            const anyChecked = [...permissionCheckboxes].some(cb => cb.checked);
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = allChecked;
                selectAllCheckbox.indeterminate = anyChecked && !allChecked;
            }
        }

        // تهيئة الحالة الأولية للصلاحيات المحددة
        categoryCheckboxes.forEach(cat => {
            updateCategoryState(cat.dataset.category);
        });
        updateSelectAllState();

        // اختيار الكل
        selectAllCheckbox?.addEventListener('change', function() {
            const isChecked = this.checked;
            permissionCheckboxes.forEach(cb => cb.checked = isChecked);
            categoryCheckboxes.forEach(cb => {
                cb.checked = isChecked;
                cb.indeterminate = false;
            });
        });

        // تغيير مجموعة معينة
        categoryCheckboxes.forEach(cat => {
            cat.addEventListener('change', function() {
                const category = this.dataset.category;
                const isChecked = this.checked;
                document.querySelectorAll(
                        `.permission-checkbox[data-category="${category}"]`)
                    .forEach(cb => cb.checked = isChecked);
                this.indeterminate = false;
                updateSelectAllState();
            });
        });

        // تغيير صلاحية فردية
        permissionCheckboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                updateCategoryState(this.dataset.category);
                updateSelectAllState();
            });
        });
    }, 300);
});
</script>
@endpush