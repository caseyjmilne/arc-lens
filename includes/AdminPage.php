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
            '',                          // Icon
            99                           // Position
        );
    }

    public function render_admin_page()
    {
        echo '<div class="wrap">';
        echo '<h1>ARC Lens</h1>';

        // Step 1: Filters (from filter.php)
        Render::output('docs');

        // Step 2: Test items
        $items = [
            ['title' => 'Doc A', 'author_id' => 5, 'status' => 'draft'],
            ['title' => 'Doc B', 'author_id' => 12, 'status' => 'published'],
        ];
        Render::renderItems($items);

        echo '</div>';
    }


}
