<?php
namespace ARC\Lens;

class Render
{
    public static function filter($args = [])
    {
        $template = ARC_LENS_PATH . 'templates/filter.php';

        if (!file_exists($template)) {
            return '<!-- ARC Lens: filter.php template missing -->';
        }

        // Optionally pass data to the template later
        ob_start();
        include $template;
        return ob_get_clean();
    }
}
