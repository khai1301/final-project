@extends('admin.layouts.app')

@section('title', 'Location Data Sync')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Location Data Synchronization</h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="bi bi-map text-primary"></i> Provinces/Cities
                    </h5>
                    <h2 class="display-4">{{ number_format($provincesCount) }}</h2>
                    <p class="text-muted">Total provinces and cities in database</p>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="bi bi-geo-alt text-success"></i> Wards/Communes
                    </h5>
                    <h2 class="display-4">{{ number_format($wardsCount) }}</h2>
                    <p class="text-muted">Total wards and communes in database</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-arrow-repeat"></i> Sync Location Data</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                <strong>Last Updated:</strong> 
                @if($lastUpdated)
                    {{ $lastUpdated->format('d/m/Y H:i:s') }} ({{ $lastUpdated->diffForHumans() }})
                @else
                    Never
                @endif
            </div>

            <p class="mb-3">
                This will fetch the latest province and ward data from the external API 
                (<code>tinhthanhpho.com</code>) and update the database.
            </p>

            <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <strong>Warning:</strong> This process may take several minutes to complete. 
                Please do not close this page during the sync.
            </div>

            <form action="{{ route('admin.location-sync.sync') }}" method="POST" id="syncForm">
                @csrf
                <button type="submit" class="btn btn-primary btn-lg" id="syncButton">
                    <i class="bi bi-cloud-download me-2"></i>
                    Start Synchronization
                </button>
            </form>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-terminal"></i> Command Line Sync</h5>
        </div>
        <div class="card-body">
            <p>You can also sync location data using the artisan command:</p>
            <pre class="bg-dark text-light p-3 rounded"><code>php artisan location:sync</code></pre>
            <p class="text-muted mb-0">
                <small>Use <code>--force</code> flag to re-sync even if data exists.</small>
            </p>
        </div>
    </div>
</div>

<script>
document.getElementById('syncForm').addEventListener('submit', function(e) {
    const button = document.getElementById('syncButton');
    button.disabled = true;
    button.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Syncing... Please wait';
});
</script>
@endsection
