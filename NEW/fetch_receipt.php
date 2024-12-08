<?php
include 'config.php';

if (isset($_GET['id'])) {
    $transaction_id = intval($_GET['id']);
    
    // Fetch the transaction details
    $query = $conn->prepare("SELECT * FROM transactions WHERE id = ?");
    $query->bind_param("i", $transaction_id);
    $query->execute();
    $result = $query->get_result()->fetch_assoc();

    if ($result) {
        $storeName = "Mr. Boy Special Tea";
        $items = json_decode($result['order_details'], true); // Assuming `order_details` is JSON
        $total = number_format($result['total_amount'], 2);
        $cash = number_format($result['cash'], 2);
        $change = number_format($result['cash'] - $result['total_amount'], 2);
    } else {
        echo "Receipt not found.";
        exit;
    }
} else {
    echo "Invalid request.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt</title>
    <link rel="stylesheet" href="receipts.css">
</head>
<body>
    <div class="receipt">
        <div class="title"><?= htmlspecialchars($storeName) ?></div>
        <div class="line"></div>
        <div class="text-bold">CASH RECEIPT</div>
        <div class="line"></div>
        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['description']) ?></td>
                        <td>₱<?= number_format($item['price'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="line"></div>
        <table>
            <tr>
                <td class="text-bold">Total</td>
                <td>₱<?= $total ?></td>
            </tr>
            <tr>
                <td>Cash</td>
                <td>₱<?= $cash ?></td>
            </tr>
            <tr>
                <td>Change</td>
                <td>₱<?= $change ?></td>
            </tr>
        </table>
        <div class="line"></div>
        <div class="thank-you">THANK YOU!</div>
    </div>
</body>
</html>
