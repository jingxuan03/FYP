<?php
session_start();
// Include functions and connect to the database using PDO MySQL
require_once 'functions.php';
$pdo = pdo_connect_mysql();
// Page is set to home (home.php) by default, so when the visitor visits, that will be the page they see.
$page = isset($_GET['page']) && file_exists($_GET['page'] . '.php') ? $_GET['page'] : 'chome';
// Include and show the requested page
include $page . '.php';
?>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("search");
    const productsList = document.getElementById("products-list");

    if (searchInput) {
        searchInput.addEventListener("input", function () {
            const searchQuery = searchInput.value;

            // Perform an AJAX request to get filtered products
            fetch("cfetchproducts.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded",
                },
                body: new URLSearchParams({
                    search: searchQuery,
                    p: 1 // Set the page number to 1 for fresh search results
                }),
            })
            .then(response => response.json())
            .then(data => {
                // Clear current products
                productsList.innerHTML = '';

                // Check if there are products in the response
                if (data.length === 0) {
                    productsList.innerHTML = '<p>No products found</p>';
                    return;
                }

                // Display filtered products
                data.forEach(product => {
                    const productDiv = document.createElement("a");
                    productDiv.href = "index.php?page=sproduct&id=" + product.id;
                    productDiv.classList.add("product");

                    const img = document.createElement("img");
                    img.src = product.img;
                    img.width = 200;
                    img.height = 200;
                    img.alt = product.name;

                    const name = document.createElement("span");
                    name.classList.add("name");
                    name.textContent = product.name;

                    const price = document.createElement("span");
                    price.classList.add("price");
                    price.textContent = "RM " + product.price;

                    const sellerName = document.createElement("span");
                    sellerName.classList.add("seller-name");
                    sellerName.textContent = "Seller: " + product.user_name;

                    // Append the product details
                    productDiv.appendChild(img);
                    productDiv.appendChild(name);
                    productDiv.appendChild(price);
                    productDiv.appendChild(sellerName);

                    productsList.appendChild(productDiv);
                });
            })
            .catch(error => console.error("Error:", error));
        });
    }
});

</script>
