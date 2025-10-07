@can('show shifts')
    @extends('layouts.app')

    @section('content')
        <div class="container" dir="rtl">
            <div class="row justify-content-center">
                <div class="col-12">

                    <div class="card shadow-lg border-0 rounded-3">
                        <div
                            class="card-header bg-light text-center fs-5 fw-bold d-flex justify-content-between align-items-center">
                            <span>قائمة الشيفتات</span>
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
                                        <th>#</th>
                                        <th>الموظف</th>
                                        <th>بداية الشيفت</th>
                                        <th>نهاية الشيفت</th>
                                        <th>خيارات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($shifts as $shift)
                                        <tr>
                                            <td>{{ $shift->id }}</td>
                                            <td>{{ $shift->user->name ?? '---' }}</td>
                                            <td>{{ \Carbon\Carbon::parse($shift->start_time)->timezone('Africa/Cairo')->format('Y-m-d H:i:s') }}
                                            </td>
                                            <td>
                                                @if ($shift->end_time)
                                                    {{ \Carbon\Carbon::parse($shift->end_time)->timezone('Africa/Cairo')->format('Y-m-d H:i:s') }}
                                                @else
                                                    <span class="badge bg-success">مفتوح</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if (!$shift->end_time)
                                                    @can('close shift')
                                                        <form action="{{ route('shifts.close', $shift->id) }}" method="POST"
                                                            style="display:inline-block"
                                                            onsubmit="return confirm('هل أنت متأكد من إغلاق هذا الشيفت؟')">
                                                            @csrf
                                                            @method('PATCH')
                                                            <x-button type="submit" color="danger" size="sm" icon="lock"
                                                                label="إغلاق" />
                                                        </form>
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
                                            <td colspan="5" class="text-center">لا يوجد شيفتات حالياً</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>

                            <div class="d-flex justify-content-center mt-3">
                                {{ $shifts->links() }}
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    @endsection
@endcan
