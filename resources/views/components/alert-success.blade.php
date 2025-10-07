@if (session('success'))
    <div class="alert alert-success text-center">
        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
    </div>
@endif
