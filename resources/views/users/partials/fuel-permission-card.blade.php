@php
    $query = \App\Models\Fuel::query();

    // البحث بالاسم
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
                   id="category_{{ $category }}"
                   data-category="{{ $category }}">
            <label class="form-check-label small fw-bold text-muted" for="category_{{ $category }}">
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
                                           data-category="{{ $category }}"
                                           id="pump_{{ $pump->id }}">
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // عند الضغط على "اختر الكل"
    document.querySelectorAll('.category-checkbox').forEach(categoryCheckbox => {
        categoryCheckbox.addEventListener('change', function() {
            const category = this.dataset.category;
            const permissions = document.querySelectorAll(`.permission-checkbox[data-category="${category}"]`);
            permissions.forEach(perm => perm.checked = this.checked);
        });
    });

    // لما يغير المستخدم صلاحية فرعية
    document.querySelectorAll('.permission-checkbox').forEach(permissionCheckbox => {
        permissionCheckbox.addEventListener('change', function() {
            const category = this.dataset.category;
            const permissions = document.querySelectorAll(`.permission-checkbox[data-category="${category}"]`);
            const allChecked = Array.from(permissions).every(p => p.checked);
            const anyChecked = Array.from(permissions).some(p => p.checked);
            const categoryCheckbox = document.querySelector(`#category_${category}`);

            if (categoryCheckbox) {
                categoryCheckbox.checked = allChecked;
                categoryCheckbox.indeterminate = anyChecked && !allChecked;
            }
        });
    });
});
</script>
@endpush
