<div class="card shadow-lg border-0 rounded-3  ">
    <div class="card-header {{ $headerClass ?? 'bg-light' }} {{ $textClass ?? 'text-dark' }} text-center fs-5 fw-bold">
        {{ $title }}
    </div>
    <div class="card-body overflow-x-auto">
        {{ $slot }}
    </div>
</div>
