<?php

namespace ARC\Lens;

class Enqueue
{
    public function __construct()
    {
        add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);
    }

    public function enqueue_assets()
    {
        wp_register_script(
            'arc-lens-filters',
            ARC_LENS_URL . 'assets/js/filter-controller.js',
            [],
            ARC_LENS_VERSION,
            true
        );

        wp_enqueue_script('arc-lens-filters');
    }
}
