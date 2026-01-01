@can('open shift')
    @extends('layouts.app')

    @section('content')
        <div class="container" dir="rtl">
            <div class="row justify-content-center">
                <div class="col-md-8">

                    {{-- كارد --}}
                    <x-card title="فتح شيفت جديد" headerClass="bg-success" textClass="text-white">

                        {{-- رسالة نجاح --}}
                        <x-alert-success />

                        <form method="POST" action="{{ route('shifts.store') }}" class="p-3" enctype="multipart/form-data">
                            @csrf

                            @role('admin')
                                <div class="mb-3">
                                    <label for="user_id" class="form-label">اختر الموظف</label>
                                    <select name="user_id" id="user_id" class="form-select" required>
                                        <option value="">-- اختر --</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endrole

                            {{-- عرض المسدسات المتاحة --}}
                            <div class="mb-4">
                                <label class="form-label fw-bold mb-2">
                                    <i class="fas fa-gas-pump me-1"></i> العدادات التي سيتم استلامها:
                                </label>
                                <div class="row g-2">
                                    @foreach($nozzles as $nozzle)
                                        <div class="col-md-6">
                                            <div class="p-3 border rounded bg-light d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="mb-1 text-dark fw-bold">{{ $nozzle->name }}</h6>
                                                    <small class="text-muted d-block">{{ $nozzle->pump->tank->fuel->name ?? '-' }}</small>
                                                </div>
                                                <div class="text-end">
                                                    <span class="badge bg-secondary mb-1">القراءة الحالية</span>
                                                    <h5 class="mb-0 text-primary fw-bold">{{ $nozzle->meter_reading }}</h5>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="meter_reading" class="form-label">قراءة/استلام العداد</label>
                                <input type="number" name="meter_reading" id="meter_reading" class="form-control"
                                    value="{{ $totalLitersDrawn }}" readonly>
                            </div>

                            <div class="mb-3">
                                <label for="meter_image" class="form-label">صورة العداد</label>
                                
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
                                
                                {{-- Image Gallery Modal --}}
                                <div class="modal fade" id="imageGalleryModal_shift_create_gallery" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-xl modal-dialog-centered">
                                        <div class="modal-content bg-dark">
                                            <div class="modal-header border-0">
                                                <h5 class="modal-title text-white">معرض الصور</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body p-0">
                                                <div id="galleryCarousel_shift_create_gallery" class="carousel slide" data-bs-ride="carousel">
                                                    <div class="carousel-inner">
                                                        <!-- Images will be populated dynamically -->
                                                    </div>
                                                    
                                                    <button class="carousel-control-prev" type="button" data-bs-target="#galleryCarousel_shift_create_gallery" data-bs-slide="prev">
                                                        <span class="carousel-control-prev-icon"></span>
                                                        <span class="visually-hidden">السابق</span>
                                                    </button>
                                                    <button class="carousel-control-next" type="button" data-bs-target="#galleryCarousel_shift_create_gallery" data-bs-slide="next">
                                                        <span class="carousel-control-next-icon"></span>
                                                        <span class="visually-hidden">التالي</span>
                                                    </button>
                                                </div>
                                                
                                                <!-- Thumbnail Navigation -->
                                                <div class="bg-dark p-3 border-top border-secondary" id="thumbnailNav_shift_create_gallery" style="display: none;">
                                                    <!-- Thumbnails will be populated dynamically -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Preview section -->
                                <div class="mt-2">
                                    <h6 class="text-muted mb-2">الصور الملتقطة (<span id="photo_count">0</span>):</h6>
                                    <div id="gallery_shift_create_gallery" class="gallery-container">
                                        <!-- Gallery will be populated dynamically -->
                                    </div>
                                </div>
                            </div>
                            {{-- نوع العملية مخفي --}}
                            <input type="hidden" name="operation_type" value="فتح شيفت">

                            <div class="mb-3">
                                <label class="form-label">حالة المطابقة</label>
                                <div class="d-flex gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="meter_match" id="match_yes"
                                            value="1" required checked>
                                        <label class="form-check-label" for="match_yes">مطابق</label>
                                    </div>

                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="meter_match" id="match_no"
                                            value="0">
                                        <label class="form-check-label" for="match_no">غير مطابق</label>
                                    </div>
                                </div>
                            </div>

                            {{-- زرار الحفظ --}}
                            <div class="d-flex justify-content-center mt-4">
                                <x-button type="submit" color="success" size="lg" icon="play-circle" label="فتح الشيفت" />
                            </div>
                        </form>

                    </x-card>

                </div>
            </div>
        </div>
        <script>
            let stream = null;
            let capturedImagesData = [];

            document.addEventListener('DOMContentLoaded', function() {
                // Camera functionality
                const startCameraBtn = document.getElementById('start_camera');
                const capturePhotoBtn = document.getElementById('capture_photo');
                const addMorePhotosBtn = document.getElementById('add_more_photos');
                const retakePhotoBtn = document.getElementById('retake_photo');
                const video = document.getElementById('camera_video');
                const canvas = document.getElementById('camera_canvas');
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
                    
                    console.log('Photo captured. Total images:', capturedImagesData.length);
                    console.log('Image data length:', imageData.length);
                    
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

                // Display photo function
                function displayPhoto(imageData, index) {
                    // Update the gallery component by refreshing the images array
                    updatePhotosDisplay();
                }

                // Remove photo function
                window.removePhoto = function(index) {
                    capturedImagesData.splice(index, 1);
                    updatePhotosDisplay();
                    updatePhotosData();
                };

                // Listen for gallery remove events
                document.addEventListener('removeGalleryImage', function(e) {
                    if (e.detail.galleryId === 'shift_create_gallery') {
                        removePhoto(e.detail.index);
                    }
                });

                // Update photos display
                function updatePhotosDisplay() {
                    photoCount.textContent = capturedImagesData.length;
                    
                    // Update the gallery component by refreshing the container
                    const galleryContainer = document.getElementById('gallery_shift_create_gallery');
                    if (galleryContainer && capturedImagesData.length > 0) {
                        // Clear and rebuild the gallery
                        const parent = galleryContainer.parentElement;
                        galleryContainer.remove();
                        
                        // Create new gallery with updated images
                        const newGalleryHtml = createGalleryHtml(capturedImagesData, 'shift_create_gallery');
                        parent.insertAdjacentHTML('beforeend', newGalleryHtml);
                    } else if (capturedImagesData.length === 0) {
                        // Clear the gallery if no images
                        if (galleryContainer) {
                            galleryContainer.innerHTML = '<!-- Gallery will be populated dynamically -->';
                        }
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

                // Open image modal function
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
                    console.log('Submitting form, capturedImagesData.length:', capturedImagesData.length);
                    if (capturedImagesData.length === 0) {
                        e.preventDefault();
                        alert('يرجى التقاط صورة بالكاميرا قبل فتح الشيفت');
                        return;
                    }
                });

                // Cleanup on page unload
                window.addEventListener('beforeunload', function() {
                    stopCamera();
                });
            });

            function previewImage(event) {
                const reader = new FileReader();
                reader.onload = function() {
                    const output = document.getElementById('image_preview');
                    output.src = reader.result;
                    output.style.display = 'block';
                };
                reader.readAsDataURL(event.target.files[0]);
            }
        </script>
    @endsection
@endcan
