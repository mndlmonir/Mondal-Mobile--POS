<?php
session_start();
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mondal Mobile-POS | <?php echo $pageTitle ?? ''; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="assets/js/main.js"></script>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="w-64 bg-blue-800 text-white shadow-lg">
            <div class="p-4 border-b border-blue-700">
                <h1 class="text-2xl font-bold">Mondal Mobile-POS</h1>
                <p class="text-sm text-blue-200">Accessories Management</p>
            </div>
            <nav class="mt-4">
                <a href="dashboard.php" class="block py-2 px-4 hover:bg-blue-700 <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'bg-blue-700' : ''; ?>">
                    <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                </a>
                <a href="products.php" class="block py-2 px-4 hover:bg-blue-700 <?php echo basename($_SERVER['PHP_SELF']) == 'products.php' ? 'bg-blue-700' : ''; ?>">
                    <i class="fas fa-boxes mr-2"></i> Products
                </a>
                <a href="new-bill.php" class="block py-2 px-4 hover:bg-blue-700 <?php echo basename($_SERVER['PHP_SELF']) == 'new-bill.php' ? 'bg-blue-700' : ''; ?>">
                    <i class="fas fa-receipt mr-2"></i> New Bill
                </a>
                <a href="saved-bills.php" class="block py-2 px-4 hover:bg-blue-700 <?php echo basename($_SERVER['PHP_SELF']) == 'saved-bills.php' ? 'bg-blue-700' : ''; ?>">
                    <i class="fas fa-save mr-2"></i> Saved Bills
                </a>
                <a href="sales.php" class="block py-2 px-4 hover:bg-blue-700 <?php echo basename($_SERVER['PHP_SELF']) == 'sales.php' ? 'bg-blue-700' : ''; ?>">
                    <i class="fas fa-chart-line mr-2"></i> Sales
                </a>
                <a href="orders.php" class="block py-2 px-4 hover:bg-blue-700 <?php echo basename($_SERVER['PHP_SELF']) == 'orders.php' ? 'bg-blue-700' : ''; ?>">
                    <i class="fas fa-clipboard-list mr-2"></i> Orders
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 overflow-auto">
            <header class="bg-white shadow-sm">
                <div class="flex justify-between items-center p-4">
                    <h2 class="text-xl font-semibold text-gray-800"><?php echo $pageTitle ?? ''; ?></h2>
                    <div class="flex items-center space-x-4">
                        <span class="text-gray-600"><?php echo date('d M Y, h:i A'); ?></span>
                        <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white">
                            <i class="fas fa-user"></i>
                        </div>
                    </div>
                </div>
            </header>
            <main class="p-4"><!-- Placeholder for header.php -->
