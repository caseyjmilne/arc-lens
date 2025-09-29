<?php
/**
 * FilterSet Abstract Base Class
 * 
 * Developers extend this to create filterable views for their collections.
 * 
 * Example:
 * class DocsFilterSet extends FilterSet {
 *     protected $collection = 'docs';
 *     protected $filters = [
 *         'category' => ['type' => 'select', 'label' => 'Category'],
 *         'status' => ['type' => 'select', 'label' => 'Status']
 *     ];
 *     protected function wrapperTemplate() { return __DIR__ . '/templates/wrapper.php'; }
 *     protected function itemTemplate() { return __DIR__ . '/templates/item.php'; }
 * }
 */

namespace ARC\Lens;

if (!defined('ABSPATH')) exit;

abstract class FilterSet
{
    /**
     * The collection key this FilterSet operates on
     */
    protected $collection;

    /**
     * Filter definitions
     * 
     * Can be either:
     * 1. Array config: ['status' => ['type' => 'select', ...]]
     * 2. Filter instances: ['author' => new AuthorFilter(...)]
     * 3. Mix of both
     */
    protected $filters = [];

    /**
     * Get filters - override this to define filters programmatically
     * If not overridden, uses $filters property
     */
    protected function getFilters()
    {
        return $this->filters;
    }

    /**
     * Default query modifications (sorting, limits, etc.)
     */
    protected $defaultQuery = [
        'orderBy' => 'created_at',
        'orderDir' => 'desc',
        'perPage' => 20
    ];

    /**
     * Template path for the grid wrapper
     * Must return absolute path to PHP template file
     */
    abstract protected function wrapperTemplate();

    /**
     * Template path for individual items
     * Must return absolute path to PHP template file
     */
    abstract protected function itemTemplate();

    /**
     * Render the complete filter interface + results container
     * This outputs the initial HTML with filter form and empty results div
     */
    public function render()
    {
        if (empty($this->collection)) {
            echo '<p>Error: No collection specified for FilterSet</p>';
            return;
        }

        // Get API route for this collection
        $routes = \arc_get_routes_for($this->collection);
        if (!is_array($routes) || empty($routes)) {
            echo '<p>No routes found for collection: ' . esc_html($this->collection) . '</p>';
            return;
        }

        // Find the GET all route
        $apiRoute = $this->findGetAllRoute($routes);
        if (!$apiRoute) {
            echo '<p>No GET route found for collection</p>';
            return;
        }

        // Create FilterType instances
        $filterInstances = $this->createFilterInstances();

        // Data for filter template
        $data = [
            'collection' => $this->collection,
            'apiRoute' => $apiRoute,
            'renderRoute' => rest_url('arc-lens/v1/render'),
            'filterInstances' => $filterInstances,
            'filterSetClass' => get_class($this)
        ];

        // Enqueue scripts/styles for active filters
        $this->enqueueFilterAssets($filterInstances);

        // Use Lens's base filter template
        $this->loadFilterTemplate($data);
        
        // Render inline scripts
        $this->renderFilterScripts($filterInstances);
    }

    /**
     * Render items using the configured templates
     * Called by RenderRoute endpoint
     */
    public function renderItems($items)
    {
        if (empty($items)) {
            echo '<p>No items found.</p>';
            return;
        }

        $wrapperFile = $this->wrapperTemplate();
        $itemFile = $this->itemTemplate();

        if (!file_exists($wrapperFile)) {
            echo '<p>Wrapper template not found: ' . esc_html($wrapperFile) . '</p>';
            return;
        }

        if (!file_exists($itemFile)) {
            echo '<p>Item template not found: ' . esc_html($itemFile) . '</p>';
            return;
        }

        // Make item template available to wrapper
        $itemTemplate = $itemFile;
        
        // Load wrapper (which will loop through $items and include $itemTemplate)
        include $wrapperFile;
    }

    /**
     * Hook for developers to modify the query before execution
     * Override this to add custom query logic
     */
    protected function modifyQuery($query, $params)
    {
        return $query;
    }

