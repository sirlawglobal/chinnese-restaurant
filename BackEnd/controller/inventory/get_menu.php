<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/db.php';

try {
    // Fetch all categories
    $categories = db_query("
        SELECT id, name, created_at 
        FROM categories 
        ORDER BY name ASC
    ", [], 'assoc');

    // Fetch all regular items
    $items = db_query("
        SELECT 
            id,
            category_id,
            name,
            description,
            price,
            has_options,
            image_url
        FROM items
        WHERE is_set_menu = 0
    ", [], 'assoc');

    // Fetch options for items that have options
    $allOptionsRaw = db_query("
        SELECT 
            item_id,
            `portion`,
            price
        FROM item_options
    ", [], 'assoc');

    // Group options by item_id
    $allOptions = [];
    foreach ($allOptionsRaw as $opt) {
        $allOptions[$opt['item_id']][] = $opt;
    }

    // Fetch set menus
    $setMenus = db_query("
        SELECT 
            id,
            name,
            price,
            items
        FROM set_menus
    ", [], 'assoc');

    // Process regular items
    foreach ($items as &$item) {
        $itemId = $item['id'];
        $item['options'] = [];

        if ($item['has_options'] && isset($allOptions[$itemId])) {
            $item['options'] = $allOptions[$itemId];
        }

        $item['price'] = (float) $item['price'];
    }

    // Set menu category
    $setMenuCategory = [
        'id' => 0,
        'name' => 'Set Menus',
        'created_at' => null,
        'items' => []
    ];

    foreach ($setMenus as $setMenu) {
        $setMenuCategory['items'][] = [
            'id' => $setMenu['id'],
            'name' => $setMenu['name'],
            'description' => 'Set menu containing: ' . $setMenu['items'],
            'price' => (float) $setMenu['price'],
            'is_set_menu' => true,
            'image_url' => null
        ];
    }

    // Organize items by category
    $menuData = [];
    foreach ($categories as $category) {
        $categoryItems = array_filter($items, function ($item) use ($category) {
            return $item['category_id'] == $category['id'];
        });

        $menuData[] = [
            'id' => (int) $category['id'],
            'name' => $category['name'],
            'items' => array_values($categoryItems)
        ];
    }

    // Add set menu category if it has items
    if (!empty($setMenuCategory['items'])) {
        $menuData[] = $setMenuCategory;
    }

    echo json_encode([
        'status' => 'success',
        'data' => [
            'categories' => $menuData
        ],
        'timestamp' => time()
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
        'error' => $GLOBALS['DB_STATE']['error'] ?? 'Unknown DB error'
    ]);
    error_log("Error: " . $e->getMessage());
}
