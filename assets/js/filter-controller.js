class FilterController {
    constructor(wrapper) {
        this.wrapper = wrapper;
        this.route = wrapper.dataset.fetchRoute;
        this.filters = {};

        this.init();
    }

    init() {
        // Find all filter elements inside wrapper
        this.filterElements = this.wrapper.querySelectorAll('[data-filter-key]');
        this.filterElements.forEach(el => {
            el.addEventListener('change', e => {
                const key = el.dataset.filterKey;
                this.filters[key] = el.value;
                console.log('Filter changed:', this.filters);
                this.fetchData();
            });
        });

        // Initial fetch
        this.fetchData();
    }

    fetchData() {
        let url = this.route;

        // Optionally append query params for filters
        const params = new URLSearchParams(this.filters);
        if (params.toString()) {
            url += '?' + params.toString();
        }

        fetch(url)
            .then(res => res.json())
            .then(data => {
                console.log('Fetched data:', data);
                // TODO: render the filtered dataset
            })
            .catch(err => console.error(err));
    }
}

// Automatically initialize for all wrappers
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.arc-lens-wrapper').forEach(wrapper => {
        new FilterController(wrapper);
    });
});
