<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    include("connection.php");
    require_once 'functions.php';

    $user_data = check_login2($con);

    // Query to get the total sales volume
    $query_sales = "SELECT SUM(total) as total_sales_volume FROM orders";
    $result_sales = mysqli_query($con, $query_sales);
    $total_sales_volume = 0;
    if ($result_sales) {
        $row_sales = mysqli_fetch_assoc($result_sales);
        $total_sales_volume = $row_sales['total_sales_volume'] ? $row_sales['total_sales_volume'] : 0;
    }

    // Query to get the total number of transactions
    $query_transactions = "SELECT COUNT(id) as total_transactions FROM orders";
    $result_transactions = mysqli_query($con, $query_transactions);
    $total_transactions = 0;
    if ($result_transactions) {
        $row_transactions = mysqli_fetch_assoc($result_transactions);
        $total_transactions = $row_transactions['total_transactions'] ? $row_transactions['total_transactions'] : 0;
    }

    $query_active_user = "SELECT COUNT(id) as total_active_user FROM users_cust";
    $result_active_user = mysqli_query($con, $query_active_user);
    $total_active_user = 0;
    if ($result_active_user) {
        $row_active_user = mysqli_fetch_assoc($result_active_user);
        $total_active_user = $row_active_user['total_active_user'] ? $row_active_user['total_active_user'] : 0;
    }

    // Query to get the top-selling products
    $query_top_selling_products = "
    SELECT p.name, SUM(oi.quantity) as total_sold
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    GROUP BY oi.product_id
    ORDER BY total_sold DESC
    LIMIT 5;
    ";
    $result_top_selling_products = mysqli_query($con, $query_top_selling_products);
    $top_selling_products = [];
    $product_names = [];
    $product_sales = [];

    if ($result_top_selling_products) {
        while ($row = mysqli_fetch_assoc($result_top_selling_products)) {
            $product_names[] = $row['name'];
            $product_sales[] = $row['total_sold'];
        }
    }

    // Query to get the top sellers based on total sales
    $query_top_sellers = "
    SELECT s.user_name, SUM(oi.quantity * p.price) AS total_sales
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    JOIN users_seller s ON p.seller_id = s.user_id
    GROUP BY s.user_id
    ORDER BY total_sales DESC
    LIMIT 5;
    ";

    $result_top_sellers = mysqli_query($con, $query_top_sellers);
    $top_sellers = [];
    $seller_names = [];
    $seller_sales = [];

    if ($result_top_sellers) {
        while ($row = mysqli_fetch_assoc($result_top_sellers)) {
            $seller_names[] = $row['user_name'];
            $seller_sales[] = $row['total_sales'];
        }
    }

    $query_monthly_earnings = "
    SELECT MONTH(o.order_date) as month, YEAR(o.order_date) as year, SUM(oi.quantity * p.price) AS total_earnings
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    JOIN orders o ON oi.order_id = o.id
    GROUP BY YEAR(o.order_date), MONTH(o.order_date)
    ORDER BY YEAR(o.order_date) DESC, MONTH(o.order_date) DESC
    ";

    $result_monthly_earnings = mysqli_query($con, $query_monthly_earnings);
    $monthly_earnings = [];
    $months = [];
    $earnings = [];

    if ($result_monthly_earnings) {
        while ($row = mysqli_fetch_assoc($result_monthly_earnings)) {
            $months[] = date('F Y', strtotime("{$row['year']}-{$row['month']}-01"));
            $earnings[] = $row['total_earnings'];
        }
    }

    // Query to get the total earnings for each year
    $query_yearly_earnings = "
    SELECT YEAR(o.order_date) as year, SUM(oi.quantity * p.price) AS total_earnings
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    JOIN orders o ON oi.order_id = o.id
    GROUP BY YEAR(o.order_date)
    ORDER BY YEAR(o.order_date) DESC
    ";
    $result_yearly_earnings = mysqli_query($con, $query_yearly_earnings);
    $years = [];
    $yearly_earnings = [];

    if ($result_yearly_earnings) {
        while ($row = mysqli_fetch_assoc($result_yearly_earnings)) {
            $years[] = $row['year'];
            $yearly_earnings[] = $row['total_earnings'];
        }
    }


    ?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Home Page</title>
        <link rel="stylesheet" href="astatistics.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    </head>

    <body>

        <div class="sidebar">
            <div class="logo">Lunar</div>
            <ul class="menu">
                <li class="wlcuser">
                    <i class=""></i>
                    <span>Welcome Admin</span>
                </li>
                <li>
                    <a href="ahome.php">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="ausers.php">
                        <i class="fas fa-users"></i>
                        <span>Users</span>
                    </a>
                </li>
                <li class="active">
                    <a href="astatistics.php">
                        <i class="fas fa-chart-bar"></i>
                        <span>Statistics</span>
                    </a>
                </li>
                <li class="logout">
                    <a href="alogout.php">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </a>
                </li>
            </ul>
        </div>

            <div class="main-content">
                <div class="chart-container">
                    <canvas id="topSellingProductsChart" width="400" height="400"></canvas>
                </div>

                <div class="chart-container">
                    <canvas id="topSellerChart" width="400" height="400"></canvas>
                </div>

                <div class="chart-container">
                    <canvas id="monthlyEarningsChart" width="400" height="400"></canvas>
                </div>

                <div class="chart-container">
                    <canvas id="yearlyEarningsChart" width="400" height="400"></canvas>
                </div>

        <script>

            let topSellingProductsChart;
            let topSellerChart;
            let monthlyEarningsChart;

            // Chart initialization (keeps the previous initialization logic)
            window.onload = function () {
                const productNames = <?php echo json_encode($product_names); ?>;
                const productSales = <?php echo json_encode($product_sales); ?>;
                const sellerNames = <?php echo json_encode($seller_names); ?>;
                const sellerSales = <?php echo json_encode($seller_sales); ?>;
                const earningsData = <?php echo json_encode($earnings); ?>;
                const monthsData = <?php echo json_encode($months); ?>;
                const yearsData = <?php echo json_encode($years); ?>;
                const yearlyEarningsData = <?php echo json_encode($yearly_earnings); ?>;

                // Sorting months data
                const sortedData = monthsData.map((month, index) => {
                    return { month: new Date(month), earnings: earningsData[index] };
                }).sort((a, b) => a.month - b.month);

                const sortedMonths = sortedData.map(item => item.month.toLocaleString('default', { month: 'long', year: 'numeric' }));
                const sortedEarnings = sortedData.map(item => item.earnings);

                // Sorting years data separately
                const sortedData2 = yearsData.map((year, index) => {
                    return { year: new Date(year), earnings: yearlyEarningsData[index] };
                }).sort((a, b) => a.year - b.year);

                const sortedYears = sortedData2.map(item => item.year.getFullYear()); // Only get the year part
                const sortedEarnings2 = sortedData2.map(item => item.earnings);

                const topSellingProductsCtx = document.getElementById('topSellingProductsChart').getContext('2d');
                const topSellingProductsChart = new Chart(topSellingProductsCtx, {
                    type: 'pie',
                    data: {
                        labels: productNames,
                        datasets: [{
                            data: productSales,
                            backgroundColor: ['#F28E8E', '#F4A2A2', '#C17D7D', '#A85D5D', '#8A3C3C'],
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { position: 'right' },
                            title: {
                                display: true,
                                text: 'Top Selling Products',
                                font: { size: 18 },
                                align: 'center',
                            }
                        }
                    }
                });

                const topSellerCtx = document.getElementById('topSellerChart').getContext('2d');
                const topSellerChart = new Chart(topSellerCtx, {
                    type: 'pie',
                    data: {
                        labels: sellerNames,
                        datasets: [{
                            data: sellerSales,
                            backgroundColor: ['#F28E8E', '#F4A2A2', '#C17D7D', '#A85D5D', '#8A3C3C'],
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { position: 'right' },
                            title: {
                                display: true,
                                text: 'Top Sellers',
                                font: { size: 18 },
                                align: 'center',
                            }
                        }
                    }
                });

                const earningsCtx = document.getElementById('monthlyEarningsChart').getContext('2d');
                    monthlyEarningsChart = new Chart(earningsCtx, {
                        type: 'line',
                        data: {
                            labels: sortedMonths,  // Labels will be the months
                            datasets: [{
                                label: 'Monthly Revenue',
                                data: sortedEarnings,
                                borderColor: '#4CAF50', // Green line
                                backgroundColor: 'rgba(76, 175, 80, 0.2)', // Light green area
                                fill: true,
                                tension: 0.4,  // Smooth line
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: { position: 'top' },
                                title: {
                                    display: true,
                                    text: 'Monthly Revenue',
                                    font: { size: 18 },
                                    align: 'center',
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    title: { display: true, text: 'Revenue (RM)' },
                                },
                                x: {
                                    reverse: false,  // Ensure the x-axis is not reversed
                                }
                            }
                        }
                });

                const yearlyEarningsCtx = document.getElementById('yearlyEarningsChart').getContext('2d');
                const yearlyEarningsChart = new Chart(yearlyEarningsCtx, {
                    type: 'line',
                    data: {
                        labels: sortedYears,  // Labels will be the years
                        datasets: [{
                            label: 'Yearly Revenue',
                            data: sortedEarnings2,
                            borderColor: '#FF5733', // Red line
                            backgroundColor: 'rgba(255, 87, 51, 0.2)', // Light red area
                            fill: true,
                            tension: 0.4,  // Smooth line
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { position: 'top' },
                            title: {
                                display: true,
                                text: 'Yearly Revenue',
                                font: { size: 18 },
                                align: 'center',
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: { display: true, text: 'Revenue (RM)' },
                            },
                            x: {
                                reverse: false,  // Ensure the x-axis is not reversed
                            }
                        }
                    }
                });

                window.addEventListener('resize', function() {
                    topSellingProductsChart.resize();
                });
            };
        </script>
    </body>

    </html>
