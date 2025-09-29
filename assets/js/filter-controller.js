class FilterController {
    constructor(container) {
        console.log('[Lens] Initializing FilterController for container:', container);
        
        this.container = container;
        this.collection = container.dataset.collection;
        this.apiRoute = container.dataset.apiRoute;
        this.renderRoute = container.dataset.renderRoute;
        
        console.log('[Lens] Configuration:', {
            collection: this.collection,
            apiRoute: this.apiRoute,
            renderRoute: this.renderRoute
        });
        
        this.form = container.querySelector('.arc-lens-filters');
        this.resultsContainer = container.querySelector('.arc-lens-results');
        this.countElement = container.querySelector('[id^="arc-lens-count-"]');
        this.loadingElement = container.querySelector('.arc-lens-loading');
        
        if (!this.form || !this.resultsContainer) {
            console.error('[Lens] Missing required elements in container');
            console.log('[Lens] Form found:', !!this.form);
            console.log('[Lens] Results container found:', !!this.resultsContainer);
            return;
        }
        
        console.log('[Lens] All required elements found');
        this.init();
    }
    
    init() {
        console.log('[Lens] Setting up form handler');
        this.setupFormHandler();
        console.log('[Lens] Loading initial results');
        this.loadInitialResults();
    }
    
    setupFormHandler() {
        // Handle form submit
        this.form.addEventListener('submit', (e) => {
            e.preventDefault();
            console.log('[Lens] Form submitted');
            this.applyFilters();
        });
        
        // Handle reset
        const resetBtn = this.form.querySelector('.arc-lens-filter-reset');
        if (resetBtn) {
            resetBtn.addEventListener('click', (e) => {
                e.preventDefault();
                console.log('[Lens] Reset clicked');
                this.form.reset();
                this.applyFilters();
            });
        }
    }
    
    loadInitialResults() {
        this.applyFilters();
    }
    
    applyFilters() {
        const formData = new FormData(this.form);
        const params = new URLSearchParams();
        
        console.log('[Lens] Building filter params from form');
        
        // Build query params from form
        for (let [key, value] of formData.entries()) {
            console.log('[Lens] Form field:', key, '=', value);
            if (value) {
                params.append(key, value);
            }
        }
        
        const url = this.apiRoute + (params.toString() ? '?' + params.toString() : '');
        
        console.log('[Lens] Fetching from:', url);
        
        this.showLoading();
        
        fetch(url)
            .then(res => {
                console.log('[Lens] API response status:', res.status);
                if (!res.ok) {
                    throw new Error(`HTTP error! status: ${res.status}`);
                }
                return res.json();
            })
            .then(response => {
                console.log('[Lens] API response:', response);
                console.log('[Lens] Checking paths:');
                console.log('[Lens]   response.data?.items:', response.data?.items);
                console.log('[Lens]   response.items:', response.items);
                
                // Extract records from Gateway response format
                const records = response?.data?.items || response?.items || [];
                
                console.log('[Lens] Extracted records:', records);
                console.log('[Lens] Record count:', records.length);
                
                this.updateCount(records.length);
                this.renderItems(records);
            })
            .catch(err => {
                console.error('[Lens] Fetch error:', err);
                this.showError('Failed to load results: ' + err.message);
            })
            .finally(() => {
                this.hideLoading();
            });
    }
    
    renderItems(records) {
        console.log('[Lens] Rendering items. Count:', records.length);
        console.log('[Lens] Sending to render route:', this.renderRoute);
        console.log('[Lens] Collection:', this.collection);
        
        const payload = {
            records: records,
            collection: this.collection
        };
        
        console.log('[Lens] Render payload:', payload);
        
        fetch(this.renderRoute, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
        })
        .then(res => {
            console.log('[Lens] Render response status:', res.status);
            if (!res.ok) {
                throw new Error(`HTTP error! status: ${res.status}`);
            }
            return res.json();
        })
        .then(response => {
            console.log('[Lens] Render response:', response);
            
            if (response.success && response.html) {
                console.log('[Lens] Updating results container with HTML');
                this.resultsContainer.innerHTML = response.html;
            } else {
                console.error('[Lens] Render failed:', response);
                this.showError('Failed to render results');
            }
        })
        .catch(err => {
            console.error('[Lens] Render error:', err);
            this.showError('Failed to render results: ' + err.message);
        });
    }
    
    updateCount(count) {
        console.log('[Lens] Updating count to:', count);
        if (this.countElement) {
            this.countElement.textContent = count;
        }
    }
    
    showLoading() {
        if (this.loadingElement) {
            this.loadingElement.style.display = 'block';
        }
        if (this.resultsContainer) {
            this.resultsContainer.style.opacity = '0.5';
        }
    }
    
    hideLoading() {
        if (this.loadingElement) {
            this.loadingElement.style.display = 'none';
        }
        if (this.resultsContainer) {
            this.resultsContainer.style.opacity = '1';
        }
    }
    
    showError(message) {
        console.error('[Lens] Showing error:', message);
        if (this.resultsContainer) {
            this.resultsContainer.innerHTML = `<p style="color: red;">${message}</p>`;
        }
    }
}

// Initialize all FilterControllers
document.addEventListener('DOMContentLoaded', () => {
    console.log('[Lens] DOM loaded, looking for .arc-lens-container elements');
    const containers = document.querySelectorAll('.arc-lens-container');
    console.log('[Lens] Found containers:', containers.length);
    
    containers.forEach(container => {
        new FilterController(container);
    });
});