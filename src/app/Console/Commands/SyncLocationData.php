<?php

namespace App\Console\Commands;

use App\Services\LocationSyncService;
use App\Models\Province;
use Illuminate\Console\Command;

class SyncLocationData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'location:sync {--force : Force sync even if data exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync provinces and wards from external API';

    protected $syncService;

    public function __construct(LocationSyncService $syncService)
    {
        parent::__construct();
        $this->syncService = $syncService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Check if data already exists
        if (!$this->option('force') && Province::count() > 0) {
            $this->warn('Location data already exists. Use --force to re-sync.');
            
            if (!$this->confirm('Do you want to proceed with re-sync?')) {
                $this->info('Sync cancelled.');
                return 0;
            }
        }

        $this->info('Starting location data sync from API...');
        $this->newLine();

        // Sync provinces
        $this->info('📍 Syncing provinces...');
        $provincesStats = $this->syncService->syncProvinces();
        
        $this->line("  ✅ Total: {$provincesStats['total']}");
        $this->line("  ➕ Created: {$provincesStats['created']}");
        $this->line("  🔄 Updated: {$provincesStats['updated']}");
        
        if (!empty($provincesStats['errors'])) {
            $this->error("  ❌ Errors: " . count($provincesStats['errors']));
            foreach ($provincesStats['errors'] as $error) {
                $this->line("     - {$error}");
            }
        }
        
        $this->newLine();

        // Sync wards
        $this->info('🏘️  Syncing wards (this may take a while)...');
        $wardsStats = $this->syncService->syncWards();
        
        $this->line("  ✅ Total: {$wardsStats['total']}");
        $this->line("  ➕ Created: {$wardsStats['created']}");
        $this->line("  🔄 Updated: {$wardsStats['updated']}");
        
        if (!empty($wardsStats['errors'])) {
            $this->error("  ❌ Errors: " . count($wardsStats['errors']));
            foreach (array_slice($wardsStats['errors'], 0, 10) as $error) {
                $this->line("     - {$error}");
            }
            if (count($wardsStats['errors']) > 10) {
                $this->line("     ... and " . (count($wardsStats['errors']) - 10) . " more errors");
            }
        }

        $this->newLine();
        $this->info('✅ Location sync completed!');

        return 0;
    }
}
