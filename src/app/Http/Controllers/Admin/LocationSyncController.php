<?php

namespace App\Http\Controllers\Admin;

use App\Services\LocationSyncService;
use App\Models\Province;
use App\Models\Ward;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LocationSyncController extends Controller
{
    protected $syncService;

    public function __construct(LocationSyncService $syncService)
    {
        $this->syncService = $syncService;
    }

    /**
     * Show sync status page
     */
    public function index()
    {
        $provincesCount = Province::count();
        $wardsCount = Ward::count();
        $lastUpdated = Province::latest('updated_at')->first()?->updated_at;

        return view('admin.location-sync.index', compact('provincesCount', 'wardsCount', 'lastUpdated'));
    }

    /**
     * Trigger manual sync
     */
    public function sync(Request $request)
    {
        set_time_limit(300); // 5 minutes

        try {
            $results = $this->syncService->syncAll();

            $message = sprintf(
                'Sync completed! Provinces: %d created, %d updated. Wards: %d created, %d updated.',
                $results['provinces']['created'],
                $results['provinces']['updated'],
                $results['wards']['created'],
                $results['wards']['updated']
            );

            return redirect()
                ->route('admin.location-sync.index')
                ->with('swal', [
                    'type' => 'success',
                    'title' => 'Success',
                    'text' => $message
                ]);

        } catch (\Exception $e) {
            return redirect()
                ->route('admin.location-sync.index')
                ->with('swal', [
                    'type' => 'error',
                    'title' => 'Error',
                    'text' => 'Sync failed: ' . $e->getMessage()
                ]);
        }
    }
}
