<?php 
     use PHPMailer\PHPMailer\PHPMailer;
     use PHPMailer\PHPMailer\SMTP;
     use PHPMailer\PHPMailer\Exception;
session_start();
include("php/config.php");

if (isset($_POST['submit'])) {
    $code =  $_POST['code'];
    $otp_str = str_shuffle("0123456789");
    $verification_code= substr($otp_str, 0, 10);

    $query = "SELECT * FROM employees WHERE Email='$code'";
    $result = mysqli_query($con, $query);
    if ($result) {
        if (mysqli_num_rows($result) == 1) {                                                                                                                   
            $result_fetch = mysqli_fetch_assoc($result);
            if ($result_fetch['status'] == 1) {
                $update = "UPDATE employees SET verification_code = $verification_code WHERE Email='$code'";
                if (mysqli_query($con, $update)) {
                 
    require 'PHPMailer/src/Exception.php';
    require 'PHPMailer/src/PHPMailer.php';
    require 'PHPMailer/src/SMTP.php';

   
    $mail = new PHPMailer(true);

        //Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'bataanpgbsap@gmail.com'; // Your Gmail address
        $mail->Password = 'cmpp hltn mxuc tcgl'; // Your Gmail password or App Password
        $mail->SMTPSecure = 'PHPMailer::ENCRYPTION_STARTTLS';
        $mail->Port = 587;
    
        //Recipients
        $mail->setFrom('bataanpgbsap@gmail.com', 'PGB-SAP');
        $mail->addAddress($code);     //Add a recipient
    
        //Content
        $mail->isHTML(true); // Set email format to HTML
        $mail->Subject = 'Forgot Password';
        $mail->Body = "Good Day! This is your verification code for password reset: $verification_code 
             <p> If you did not request for this code. Please ignore this email.</p>";
          
    
       if($mail->send()){
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
                echo '<body>
                <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
                <script>
                swal(" Your account is not verified.", "Please verify your account.", "error")
                </script>';
                  echo '<script>
                 setTimeout(function(){
                    window.location.href="verifyEmpEmail.php";
                } , 3000);
              </script>
              </body>';
            }
        } else {
            echo '<body>
            <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
            <script>
            swal(" Email not found", "Please enter correct email.", "info")
            </script>';
              echo '<script>
             setTimeout(function(){
                window.location.href="forgotpassemp.php";
            } , 3000);
          </script>
          </body>';
        }
    } else {
      
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
    <title>Email Verification</title>
</head>
<body>
    <div class="container">
        <div class="box form-box">
            <header  style="background:#1477d2;">Email Verification</header>
            <form id="" action="" method="post">
                <div class="field input">
                    <label for="username">Enter your email</label>
                    <input type="text" required name="code" id="code" autocomplete="off">
                     
                </div>
                
                <div class="field">
                    <input type="submit"  style="background:#1477d2;" class="btn" name="submit" value="Verify" required >  
                    
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
