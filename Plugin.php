<?php
/**
 * Plugin Name: ARC Lens
 * Description: Facet filters and search.
 * Version: 1.0.0
 * Author: ARC Software
 * Requires PHP: 7.4
 * Namespace: ARC\Lens
 */

namespace ARC\Lens;

if (!defined('ABSPATH')) {
    exit;
}

define('ARC_LENS_VERSION', '1.0.0');
define('ARC_LENS_PATH', plugin_dir_path(__FILE__));
define('ARC_LENS_URL', plugin_dir_url(__FILE__));
define('ARC_LENS_FILE', __FILE__);

class Plugin
{
    public function __construct()
    {
        $this->includes();
        $this->init();
    }

    private function includes()
    {
        require_once ARC_LENS_PATH . 'includes/Render.php';
        require_once ARC_LENS_PATH . 'includes/AdminPage.php';
        require_once ARC_LENS_PATH . 'includes/Enqueue.php';
        require_once ARC_LENS_PATH . 'includes/Endpoints/RenderRoute.php';
        require_once ARC_LENS_PATH . 'includes/FilterSetRegistry.php';
        require_once ARC_LENS_PATH . 'includes/FilterSet.php';
        require_once ARC_LENS_PATH . 'includes/Filter.php';
        require_once ARC_LENS_PATH . 'includes/FilterTypes/FilterType.php';
        require_once ARC_LENS_PATH . 'includes/FilterTypes/Checkbox.php';
        require_once ARC_LENS_PATH . 'includes/FilterTypes/Search.php';
        require_once ARC_LENS_PATH . 'includes/FilterTypes/Select.php';
    }

    private function init()
    {
        new AdminPage();
        new Enqueue();
        new Endpoints\RenderRoute();
    }
}

new Plugin();
