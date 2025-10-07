@can('view dashboard')
    @extends('layouts.app')

    @section('content')
        <style>
            a {
                text-decoration: none;
            }

            .dashboard-card,
            .dashboard-card-main {
                border: none;
                color: #fff;
                transition: all 0.3s ease-in-out;
                box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
                display: flex;
                align-items: center;
                position: relative;
                overflow: hidden;
                height: 200px;
            }

            .dashboard-card:hover,
            .dashboard-card-main:hover {
                transform: translateY(-8px) scale(1.02);
                box-shadow: 0 12px 25px rgba(0, 0, 0, 0.25);
            }

            .dashboard-card-main i {
                font-size: 50rem;
                opacity: 0.2;
                position: absolute;
                left: 22px;
                top: -10rem;
                transform: rotate(70deg);
            }

            .dashboard-card i {
                font-size: 10rem;
                opacity: 0.2;
                position: absolute;
                left: 22px;
                top: 1rem;
                transform: rotate(50deg);
            }

            .dashboard-card h3 {
                font-size: 2rem;
                font-weight: bolder;
                margin: 0;
            }

            .dashboard-card p {
                margin: 0;
                font-size: 1.4rem;
                font-weight: bolder;
                opacity: 0.9;
            }

            .box-content-main h3,
            .box-content-main p {
                font-size: 3rem;
            }

            /* ألوان متدرجة مختلفة لكل كارت */
            .bg-gradient-secondary {
                background: linear-gradient(135deg, #6c757d, #343a40);
            }

            .bg-gradient-success {
                background: linear-gradient(135deg, #28a745, #218838);
            }

            .bg-gradient-warning {
                background: linear-gradient(135deg, #ffc107, #e0a800);
            }

            .bg-gradient-danger {
                background: linear-gradient(135deg, #dc3545, #bd2130);
            }
        </style>

        <div class="container-fluid">
            <div class="row g-4 text-center">

                <!-- عدد التانكات -->
                <a href="{{ route('tanks.index') }}" class=" col-12">
                    <div class="dashboard-card-main rounded-2 p-3 bg-gradient-secondary">
                        <div class="box-content-main">
                            <h3>{{ \App\Models\Tank::count() }}</h3>
                            <p>عدد التانكات</p>
                        </div>
                        <i class="fas fa-database"></i>
                    </div>
                </a>

                <!-- السعة الكلية -->
                <div class="col-lg-4 col-12">
                    <div class="dashboard-card rounded-2 p-3 bg-gradient-success">
                        <div class="box-content">
                            <h3>{{ \App\Models\Tank::sum('capacity') }}</h3>
                            <p>إجمالي السعة الكلية (لتر)</p>
                        </div>
                        <i class="fas fa-gas-pump" style="transform: rotate(0deg);"></i>
                    </div>
                </div>

                <!-- السعة الحالية -->
                <div class="col-lg-4 col-12">
                    <div class="dashboard-card rounded-2 p-3 bg-gradient-warning">
                        <div class="box-content">
                            <h3>{{ \App\Models\Tank::sum('current_level') }}</h3>
                            <p>اجمالي عدداللترات (تراكمي)</p>
                        </div>
                        <i class="fas fa-tachometer-alt"></i>
                    </div>
                </div>

                <!-- عدد العمليات -->
                <div class="col-lg-4 col-12">
                    <div class="dashboard-card rounded-2 p-3 bg-gradient-danger">
                        <div class="box-content">
                            <h3>{{ \App\Models\Transaction::count() }}</h3>
                            <p>إجمالي العمليات</p>
                        </div>
                        <i class="fas fa-exchange-alt"></i>
                    </div>
                </div>

            </div>

            <!-- الجداول -->
            <div class="row mt-5">
                <!-- جدول الشفتات المفتوحة -->
                <div class="col-lg-6 col-12 mb-4">
                    <div class="card shadow">
                        <div class="card-header text-center fw-bold">
                            <i class="fas fa-door-open me-2"></i> الشفتات المفتوحة
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-hover mb-0 text-center">
                                <thead class="table-light">
                                    <tr>
                                        <th>الموظف</th>
                                        <th>بداية الشفت</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse(\App\Models\Shift::whereNull('end_time')->get() as $shift)
                                        <tr>
                                            <td>{{ $shift->user->name ?? '---' }}</td>
                                            <td>{{ $shift->start_time }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2">لا يوجد شفتات مفتوحة</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- جدول الشفتات المقفولة النهارده -->
                <div class="col-lg-6 col-12 mb-4">
                    <div class="card shadow">
                        <div class="card-header text-center fw-bold">
                            <i class="fas fa-door-closed me-2"></i> الشفتات المغلقة اليوم
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-hover mb-0 text-center">
                                <thead class="table-light">
                                    <tr>
                                        <th>الموظف</th>
                                        <th>بداية الشفت</th>
                                        <th>نهاية الشفت</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse(\App\Models\Shift::whereDate('end_time', today())->get() as $shift)
                                        <tr>
                                            <td>{{ $shift->user->name ?? '---' }}</td>
                                            <td>{{ $shift->start_time }}</td>
                                            <td>{{ $shift->end_time }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3">لا يوجد شفتات مغلقة اليوم</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection
@endcan
