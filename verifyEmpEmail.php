<?php 
session_start();
include("php/config.php");

if (isset($_POST['submit'])) {
    $code =  $_POST['code'];
  
    $query = "SELECT * FROM employees WHERE verification_code='$code'";
    $result = mysqli_query($con, $query);
    if ($result) {
        if (mysqli_num_rows($result) == 1) {                                                                                                                   
            $result_fetch = mysqli_fetch_assoc($result);
            if ($result_fetch['status'] == 0) {
                $update = "UPDATE employees SET status = 1 WHERE verification_code='$code'";
                if (mysqli_query($con, $update)) {
                    echo '<body>
                    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
                    <script>
                    swal("Email verified successfully!", "", "success")
                    </script>';
                      echo '<script>
                     setTimeout(function(){
                        window.location.href="employee-login.php";
                    } , 2000);
                  </script>
                  </body>'; 
                } else {
                   
                          echo '<body>
                          <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
                          <script>
                          swal("An error occurred while updating the status.", "Please try again later.", "error")
                          </script>';
                            echo '<script>
                           setTimeout(function(){
                              window.location.href="employee-login.php";
                          } , 2000);
                        </script>
                        </body>';
                }
            } else {
                echo '<body>
                <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
                <script>
                swal(" Your account is already verified.", "You can now log in.", "info")
                </script>';
                  echo '<script>
                 setTimeout(function(){
                    window.location.href="employee-login.php";
                } , 2000);
              </script>
              </body>';
            }
        } else {
            echo '<body>
            <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
            <script>
            swal(" Invalid verification code", "Please enter correct verification code.", "info")
            </script>';
              echo '<script>
             setTimeout(function(){
                window.location.href="verifyEmpEmail.php";
            } , 2000);
          </script>
          </body>';
        }
    } else {
        echo '<div class="alert alert-danger" role="alert">
                Email verification failed! Please try again later.
              </div>';
              echo '<body>
              <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
              <script>
              swal("Email verification failed!", "Please try again later.", "error")
              </script>';
                echo '<script>
               setTimeout(function(){
                  window.location.href="employee-login.php";
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
            <center><header>Verification</header></center>
            <form id="" action="" method="post">
                <div class="field input">
                    <label for="username">Enter verification code here</label>
                    <input type="text" required name="code" id="code" autocomplete="off">
                     
                </div>
                
                <div class="field">
                    <input type="submit" class="btn" name="submit" value="Verify" required >  
                    
                </div>
                <?php if(isset($errorMessage)): ?>
                <div class="message">
                    <p><?php echo $errorMessage ?></p>
                </div>
                <?php endif; ?>
               
            </form>
        </div>
    </div>
   
</body>
</html>
