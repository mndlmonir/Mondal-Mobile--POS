<?php
$pageTitle = "New Bill";
require_once 'includes/header.php';
?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    <div class="lg:col-span-2">
        <div class="bg-white p-4 rounded-lg shadow mb-4">
            <h3 class="text-lg font-semibold mb-4">Select Products</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="productSearchInput" class="block text-sm font-medium text-gray-700">Search Product</label>
                    <input type="text" id="productSearchInput" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500" placeholder="Type product name...">
                </div>
                <div>
                    <label for="productSelect" class="block text-sm font-medium text-gray-700">Select Product</label>
                    <select id="productSelect" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="">-- Select a product --</option>
                        <!-- Products will be loaded via JavaScript -->
                    </select>
                </div>
            </div>
            
            <div id="selectedProductDetails" class="hidden">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div>
                        <label for="productPrice" class="block text-sm font-medium text-gray-700">Price (₹)</label>
                        <input type="number" step="0.01" id="productPrice" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500" readonly>
                    </div>
                    <div>
                        <label for="productQuantity" class="block text-sm font-medium text-gray-700">Quantity</label>
                        <input type="number" id="productQuantity" min="1" value="1" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="flex items-end">
                        <button id="addToBillBtn" class="bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 w-full">
                            Add to Bill
                        </button>
                    </div>
                </div>
                <input type="hidden" id="selectedProductId">
                <input type="hidden" id="selectedProductName">
                <input type="hidden" id="selectedProductPurchasePrice">
            </div>
        </div>
        
        <div class="bg-white p-4 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-4">Bill Items</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="billItemsTable">
                        <!-- Bill items will be added here -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div>
        <div class="bg-white p-4 rounded-lg shadow sticky top-4">
            <h3 class="text-lg font-semibold mb-4">Bill Summary</h3>
            
            <div class="mb-4">
                <label for="customerName" class="block text-sm font-medium text-gray-700">Customer Name</label>
                <input type="text" id="customerName" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500" placeholder="Optional">
            </div>
            
            <div class="mb-4">
                <label for="customerPhone" class="block text-sm font-medium text-gray-700">Customer Phone</label>
                <input type="text" id="customerPhone" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500" placeholder="Optional">
            </div>
            
            <div class="mb-4">
                <label for="discount" class="block text-sm font-medium text-gray-700">Discount (₹)</label>
                <input type="number" step="0.01" id="discount" min="0" value="0" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>
            
            <div class="border-t border-gray-200 pt-4 mb-4">
                <div class="flex justify-between mb-2">
                    <span class="text-gray-600">Subtotal:</span>
                    <span class="font-medium" id="subtotal">₹0.00</span>
                </div>
                <div class="flex justify-between mb-2">
                    <span class="text-gray-600">Discount:</span>
                    <span class="font-medium" id="discountAmount">₹0.00</span>
                </div>
                <div class="flex justify-between text-lg font-bold">
                    <span>Total:</span>
                    <span id="grandTotal">₹0.00</span>
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-3">
                <button id="printBillBtn" class="bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <i class="fas fa-print mr-2"></i> Print
                </button>
                <button id="saveBillBtn" class="bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-save mr-2"></i> Save
                </button>
                <button id="cancelBillBtn" class="bg-gray-300 text-gray-700 py-2 px-4 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 col-span-2">
                    <i class="fas fa-times mr-2"></i> Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Print Template (Hidden) -->
<div id="printTemplate" class="hidden">
    <div class="p-6 max-w-md mx-auto" id="printContent">
        <div class="text-center mb-4">
            <h2 class="text-2xl font-bold">Mondal Mobile Accessories</h2>
            <p class="text-gray-600">Phone: 7001902533</p>
        </div>
        
        <div class="border-b border-gray-200 pb-2 mb-4">
            <div class="flex justify-between">
                <span class="font-medium">Invoice No:</span>
                <span id="printInvoiceNumber"></span>
            </div>
            <div class="flex justify-between">
                <span class="font-medium">Date:</span>
                <span id="printInvoiceDate"></span>
            </div>
        </div>
        
        <div class="mb-2">
            <div class="flex justify-between">
                <span class="font-medium">Customer:</span>
                <span id="printCustomerName">Walk-in Customer</span>
            </div>
            <div class="flex justify-between">
                <span class="font-medium">Phone:</span>
                <span id="printCustomerPhone">-</span>
            </div>
        </div>
        
        <table class="w-full mb-4">
            <thead>
                <tr class="border-b border-gray-200">
                    <th class="text-left py-2">Item</th>
                    <th class="text-right py-2">Price</th>
                    <th class="text-right py-2">Qty</th>
                    <th class="text-right py-2">Total</th>
                </tr>
            </thead>
            <tbody id="printBillItems">
                <!-- Bill items will be added here -->
            </tbody>
        </table>
        
        <div class="border-t border-gray-200 pt-2">
            <div class="flex justify-between">
                <span class="font-medium">Subtotal:</span>
                <span id="printSubtotal">₹0.00</span>
            </div>
            <div class="flex justify-between">
                <span class="font-medium">Discount:</span>
                <span id="printDiscount">₹0.00</span>
            </div>
            <div class="flex justify-between text-lg font-bold">
                <span>Grand Total:</span>
                <span id="printGrandTotal">₹0.00</span>
            </div>
        </div>
        
        <div class="text-center mt-6 pt-4 border-t border-gray-200">
            <p class="text-sm text-gray-500">Thank you for your purchase!</p>
        </div>
    </div>
</div>

<script src="assets/js/new-bill.js"></script>
<script src="assets/js/print.js"></script>

<?php
require_once 'includes/footer.php';
?><!-- Placeholder for new-bill.php -->
