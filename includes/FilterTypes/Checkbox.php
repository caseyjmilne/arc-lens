<?php
namespace ARC\Lens\FilterTypes;

if (!defined('ABSPATH')) exit;

class Checkbox extends FilterType
{
    public function render()
    {
        ob_start();
        ?>
        <div class="arc-lens-filter arc-lens-filter-checkbox">
            <label class="arc-lens-checkbox-label">
                <input 
                    type="checkbox" 
                    name="<?php echo esc_attr($this->key); ?>" 
                    id="<?php echo esc_attr($this->getId()); ?>"
                    value="1"
                    class="arc-lens-checkbox"
                    <?php echo $this->getAttributes(); ?>>
                <span><?php echo esc_html($this->getLabel()); ?></span>
            </label>
        </div>
        <?php
        return ob_get_clean();
    }
}