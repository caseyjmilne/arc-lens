<div class="arc-lens-grid">
    <?php if (!empty($items) && is_array($items)): ?>
        <?php foreach ($items as $item): ?>
            <?php
                // expose $item as individual vars for item template
                extract($item);
                include ARC_LENS_PATH . 'templates/item-doc.php';
            ?>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No items found.</p>
    <?php endif; ?>
</div>
