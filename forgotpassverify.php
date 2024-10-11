<?php 
session_start();
include("php/config.php");

if (isset($_POST['submit2'])) {
    $code =  $_POST['code'];
  
    $query = "SELECT * FROM users WHERE verification_code='$code'";
    $result = mysqli_query($con, $query);
    if ($result) {
        if (mysqli_num_rows($result) == 1) {                                                                                                                   
            $result_fetch = mysqli_fetch_assoc($result);
            if ($result_fetch['status'] == 1) {
              
                    echo '<body>
                    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
                    <script>
                    swal("Verification code is correct", "Please add your new password", "success")
                    </script>';
                      echo '<script>
                     setTimeout(function(){
                        window.location.href="resetpass.php";
                    } , 2000);
                  </script>
                  </body>'; 
                  $_SESSION['Id'] = $result_fetch['Id'];

                } else {
                   
                          echo '<body>
                          <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
                          <script>
                          swal("Your email is not verified", "Please verify you email.", "error")
                          </script>';
                            echo '<script>
                           setTimeout(function(){
                              window.location.href="verification.php";
                          } , 2000);
                        </script>
                        </body>';
                }
            } 
        } else {
            echo '<body>
            <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
            <script>
            swal(" Invalid verification code", "Please enter correct verification code.", "info")
            </script>';
              echo '<script>
             setTimeout(function(){
                window.location.href="forgotpassverify.php";
            } , 2000);
          </script>
          </body>';
        }
    } 
    

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <title>Verification</title>
</head>
<body>
    <div class="container">
        <div class="box form-box">
            <header>Verification</header>
            <form id="" action="" method="post">
                <div class="field input">
                    <label for="username">Enter verification code here</label>
                    <input type="text" required name="code" id="code" autocomplete="off">
                     
                </div>
                
                <div class="field">
                    <input type="submit" class="btn" name="submit2" value="Verify" required >  
                    
                </div>
                <?php if(isset($errorMessage)): ?>
                <div class="message">
                    <p><?php echo $errorMessage ?></p>
                </div>
                <?php endif; ?>
                <center>  <a href="forgotpass.php" style="color:gray; text-decoration: none; display: inline-flex; align-items: center;margin-top:7px;">
        <img src="images/back.png" style="height: 15px; width: 20px; margin-right: 6px;" />
        Back
    </a>  <center>
            </form>
        </div>
    </div>
   
</body>
</html>
