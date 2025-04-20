<?php
require_once 'config.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    // Products
    case 'get_products':
        getProducts($conn);
        break;
    case 'get_product':
        getProduct($conn);
        break;
    case 'add_product':
        addProduct($conn);
        break;
    case 'update_product':
        updateProduct($conn);
        break;
    case 'delete_product':
        deleteProduct($conn);
        break;
    case 'get_products_for_bill':
        getProductsForBill($conn);
        break;
        
    // Bills
    case 'save_bill':
        saveBill($conn);
        break;
    case 'get_saved_bills':
        getSavedBills($conn);
        break;
    case 'get_bill_details':
        getBillDetails($conn);
        break;
    case 'delete_bill':
        deleteBill($conn);
        break;
        
    // Sales
    case 'get_sales_report':
        getSalesReport($conn);
        break;
        
    // Orders
    case 'get_orders':
        getOrders($conn);
        break;
    case 'get_order':
        getOrder($conn);
        break;
    case 'create_order':
        createOrder($conn);
        break;
    case 'update_order':
        updateOrder($conn);
        break;
    case 'delete_order':
        deleteOrder($conn);
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}

// Product functions
function getProducts($conn) {
    $sql = "SELECT * FROM products ORDER BY name";
    $result = $conn->query($sql);
    
    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    
    echo json_encode(['success' => true, 'data' => $products]);
}

function getProduct($conn) {
    $id = $_GET['id'];
    
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        echo json_encode(['success' => true, 'data' => $product]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Product not found']);
    }
}

function addProduct($conn) {
    $name = $_POST['name'];
    $purchasePrice = $_POST['purchase_price'];
    $sellPrice = $_POST['sell_price'];
    $quantity = $_POST['quantity'];
    
    $stmt = $conn->prepare("INSERT INTO products (name, purchase_price, sell_price, quantity) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sddi", $name, $purchasePrice, $sellPrice, $quantity);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => $conn->error]);
    }
}

function updateProduct($conn) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $purchasePrice = $_POST['purchase_price'];
    $sellPrice = $_POST['sell_price'];
    $quantity = $_POST['quantity'];
    
    $stmt = $conn->prepare("UPDATE products SET name = ?, purchase_price = ?, sell_price = ?, quantity = ? WHERE id = ?");
    $stmt->bind_param("sddii", $name, $purchasePrice, $sellPrice, $quantity, $id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => $conn->error]);
    }
}

function deleteProduct($conn) {
    $id = $_POST['id'];
    
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => $conn->error]);
    }
}

function getProductsForBill($conn) {
    $sql = "SELECT id, name, sell_price, quantity FROM products WHERE quantity > 0 ORDER BY name";
    $result = $conn->query($sql);
    
    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    
    echo json_encode(['success' => true, 'data' => $products]);
}

