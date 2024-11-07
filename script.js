document.addEventListener('DOMContentLoaded', function () {
    console.log("DOMContentLoaded event fired"); // Debugging line
    document.querySelectorAll('input[name="payment_method"]').forEach(function (elem) {
        elem.addEventListener('change', function() {
            console.log("Payment method changed to:", this.value); // For debugging
            // Show the credit card info form if 'Credit Card' is selected, hide otherwise
            if (this.value === 'Credit Card') {
                console.log("Showing credit card info"); // Debugging line
                document.getElementById('credit-card-info').style.display = 'block';
                document.getElementById('credit-card-info').setAttribute('aria-hidden', 'false');
            } else {
                console.log("Hiding credit card info"); // Debugging line
                document.getElementById('credit-card-info').style.display = 'none';
                document.getElementById('credit-card-info').setAttribute('aria-hidden', 'true');
            }
        });
    });
});