# ARC Lens FilterSet Implementation Guide

## Quick Start

### 1. Create Your FilterSet Class

```php
<?php
namespace YourPlugin;

use ARC\Lens\FilterSet;

class ProductsFilterSet extends FilterSet
{
    protected $collection = 'products';
    
    protected $filters = [
        'category' => [
            'type' => 'select',
            'label' => 'Category',
            'options' => ['electronics', 'clothing', 'books']
        ],
        'search' => [
            'type' => 'text',
            'label' => 'Search',
            'placeholder' => 'Search products...'
        ]
    ];
    
    protected function wrapperTemplate() {
        return __DIR__ . '/templates/products-wrapper.php';
    }
    
    protected function itemTemplate() {
        return __DIR__ . '/templates/product-item.php';
    }
}
```

### 2. Register Your FilterSet

```php
add_action('arc_lens_register_filtersets', function() {
    $filterSet = new YourPlugin\ProductsFilterSet();
    arc_lens_register('products', $filterSet);
});
```

### 3. Create Templates

**products-wrapper.php**
```php
<div class="products-grid">
    <?php foreach ($items as $item): ?>
        <?php include $itemTemplate; ?>
    <?php endforeach; ?>
</div>
```

**product-item.php**
```php
<div class="product-card">
    <h3><?php echo esc_html($item['name']); ?></h3>
    <p><?php echo esc_html($item['price']); ?></p>
</div>
```

### 4. Render in Your Theme

```php
<?php arc_lens_render('products'); ?>
```

## Filter Types

### Select Dropdown
```php
'status' => [
    'type' => 'select',
    'label' => 'Status',
    'options' => ['draft', 'published'],
    'placeholder' => 'All Statuses'
]
```

### Text Input
```php
'search' => [
    'type' => 'text',
    'label' => 'Search',
    'placeholder' => 'Search...'
]
```

### Checkbox
```php
'featured' => [
    'type' => 'checkbox',
    'label' => 'Featured Only'
]
```

### Dynamic Options
```php
'category' => [
    'type' => 'select',
    'label' => 'Category',
    'options' => 'get_categories' // method name
]

// In your FilterSet class:
private function get_categories() {
    return ['cat1' => 'Category 1', 'cat2' => 'Category 2'];
}
```

## Advanced Features

### Custom Query Logic
```php
protected function modifyQuery($query, $params) {
    // Only published for non-admins
    if (!current_user_can('manage_options')) {
        $query->where('status', 'published');
    }
    
    // Multi-field search
    if (!empty($params['search'])) {
        $search = $params['search'];
        $query->where(function($q) use ($search) {
            $q->where('title', 'LIKE', "%{$search}%")
              ->orWhere('content', 'LIKE', "%{$search}%");
        });
    }
    
    return $query;
}
```

### Template Fallbacks
```php
protected function wrapperTemplate() {
    // Check theme first
    $theme = get_stylesheet_directory() . '/products/wrapper.php';
    if (file_exists($theme)) {
        return $theme;
    }
    
    // Fall back to plugin
    return __DIR__ . '/templates/wrapper.php';
}
```

### Default Query Settings
```php
protected $defaultQuery = [
    'orderBy' => 'created_at',
    'orderDir' => 'desc',
    'perPage' => 20
];
```

## Template Variables

### Wrapper Template
- `$items` - Array of all records
- `$itemTemplate` - Path to item template

### Item Template
- `$item` - Single record (array/object)

### Filter Template
- `$collection` - Collection key
- `$apiRoute` - REST endpoint for data
- `$renderRoute` - REST endpoint for rendering
- `$filters` - Filter configurations

## Helper Functions

```php
// Render filters
arc_lens_render('products');

// Get FilterSet instance
$filterSet = arc_lens_get_filterset('products');

// Check if registered
if (arc_lens_has_filterset('products')) {
    // ...
}

// Register programmatically
arc_lens_register('products', $filterSet);
```

## File Structure

```
your-plugin/
├── src/
│   └── ProductsFilterSet.php
├── templates/
│   ├── products-wrapper.php
│   └── product-item.php
└── your-plugin.php (registration)
```