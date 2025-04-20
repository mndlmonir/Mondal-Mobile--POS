<!-- Placeholder for dashboard.php -->
<?php
$pageTitle = "Dashboard";
require_once 'includes/header.php';
require_once 'includes/functions.php';
?>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <!-- Today's Sales -->
    <div class="bg-white p-4 rounded-lg shadow">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-gray-500">Today's Sales</p>
                <h3 class="text-2xl font-bold">₹<?php echo number_format(getTodaysSales($conn), 2); ?></h3>
            </div>
            <div class="bg-blue-100 p-3 rounded-full">
                <i class="fas fa-rupee-sign text-blue-600 text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Today's Profit -->
    <div class="bg-white p-4 rounded-lg shadow">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-gray-500">Today's Profit</p>
                <h3 class="text-2xl font-bold">₹<?php echo number_format(getTodaysProfit($conn), 2); ?></h3>
            </div>
            <div class="bg-green-100 p-3 rounded-full">
                <i class="fas fa-chart-line text-green-600 text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Monthly Sales -->
    <div class="bg-white p-4 rounded-lg shadow">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-gray-500">Monthly Sales</p>
                <h3 class="text-2xl font-bold">₹<?php echo number_format(getMonthlySales($conn), 2); ?></h3>
            </div>
            <div class="bg-purple-100 p-3 rounded-full">
                <i class="fas fa-calendar-alt text-purple-600 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Recent Sales Activity -->
<div class="bg-white p-4 rounded-lg shadow">
    <h3 class="text-lg font-semibold mb-4">Recent Sales Activity</h3>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice No.</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php
                $recentSales = getRecentSales($conn);
                if (count($recentSales) > 0) {
                    foreach ($recentSales as $sale) {
                        echo '<tr>';
                        echo '<td class="px-6 py-4 whitespace-nowrap">' . $sale['invoice_number'] . '</td>';
                        echo '<td class="px-6 py-4 whitespace-nowrap">' . date('d M Y, h:i A', strtotime($sale['created_at'])) . '</td>';
                        echo '<td class="px-6 py-4 whitespace-nowrap">' . $sale['item_count'] . '</td>';
                        echo '<td class="px-6 py-4 whitespace-nowrap">₹' . number_format($sale['total_amount'], 2) . '</td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="4" class="px-6 py-4 text-center text-gray-500">No recent sales found</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>