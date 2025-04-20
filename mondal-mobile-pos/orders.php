<!-- Placeholder for orders.php -->
<?php
$pageTitle = "Orders";
require_once 'includes/header.php';
?>

<div class="bg-white p-4 rounded-lg shadow mb-6">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold">Create New Order</h3>
    </div>
    
    <form id="createOrderForm" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label for="orderCustomerName" class="block text-sm font-medium text-gray-700">Customer Name</label>
            <input type="text" id="orderCustomerName" name="orderCustomerName" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
        </div>
        <div>
            <label for="orderCustomerPhone" class="block text-sm font-medium text-gray-700">Customer Phone</label>
            <input type="text" id="orderCustomerPhone" name="orderCustomerPhone" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
        </div>
        <div class="md:col-span-2">
            <label for="orderProductDetails" class="block text-sm font-medium text-gray-700">Product Details</label>
            <textarea id="orderProductDetails" name="orderProductDetails" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500" required></textarea>
        </div>
        <div>
            <label for="orderStatus" class="block text-sm font-medium text-gray-700">Status</label>
            <select id="orderStatus" name="orderStatus" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                <option value="pending">Pending</option>
                <option value="delivered">Delivered</option>
            </select>
        </div>
        <div class="md:col-span-2 flex justify-end">
            <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Create Order
            </button>
        </div>
    </form>
</div>

<div class="bg-white p-4 rounded-lg shadow">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold">Order List</h3>
        <div class="relative">
            <input type="text" id="orderSearch" placeholder="Search orders..." class="border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Products</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200" id="ordersTable">
                <!-- Orders will be loaded here via JavaScript -->
            </tbody>
        </table>
    </div>
</div>

<!-- Edit Order Modal -->
<div id="editOrderModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">Edit Order</h3>
            <button id="closeEditOrderModal" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="editOrderForm">
            <input type="hidden" id="editOrderId">
            <div class="mb-4">
                <label for="editOrderCustomerName" class="block text-sm font-medium text-gray-700">Customer Name</label>
                <input type="text" id="editOrderCustomerName" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
            </div>
            <div class="mb-4">
                <label for="editOrderCustomerPhone" class="block text-sm font-medium text-gray-700">Customer Phone</label>
                <input type="text" id="editOrderCustomerPhone" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
            </div>
            <div class="mb-4">
                <label for="editOrderProductDetails" class="block text-sm font-medium text-gray-700">Product Details</label>
                <textarea id="editOrderProductDetails" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500" required></textarea>
            </div>
            <div class="mb-4">
                <label for="editOrderStatus" class="block text-sm font-medium text-gray-700">Status</label>
                <select id="editOrderStatus" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="pending">Pending</option>
                    <option value="delivered">Delivered</option>
                </select>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" id="cancelEditOrder" class="bg-gray-300 text-gray-700 py-2 px-4 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    Cancel
                </button>
                <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Update
                </button>
            </div>
        </form>
    </div>
</div>

<script src="assets/js/orders.js"></script>

<?php
require_once 'includes/footer.php';
?>