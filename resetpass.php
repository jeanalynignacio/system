<?php 
session_start();
include("php/config.php");
$errors = []; // Initialize $errors as an empty array


if(isset($_SESSION['Id'])) {
    $Id = $_SESSION['Id'];
    $query = mysqli_query($con, "SELECT * FROM users WHERE Id=$Id");
    if($result = mysqli_fetch_assoc($query)){
        $res_Id = $result['Id'];

    }
}  
$passError = "";
if (isset($_POST['submit'])) {
    $code =  $_POST['code'];
    $hashed_password = password_hash($code, PASSWORD_BCRYPT);
                  
    $Id =  $_POST['Id'];
    if(empty($code))
    {
        array_push($errors, $passError = "Password is required");
      } elseif (!preg_match('/^(?=.*\d)(?=.*[A-Z])(?=.*[a-z])(?=.*[^A-Za-z0-9]).{8,}$/', $code)) {
        array_push($errors, $passError = "Password must be at least 8 characters long and contain at least one number, one uppercase letter, one lowercase letter, and one special character.");
    }

    if (empty($errors)) {
    
                $update = "UPDATE users SET Password = '$hashed_password' WHERE Id='$Id'";
                if (mysqli_query($con, $update)) {
                    echo '<body>
                    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
                    <script>
                    swal("Password updated successfully", "You may now log in", "success")
                    </script>';
                      echo '<script>
                     setTimeout(function(){
                        window.location.href="login.php";
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
                              window.location.href="index.php";
                          } , 2000);
                        </script>
                        </body>';
                }
            } 
        } 
  


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="resetpass.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <title>Reset Password</title>
</head>
<body>
    <div class="container">
        <div class="box form-box">
            <header>New Password</header>
            <form id="" action="" method="post">
                <div class="field input">
                    <label for="username">Enter your new password</label>
                    <input type="text" required name="code" id="code" autocomplete="off">
                    <span class="toggle-password"><i class="fas fa-eye" id="togglePassword"></i></span>
                    <p style="color:red;"><?php echo $passError ?></p>                    
                </div>
                <input type="hidden" name="Id" value="<?php echo $Id; ?>">

                
                <div class="field">
                    <input type="submit" class="btn" name="submit" value="Submit" required >  
                    

                </div>

               
                <?php if(isset($errorMessage)): ?>
                <div class="message">
                    <p><?php echo $errorMessage ?></p>
                </div>
                <?php endif; ?>
               
            </form>
        </div>
    </div>
    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#code');

        togglePassword.addEventListener('click', function (e) {
            // toggle the type attribute
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            // toggle the eye slash icon
            this.classList.toggle('fa-eye-slash');
        });
    </script>
</body>
</html>
