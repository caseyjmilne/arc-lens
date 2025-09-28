<?php

namespace ARC\Lens\Endpoints;

use WP_REST_Request;
use WP_REST_Response;
use ARC\Lens\Render;

class RenderRoute
{
    public function __construct()
    {
        add_action('rest_api_init', [$this, 'registerRoute']);
    }

    public function registerRoute()
    {
        register_rest_route('arc-lens/v1', '/render', [
            'methods' => 'POST',
            'callback' => [$this, 'handle'],
            'permission_callback' => function() { return true; },
            'args' => [
                'records' => ['required' => true, 'type' => 'array'],
            ],
        ]);
    }

    public function handle(WP_REST_Request $request)
    {
        $records = $request->get_param('records');

        // Render HTML using the existing templates
        ob_start();
        Render::renderItems($records); // minimal: uses the templates we already made
        $html = ob_get_clean();

        return new WP_REST_Response([
            'success' => true,
            'html' => $html,
            'count' => count($records)
        ]);
    }
}
