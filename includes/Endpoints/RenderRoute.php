<?php

namespace ARC\Lens\Endpoints;

use WP_REST_Request;
use WP_REST_Response;
use ARC\Lens\FilterSetRegistry;

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
                'records' => [
                    'required' => true,
                    'type' => 'array',
                    'description' => 'Array of records to render'
                ],
                'collection' => [
                    'required' => true,
                    'type' => 'string',
                    'description' => 'Collection key'
                ],
            ],
        ]);
    }

    public function handle(WP_REST_Request $request)
    {
        $records = $request->get_param('records');
        $collectionKey = $request->get_param('collection');

        if (empty($records) || !is_array($records)) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'Invalid records parameter'
            ], 400);
        }

        if (empty($collectionKey)) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'Collection key is required'
            ], 400);
        }

        // Get the FilterSet for this collection
        $filterSet = FilterSetRegistry::get($collectionKey);
        
        if (!$filterSet) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'No FilterSet registered for collection: ' . $collectionKey
            ], 404);
        }

        // Render using the FilterSet's templates
        ob_start();
        $filterSet->renderItems($records);
        $html = ob_get_clean();

        return new WP_REST_Response([
            'success' => true,
            'html' => $html,
            'count' => count($records),
            'collection' => $collectionKey
        ]);
    }
}