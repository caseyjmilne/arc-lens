<?php
/**
 * Default Filter Template for ARC Lens
 * 
 * This template renders the filter form container.
 * Individual filters are rendered by Filter instances.
 * 
 * Variables available:
 * @var string $collection - Collection key (e.g., 'docs')
 * @var string $apiRoute - REST API endpoint for fetching data
 * @var string $renderRoute - REST API endpoint for rendering HTML
 * @var array $filterInstances - Array of Filter objects
 * @var string $filterSetClass - FilterSet class name
 */

if (!defined('ABSPATH')) exit;
?>

<div class="arc-lens-container" 
     data-collection="<?php echo esc_attr($collection); ?>"
     data-api-route="<?php echo esc_url($apiRoute); ?>"
     data-render-route="<?php echo esc_url($renderRoute); ?>">
    
    <?php if (!empty($filterInstances)): ?>
    <form class="arc-lens-filters">
        <?php foreach ($filterInstances as $filter): ?>
            <?php echo $filter->render(); ?>
        <?php endforeach; ?>
        
        <div class="arc-lens-filter-actions">
            <button type="submit" class="button button-primary">
                Apply Filters
            </button>
            
            <button type="reset" class="button">
                Reset
            </button>
        </div>
    </form>
    <?php endif; ?>
    
    <div class="arc-lens-loading" style="display: none;">
        <p>Loading...</p>
    </div>
    
    <div class="arc-lens-results"></div>
    
    <div class="arc-lens-meta">
        <span class="arc-lens-count">
            <strong id="arc-lens-count-<?php echo esc_attr($collection); ?>">0</strong> results
        </span>
    </div>
    
</div>