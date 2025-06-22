<?php
// chartBar_PackageSales.php
?>
<div class="card-body">
    <h6 class="card-title text-center mb-4">Package Sales Overview</h6>
    
    <?php
    // Fetch package sales data
    $packageSalesQuery = "SELECT p.packageName, p.colorOption, 
                         COUNT(op.orderPackageID) as orderCount,
                         SUM(op.orderPackageQuantity) as totalQuantity
                         FROM package p
                         LEFT JOIN orderpackage op ON p.packageID = op.packageID
                         GROUP BY p.packageID
                         ORDER BY totalQuantity DESC";
    
    $packageSalesResult = mysqli_query($conn, $packageSalesQuery);
    
    if ($packageSalesResult && mysqli_num_rows($packageSalesResult) > 0):
    ?>
    
    <div class="table-responsive">
        <table class="table table-sm">
            <thead>
                <tr>
                    <th>Package</th>
                    <th>Orders</th>
                    <th>Total Qty</th>
                    <th>Progress</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $maxQuantity = 0;
                $salesData = [];
                
                // First pass to get max quantity for progress bars
                mysqli_data_seek($packageSalesResult, 0);
                while ($row = mysqli_fetch_assoc($packageSalesResult)) {
                    $salesData[] = $row;
                    if ($row['totalQuantity'] > $maxQuantity) {
                        $maxQuantity = $row['totalQuantity'];
                    }
                }
                
                // Second pass to display data
                foreach ($salesData as $row): 
                    $progressPercent = $maxQuantity > 0 ? ($row['totalQuantity'] / $maxQuantity) * 100 : 0;
                ?>
                <tr>
                    <td>
                        <small><strong><?= htmlspecialchars($row['packageName']) ?></strong></small><br>
                        <small class="text-muted"><?= htmlspecialchars($row['colorOption']) ?></small>
                    </td>
                    <td><span class="badge bg-primary"><?= $row['orderCount'] ?></span></td>
                    <td><strong><?= $row['totalQuantity'] ?></strong></td>
                    <td>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success" role="progressbar" 
                                 style="width: <?= $progressPercent ?>%" 
                                 aria-valuenow="<?= $progressPercent ?>" 
                                 aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>
                        <small class="text-muted"><?= number_format($progressPercent, 1) ?>%</small>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <?php else: ?>
    <div class="text-center text-muted">
        <i class="material-symbols-outlined" style="font-size: 3rem;">analytics</i>
        <p>No sales data available yet</p>
        <small>Sales data will appear once orders are placed</small>
    </div>
    <?php endif; ?>
</div>