<div class="card shadow-sm mb-4 border-0 rounded-3">
    <div class="card-header bg-light d-flex justify-content-between align-items-center py-2 px-3 rounded-top">
        <h6 class="mb-0 fw-bold text-primary">
            <i class="fa {{ $icon }} me-2 text-secondary"></i>
            {{ $title }}
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
                       id="perm_{{ $permission['value'] }}">
                <label class="form-check-label small fw-semibold" for="perm_{{ $permission['value'] }}">
                    {{ $permission['label'] }}
                </label>
            </div>
        @endforeach
    </div>
</div>
