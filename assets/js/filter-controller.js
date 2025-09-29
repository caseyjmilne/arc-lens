class FilterController {
    constructor(container) {
        this.container = container;
        this.collection = container.dataset.collection;
        this.apiRoute = container.dataset.apiRoute;
        this.renderRoute = container.dataset.renderRoute;
        
        this.form = container.querySelector('.arc-lens-filters');
        this.resultsContainer = container.querySelector('.arc-lens-results');
        this.countElement = container.querySelector('[id^="arc-lens-count-"]');
        this.loadingElement = container.querySelector('.arc-lens-loading');
        
        if (!this.form || !this.resultsContainer) {
            console.error('[Lens] Missing required elements in container');
            return;
        }
        
        this.init();
    }
    
    init() {
        this.setupFormHandler();
        this.loadInitialResults();
    }
    
    setupFormHandler() {
        // Handle form submit
        this.form.addEventListener('submit', (e) => {
            e.preventDefault();
            this.applyFilters();
        });
        
        // Handle reset
        const resetBtn = this.form.querySelector('.arc-lens-filter-reset');
        if (resetBtn) {
            resetBtn.addEventListener('click', (e) => {
                e.preventDefault();
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
        
        // Build query params from form
        for (let [key, value] of formData.entries()) {
            if (value) {
                params.append(key, value);
            }
        }
        
        const url = this.apiRoute + '?' + params.toString();
        
        console.log('[Lens] Fetching from:', url);
        
        this.showLoading();
        
        fetch(url)
            .then(res => {
                if (!res.ok) {
                    throw new Error(`HTTP error! status: ${res.status}`);
                }
                return res.json();
            })
            .then(data => {
                console.log('[Lens] API response:', data);
                
                // Extract records from Gateway response format
                const records = data?.data?.items || data?.items || [];
                
                this.updateCount(records.length);
                this.renderItems(records);
            })
            .catch(err => {
                console.error('[Lens] Fetch error:', err);
                this.showError('Failed to load results');
            })
            .finally(() => {
                this.hideLoading();
            });
    }
    
    renderItems(records) {
        console.log('[Lens] Rendering items:', records);
        
        fetch(this.renderRoute, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                records: records,
                collection: this.collection
            })
        })
        .then(res => {
            if (!res.ok) {
                throw new Error(`HTTP error! status: ${res.status}`);
            }
            return res.json();
        })
        .then(data => {
            console.log('[Lens] Render response:', data);
            
            if (data.success && data.html) {
                this.resultsContainer.innerHTML = data.html;
            } else {
                this.showError('Failed to render results');
            }
        })
        .catch(err => {
            console.error('[Lens] Render error:', err);
            this.showError('Failed to render results');
        });
    }
    
    updateCount(count) {
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
        if (this.resultsContainer) {
            this.resultsContainer.innerHTML = `<p style="color: red;">${message}</p>`;
        }
    }
}

// Initialize all FilterControllers
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.arc-lens-container').forEach(container => {
        new FilterController(container);
    });
});