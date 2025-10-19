@can('add client')

@extends('layouts.app')

@section('content')
<div class="container" dir="rtl">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="card">
                <div class="card-header bg-success text-white fw-bold">
                    إضافة عميل جديد
                </div>

                <div class="card-body">
                    <form action="{{ route('clients.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">اسم العميل</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">المبلغ المدفوع</label>
                            <input type="number" step="0.01" name="amount_paid" class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-success">حفظ</button>
                        <a href="{{ route('clients.index') }}" class="btn btn-secondary">رجوع</a>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
    
@endcan