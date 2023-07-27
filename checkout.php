<?php

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
   header('location:user_login.php');
};

if(isset($_POST['order'])){

   $name = $_POST['name'];
   $number = $_POST['number'];
   if (!preg_match ("/^[a-zA-z]*$/", $name) ) {  
    $message[] = 'Please enter valid Name';
   }
  
   else
   {
      $name = filter_var($name, FILTER_SANITIZE_STRING);
      $number = filter_var($number, FILTER_SANITIZE_STRING);
      $email = $_POST['email'];
      $email = filter_var($email, FILTER_SANITIZE_STRING);
      $method = $_POST['method'];
      $method = filter_var($method, FILTER_SANITIZE_STRING);
      $address = 'flat no. '. $_POST['flat'] .', '. $_POST['street'] .', '. $_POST['city'] .', '. $_POST['state'] .', '. $_POST['country'] .' - '. $_POST['pin_code'];
      $address = filter_var($address, FILTER_SANITIZE_STRING);
      $total_products = $_POST['total_products'];
      $total_price = $_POST['total_price'];

      $check_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
      $check_cart->execute([$user_id]);

      if($check_cart->rowCount() > 0){

         $insert_order = $conn->prepare("INSERT INTO `orders`(user_id, name, number, email, method, address, total_products, total_price) VALUES(?,?,?,?,?,?,?,?)");
         $insert_order->execute([$user_id, $name, $number, $email, $method, $address, $total_products, $total_price]);

         $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
         $delete_cart->execute([$user_id]);

         $message[] = 'order placed successfully!';
      }else{
         $message[] = 'your cart is empty';
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
     <link rel="icon"
        href="https://www.citypng.com/public/uploads/preview/flipkart-logo-icon-hd-png-11664324777lywvxb7wvs.png"
        type="image/x-icon">
    
   <title>checkout</title>
   
   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'components/user_header.php'; ?>

<section class="checkout-orders">

   <form action="" method="POST">

   <h3>your orders</h3>

      <div class="display-orders">
      <?php
         $grand_total = 0;
         $cart_items[] = '';
         $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
         $select_cart->execute([$user_id]);
         if($select_cart->rowCount() > 0){
            while($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)){
               $cart_items[] = $fetch_cart['name'].' ('.$fetch_cart['price'].' x '. $fetch_cart['quantity'].') - ';
               $total_products = implode($cart_items);
               $grand_total += ($fetch_cart['price'] * $fetch_cart['quantity']);
      ?>
         <p> <?= $fetch_cart['name']; ?> <span>(<?= '$'.$fetch_cart['price'].'/- x '. $fetch_cart['quantity']; ?>)</span> </p>
      <?php
            }
         }else{
            echo '<p class="empty">your cart is empty!</p>';
         }
      ?>
         <input type="hidden" name="total_products" value="<?= $total_products; ?>">
         <input type="hidden" name="total_price" value="<?= $grand_total; ?>" value="">
         <div class="grand-total">grand total : <span>$<?= $grand_total; ?>/-</span></div>
      </div>

      <h3>place your orders</h3>

      <div class="flex">
         <div class="inputBox">
            <span>your name :</span>
            <input type="text" name="name" placeholder="enter your name" class="box" maxlength="20" required>
         </div>
         <div class="inputBox">
            <span>your number :</span>
            <input type="number" name="number" placeholder="enter your number" class="box"  onkeypress="if(this.value.length == 10) return false;" required>
         </div>

         <!-- min="0" max="9999999999" -->

         <div class="inputBox">
            <span>your email :</span>
            <input type="email" name="email" placeholder="enter your email" class="box" maxlength="50" required>
         </div>
         <div class="inputBox">
            <span>payment method :</span>
            <select name="method" class="box" required>
               <option value="cash on delivery">cash on delivery</option>
               <option value="credit card">credit card</option>
               <option value="paytm">paytm</option>
               <option value="paypal">paypal</option>
            </select>
         </div>
         <div class="inputBox">
            <span>address line 01 :</span>
            <input type="text" name="flat" placeholder="e.g. flat number" class="box" maxlength="50" required>
         </div>
         <div class="inputBox">
            <span>address line 02 :</span>
            <input type="text" name="street" placeholder="e.g. street name" class="box" maxlength="50" required>
         </div>
         <div class="inputBox">
            <span>city :</span>
            <input type="text" name="city" placeholder="e.g. mumbai" class="box" maxlength="50" required>
         </div>
         <div class="inputBox">
            <span>state :</span>
            <!-- <input type="text" name="state" placeholder="e.g. maharashtra" class="box" maxlength="50" required> -->

            <select name="state" class="box" required>
               <option value="Select"  default>Select</option>
               <option value="Andhra Pradesh">Andhra Pradesh</option>
               <option value="Arunachal Pradesh">Arunachal Pradesh</option>
               <option value="Assam">Assam</option>
               <option value="Bihar">Bihar</option>
               <option value="Chhattisgarh">Chhattisgarh</option>
               <option value="Goa">Goa</option>
               <option value="Gujarat">Gujarat</option>
               <option value="Haryana">Haryana</option>
               <option value="Himachal Pradesh">Himachal Pradesh</option>
               <option value="Jharkhand">Jharkhand</option>
               <option value="Karnataka">Karnataka</option>
               <option value="Kerala">Kerala</option>
               <option value="Madhya Pradesh">Madhya Pradesh</option>
               <option value="Maharashtra">Maharashtra</option>
               <option value="Manipur">Manipur</option>
               <option value="Meghalaya">Meghalaya</option>
               <option value="Mizoram">Mizoram</option>
               <option value="Nagaland">Nagaland</option>
               <option value="Rajasthan">Rajasthan</option>
               <option value="Sikkim">Sikkim</option>
               <option value="Tamil Nadu">Tamil Nadu</option>
               <option value="Telangana">Telangana</option>
               <option value="Tripura">Tripura</option>
               <option value="Uttar Pradesh">Uttar Pradesh</option>
               <option value="Uttarakhand">Uttarakhand</option>
               <option value="West Bengal">West Bengal</option>
            </select>

         </div>



         <div class="inputBox">
            <span>country :</span>
            <!-- <input type="text" name="country" placeholder="e.g. India" class="box" maxlength="50" required> -->

             <select name="country" class="box" required>
               <option value="Select">Select</option>
               <option value="Afghanistan">Afghanistan</option>
               <option value="Albania">Albania</option>
               <option value="Argentina">Argentina</option>
               <option value="Australia">Australia</option>
               <option value="Bangladesh">Bangladesh</option>
               <option value="Belgium">Belgium</option>
               <option value="Bhutan">Bhutan</option>
               <option value="Brazil">Brazil</option>
               <option value="Canada">Canada</option>
               <option value="China">China</option>
               <option value="Denmark">Denmark</option>
               <option value="Egypt">Egypt</option>
               <option value="France">France</option>
               <option value="Germany">Germany</option>
               <option value="Iceland">Iceland</option>
               <option value="India">India</option>
               <option value="Indonesia">Indonesia</option>
               <option value="Iran">Iran</option>
               <option value="Iraq">Iraq</option>
               <option value="Ireland">Ireland</option>
               <option value="Israel">Israel</option>
               <option value="Japan">Japan</option>
               <option value="Malaysia">Malaysia</option>
               <option value="Mexico">Mexico</option>
               <option value="Nepal">Nepal</option>
               <option value="Pakistan">Pakistan</option>
               <option value="Russia">Russia</option>
               <option value="Singapore">Singapore</option>
               <option value="Saudi Arabia">Saudi Arabia</option>
               <option value="Sri Lanka">	Sri Lanka</option>
               <option value="Switzerland">Switzerland</option>
               <option value="Turkey">Turkey</option>
               <option value="Ukraine">Ukraine</option>
               <option value="United Kingdom">United Kingdom</option>
               <option value="United States of America">United States of America</option>
            </select>


         </div>
         <div class="inputBox">
            <span>pin code :</span>
            <input type="number" min="0" name="pin_code" placeholder="e.g. 123456" min="0" max="999999" onkeypress="if(this.value.length == 6) return false;" class="box" required>
         </div>
      </div>

      <input type="submit" name="order"  class="btn <?= ($grand_total > 1)?'':'disabled'; ?>" value="place order">

   </form>

</section>


<?php include 'components/footer.php'; ?>

<script src="js/script.js"></script>


</body>
</html>