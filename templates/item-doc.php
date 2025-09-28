<div class="arc-lens-item">
    <h3><?php echo esc_html($title ?? 'Untitled'); ?></h3>
    <p><?php echo esc_html($author_id ?? 'Unknown Author'); ?></p>
    <?php if (!empty($status)): ?>
        <span class="status"><?php echo esc_html($status); ?></span>
    <?php endif; ?>
</div>
