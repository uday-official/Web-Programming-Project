<?php

@include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:login.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>about</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'header.php'; ?>

<section class="about">

   <div class="row">

      <div class="box">
         <img src="images/Whole.jpeg" alt="">
         <h3>What Makes Us Special ?</h3>
         <p>We deliver farm-fresh vegetables and hygienically sourced meat, handpicked daily, quality-checked, responsibly sourced, and delivered fresh to your doorstep with care, trust, and uncompromised freshness.
         </p>
         <a href="contact.php" class="btn">Reach Us Anytime</a>
      </div>

      <div class="box">
         <img src="images/Shopin.jpeg" alt="">
         <h3>What We Provide?</h3>
         <p>We provide a wide selection of fresh fruits, seasonal vegetables, and quality meat, sourced daily from trusted suppliers to support convenient shopping, healthy meals, and reliable access to essential food items.</p>
         <a href="shop.php" class="btn">Shop With Us</a>
      </div>

   </div>

</section>

<section class="reviews">

   <h1 class="title">Customer Reivews</h1>

   <div class="box-container">

      <div class="box">
         <img src="uploaded_img/Jr_NTR.jpeg" alt="">
         <p>Great online grocery platform with reasonable prices and very reliable doorstep delivery service.</p>
         <div class="stars">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="far fa-star"></i>
         </div>
         <h3>Jr NTR</h3>
      </div>

      <div class="box">
         <img src="uploaded_img/Srileela.jpeg" alt="">
         <p>Excellent quality products, fast delivery, and fresh items every single time I order, I recommend.</p>
         <div class="stars">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
         </div>
         <h3>Srileela</h3>
      </div>

      <div class="box">
         <img src="uploaded_img/Mahesh.jpeg" alt="">
         <p>Fresh fruits, vegetables, and meat delivered hygienically with outstanding customer support.</p>
         <div class="stars">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star-half-alt"></i>
         </div>
         <h3>Mahesh</h3>
      </div>

      <div class="box">
         <img src="uploaded_img/Ram.jpeg" alt="">
         <p>User friendly web, smooth ordering process, and timely delivery impressed me greatly.</p>
         <div class="stars">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="far fa-star"></i>
         </div>
         <h3>Ram</h3>
      </div>

      <div class="box">
         <img src="uploaded_img/Bhaai.jpeg" alt="">
         <p>Top quality groceries, neatly packed, affordable prices, and consistently satisfying experience.</p>
         <div class="stars">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star-half-alt"></i>
         </div>
         <h3>Bhaai</h3>
      </div>

      <div class="box">
         <img src="uploaded_img/Prabhas.jpeg" alt="">
         <p>Wide variety of products, fresh quality, and Good service overall. I definitely recommend.</p>
         <div class="stars">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
         </div>
         <h3>Prabhas</h3>
      </div>

   </div>

</section>


<?php include 'footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>