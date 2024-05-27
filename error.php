<?php 
    session_start();

    include("php/config.php");
    if(!isset($_SESSION['valid'])){
        header("Location: usershomepage.php");

    }
    if(isset($_POST['Back'])){
        header("Location: usershomepage.php");
       exit;
   }
   
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
   
    <link rel="stylesheet" href="error.css" />
</head>
<body>
    <div class="container">
        <div class="box form-box">
       
               
    
                   <div class="STATUS">
               <center>
               <header>
                   <?php
                if (isset($_SESSION['status']))
                {
                    
                    echo $_SESSION['status'];
                    unset($_SESSION['status']);
                }
                ?>
                </header>
                 </center>
               </div>
               
            <form id="" action="" method="post">
            
            <div class="field">
                    <input type="submit" class="btn" name="Back" value="Back to Home" required >                  
                </div>
        </div>
    </div>
</body>
</html>