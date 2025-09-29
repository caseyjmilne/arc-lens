<?php
/**
 * FilterType Abstract Base Class
 * 
 * Each filter type (Select, Search, Checkbox, etc.) extends this
 */

namespace ARC\Lens\FilterTypes;

if (!defined('ABSPATH')) exit;

abstract class FilterType
{
    /**
     * Filter configuration
     */
    protected $config = [];
    
    /**
     * Filter key/name
     */
    protected $key;
    
    /**
     * Collection this filter belongs to
     */
    protected $collection;

    public function __construct($key, $config, $collection)
    {
        $this->key = $key;
        $this->config = $config;
        $this->collection = $collection;
    }

    /**
     * Render the filter HTML
     * Must be implemented by each filter type
     */
    abstract public function render();

    /**
     * Get JavaScript dependencies for this filter type
     * Returns array of script handles to enqueue
     */
    public function getScripts()
    {
        return [];
    }

    /**
     * Get CSS dependencies for this filter type
     * Returns array of style handles to enqueue
     */
    public function getStyles()
    {
        return [];
    }

    /**
     * Render inline JavaScript for this filter instance
     * Override if filter needs instance-specific JS
     */
    public function renderInlineScript()
    {
        return '';
    }

    /**
     * Get the filter key
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Get config value
     */
    protected function getConfig($key, $default = null)
    {
        return $this->config[$key] ?? $default;
    }

    /**
     * Get label
     */
    protected function getLabel()
    {
        return $this->getConfig('label', ucfirst($this->key));
    }

    /**
     * Get placeholder
     */
    protected function getPlaceholder()
    {
        return $this->getConfig('placeholder', '');
    }

    /**
     * Get HTML attributes as string
     */
    protected function getAttributes()
    {
        $attrs = $this->getConfig('attributes', []);
        $html = [];
        
        foreach ($attrs as $key => $value) {
            $html[] = esc_attr($key) . '="' . esc_attr($value) . '"';
        }
        
        return implode(' ', $html);
    }

    /**
     * Get unique ID for this filter instance
     */
    protected function getId()
    {
        return 'filter-' . $this->collection . '-' . $this->key;
    }
}