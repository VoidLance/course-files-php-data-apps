<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <style>
        body {
            margin: 0;
            font-family: "Segoe UI", Tahoma, sans-serif;
            background: linear-gradient(135deg, #f5f7fa, #e4edf9);
            color: #1f2937;
            padding: 24px;
        }

        .bill-card {
            max-width: 900px;
            margin: 0 auto 24px;
            background: #ffffff;
            border: 1px solid #d8e2f0;
            border-radius: 14px;
            box-shadow: 0 10px 30px rgba(10, 36, 99, 0.08);
            overflow: hidden;
        }

        .bill-header {
            padding: 20px 24px;
            background: #0f4c81;
            color: #ffffff;
        }

        .bill-header h1 {
            margin: 0 0 8px;
            font-size: 1.8rem;
        }

        .bill-header p {
            margin: 4px 0;
            opacity: 0.95;
        }

        .content {
            padding: 20px 24px 24px;
        }

        .page-intro {
            max-width: 900px;
            margin: 0 auto 20px;
            color: #314158;
        }

        .page-intro h1 {
            margin: 0 0 8px;
        }

        .page-intro p {
            margin: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
            font-size: 0.95rem;
        }

        thead {
            background: #eff5ff;
        }

        th,
        td {
            border: 1px solid #dbe7f8;
            padding: 10px;
            text-align: left;
        }

        .money {
            text-align: right;
            font-variant-numeric: tabular-nums;
        }

        .discount {
            color: #b42318;
            font-weight: 600;
        }

        .summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 12px;
            margin-top: 14px;
        }

        .summary-card {
            border: 1px solid #dbe7f8;
            border-radius: 10px;
            padding: 12px;
            background: #f9fbff;
        }

        .summary-card strong {
            display: block;
            margin-bottom: 6px;
        }

        .total {
            font-size: 1.1rem;
            font-weight: 700;
            color: #0f4c81;
        }

        .errors {
            margin: 0 0 14px;
            padding: 10px 14px;
            background: #fff4f2;
            border: 1px solid #f9c8c1;
            border-radius: 10px;
            color: #7a1d13;
        }

        .rule-list {
            margin: 0 0 16px;
            padding-left: 18px;
            color: #314158;
        }
    </style>
