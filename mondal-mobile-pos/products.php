<!-- Placeholder for products.php -->
<?php
$pageTitle = "Products";
require_once 'includes/header.php';
?>

<div class="bg-white p-4 rounded-lg shadow mb-6">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold">Add New Product</h3>
    </div>
    
    <form id="addProductForm" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div>
            <label for="productName" class="block text-sm font-medium text-gray-700">Product Name</label>
            <input type="text" id="productName" name="productName" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div>
            <label for="purchasePrice" class="block text-sm font-medium text-gray-700">Purchase Price (₹)</label>
            <input type="number" step="0.01" id="purchasePrice" name="purchasePrice" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div>
            <label for="sellPrice" class="block text-sm font-medium text-gray-700">Sell Price (₹)</label>
            <input type="number" step="0.01" id="sellPrice" name="sellPrice" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div>
            <label for="quantity" class="block text-sm font-medium text-gray-700">Quantity</label>
            <input type="number" id="quantity" name="quantity" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div class="md:col-span-2 lg:col-span-4">
            <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Add Product
            </button>
        </div>
    </form>
</div>

<div class="bg-white p-4 rounded-lg shadow">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold">Product List</h3>
        <div class="relative">
            <input type="text" id="productSearch" placeholder="Search products..." class="border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200" id="productsTable">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Purchase Price</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sell Price</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200" id="productsTableBody">
                <!-- Products will be loaded here via JavaScript -->
            </tbody>
        </table>
    </div>
</div>

<!-- Edit Product Modal -->
<div id="editProductModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">Edit Product</h3>
            <button id="closeEditModal" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="editProductForm">
            <input type="hidden" id="editProductId">
            <div class="mb-4">
                <label for="editProductName" class="block text-sm font-medium text-gray-700">Product Name</label>
                <input type="text" id="editProductName" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="mb-4">
                <label for="editPurchasePrice" class="block text-sm font-medium text-gray-700">Purchase Price (₹)</label>
                <input type="number" step="0.01" id="editPurchasePrice" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="mb-4">
                <label for="editSellPrice" class="block text-sm font-medium text-gray-700">Sell Price (₹)</label>
                <input type="number" step="0.01" id="editSellPrice" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="mb-4">
                <label for="editQuantity" class="block text-sm font-medium text-gray-700">Quantity</label>
                <input type="number" id="editQuantity" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" id="cancelEdit" class="bg-gray-300 text-gray-700 py-2 px-4 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    Cancel
                </button>
                <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Update
                </button>
            </div>
        </form>
    </div>
</div>

<script src="assets/js/products.js"></script>

<?php
require_once 'includes/footer.php';
?>