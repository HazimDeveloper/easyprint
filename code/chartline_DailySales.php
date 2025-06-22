<?php
// chartLine_DailySales.php
?>
<div class="card-body">
    <h6 class="card-title text-center mb-4">Daily Sales Trends</h6>
    
    <?php
    // Fetch daily sales data for the last 7 days
    $dailySalesQuery = "SELECT DATE(p.paymentDate) as saleDate, 
                        SUM(p.amount) as dailyTotal,
                        COUNT(p.paymentID) as orderCount
                        FROM payment p
                        JOIN `order` o ON p.orderID = o.orderID
                        WHERE p.paymentDate >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                        AND o.orderStatus = 'Completed'
                        GROUP BY DATE(p.paymentDate)
                        ORDER BY saleDate DESC
                        LIMIT 7";
    
    $dailySalesResult = mysqli_query($conn, $dailySalesQuery);
    
    if ($dailySalesResult && mysqli_num_rows($dailySalesResult) > 0):
        $salesData = [];
        $totalWeekSales = 0;
        $totalWeekOrders = 0;
        
        while ($row = mysqli_fetch_assoc($dailySalesResult)) {
            $salesData[] = $row;
            $totalWeekSales += $row['dailyTotal'];
            $totalWeekOrders += $row['orderCount'];
        }
        
        // Reverse array to show oldest first
        $salesData = array_reverse($salesData);
    ?>
    
    <div class="row mb-3">
        <div class="col-6 text-center">
            <div class="border-end">
                <h5 class="text-primary mb-0">RM <?= number_format($totalWeekSales, 2) ?></h5>
                <small class="text-muted">Weekly Sales</small>
            </div>
        </div>
        <div class="col-6 text-center">
            <h5 class="text-success mb-0"><?= $totalWeekOrders ?></h5>
            <small class="text-muted">Total Orders</small>
        </div>
    </div>
    
    <div class="table-responsive">
        <table class="table table-sm">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Sales (RM)</th>
                    <th>Orders</th>
                    <th>Trend</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $maxSales = max(array_column($salesData, 'dailyTotal'));
                foreach ($salesData as $index => $row): 
                    $progressPercent = $maxSales > 0 ? ($row['dailyTotal'] / $maxSales) * 100 : 0;
                    $dateFormatted = date('M j', strtotime($row['saleDate']));
                    $dayName = date('D', strtotime($row['saleDate']));
                ?>
                <tr>
                    <td>
                        <strong><?= $dateFormatted ?></strong><br>
                        <small class="text-muted"><?= $dayName ?></small>
                    </td>
                    <td>
                        <strong>RM <?= number_format($row['dailyTotal'], 2) ?></strong>
                    </td>
                    <td>
                        <span class="badge bg-info"><?= $row['orderCount'] ?></span>
                    </td>
                    <td>
                        <div class="progress" style="height: 6px; width: 60px;">
                            <div class="progress-bar bg-primary" role="progressbar" 
                                 style="width: <?= $progressPercent ?>%" 
                                 aria-valuenow="<?= $progressPercent ?>" 
                                 aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <?php else: ?>
    <div class="text-center text-muted">
        <i class="material-symbols-outlined" style="font-size: 3rem;">trending_up</i>
        <p>No sales data available</p>
        <small>Daily sales trends will appear once payments are made</small>
    </div>
    <?php endif; ?>
</div>