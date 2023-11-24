<?php

include 'config.php';

session_start();

if (isset($_SESSION['user_id'])) {
   $user_id = $_SESSION['user_id'];
} else {
   $user_id = '';
};

if (isset($_POST['register'])) {

   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $pass = sha1($_POST['pass']);
   $pass = filter_var($pass, FILTER_SANITIZE_STRING);
   $cpass = sha1($_POST['cpass']);
   $cpass = filter_var($cpass, FILTER_SANITIZE_STRING);

   $select_user = $conn->prepare("SELECT * FROM `user` WHERE name = ? AND email = ?");
   $select_user->execute([$name, $email]);

   if ($select_user->rowCount() > 0) {
      $message[] = 'username or email already exists!';
   } else {
      if ($pass != $cpass) {
         $message[] = 'confirm password not matched!';
      } else {
         $insert_user = $conn->prepare("INSERT INTO `user`(name, email, password) VALUES(?,?,?)");
         $insert_user->execute([$name, $email, $cpass]);
         $message[] = 'registered successfully, login now please!';
      }
   }
}

if (isset($_POST['update_qty'])) {
   $cart_id = $_POST['cart_id'];
   $qty = $_POST['qty'];
   $qty = filter_var($qty, FILTER_SANITIZE_STRING);
   $update_qty = $conn->prepare("UPDATE `cart` SET quantity = ? WHERE id = ?");
   $update_qty->execute([$qty, $cart_id]);
   $message[] = 'cart quantity updated!';
}

if (isset($_GET['delete_cart_item'])) {
   $delete_cart_id = $_GET['delete_cart_item'];
   $delete_cart_item = $conn->prepare("DELETE FROM `cart` WHERE id = ?");
   $delete_cart_item->execute([$delete_cart_id]);
   header('location:index.php');
}

if (isset($_GET['logout'])) {
   session_unset();
   session_destroy();
   header('location:index.php');
}



