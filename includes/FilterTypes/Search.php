<?php
namespace ARC\Lens\FilterTypes;

if (!defined('ABSPATH')) exit;

class Search extends FilterType
{
    public function render()
    {
        ob_start();
        ?>
        <div class="arc-lens-filter arc-lens-filter-search">
            <label for="<?php echo esc_attr($this->getId()); ?>">
                <?php echo esc_html($this->getLabel()); ?>
            </label>
            
            <input 
                type="text" 
                name="<?php echo esc_attr($this->key); ?>" 
                id="<?php echo esc_attr($this->getId()); ?>"
                placeholder="<?php echo esc_attr($this->getPlaceholder()); ?>"
                class="arc-lens-search"
                <?php echo $this->getAttributes(); ?>>
        </div>
        <?php
        return ob_get_clean();
    }

    public function getScripts()
    {
        return ['arc-lens-search'];
    }

    public function renderInlineScript()
    {
        // Optional: Add debounce for search
        $debounce = $this->getConfig('debounce', 300);
        
        return "
        // Debounce search input
        (function() {
            const input = document.getElementById('{$this->getId()}');
            let timeout;
            input?.addEventListener('input', function() {
                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    input.form?.dispatchEvent(new Event('submit'));
                }, {$debounce});
            });
        })();
        ";
    }
}