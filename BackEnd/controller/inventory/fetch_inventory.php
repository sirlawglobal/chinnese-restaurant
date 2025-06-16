<?php
require_once __DIR__ . '/../../config/init.php';

header('Content-Type: application/json');

// ============ STOCK CHART LOGIC ============
if (isset($_GET['chart']) && $_GET['chart'] === 'stock') {
    $sql = "SELECT qty, record_qty FROM stock";
    $rows = db_query($sql, [], 'assoc');

    $available = $low = $out = 0;

    foreach ($rows as $row) {
        $qty = (int)$row['qty'];
        $record = (int)$row['record_qty'];

        if ($qty === 0) {
            $out++;
        } elseif ($qty <= 10 || $qty <= $record) {
            $low++;
        } else {
            $available++;
        }
    }

    echo json_encode([
        'available' => $available,
        'low' => $low,
        'out' => $out
    ]);
    exit;
}

// ============ SUPPLY LINE CHART LOGIC ============
if (isset($_GET['chart']) && $_GET['chart'] === 'supply') {
    $sql = "
        SELECT DATE_FORMAT(created_at, '%b') AS month, COUNT(*) AS total
        FROM stock
        GROUP BY MONTH(created_at)
        ORDER BY MONTH(created_at)
    ";
    $rows = db_query($sql, [], 'assoc');

    $labels = [];
    $data = [];

    foreach ($rows as $row) {
        $labels[] = $row['month'];
        $data[] = (int)$row['total'];
    }

    echo json_encode([
        'labels' => $labels,
        'data' => $data
    ]);
    exit;
}

// ============ DEFAULT: INVENTORY TABLE (no chart) ============
$sql = " 
    SELECT 
        s.id,
        s.name,
        c.name AS category,
        s.qty AS stock_quantity,
        s.record_qty AS reorder_quantity
    FROM stock s
    LEFT JOIN inves_categories c ON s.category_id = c.id
    ORDER BY s.updated_at DESC
";

$items = db_query($sql, [], 'assoc');
$data = [];

if ($items !== false) {
    foreach ($items as $item) {
        $stock = (int)$item['stock_quantity'];
        $reorder = (int)$item['reorder_quantity'];
        $percent = ($reorder > 0) ? min(100, ($stock / $reorder) * 100) : 0;
        $percent = number_format($percent, 2);

        $status = 'Available';
        $statusClass = 'available';
        if ($stock == 0) {
            $status = 'Out of Stock';
            $statusClass = 'out-of-stock';
        } elseif ($stock <= 10) {
            $status = 'Low';
            $statusClass = 'low';
        }

        $stockHtml = "
            <div class='stock-level' data-stock='{$stock}' data-reorder='{$reorder}'>
                <div class='progress-bar-container'>
                    <div class='progress-bar {$statusClass}' style='width: {$percent}%'></div>
                </div>
                {$stock}
            </div>
        ";

        $data[] = [
            '',
            htmlspecialchars($item['name']),
            htmlspecialchars($item['category'] ?? 'Uncategorized'),
            "<span class='status {$statusClass}'>" . htmlspecialchars($status) . "</span>",
            $stockHtml,
            $reorder,
            "<div class='action'>
                <button class='reorder-button' data-id='{$item['id']}' data-name=\"" . htmlspecialchars($item['name']) . "\">Reorder</button>
                <button class='update-stock-button' data-id='{$item['id']}' data-name=\"" . htmlspecialchars($item['name']) . "\">Update Stock</button>
            </div>"
        ];
    }
}

echo json_encode(['data' => $data]);
