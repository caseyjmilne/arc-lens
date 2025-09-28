<?php
/**
 * Filter group template
 * Variables provided:
 * - $alias         (string)
 * - $config        (array)
 * - $getAllRoute   (string)
 */

$collectionData = [
    'alias'  => $alias,
    'config' => $config,
];
?>
<div class="arc-lens-wrapper"
     data-collection='<?php echo esc_attr(json_encode($collectionData)); ?>'
     data-fetch-route='<?php echo esc_attr($getAllRoute); ?>'>

    <!-- Filter 1 -->
    <div class="arc-lens-filter" data-filter-key="status">
        <label>Status</label>
        <select>
            <option value="">All</option>
            <option value="published">Published</option>
            <option value="draft">Draft</option>
        </select>
    </div>

    <!-- Filter 2 -->
    <div class="arc-lens-filter" data-filter-key="author_id">
        <label>Author</label>
        <select>
            <option value="">All</option>
            <option value="1">Author 1</option>
            <option value="2">Author 2</option>
        </select>
    </div>
</div>
