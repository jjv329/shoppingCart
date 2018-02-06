<?php
    if (!session_id()){
        session_start();
    }
    if (isset($_POST['checkOut']) && $_POST['checkOut'] == "Check Out"){
        header("Location: /shoppingCart_jvollmer3/checkout.php");
    }

    
?>
<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title>Shopping Cart</title>
        <link href="css/minismall.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        <h3>Minis Mall Shopping Cart - Education Project only</h3>
        <?php
        if (isset($_SESSION['cart'])){
            $cart = $_SESSION['cart'];
        } else {
            $cart = Array();
        }

        if (isset($_POST['remove'])){
            $remove = $_POST['remove'];
        } else {
            $remove = Array();
        }
        
        $totalPrice = 0;
        $prodIDStr = "";
        $_SESSION['numItems'] = 0;
        
        foreach ($_POST as $id => $qty) {         
            if (is_numeric($qty) && $qty > 0 && !isset($remove[$id])){
                $cart[$id] = $qty;  
            } elseif ($qty == 0 || isset($remove[$id])) {
                unset($_POST[$id]);
                unset($cart[$id]);
                unset($_POST["remove[$id]"]);               
            }
        }
        if (empty($cart)){
            echo "<h3>Your shopping cart is empty!</h3>";
        }else{
            require 'dbConnect.php';

            foreach ($cart as $id => $qty) {
                $_SESSION['numItems'] += $qty;
                $prodIDStr = $prodIDStr . $id . ",";
            }
            
            $prodIDStr = substr($prodIDStr, 0, -1);
            try {
                $itemsResult = $pdo->query("SELECT * FROM products WHERE prodid IN ($prodIDStr) ORDER BY category;");
            } catch (PDOException $ex) {
                $error = "Error fetching product information: " . $ex->getMessage();
            }
        ?>
        
        <form action ="cart_jvollmer3.php" method="post">
            <table>
                <tr class="header">
                    <th>Remove</th>
                    <th>Image</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Subtotal</th>
                    <th>Quantity</th>
                    <th style="background-color: #fff">&nbsp;</th>
                </tr>
               <?php
               while ($row = $itemsResult->fetch()){
                $imageLocation = htmlspecialchars(strip_tags($row['loc']));
                $description = htmlspecialchars(strip_tags($row['description']));
                $price = htmlspecialchars(strip_tags($row['price']));
                $productID = strip_tags($row['prodid']);
                $subTotal = $cart[$productID] * $price;
                $totalPrice += $subTotal;
                $price = "\$" . number_format($price,2);
                $subTotal = "\$" . number_format($subTotal,2);
                
                echo <<<TABLEROW
                <tr>
                    <td class="remove"><input type="checkbox" name="remove[$productID]" id="removeProduct$productID" value=1></td>
                    <td><img src="$imageLocation" alt="image of $description"></td>
                    <td class="desc">$description</td>
                    <td class="price">$price</td>
                    <td class="price">$subTotal</td>
                    <td class="qty">
                        <input type="text" name="$productID" id="quantityForProduct$productID" value="$cart[$productID]" size="3em">
                    </td>
                </tr>
TABLEROW;
               }
               ?>
                <tr>
                    <td colspan="3"></td>
                    <td colspan="2" id="totals">Total: <?= "\$" . number_format($totalPrice,2)?></td>
                    <td id="count"><?= $_SESSION['numItems'] ?></td>
                </tr>
                <tr>
                    <td colspan="3"></td>
                    <td colspan="3" id="buttons">
                        <input type="submit" name="checkOut" value="Check Out">
                        <input type="submit" name="updateCart" value="Update">
                    </td>
                </tr>
            </table>
        </form>
        <?php
        $_SESSION['cart'] = $cart;
        }
        ?>
        
        <a href="index.html">Continue Shopping</a>
    </body>
</html>
