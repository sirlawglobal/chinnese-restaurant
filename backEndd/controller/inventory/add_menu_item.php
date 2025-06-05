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
    $category_id  = filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT);
    $name         = trim(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING));
    $description  = trim(filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING));
    $price        = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);
    $has_options  = isset($_POST['has_options']) && $_POST['has_options'] === 'true' ? 1 : 0;
    $is_set_menu  = isset($_POST['is_set_menu']) && $_POST['is_set_menu'] === 'true' ? 1 : 0;

    if (!$category_id || empty($name)) {
        throw new Exception('Category ID and name are required.');
    }

    // Upload image if applicable
    $image_url = null;
    if (!$has_options && !$is_set_menu && isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image_url = uploadImage($_FILES['image']);
        if (!$image_url) {
            throw new Exception('Image upload failed.');
        }
    }

    // Insert main item
    $query = "INSERT INTO items (category_id, name, description, price, has_options, is_set_menu, image_url)
              VALUES (:category_id, :name, :description, :price, :has_options, :is_set_menu, :image_url)";
    $params = [
        'category_id' => $category_id,
        'name'        => $name,
        'description' => $description ?: null,
        'price'       => $price !== false ? $price : null,
        'has_options' => $has_options,
        'is_set_menu' => $is_set_menu,
        'image_url'   => $image_url
    ];

    if (!db_query($query, $params)) {
        throw new Exception("Failed to insert item: " . $GLOBALS['DB_STATE']['error']);
    }

    $item_id = $GLOBALS['DB_STATE']['insert_id'];

    // Handle options if present
    if ($has_options && !empty($_POST['options'])) {
        $options = json_decode($_POST['options'], true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid options JSON format.');
        }

        foreach ($options as $option) {
            if (!empty($option['portion']) && isset($option['price'])) {
                $opt_query = "INSERT INTO item_options (item_id, `portion`, price)
                              VALUES (:item_id, :portion, :price)";
                $opt_params = [
                    'item_id' => $item_id,
                    'portion' => $option['portion'],
                    'price'   => $option['price']
                ];

                if (!db_query($opt_query, $opt_params)) {
                    throw new Exception("Failed to insert item option: " . $GLOBALS['DB_STATE']['error']);
                }
            }
        }
    }

    // Handle set menu if applicable
    if ($is_set_menu && !empty($_POST['set_menu'])) {
        $set_menu = json_decode($_POST['set_menu'], true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($set_menu)) {
            throw new Exception('Invalid set_menu JSON format.');
        }

        $setMenuName  = !empty($set_menu['name']) ? $set_menu['name'] : $name;
        $setMenuPrice = isset($set_menu['price']) ? floatval($set_menu['price']) : 0;
        $setMenuItems = [];

        foreach ($set_menu['items'] ?? [] as $item) {
            if (!empty($item)) {
                $setMenuItems[] = htmlspecialchars($item);
            }
        }

        $menu_query = "INSERT INTO set_menus (item_id, name, price, items)
                       VALUES (:item_id, :name, :price, :items)";
        $menu_params = [
            'item_id' => $item_id,
            'name'    => $setMenuName,
            'price'   => $setMenuPrice,
            'items'   => json_encode($setMenuItems)
        ];

        if (!db_query($menu_query, $menu_params)) {
            throw new Exception("Failed to insert set menu: " . $GLOBALS['DB_STATE']['error']);
        }
    }

    $response['success'] = true;
    $response['message'] = 'Menu item added successfully';
    $response['item_id'] = $item_id;
    $response['image_url'] = $image_url;

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
exit;
