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
                        <form action="{{ route('transactions.index') }}" method="GET"
                            class="mb-4 bg-light p-3 rounded shadow-sm border">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label for="client_id" class="form-label fw-bold text-primary"><i
                                            class="fas fa-user-tie me-1"></i>العميل</label>
                                    <select name="client_id" id="client_id" class="form-select">
                                        <option value="">كل العملاء</option>
                                        @foreach ($clients as $client)
                                            <option value="{{ $client->id }}"
                                                {{ request('client_id') == $client->id ? 'selected' : '' }}>
                                                {{ $client->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="user_id" class="form-label fw-bold text-primary"><i
                                            class="fas fa-user me-1"></i>الموظف</label>
                                    <select name="user_id" id="user_id" class="form-select">
                                        <option value="">كل الموظفين</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}"
                                                {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="from_date" class="form-label fw-bold text-primary"><i
                                            class="fas fa-calendar-alt me-1"></i>من تاريخ</label>
                                    <input type="date" name="from_date" id="from_date" class="form-control"
                                        value="{{ request('from_date') }}">
                                </div>
                                <div class="col-md-2">
                                    <label for="to_date" class="form-label fw-bold text-primary"><i
                                            class="fas fa-calendar-alt me-1"></i>إلى تاريخ</label>
                                    <input type="date" name="to_date" id="to_date" class="form-control"
                                        value="{{ request('to_date') }}">
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary w-100 me-2"><i class="fas fa-filter"></i>
                                        تصفية</button>
                                    <a href="{{ route('transactions.index') }}" class="btn btn-secondary w-100"><i
                                            class="fas fa-undo"></i> إعادة</a>
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
                                    <th><i class="fas fa-image"></i> صورة العملية</th>
                                    <th><i class="fas fa-camera"></i> صورة إغلاق الشيفت</th>
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
                                                @php
                                                    $images = $transaction->getMedia('transactions');
                                                    $imageUrls = $images->map(fn($media) => $media->getUrl())->toArray();
                                                @endphp
                                                
                                                @if(count($imageUrls) > 0)
                                                    <!-- Display all images in a small gallery -->
                                                    <div class="d-flex gap-1 flex-wrap">
                                                        @foreach($imageUrls as $index => $imageUrl)
                                                            <img src="{{ $imageUrl }}" 
                                                                 alt="صورة {{ $index + 1 }}" 
                                                                 class="img-thumbnail rounded cursor-pointer" 
                                                                 style="width: 35px; height: 35px; object-fit: cover;"
                                                                 onclick="openGalleryModal('transaction_{{ $transaction->id }}', {{ $index }})">
                                                        @endforeach
                                                    </div>
                                                    
                                                    <!-- Gallery Modal -->
                                                    <div class="modal fade" id="imageGalleryModal_transaction_{{ $transaction->id }}" tabindex="-1" aria-hidden="true">
                                                        <div class="modal-dialog modal-xl modal-dialog-centered">
                                                            <div class="modal-content bg-dark">
                                                                <div class="modal-header border-0">
                                                                    <h5 class="modal-title text-white">صور العملية #{{ $transaction->id }}</h5>
                                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body p-0">
                                                                    <div id="galleryCarousel_transaction_{{ $transaction->id }}" class="carousel slide" data-bs-ride="carousel">
                                                                        <div class="carousel-inner">
                                                                            @foreach($imageUrls as $index => $imageUrl)
                                                                                <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                                                                    <div class="text-center bg-dark">
                                                                                        <img src="{{ $imageUrl }}" 
                                                                                             alt="صورة {{ $index + 1 }}" 
                                                                                             class="img-fluid" 
                                                                                             style="max-height: 80vh; width: auto;">
                                                                                        <div class="carousel-caption d-none d-md-block">
                                                                                            <h5>صورة {{ $index + 1 }} من {{ count($imageUrls) }}</h5>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            @endforeach
                                                                        </div>
                                                                        
                                                                        @if(count($imageUrls) > 1)
                                                                            <button class="carousel-control-prev" type="button" data-bs-target="#galleryCarousel_transaction_{{ $transaction->id }}" data-bs-slide="prev">
                                                                                <span class="carousel-control-prev-icon"></span>
                                                                                <span class="visually-hidden">السابق</span>
                                                                            </button>
                                                                            <button class="carousel-control-next" type="button" data-bs-target="#galleryCarousel_transaction_{{ $transaction->id }}" data-bs-slide="next">
                                                                                <span class="carousel-control-next-icon"></span>
                                                                                <span class="visually-hidden">التالي</span>
                                                                            </button>
                                                                        @endif
                                                                    </div>
                                                                    
                                                                    <!-- Thumbnail Navigation -->
                                                                    @if(count($imageUrls) > 1)
                                                                        <div class="bg-dark p-3 border-top border-secondary">
                                                                            <div class="d-flex gap-2 overflow-auto justify-content-center">
                                                                                @foreach($imageUrls as $index => $imageUrl)
                                                                                    <button type="button" 
                                                                                            class="btn btn-outline-light p-1 {{ $index == 0 ? 'active' : '' }}" 
                                                                                            data-bs-target="#galleryCarousel_transaction_{{ $transaction->id }}" 
                                                                                            data-bs-slide-to="{{ $index }}"
                                                                                            style="min-width: 60px;">
                                                                                        <img src="{{ $imageUrl }}" 
                                                                                             alt="صورة {{ $index + 1 }}" 
                                                                                             class="img-thumbnail" 
                                                                                             style="width: 50px; height: 50px; object-fit: cover;">
                                                                                    </button>
                                                                                @endforeach
                                                                            </div>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    -
                                                @endif
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @if ($transaction->shift && $transaction->shift->hasMedia('end_meter_images'))
                                                @php
                                                    $endImages = $transaction->shift->getMedia('end_meter_images');
                                                    $endImageUrls = $endImages->map(fn($media) => $media->getUrl())->toArray();
                                                @endphp
                                                
                                                @if(count($endImageUrls) > 0)
                                                    <!-- Display all end images in a small gallery -->
                                                    <div class="d-flex gap-1 flex-wrap">
                                                        @foreach($endImageUrls as $index => $imageUrl)
                                                            <img src="{{ $imageUrl }}" 
                                                                 alt="صورة إغلاق {{ $index + 1 }}" 
                                                                 class="img-thumbnail rounded cursor-pointer" 
                                                                 style="width: 35px; height: 35px; object-fit: cover;"
                                                                 onclick="openGalleryModal('shift_end_{{ $transaction->id }}', {{ $index }})">
                                                        @endforeach
                                                    </div>
                                                    
                                                    <!-- Gallery Modal -->
                                                    <div class="modal fade" id="imageGalleryModal_shift_end_{{ $transaction->id }}" tabindex="-1" aria-hidden="true">
                                                        <div class="modal-dialog modal-xl modal-dialog-centered">
                                                            <div class="modal-content bg-dark">
                                                                <div class="modal-header border-0">
                                                                    <h5 class="modal-title text-white">صور إغلاق الشيفت - العملية #{{ $transaction->id }}</h5>
                                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body p-0">
                                                                    <div id="galleryCarousel_shift_end_{{ $transaction->id }}" class="carousel slide" data-bs-ride="carousel">
                                                                        <div class="carousel-inner">
                                                                            @foreach($endImageUrls as $index => $imageUrl)
                                                                                <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                                                                    <div class="text-center bg-dark">
                                                                                        <img src="{{ $imageUrl }}" 
                                                                                             alt="صورة إغلاق {{ $index + 1 }}" 
                                                                                             class="img-fluid" 
                                                                                             style="max-height: 80vh; width: auto;">
                                                                                        <div class="carousel-caption d-none d-md-block">
                                                                                            <h5>صورة {{ $index + 1 }} من {{ count($endImageUrls) }}</h5>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            @endforeach
                                                                        </div>
                                                                        
                                                                        @if(count($endImageUrls) > 1)
                                                                            <button class="carousel-control-prev" type="button" data-bs-target="#galleryCarousel_shift_end_{{ $transaction->id }}" data-bs-slide="prev">
                                                                                <span class="carousel-control-prev-icon"></span>
                                                                                <span class="visually-hidden">السابق</span>
                                                                            </button>
                                                                            <button class="carousel-control-next" type="button" data-bs-target="#galleryCarousel_shift_end_{{ $transaction->id }}" data-bs-slide="next">
                                                                                <span class="carousel-control-next-icon"></span>
                                                                                <span class="visually-hidden">التالي</span>
                                                                            </button>
                                                                        @endif
                                                                    </div>
                                                                    
                                                                    <!-- Thumbnail Navigation -->
                                                                    @if(count($endImageUrls) > 1)
                                                                        <div class="bg-dark p-3 border-top border-secondary">
                                                                            <div class="d-flex gap-2 overflow-auto justify-content-center">
                                                                                @foreach($endImageUrls as $index => $imageUrl)
                                                                                    <button type="button" 
                                                                                            class="btn btn-outline-light p-1 {{ $index == 0 ? 'active' : '' }}" 
                                                                                            data-bs-target="#galleryCarousel_shift_end_{{ $transaction->id }}" 
                                                                                            data-bs-slide-to="{{ $index }}"
                                                                                            style="min-width: 60px;">
                                                                                        <img src="{{ $imageUrl }}" 
                                                                                             alt="صورة إغلاق {{ $index + 1 }}" 
                                                                                             class="img-thumbnail" 
                                                                                             style="width: 50px; height: 50px; object-fit: cover;">
                                                                                    </button>
                                                                                @endforeach
                                                                            </div>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="badge bg-warning">لم يُغلق الشيفت</span>
                                                @endif
                                            @else
                                                <span class="badge bg-warning">لم يُغلق الشيفت</span>
                                            @endif
                                        </td>
                                        <td>{{ $transaction->notes ?? '-' }}</td>
                                        <td>{{ $transaction->created_at->format('Y-m-d H:i') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center">لا توجد عمليات حتى الآن</td>
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