    /**
     * Get filter configuration for this FilterSet
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * Get collection key
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * Get default query settings
     */
    public function getDefaultQuery()
    {
        return $this->defaultQuery;
    }

    // --- Private Helper Methods ---

    /**
     * Create FilterType instances from filter config
     */
    private function createFilterInstances()
    {
        $instances = [];
        
        foreach ($this->filters as $key => $config) {
            $type = $config['type'] ?? 'text';
            $className = $this->getFilterTypeClass($type);
            
            if (class_exists($className)) {
                $instances[$key] = new $className($key, $config, $this->collection);
            }
        }
        
        return $instances;
    }

    /**
     * Get FilterType class name from type string
     */
    private function getFilterTypeClass($type)
    {
        $typeMap = [
            'select' => 'ARC\\Lens\\FilterTypes\\Select',
            'search' => 'ARC\\Lens\\FilterTypes\\Search',
            'text' => 'ARC\\Lens\\FilterTypes\\Search',
            'checkbox' => 'ARC\\Lens\\FilterTypes\\Checkbox',
        ];
        
        return $typeMap[$type] ?? 'ARC\\Lens\\FilterTypes\\Search';
    }

    /**
     * Enqueue scripts and styles for filters
     */
    private function enqueueFilterAssets($filterInstances)
    {
        foreach ($filterInstances as $filter) {
            foreach ($filter->getScripts() as $handle) {
                wp_enqueue_script($handle);
            }
            foreach ($filter->getStyles() as $handle) {
                wp_enqueue_style($handle);
            }
        }
    }

    /**
     * Render inline scripts for filters
     */
    private function renderFilterScripts($filterInstances)
    {
        $scripts = [];
        
        foreach ($filterInstances as $filter) {
            $script = $filter->renderInlineScript();
            if (!empty($script)) {
                $scripts[] = $script;
            }
        }
        
        if (!empty($scripts)) {
            echo '<script>';
            echo implode("\n", $scripts);
            echo '</script>';
        }
    }

    private function findGetAllRoute($routes)
    {
        foreach ($routes as $route) {
            // Find GET route without parameters (the "get all" route)
            if ($route['method'] === 'GET' && !preg_match('/\(\?P/', $route['route'])) {
                return rest_url($route['route']);
            }
        }
        return null;
    }

    private function prepareFilters()
    {
        // Deprecated - kept for backwards compatibility
        // Use createFilterInstances() instead
        $prepared = [];
        
        foreach ($this->filters as $key => $config) {
            $prepared[$key] = $config;
            
            if (isset($config['options']) && is_callable($config['options'])) {
                $prepared[$key]['options'] = call_user_func($config['options']);
            }
        }
        
        return $prepared;
    }

    private function loadFilterTemplate($data)
    {
        // Extract data for template
        extract($data);
        
        // Check for custom filter template in theme/plugin first
        $customTemplate = apply_filters('arc_lens_filter_template', null, $this->collection);
        
        if ($customTemplate && file_exists($customTemplate)) {
            include $customTemplate;
        } else {
            // Use default Lens filter template
            $defaultTemplate = ARC_LENS_PATH . 'templates/filter.php';
            if (file_exists($defaultTemplate)) {
                include $defaultTemplate;
            } else {
                echo '<p>Filter template not found</p>';
            }
        }
    }
}


/**
 * Registry for FilterSets
 * Allows lookup by collection key
 */
class FilterSetRegistry
{
    private static $registry = [];

    /**
     * Register a FilterSet instance
     */
    public static function register($collectionKey, FilterSet $filterSet)
    {
        self::$registry[$collectionKey] = $filterSet;
    }

    /**
     * Get FilterSet for a collection
     */
    public static function get($collectionKey)
    {
        return self::$registry[$collectionKey] ?? null;
    }

    /**
     * Check if FilterSet exists for collection
     */
    public static function has($collectionKey)
    {
        return isset(self::$registry[$collectionKey]);
    }
}