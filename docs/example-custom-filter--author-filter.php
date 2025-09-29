<?php
/**
 * Example Custom Filter: AuthorFilter
 * 
 * This demonstrates a complex filter with custom logic
 * that wouldn't be practical with simple array config
 */

namespace YourPlugin\Filters;

use ARC\Lens\Filter;

class AuthorFilter extends Filter
{
    protected $type = 'select';
    protected $label = 'Author';
    protected $placeholder = 'All Authors';

    /**
     * Get options dynamically from WordPress users
     * This method is called when rendering, so options are always fresh
     */
    public function getOptions()
    {
        $users = get_users([
            'role__in' => ['administrator', 'editor', 'author'],
            'orderby' => 'display_name',
            'order' => 'ASC'
        ]);

        $options = [];
        foreach ($users as $user) {
            $options[$user->ID] = $user->display_name . ' (' . $user->user_email . ')';
        }

        return $options;
    }

    /**
     * Optionally override other aspects
     */
    public function getScripts()
    {
        // Add select2 for better UX with many authors
        return array_merge(parent::getScripts(), ['select2']);
    }

    public function getInlineScript()
    {
        // Initialize select2 on this specific filter
        return "
        jQuery(document).ready(function($) {
            $('#filter-{$this->collection}-{$this->key}').select2({
                placeholder: '{$this->placeholder}',
                allowClear: true
            });
        });
        ";
    }
}


/**
 * Usage in FilterSet:
 */
class DocsFilterSet extends \ARC\Lens\FilterSet
{
    protected $collection = 'docs';

    protected function getFilters()
    {
        return [
            // Simple array config
            'status' => [
                'type' => 'select',
                'label' => 'Status',
                'options' => ['draft', 'published']
            ],
            
            // Custom Filter class
            'author' => new \YourPlugin\Filters\AuthorFilter('author', $this->collection),
            
            // Another simple one
            'search' => [
                'type' => 'search',
                'placeholder' => 'Search docs...'
            ]
        ];
    }
}