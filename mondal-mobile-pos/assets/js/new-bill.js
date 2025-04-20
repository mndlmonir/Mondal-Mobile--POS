<!-- Placeholder for new-bill.js -->
document.addEventListener('DOMContentLoaded', function() {
    // Load products for dropdown
    loadProductsForBill();
    
    // Product selection change
    $('#productSelect').change(function() {
        const productId = $(this).val();
        if (productId) {
            getProductDetails(productId);
        } else {
            $('#selectedProductDetails').addClass('hidden');
        }
    });
    
    // Add to bill button click
    $('#addToBillBtn').click(function() {
        addToBill();
    });
    
    // Discount input change
    $('#discount').on('input', function() {
        updateBillSummary();
    });
    
    // Print bill button
    $('#printBillBtn').click(function() {
        printBill();
    });
    
    // Save bill button
    $('#saveBillBtn').click(function() {
        saveBill();
    });
    
    // Cancel bill button
    $('#cancelBillBtn').click(function() {
        if (confirm('Are you sure you want to cancel this bill?')) {
            resetBill();
        }
    });
});

function loadProductsForBill() {
    $.ajax({
        url: 'includes/actions.php',
        type: 'GET',
        data: { action: 'get_products_for_bill' },
        success: function(response) {
            if (response.success) {
                const products = response.data;
                let options = '<option value="">-- Select a product --</option>';
                
                products.forEach(product => {
                    options += `<option value="${product.id}">${product.name} (₹${parseFloat(product.sell_price).toFixed(2)}) - Qty: ${product.quantity}</option>`;
                });
                
                $('#productSelect').html(options);
            } else {
                alert('Error loading products: ' + response.message);
            }
        },
        error: function() {
            alert('Error loading products');
        }
    });
}