if (isset($_POST['order'])) {

   if ($user_id == '') {
      $message[] = 'please login first!';
   } else {
      $name = $_POST['name'];
      $name = filter_var($name, FILTER_SANITIZE_STRING);
      $number = $_POST['number'];
      $number = filter_var($number, FILTER_SANITIZE_STRING);
      $address = 'flat no.' . $_POST['flat'] . ', ' . $_POST['street'] . ' - ' . $_POST['pin_code'];
      $address = filter_var($address, FILTER_SANITIZE_STRING);
      $method = $_POST['method'];
      $method = filter_var($method, FILTER_SANITIZE_STRING);
      $total_price = $_POST['total_price'];
      $total_products = $_POST['total_products'];

      $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
      $select_cart->execute([$user_id]);

      if ($select_cart->rowCount() > 0) {
         $insert_order = $conn->prepare("INSERT INTO `orders`(user_id, name, number, method, address, total_products, total_price) VALUES(?,?,?,?,?,?,?)");
         $insert_order->execute([$user_id, $name, $number, $method, $address, $total_products, $total_price]);
         $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
         $delete_cart->execute([$user_id]);
         $message[] = 'order placed successfully!';
      } else {
         $message[] = 'your cart empty!';
      }
   }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Pizza Hot</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/main.css">

</head>

<body>

   <?php
   if (isset($message)) {
      foreach ($message as $message) {
         echo '
         <div class="message">
            <span>' . $message . '</span>
            <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
         </div>
         ';
      }
   }
   ?>

   <!-- header section starts  -->

   <header class="header">

      <section class="flex">

         <a href="#home" class="logo"><img class="logo_img" width="175px" src="project_images/wide_logo.png"></a>
         <nav class="navbar">
            <a href="index.php ">Home</a>
            <a href="index.php #about">About</a>
            <a href="menu.php" class="menu-link">Menu</a>
            <a href="index.php #faq">FAQs</a>
         </nav>

         <div class="icons">
            <div id="menu-btn" class="fas fa-bars"></div>
            <div id="user-btn" class="fas fa-user"></div>
            <div id="order-btn" class="fas fa-box"></div>
            <?php
            $count_cart_items = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
            $count_cart_items->execute([$user_id]);
            $total_cart_items = $count_cart_items->rowCount();
            ?>
           <!-- <div id="cart-btn" class="fas fa-shopping-cart"><span>(<?= $total_cart_items; ?>)</span></div>-->
         </div>

      </section>

   </header>

   <!-- header section ends -->

   <div class="user-account">

      <section>

         <div id="close-account"><span>close</span></div>

         <div class="user">
            <?php
            $select_user = $conn->prepare("SELECT * FROM `user` WHERE id = ?");
            $select_user->execute([$user_id]);
            if ($select_user->rowCount() > 0) {
               while ($fetch_user = $select_user->fetch(PDO::FETCH_ASSOC)) {
                  echo '<p>welcome ! <span>' . $fetch_user['name'] . '</span></p>';
                  echo '<a href="index.php?logout" class="btn">logout</a>';
               }
            } else {
               echo '<p><span>you are not logged in now!</span></p>';
            }
            ?>
         </div>

         <div class="display-orders">
            <?php
            $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
            $select_cart->execute([$user_id]);
            if ($select_cart->rowCount() > 0) {
               while ($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)) {
                  echo '<p>' . $fetch_cart['name'] . ' <span>(' . $fetch_cart['price'] . ' x ' . $fetch_cart['quantity'] . ')</span></p>';
               }
            } else {
               echo '<p><span>your cart is empty!</span></p>';
            }
            ?>
         </div>

         <div class="flex">

            <form action="user_login.php" method="post">
               <h3>login now</h3>
               <input type="email" name="email" required class="box" placeholder="enter your email" maxlength="50">
               <input type="password" name="pass" required class="box" placeholder="enter your password" maxlength="20">
               <input type="submit" value="login now" name="login" class="btn">
            </form>

            <form action="" method="post">
               <h3>register now</h3>
               <input type="text" name="name" oninput="this.value = this.value.replace(/\s/g, '')" required class="box" placeholder="enter your username" maxlength="20">
               <input type="email" name="email" required class="box" placeholder="enter your email" maxlength="50">
               <input type="password" name="pass" required class="box" placeholder="enter your password" maxlength="20" oninput="this.value = this.value.replace(/\s/g, '')">
               <input type="password" name="cpass" required class="box" placeholder="confirm your password" maxlength="20" oninput="this.value = this.value.replace(/\s/g, '')">
               <input type="submit" value="register now" name="register" class="btn">
            </form>

         </div>

      </section>

   </div>

   <!-- order section starts -->
   <div class="my-orders">

        <section>

            <div id="close-orders"><span>close</span></div>

            <h3 class="title"> my orders </h3>

         <?php
         $select_orders = $conn->prepare("SELECT * FROM `orders` WHERE user_id = ?");
         $select_orders->execute([$user_id]);
         if ($select_orders->rowCount() > 0) {
            while ($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)) {
         ?>
               <div class="box">
                  <p> placed on : <span><?= $fetch_orders['placed_on']; ?></span> </p>
                  <p> name : <span><?= $fetch_orders['name']; ?></span> </p>
                  <p> number : <span><?= $fetch_orders['number']; ?></span> </p>
                  <p> address : <span><?= $fetch_orders['address']; ?></span> </p>
                  <p> payment method : <span><?= $fetch_orders['method']; ?></span> </p>
                  <div class="details">
                     <p> Details : <span><?= $fetch_orders['items_list']; ?></span> </p>
                  </div>
                  <p> total price : <span>$<?= $fetch_orders['total_price']; ?>/-</span> </p>
                  <p> payment status : <span style="color:<?php if ($fetch_orders['payment_status'] == 'pending') {
                                                               echo 'red';
                                                            } else {
                                                               echo 'green';
                                                            }; ?>"><?= $fetch_orders['payment_status']; ?></span> </p>
               </div>
         <?php
            }
            ?>

        </section>

   </div>
   <!-- order section ends -->
   <!-- CART  -->
   <div class="shopping-cart">

      <section>

         <div id="close-cart"><span>close</span></div>

         <?php
         $grand_total = 0;
         $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
         $select_cart->execute([$user_id]);
         if ($select_cart->rowCount() > 0) {
            while ($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)) {
               $item_total = ($fetch_cart['price']);
         ?>
               <div class="box">
                  <a href="index.php?delete_cart_item=<?= $fetch_cart['id']; ?>" class="fas fa-times" onclick="return confirm('delete this cart item?');"></a>
                  <img src="uploaded_img/<?= $fetch_cart['image']; ?>" alt="">
                  <div class="content">
                     <p> <?= $fetch_cart['name']; ?> </p>
                     <p> <?= $fetch_cart['size']; ?><br><span>(<?= $fetch_cart['price']; ?> x <?= $fetch_cart['quantity']; ?>)</span></p>
                     <?php
                     if ($fetch_cart['toppings'] == 'f') {
                        echo "<p> No extra toppings.</p>";
                     } else {

                        echo "<p> Extra " . $fetch_cart['toppings'] . "<br><span>(+₹60)</span></p>";
                        $item_total += 60;
                     }
                     ?>
                     <p><?= $fetch_cart['crust']; ?>
                        <?php
                        if ($fetch_cart['crust'] == '100% Wheat Thin Crust') {
                           echo "<br><span>(+₹60)</span>";
                           $item_total += 60;
                        }
                        if ($fetch_cart['crust'] == 'Cheese Burst') {
                           echo "<br><span>(+₹120)</span>";
                           $item_total += 120;
                        }
                        ?></p>
                     <?php $item_total = $item_total * $fetch_cart['quantity']; ?>

                     <h2>Item Total : <?= $item_total; ?></h2>

                     <form action="" method="post">
                        <input type="hidden" name="cart_id" value="<?= $fetch_cart['id']; ?>">
                        <input type="number" name="qty" class="qty" min="1" max="99" value="<?= $fetch_cart['quantity']; ?>" onkeypress="if(this.value.length == 2) return false;">
                        <button type="submit" class="fas fa-edit" name="update_qty"></button>
                     </form>
                  </div>
               </div>
         <?php
               $grand_total += $item_total;
            }
         } else {
            echo '<p class="empty"><span>your cart is empty!</span></p>';
         }
         ?>

         <div class="cart-total"> grand total : <span>Rs.<?= $grand_total; ?>/-</span></div>

         <a href="cart.php" class="btn">order now</a>

      </section>

   </div>
   <!-- CART  -->


   <div class="home-bg">

      <section class="home" id="home">

         <div class="slide-container">

            <div class="slide active">
               <div class="image">
                  <img src="images/home-img-1.png" alt="">
               </div>
               <div class="content">
                  <h3>Homemade Pepperoni Pizza</h3>
                  <div class="fas fa-angle-left" onclick="prev()"></div>
                  <div class="fas fa-angle-right" onclick="next()"></div>
               </div>
            </div>

            <div class="slide">
               <div class="image">
                  <img src="images/home-img-2.png" alt="">
               </div>
               <div class="content">
                  <h3>Pizza With Onions</h3>
                  <div class="fas fa-angle-left" onclick="prev()"></div>
                  <div class="fas fa-angle-right" onclick="next()"></div>
               </div>
            </div>

            <div class="slide">
               <div class="image">
                  <img src="images/home-img-3.png" alt="">
               </div>
               <div class="content">
                  <h3>Mascarpone And Mushrooms</h3>
                  <div class="fas fa-angle-left" onclick="prev()"></div>
                  <div class="fas fa-angle-right" onclick="next()"></div>
               </div>
            </div>

         </div>

      </section>

   </div>


   <!-- image cards -->
   <h1 class="heading">Our Offered Products</h1>

   <div class="card-section">
      <div class="card-container">
         <a href="menu.php#pizzas">
            <div class="card">
               <div class="card-details">
                  <img src="./images/pizza1.png">
               </div>


            </div>
            <h1 class="subheads">Pizzas </h1>
      </div>
      <div class="card-container">
         <a href="menu.php#sides">
            <div class="card">
               <div class="card-details">
                  <img src="./images/sides1.png">
               </div>


            </div>
            <h1 class="subheads">Sides</h1>
      </div>
      <div class="card-container">
         <a href="menu.php#beverages">
            <div class="card">
               <div class="card-details">
                  <img src="./images/beverages1.png">
               </div>


            </div>
            <h1 class="subheads">Beverages</h1>
      </div>
      <div class="card-container">
         <a href="menu.php#desserts">
            <div class="card">
               <div class="card-details">
                  <img src="./images/desserts1.png">
               </div>


            </div>
            <h1 class="subheads">Desserts</h1>
      </div>
   </div>


   <h1 class="heading">Latest Offers </h1>

   <div class="banner-container">
      <div class="banner-wrapper">
         <img src="images/banner3.png">
         <img src="images/banner2.png">
         <img src="images/banner1.png">
      </div>
   </div>



   <!-- testimonials -->
   <h1 class="heading">Reviews We Take Pride In</h1>

   <div class="testimonials">

      <figure class="snip1533">
         <figcaption>
            <blockquote>
               <p>Having tried pizzas from various establishments, Pizza Hot consistently impresses with their inventive combinations and top-notch ingredients.</p>
            </blockquote>
            <h3>The Infatuation</h3>
            <h4>Rated 4.8/5</h4>
         </figcaption>
      </figure>
      <figure class="snip1533">
         <figcaption>

            <blockquote>
               <p>From the first bite to the last, Pizza Hot delivers an unforgettable pizza experience. The perfect blend of flavors and quality ingredients puts them in a league of their own.</p>
            </blockquote>
            <h3>Eater</h3>
            <h4>Rated 5/5</h4>
         </figcaption>
      </figure>
      <figure class="snip1533">
         <figcaption>

            <blockquote>
               <p>Pizza Hot stands out for its exceptional pizzas. The crust is a masterpiece, and the toppings are a symphony of taste. A must-try for any pizza lover!</p>
            </blockquote>
            <h3>Bon Appétit</h3>
            <h4>Rated 4.9/5</h4>
         </figcaption>
      </figure>
   </div>


   <!-- about section starts  -->

   <section class="about" id="about">




      <h1 class="heading">about us</h1>

      <div class="box-container">

         <div class="box">
            <img src="images/about-1.svg" alt="">
            <h3>Made with Love</h3>
            <p>A symphony of premium ingredients, artisanal craftsmanship, and a dash of passion in every slice. Indulge in the warmth and flavor that sets our pizzas apart—a true labor of love from our kitchen to your table. </p>

         </div>

         <div class="box">
            <img src="images/about-2.svg" alt="">
            <h3>30-Minute Delivery</h3>
            <p> Because we believe in serving more than just pizza—prompt, piping hot perfection delivered straight to your door. Fast, fresh, and flavorful, our commitment to speedy service ensures your satisfaction with every bite. </p>
         </div>

         <div class="box">
            <img src="images/about-3.svg" alt="">
            <h3>Share with Friends</h3>
            <p>Spread the joy, not just the cheese! Elevate your pizza experience by sharing the love with friends and family. Our tantalizing creations are made for communal indulgence, turning every meal into a celebration. </p>

         </div>

      </div>

   </section>

   <!-- about section ends -->





   <!-- faq section starts  -->

   <section class="faq" id="faq">

      <h1 class="heading">FAQs</h1>

      <div class="accordion-container">

         <div class="accordion active">
            <div class="accordion-heading">
               <span>Are your ingredients fresh?</span>
               <i class="fas fa-angle-down"></i>
            </div>
            <p class="accrodion-content">
               Absolutely! We take pride in using only the freshest, high-quality ingredients. From locally sourced produce to premium cheeses and meats, our commitment to freshness is at the core of our culinary philosophy.
            </p>
         </div>

         <div class="accordion">
            <div class="accordion-heading">
               <span>What's your delivery time?</span>
               <i class="fas fa-angle-down"></i>
            </div>
            <p class="accrodion-content">
               We strive for a prompt delivery experience. Our standard delivery time is 30 minutes, ensuring that you receive your hot and fresh pizza in a timely manner.
            </p>
         </div>

         <div class="accordion">
            <div class="accordion-heading">
               <span> Can I place a large order for events or parties?</span>
               <i class="fas fa-angle-down"></i>
            </div>
            <p class="accrodion-content">
               Certainly! We cater to events of all sizes. Contact our catering team, and we'll help you customize a delicious pizza package that suits your party or gathering.
            </p>
         </div>

         <div class="accordion">
            <div class="accordion-heading">
               <span>Can I customize my pizza with specific toppings?</span>
               <i class="fas fa-angle-down"></i>
            </div>
            <p class="accrodion-content">
               Certainly! Our menu is a starting point. Feel free to customize your pizza with a wide array of toppings to create the perfect flavor combination for your taste buds.
            </p>
         </div>




      </div>

   </section>

   <!-- faq section ends -->

   <!-- footer section starts  -->

   <div class="footer">

      <div class="box-container">

         <div class="box">
            <i class="fas fa-phone"></i>
            <h3>Phone Number</h3>
            <p>+91 8849779702</p>
            <p>+91 8962749659</p>
         </div>

         <div class="box">
            <i class="fas fa-map-marker-alt"></i>
            <h3>Our Address</h3>
            <p>South Civil Lines, Jabalpur (482001)</p>
         </div>

         <div class="box">
            <i class="fas fa-clock"></i>
            <h3>Opening Hours</h3>
            <p>11:00 am to <br>

               11:00 pm</p>
         </div>

         <div class="box">
            <i class="fas fa-envelope"></i>
            <h3>Email Address</h3>
            <p>poojangoyani@gmail.com</p>
            <p>pathakarpita867@gmail.com</p>
         </div>

      </div>

      <div class="credit">
         &copy; copyright @ 2023 by <span>Pizza Hot</span> | all rights reserved!
      </div>
      </section>

      <!-- footer section ends -->



















      <!-- custom js file link  -->
      <script src="js/main.js"></script>

</body>

</html>