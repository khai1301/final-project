<?php

namespace App\Services;

use App\Models\Province;
use App\Models\Ward;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LocationSyncService
{
    private const API_BASE_URL = 'https://tinhthanhpho.com/api/v1';
    private const PROVINCES_ENDPOINT = '/new-provinces';
    private const WARDS_ENDPOINT = '/new-provinces/{code}/wards';
    
    /**
     * Sync all provinces from API
     */
    public function syncProvinces(): array
    {
        $stats = [
            'total' => 0,
            'created' => 0,
            'updated' => 0,
            'errors' => [],
        ];
        
        try {
            $page = 1;
            $hasMore = true;
            
            while ($hasMore) {
                $response = Http::timeout(30)->get(self::API_BASE_URL . self::PROVINCES_ENDPOINT, [
                    'page' => $page,
                    'limit' => 100, // Request more per page
                ]);
                
                if (!$response->successful()) {
                    $stats['errors'][] = "Failed to fetch provinces page {$page}";
                    break;
                }
                
                $data = $response->json();
                
                if (empty($data['data'])) {
                    break;
                }
                
                foreach ($data['data'] as $provinceData) {
                    try {
                        $province = Province::updateOrCreate(
                            ['external_id' => $provinceData['province_id']],
                            [
                                'code' => $provinceData['code'],
                                'name' => $provinceData['name'],
                                'type' => $provinceData['type'],
                            ]
                        );
                        
                        if ($province->wasRecentlyCreated) {
                            $stats['created']++;
                        } else {
                            $stats['updated']++;
                        }
                        
                        $stats['total']++;
                    } catch (\Exception $e) {
                        $stats['errors'][] = "Province {$provinceData['name']}: " . $e->getMessage();
                    }
                }
                
                // Check if there are more pages
                $metadata = $data['metadata'] ?? [];
                $total = $metadata['total'] ?? 0;
                $currentCount = $page * ($metadata['limit'] ?? 20);
                
                if ($currentCount >= $total) {
                    $hasMore = false;
                } else {
                    $page++;
                }
            }
            
        } catch (\Exception $e) {
            $stats['errors'][] = "General error: " . $e->getMessage();
            Log::error('Province sync failed', ['error' => $e->getMessage()]);
        }
        
        return $stats;
    }
    
    /**
     * Sync all wards for all provinces
     */
    public function syncWards(): array
    {
        $stats = [
            'total' => 0,
            'created' => 0,
            'updated' => 0,
            'errors' => [],
        ];
        
        $provinces = Province::all();
        
        foreach ($provinces as $province) {
            try {
                $page = 1;
                $hasMore = true;
                
                while ($hasMore) {
                    $endpoint = str_replace('{code}', $province->code, self::WARDS_ENDPOINT);
                    
                    $response = Http::timeout(30)->get(self::API_BASE_URL . $endpoint, [
                        'page' => $page,
                        'limit' => 100,
                    ]);
                    
                    if (!$response->successful()) {
                        $stats['errors'][] = "Failed to fetch wards for {$province->name} page {$page}";
                        break;
                    }
                    
                    $data = $response->json();
                    
                    if (empty($data['data'])) {
                        break;
                    }
                    
                    foreach ($data['data'] as $wardData) {
                        try {
                            $ward = Ward::updateOrCreate(
                                ['external_id' => $wardData['ward_id']],
                                [
                                    'code' => $wardData['code'],
                                    'name' => $wardData['name'],
                                    'type' => $wardData['type'],
                                    'province_code' => $wardData['province_code'],
                                ]
                            );
                            
                            if ($ward->wasRecentlyCreated) {
                                $stats['created']++;
                            } else {
                                $stats['updated']++;
                            }
                            
                            $stats['total']++;
                        } catch (\Exception $e) {
                            $stats['errors'][] = "Ward {$wardData['name']}: " . $e->getMessage();
                        }
                    }
                    
                    // Check pagination
                    $metadata = $data['metadata'] ?? [];
                    $total = $metadata['total'] ?? 0;
                    $currentCount = $page * ($metadata['limit'] ?? 20);
                    
                    if ($currentCount >= $total) {
                        $hasMore = false;
                    } else {
                        $page++;
                    }
                }
                
            } catch (\Exception $e) {
                $stats['errors'][] = "Province {$province->name}: " . $e->getMessage();
                Log::error("Ward sync failed for {$province->name}", ['error' => $e->getMessage()]);
            }
        }
        
        return $stats;
    }
    
    /**
     * Sync both provinces and wards
     */
    public function syncAll(): array
    {
        $provincesStats = $this->syncProvinces();
        $wardsStats = $this->syncWards();
        
        return [
            'provinces' => $provincesStats,
            'wards' => $wardsStats,
        ];
    }
}
