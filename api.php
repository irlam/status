<?php
/**
 * Status API Endpoint
 * Returns JSON data for all services
 */

declare(strict_types=1);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$config = require __DIR__ . '/config.php';

function checkService(array $service): array
{
    $startTime = microtime(true);
    $status = 'DOWN';
    $httpCode = 0;
    $latency = 0;
    $reason = null;

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

        $body = (string) curl_exec($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $latency = round((microtime(true) - $startTime) * 1000, 2);

        if ($httpCode >= 200 && $httpCode < 400) {
            $expectedText = trim((string) ($service['expected_text'] ?? ''));
            if ($expectedText !== '' && !str_contains($body, $expectedText)) {
                $reason = 'unexpected_response';
            } else {
                $status = 'UP';
            }
        } else {
            $reason = 'http_' . $httpCode;
        }

        curl_close($ch);
    } catch (Throwable $e) {
        $latency = round((microtime(true) - $startTime) * 1000, 2);
        $reason = 'request_failed';
    }

    return [
        'name' => $service['name'],
        'tag' => $service['tag'],
        'status' => $status,
        'http_code' => $httpCode,
        'latency_ms' => $latency,
        'link' => $service['link'],
        'reason' => $reason,
    ];
}

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
    }

    return $downCount === count($services) ? 'major_outage' : 'partial_outage';
}

$serviceResults = [];
foreach ($config['services'] as $service) {
    $serviceResults[] = checkService($service);
}

echo json_encode([
    'overall' => calculateOverallStatus($serviceResults),
    'last_updated' => date('c'),
    'thresholds' => $config['thresholds'],
    'services' => $serviceResults,
    'incidents' => $config['incidents'],
], JSON_PRETTY_PRINT);
