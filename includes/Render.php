<?php 
namespace ARC\Lens;

if (!defined('ABSPATH')) exit;

/**
 * Backwards-compatible Render class
 * Now primarily delegates to FilterSet system
 */
class Render
{
    /**
     * Legacy method - kept for backwards compatibility
     * Prefer using arc_lens_render() helper instead
     */
    public static function output($collectionKey = 'docs')
    {
        // Try to use registered FilterSet
        $filterSet = FilterSetRegistry::get($collectionKey);
        
        if ($filterSet) {
            $filterSet->render();
            return;
        }

        // Fallback to old hardcoded behavior
        self::legacyOutput($collectionKey);
    }

    /**
     * Legacy rendering (hardcoded templates)
     * Only used if no FilterSet registered
     */
    private static function legacyOutput($collectionKey)
    {
        $routes = \arc_get_routes_for($collectionKey);
        if (!is_array($routes) || empty($routes)) {
            echo '<p>No routes found for collection.</p>';
            return;
        }

        $getAllRoute = '';
        foreach ($routes as $route) {
            if ($route['method'] === 'GET' && !preg_match('/\(\?P/', $route['route'])) {
                $getAllRoute = rest_url($route['route']);
                break;
            }
        }

        $alias = $collectionKey;
        $config = [];

        $templateFile = ARC_LENS_PATH . 'templates/filter.php';
        if (file_exists($templateFile)) {
            include $templateFile;
        } else {
            echo '<p>Template not found.</p>';
        }
    }

    /**
     * Render items - now delegates to FilterSet if available
     */
    public static function renderItems($items, $collectionKey = 'docs')
    {
        $filterSet = FilterSetRegistry::get($collectionKey);
        
        if ($filterSet) {
            $filterSet->renderItems($items);
            return;
        }

        // Fallback to old hardcoded templates
        self::legacyRenderItems($items);
    }

    /**
     * Legacy item rendering
     */
    private static function legacyRenderItems($items)
    {
        $wrapperFile = ARC_LENS_PATH . 'templates/grid-wrapper.php';
        if (!empty($items) && file_exists($wrapperFile)) {
            include $wrapperFile;
        } else {
            echo '<p>No items available.</p>';
        }
    }
}