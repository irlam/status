<?php
/**
 * Status API Endpoint
 * Returns JSON data for all services
 */

declare(strict_types=1);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Load configuration
$config = require __DIR__ . '/config.php';

/**
 * Check a single service status
 */
function checkService(array $service): array
{
    $startTime = microtime(true);
    $status = 'DOWN';
    $httpCode = 0;
    $latency = 0;
    
    try {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $service['url'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 3,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_USERAGENT => 'StatusChecker/1.0',
        ]);
        
        curl_exec($ch);
        $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $latency = round((microtime(true) - $startTime) * 1000, 2);
        
        // Consider 2xx and 3xx as UP
        if ($httpCode >= 200 && $httpCode < 400) {
            $status = 'UP';
        }
        
        curl_close($ch);
    } catch (Exception $e) {
        $latency = round((microtime(true) - $startTime) * 1000, 2);
    }
    
    return [
        'name' => $service['name'],
        'tag' => $service['tag'],
        'status' => $status,
        'http_code' => $httpCode,
        'latency_ms' => $latency,
        'link' => $service['link'],
    ];
}

/**
 * Calculate overall status
 */
function calculateOverallStatus(array $services): string
{
    $downCount = 0;
    foreach ($services as $service) {
        if ($service['status'] === 'DOWN') {
            $downCount++;
        }
    }
    
    if ($downCount === 0) {
        return 'operational';
    } elseif ($downCount === count($services)) {
        return 'major_outage';
    } else {
        return 'partial_outage';
    }
}

// Check all services
$serviceResults = [];
foreach ($config['services'] as $service) {
    $serviceResults[] = checkService($service);
}

// Build response
$response = [
    'overall' => calculateOverallStatus($serviceResults),
    'last_updated' => date('c'),
    'thresholds' => $config['thresholds'],
    'services' => $serviceResults,
    'incidents' => $config['incidents'],
];

echo json_encode($response, JSON_PRETTY_PRINT);
