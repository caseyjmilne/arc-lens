<?php

namespace ARC\Lens;

class AdminPage
{
    public function __construct()
    {
        add_action('admin_menu', [$this, 'register_admin_page']);
    }

    public function register_admin_page()
    {
        add_menu_page(
            'ARC Lens',                  // Page title
            'ARC Lens',                  // Menu title
            'manage_options',            // Capability
            'arc-lens',                  // Menu slug
            [$this, 'render_admin_page'], // Callback
            '',                          // Icon (none for now)
            99                           // Position (low in menu)
        );
    }

    public function render_admin_page()
    {
        echo '<div class="wrap">';
        echo '<h1>ARC Lens</h1>';
        echo Render::filter();
        echo '</div>';
    }
}
