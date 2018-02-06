<?php
    // if a session is not already in progress, start one...
    if (!session_id()){
        session_start();
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
        <title>Shopping Cart Catalog - Education Project Only</title>
        <link href="css/minismall.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        <h2>Product Catalog - Education Project Only</h2>
        
        <?php
        // If a session variable named 'numItems' has never been set then initialize to 0
        if (!isset($_SESSION['numItems'])){
            $_SESSION['numItems'] = 0;
        }
        ?>
        <p>Your shopping cart contains <?= $_SESSION['numItems']?> item(s)</p>
        
        <a href="cart_jvollmer3.php">View your cart</a> | 
        <a href="index.html">Back to product categories</a>
        
        <?php
        // Connect to db server and select our DB
        require 'dbConnect.php';
        //
        //Query the DB for all category ids
        try {
            $categoryResult = $pdo->query("SELECT catid FROM categories;");
        } catch (PDOException $ex) {
            $error = "Error fetching category info: " . $ex->getMessage();
        }
        $catIDs = Array(); //empty array
        $ctr = 0;
        
        //Step through resultset and store each categoryid in our array
        
        while ($row = $categoryResult->fetch()) {
            $catIDs[$ctr] = $row['catid'];
            $ctr += 1;
        }
        
        //Check if incoming category is valid
        
        if (isset($_GET['cat']) && in_array($_GET['cat'], $catIDs)){
            $cat = $_GET['cat'];
        } else {
            $cat = 1; 
        }
        
        $_SESSION['cat'] = $cat; //remember the chosen category id
        
        //Query for all products in the chosen category and display them in a table
        try {
            $itemsResult = $pdo->query("SELECT * FROM products WHERE category = $cat;");
        } catch (PDOException $ex) {
            $error = "Error fetching product info: " . $ex->getMessage();
        }
        
        ?>
        <br><br>
        <form action ="cart_jvollmer3.php" method="post">
            <table>
                <tr class="header">
                    <th>Image</th>
                    <th>Description</th>
                    <th>USD</th>
                    <th style="background-color: #fff">&nbsp;</th>
                </tr>
               <?php
               //step through result set of products 
               while ($row = $itemsResult->fetch()){
                 //Convert any special HTML characters to their HTML entities. Example: & --> &amp;
                   
                 //Also, strip out any HTML tags found in the data.
                $imageLocation = htmlspecialchars(strip_tags($row['loc']));
                $description = htmlspecialchars(strip_tags($row['description']));
                $price = htmlspecialchars(strip_tags($row['price']));
                
                $price = "\$" . number_format($price,2);
                $productID = strip_tags($row['prodid']);
                
                //Set $qty to contain what is in our session cart array variable.
                if(isset($_SESSION['cart'][$productID])){
                    $qty = $_SESSION['cart'][$productID];
                }else{
                    $qty = 0;
                }
                echo <<<TABLEROW
                <tr>
                    <td><img src="$imageLocation" alt="image of $description"></td>
                    <td class="desc">$description</td>
                    <td class="price">$price</td>
                    <td class="qty">
                        <label for="quantityForProduct$productID">Qty</label>
                        <input type="text" name="$productID" id="quantityForProduct$productID" value="$qty" size="3em">
                    </td>
                </tr>
TABLEROW;
               }
               ?>
                <tr>
                    <td colspan="4" id="submitCell">
                        <input type="submit" name="addCart" value="Add Items to cart">
                    </td>
                </tr>
            </table>
        </form>
        <a href="cart_jvollmer3.php">View your cart</a> | 
        <a href="index.html">Back to product categories</a>
    </body>
</html>
