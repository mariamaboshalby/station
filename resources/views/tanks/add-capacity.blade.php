@extends('layouts.app')

@section('content')
<div class="container" dir="rtl">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <x-card title="إضافة سعة للتانك {{ $tank->name }}">

                <form action="{{ route('tanks.addCapacity', $tank->id) }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">السعة الحالية</label>
                        <input type="text" class="form-control" value="{{ $tank->current_level }}" disabled>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">السعة الكلية</label>
                        <input type="text" class="form-control" value="{{ $tank->capacity }}" disabled>
                    </div>

                    <div class="mb-3">
                        <label class="form-label font-bold">الكمية المضافة (لتر)</label>
                        <input type="number" step="0.01" name="amount" class="form-control fw-bold" min="1" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label font-bold">سعر الشراء / لتر (التكلفة)</label>
                        <input type="number" step="0.01" name="cost_per_liter" class="form-control" placeholder="0.00">
                        <div class="form-text text-muted">اتركه فارغاً إذا كنت لا تريد تسجيل تكلفة مالية.</div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch p-0">
                            <label class="form-check-label fw-bold ms-5" for="deduct_from_treasury">خصم المبلغ من الخزنة (تسجيل كمصروف)</label>
                            <input class="form-check-input float-end ms-2" type="checkbox" name="deduct_from_treasury" id="deduct_from_treasury" checked style="margin-right: -2.5em;"> 
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-plus"></i> إضافة
                    </button>
                    <a href="{{ route('tanks.index') }}" class="btn btn-secondary">رجوع</a>
                </form>

            </x-card>

        </div>
    </div>
</div>
@endsection
