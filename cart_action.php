<?php
session_start();
require 'config.php';

// --- 1. ADD TO CART ---
if (isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

    if (!isset($_SESSION['cart'])) { $_SESSION['cart'] = []; }

    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] += $quantity;
    } else {
        $_SESSION['cart'][$product_id] = $quantity;
    }
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
}

// --- 2. BUY NOW ---
if (isset($_POST['buy_now'])) {
    $product_id = $_POST['product_id'];
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1; // Check if quantity was posted
    
    if (!isset($_SESSION['cart'])) { $_SESSION['cart'] = []; }
    
    $_SESSION['cart'][$product_id] = $quantity;
    header("Location: checkout.php");
    exit;
}

// --- 3. INCREASE QUANTITY ---
if (isset($_GET['action']) && $_GET['action'] == 'increase') {
    $id = $_GET['id'];
    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]++;
    }
    header("Location: cart.php");
    exit;
}

// --- 4. DECREASE QUANTITY ---
if (isset($_GET['action']) && $_GET['action'] == 'decrease') {
    $id = $_GET['id'];
    if (isset($_SESSION['cart'][$id])) {
        if ($_SESSION['cart'][$id] > 1) {
            $_SESSION['cart'][$id]--;
        }
    }
    header("Location: cart.php");
    exit;
}

// --- 5. REMOVE ITEM ---
if (isset($_GET['remove'])) {
    $id = $_GET['remove'];
    unset($_SESSION['cart'][$id]);
    header("Location: cart.php");
    exit;
}
?>