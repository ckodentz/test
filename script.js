$(document).ready(function () {
    // Function to fetch and display data from the server
    function fetchData() {
        $.ajax({
            url: "process.php",
            type: "GET",
            dataType: "json",
            success: function (data) {
                updateTable(data);
                // Fetch data again after a certain interval (e.g., every 5 seconds)
                setTimeout(fetchData, 5000);
            },
            error: function (error) {
                console.error("Error fetching data:", error);
                // Retry after a certain interval
                setTimeout(fetchData, 5000);
            },
        });
    }

    // Function to update the table with fetched data
    function updateTable(data) {
        var tableBody = $("#productTableBody");
        tableBody.empty();

        data.forEach(function (product) {
            var row = `<tr>
                <td>${product.name}</td>
                <td>${product.unit}</td>
                <td>${product.price}</td>
                <td>${product.expiry}</td>
                <td>${product.inventory}</td>
            </tr>`;
            tableBody.append(row);
        });
    }

    $("#productForm").submit(function (event) {
        event.preventDefault();

        var formData = $(this).serialize(); // Serialize form data
        $.ajax({
            url: "process.php",
            type: "POST",
            data: formData + "&action=addProduct", // Include action parameter
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    // Refresh the table after successful addition
                    fetchData();
                    // Reset the form
                    $("#productForm")[0].reset();
                } else {
                    console.error("Error adding product:", response.error);
                }
            },
            error: function (error) {
                console.error("Error adding product:", error);
            },
        });
    });

    // Fetch and display data when the page loads
    fetchData();
});
