<?php

include 'connect.php';

if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   setcookie('user_id', create_unique_id(), time() + 60*60*24*30);
}

if(isset($_POST['update_cart'])){

   $cart_id = $_POST['cart_id'];
   $cart_id = filter_var($cart_id, FILTER_SANITIZE_STRING);
   $qty = $_POST['qty'];
   $qty = filter_var($qty, FILTER_SANITIZE_STRING);

   $update_qty = $conn->prepare("UPDATE `cart` SET qty = ? WHERE id = ?");
   $update_qty->execute([$qty, $cart_id]);

   $success_msg[] = 'Cart quantity updated!';

}

if(isset($_POST['delete_item'])){

   $cart_id = $_POST['cart_id'];
   $cart_id = filter_var($cart_id, FILTER_SANITIZE_STRING);
   
   $verify_delete_item = $conn->prepare("SELECT * FROM `cart` WHERE id = ?");
   $verify_delete_item->execute([$cart_id]);

   if($verify_delete_item->rowCount() > 0){
      $delete_cart_id = $conn->prepare("DELETE FROM `cart` WHERE id = ?");
      $delete_cart_id->execute([$cart_id]);
      $success_msg[] = 'Cart item deleted!';
   }else{
      $warning_msg[] = 'Cart item already deleted!';
   } 

}

if(isset($_POST['empty_cart'])){
   
   $verify_empty_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
   $verify_empty_cart->execute([$user_id]);

   if($verify_empty_cart->rowCount() > 0){
      $delete_cart_id = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
      $delete_cart_id->execute([$user_id]);
      $success_msg[] = 'Cart emptied!';
   }else{
      $warning_msg[] = 'Cart already emptied!';
   } 

}

?>

<?php
//include auth_session.php file on all user panel pages
include("auth_session.php");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title> Cart </title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <div class="form">
        <p>Hey, <?php echo $_SESSION['username']; ?>!</p>
        <p>Have a Nice Day!</p>
        <p><a href="index.html">Logout</a></p>
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Shopping Cart</title>

 

   <link rel="stylesheet" href="style1.css">

</head>
<body>
   
<?php include 'header.php'; ?>

<section class="products">

   <h1 class="heading">shopping cart</h1>

   <div class="box-container">

   <?php
      $grand_total = 0;
      $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
      $select_cart->execute([$user_id]);
      if($select_cart->rowCount() > 0){
         while($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)){

         $select_products = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
         $select_products->execute([$fetch_cart['product_id']]);
         if($select_products->rowCount() > 0){
            $fetch_product = $select_products->fetch(PDO::FETCH_ASSOC);
      
   ?>
   <form action="" method="POST" class="box">
      <input type="hidden" name="cart_id" value="<?= $fetch_cart['id']; ?>">
      <img src="uploaded_files/<?= $fetch_product['image']; ?>" class="image" alt="">
      <h3 class="name"><?= $fetch_product['name']; ?></h3>
      <div class="flex">
         <p class="price">Rs <?= $fetch_cart['price']; ?></p>
         <input type="number" name="qty" required min="1" value="<?= $fetch_cart['qty']; ?>" max="99" maxlength="2" class="qty">
         <button type="submit" name="update_cart" class="fas fa-edit">
         </button>
      </div>
      <p class="sub-total">sub total : <span>Rs<?= $sub_total = ($fetch_cart['qty'] * $fetch_cart['price']); ?></span></p>
      <input type="submit" value="delete" name="delete_item" class="delete-btn" onclick="return confirm('delete this item?');">
   </form>
   <?php
      $grand_total += $sub_total;
      }else{
         echo '<p class="empty">product was not found!</p>';
      }
      }
   }else{
      echo '<p class="empty">your cart is empty!</p>';
   }
   ?>

   </div>

   <?php if($grand_total != 0){ ?>
      <div class="cart-total">
         <p>grand total : <span>Rs <?= $grand_total; ?></span></p>
         <form action="" method="POST">
          <input type="submit" value="empty cart" name="empty_cart" class="delete-btn" onclick="return confirm('empty your cart?');">
         </form>
         <a href="checkout.php" class="btn">proceed to checkout</a>
      </div>
   <?php } ?>

</section>






<script src="js/script.js"></script>

<?php include 'alert.php'; ?>

</body>
</html>