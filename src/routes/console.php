<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('test:ai-cache', function () {
    $this->info('--- Starting AI Smart Cache Test ---');

    $request = \App\Models\Request::where('status', 'open')->first();
    if (!$request) {
        $this->error('No open request found to test.');
        return;
    }

    $service = app(\App\Services\MatchingService::class);
    $cacheKeyData = "ai_recs_tutors_data_{$request->id}";
    $cacheKeySig = "ai_recs_tutors_sig_{$request->id}";

    // 1. Clear Cache
    \Illuminate\Support\Facades\Cache::forget($cacheKeyData);
    \Illuminate\Support\Facades\Cache::forget($cacheKeySig);
    $this->comment("Cleared cache for Request #{$request->id}");

    // 2. First Run (Fresh)
    $this->info("\n[Run 1] Initial Request (Should be Fresh)...");
    $start1 = microtime(true);
    $result1 = $service->recommendTutorsForRequest($request->id);
    $time1 = round((microtime(true) - $start1) * 1000, 2);
    
    $this->line("Time: {$time1}ms");
    $this->line("Cached: " . ($result1['cached'] ? 'YES' : 'NO'));
    $this->line("Data Count: " . count($result1['data']));

    // 3. Second Run (Cached)
    $this->info("\n[Run 2] Immediate Retry (Should be CACHED)...");
    $start2 = microtime(true);
    $result2 = $service->recommendTutorsForRequest($request->id);
    $time2 = round((microtime(true) - $start2) * 1000, 2);
    
    $this->line("Time: {$time2}ms");
    $this->line("Cached: " . ($result2['cached'] ? 'YES' : 'NO'));
    
    if ($result2['cached'] && $time2 < 100) {
        $this->info("✅ SUCCESS: Result was retrieved from cache instantly.");
    } else {
        $this->error("❌ FAIL: Result was not cached or too slow.");
    }

    // 4. Modify Data (Invalidate Cache)
    $this->info("\n[Run 3] Modifying Request Data (touching updated_at)...");
    $request->touch(); // Updates timestamp
    sleep(1); // Ensure timestamp diff

    $this->info("Request Updated. Rerunning matching (Should be FRESH)...");
    $start3 = microtime(true);
    $result3 = $service->recommendTutorsForRequest($request->id);
    $time3 = round((microtime(true) - $start3) * 1000, 2);

    $this->line("Time: {$time3}ms");
    $this->line("Cached: " . ($result3['cached'] ? 'YES' : 'NO'));

    if (!$result3['cached']) {
        $this->info("✅ SUCCESS: Cache was invalidated and AI ran again.");
    } else {
        $this->error("❌ FAIL: Stale cache was returned.");
    }

    $this->info("\n--- Test Complete ---");

})->purpose('Test AI Smart Caching Logic');
