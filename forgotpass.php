<?php 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

session_start();
include("php/config.php");

if (isset($_POST['submit'])) {
    $code =  $_POST['code'];  // The email address provided by the user

    // Function to generate a 6-digit verification code that doesn't start with 0
    function generateVerificationCode() {
        $otp_str_with_zero = "0123456789"; // Full range of digits
        do {
            $verification_code = substr(str_shuffle($otp_str_with_zero), 0, 6);
        } while ($verification_code[0] == '0'); // Ensure the first digit is not 0
        return $verification_code;
    }

    $verification_code = generateVerificationCode();

    // Helper function to send email
    function sendVerificationEmail($email, $verification_code, $subject, $body) {
        require 'PHPMailer/src/Exception.php';
        require 'PHPMailer/src/PHPMailer.php';
        require 'PHPMailer/src/SMTP.php';
        
        $mail = new PHPMailer(true);
        
        try {
            //Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'bataanpgbsap@gmail.com'; // Gmail address
            $mail->Password = 'cmpp hltn mxuc tcgl';     // Gmail App Password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            //Recipients
            $mail->setFrom('bataanpgbsap@gmail.com', 'PGB-SAP');
            $mail->addAddress($email);     // Add the recipient

            //Content
            $mail->isHTML(true); // Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body = $body;

            // Send email
            $mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    // Try fetching the user from the `users` table
    $query = "SELECT * FROM users WHERE Email='$code'";
    $result = mysqli_query($con, $query);
    
    if ($result && mysqli_num_rows($result) == 1) {
        $result_fetch = mysqli_fetch_assoc($result);
        if ($result_fetch['status'] == 1) {
            // User is verified, update verification code
            $update = "UPDATE users SET verification_code = '$verification_code' WHERE Email='$code'";
            if (mysqli_query($con, $update)) {
                // Send verification email
                $subject = 'Forgot Password';
                $body = "Good Day! This is your verification code for password reset: $verification_code
                        <p>If you did not request for this code, please ignore this email.</p>";

                if (sendVerificationEmail($code, $verification_code, $subject, $body)) {
                    echo '<body>
                    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
                    <script>
                    swal("Verification code sent", "Please check your email", "success")
                    </script>';
                    echo '<script>
                         setTimeout(function(){
                            window.location.href="forgotpassverify.php";
                        } , 3000);
                      </script>
                      </body>';
                }
            }
        } else {
            // User is not verified, handle this case
            echo '<body>
            <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
            <script>
            swal("Your account is not verified.", "Please verify your account.", "error")
            </script>';
            echo '<script>
                 setTimeout(function(){
                    window.location.href="verification.php";
                } , 3000);
              </script>
              </body>';
        }
    } else {
        // Try fetching the employee from the `employees` table
        $query2 = "SELECT * FROM employees WHERE Email='$code'";
        $result2 = mysqli_query($con, $query2);
        
        if ($result2 && mysqli_num_rows($result2) == 1) {
            $result_fetch2 = mysqli_fetch_assoc($result2);
            if ($result_fetch2['status'] == 1) {
                // Employee is verified, update verification code
                $update = "UPDATE employees SET verification_code = '$verification_code' WHERE Email='$code'";
                if (mysqli_query($con, $update)) {
                    // Send verification email for employee
                    $subject = 'Forgot Password';
                    $body = "Good Day! This is your verification code for password reset: $verification_code
                            <p>If you did not request for this code, please ignore this email.</p>";

                    if (sendVerificationEmail($code, $verification_code, $subject, $body)) {
                        echo '<body>
                        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
                        <script>
                        swal("Verification code sent", "Please check your email", "success")
                        </script>';
                        echo '<script>
                             setTimeout(function(){
                                window.location.href="forgotpassverifyemp.php";
                            } , 3000);
                          </script>
                          </body>';
                    }
                }
            } else {
                // Employee not verified
                echo '<body>
                <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
                <script>
                swal("Your account is not verified.", "Please verify your account.", "error")
                </script>';
                echo '<script>
                     setTimeout(function(){
                        window.location.href="verifyEmpEmail.php";
                    } , 3000);
                  </script>
                  </body>';
            }
        } else {
            // Email not found in either table
            echo '<body>
            <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
            <script>
            swal("Email not found", "Please enter a correct email.", "info")
            </script>';
            echo '<script>
                 setTimeout(function(){
                    window.location.href="forgotpassemp.php";
                } , 3000);
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
    <link rel="stylesheet" href="login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <title>Email</title>
</head>
<body>
    <div class="container">
        <div class="box form-box">
            <header>Email</header>
            <form id="" action="" method="post">
                <div class="field input">
                    <label for="username">Enter your email</label>
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
                <center>  <a href="index.php" style="color:gray; text-decoration: none; display: inline-flex; align-items: center;margin-top:7px;">
        <img src="images/back.png" style="height: 15px; width: 20px; margin-right: 6px;" />
        Back to Home
    </a>  <center>
            </form>
        </div>
    </div>
   
</body>
</html>
