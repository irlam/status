<?php
/**
 * Configuration for status page services
 * Centralised service definitions
 */

return [
    'services' => [
        [
            'name' => 'Main Website',
            'tag' => 'website',
            'url' => 'https://defecttracker.uk',
            'link' => 'https://defecttracker.uk',
        ],
        [
            'name' => 'API',
            'tag' => 'api',
            'url' => 'https://api.defecttracker.uk/health',
            'link' => 'https://api.defecttracker.uk',
        ],
        [
            'name' => 'Database',
            'tag' => 'database',
            'url' => 'https://db.defecttracker.uk/ping',
            'link' => 'https://db.defecttracker.uk',
        ],
        [
            'name' => 'GitHub (Test)',
            'tag' => 'github',
            'url' => 'https://github.com',
            'link' => 'https://github.com',
        ],
    ],
    'thresholds' => [
        'warning_latency_ms' => 1000,
        'critical_latency_ms' => 3000,
    ],
    'incidents' => [
        [
            'id' => 1,
            'title' => 'API Performance Degradation',
            'status' => 'investigating',
            'severity' => 'medium',
            'started_at' => '2025-11-19T08:00:00Z',
            'resolved_at' => null,
            'description' => 'We are currently investigating reports of slow API response times. Our team is working to identify and resolve the issue.',
        ],
        [
            'id' => 2,
            'title' => 'Database Maintenance',
            'status' => 'resolved',
            'severity' => 'low',
            'started_at' => '2025-11-19T06:00:00Z',
            'resolved_at' => '2025-11-19T07:30:00Z',
            'description' => 'Scheduled database maintenance completed successfully. All services have been restored.',
        ],
    ],
];
