class FilterController {
    constructor(wrapper) {
        this.wrapper = wrapper;

        // Fixed route for rendering HTML templates
        this.renderRoute = '/wp-json/arc-lens/v1/render';

        // Filters object
        this.filters = {};

        // Cache filter wrappers
        this.filterWrappers = this.wrapper.querySelectorAll('[data-filter-key]');

        this.init();
    }

    init() {
        this.setupChangeHandlers();

        // Initial fetch
        this.fetchModelData();
    }

    setupChangeHandlers() {
        this.filterWrappers.forEach(wrapper => {
            const key = wrapper.dataset.filterKey;

            // Find the actual control inside the filter wrapper
            const control = wrapper.querySelector('[data-filter-control]');
            if (!control) {
                console.warn('[Lens] No control found inside filter wrapper for key:', key);
                return;
            }

            // Listen for input/change events
            const eventType = control.tagName === 'INPUT' ? 'input' : 'change';
            control.addEventListener(eventType, () => {
                this.filters[key] = control.value;
                this.fetchModelData(); // Existing API call
            });
        });
    }

    // Call the model API (existing working part)
    fetchModelData() {
        const modelRoute = this.wrapper.dataset.fetchRoute;
        const params = new URLSearchParams(this.filters);
        const url = modelRoute + (params.toString() ? '?' + params.toString() : '');

        console.log('[Lens] Fetching model data from:', url);

        fetch(url)
            .then(res => res.json())
            .then(data => {
                console.log('[Lens] Model data fetched:', data);

                // Access the items array in the response
                const records = data?.data?.data?.items || [];
                this.fetchRenderedHTML(records);
            })
            .catch(err => console.error('[Lens] Model fetch error:', err));
    }

    // Call the render route to get HTML
    fetchRenderedHTML(records) {
        console.log('[Lens] Sending records to render route:', records);

        fetch(this.renderRoute, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ records }),
        })
        .then(res => res.json())
        .then(data => {
            console.log('[Lens] Render response:', data);

            const html = data?.html || '<p>No items found.</p>';

            // Find the .arc-lens-grid that comes after the wrapper
            let grid = this.wrapper.nextElementSibling;
            if (!grid || !grid.classList.contains('arc-lens-grid')) {
                console.warn('[Lens] Could not find .arc-lens-grid after wrapper.');
                return;
            } else {
                // Replace grid content
                grid.innerHTML = html.trim();
            }

            console.log('[Lens] Grid updated successfully.');
        })
        .catch(err => console.error('[Lens] Render fetch error:', err));
    }
}

// Initialize for all wrappers
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.arc-lens-wrapper').forEach(wrapper => {
        new FilterController(wrapper);
    });
});
