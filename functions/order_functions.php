<?php

// Fungsi untuk mengambil detail pesanan berdasarkan ID
function getOrderById($conn, $id) {
    $query = "SELECT o.*, f.name AS food_name 
              FROM orders o 
              JOIN food_items f ON o.food_id = f.id 
              WHERE o.id = :id";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Fungsi untuk membuat pesanan baru
function createOrder($conn, $data) {
    $query = "INSERT INTO orders (food_id, customer_name, quantity, pickup_time, notes, status, created_at) 
              VALUES (:food_id, :customer_name, :quantity, :pickup_time, :notes, :status, NOW())";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':food_id', $data['food_id'], PDO::PARAM_INT);
    $stmt->bindParam(':customer_name', $data['customer_name']);
    $stmt->bindParam(':quantity', $data['quantity'], PDO::PARAM_INT);
    $stmt->bindParam(':pickup_time', $data['pickup_time']);
    $stmt->bindParam(':notes', $data['notes']);
    $stmt->bindParam(':status', $data['status']);
    
    if ($stmt->execute()) {
        return $conn->lastInsertId();
    }
    
    return false;
}

// Fungsi untuk memperbarui status pesanan
function updateOrderStatus($conn, $id, $status) {
    $query = "UPDATE orders SET status = :status, updated_at = NOW() WHERE id = :id";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    
    return $stmt->execute();
}
