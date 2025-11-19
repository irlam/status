/**
 * Status Page Application
 * Fetches JSON data and updates UI
 */

let refreshInterval;
const REFRESH_INTERVAL_MS = 60000; // 60 seconds

/**
 * Fetch status data from API
 */
async function fetchStatus() {
    try {
        const response = await fetch('api.php');
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const data = await response.json();
        updateUI(data);
    } catch (error) {
        console.error('Error fetching status:', error);
        showError('Failed to load status data. Retrying...');
    }
}

/**
 * Update all UI elements with new data
 */
function updateUI(data) {
    updateBanner(data.overall);
    updateLastUpdated(data.last_updated);
    updateServices(data.services, data.thresholds);
    updateIncidents(data.incidents);
}

/**
 * Update the status banner
 */
function updateBanner(overallStatus) {
    const banner = document.getElementById('status-banner');
    const statusText = document.getElementById('overall-status');
    const statusDescription = document.getElementById('overall-description');
    const statusIcon = banner.querySelector('.status-icon');
    
    banner.classList.remove('loading', 'operational', 'partial_outage', 'major_outage');
    banner.classList.add(overallStatus);
    
    const statusConfig = {
        operational: {
            icon: '✓',
            title: 'All Systems Operational',
            description: 'All services are running normally'
        },
        partial_outage: {
            icon: '⚠',
            title: 'Partial Outage',
            description: 'Some services are experiencing issues'
        },
        major_outage: {
            icon: '✗',
            title: 'Major Outage',
            description: 'Multiple services are down'
        }
    };
    
    const config = statusConfig[overallStatus] || statusConfig.operational;
    statusIcon.textContent = config.icon;
    statusText.textContent = config.title;
    statusDescription.textContent = config.description;
}

/**
 * Update last updated timestamp
 */
function updateLastUpdated(timestamp) {
    const element = document.getElementById('last-updated-time');
    const date = new Date(timestamp);
    element.textContent = date.toLocaleString();
}

/**
 * Update services grid
 */
function updateServices(services, thresholds) {
    const container = document.getElementById('services-container');
    container.innerHTML = '';
    
    services.forEach(service => {
        const card = createServiceCard(service, thresholds);
        container.appendChild(card);
    });
}

/**
 * Create a service card element
 */
function createServiceCard(service, thresholds) {
    const card = document.createElement('div');
    card.className = `service-card ${service.status.toLowerCase()}`;
    
    const statusIcon = service.status === 'UP' ? '✓' : '✗';
    const statusClass = service.status === 'UP' ? 'up' : 'down';
    
    let latencyClass = '';
    if (service.status === 'UP') {
        if (service.latency_ms > thresholds.critical_latency_ms) {
            latencyClass = 'critical';
        } else if (service.latency_ms > thresholds.warning_latency_ms) {
            latencyClass = 'warning';
        }
    }
    
    card.innerHTML = `
        <div class="service-header">
            <span class="service-name">${escapeHtml(service.name)}</span>
            <span class="service-status ${statusClass}">${statusIcon} ${service.status}</span>
        </div>
        <div class="service-details">
            <div class="service-detail">
                <span class="detail-label">HTTP Code:</span>
                <span class="detail-value">${service.http_code || 'N/A'}</span>
            </div>
            <div class="service-detail">
                <span class="detail-label">Latency:</span>
                <span class="detail-value ${latencyClass}">${service.latency_ms}ms</span>
            </div>
            <div class="service-detail">
                <span class="detail-label">Tag:</span>
                <span class="detail-value">${escapeHtml(service.tag)}</span>
            </div>
        </div>
        ${service.link ? `<a href="${escapeHtml(service.link)}" class="service-link" target="_blank" rel="noopener">Visit →</a>` : ''}
    `;
    
    return card;
}

/**
 * Update incidents section
 */
function updateIncidents(incidents) {
    const section = document.getElementById('incidents-section');
    const container = document.getElementById('incidents-container');
    
    if (!incidents || incidents.length === 0) {
        section.style.display = 'none';
        return;
    }
    
    section.style.display = 'block';
    container.innerHTML = '';
    
    incidents.forEach(incident => {
        const card = createIncidentCard(incident);
        container.appendChild(card);
    });
}

/**
 * Create an incident card element
 */
function createIncidentCard(incident) {
    const card = document.createElement('div');
    card.className = `incident-card ${incident.status} ${incident.severity}`;
    
    const startedDate = new Date(incident.started_at);
    const resolvedDate = incident.resolved_at ? new Date(incident.resolved_at) : null;
    
    card.innerHTML = `
        <div class="incident-header">
            <span class="incident-severity">${escapeHtml(incident.severity)}</span>
            <span class="incident-status">${escapeHtml(incident.status)}</span>
        </div>
        <h3 class="incident-title">${escapeHtml(incident.title)}</h3>
        <p class="incident-description">${escapeHtml(incident.description)}</p>
        <div class="incident-times">
            <div class="incident-time">
                <strong>Started:</strong> ${startedDate.toLocaleString()}
            </div>
            ${resolvedDate ? `
                <div class="incident-time">
                    <strong>Resolved:</strong> ${resolvedDate.toLocaleString()}
                </div>
            ` : ''}
        </div>
    `;
    
    return card;
}

/**
 * Show error message
 */
function showError(message) {
    const banner = document.getElementById('status-banner');
    banner.className = 'status-banner major_outage';
    document.getElementById('overall-status').textContent = 'Error';
    document.getElementById('overall-description').textContent = message;
}

/**
 * Escape HTML to prevent XSS
 */
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

/**
 * Start auto-refresh
 */
function startAutoRefresh() {
    // Clear existing interval if any
    if (refreshInterval) {
        clearInterval(refreshInterval);
    }
    
    // Set up new interval
    refreshInterval = setInterval(fetchStatus, REFRESH_INTERVAL_MS);
}

/**
 * Initialize the application
 */
function init() {
    // Initial fetch
    fetchStatus();
    
    // Start auto-refresh
    startAutoRefresh();
}

// Start the application when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
} else {
    init();
}
