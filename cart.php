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
    <title>Cart - Pizza Hot</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/cart.css">



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

            <a href="index.php" class="logo"><img class="logo_img" width="175px" src="project_images/wide_logo.png"></a>

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
                <!-- <div id="cart-btn" class="fas fa-shopping-cart"><span>(<?= $total_cart_items; ?>)</span></div> -->
            </div>

        </section>

    </header>


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
            } else {
                echo '<p class="empty">nothing ordered yet!</p>';
            }
            ?>

        </section>

    </div>
    <!-- order section ends -->

    <div class="shopping-cart">

        <section>

            <div id="close-cart"><span>close</span></div>

            <?php
            $grand_total = 0;
            $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
            $select_cart->execute([$user_id]);
            if ($select_cart->rowCount() > 0) {
                while ($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)) {
                    $item_total = $fetch_cart['price'];
            ?>
                    <div class="box">
                        <a href="index.php?delete_cart_item=<?= $fetch_cart['id']; ?>" class="fas fa-times" onclick="return confirm('delete this cart item?');"></a>
                        <img src="uploaded_img/<?= $fetch_cart['image']; ?>" alt="">
                        <div class="content">
                            <p> <?= $fetch_cart['name']; ?> </p>
                            <?php if ($fetch_cart['category'] == 'Pizza') { ?><p> <?= $fetch_cart['size']; ?><br><span>(<?= $fetch_cart['price']; ?> x <?= $fetch_cart['quantity']; ?>)</span></p>
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
                                    ?></p><?php } else {
                                            ?>
                                <p><span>(<?= $fetch_cart['price']; ?> x <?= $fetch_cart['quantity']; ?>)</span></p>
                            <?php } ?>
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

            <a href="#order" class="btn">order now</a>

        </section>


    </div>
    <div class="display-cart">
        <section>

            <?php
            $grand_total = 0;
            $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
            $select_cart->execute([$user_id]);
            if ($select_cart->rowCount() > 0) {
                while ($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)) {
                    $item_total = $fetch_cart['price'];
            ?>
                    <div class="cart-box">



                        <img src="uploaded_img/<?= $fetch_cart['image']; ?>" alt="">


                        <div class="content">

                            <p class="title"> <?= $fetch_cart['name']; ?> </p>

                            <?php if ($fetch_cart['category'] == 'Pizza') { ?><p class="size"> <?= $fetch_cart['size']; ?><br><span class="price">(<?= $fetch_cart['price']; ?> x <?= $fetch_cart['quantity']; ?>)</span></p>
                                <?php
                                if ($fetch_cart['toppings'] == 'f') {
                                    echo "<p> No extra toppings.</p>";
                                } else {
                                ?>
                                    <p class="cart-subheadings"> Extra <?= $fetch_cart['toppings']; ?> <br><span class="price">(+₹60)</span></p>
                                <?php $item_total += 60;
                                }
                                ?>
                                <p class="cart-subheadings"><?= $fetch_cart['crust']; ?>
                                    <?php
                                    if ($fetch_cart['crust'] == '100% Wheat Thin Crust') {
                                    ?>
                                        <br><span class="price">(+₹60)</span>

                                    <?php $item_total += 60;
                                    }
                                    if ($fetch_cart['crust'] == 'Cheese Burst') {
                                    ?>
                                        <br><span class="price">(+₹120)</span>

                                    <?php
                                        $item_total += 120;
                                    }
                                    ?>
                                </p><?php } else {
                                    ?>
                                <p class="price"><span>(<?= $fetch_cart['price']; ?> x <?= $fetch_cart['quantity']; ?>)</span></p>
                            <?php } ?>

                            <form action="" method="post">
                                <input type="hidden" name="cart_id" value="<?= $fetch_cart['id']; ?>">
                                <input type="number" name="qty" class="qty" min="1" max="99" value="<?= $fetch_cart['quantity']; ?>" onkeypress="if(this.value.length == 2) return false;">
                                <button type="submit" class="fas fa-edit" name="update_qty"></button>
                                <?php $item_total = $item_total * $fetch_cart['quantity']; ?>

                                <h2 class="item-total">Item Total : <?= $item_total; ?></h2>

                            </form>
                        </div>
                        <a href="index.php?delete_cart_item=<?= $fetch_cart['id']; ?>" class="fas fa-times" onclick="return confirm('delete this cart item?');"></a>
                    </div>
            <?php
                    $grand_total += $item_total;
                }
            } else {
                echo '<p class="empty"><span>your cart is empty!</span></p>';
            }
            ?>

            <div class="cart-total"> GRAND TOTAL : <span>Rs.<?= $grand_total; ?>/-</span></div>


            <a href="checkout.php" class="btn">Place Order</a>

        </section>
    </div>
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
    <script src="js/main.js">
    </script>
</body>

</html>