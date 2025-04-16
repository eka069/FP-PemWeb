<?php
// Fungsi untuk mengambil semua pesanan
function getAllOrders($conn) {
try{
    $query = "SELECT o.*, f.name AS food_name 
              FROM orders o 
              JOIN food_items f ON o.food_id = f.id 
              ORDER BY o.created_at DESC";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Query error: " . $e->getMessage();
        return [];
    }
}

// Misal fungsi ada di functions/order_functions.php
function getUserOrders($conn, $userId) {
    $query = "SELECT * FROM orders WHERE user_id = :user_id";
    $stmt = $conn->prepare($query);
    $stmt->execute([':user_id' => $userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fungsi untuk mengambil detail pesanan berdasarkan ID
function getOrderById($conn, $id) {
    $id = (int)$id;
    $query = "SELECT o.*, f.name AS food_name 
              FROM orders o 
              JOIN food_items f ON o.food_id = f.id 
              WHERE o.id = $id";
    $result = mysqli_query($conn, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    
    return null;
}

// Fungsi untuk membuat pesanan baru
function createOrder($conn, $data) {
    $food_id = (int)$data['food_id'];
    $customer_name = clean_input($conn, $data['customer_name']);
    $quantity = (int)$data['quantity'];
    $pickup_time = clean_input($conn, $data['pickup_time']);
    $notes = clean_input($conn, $data['notes'] ?? '');
    $status = clean_input($conn, $data['status']);
    
    $query = "INSERT INTO orders (food_id, customer_name, quantity, pickup_time, notes, status, created_at) 
              VALUES ($food_id, '$customer_name', $quantity, '$pickup_time', '$notes', '$status', NOW())";
    
    if (mysqli_query($conn, $query)) {
        return mysqli_insert_id($conn);
    }
    
    return false;
}

// Fungsi untuk memperbarui status pesanan
function updateOrderStatus($conn, $id, $status) {
    $id = (int)$id;
    $status = clean_input($conn, $status);
    
    $query = "UPDATE orders SET status = '$status', updated_at = NOW() WHERE id = $id";
    
    return mysqli_query($conn, $query);
}
?>
