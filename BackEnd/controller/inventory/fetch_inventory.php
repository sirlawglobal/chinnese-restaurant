<?php
require_once __DIR__ . '/../../config/init.php';
// Fetch inventory items with category names
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

$items = db_query($sql, [], 'assoc'); // Using your db_query() function

$data = [];

if ($items !== false) {
    foreach ($items as $item) {
        // Determine stock status
        $status = 'Available';
        $statusClass = 'available';

        if ((int)$item['stock_quantity'] == 0) {
            $status = 'Out of Stock';
            $statusClass = 'out-of-stock';
        } elseif ((int)$item['stock_quantity'] <= 10) {
            $status = 'Low';
            $statusClass = 'low';
        }

        // Calculate stock progress bar percentage
        $stock = (int)$item['stock_quantity'];
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
    '', // checkbox column
    htmlspecialchars($item['name']),
    htmlspecialchars($item['category'] ?? 'Uncategorized'),
    "<span class='status {$statusClass}'>{$status}</span>",
    $stockHtml,
    $reorder,
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
    </div>"
];

    }
}

echo json_encode(['data' => $data]);
