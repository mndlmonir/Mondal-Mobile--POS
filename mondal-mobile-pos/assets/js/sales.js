document.addEventListener('DOMContentLoaded', function() {
    // Load sales report with default date range (today)
    const today = new Date().toISOString().split('T')[0];
    $('#dateFrom').val(today);
    $('#dateTo').val(today);
    
    loadSalesReport(today, today);
    
    // Filter button click
    $('#filterSalesBtn').click(function() {
        const dateFrom = $('#dateFrom').val();
        const dateTo = $('#dateTo').val();
        
        if (!dateFrom || !dateTo) {
            alert('Please select both date ranges');
            return;
        }
        
        loadSalesReport(dateFrom, dateTo);
    });
});

function loadSalesReport(dateFrom, dateTo) {
    $.ajax({
        url: 'includes/actions.php',
        type: 'GET',
        data: { 
            action: 'get_sales_report',
            date_from: dateFrom,
            date_to: dateTo
        },
        success: function(response) {
            if (response.success) {
                const sales = response.data;
                let html = '';
                let totalSelling = 0;
                let totalPurchase = 0;
                let totalDiscount = 0;
                let totalProfit = 0;
                
                if (sales.length === 0) {
                    html = '<tr><td colspan="7" class="px-6 py-4 text-center text-gray-500">No sales found for selected period</td></tr>';
                } else {
                    sales.forEach(sale => {
                        const dateTime = new Date(sale.created_at).toLocaleString('en-IN', {
                            day: '2-digit',
                            month: 'short',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        });
                        
                        totalSelling += parseFloat(sale.total_amount) + parseFloat(sale.discount);
                        totalPurchase += sale.purchase_total;
                        totalDiscount += parseFloat(sale.discount);
                        totalProfit += parseFloat(sale.profit);
                        
                        html += `
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">${sale.invoice_number}</td>
                                <td class="px-6 py-4 whitespace-nowrap">${dateTime}</td>
                                <td class="px-6 py-4 whitespace-nowrap">${sale.customer_name || 'Walk-in'}</td>
                                <td class="px-6 py-4 whitespace-nowrap">₹${(parseFloat(sale.total_amount) + parseFloat(sale.discount)).toFixed(2)}</td>
                                <td class="px-6 py-4 whitespace-nowrap">₹${parseFloat(sale.purchase_total).toFixed(2)}</td>
                                <td class="px-6 py-4 whitespace-nowrap">₹${parseFloat(sale.discount).toFixed(2)}</td>
                                <td class="px-6 py-4 whitespace-nowrap">₹${parseFloat(sale.profit).toFixed(2)}</td>
                            </tr>
                        `;
                    });
                }
                
                $('#salesReportTable').html(html);
                
                // Update totals
                $('#totalSellingPrice').text('₹' + totalSelling.toFixed(2));
                $('#totalPurchasePrice').text('₹' + totalPurchase.toFixed(2));
                $('#totalDiscount').text('₹' + totalDiscount.toFixed(2));
                $('#totalProfit').text('₹' + totalProfit.toFixed(2));
            } else {
                alert('Error loading sales report: ' + response.message);
            }
        },
        error: function() {
            alert('Error loading sales report');
        }
    });
}