<?php

require('new_connection.php'); // Corrected filename

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['action']) && $_GET['action'] === 'viewProducts') {
        // Fetch data from the database
        $sql = 'SELECT * FROM products';
        $data = fetch_all($sql); // Using the fetch_all function from new_connection.php

        // Modify the date format for the "expiry" field
        foreach ($data as &$product) {
            $product['expiry'] = date('M d, Y', strtotime($product['expiry']));
        }
        unset($product);

        // Return data as JSON
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    if (isset($_GET['action']) && $_GET['action'] === 'getProductDetails') {
        // Fetch data from the database
        if (isset($_GET['productId'])) {
            $productId = (int) $_GET['productId']; // Sanitize input as an integer

            $sql = "SELECT * FROM products WHERE id = $productId";
            $product = fetch_record($sql); // Using the fetch_record function from new_connection.php

            if ($product) {
                // Modify the date format for the "expiry" field
                $product['expiry'] = date('M d, Y', strtotime($product['expiry']));

                // Return data as JSON
                header('Content-Type: application/json');
                echo json_encode($product);
            } else {
                // Product not found
                header("HTTP/1.0 404 Not Found");
            }
        } else {
            // Invalid request
            header("HTTP/1.0 400 Bad Request");
        }
    }

}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'addProduct') {
        $inputs = $_POST['formData'];

        $formDataArray = array();
        foreach ($inputs as $input) {
            $formDataArray[$input['name']] = $input['value'];
        }

        $name = escape_this_string($formDataArray['name']);
        $unit = escape_this_string($formDataArray['unit']);
        $price = $formDataArray['price']; // Convert to float
        $expiry = date('Y-m-d', strtotime($formDataArray['expiry']));
        $inventory = intval($formDataArray['inventory']);


        $insertQuery = "INSERT INTO products (name, unit, price, expiry, inventory, created_at, updated_at) VALUES ('$name', '$unit', '$price', '$expiry', '$inventory', NOW(), NOW())";
        $insertedProductId = run_mysql_query($insertQuery);

        if ($insertedProductId) {
            echo "Product added successfully!";
        } else {
            echo "Failed to add product: " . $connection->error;
        }

    } else if (isset($_POST['action']) && $_POST['action'] === 'updateProduct') {
        $productId = (int) $_POST['productId']; // Sanitize input as an integer
        $updatedData = $_POST['updatedData']; // This should be sanitized and validated

        $name = escape_this_string($updatedData['name']);
        $unit = escape_this_string($updatedData['unit']);
        $price = floatval($updatedData['price']); // Convert to float
        $expiry = date('Y-m-d', strtotime($updatedData['expiry']));
        $inventory = intval($updatedData['inventory']);


        $updateQuery = "UPDATE products SET name = '$name', unit = '$unit', price = '$price', expiry = '$expiry', inventory = '$inventory', updated_at = NOW() WHERE id = $productId";

        if (run_mysql_query($updateQuery)) {
            echo "Product updated successfully!";
        } else {
            echo "Failed to update product: " . $connection->error;
        }
    } else {
        echo "Action or message not received.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    parse_str(file_get_contents("php://input"), $deleteData);

    if (isset($deleteData['action']) && $deleteData['action'] === 'deleteProduct') {
        $productId = intval($deleteData['productId']); // Sanitize input

        $deleteQuery = "DELETE FROM products WHERE id = ?";

        $stmt = $connection->prepare($deleteQuery);
        $stmt->bind_param("i", $productId);

        if ($stmt->execute()) {
            echo "Product deleted successfully!";
        } else {
            echo "Failed to delete product: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Action or message not received.";
    }
}

?>