@can('show shifts')
    @extends('layouts.app')

    @section('content')
        <div class="row justify-content-center" dir="rtl">
            <div class="col-12">

                <div class="card shadow-lg border-0 rounded-3">
                    <div class="card-header bg-light text-center fs-5 fw-bold d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-clock me-2"></i>قائمة الشيفتات</span>
                        @can('open shift')
                            <x-button type="link" color="success" size="sm" icon="plus-circle" label="فتح شيفت جديد"
                                :href="route('shifts.create')" />
                        @endcan
                    </div>

                    <div class="card-body overflow-x-auto">
                        <x-alert-success />
                        @props(['shifts'])

                        <table class="table table-hover table-striped text-center">
                            <thead class="table-light">
                                <tr>
                                    <th><i class="fas fa-hashtag"></i> #</th>
                                    <th><i class="fas fa-user"></i> الموظف</th>
                                    <th><i class="fas fa-tachometer-alt"></i> العداد في بدايه الشيفت</th>
                                    <th><i class="fas fa-camera"></i> صوره العداد في بديه الشيفت</th>
                                    <th><i class="fas fa-play-circle"></i> بداية الشيفت</th>
                                    <th><i class="fas fa-stop-circle"></i> نهاية الشيفت</th>
                                    <th><i class="fas fa-check-circle"></i> مطابق</th>
                                    <th><i class="fas fa-cogs"></i> خيارات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($shifts as $shift)
                                    <tr>
                                        <td>{{ $shift->id }}</td>
                                        <td>{{ $shift->user->name ?? '---' }}</td>
                                        <td>{{ $shift->nozzleReadings->sum('start_reading') }}</td>
                                        <td>
                                            <a href="{{ $shift->getFirstMediaUrl('start_meter_images') }}" target="_blank">
                                                <img src="{{ $shift->getFirstMediaUrl('start_meter_images') }}"
                                                    alt="صورة العداد" width="100" height="80" style="object-fit: cover;">
                                            </a>
                                        </td>

                                        <td>{{ $shift->start_time }}</td>
                                        <td>
                                            @if ($shift->end_time)
                                                {{ $shift->end_time }}
                                            @else
                                                <span class="badge bg-success">مفتوح</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($shift->meter_match == 1)
                                                <span class="badge bg-success">مطابق</span>
                                            @else
                                                <span class="badge bg-danger">غير مطابق</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if (!$shift->end_time)
                                                @can('close shift')
                                                    <a href="{{ route('shifts.closeForm', $shift->id) }}"
                                                        class="btn btn-danger btn-sm">
                                                        <i class="fas fa-lock me-2"></i> إغلاق
                                                    </a>
                                                @endcan
                                            @endif

                                            @can('show report')
                                                <x-button type="link" color="info" size="sm" icon="file-alt"
                                                    label="تقرير" :href="route('shifts.report', $shift->id)" />
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">لا يوجد شيفتات حالياً</td>
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
