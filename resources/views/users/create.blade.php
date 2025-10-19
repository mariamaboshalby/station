@extends('layouts.app')

@section('content')
    @can('add user')
        <div class="container card p-3">
            <h2 class="mb-4 text-center fw-bold text-primary">
                <i class="fa fa-user-plus me-2"></i> إضافة مستخدم جديد
            </h2>

            <form action="{{ route('users.store') }}" method="POST">
                @csrf

                <!-- Tabs -->
                <ul class="nav nav-tabs mb-4" id="userTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-bold" id="basic-tab" data-bs-toggle="tab" data-bs-target="#basic"
                            type="button" role="tab">
                            <i class="fa fa-info-circle me-1"></i> البيانات الأساسية
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold" id="permissions-tab" data-bs-toggle="tab" data-bs-target="#permissions"
                            type="button" role="tab">
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
                                <input type="text" name="name" class="form-control" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold"><i class="fa fa-phone me-1"></i> رقم التليفون</label>
                                <input type="text" name="phone" class="form-control" required pattern="[0-9]{11}"
                                    maxlength="11" placeholder="01xxxxxxxxx">
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label fw-bold"><i class="fa fa-key me-1"></i> كلمة المرور</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold"><i class="fa fa-check-circle me-1"></i> تأكيد كلمة
                                    المرور</label>
                                <input type="password" name="password_confirmation" class="form-control" required>
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
                                    'clients'=>[
                                        ['value'=>'add client', 'label'=>'إضافه عميل'],
                                        ['value'=>'edit client', 'label'=>'تعديل عميل'],
                                        ['value'=>'show clients', 'label'=>'عرض العملاء'],
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
                                    '80' => ['icon' => 'fa-gas-pump', 'label' => 'بنزين 80'],
                                    '92' => ['icon' => 'fa-gas-pump', 'label' => 'بنزين 92'],
                                    '95' => ['icon' => 'fa-gas-pump', 'label' => 'بنزين 95'],
                                    'solar' => ['icon' => 'fa-oil-can', 'label' => 'سولار'],
                                ];
                            @endphp

                            <!-- General Categories -->
                            @foreach ($generalPermissions as $category => $permissions)
                                <div class="col-md-4 mb-3">
                                    @include('users.partials.permission-card', [
                                        'category' => $category,
                                        'title' => $permissionCategories[$category]['label'],
                                        'icon' => $permissionCategories[$category]['icon'],
                                        'permissions' => $permissions,
                                    ])
                                </div>
                            @endforeach

                            <!-- Fuel Categories -->
                            @foreach ($fuelCategories as $key => $fuelData)
                                <div class="col-md-4 mb-3">
                                    @include('users.partials.fuel-permission-card', [
                                        'category' => $key,
                                        'fuelData' => $fuelData,
                                    ])
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="mt-4 text-center">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fa fa-save me-1"></i> حفظ المستخدم
                    </button>
                    <a href="{{ route('users.index') }}" class="btn btn-outline-primary px-4">
                        <i class="fa fa-arrow-left me-1"></i> رجوع للقائمة
                    </a>
                </div>
            </form>
        </div>
    @endcan
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectAllCheckbox = document.getElementById('selectAllPermissions');
            const permissionCheckboxes = document.querySelectorAll('.permission-checkbox');
            const categoryCheckboxes = document.querySelectorAll('.category-checkbox');

            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', function() {
                    const isChecked = this.checked;
                    permissionCheckboxes.forEach(cb => cb.checked = isChecked);
                    categoryCheckboxes.forEach(cb => cb.checked = isChecked);
                });
            }

            categoryCheckboxes.forEach(categoryCheckbox => {
                categoryCheckbox.addEventListener('change', function() {
                    const category = this.dataset.category;
                    const isChecked = this.checked;
                    document.querySelectorAll(`.permission-checkbox[data-category="${category}"]`)
                        .forEach(cb => cb.checked = isChecked);
                    updateSelectAllState();
                });
            });

            permissionCheckboxes.forEach(cb => {
                cb.addEventListener('change', function() {
                    updateCategoryState(this.dataset.category);
                    updateSelectAllState();
                });
            });

            function updateCategoryState(category) {
                const categoryPerms = document.querySelectorAll(
                `.permission-checkbox[data-category="${category}"]`);
                const categoryCheckbox = document.querySelector(`.category-checkbox[data-category="${category}"]`);

                if (categoryCheckbox) {
                    const allChecked = [...categoryPerms].every(c => c.checked);
                    const anyChecked = [...categoryPerms].some(c => c.checked);
                    categoryCheckbox.checked = allChecked;
                    categoryCheckbox.indeterminate = anyChecked && !allChecked;
                }
            }

            function updateSelectAllState() {
                const allChecked = [...permissionCheckboxes].every(cb => cb.checked);
                const anyChecked = [...permissionCheckboxes].some(cb => cb.checked);
                if (selectAllCheckbox) {
                    selectAllCheckbox.checked = allChecked;
                    selectAllCheckbox.indeterminate = anyChecked && !allChecked;
                }
            }
        });
    </script>
@endpush
