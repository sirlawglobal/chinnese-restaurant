<?php
ini_set('display_errors', 0);
ini_set('log_errors', 1);

require_once '../../config/db.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => '', 'item_id' => null, 'image_url' => null];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON format.');
    }

    // Validate required item_id
    $item_id = filter_var($input['id'] ?? null, FILTER_VALIDATE_INT);
    if (!$item_id) {
        throw new Exception('Item ID is required.');
    }

    // Fetch current item data
    $check_query = "SELECT category_id, name, description, price, has_options, is_set_menu, image_url 
                    FROM items WHERE id = :item_id";
    $check_params = ['item_id' => $item_id];
    $result = db_query($check_query, $check_params);
    if (!$result || $result->fetchColumn() == 0) {
        throw new Exception('Menu item not found.');
    }
    $current_item = $result->fetch(PDO::FETCH_ASSOC);

    // Prepare fields for update, maintaining current values if not provided
    $category_id = isset($input['category_id']) ? filter_var($input['category_id'], FILTER_VALIDATE_INT) : $current_item['category_id'];
    $name = isset($input['name']) ? trim(filter_var($input['name'], FILTER_SANITIZE_STRING)) : $current_item['name'];
    $description = isset($input['description']) ? trim(filter_var($input['description'], FILTER_SANITIZE_STRING)) : $current_item['description'];
    $price = isset($input['price']) ? filter_var($input['price'], FILTER_VALIDATE_FLOAT) : $current_item['price'];
    $has_options = isset($input['has_options']) ? ($input['has_options'] === true ? 1 : 0) : $current_item['has_options'];
    $is_set_menu = isset($input['is_set_menu']) ? ($input['is_set_menu'] === true ? 1 : 0) : $current_item['is_set_menu'];

    if (!$category_id || empty($name)) {
        throw new Exception('Category ID and name are required.');
    }

    // Handle image upload if provided
    $image_url = $current_item['image_url'];
    if (!$has_options && !$is_set_menu && isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image_url = uploadImage($_FILES['image']);
        if (!$image_url) {
            throw new Exception('Image upload failed.');
        }
    }

    // Build dynamic UPDATE query for items table
    $fields = [];
    $params = ['item_id' => $item_id];
    
    if ($category_id !== $current_item['category_id']) {
        $fields[] = 'category_id = :category_id';
        $params['category_id'] = $category_id;
    }
    if ($name !== $current_item['name']) {
        $fields[] = 'name = :name';
        $params['name'] = $name;
    }
    if ($description !== $current_item['description']) {
        $fields[] = 'description = :description';
        $params['description'] = $description ?: null;
    }
    if ($price !== $current_item['price'] && $price !== false) {
        $fields[] = 'price = :price';
        $params['price'] = $price;
    }
    if ($has_options !== $current_item['has_options']) {
        $fields[] = 'has_options = :has_options';
        $params['has_options'] = $has_options;
    }
    if ($is_set_menu !== $current_item['is_set_menu']) {
        $fields[] = 'is_set_menu = :is_set_menu';
        $params['is_set_menu'] = $is_set_menu;
    }
    if ($image_url !== $current_item['image_url']) {
        $fields[] = 'image_url = :image_url';
        $params['image_url'] = $image_url;
    }

    if (!empty($fields)) {
        $query = "UPDATE items SET " . implode(', ', $fields) . " WHERE id = :item_id";
        if (!db_query($query, $params)) {
            throw new Exception("Failed to update item: " . $GLOBALS['DB_STATE']['error']);
        }
    }

    // Handle options if provided
    if (isset($input['options']) && $has_options) {
        $options = $input['options'];
        if (!is_array($options)) {
            throw new Exception('Invalid options format.');
        }

        // Delete existing options
        $delete_query = "DELETE FROM item_options WHERE item_id = :item_id";
        if (!db_query($delete_query, ['item_id' => $item_id])) {
            throw new Exception("Failed to clear existing options: " . $GLOBALS['DB_STATE']['error']);
        }

        // Insert new options
        foreach ($options as $option) {
            if (!empty($option['portion']) && isset($option['price'])) {
                $opt_query = "INSERT INTO item_options (item_id, portion, price)
                              VALUES (:item_id, :portion, :price)";
                $opt_params = [
                    'item_id' => $item_id,
                    'portion' => $option['portion'],
                    'price' => filter_var($option['price'], FILTER_VALIDATE_FLOAT) ?: 0
                ];

                if (!db_query($opt_query, $opt_params)) {
                    throw new Exception("Failed to insert item option: " . $GLOBALS['DB_STATE']['error']);
                }
            }
        }
    } elseif ($has_options == 0 && $current_item['has_options'] == 1) {
        // Clear options if has_options is explicitly set to false
        $delete_query = "DELETE FROM item_options WHERE item_id = :item_id";
        db_query($delete_query, ['item_id' => $item_id]);
    }

    // Handle set menu if provided
    if (isset($input['set_menu']) && $is_set_menu) {
        $set_menu = $input['set_menu'];
        if (!is_array($set_menu)) {
            throw new Exception('Invalid set_menu format.');
        }

        $setMenuName = !empty($set_menu['name']) ? $set_menu['name'] : $name;
        $setMenuPrice = isset($set_menu['price']) ? floatval($set_menu['price']) : 0;
        $setMenuItems = [];

        foreach ($set_menu['items'] ?? [] as $item) {
            if (!empty($item)) {
                $setMenuItems[] = htmlspecialchars($item);
            }
        }

        // Delete existing set menu
        $delete_menu_query = "DELETE FROM set_menus WHERE item_id = :item_id";
        if (!db_query($delete_menu_query, ['item_id' => $item_id])) {
            throw new Exception("Failed to clear existing set menu: " . $GLOBALS['DB_STATE']['error']);
        }

        // Insert new set menu
        $menu_query = "INSERT INTO set_menus (item_id, name, price, items)
                       VALUES (:item_id, :name, :price, :items)";
        $menu_params = [
            'item_id' => $item_id,
            'name' => $setMenuName,
            'price' => $setMenuPrice,
            'items' => json_encode($setMenuItems)
        ];

        if (!db_query($menu_query, $menu_params)) {
            throw new Exception("Failed to insert set menu: " . $GLOBALS['DB_STATE']['error']);
        }
    } elseif ($is_set_menu == 0 && $current_item['is_set_menu'] == 1) {
        // Clear set menu if is_set_menu is explicitly set to false
        $delete_menu_query = "DELETE FROM set_menus WHERE item_id = :item_id";
        db_query($delete_menu_query, ['item_id' => $item_id]);
    }

    $response['success'] = true;
    $response['message'] = 'Menu item updated successfully';
    $response['item_id'] = $item_id;
    $response['image_url'] = $image_url;

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
exit;
?>