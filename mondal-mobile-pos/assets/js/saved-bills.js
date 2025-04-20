document.addEventListener('DOMContentLoaded', function() {
    // Load saved bills
    loadSavedBills();
    
    // Search bills
    $('#billSearch').on('input', function() {
        const searchTerm = $(this).val().toLowerCase();
        $('#savedBillsTable tr').each(function() {
            const rowText = $(this).text().toLowerCase();
            $(this).toggle(rowText.includes(searchTerm));
        });
    });
});

function loadSavedBills() {
    $.ajax({
        url: 'includes/actions.php',
        type: 'GET',
        data: { action: 'get_saved_bills' },
        success: function(response) {
            if (response.success) {
                const bills = response.data;
                let html = '';
                
                if (bills.length === 0) {
                    html = '<tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">No saved bills found</td></tr>';
                } else {
                    bills.forEach(bill => {
                        const date = new Date(bill.created_at).toLocaleString('en-IN', {
                            day: '2-digit',
                            month: 'short',
                            year: 'numeric'
                        });
                        
                        html += `
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">${bill.invoice_number}</td>
                                <td class="px-6 py-4 whitespace-nowrap">${date}</td>
                                <td class="px-6 py-4 whitespace-nowrap">${bill.customer_name || 'Walk-in'}</td>
                                <td class="px-6 py-4 whitespace-nowrap">${bill.customer_phone || '-'}</td>
                                <td class="px-6 py-4 whitespace-nowrap">₹${parseFloat(bill.total_amount).toFixed(2)}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <button onclick="printSavedBill('${bill.invoice_number}')" class="text-blue-600 hover:text-blue-900 mr-3">
                                        <i class="fas fa-print"></i>
                                    </button>
                                    <button onclick="deleteBill(${bill.id})" class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                }
                
                $('#savedBillsTable').html(html);
            } else {
                alert('Error loading saved bills: ' + response.message);
            }
        },
        error: function() {
            alert('Error loading saved bills');
        }
    });
}

function printSavedBill(invoiceNumber) {
    $.ajax({
        url: 'includes/actions.php',
        type: 'GET',
        data: { action: 'get_bill_details', invoice_number: invoiceNumber },
        success: function(response) {
            if (response.success) {
                const bill = response.bill;
                const items = response.items;
                
                // Update print template
                $('#printInvoiceNumber').text(bill.invoice_number);
                $('#printInvoiceDate').text(new Date(bill.created_at).toLocaleString('en-IN', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                }));
                $('#printCustomerName').text(bill.customer_name || 'Walk-in Customer');
                $('#printCustomerPhone').text(bill.customer_phone || '-');
                $('#printSubtotal').text('₹' + (parseFloat(bill.total_amount) + parseFloat(bill.discount)).toFixed(2));
                $('#printDiscount').text('₹' + parseFloat(bill.discount).toFixed(2));
                $('#printGrandTotal').text('₹' + parseFloat(bill.total_amount).toFixed(2));
                
                // Add bill items to print template
                let itemsHtml = '';
                items.forEach(item => {
                    itemsHtml += `
                        <tr>
                            <td class="py-1">${item.product_name}</td>
                            <td class="text-right py-1">${parseFloat(item.unit_price).toFixed(2)}</td>
                            <td class="text-right py-1">${item.quantity}</td>
                            <td class="text-right py-1">${parseFloat(item.total_price).toFixed(2)}</td>
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
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function() {
            alert('Error loading bill details');
        }
    });
}

function deleteBill(billId) {
    if (confirm('Are you sure you want to delete this bill?')) {
        $.ajax({
            url: 'includes/actions.php',
            type: 'POST',
            data: { action: 'delete_bill', id: billId },
            success: function(response) {
                if (response.success) {
                    alert('Bill deleted successfully');
                    loadSavedBills();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('Error deleting bill');
            }
        });
    }
}