// Bill functions
function saveBill($conn) {
    $customerName = $_POST['customer_name'];
    $customerPhone = $_POST['customer_phone'];
    $discount = $_POST['discount'];
    $totalAmount = $_POST['total_amount'];
    $profit = $_POST['profit'];
    $items = json_decode($_POST['items'], true);
    
    // Generate invoice number
    $invoiceNumber = generateInvoiceNumber($conn);
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Insert bill
        $stmt = $conn->prepare("INSERT INTO bills (invoice_number, customer_name, customer_phone, discount, total_amount, profit) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssddd", $invoiceNumber, $customerName, $customerPhone, $discount, $totalAmount, $profit);
        $stmt->execute();
        $billId = $conn->insert_id;
        
        // Insert bill items and update product quantities
        foreach ($items as $item) {
            // Insert bill item
            $stmt = $conn->prepare("INSERT INTO bill_items (bill_id, product_id, product_name, quantity, unit_price, total_price, purchase_price, profit) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("iisidddd", $billId, $item['product_id'], $item['product_name'], $item['quantity'], $item['unit_price'], $item['total_price'], $item['purchase_price'], $item['profit']);
            $stmt->execute();
            
            // Update product quantity
            $stmt = $conn->prepare("UPDATE products SET quantity = quantity - ? WHERE id = ?");
            $stmt->bind_param("ii", $item['quantity'], $item['product_id']);
            $stmt->execute();
        }
        
        $conn->commit();
        echo json_encode(['success' => true, 'invoice_number' => $invoiceNumber]);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function getSavedBills($conn) {
    $sql = "SELECT * FROM bills WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) ORDER BY created_at DESC";
    $result = $conn->query($sql);
    
    $bills = [];
    while ($row = $result->fetch_assoc()) {
        $bills[] = $row;
    }
    
    echo json_encode(['success' => true, 'data' => $bills]);
}

function getBillDetails($conn) {
    $invoiceNumber = $_GET['invoice_number'];
    
    // Get bill
    $stmt = $conn->prepare("SELECT * FROM bills WHERE invoice_number = ?");
    $stmt->bind_param("s", $invoiceNumber);
    $stmt->execute();
    $billResult = $stmt->get_result();
    
    if ($billResult->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Bill not found']);
        return;
    }
    
    $bill = $billResult->fetch_assoc();
    
    // Get bill items
    $stmt = $conn->prepare("SELECT * FROM bill_items WHERE bill_id = ?");
    $stmt->bind_param("i", $bill['id']);
    $stmt->execute();
    $itemsResult = $stmt->get_result();
    
    $items = [];
    while ($row = $itemsResult->fetch_assoc()) {
        $items[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'bill' => $bill,
        'items' => $items
    ]);
}

function deleteBill($conn) {
    $id = $_POST['id'];
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // First, get all items to restore quantities
        $stmt = $conn->prepare("SELECT product_id, quantity FROM bill_items WHERE bill_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            // Restore product quantity
            $updateStmt = $conn->prepare("UPDATE products SET quantity = quantity + ? WHERE id = ?");
            $updateStmt->bind_param("ii", $row['quantity'], $row['product_id']);
            $updateStmt->execute();
        }
        
        // Now delete the bill (cascade will delete items)
        $stmt = $conn->prepare("DELETE FROM bills WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        $conn->commit();
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

// Sales functions
function getSalesReport($conn) {
    $dateFrom = $_GET['date_from'];
    $dateTo = $_GET['date_to'];
    
    $stmt = $conn->prepare("
        SELECT 
            b.id, 
            b.invoice_number, 
            b.customer_name, 
            b.customer_phone, 
            b.discount, 
            b.total_amount, 
            b.profit, 
            b.created_at,
            SUM(bi.purchase_price * bi.quantity) as purchase_total
        FROM 
            bills b
        JOIN 
            bill_items bi ON b.id = bi.bill_id
        WHERE 
            DATE(b.created_at) BETWEEN ? AND ?
        GROUP BY 
            b.id
        ORDER BY 
            b.created_at DESC
    ");
    $stmt->bind_param("ss", $dateFrom, $dateTo);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $sales = [];
    while ($row = $result->fetch_assoc()) {
        $sales[] = $row;
    }
    
    echo json_encode(['success' => true, 'data' => $sales]);
}

// Order functions
function getOrders($conn) {
    $sql = "SELECT * FROM orders ORDER BY created_at DESC";
    $result = $conn->query($sql);
    
    $orders = [];
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
    
    echo json_encode(['success' => true, 'data' => $orders]);
}

function getOrder($conn) {
    $id = $_GET['id'];
    
    $stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $order = $result->fetch_assoc();
        echo json_encode(['success' => true, 'data' => $order]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Order not found']);
    }
}

function createOrder($conn) {
    $customerName = $_POST['customer_name'];
    $customerPhone = $_POST['customer_phone'];
    $productDetails = $_POST['product_details'];
    $status = $_POST['status'];
    
    $stmt = $conn->prepare("INSERT INTO orders (customer_name, customer_phone, product_details, status) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $customerName, $customerPhone, $productDetails, $status);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => $conn->error]);
    }
}

function updateOrder($conn) {
    $id = $_POST['id'];
    $customerName = $_POST['customer_name'];
    $customerPhone = $_POST['customer_phone'];
    $productDetails = $_POST['product_details'];
    $status = $_POST['status'];
    
    $stmt = $conn->prepare("UPDATE orders SET customer_name = ?, customer_phone = ?, product_details = ?, status = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $customerName, $customerPhone, $productDetails, $status, $id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => $conn->error]);
    }
}

function deleteOrder($conn) {
    $id = $_POST['id'];
    
    $stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => $conn->error]);
    }
}

$conn->close();
?>