</head>
<body>
    <?php
    # Calculate tax after discounts are applied.
    function calculateTax($amount, $taxRate)
    {
        return round($amount * $taxRate, 2);
    }

    # Multiple customers can use the same calculator in one run.
    $customers = [
        [
            'name' => 'Customer 1',
            'products' => [
                ['name' => 'Product 1', 'price' => 10, 'quantity' => 5],
                ['name' => 'Product 2', 'price' => 20, 'quantity' => 1],
                ['name' => 'Product 3', 'price' => 30, 'quantity' => 2],
                ['name' => 'Product 4', 'price' => 18.50, 'quantity' => 3],
                ['name' => 'Product 5', 'price' => 12.75, 'quantity' => 4],
            ],
        ],
        [
            'name' => 'Customer 2',
            'products' => [
                ['name' => 'Product 1', 'price' => 14, 'quantity' => 2],
                ['name' => 'Product 2', 'price' => 26.25, 'quantity' => 3],
                ['name' => 'Product 3', 'price' => 9.99, 'quantity' => 6],
                ['name' => 'Product 4', 'price' => 45, 'quantity' => 2],
                ['name' => 'Product 5', 'price' => 7.50, 'quantity' => 5],
            ],
        ],
    ];

    # Build item totals and validate input before rendering each bill.
    ?>

    <div class="page-intro">
        <h1>Shopping Cart Calculator</h1>
        <p>This example now processes multiple customers, line-item discounts, an extra cart discount, and tax.</p>
    </div>

    <?php
    foreach ($customers as $customer) {
        $itemized = [];
        $lineTotals = [];
        $errors = [];
        $discountThreshold = 100;
        $discountRate = 0;
        $cartDiscountThreshold = 180;
        $cartDiscountRate = 0.05;
        $taxRate = 0.08;

        foreach ($customer['products'] as $product) {
            if (!is_numeric($product['quantity']) || $product['quantity'] < 0) {
                $errors[] = "Invalid quantity for {$product['name']} (must be 0 or greater).";
                continue;
            }

            $lineTotal = $product['price'] * $product['quantity'];
            $itemized[] = [
                'name' => $product['name'],
                'price' => $product['price'],
                'quantity' => $product['quantity'],
                'line_total' => $lineTotal,
            ];
            $lineTotals[] = $lineTotal;
        }

        $subtotal = array_sum($lineTotals);
        if ($subtotal > $discountThreshold) {
            $discountRate = 0.10;
        }

                    # Apply discount per item so the bill shows where savings come from.
        foreach ($itemized as $key => $item) {
            $itemDiscount = $item['line_total'] * $discountRate;
            $itemized[$key]['discount_amount'] = round($itemDiscount, 2);
            $itemized[$key]['discounted_total'] = round($item['line_total'] - $itemDiscount, 2);
        }

        $lineItemDiscount = 0;
        foreach ($itemized as $item) {
            $lineItemDiscount += $item['discount_amount'];
        }

        $afterLineDiscounts = round($subtotal - $lineItemDiscount, 2);
        $cartDiscount = 0;
        if ($afterLineDiscounts >= $cartDiscountThreshold) {
            $cartDiscount = round($afterLineDiscounts * $cartDiscountRate, 2);
        }

        $taxableTotal = round($afterLineDiscounts - $cartDiscount, 2);
        $taxAmount = calculateTax($taxableTotal, $taxRate);
        $finalTotal = round($taxableTotal + $taxAmount, 2);

                    # Identify most/least expensive line totals.
        $highestLineTotal = !empty($lineTotals) ? max($lineTotals) : 0;
        $lowestLineTotal = !empty($lineTotals) ? min($lineTotals) : 0;

                    # Generate a random order number.
        $orderNumber = rand(100000, 999999);
        ?>

        <div class="bill-card">
            <div class="bill-header">
                <h2><?= htmlspecialchars($customer['name'], ENT_QUOTES, 'UTF-8'); ?></h2>
                <p>Order Number: <?= $orderNumber; ?></p>
                <p>Line Discount Rule: 10% off each item when subtotal exceeds $<?= number_format($discountThreshold, 2); ?></p>
                <p>Cart Discount Rule: 5% off the discounted cart total when it reaches $<?= number_format($cartDiscountThreshold, 2); ?> or more</p>
                <p>Tax Rate: <?= number_format($taxRate * 100, 2); ?>%</p>
            </div>

            <div class="content">
                <ul class="rule-list">
                    <li>Subtotal before discounts: $<?= number_format($subtotal, 2); ?></li>
                    <li>
                        Line-item discount status:
                        <?= $discountRate > 0
                            ? 'Applied because subtotal exceeded the threshold.'
                            : 'Not applied because subtotal stayed below the threshold.'; ?>
                    </li>
                    <li>
                        Cart discount status:
                        <?= $cartDiscount > 0
                            ? 'Applied after line-item discounts because the discounted subtotal met the second threshold.'
                            : 'Not applied because the discounted subtotal did not reach the second threshold.'; ?>
                    </li>
                </ul>

                <?php if (!empty($errors)): ?>
                    <div class="errors">
                        <strong>Input Errors</strong>
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <table>
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Unit Price</th>
                            <th>Qty</th>
                            <th>Line Total</th>
                            <th>Discount (10% of line total)</th>
                            <th>Total After Line Discount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($itemized as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td class="money">$<?= number_format($item['price'], 2); ?></td>
                                <td><?= $item['quantity']; ?></td>
                                <td class="money">$<?= number_format($item['line_total'], 2); ?></td>
                                <td class="money discount">-$<?= number_format($item['discount_amount'], 2); ?></td>
                                <td class="money">$<?= number_format($item['discounted_total'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="summary">
                    <div class="summary-card">
                        <strong>Subtotal</strong>
                        <span>$<?= number_format($subtotal, 2); ?></span>
                    </div>
                    <div class="summary-card">
                        <strong>Line-Item Discount Total</strong>
                        <span class="discount">-$<?= number_format($lineItemDiscount, 2); ?></span>
                    </div>
                    <div class="summary-card">
                        <strong>Total After Line Discounts</strong>
                        <span>$<?= number_format($afterLineDiscounts, 2); ?></span>
                    </div>
                    <div class="summary-card">
                        <strong>Cart Discount</strong>
                        <span class="discount">-$<?= number_format($cartDiscount, 2); ?></span>
                    </div>
                    <div class="summary-card">
                        <strong>Tax</strong>
                        <span>$<?= number_format($taxAmount, 2); ?></span>
                    </div>
                    <div class="summary-card">
                        <strong>Final Total</strong>
                        <span class="total">$<?= number_format($finalTotal, 2); ?></span>
                    </div>
                    <div class="summary-card">
                        <strong>Highest Line Item</strong>
                        <span>$<?= number_format($highestLineTotal, 2); ?></span>
                    </div>
                    <div class="summary-card">
                        <strong>Lowest Line Item</strong>
                        <span>$<?= number_format($lowestLineTotal, 2); ?></span>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
</body>
</html>