@can('show users')
    @extends('layouts.app')
    @section('content')
        <div class="row justify-content-center">
            <div class="col-12">

                <div class="card shadow-lg border-0 rounded-3">
                    @can('add user')
                        <div class="card-header bg-light text-center fs-5 fw-bold d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-users me-2"></i>قائمة المستخدمين</span>
                            <a href="{{ route('users.create') }}" class="btn btn-sm btn-success">
                                <i class="fas fa-user-plus me-1"></i> إضافة موظف جديد
                            </a>
                        </div>
                    @endcan


                    <div class="card-body overflow-x-auto">
                        @if (session('success'))
                            <div class="alert alert-success text-center">
                                {{ session('success') }}
                            </div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <table class="table table-hover table-striped text-center">
                            <thead class="table-light">
                                <tr>
                                    <th><i class="fas fa-hashtag"></i> #</th>
                                    <th><i class="fas fa-user"></i> الاسم</th>
                                    <th><i class="fas fa-phone"></i> رقم التليفون</th>
                                    <th><i class="fas fa-calendar"></i> تاريخ الإضافة</th>
                                    <th><i class="fas fa-cogs"></i> اختيارات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                    <tr>
                                        <td>{{ $user->id }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->phone }}</td>
                                        <td>{{ $user->created_at->format('Y-m-d H:i') }}</td>
                                        <td>
                                            <a href="{{ route('users.shifts', $user->id) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-file-alt me-1"></i>
                                            </a>
                                            <a href="{{ route('users.show', $user->id) }}" class="btn btn-sm btn-info">
                                                <i class="fa-solid fa-circle-info"></i>
                                            </a>
                                            <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-success">
                                                <i class="fas fa-edit me-1"></i>
                                            </a>
                                            <form action="{{ route('users.destroy', $user->id) }}" method="POST"
                                                class="d-inline"
                                                onsubmit="return confirm('هل أنت متأكد من حذف هذا المستخدم؟');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash me-1"></i>
                                                </button>
                                            </form>

                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">لا يوجد مستخدمين حتى الآن</td>
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
