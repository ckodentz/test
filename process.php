<?php
// Set up database connection
$host = 'localhost';
$username = 'root';
$password = 'root';
$dbname = 'inventory_system';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

// Fetch data from the database
$sql = 'SELECT * FROM products';
$result = $conn->query($sql);

$data = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = array(
            'name' => $row['name'],
            'unit' => $row['unit'],
            'price' => $row['price'],
            'expiry' => $row['expiry_date'],
            'inventory' => $row['inventory']
        );
    }
}

// Return data as JSON
header('Content-Type: application/json');
echo json_encode($data);

$conn->close();
?>