function getProductDetails(productId) {
    $.ajax({
        url: 'includes/actions.php',
        type: 'GET',
        data: { action: 'get_product', id: productId },
        success: function(response) {
            if (response.success) {
                const product = response.data;
                
                $('#selectedProductId').val(product.id);
                $('#selectedProductName').val(product.name);
                $('#selectedProductPurchasePrice').val(product.purchase_price);
                $('#productPrice').val(product.sell_price);
                
                // Set max quantity to available quantity
                $('#productQuantity').attr('max', product.quantity);
                
                $('#selectedProductDetails').removeClass('hidden');
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function() {
            alert('Error loading product details');
        }
    });
}

function addToBill() {
    const productId = $('#selectedProductId').val();
    const productName = $('#selectedProductName').val();
    const price = parseFloat($('#productPrice').val());
    const quantity = parseInt($('#productQuantity').val());
    const purchasePrice = parseFloat($('#selectedProductPurchasePrice').val());
    
    if (!productId || !price || !quantity) {
        alert('Please select a product and enter valid quantity');
        return;
    }
    
    const totalPrice = price * quantity;
    const profit = (price - purchasePrice) * quantity;
    
    // Check if product already exists in bill
    let existingRow = $(`#billItemsTable tr[data-product-id="${productId}"]`);
    
    if (existingRow.length > 0) {
        // Update existing row
        const existingQty = parseInt(existingRow.find('.product-qty').text());
        const newQty = existingQty + quantity;
        
        existingRow.find('.product-qty').text(newQty);
        existingRow.find('.product-total').text('₹' + (price * newQty).toFixed(2));
    } else {
        // Add new row
        const row = `
            <tr data-product-id="${productId}" data-unit-price="${price}" data-purchase-price="${purchasePrice}">
                <td class="px-6 py-4 whitespace-nowrap">${productName}</td>
                <td class="px-6 py-4 whitespace-nowrap">₹${price.toFixed(2)}</td>
                <td class="px-6 py-4 whitespace-nowrap product-qty">${quantity}</td>
                <td class="px-6 py-4 whitespace-nowrap product-total">₹${totalPrice.toFixed(2)}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <button onclick="removeBillItem(${productId})" class="text-red-600 hover:text-red-900">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        
        $('#billItemsTable').append(row);
    }
    
    // Reset product selection
    $('#productSelect').val('');
    $('#selectedProductDetails').addClass('hidden');
    
    // Update bill summary
    updateBillSummary();
}

function removeBillItem(productId) {
    $(`#billItemsTable tr[data-product-id="${productId}"]`).remove();
    updateBillSummary();
}

function updateBillSummary() {
    let subtotal = 0;
    let totalProfit = 0;
    
    $('#billItemsTable tr').each(function() {
        const unitPrice = parseFloat($(this).attr('data-unit-price'));
        const purchasePrice = parseFloat($(this).attr('data-purchase-price'));
        const quantity = parseInt($(this).find('.product-qty').text());
        
        subtotal += unitPrice * quantity;
        totalProfit += (unitPrice - purchasePrice) * quantity;
    });
    
    const discount = parseFloat($('#discount').val()) || 0;
    const grandTotal = subtotal - discount;
    
    // Update profit if discount is applied
    if (discount > 0) {
        totalProfit = Math.max(0, totalProfit - discount);
    }
    
    $('#subtotal').text('₹' + subtotal.toFixed(2));
    $('#discountAmount').text('₹' + discount.toFixed(2));
    $('#grandTotal').text('₹' + grandTotal.toFixed(2));
    
    // Store profit in a hidden field for saving
    $('#totalProfit').val(totalProfit.toFixed(2));
}

function printBill() {
    if ($('#billItemsTable tr').length === 0) {
        alert('No items in the bill to print');
        return;
    }
    
    const customerName = $('#customerName').val() || 'Walk-in Customer';
    const customerPhone = $('#customerPhone').val() || '-';
    const discount = parseFloat($('#discount').val()) || 0;
    const grandTotal = parseFloat($('#grandTotal').text().replace('₹', ''));
    const subtotal = parseFloat($('#subtotal').text().replace('₹', ''));
    
    // Generate invoice number and date
    const invoiceNumber = 'MM' + Date.now().toString().slice(-6);
    const invoiceDate = new Date().toLocaleString('en-IN', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
    
    // Update print template
    $('#printInvoiceNumber').text(invoiceNumber);
    $('#printInvoiceDate').text(invoiceDate);
    $('#printCustomerName').text(customerName);
    $('#printCustomerPhone').text(customerPhone);
    $('#printSubtotal').text('₹' + subtotal.toFixed(2));
    $('#printDiscount').text('₹' + discount.toFixed(2));
    $('#printGrandTotal').text('₹' + grandTotal.toFixed(2));
    
    // Add bill items to print template
    let itemsHtml = '';
    $('#billItemsTable tr').each(function() {
        const productName = $(this).find('td:eq(0)').text();
        const price = $(this).find('td:eq(1)').text().replace('₹', '');
        const quantity = $(this).find('.product-qty').text();
        const total = $(this).find('.product-total').text().replace('₹', '');
        
        itemsHtml += `
            <tr>
                <td class="py-1">${productName}</td>
                <td class="text-right py-1">${price}</td>
                <td class="text-right py-1">${quantity}</td>
                <td class="text-right py-1">${total}</td>
            </tr>
        `;
    });
    
    $('#printBillItems').html(itemsHtml);
    
    // Trigger print
    const printContent = $('#printContent').html();
    const originalContent = $('body').html();
    
    $('body').html(printContent);
    window.print();
    $('body').html(originalContent);
    
    // After printing, save the bill if not already saved
    if (!$('#billSaved').val()) {
        saveBill();
    }
}

function saveBill() {
    if ($('#billItemsTable tr').length === 0) {
        alert('No items in the bill to save');
        return;
    }
    
    const customerName = $('#customerName').val() || 'Walk-in Customer';
    const customerPhone = $('#customerPhone').val() || '';
    const discount = parseFloat($('#discount').val()) || 0;
    const grandTotal = parseFloat($('#grandTotal').text().replace('₹', ''));
    const profit = parseFloat($('#totalProfit').val()) || 0;
    
    // Collect bill items
    let billItems = [];
    $('#billItemsTable tr').each(function() {
        const productId = $(this).attr('data-product-id');
        const productName = $(this).find('td:eq(0)').text();
        const unitPrice = parseFloat($(this).attr('data-unit-price'));
        const purchasePrice = parseFloat($(this).attr('data-purchase-price'));
        const quantity = parseInt($(this).find('.product-qty').text());
        const totalPrice = unitPrice * quantity;
        const itemProfit = (unitPrice - purchasePrice) * quantity;
        
        billItems.push({
            product_id: productId,
            product_name: productName,
            quantity: quantity,
            unit_price: unitPrice,
            total_price: totalPrice,
            purchase_price: purchasePrice,
            profit: itemProfit
        });
    });
    
    $.ajax({
        url: 'includes/actions.php',
        type: 'POST',
        data: {
            action: 'save_bill',
            customer_name: customerName,
            customer_phone: customerPhone,
            discount: discount,
            total_amount: grandTotal,
            profit: profit,
            items: JSON.stringify(billItems)
        },
        success: function(response) {
            if (response.success) {
                alert('Bill saved successfully');
                resetBill();
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function() {
            alert('Error saving bill');
        }
    });
}

function resetBill() {
    $('#billItemsTable').empty();
    $('#customerName').val('');
    $('#customerPhone').val('');
    $('#discount').val('0');
    $('#subtotal').text('₹0.00');
    $('#discountAmount').text('₹0.00');
    $('#grandTotal').text('₹0.00');
    $('#productSelect').val('');
    $('#selectedProductDetails').addClass('hidden');
    $('#billSaved').val('0');
}