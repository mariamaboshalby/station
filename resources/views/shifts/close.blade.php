@can('close shift')
    @extends('layouts.app')

    @section('content')
        <div class="container" dir="rtl">
            <div class="row justify-content-center">
                <div class="col-lg-8"> {{-- عرض أقل للتركيز --}}

                    <div class="card shadow-sm border-0 rounded-3">

                        {{-- الهيدر الأحمر البسيط --}}
                        <div class="card-header bg-danger text-white text-center py-3">
                            <h4 class="mb-0 fw-bold">إغلاق الشيفت</h4>
                            <small class="opacity-75">{{ $shift->user->name }} -
                                {{ $shift->start_time->format('Y-m-d h:i A') }}</small>
                        </div>

                        <div class="card-body p-4 bg-white">

                            <x-alert-success />

                            <form action="{{ route('shifts.close', $shift->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PATCH')

                                {{-- عرض المبيعات الآجلة إن وجدت بشكل بسيط --}}
                                @if ($totalCreditLiters > 0)
                                    <div class="mb-4">
                                        <label class="form-label text-muted">إجمالي المبيعات الآجلة في هذا الشيفت</label>
                                        <input type="text" class="form-control bg-light"
                                            value="{{ number_format($totalCreditLiters, 2) }} لتر" readonly>
                                    </div>
                                @endif

                                {{-- حلقة المسدسات: عرض بسيط --}}
                                @foreach ($shift->nozzleReadings as $index => $reading)
                                    <div class="mb-4 p-3 rounded bg-light border-start border-4 border-danger">

                                        <div class="d-flex justify-content-between mb-2">
                                            <label class="fw-bold text-dark">
                                                {{ $reading->nozzle->name }}
                                                <small
                                                    class="text-muted">({{ $reading->nozzle->pump->tank->fuel->name ?? '' }})</small>
                                            </label>
                                            <span class="text-muted small">قراءة البداية: {{ $reading->start_reading }}</span>
                                        </div>

                                        <div class="form-group">
                                            <input type="number" step="0.01"
                                                name="nozzle_end_readings[{{ $reading->nozzle_id }}]"
                                                class="form-control form-control-lg end-reading-input"
                                                placeholder="اكتب القراءة النهائية هنا" required
                                                min="{{ $reading->start_reading }}" data-start="{{ $reading->start_reading }}">

                                            <div class="d-flex justify-content-between mt-1">
                                                <small class="text-muted">القراءة الحالية بالعداد:
                                                    {{ $reading->nozzle->meter_reading }}</small>
                                                <small class="text-success fw-bold calculated-liters" style="display:none">0.00
                                                    لتر</small>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                {{-- الإجمالي --}}
                                <div class="text-center mb-4 pt-2 border-top">
                                    <label class="text-muted mb-1">اجمالي السحب (اللتر)</label>
                                    <h2 class="fw-bold text-dark" id="total-liters-display">0.00</h2>
                                </div>

                                {{-- الصورة --}}
                                <div class="mb-3">
                                    <label class="form-label fw-bold">صورة العداد عند الإغلاق</label>

                                    <!-- Camera capture section -->
                                    <div id="camera_section" class="capture-section">
                                        <video id="camera_video"
                                            style="width: 100%; max-height: 300px; border-radius: 8px; display: none;"
                                            autoplay></video>
                                        <canvas id="camera_canvas" style="display: none;"></canvas>

                                        <div class="d-grid gap-2 mb-3">
                                            <button type="button" id="start_camera" class="btn btn-primary">
                                                <i class="fas fa-video me-1"></i> تشغيل الكاميرا
                                            </button>
                                            <button type="button" id="capture_photo" class="btn btn-success"
                                                style="display: none;">
                                                <i class="fas fa-camera me-1"></i> التقاط صورة
                                            </button>
                                            <button type="button" id="add_more_photos" class="btn btn-info"
                                                style="display: none;">
                                                <i class="fas fa-plus me-1"></i> إضافة صورة إضافية
                                            </button>
                                            <button type="button" id="retake_photo" class="btn btn-warning"
                                                style="display: none;">
                                                <i class="fas fa-redo me-1"></i> إعادة التصوير
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Hidden field to store captured images data -->
                                    <input type="hidden" name="captured_images_data" id="captured_images_data">

                                    {{-- Image Gallery Modal --}}
                                    <div class="modal fade" id="imageGalleryModal_shift_gallery" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-xl modal-dialog-centered">
                                            <div class="modal-content bg-dark">
                                                <div class="modal-header border-0">
                                                    <h5 class="modal-title text-white">معرض الصور</h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body p-0">
                                                    <div id="galleryCarousel_shift_gallery" class="carousel slide" data-bs-ride="carousel">
                                                        <div class="carousel-inner">
                                                            <!-- Images will be populated dynamically -->
                                                        </div>
                                                        
                                                        <button class="carousel-control-prev" type="button" data-bs-target="#galleryCarousel_shift_gallery" data-bs-slide="prev">
                                                            <span class="carousel-control-prev-icon"></span>
                                                            <span class="visually-hidden">السابق</span>
                                                        </button>
                                                        <button class="carousel-control-next" type="button" data-bs-target="#galleryCarousel_shift_gallery" data-bs-slide="next">
                                                            <span class="carousel-control-next-icon"></span>
                                                            <span class="visually-hidden">التالي</span>
                                                        </button>
                                                    </div>
                                                    
                                                    <!-- Thumbnail Navigation -->
                                                    <div class="bg-dark p-3 border-top border-secondary" id="thumbnailNav_shift_gallery" style="display: none;">
                                                        <!-- Thumbnails will be populated dynamically -->
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Preview section -->
                                    <div class="mt-2">
                                        <h6 class="text-muted mb-2">الصور الملتقطة (<span id="photo_count">0</span>):</h6>
                                        <div id="gallery_shift_gallery" class="gallery-container">
                                            <!-- Gallery will be populated dynamically -->
                                        </div>
                                    </div>
                                </div>

                                {{-- الملاحظات --}}
                                <div class="mb-4">
                                    <label class="form-label fw-bold">ملاحظات</label>
                                    <textarea name="notes" class="form-control" rows="3" placeholder="أدخل ملاحظات إن وجدت"></textarea>
                                </div>

                                {{-- الغرامات --}}
                                <div class="mb-4">
                                    <label class="form-label fw-bold text-warning">
                                        <i class="fas fa-exclamation-triangle me-1"></i> غرامات على الموظف (إن وجدت)
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-warning text-dark">
                                            <i class="fas fa-coins"></i>
                                        </span>
                                        <input type="number" 
                                               name="penalty_amount" 
                                               class="form-control" 
                                               step="0.01" 
                                               min="0" 
                                               value="0"
                                               placeholder="0.00">
                                        <span class="input-group-text">جنيه</span>
                                    </div>
                                    <small class="text-muted d-block mt-1">
                                        <i class="fas fa-info-circle me-1"></i>
                                        في حالة وجود أخطاء من الموظف، أدخل المبلغ المطلوب دفعه
                                    </small>
                                </div>

                                {{-- زر الإغلاق --}}
                                <button type="submit" class="btn btn-danger w-100 py-2 fs-5 fw-bold shadow-sm">
                                    <i class="fas fa-lock me-2"></i> إغلاق الشيفت
                                </button>

                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <script>
            let stream = null;
            let capturedImagesData = [];

            document.addEventListener('DOMContentLoaded', function() {
                const inputs = document.querySelectorAll('.end-reading-input');
                const totalDisplay = document.getElementById('total-liters-display');

                function calculate() {
                    let total = 0;
                    inputs.forEach(input => {
                        const start = parseFloat(input.dataset.start);
                        const end = parseFloat(input.value);
                        const container = input.closest('.mb-4');
                        const litersSpan = container.querySelector('.calculated-liters');

                        if (!isNaN(end) && end >= start) {
                            const diff = end - start;
                            total += diff;
                            litersSpan.textContent = diff.toFixed(2) + ' لتر';
                            litersSpan.style.display = 'block';
                            input.classList.remove('is-invalid');
                            input.classList.add('is-valid');
                        } else {
                            if (input.value !== '') {
                                input.classList.add('is-invalid');
                                input.classList.remove('is-valid');
                            }
                            litersSpan.style.display = 'none';
                        }
                    });
                    totalDisplay.textContent = total.toFixed(2);
                }

                inputs.forEach(input => {
                    input.addEventListener('input', calculate);
                });

                // Camera functionality
                const cameraSection = document.getElementById('camera_section');
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
                                width: {
                                    ideal: 1280
                                },
                                height: {
                                    ideal: 720
                                }
                            }
                        });
                        video.srcObject = stream;
                        video.style.display = 'block';
                        startCameraBtn.style.display = 'none';
                        capturePhotoBtn.style.display = 'block';
                    } catch (err) {
                        console.error('Error accessing camera:', err);
                        alert(
                        'لا يمكن الوصول إلى الكاميرا. يرجى التأكد من منح الإذن واستخدام كاميرا تعمل.');
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
                    if (e.detail.galleryId === 'shift_gallery') {
                        removePhoto(e.detail.index);
                    }
                });

                // Update photos display - Updated for Gallery Component
                function updatePhotosDisplay() {
                    photoCount.textContent = capturedImagesData.length;
                    
                    // Update the gallery component by refreshing the container
                    const galleryContainer = document.getElementById('gallery_shift_gallery');
                    if (galleryContainer) {
                        // Clear and rebuild the gallery
                        const parent = galleryContainer.parentElement;
                        galleryContainer.remove();
                        
                        // Create new gallery with updated images
                        const newGalleryHtml = createGalleryHtml(capturedImagesData, 'shift_gallery');
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

                // Clear photos display
                function clearPhotosDisplay() {
                    photosContainer.innerHTML = '';
                    capturedImagesData = [];
                    photoCount.textContent = '0';
                    updatePhotosData();
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

                // Form validation
                const form = document.querySelector('form');
                form.addEventListener('submit', function(e) {
                    if (capturedImagesData.length === 0) {
                        e.preventDefault();
                        alert('يرجى التقاط صورة بالكاميرا قبل إغلاق الشيفت');
                    }
                });

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

                // Cleanup on page unload
                window.addEventListener('beforeunload', function() {
                    stopCamera();
                });
            });
        </script>
    @endsection
@endcan
