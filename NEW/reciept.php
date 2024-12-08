<?php
// Sample data for the receipt
$storeName = "Mr. Boy Special Tea";
$items = []; // Empty items array to reflect the blank receipt
$total = "";
$cash = "";
$change = "";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt</title>
    <link rel="stylesheet" href="receipts.css">
</head>
<body>
    <div class="receipt">
        <div class="title"><?= $storeName ?></div>
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
                <?php if (empty($items)): ?>
                <tr>
                    <td colspan="2" style="text-align: center;">No Items</td>
                </tr>
                <?php else: ?>
                    <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?= $item['description'] ?></td>
                        <td><?= $item['price'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        <div class="line"></div>
        <table>
            <tr>
                <td class="text-bold">Total</td>
                <td><?= $total ?></td>
            </tr>
            <tr>
                <td>Cash</td>
                <td><?= $cash ?></td>
            </tr>
            <tr>
                <td>Change</td>
                <td><?= $change ?></td>
            </tr>
        </table>
        <div class="line"></div>
        <div class="thank-you">THANK YOU!</div>
    </div>
</body>
</html>
