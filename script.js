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

document.addEventListener('DOMContentLoaded', function () {
    console.log("Carousel script loaded");

    let currentSlide = 0;
    const slides = document.querySelectorAll(".carousel-slide");
    const indicators = document.querySelectorAll(".indicator");
    const carousel = document.querySelector(".carousel");

    // Update the carousel with the correct slide and indicator
    function updateCarousel() {
        // Hide all slides and remove the active class from indicators
        slides.forEach((slide, index) => {
            slide.classList.remove("active");
        });
        indicators.forEach((indicator, index) => {
            indicator.classList.remove("active");
        });

        // Show the active slide and active indicator
        slides[currentSlide].classList.add("active");
        indicators[currentSlide].classList.add("active");

        // Adjust the carousel's translateX to show the current slide
        carousel.style.transform = `translateX(-${currentSlide * 100}%)`;
        console.log("Updated to slide:", currentSlide); // Debugging line
    }

    function nextSlide() {
        currentSlide = (currentSlide + 1) % slides.length; // Wraps around the slides
        updateCarousel();
        console.log("Next slide:", currentSlide); // Debugging line
    }

    function prevSlide() {
        currentSlide = (currentSlide - 1 + slides.length) % slides.length; // Wraps around the slides
        updateCarousel();
        console.log("Previous slide:", currentSlide); // Debugging line
    }

    function goToSlide(slideIndex) {
        currentSlide = slideIndex;
        updateCarousel();
        console.log("Go to slide:", currentSlide); // Debugging line
    }

    // Add event listeners for previous and next buttons
    document.querySelector(".prev").addEventListener("click", function() {
        console.log("Previous button clicked"); // Debugging line
        prevSlide();
    });
    document.querySelector(".next").addEventListener("click", function() {
        console.log("Next button clicked"); // Debugging line
        nextSlide();
    });

    // Add event listeners for indicators
    indicators.forEach((indicator, index) => {
        indicator.addEventListener("click", () => {
            console.log("Indicator clicked, go to slide", index); // Debugging line
            goToSlide(index);
        });
    });

});
