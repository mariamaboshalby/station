@can('show clients')
    @extends('layouts.app')

    @section('content')
        <div class="" dir="rtl">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="text-primary fw-bold mb-0">
                    حساب العميل: {{ $client->name }}
                </h3>
                <div class="dropdown">
                    <button class="btn btn-danger dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-file-export me-2"></i> تصدير
                    </button>
                    <ul class="dropdown-menu text-end">
                        <li>
                            <a class="dropdown-item" href="{{ route('clients.transactions.pdf', $client->id) }}">
                                <i class="fas fa-file-pdf text-danger me-2"></i> تصدير PDF
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('clients.transactions.excel', $client->id) }}">
                                <i class="fas fa-file-excel text-success me-2"></i> تصدير Excel
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card shadow overflow-x-auto">
                <div class="card-header bg-success text-white fw-bold">
                    سجل التفويلات
                </div>
                <div class="card-body">
                    <table class="table table-bordered text-center align-middle">
                        <thead class="table-success">
                            <tr>
                                <th>التاريخ</th>
                                <th>الشيفت</th>
                                <th>رقم العربية</th>
                                <th>المسدس</th>
                                <th>عدد اللترات</th>
                                <th>سعر اللتر</th>
                                <th>الإجمالي</th>
                                <th>صورة الإيصال</th>
                                <th>الإجراء</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($client->refuelings as $r)
                                <tr>
                                    <td>{{ $r->created_at->format('Y-m-d H:i') }}</td>
                                    <td>{{ $r->shift->user->name }}</td>
                                    <td class="fw-bold">{{ $r->transaction->vehicle_number ?? '-' }}</td>
                                    <td>
                                        @if ($r->transaction && ($r->transaction->pump || $r->transaction->nozzle))
                                            @php
                                                $transaction = $r->transaction;
                                                $fuelName =
                                                    $transaction->pump->tank->fuel->name ??
                                                    ($transaction->nozzle->pump->tank->fuel->name ?? '---');
                                                        $badgeClass = '';
                                                        if (str_contains($fuelName, '95')) {
                                                            $badgeClass = 'bg-danger text-white';
                                                        } elseif (str_contains($fuelName, '80')) {
                                                            $badgeClass = 'bg-primary text-white';
                                                        } elseif (str_contains($fuelName, '92')) {
                                                            $badgeClass = 'bg-success text-white';
                                                        } elseif (str_contains($fuelName, 'سولار')) {
                                                            $badgeClass = 'bg-warning text-dark';
                                                        } else {
                                                            $badgeClass = 'bg-secondary text-white';
                                                        }
                                            @endphp
                                            <span class="badge {{ $badgeClass }}">
                                                {{ $fuelName }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $r->liters }}</td>
                                    <td>{{ $r->price_per_liter }}</td>
                                    <td>{{ $r->total_amount }}</td>
                                    <td>
                                        @if ($r->transaction && $r->transaction->hasMedia('transactions'))
                                            @php
                                                $images = $r->transaction->getMedia('transactions');
                                                $imageUrls = $images->map(fn($media) => $media->getUrl())->toArray();
                                            @endphp

                                            @if (count($imageUrls) > 0)
                                                <!-- Display all images in a small gallery -->
                                                <div class="d-flex gap-1 flex-wrap">
                                                    @foreach ($imageUrls as $index => $imageUrl)
                                                        <img src="{{ $imageUrl }}" alt="صورة {{ $index + 1 }}"
                                                            class="img-thumbnail rounded cursor-pointer"
                                                            style="width: 35px; height: 35px; object-fit: cover;"
                                                            onclick="openGalleryModal('client_{{ $r->id }}', {{ $index }})">
                                                    @endforeach
                                                </div>

                                                <!-- Gallery Modal -->
                                                <div class="modal fade" id="imageGalleryModal_client_{{ $r->id }}"
                                                    tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog modal-xl modal-dialog-centered">
                                                        <div class="modal-content bg-dark">
                                                            <div class="modal-header border-0">
                                                                <h5 class="modal-title text-white">صور الإيصال - العملية
                                                                    #{{ $r->id }}</h5>
                                                                <button type="button" class="btn-close btn-close-white"
                                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body p-0">
                                                                <div id="galleryCarousel_client_{{ $r->id }}"
                                                                    class="carousel slide" data-bs-ride="carousel">
                                                                    <div class="carousel-inner">
                                                                        @foreach ($imageUrls as $index => $imageUrl)
                                                                            <div
                                                                                class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                                                                <div class="text-center bg-dark">
                                                                                    <img src="{{ $imageUrl }}"
                                                                                        alt="صورة {{ $index + 1 }}"
                                                                                        class="img-fluid"
                                                                                        style="max-height: 80vh; width: auto;">
                                                                                    <div
                                                                                        class="carousel-caption d-none d-md-block">
                                                                                        <h5>صورة {{ $index + 1 }} من
                                                                                            {{ count($imageUrls) }}</h5>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>

                                                                    @if (count($imageUrls) > 1)
                                                                        <button class="carousel-control-prev" type="button"
                                                                            data-bs-target="#galleryCarousel_client_{{ $r->id }}"
                                                                            data-bs-slide="prev">
                                                                            <span class="carousel-control-prev-icon"></span>
                                                                            <span class="visually-hidden">السابق</span>
                                                                        </button>
                                                                        <button class="carousel-control-next" type="button"
                                                                            data-bs-target="#galleryCarousel_client_{{ $r->id }}"
                                                                            data-bs-slide="next">
                                                                            <span class="carousel-control-next-icon"></span>
                                                                            <span class="visually-hidden">التالي</span>
                                                                        </button>
                                                                    @endif
                                                                </div>

                                                                <!-- Thumbnail Navigation -->
                                                                @if (count($imageUrls) > 1)
                                                                    <div class="bg-dark p-3 border-top border-secondary">
                                                                        <div
                                                                            class="d-flex gap-2 overflow-auto justify-content-center">
                                                                            @foreach ($imageUrls as $index => $imageUrl)
                                                                                <button type="button"
                                                                                    class="btn btn-outline-light p-1 {{ $index == 0 ? 'active' : '' }}"
                                                                                    data-bs-target="#galleryCarousel_client_{{ $r->id }}"
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
                                                <span class="text-muted">-</span>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <form action="{{ route('client_refuelings.destroy', $r->id) }}" method="POST"
                                            onsubmit="return confirm('هل أنت متأكد من حذف هذه العملية؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endsection

    @push('script')
        <script>
            // Open gallery modal for client transactions
            window.openGalleryModal = function(galleryId, startIndex = 0) {
                const modal = document.getElementById('imageGalleryModal_' + galleryId);
                const carousel = document.getElementById('galleryCarousel_' + galleryId);

                if (modal && carousel) {
                    const bsCarousel = new bootstrap.Carousel(carousel);
                    bsCarousel.to(startIndex);

                    const bsModal = new bootstrap.Modal(modal);
                    bsModal.show();
                }
            };
        </script>
    @endpush
@endcan
