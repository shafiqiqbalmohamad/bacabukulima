<?php

@include 'config.php';

if (isset($_POST['order_btn'])) {

   $name = $_POST['name'];
   $number = $_POST['number'];
   $email = $_POST['email'];
   $street = $_POST['street'];

   $cart_query = mysqli_query($conn, "SELECT * FROM `cart`");
   $price_total = 0;
   if (mysqli_num_rows($cart_query) > 0) {
      while ($product_item = mysqli_fetch_assoc($cart_query)) {
         $product_name[] = $product_item['name'] . ' (' . $product_item['quantity'] . ') ';
         $product_price = number_format($product_item['price'] * $product_item['quantity']);
         $price_total += $product_price;
      };
   };

   $total_product = implode(', ', $product_name);
   $detail_query = mysqli_query($conn, "INSERT INTO `ordering`(name, number, email, street, total_products, total_price) VALUES('$name','$number','$email','$street','$total_product','$price_total')") or die('query failed');

   if ($cart_query && $detail_query) {
      echo "
      <div class='order-message-container'>
      <div class='message-container'>
         <h3>thank you for shopping!</h3>
         <div class='order-detail'>
            <span>" . $total_product . "</span>
            <span class='total'> total : RM" . $price_total . "/-  </span>
         </div>
         <div class='customer-details'>
            <p> your name : <span>" . $name . "</span> </p>
            <p> your number : <span>" . $number . "</span> </p>
            <p> your email : <span>" . $email . "</span> </p>
            <p> your address : <span>" . $street . "</span> </p>
         </div>
            
            <button class='btn' id='payButton'>
               <div class='spinner hidden' id='spinner'></div>
               <span id='buttonText'>Make Payment</span>
            </button>
         </div>
      </div>
      ";
   }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>checkout</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="./css/style.css">
   <link rel="stylesheet" href="./css/stripe.css">

   <!-- stripe javascript library -->
   <script src="https://js.stripe.com/v3/"></script>

</head>

<body>

   <?php include 'header.php'; ?>

   <div class="container">
      <!-- Display errors returned by checkout session. -->
      <div id="paymentResponse" class="hidden"></div>

      <section class="checkout-form">

         <h1 class="heading">complete your order</h1>

         <form action="" method="post">

            <div class="display-order">
               <?php
               $select_cart = mysqli_query($conn, "SELECT * FROM `cart`");
               $total = 0;
               $grand_total = 0;
               if (mysqli_num_rows($select_cart) > 0) {
                  while ($fetch_cart = mysqli_fetch_assoc($select_cart)) {
                     $total_price = number_format($fetch_cart['price'] * $fetch_cart['quantity']);
                     $grand_total = $total += $total_price;
               ?>
                     <span><?= $fetch_cart['name']; ?>(<?= $fetch_cart['quantity']; ?>)</span>
               <?php
                  }
               } else {
                  echo "<div class='display-order'><span>your cart is empty!</span></div>";
               }
               ?>
               <span class="grand-total"> total price : RM<?= $grand_total; ?> </span>
            </div>

            <div class="flex">
               <div class="inputBox">
                  <span>your name</span>
                  <input type="text" placeholder="enter your name" name="name" required>
               </div>
               <div class="inputBox">
                  <span>your number</span>
                  <input type="number" placeholder="enter your number" name="number" required>
               </div>
               <div class="inputBox">
                  <span>your email</span>
                  <input type="email" placeholder="enter your email" name="email" required>
               </div>

               <div class="inputBox">
                  <span>full address</span>
                  <input type="text" placeholder="full address" name="street" required>
               </div>

            </div>
            <!-- <input type="submit" value="confirm order" name="order_btn" class="btn"> -->
            <input type="submit" value="confirm order" name="order_btn" class="btn">
            <!-- <button class="btn" id="payButton">
               <div class="spinner hidden" id="spinner"></div>
               <span id="buttonText">Make Payment</span>
            </button> -->
         </form>

      </section>

   </div>

   <script>
      // Set Stripe publishable key to initialize Stripe.js
      const stripe = Stripe('<?php echo STRIPE_PUBLISHABLE_KEY; ?>');

      // Select payment button
      const payBtn = document.querySelector("#payButton");

      // Payment request handler
      payBtn.addEventListener("click", function(evt) {
         setLoading(true);

         createCheckoutSession().then(function(data) {
            if (data.sessionId) {
               stripe.redirectToCheckout({
                  sessionId: data.sessionId,
               }).then(handleResult);
            } else {
               handleResult(data);
            }
         });
      });

      // Create a Checkout Session with the selected product
      const createCheckoutSession = function(stripe) {
         return fetch("payment_init.php", {
            method: "POST",
            headers: {
               "Content-Type": "application/json",
            },
            body: JSON.stringify({
               createCheckoutSession: 1,
            }),
         }).then(function(result) {
            return result.json();
         });
      };

      // Handle any errors returned from Checkout
      const handleResult = function(result) {
         if (result.error) {
            showMessage(result.error.message);
         }

         setLoading(false);
      };

      // Show a spinner on payment processing
      function setLoading(isLoading) {
         if (isLoading) {
            // Disable the button and show a spinner
            payBtn.disabled = true;
            document.querySelector("#spinner").classList.remove("hidden");
            document.querySelector("#buttonText").classList.add("hidden");
         } else {
            // Enable the button and hide spinner
            payBtn.disabled = false;
            document.querySelector("#spinner").classList.add("hidden");
            document.querySelector("#buttonText").classList.remove("hidden");
         }
      }

      // Display message
      function showMessage(messageText) {
         const messageContainer = document.querySelector("#paymentResponse");

         messageContainer.classList.remove("hidden");
         messageContainer.textContent = messageText;

         setTimeout(function() {
            messageContainer.classList.add("hidden");
            messageText.textContent = "";
         }, 5000);
      }
   </script>

   <!-- custom js file link  -->
   <script src="js/script.js"></script>


</body>

</html>