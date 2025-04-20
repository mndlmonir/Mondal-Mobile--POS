<!-- Placeholder for products.js -->
document.addEventListener('DOMContentLoaded', function() {
    // Load products on page load
    loadProducts();

    // Add product form submission
    $('#addProductForm').submit(function(e) {
        e.preventDefault();
        
        const productName = $('#productName').val();
        const purchasePrice = $('#purchasePrice').val();
        const sellPrice = $('#sellPrice').val();
        const quantity = $('#quantity').val();
        
        if (!productName || !purchasePrice || !sellPrice || !quantity) {
            alert('Please fill all fields');
            return;
        }
        
        $.ajax({
            url: 'includes/actions.php',
            type: 'POST',
            data: {
                action: 'add_product',
                name: productName,
                purchase_price: purchasePrice,
                sell_price: sellPrice,
                quantity: quantity
            },
            success: function(response) {
                if (response.success) {
                    alert('Product added successfully');
                    $('#addProductForm')[0].reset();
                    loadProducts();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('Error adding product');
            }
        });
    });
    
    // Search products
    $('#productSearch').on('input', function() {
        const searchTerm = $(this).val().toLowerCase();
        $('#productsTableBody tr').each(function() {
            const rowText = $(this).text().toLowerCase();
            $(this).toggle(rowText.includes(searchTerm));
        });
    });
});

function loadProducts() {
    $.ajax({
        url: 'includes/actions.php',
        type: 'GET',
        data: { action: 'get_products' },
        success: function(response) {
            if (response.success) {
                const products = response.data;
                let html = '';
                
                if (products.length === 0) {
                    html = '<tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">No products found</td></tr>';
                } else {
                    products.forEach(product => {
                        html += `
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">${product.id}</td>
                                <td class="px-6 py-4 whitespace-nowrap">${product.name}</td>
                                <td class="px-6 py-4 whitespace-nowrap">₹${parseFloat(product.purchase_price).toFixed(2)}</td>
                                <td class="px-6 py-4 whitespace-nowrap">₹${parseFloat(product.sell_price).toFixed(2)}</td>
                                <td class="px-6 py-4 whitespace-nowrap">${product.quantity}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <button onclick="editProduct(${product.id})" class="text-blue-600 hover:text-blue-900 mr-3">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="deleteProduct(${product.id})" class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                }
                
                $('#productsTableBody').html(html);
            } else {
                alert('Error loading products: ' + response.message);
            }
        },
        error: function() {
            alert('Error loading products');
        }
    });
}

function editProduct(productId) {
    $.ajax({
        url: 'includes/actions.php',
        type: 'GET',
        data: { action: 'get_product', id: productId },
        success: function(response) {
            if (response.success) {
                const product = response.data;
                
                $('#editProductId').val(product.id);
                $('#editProductName').val(product.name);
                $('#editPurchasePrice').val(product.purchase_price);
                $('#editSellPrice').val(product.sell_price);
                $('#editQuantity').val(product.quantity);
                
                $('#editProductModal').removeClass('hidden');
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function() {
            alert('Error loading product details');
        }
    });
    
    // Close modal handler
    $('#closeEditModal, #cancelEdit').click(function() {
        $('#editProductModal').addClass('hidden');
    });
    
    // Edit form submission
    $('#editProductForm').off('submit').submit(function(e) {
        e.preventDefault();
        
        const productId = $('#editProductId').val();
        const productName = $('#editProductName').val();
        const purchasePrice = $('#editPurchasePrice').val();
        const sellPrice = $('#editSellPrice').val();
        const quantity = $('#editQuantity').val();
        
        $.ajax({
            url: 'includes/actions.php',
            type: 'POST',
            data: {
                action: 'update_product',
                id: productId,
                name: productName,
                purchase_price: purchasePrice,
                sell_price: sellPrice,
                quantity: quantity
            },
            success: function(response) {
                if (response.success) {
                    alert('Product updated successfully');
                    $('#editProductModal').addClass('hidden');
                    loadProducts();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('Error updating product');
            }
        });
    });
}

function deleteProduct(productId) {
    if (confirm('Are you sure you want to delete this product?')) {
        $.ajax({
            url: 'includes/actions.php',
            type: 'POST',
            data: { action: 'delete_product', id: productId },
            success: function(response) {
                if (response.success) {
                    alert('Product deleted successfully');
                    loadProducts();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('Error deleting product');
            }
        });
    }
}