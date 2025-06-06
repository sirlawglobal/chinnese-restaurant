<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../BackEnd/config/db.php';
$pdo = db_connect();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $response = ['success' => false, 'message' => '', 'order_id' => null, 'status' => null];

    try {
        $tx_ref = filter_input(INPUT_GET, 'tx_ref', FILTER_SANITIZE_STRING);
        $transaction_id = filter_input(INPUT_GET, 'transaction_id', FILTER_VALIDATE_INT);

        if (empty($tx_ref) || !$transaction_id) {
            throw new Exception('Transaction reference and ID are required.');
        }

        // Verify transaction with Flutterwave
        $secret_key = 'FLWSECK_TEST-c224ef82c01088e1287915ab56c0d269-X'; // Replace with your test secret key
        $url = "https://api.flutterwave.com/v3/transactions/{$transaction_id}/verify";
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer {$secret_key}",
                "Content-Type: application/json",
            ],
        ]);
        $result = curl_exec($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($http_code !== 200) {
            throw new Exception('Failed to verify transaction with Flutterwave.');
        }

        $data = json_decode($result, true);
        if ($data['status'] !== 'success' || $data['data']['tx_ref'] !== $tx_ref || $data['data']['status'] !== 'successful') {
            throw new Exception('Invalid or unsuccessful transaction.');
        }

        $pdo->beginTransaction();

        // Update order status
        $stmt = $pdo->prepare("
            UPDATE orders
            SET status = 'completed', transaction_id = :transaction_id
            WHERE tx_ref = :tx_ref AND status = 'pending'
        ");
        $stmt->execute([
            ':transaction_id' => $transaction_id,
            ':tx_ref' => $tx_ref,
        ]);

        if ($stmt->rowCount() === 0) {
            throw new Exception('Order not found or already processed.');
        }

        // Get order ID
        $stmt = $pdo->prepare("SELECT id FROM orders WHERE tx_ref = :tx_ref");
        $stmt->execute([':tx_ref' => $tx_ref]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        $pdo->commit();

        $response['success'] = true;
        $response['message'] = 'Payment verified successfully.';
        $response['order_id'] = $order['id'];
        $response['status'] = 'completed';

    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $response['message'] = 'Error: ' . $e->getMessage();
    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $response['message'] = 'Database error: ' . $e->getMessage();
    }

    echo json_encode($response);
    exit;
}
?>