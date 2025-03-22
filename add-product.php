<?php
session_start();
include('auth_check.php');
include('db_connection.php'); // Ensure database connection is included

// Ensure user is logged in and is an admin
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch unique categories from tb_inventory
$categories = [];
$result = $conn->query("SELECT DISTINCT category FROM tb_inventory ORDER BY category ASC");

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row['category'];
    }
} else {
    die("Error fetching categories: " . $conn->error);
}

// Fetch unique main categories from tb_inventory
$main_categories = [];
$result = $conn->query("SELECT DISTINCT main_category FROM tb_inventory ORDER BY main_category ASC");

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $main_categories[] = $row['main_category'];
    }
} else {
    die("Error fetching main categories: " . $conn->error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_name = $_POST['product_name'];
    $category = $_POST['category'];
    $main_category = $_POST['main_category'];
    $size = $_POST['size'];
    $quantity = $_POST['quantity'];

    // Insert new product into inventory
    $stmt = $conn->prepare("INSERT INTO tb_inventory (name, category, main_category, size, quantity) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $product_name, $category, $main_category, $size, $quantity);

    if ($stmt->execute()) {
        $success_message = "Product added successfully!";
    } else {
        $error_message = "Error adding product: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <link rel="icon" href="img/GSL25_transparent 2.png">
    <style>
                /* General Reset */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Poppins', sans-serif;
}

body {
    background: linear-gradient(to right, #34495e, #2c3e50);
    color: #fff;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

.container {
    background: rgba(255, 255, 255, 0.1);
    padding: 30px;
    border-radius: 10px;
    width: 400px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    text-align: center;
}

h2 {
    margin-bottom: 20px;
    font-size: 24px;
}

form {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

label {
    font-weight: bold;
    font-size: 14px;
    text-align: left;
}

input, select {
    padding: 10px;
    border: none;
    border-radius: 5px;
    font-size: 14px;
    width: 100%;
    outline: none;
}

input[type="file"] {
    background: #fff;
    padding: 8px;
}

button {
    background: #2980b9;
    color: white;
    font-size: 16px;
    font-weight: bold;
    padding: 10px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: 0.3s;
}

button:hover {
    background: #1f618d;
}

.success, .error {
    padding: 10px;
    border-radius: 5px;
    font-size: 14px;
    text-align: center;
    font-weight: bold;
}

.success {
    background: #2ecc71;
    color: white;
}

.error {
    background: #e74c3c;
    color: white;
}

a {
    display: inline-block;
    margin-top: 15px;
    text-decoration: none;
    color: #fff;
    font-weight: bold;
    transition: 0.3s;
}

a:hover {
    color: #f1c40f;
}
    </style>
</head>
<body>
    <div class="container">
        <h2>Add New Product</h2>

        <?php if (isset($success_message)): ?>
            <p class="success"><?= htmlspecialchars($success_message) ?></p>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <p class="error"><?= htmlspecialchars($error_message) ?></p>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="product_name">Product Name:</label>
            <input type="text" id="product_name" name="product_name" required>

            <label for="category">Category:</label>
            <select id="category" name="category" required>
                <option value="">Select a Category</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars($cat) ?></option>
                <?php endforeach; ?>
            </select>

            <label for="main_category">Main Category:</label>
            <select id="main_category" name="main_category" required>
                <option value="">Select a Main Category</option>
                <?php foreach ($main_categories as $main_cat): ?>
                    <option value="<?= htmlspecialchars($main_cat) ?>"><?= htmlspecialchars($main_cat) ?></option>
                <?php endforeach; ?>
            </select>

            <label for="size">Size:</label>
            <input type="text" id="size" name="size" required>

            <label for="quantity">Quantity:</label>
            <input type="number" id="quantity" name="quantity" required>

            <button type="submit">Add Product</button>
        </form>

        <a href="inventory.php">Back to Inventory</a>
    </div>
</body>
</html>
