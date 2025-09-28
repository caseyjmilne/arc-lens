<?php 

namespace ARC\Lens;

if (!defined('ABSPATH')) exit;

class Render
{
    public static function output($collectionKey = 'docs')
    {
        // Get routes for this collection
        $routes = \arc_get_routes_for($collectionKey);

        if (!is_array($routes) || empty($routes)) {
            echo '<p>No routes found for collection.</p>';
            return;
        }

        // Find the main GET route (all items)
        $getAllRoute = '';
        foreach ($routes as $route) {
            if ($route['method'] === 'GET' && !preg_match('/\(\?P/', $route['route'])) {
                $getAllRoute = rest_url($route['route']);
                break;
            }
        }

        // Prepare any other needed data for the template
        $alias = $collectionKey;
        $config = []; // later we can fetch from the collection

        // Front-end template that includes JS and the empty container
        $templateFile = ARC_LENS_PATH . 'templates/filter.php';

        if (file_exists($templateFile)) {
            include $templateFile;
        } else {
            echo '<p>Template not found.</p>';
        }
    }

    /**
     * Temporary helper to render items (server-side)
     * using the new grid-wrapper + item-doc templates.
     */
    public static function renderItems($items)
    {
        $wrapperFile = ARC_LENS_PATH . 'templates/grid-wrapper.php';

        if (!empty($items) && file_exists($wrapperFile)) {
            // $items will be available inside the wrapper
            include $wrapperFile;
        } else {
            echo '<p>No items available.</p>';
        }
    }
}
