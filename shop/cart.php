<?php



?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart</title>
</head>
<body>
    
<script type="module">
    import  {cart_manager , add_particular_item , remove_particular_item , items_checkout} from './cart.js';
    cart_manager();
    console.log(items_checkout());
</script>
</body>
</html>