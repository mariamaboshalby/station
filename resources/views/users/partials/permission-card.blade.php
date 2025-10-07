<div class="card mb-3">
    <div class="p-2 ">
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold">
                <i class="fa {{ $icon }} me-2"></i>
                {{ $title }}
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
    <hr>
    <div class="card-body">
        <div class="row">
            @foreach($permissions as $permission)
                <div class="col-md-12 mb-3">
                    <div class="mb-3 ms-3  border-starth-100">
                        <div class=" d-flex align-items-center">
                            <input class="form-check-input permission-checkbox me-2" 
                                   type="checkbox"
                                   name="permissions[]" 
                                   value="{{ $permission['value'] }}"
                                   data-category="{{ $category }}">
                            <label class="form-check-label fw-bold mb-0">
                                {{ $permission['label'] }}
                            </label>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
