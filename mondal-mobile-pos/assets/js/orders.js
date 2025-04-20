document.addEventListener('DOMContentLoaded', function() {
    // Load orders
    loadOrders();
    
    // Search orders
    $('#orderSearch').on('input', function() {
        const searchTerm = $(this).val().toLowerCase();
        $('#ordersTable tr').each(function() {
            const rowText = $(this).text().toLowerCase();
            $(this).toggle(rowText.includes(searchTerm));
        });
    });
    
    // Create order form submission
    $('#createOrderForm').submit(function(e) {
        e.preventDefault();
        
        const customerName = $('#orderCustomerName').val();
        const customerPhone = $('#orderCustomerPhone').val();
        const productDetails = $('#orderProductDetails').val();
        const status = $('#orderStatus').val();
        
        if (!customerName || !customerPhone || !productDetails) {
            alert('Please fill all required fields');
            return;
        }
        
        $.ajax({
            url: 'includes/actions.php',
            type: 'POST',
            data: {
                action: 'create_order',
                customer_name: customerName,
                customer_phone: customerPhone,
                product_details: productDetails,
                status: status
            },
            success: function(response) {
                if (response.success) {
                    alert('Order created successfully');
                    $('#createOrderForm')[0].reset();
                    loadOrders();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('Error creating order');
            }
        });
    });
});

function loadOrders() {
    $.ajax({
        url: 'includes/actions.php',
        type: 'GET',
        data: { action: 'get_orders' },
        success: function(response) {
            if (response.success) {
                const orders = response.data;
                let html = '';
                
                if (orders.length === 0) {
                    html = '<tr><td colspan="7" class="px-6 py-4 text-center text-gray-500">No orders found</td></tr>';
                } else {
                    orders.forEach(order => {
                        const date = new Date(order.created_at).toLocaleString('en-IN', {
                            day: '2-digit',
                            month: 'short',
                            year: 'numeric'
                        });
                        
                        const statusClass = order.status === 'delivered' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800';
                        
                        html += `
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">${order.id}</td>
                                <td class="px-6 py-4 whitespace-nowrap">${order.customer_name}</td>
                                <td class="px-6 py-4 whitespace-nowrap">${order.customer_phone}</td>
                                <td class="px-6 py-4">${order.product_details}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs rounded-full ${statusClass}">${order.status.charAt(0).toUpperCase() + order.status.slice(1)}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">${date}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <button onclick="editOrder(${order.id})" class="text-blue-600 hover:text-blue-900 mr-3">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="deleteOrder(${order.id})" class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                }
                
                $('#ordersTable').html(html);
            } else {
                alert('Error loading orders: ' + response.message);
            }
        },
        error: function() {
            alert('Error loading orders');
        }
    });
}

function editOrder(orderId) {
    $.ajax({
        url: 'includes/actions.php',
        type: 'GET',
        data: { action: 'get_order', id: orderId },
        success: function(response) {
            if (response.success) {
                const order = response.data;
                
                $('#editOrderId').val(order.id);
                $('#editOrderCustomerName').val(order.customer_name);
                $('#editOrderCustomerPhone').val(order.customer_phone);
                $('#editOrderProductDetails').val(order.product_details);
                $('#editOrderStatus').val(order.status);
                
                $('#editOrderModal').removeClass('hidden');
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function() {
            alert('Error loading order details');
        }
    });
    
    // Close modal handler
    $('#closeEditOrderModal, #cancelEditOrder').click(function() {
        $('#editOrderModal').addClass('hidden');
    });
    
    // Edit form submission
    $('#editOrderForm').off('submit').submit(function(e) {
        e.preventDefault();
        
        const orderId = $('#editOrderId').val();
        const customerName = $('#editOrderCustomerName').val();
        const customerPhone = $('#editOrderCustomerPhone').val();
        const productDetails = $('#editOrderProductDetails').val();
        const status = $('#editOrderStatus').val();
        
        $.ajax({
            url: 'includes/actions.php',
            type: 'POST',
            data: {
                action: 'update_order',
                id: orderId,
                customer_name: customerName,
                customer_phone: customerPhone,
                product_details: productDetails,
                status: status
            },
            success: function(response) {
                if (response.success) {
                    alert('Order updated successfully');
                    $('#editOrderModal').addClass('hidden');
                    loadOrders();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('Error updating order');
            }
        });
    });
}

function deleteOrder(orderId) {
    if (confirm('Are you sure you want to delete this order?')) {
        $.ajax({
            url: 'includes/actions.php',
            type: 'POST',
            data: { action: 'delete_order', id: orderId },
            success: function(response) {
                if (response.success) {
                    alert('Order deleted successfully');
                    loadOrders();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('Error deleting order');
            }
        });
    }
}