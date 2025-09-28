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

        // Prepare template variables
        $alias = $collectionKey;
        $config = []; // optionally fetch actual collection config here

        $templateFile = ARC_LENS_PATH . 'templates/filter.php';

        if (file_exists($templateFile)) {
            include $templateFile;
        } else {
            echo '<p>Template not found.</p>';
        }
    }
}
