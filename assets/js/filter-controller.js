class FilterController {
    constructor() {
        this.init();
    }

    init() {
        const filters = document.querySelectorAll('.arc-lens-filter');
        if (!filters.length) return;

        filters.forEach(filter => {
            filter.addEventListener('change', (e) => {
                console.log('Filter changed:', e.target.name, e.target.value);
            });
        });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new FilterController();
});
