<?php
require_once 'functions.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Interactive Carousel</title>
  <link rel="stylesheet" href="homepage.css">
</head>
<body>
<?=template_header1('Homepage')?>
  <div class="carousel-container">
    <div class="carousel">
      <div class="carousel-slide active">
        <img src="image1.png" alt="Slide 0">
        <div class="caption">Explore the latest smartphones</div>
      </div>
      <div class="carousel-slide">
        <img src="image2.png" alt="Slide 1">
        <div class="caption">Find top-quality accessories</div>
      </div>
      <div class="carousel-slide">
        <img src="image3.png" alt="Slide 2">
        <div class="caption">Discover tablets and more</div>
      </div>
    </div>
    <button class="carousel-control prev" onclick="prevSlide()">❮</button>
    <button class="carousel-control next" onclick="nextSlide()">❯</button>
    <div class="indicators">
      <span onclick="goToSlide(0)" class="indicator active"></span>
      <span onclick="goToSlide(1)" class="indicator"></span>
      <span onclick="goToSlide(2)" class="indicator"></span>
    </div>
  </div>

  <script src="script.js"></script>
  <?=template_footer()?>
</body>
</html>
