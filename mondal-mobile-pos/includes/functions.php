<?php
function generateInvoiceNumber($conn) {
    $prefix = "MM";
    $date = date("Ymd");
    $random = mt_rand(1000, 9999);
    
    // Check if this invoice number exists
    $invoice_number = $prefix . $date . $random;
    $check_sql = "SELECT id FROM bills WHERE invoice_number = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("s", $invoice_number);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        // If exists, generate a new one
        return generateInvoiceNumber($conn);
    }
    
    return $invoice_number;
}

function getTodaysSales($conn) {
    $today = date('Y-m-d');
    $sql = "SELECT SUM(total_amount) as total_sales FROM bills WHERE DATE(created_at) = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $today);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['total_sales'] ?? 0;
}

function getTodaysProfit($conn) {
    $today = date('Y-m-d');
    $sql = "SELECT SUM(profit) as total_profit FROM bills WHERE DATE(created_at) = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $today);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['total_profit'] ?? 0;
}

function getMonthlySales($conn) {
    $current_month = date('Y-m');
    $sql = "SELECT SUM(total_amount) as total_sales FROM bills WHERE DATE_FORMAT(created_at, '%Y-%m') = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $current_month);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['total_sales'] ?? 0;
}

function getRecentSales($conn, $limit = 5) {
    $sql = "SELECT b.invoice_number, b.total_amount, b.created_at, 
                   COUNT(bi.id) as item_count 
            FROM bills b
            LEFT JOIN bill_items bi ON b.id = bi.bill_id
            GROUP BY b.id
            ORDER BY b.created_at DESC
            LIMIT ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}
?><!-- Placeholder for functions.php -->
