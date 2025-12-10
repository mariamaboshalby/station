@can('show transaction')
    @extends('layouts.app')

    @section('content')
        <div class="row justify-content-center" dir="rtl">
            <div class="col-12">
                <div class="card shadow-lg border-0 rounded-3">
                    <div class="card-header bg-light text-center fs-5 fw-bold d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-exchange-alt me-2"></i>قائمة العمليات</span>
                        @can('add transaction')
                            <a href="{{ route('transactions.create') }}" class="btn btn-sm btn-success">
                                <i class="fas fa-plus-circle me-1"></i> إضافة عملية جديدة
                            </a>
                        @endcan
                    </div>

                    <div class="card-body overflow-x-auto">
                        @if (session('success'))
                            <div class="alert alert-success text-center">
                                {{ session('success') }}
                            </div>
                        @endif

                        {{-- Filter Form --}}
                        <form action="{{ route('transactions.index') }}" method="GET" class="mb-4 bg-light p-3 rounded shadow-sm border">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label for="client_id" class="form-label fw-bold text-primary"><i class="fas fa-user-tie me-1"></i>العميل</label>
                                    <select name="client_id" id="client_id" class="form-select">
                                        <option value="">كل العملاء</option>
                                        @foreach($clients as $client)
                                            <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                                                {{ $client->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="user_id" class="form-label fw-bold text-primary"><i class="fas fa-user me-1"></i>الموظف</label>
                                    <select name="user_id" id="user_id" class="form-select">
                                        <option value="">كل الموظفين</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="from_date" class="form-label fw-bold text-primary"><i class="fas fa-calendar-alt me-1"></i>من تاريخ</label>
                                    <input type="date" name="from_date" id="from_date" class="form-control" value="{{ request('from_date') }}">
                                </div>
                                <div class="col-md-2">
                                    <label for="to_date" class="form-label fw-bold text-primary"><i class="fas fa-calendar-alt me-1"></i>إلى تاريخ</label>
                                    <input type="date" name="to_date" id="to_date" class="form-control" value="{{ request('to_date') }}">
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary w-100 me-2"><i class="fas fa-filter"></i> تصفية</button>
                                    <a href="{{ route('transactions.index') }}" class="btn btn-secondary w-100"><i class="fas fa-undo"></i> إعادة</a>
                                </div>
                            </div>
                        </form>

                        <table class="table table-hover table-striped text-center align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th><i class="fas fa-user"></i> الموظف</th>
                                    <th><i class="fas fa-cog"></i> الطرمبة</th>
                                    <th><i class="fas fa-user-tie"></i> العميل (آجل)</th>
                                    <th><i class="fas fa-clock"></i> اللترات الآجل</th>
                                    <th><i class="fas fa-money-bill-wave"></i> اللترات كاش</th>
                                    <th><i class="fas fa-calculator"></i> إجمالي السعر</th>
                                    <th><i class="fas fa-image"></i> الصورة</th>
                                    <th><i class="fas fa-sticky-note"></i> ملاحظات</th>
                                    <th><i class="fas fa-calendar"></i> تاريخ العملية</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $transaction)
                                    <tr>
                                        <td>{{ $transaction->shift->user->name ?? '-' }}</td>
                                        <td>{{ $transaction->pump->name ?? '-' }}-تانك
                                            {{ $transaction->pump->tank->name ?? '-' }} -
                                            {{ $transaction->pump->tank->fuel->name ?? '-' }}</td>
                                        <td>{{ $transaction->client->name ?? '-' }}</td>
                                        <td>{{ $transaction->credit_liters }}</td>
                                        <td>{{ $transaction->cash_liters }}</td>
                                        <td>{{ number_format($transaction->total_amount, 2) }}</td>
                                        <td>
                                            @if ($transaction->hasMedia('transactions'))
                                                <a href="{{ $transaction->getFirstMediaUrl('transactions') }}" target="_blank">
                                                    <img src="{{ $transaction->getFirstMediaUrl('transactions') }}"
                                                        alt="صورة العملية" width="100" height="80"
                                                        style="object-fit: cover;">
                                                </a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ $transaction->notes ?? '-' }}</td>
                                        <td>{{ $transaction->created_at->format('Y-m-d H:i') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">لا توجد عمليات حتى الآن</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>


                    </div>

                </div>
            </div>
        </div>
    @endsection
@endcan
