@can('add transaction')
    @extends('layouts.app')

    @section('content')
        <div class="container" dir="rtl">
            <div class="row justify-content-center">
                <div class="col-md-8">

                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-success text-white fw-bold">
                            <i class="fas fa-plus-circle me-2"></i> إضافة عملية جديدة
                        </div>

                        {{-- زر إغلاق الشيفت --}}
                        @isset($shift)
                            <a href="{{ route('shifts.closeForm', $shift->id) }}" class="btn btn-danger m-2 col-md-3 col-12 ms-auto">
                                <i class="fas fa-lock me-2"></i> إغلاق شيفت
                            </a>
                        @endisset

                        <div class="card-body">

                            {{-- رسالة نجاح --}}
                            @if (session('success'))
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
                                </div>
                            @endif

                            {{-- رسالة خطأ --}}
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            {{-- Image Gallery Modal --}}
                            <div class="modal fade" id="imageGalleryModal_transaction_gallery" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-xl modal-dialog-centered">
                                    <div class="modal-content bg-dark">
                                        <div class="modal-header border-0">
                                            <h5 class="modal-title text-white">معرض الصور</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-0">
                                            <div id="galleryCarousel_transaction_gallery" class="carousel slide" data-bs-ride="carousel">
                                                <div class="carousel-inner">
                                                    <!-- Images will be populated dynamically -->
                                                </div>
                                                
                                                <button class="carousel-control-prev" type="button" data-bs-target="#galleryCarousel_transaction_gallery" data-bs-slide="prev">
                                                    <span class="carousel-control-prev-icon"></span>
                                                    <span class="visually-hidden">السابق</span>
                                                </button>
                                                <button class="carousel-control-next" type="button" data-bs-target="#galleryCarousel_transaction_gallery" data-bs-slide="next">
                                                    <span class="carousel-control-next-icon"></span>
                                                    <span class="visually-hidden">التالي</span>
                                                </button>
                                            </div>
                                            
                                            <!-- Thumbnail Navigation -->
                                            <div class="bg-dark p-3 border-top border-secondary" id="thumbnailNav_transaction_gallery" style="display: none;">
                                                <!-- Thumbnails will be populated dynamically -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- فورم إضافة العملية --}}
                            <form action="{{ route('transactions.store') }}" method="POST" enctype="multipart/form-data"
                                class="p-3">
                                @csrf

                                {{-- الشيفت --}}
                                @role('admin')
                                    <div class="mb-3">
                                        <label for="shift_id" class="form-label fw-bold">الشيفت</label>
                                        <select name="shift_id" id="shift_id" class="form-select" required>
                                            <option value="">-- اختر الشيفت --</option>
                                            @foreach ($shifts as $shift)
                                                <option value="{{ $shift->id }}">
                                                    {{ $shift->user->name }} - {{ $shift->start_time }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @else
                                    <input type="hidden" name="shift_id" value="{{ $shifts->first()->id ?? '' }}">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">الشيفت الحالي</label>
                                        <input type="text" class="form-control text-center"
                                            value="{{ $shifts->first()->user->name . ' - ' . $shifts->first()->start_time }}"
                                            readonly>
                                    </div>
                                @endrole

                                {{-- نوع العملية --}}
                                <div class="mb-3">
                                    <label class="form-label fw-bold">نوع العملية</label>
                                    <input type="text" class="form-control text-center bg-light" value="آجل" readonly>
                                </div>

                                {{-- المسدس --}}
                                <div class="mb-3">
                                    <label for="nozzle_id" class="form-label fw-bold">المسدس (الطلمبة)</label>
                                    <select name="nozzle_id" id="nozzle_id" class="form-select" required>
                                        <option value="">-- اختر المسدس المستخدم --</option>
                                        @foreach ($nozzles as $nozzle)
                                            <option value="{{ $nozzle->id }}">
                                                {{ $nozzle->name }} - {{ $nozzle->pump->name }}
                                                ({{ $nozzle->pump->tank->fuel->name }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="client_id" class="form-label fw-bold">اسم العميل</label>
                                    <select id="client_id" name="client_id" class="form-select" placeholder="ابحث عن العميل...">
                                        <option value="">-- اختر العميل --</option>
                                    </select>
                                </div>

                                {{-- رقم العربية --}}
                                <div class="mb-3">
                                    <label for="vehicle_number" class="form-label fw-bold">رقم العربية</label>
                                    <input type="text" name="vehicle_number" id="vehicle_number"
                                        class="form-control text-center" placeholder="مثال: أ ب ج 1234">
                                </div>


                                {{-- عدد اللترات --}}
                                <div class="mb-3">
                                    <label for="credit_liters" class="form-label fw-bold">عدد اللترات المسحوبة</label>
                                    <input type="number" step="0.01" name="credit_liters" id="credit_liters"
                                        class="form-control text-center" required min="0.01">
                                </div>

                                {{-- صورة العداد --}}
                                <div class="mb-3">
                                    <label for="image" class="form-label fw-bold">صورة العداد</label>
                                    
                                    <!-- Camera capture section -->
                                    <div id="camera_section" class="capture-section">
                                        <video id="camera_video" style="width: 100%; max-height: 300px; border-radius: 8px; display: none;" autoplay></video>
                                        <canvas id="camera_canvas" style="display: none;"></canvas>
                                        
                                        <div class="d-grid gap-2 mb-3">
                                            <button type="button" id="start_camera" class="btn btn-primary">
                                                <i class="fas fa-video me-1"></i> تشغيل الكاميرا
                                            </button>
                                            <button type="button" id="capture_photo" class="btn btn-success" style="display: none;">
                                                <i class="fas fa-camera me-1"></i> التقاط صورة
                                            </button>
                                            <button type="button" id="add_more_photos" class="btn btn-info" style="display: none;">
                                                <i class="fas fa-plus me-1"></i> إضافة صورة إضافية
                                            </button>
                                            <button type="button" id="retake_photo" class="btn btn-warning" style="display: none;">
                                                <i class="fas fa-redo me-1"></i> إعادة التصوير
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Hidden field to store captured images data -->
                                    <input type="hidden" name="captured_images_data" id="captured_images_data">

                                    {{-- عرض الصور قبل الحفظ --}}
                                    <div class="mt-3">
                                        <h6 class="text-muted mb-2">الصور الملتقطة (<span id="photo_count">0</span>):</h6>
                                        <div id="gallery_transaction_gallery" class="gallery-container">
                                            <!-- Gallery will be populated dynamically -->
                                        </div>
                                    </div>
                                </div>

                                {{-- الملاحظات --}}
                                <div class="mb-3">
                                    <label for="notes" class="form-label fw-bold">ملاحظات</label>
                                    <textarea name="notes" id="notes" rows="3" class="form-control" placeholder="اكتب أي ملاحظات إضافية..."></textarea>
                                </div>

                                {{-- زر الحفظ --}}
                                <div class="d-flex justify-content-center mt-4">
                                    <button type="submit" class="btn btn-success btn-lg px-5">
                                        <i class="fas fa-save me-1"></i> حفظ العملية
                                    </button>
                                </div>
                            </form>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    @endsection
    @push('script')
        <script>
            let stream = null;
            let capturedImagesData = [];

            document.addEventListener('DOMContentLoaded', function() {
                // Client search functionality
                new TomSelect("#client_id", {
                    valueField: 'id',
                    labelField: 'name',
                    searchField: 'name',
                    load: function(query, callback) {
                        if (!query.length) return callback();
                        fetch(`/clients/search?term=${encodeURIComponent(query)}`)
                            .then(response => response.json())
                            .then(json => {
                                callback(json);
                            })
                            .catch(() => {
                                callback();
                            });
                    },
                    placeholder: 'ابحث عن العميل...',
                    create: false,
                    maxOptions: 20,
                    render: {
                        option: function(item, escape) {
                            return `<div><strong>${escape(item.name)}</strong></div>`;
                        }
                    }
                });

                // Camera functionality
                const startCameraBtn = document.getElementById('start_camera');
                const capturePhotoBtn = document.getElementById('capture_photo');
                const addMorePhotosBtn = document.getElementById('add_more_photos');
                const retakePhotoBtn = document.getElementById('retake_photo');
                const video = document.getElementById('camera_video');
                const canvas = document.getElementById('camera_canvas');
                const photosContainer = document.getElementById('photos_container');
                const photoCount = document.getElementById('photo_count');
                const capturedImagesInput = document.getElementById('captured_images_data');

                // Start camera
                startCameraBtn.addEventListener('click', async function() {
                    try {
                        stream = await navigator.mediaDevices.getUserMedia({ 
                            video: { 
                                facingMode: 'environment',
                                width: { ideal: 1280 },
                                height: { ideal: 720 }
                            } 
                        });
                        video.srcObject = stream;
                        video.style.display = 'block';
                        startCameraBtn.style.display = 'none';
                        capturePhotoBtn.style.display = 'block';
                    } catch (err) {
                        console.error('Error accessing camera:', err);
                        alert('لا يمكن الوصول إلى الكاميرا. يرجى التأكد من منح الإذن واستخدام كاميرا تعمل.');
                    }
                });

                // Capture photo
                capturePhotoBtn.addEventListener('click', function() {
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    const context = canvas.getContext('2d');
                    context.drawImage(video, 0, 0);
                    
                    const imageData = canvas.toDataURL('image/jpeg', 0.8);
                    capturedImagesData.push(imageData);
                    
                    displayPhoto(imageData, capturedImagesData.length - 1);
                    updatePhotosData();
                    
                    capturePhotoBtn.style.display = 'none';
                    addMorePhotosBtn.style.display = 'block';
                    
                    stopCamera();
                });

                // Add more photos
                addMorePhotosBtn.addEventListener('click', function() {
                    startCameraBtn.click();
                    addMorePhotosBtn.style.display = 'none';
                });

                // Retake photo (remove last photo)
                retakePhotoBtn.addEventListener('click', function() {
                    if (capturedImagesData.length > 0) {
                        capturedImagesData.pop();
                        updatePhotosDisplay();
                        updatePhotosData();
                    }
                    retakePhotoBtn.style.display = 'none';
                    startCameraBtn.style.display = 'block';
                });

                // Display photo function - Updated for Gallery Component
                function displayPhoto(imageData, index) {
                    // Update the gallery component by refreshing the images array
                    updatePhotosDisplay();
                }

                // Remove photo function - Updated for Gallery Component
                window.removePhoto = function(index) {
                    capturedImagesData.splice(index, 1);
                    updatePhotosDisplay();
                    updatePhotosData();
                };

                // Listen for gallery remove events
                document.addEventListener('removeGalleryImage', function(e) {
                    if (e.detail.galleryId === 'transaction_gallery') {
                        removePhoto(e.detail.index);
                    }
                });

                // Update photos display - Updated for Gallery Component
                function updatePhotosDisplay() {
                    photoCount.textContent = capturedImagesData.length;
                    
                    // Update the gallery component by refreshing the container
                    const galleryContainer = document.getElementById('gallery_transaction_gallery');
                    if (galleryContainer) {
                        // Clear and rebuild the gallery
                        const parent = galleryContainer.parentElement;
                        galleryContainer.remove();
                        
                        // Create new gallery with updated images
                        const newGalleryHtml = createGalleryHtml(capturedImagesData, 'transaction_gallery');
                        parent.insertAdjacentHTML('beforeend', newGalleryHtml);
                    }
                    
                    // Show/hide retake button
                    if (capturedImagesData.length > 0) {
                        retakePhotoBtn.style.display = 'block';
                    } else {
                        retakePhotoBtn.style.display = 'none';
                    }
                }

                // Create gallery HTML dynamically
                function createGalleryHtml(images, galleryId) {
                    let html = `<div id="gallery_${galleryId}" class="gallery-container">`;
                    html += '<div class="row g-2">';
                    
                    images.forEach((imageData, index) => {
                        html += `
                            <div class="col-md-3 col-6 gallery-item">
                                <div class="position-relative">
                                    <img src="${imageData}" 
                                         alt="صورة ${index + 1}" 
                                         class="img-thumbnail rounded cursor-pointer gallery-image" 
                                         style="width: 100%; height: 120px; object-fit: cover;"
                                         onclick="openGalleryModal('${galleryId}', ${index})"
                                         data-gallery-id="${galleryId}"
                                         data-image-index="${index}">
                                    
                                    <button type="button" 
                                            class="btn btn-danger btn-sm position-absolute top-0 start-0 m-1" 
                                            onclick="removeGalleryImage('${galleryId}', ${index})" 
                                            style="width: 30px; height: 30px; padding: 0;">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    
                                    <div class="position-absolute bottom-0 start-0 m-1">
                                        <span class="badge bg-dark bg-opacity-75 text-white">صورة ${index + 1}</span>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    
                    html += '</div>';
                    
                    if (images.length > 3) {
                        html += `
                            <div class="text-center mt-2">
                                <button type="button" 
                                        class="btn btn-sm btn-outline-primary"
                                        onclick="openGalleryModal('${galleryId}', 0)">
                                    <i class="fas fa-images me-1"></i> عرض جميع الصور (${images.length})
                                </button>
                            </div>
                        `;
                    }
                    
                    html += '</div>';
                    return html;
                }

                // Update photos data in hidden input
                function updatePhotosData() {
                    capturedImagesInput.value = JSON.stringify(capturedImagesData);
                }

                // Helper functions
                function stopCamera() {
                    if (stream) {
                        stream.getTracks().forEach(track => track.stop());
                        stream = null;
                    }
                    video.style.display = 'none';
                }

                // Open image modal function - Updated for Gallery Component
                window.openGalleryModal = function(galleryId, startIndex = 0) {
                    const modal = document.getElementById('imageGalleryModal_' + galleryId);
                    const carousel = document.getElementById('galleryCarousel_' + galleryId);
                    
                    if (modal && carousel) {
                        // Update carousel images
                        const carouselInner = carousel.querySelector('.carousel-inner');
                        carouselInner.innerHTML = '';
                        
                        capturedImagesData.forEach((imageData, index) => {
                            const carouselItem = document.createElement('div');
                            carouselItem.className = `carousel-item ${index === startIndex ? 'active' : ''}`;
                            carouselItem.innerHTML = `
                                <div class="text-center bg-dark">
                                    <img src="${imageData}" 
                                         alt="صورة ${index + 1}" 
                                         class="img-fluid" 
                                         style="max-height: 80vh; width: auto;">
                                    <div class="carousel-caption d-none d-md-block">
                                        <h5>صورة ${index + 1} من ${capturedImagesData.length}</h5>
                                    </div>
                                </div>
                            `;
                            carouselInner.appendChild(carouselItem);
                        });
                        
                        // Update thumbnails
                        const thumbnailNav = document.getElementById('thumbnailNav_' + galleryId);
                        if (capturedImagesData.length > 1) {
                            thumbnailNav.style.display = 'block';
                            thumbnailNav.innerHTML = `
                                <div class="d-flex gap-2 overflow-auto justify-content-center">
                                    ${capturedImagesData.map((imageData, index) => `
                                        <button type="button" 
                                                class="btn btn-outline-light p-1 ${index === startIndex ? 'active' : ''}" 
                                                data-bs-target="#galleryCarousel_${galleryId}" 
                                                data-bs-slide-to="${index}"
                                                style="min-width: 60px;">
                                            <img src="${imageData}" 
                                                 alt="صورة ${index + 1}" 
                                                 class="img-thumbnail" 
                                                 style="width: 50px; height: 50px; object-fit: cover;">
                                        </button>
                                    `).join('')}
                                </div>
                            `;
                        } else {
                            thumbnailNav.style.display = 'none';
                        }
                        
                        const bsCarousel = new bootstrap.Carousel(carousel);
                        bsCarousel.to(startIndex);
                        
                        const bsModal = new bootstrap.Modal(modal);
                        bsModal.show();
                    }
                };

                // Form validation
                const form = document.querySelector('form');
                form.addEventListener('submit', function(e) {
                    if (capturedImagesData.length === 0) {
                        e.preventDefault();
                        alert('يرجى التقاط صورة بالكاميرا قبل حفظ العملية');
                        return;
                    }
                });

                // Cleanup on page unload
                window.addEventListener('beforeunload', function() {
                    stopCamera();
                });
            });
        </script>
    @endpush
@endcan
