# Status Page

A modern, responsive PHP status page for monitoring services at https://status.defecttracker.uk.

## Features

- **JSON-first architecture**: All data is served via a JSON API endpoint
- **Responsive dashboard**: Beautiful UI that works on desktop, tablet, and mobile
- **Real-time updates**: Auto-refreshes every 60 seconds
- **Service monitoring**: Displays service status (UP/DOWN), HTTP codes, and latency
- **Incident management**: Shows active and resolved incidents with severity levels
- **Summary banner**: Overall system status at a glance
- **No build tools**: Pure PHP 8.x + static CSS/JS

## Quick Start

1. **Configure your services** in `config.php`:
   ```php
   'services' => [
       [
           'name' => 'Your Service',
           'tag' => 'service-tag',
           'url' => 'https://your-service.com/health',
           'link' => 'https://your-service.com',
       ],
   ],
   ```

2. **Add incidents** (optional) in `config.php`:
   ```php
   'incidents' => [
       [
           'id' => 1,
           'title' => 'Incident Title',
           'status' => 'investigating', // or 'resolved'
           'severity' => 'medium', // low, medium, high, critical
           'started_at' => '2025-11-19T08:00:00Z',
           'resolved_at' => null, // or timestamp when resolved
           'description' => 'Incident description...',
       ],
   ],
   ```

3. **Serve the status page**:
   - For development: `php -S localhost:8080`
   - For production: Configure your web server (Apache/Nginx) to serve the directory

## API Endpoint

The JSON API is available at `/api.php`:

```bash
curl https://status.defecttracker.uk/api.php
```

Response format:
```json
{
  "overall": "operational",
  "last_updated": "2025-11-19T08:00:00+00:00",
  "thresholds": {
    "warning_latency_ms": 1000,
    "critical_latency_ms": 3000
  },
  "services": [
    {
      "name": "Service Name",
      "tag": "service-tag",
      "status": "UP",
      "http_code": 200,
      "latency_ms": 123.45,
      "link": "https://service.com"
    }
  ],
  "incidents": []
}
```

## Files

- `index.html` - Main status page UI
- `api.php` - JSON API endpoint that checks service status
- `config.php` - Centralized service and incident configuration
- `app.js` - Frontend JavaScript (auto-refresh, UI updates)
- `style.css` - Responsive CSS styles

## Requirements

- PHP 8.0 or higher
- cURL extension enabled

## Configuration

### Thresholds

Adjust warning and critical latency thresholds in `config.php`:

```php
'thresholds' => [
    'warning_latency_ms' => 1000,  // Yellow warning
    'critical_latency_ms' => 3000, // Red critical
],
```

### Auto-refresh Interval

Modify the refresh interval in `app.js`:

```javascript
const REFRESH_INTERVAL_MS = 60000; // 60 seconds
```

## License

MIT