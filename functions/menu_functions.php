<?php
// Fungsi untuk mengambil semua menu makanan
function getAllFoodItems($conn) {
    try {
        $query = "SELECT f.*, 
                         c.name AS category_name, 
                         c.slug AS category_slug, 
                         s.name AS seller_name 
                  FROM food_items f 
                  JOIN categories c ON f.category_id = c.id 
                  JOIN sellers s ON f.seller_id = s.id 
                  ORDER BY f.id DESC";

        $stmt = $conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Query error: " . $e->getMessage();
        return [];
    }
}

// Fungsi untuk mengambil detail menu makanan berdasarkan ID
function getFoodItemById(PDO $conn, $id) {
    $id = (int)$id;

    try {
        $query = "SELECT f.*, 
                         c.name AS category_name, 
                         c.slug AS category_slug, 
                         s.name AS seller_name 
                  FROM food_items f 
                  JOIN categories c ON f.category_id = c.id 
                  JOIN sellers s ON f.seller_id = s.id 
                  WHERE f.id = :id";

        $stmt = $conn->prepare($query);
        $stmt->execute(['id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        echo "Query error: " . $e->getMessage();
        return [];
    }
}


// Fungsi untuk mengambil semua kategori
function getAllCategories($conn) {
    try {
        $query = "SELECT * FROM categories ORDER BY name";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        return [];
    }
}

// Fungsi untuk membuat menu makanan baru
function createFoodItem($conn, $data) {
    $name = clean_input($conn, $data['name']);
    $category_id = (int)$data['category_id'];
    $price = (int)$data['price'];
    $stock = (int)$data['stock'];
    $description = clean_input($conn, $data['description']);
    $image = clean_input($conn, $data['image']);
    $seller_id = (int)$data['seller_id'];
    
    $query = "INSERT INTO food_items (name, category_id, price, stock, description, image, seller_id, created_at) 
              VALUES ('$name', $category_id, $price, $stock, '$description', '$image', $seller_id, NOW())";
    
    if (mysqli_query($conn, $query)) {
        return mysqli_insert_id($conn);
    }
    
    return false;
}

// Fungsi untuk memperbarui menu makanan
function updateFoodItem($conn, $id, $data) {
    $id = (int)$id;
    $name = clean_input($conn, $data['name']);
    $category_id = (int)$data['category_id'];
    $price = (int)$data['price'];
    $stock = (int)$data['stock'];
    $description = clean_input($conn, $data['description']);
    $image = clean_input($conn, $data['image']);
    
    $query = "UPDATE food_items 
              SET name = '$name', 
                  category_id = $category_id, 
                  price = $price, 
                  stock = $stock, 
                  description = '$description', 
                  image = '$image', 
                  updated_at = NOW() 
              WHERE id = $id";
    
    return mysqli_query($conn, $query);
}

// Fungsi untuk menghapus menu makanan
function deleteFoodItem($conn, $id) {
    $id = (int)$id;
    $query = "DELETE FROM food_items WHERE id = $id";
    
    return mysqli_query($conn, $query);
}

// Fungsi untuk memperbarui stok makanan
function updateFoodStock($conn, $id, $stock) {
    $id = (int)$id;
    $stock = (int)$stock;
    
    $query = "UPDATE food_items SET stock = $stock, updated_at = NOW() WHERE id = $id";
    
    return mysqli_query($conn, $query);
}
?>
