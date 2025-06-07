<?php
require_once __DIR__ . '/../../config/init.php';
$sql = "
    SELECT 
        i.id,
        i.name,
        c.name AS category,
        i.stock_quantity,
        i.reorder_quantity
    FROM items i
    LEFT JOIN categories c ON i.category_id = c.id
";

$items = db_query($sql, [], 'assoc');
$data = [];

if ($items !== false) {
    foreach ($items as $item) {
        $status = 'Available';
        $statusClass = 'available';
        $stock = (int)$item['stock_quantity'];
        if ($stock == 0) {
            $status = 'Out of Stock';
            $statusClass = 'out-of-stock';
        } elseif ($stock <= 10) {
            $status = 'Low';
            $statusClass = 'low';
        }
        error_log("Item: {$item['name']}, Status: {$status}"); // Debug log

        $reorder = (int)$item['reorder_quantity'];
        $percent = ($reorder > 0) ? min(100, ($stock / $reorder) * 100) : 0;
        $percent = number_format($percent, 2);

        $stockHtml = "
            <div class='stock-level' data-stock='{$stock}' data-reorder='{$reorder}'>
                <div class='progress-bar-container'>
                    <div class='progress-bar {$statusClass}' style='width: {$percent}%'></div>
                </div>
                {$stock}
            </div>
        ";

        $data[] = [
            '', // Checkbox (index 0)
            htmlspecialchars($item['name']), // Item (index 1)
            htmlspecialchars($item['category'] ?? 'Uncategorized'), // Category (index 2)
            "<span class='status {$statusClass}'>" . htmlspecialchars($status) . "</span>", // Status (index 3)
            $stockHtml, // Qty in Stock (index 4)
            $reorder, // Qty in Reorder (index 5)
            "<div class='action'>
                <button class='reorder-button' 
                        data-id='{$item['id']}' 
                        data-name=\"" . htmlspecialchars($item['name']) . "\">
                    Reorder
                </button>
                <button class='update-stock-button' 
                        data-id='{$item['id']}' 
                        data-name=\"" . htmlspecialchars($item['name']) . "\">
                    Update Stock
                </button>
            </div>" // Action (index 6)
        ];
    }
}

header('Content-Type: application/json');
echo json_encode(['data' => $data]);