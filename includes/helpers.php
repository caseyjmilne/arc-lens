<?php
/**
 * Helper functions for ARC Lens
 */

if (!defined('ABSPATH')) exit;

/**
 * Render a FilterSet by collection key
 * 
 * Usage in templates:
 * <?php arc_lens_render('docs'); ?>
 * 
 * @param string $collectionKey The collection to render filters for
 */
function arc_lens_render($collectionKey)
{
    $filterSet = ARC\Lens\FilterSetRegistry::get($collectionKey);
    
    if (!$filterSet) {
        echo '<p>No FilterSet registered for: ' . esc_html($collectionKey) . '</p>';
        return;
    }
    
    $filterSet->render();
}

/**
 * Get a FilterSet instance
 * 
 * @param string $collectionKey
 * @return ARC\Lens\FilterSet|null
 */
function arc_lens_get_filterset($collectionKey)
{
    return ARC\Lens\FilterSetRegistry::get($collectionKey);
}

/**
 * Register a FilterSet
 * 
 * @param string $collectionKey
 * @param ARC\Lens\FilterSet $filterSet
 */
function arc_lens_register($collectionKey, ARC\Lens\FilterSet $filterSet)
{
    ARC\Lens\FilterSetRegistry::register($collectionKey, $filterSet);
}

/**
 * Check if a FilterSet is registered
 * 
 * @param string $collectionKey
 * @return bool
 */
function arc_lens_has_filterset($collectionKey)
{
    return ARC\Lens\FilterSetRegistry::has($collectionKey);
}

/**
 * Helper function to render a FilterSet
 * 
 * Usage in template:
 * arc_lens_render('docs');
 */
function arc_lens_render($collectionKey)
{
    $filterSet = FilterSetRegistry::get($collectionKey);
    
    if (!$filterSet) {
        echo '<p>No FilterSet registered for: ' . esc_html($collectionKey) . '</p>';
        return;
    }
    
    $filterSet->render();
}


/**
 * Helper to get FilterSet instance
 * Useful for RenderRoute to access correct templates
 */
function arc_lens_get_filterset($collectionKey)
{
    return FilterSetRegistry::get($collectionKey);
}