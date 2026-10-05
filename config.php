<?php
/**
 * Live Construction Suite service checks.
 */

return [
    'services' => [
        [
            'name' => 'Construction Suite',
            'tag' => 'suite',
            'url' => 'https://suite.defecttracker.uk/',
            'link' => 'https://suite.defecttracker.uk/',
            'expected_text' => 'Construction',
        ],
        [
            'name' => 'Defect Tracker',
            'tag' => 'defects',
            'url' => 'https://defectnotice.site/health.php',
            'link' => 'https://defectnotice.site/',
        ],
        [
            'name' => 'Site Deliveries',
            'tag' => 'deliveries',
            'url' => 'https://sitedeliveries.site/',
            'link' => 'https://sitedeliveries.site/',
            'expected_text' => 'Site Deliveries',
        ],
        [
            'name' => 'Safety Tours',
            'tag' => 'safety',
            'url' => 'https://sitesafety.site/health.php?format=json',
            'link' => 'https://sitesafety.site/',
        ],
        [
            'name' => 'Site Permits',
            'tag' => 'permits',
            'url' => 'https://sitepermits.site/',
            'link' => 'https://sitepermits.site/',
            'expected_text' => 'Permits System',
        ],
        [
            'name' => 'Handover',
            'tag' => 'handover',
            'url' => 'https://handover.defecttracker.uk/',
            'link' => 'https://handover.defecttracker.uk/',
            'expected_text' => 'Handover',
        ],
    ],
    'thresholds' => [
        'warning_latency_ms' => 1000,
        'critical_latency_ms' => 3000,
    ],
    'incidents' => [],
];
