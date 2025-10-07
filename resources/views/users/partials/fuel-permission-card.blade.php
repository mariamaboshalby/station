@php
    $query = \App\Models\Fuel::query();

    // نبحث بالاسم مباشرة
    if(!empty($fuelData['name'])) {
        $query->where('name', 'like', "%{$fuelData['name']}%");
    }

    $fuel = $query->first();
    $tanks = $fuel ? $fuel->tanks()->with(['pumps'])->get() : collect();
@endphp

<div class="card">
    <div class="p-2">
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold">
                <i class="fa {{ $fuelData['icon'] ?? 'fa-gas-pump' }} me-2"></i>
                {{ $fuelData['name'] ?? 'وقود' }} 
            </h6>
            <div class="form-check">
                <input class="form-check-input category-checkbox" 
                       type="checkbox" 
                       id="category_{{ $category }}"
                       data-category="{{ $category }}">
                <label class="form-check-label small fw-bold" for="category_{{ $category }}">
                    اختر الكل
                </label>
            </div>
        </div>
    </div>
    <div class="">
        @if($tanks->count() > 0)
            @foreach($tanks as $tank)
                <div class="tank-section mb-4 p-3 border rounded ">
                    <h6 class="fw-bold mb-3">
                        <i class="fa fa-oil-can me-2"></i> تانك: {{ $tank->name }}
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
                                    <label class="form-check-label fw-bold" for="pump_{{ $pump->id }}">
                                        <i class="fa fa-tachometer-alt me-1"></i> طلمبة: {{ $pump->name }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="alert alert-info py-1">
                            <small><i class="fa fa-info-circle me-1"></i> لا توجد طلمبات لهذا التانك</small>
                        </div>
                    @endif
                </div>
            @endforeach
        @else
            <div class="alert alert-warning text-center">
                <i class="fa fa-exclamation-triangle me-2"></i> لا توجد تانكات {{ $fuelData['name'] ?? '' }}
            </div>
        @endif
    </div>
</div>
