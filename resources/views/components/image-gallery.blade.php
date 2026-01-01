@props([
    'images' => [],
    'preview' => false,
    'showModal' => true,
    'gridCols' => 'col-md-3 col-6',
    'imageHeight' => '120px',
    'showRemoveButton' => true,
    'showImageNumber' => true
])

<!-- Image Gallery Modal -->
@if($showModal)
<div class="modal fade" id="imageGalleryModal_{{ $attributes->get('id', uniqid()) }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content bg-dark">
            <div class="modal-header border-0">
                <h5 class="modal-title text-white">معرض الصور</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div id="galleryCarousel_{{ $attributes->get('id', uniqid()) }}" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        @foreach($images as $index => $image)
                            <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                <div class="text-center bg-dark">
                                    <img src="{{ $image }}" 
                                         alt="صورة {{ $index + 1 }}" 
                                         class="img-fluid" 
                                         style="max-height: 80vh; width: auto;">
                                    <div class="carousel-caption d-none d-md-block">
                                        <h5>صورة {{ $index + 1 }} من {{ count($images) }}</h5>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    @if(count($images) > 1)
                        <button class="carousel-control-prev" type="button" data-bs-target="#galleryCarousel_{{ $attributes->get('id', uniqid()) }}" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon"></span>
                            <span class="visually-hidden">السابق</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#galleryCarousel_{{ $attributes->get('id', uniqid()) }}" data-bs-slide="next">
                            <span class="carousel-control-next-icon"></span>
                            <span class="visually-hidden">التالي</span>
                        </button>
                    @endif
                </div>
                
                <!-- Thumbnail Navigation -->
                @if(count($images) > 1)
                <div class="bg-dark p-3 border-top border-secondary">
                    <div class="d-flex gap-2 overflow-auto justify-content-center">
                        @foreach($images as $index => $image)
                            <button type="button" 
                                    class="btn btn-outline-light p-1 {{ $index == 0 ? 'active' : '' }}" 
                                    data-bs-target="#galleryCarousel_{{ $attributes->get('id', uniqid()) }}" 
                                    data-bs-slide-to="{{ $index }}"
                                    style="min-width: 60px;">
                                <img src="{{ $image }}" 
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
@endif

<!-- Gallery Grid -->
@if($preview)
<div class="gallery-container" id="gallery_{{ $attributes->get('id', uniqid()) }}">
    <div class="row g-2">
        @foreach($images as $index => $image)
            <div class="{{ $gridCols }} gallery-item">
                <div class="position-relative">
                    <img src="{{ $image }}" 
                         alt="صورة {{ $index + 1 }}" 
                         class="img-thumbnail rounded cursor-pointer gallery-image" 
                         style="width: 100%; height: {{ $imageHeight }}; object-fit: cover;"
                         @if($showModal)
                         onclick="openGalleryModal('{{ $attributes->get('id', uniqid()) }}', {{ $index }})"
                         @endif
                         data-gallery-id="{{ $attributes->get('id', uniqid()) }}"
                         data-image-index="{{ $index }}">
                    
                    @if($showRemoveButton)
                        <button type="button" 
                                class="btn btn-danger btn-sm position-absolute top-0 start-0 m-1" 
                                onclick="removeGalleryImage('{{ $attributes->get('id', uniqid()) }}', {{ $index }})" 
                                style="width: 30px; height: 30px; padding: 0;">
                            <i class="fas fa-times"></i>
                        </button>
                    @endif
                    
                    @if($showImageNumber)
                        <div class="position-absolute bottom-0 start-0 m-1">
                            <span class="badge bg-dark bg-opacity-75 text-white">صورة {{ $index + 1 }}</span>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
    
    @if(count($images) > 3)
    <div class="text-center mt-2">
        <button type="button" 
                class="btn btn-sm btn-outline-primary"
                @if($showModal)
                onclick="openGalleryModal('{{ $attributes->get('id', uniqid()) }}', 0)"
                @endif>
            <i class="fas fa-images me-1"></i> عرض جميع الصور ({{ count($images) }})
        </button>
    </div>
    @endif
</div>
@endif

<script>
// Global gallery functions
function openGalleryModal(galleryId, startIndex = 0) {
    const modal = document.getElementById('imageGalleryModal_' + galleryId);
    const carousel = document.getElementById('galleryCarousel_' + galleryId);
    
    if (modal && carousel) {
        const bsCarousel = new bootstrap.Carousel(carousel);
        bsCarousel.to(startIndex);
        
        const bsModal = new bootstrap.Modal(modal);
        bsModal.show();
    }
}

function removeGalleryImage(galleryId, index) {
    if (confirm('هل أنت متأكد من حذف هذه الصورة؟')) {
        // Trigger custom event for parent to handle
        const event = new CustomEvent('removeGalleryImage', {
            detail: { galleryId: galleryId, index: index }
        });
        document.dispatchEvent(event);
    }
}

// Keyboard navigation for gallery
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        // Close any open gallery modal
        document.querySelectorAll('[id^="imageGalleryModal_"].show').forEach(modal => {
            const bsModal = bootstrap.Modal.getInstance(modal);
            if (bsModal) bsModal.hide();
        });
    }
});
</script>

<style>
.gallery-image {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.gallery-image:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 15px rgba(0,0,0,0.3);
}

.gallery-item {
    animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.carousel-item img {
    object-fit: contain;
}

.carousel-caption {
    background: rgba(0,0,0,0.7);
    border-radius: 8px;
    padding: 10px;
}

.carousel-control-prev-icon,
.carousel-control-next-icon {
    filter: invert(1);
}

[data-bs-target^="#galleryCarousel_"].active img {
    border: 2px solid #0d6efd;
}
</style>
