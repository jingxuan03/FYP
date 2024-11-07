    <?php
    include("connection.php");

    $start_date = isset($_GET['start_date']) ? $_GET['start_date'] : null;
    $end_date = isset($_GET['end_date']) ? $_GET['end_date'] : null;
    $type = isset($_GET['type']) ? $_GET['type'] : '';

    error_log("Start Date: $start_date, End Date: $end_date, Type: $type");

    // Check for the type (products or sellers)
    if ($type === 'products') {
        if ($start_date && $end_date) {
            // Convert the date to a valid format if needed
            $start_date = mysqli_real_escape_string($con, $start_date);
            $end_date = mysqli_real_escape_string($con, $end_date);
    
            $query_top_selling_products = "
            SELECT p.name, SUM(oi.quantity) as total_sold
            FROM order_items oi
            JOIN products p ON oi.product_id = p.id
            JOIN orders o ON oi.order_id = o.id
            WHERE o.date BETWEEN '$start_date' AND '$end_date'
            GROUP BY oi.product_id
            ORDER BY total_sold DESC
            LIMIT 5;
            ";
        } else {
            // If no dates are provided, return all data
            $query_top_selling_products = "
            SELECT p.name, SUM(oi.quantity) as total_sold
            FROM order_items oi
            JOIN products p ON oi.product_id = p.id
            GROUP BY oi.product_id
            ORDER BY total_sold DESC
            LIMIT 5;
            ";
        }

        $result_top_selling_products = mysqli_query($con, $query_top_selling_products);

        $product_names = [];
        $product_sales = [];

        if ($result_top_selling_products) {
            while ($row = mysqli_fetch_assoc($result_top_selling_products)) {
                $product_names[] = $row['name'];
                $product_sales[] = $row['total_sold'];
            }
        }

        // Return product data as JSON
        echo json_encode([
            'product_names' => $product_names,
            'product_sales' => $product_sales
        ]);
    } elseif ($type === 'sellers') {
        // Query for top sellers
        $query_top_sellers = "
        SELECT s.user_name, SUM(oi.quantity * p.price) AS total_sales
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        JOIN users_seller s ON p.seller_id = s.user_id
        JOIN orders o ON oi.order_id = o.id
        WHERE o.date BETWEEN '$start_date' AND '$end_date'
        GROUP BY s.user_id
        ORDER BY total_sales DESC
        LIMIT 5;
        ";

        $result_top_sellers = mysqli_query($con, $query_top_sellers);

        $seller_names = [];
        $seller_sales = [];

        if ($result_top_sellers) {
            while ($row = mysqli_fetch_assoc($result_top_sellers)) {
                $seller_names[] = $row['user_name'];
                $seller_sales[] = $row['total_sales'];
            }
        }

        // Return seller data as JSON
        echo json_encode([
            'seller_names' => $seller_names,
            'seller_sales' => $seller_sales
        ]);
    }
    
    ?>
