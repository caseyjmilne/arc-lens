<?php
namespace ARC\Lens\FilterTypes;

if (!defined('ABSPATH')) exit;

class Select extends FilterType
{
    public function render()
    {
        $options = $this->getConfig('options', []);
        
        // Resolve callable options
        if (is_callable($options)) {
            $options = call_user_func($options);
        }
        
        ob_start();
        ?>
        <div class="arc-lens-filter arc-lens-filter-select">
            <label for="<?php echo esc_attr($this->getId()); ?>">
                <?php echo esc_html($this->getLabel()); ?>
            </label>
            
            <select 
                name="<?php echo esc_attr($this->key); ?>" 
                id="<?php echo esc_attr($this->getId()); ?>"
                class="arc-lens-select"
                <?php echo $this->getAttributes(); ?>>
                
                <option value="">
                    <?php echo esc_html($this->getPlaceholder() ?: 'All'); ?>
                </option>
                
                <?php foreach ($options as $value => $label): ?>
                    <?php
                    // Handle indexed arrays
                    if (is_numeric($value)) {
                        $value = $label;
                    }
                    ?>
                    <option value="<?php echo esc_attr($value); ?>">
                        <?php echo esc_html($label); ?>
                    </option>
                <?php endforeach; ?>
                
            </select>
        </div>
        <?php
        return ob_get_clean();
    }

    public function getScripts()
    {
        return ['arc-lens-select'];
    }
}