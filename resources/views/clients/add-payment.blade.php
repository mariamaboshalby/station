@can('edit client')

@extends('layouts.app')

@section('content')
    <div class="container" dir="rtl">
        <div class="row justify-content-center">
            <div class="col-md-6">

                <x-card title="إضافة مبلغ {{ $client->name }}">
                    <form action="{{ route('clients.addPayment', $client->id) }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">المبلغ المضاف</label>
                            <input type="number" name="added_amount" step="0.01" min="0" class="form-control"
                                placeholder="أدخل المبلغ" required>
                        </div>

                        <div class="text-center"> 
                            <button type="submit" class="btn btn-success px-5">إضافة المبلغ</button>
                        </div>
                    </form>
                </x-card>

            </div>
        </div>
    </div>
@endsection
    
@endcan