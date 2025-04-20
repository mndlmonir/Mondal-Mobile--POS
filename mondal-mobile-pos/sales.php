<!-- Placeholder for sales.php -->
<?php
$pageTitle = "Sales Report";
require_once 'includes/header.php';
?>

<div class="bg-white p-4 rounded-lg shadow">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold">Sales Report</h3>
        <div class="flex space-x-3">
            <div>
                <label for="dateFrom" class="block text-sm font-medium text-gray-700">From</label>
                <input type="date" id="dateFrom" class="mt-1 block border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label for="dateTo" class="block text-sm font-medium text-gray-700">To</label>
                <input type="date" id="dateTo" class="mt-1 block border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="flex items-end">
                <button id="filterSalesBtn" class="bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Filter
                </button>
            </div>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice No.</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Selling Price</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Purchase Price</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Discount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Profit</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200" id="salesReportTable">
                <!-- Sales data will be loaded here via JavaScript -->
            </tbody>
            <tfoot class="bg-gray-50 font-semibold">
                <tr>
                    <td colspan="3" class="px-6 py-3 text-right">Totals:</td>
                    <td class="px-6 py-3" id="totalSellingPrice">₹0.00</td>
                    <td class="px-6 py-3" id="totalPurchasePrice">₹0.00</td>
                    <td class="px-6 py-3" id="totalDiscount">₹0.00</td>
                    <td class="px-6 py-3" id="totalProfit">₹0.00</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<script src="assets/js/sales.js"></script>

<?php
require_once 'includes/footer.php';
?>