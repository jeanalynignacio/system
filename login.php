<?php 
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

include ("php/config.php");
$userError = "";
$passError = "";

if(isset($_POST['submit'])){
    $Username = mysqli_real_escape_string($con, $_POST['username']);
    $Password = mysqli_real_escape_string($con, $_POST['password']);

    if(empty($Username)){
        $userError = "Username is required";
    }
    if(empty($Password)){
        $passError = "Password is required";
    }

    if(empty($userError) && empty($passError)) {
        $result = mysqli_query($con, "SELECT * FROM users WHERE BINARY Username = '$Username' ") or die("Select Error");
        $row = mysqli_fetch_assoc($result);

        if(is_array($row) && !empty($row)){
            if (password_verify($Password, $row['Password'])) {
                if ($row['status'] == 1){
                    echo '<body>
                                    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
                                    <script>
                                    swal("Log in successful!", "", "success")
                                    </script>';
                                      echo '<script>
                                     setTimeout(function(){
                                        window.location.href="usershomepage.php";
                                    } , 2000);
                                  </script>
                                  </body>';
                     
        $_SESSION['valid'] = $row['Lastname'];
        $_SESSION['firstname'] = $row['Firstname'];
        $_SESSION['middlename'] = $row['Middlename'];
        $_SESSION['birthday'] = $row['Birthday'];
        $_SESSION['contactnumber'] = $row['Contactnumber'];
        $_SESSION['province'] = $row['Province'];
        $_SESSION['citymunicipality'] = $row['CityMunicipality'];
        $_SESSION['Barangay'] = $row['Barangay'];
        $_SESSION['housenostreet'] = $row['HousenoStreet'];
        $_SESSION['email'] = $row['Email'];
        $_SESSION['username'] = $row['Username'];
        $_SESSION['password'] = $row['Password'];
        $_SESSION['id'] = $row['Id'];
        
        exit(); // Add exit() after header redirect to prevent further execution
    } else{
        $passError = "Email not verified. Please verify your email.";

      }
    }
     else {
        $errorMessage = "Wrong Username or Password";
    }
}     $query = "SELECT * FROM employees WHERE username = '$Username' " ;
$result2 = mysqli_query($con, "SELECT * FROM employees WHERE BINARY username = '$Username' ") or die("Select Error");
    


if (mysqli_num_rows($result2) == 1) {
    $row = mysqli_fetch_assoc($result2);
    if($Password ==$row['password_hash']){
      if ($row['Email'] !== NULL && $row['Email'] !== "" ) {
        $_SESSION['valid'] = $row['username'];

        $_SESSION['Emp_ID'] = $row['Emp_ID'];
        
       if ($row['status'] == 1){
            // Login successful   $_SESSION['Email'] = $Email;
           
                echo '<body>
                <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
                <script>
                swal("Log in successful!", "", "success")
                </script>';
                  echo '<script>
                 setTimeout(function(){
                    window.location.href="dashboard.php";
                } , 2000);
              </script>
              </body>';
              
           
       } else{
        $Email = $row['Email'];
        $res_Id = $row['Emp_ID']; 
        // Define variables to store selected city and barangay
        $otp_str = "123456789"; // String na hindi naglalaman ng 0 sa unang character
        $otp_str_with_zero = "0123456789"; // Buong range ng digits
        
        do {
            $verification_code = substr(str_shuffle($otp_str_with_zero), 0, 6);
        } while ($verification_code[0] == '0'); // Siguraduhin na hindi magsisimula sa 0
        $query1 = "UPDATE employees SET Email ='$Email', verification_code='$verification_code', status='0' WHERE Emp_ID='$res_Id'";
                
        if(mysqli_query($con, $query1)){
        require 'PHPMailer/src/Exception.php';
        require 'PHPMailer/src/PHPMailer.php';
        require 'PHPMailer/src/SMTP.php';

        $mail = new PHPMailer(true);

        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'bataanpgbsap@gmail.com'; // Your Gmail address
        $mail->Password = 'cmpp hltn mxuc tcgl'; // Your Gmail password or App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('bataanpgbsap@gmail.com', 'PGB-SAP');
        $mail->addAddress($Email); // Add a recipient

        // Content
        $mail->isHTML(true); // Set email format to HTML
        $mail->Subject = 'Email Verification';
        $mail->Body = "Good Day! This is your verification code: $verification_code 
        <p> If you did not request for this code. Please ignore this email.</p>";
     

        if($mail->send()){
        
            echo '<body>
            <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
            <script>
            swal("Email not verified. Please verify your email.", "", "success")
            </script>';
              echo '<script>
             setTimeout(function(){
                window.location.href="verifyEmpEmail.php";
            } , 2000);
          </script>
          </body>';
           

        }

        }
    }
      }
        
        else {
            // Email not verified
            $result = mysqli_query($con, "SELECT * FROM employees WHERE username = '$Username' AND password_hash = '$Password' ") or die("Select Error");
            $row = mysqli_fetch_assoc($result);
            
            
            if(is_array($row) && !empty($row)){
                $_SESSION['Emp_ID'] = $row['Emp_ID'];
                
           
            echo '<body>
            <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
            <script>
            swal("Please add your Email", "", "info")
            </script>';
            echo '<script>
            setTimeout(function(){
                window.location.href="addingemail.php";
            }, 3000);
            </script>
            </body>';

          }

  }
           
  }  else {
            // Email not verified
            $errorMessage = "Incorrect Password. Please try again";               
                         } 
        } 
      
        else{
          
            $errorMessage = "User not found. Please register.";
}



if(empty($errorMessage) /*&& empty($passError) && empty($logError)*/ ) {
    $result = mysqli_query($con, "SELECT * FROM employees WHERE username = '$Username' AND password_hash = '$Password' ") or die("Select Error");
    $row = mysqli_fetch_assoc($result);
    
    
    if(is_array($row) && !empty($row)){
        $_SESSION['firstname'] = $row['Firstname'];
       $_SESSION['email'] = $row['Email'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['password'] = $row['password_hash'];
        $_SESSION['Emp_ID'] = $row['Emp_ID'];
        $_SESSION['valid'] = $row['Lastname'];
      
    
   
}
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
    <title>Login</title>
</head>
<body>
    <div class="container">
        <div class="box form-box">
            <header>Login</header>
            <form id="" action="" method="post">
                <div class="field input">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" autocomplete="off" value="<?php echo $_POST['username'] ?? ''; ?>">
                    <p style="color:red;"><?php echo $userError ?></p>  
                </div>
                <div class="field input">
                    <label for="password">Password</label>
                    <div class="password-wrapper">
                        <input type="password" name="password" id="password" autocomplete="off" value="<?php echo $_POST['password'] ?? ''; ?>" >
                        <span class="toggle-password"><i class="fas fa-eye" id="togglePassword"></i></span>
                    </div>
                    <p style="color:red;"><?php echo $passError ?></p>                    
                </div>
                <div class="field">
                    <input type="submit" class="btn" name="submit" value="Login" required >  
                    <input type='hidden' name='userId' value='$res_Id'>
                </div>
                <?php if(isset($errorMessage)): ?>
                <div class="message">
                    <p><?php echo $errorMessage ?></p>
                </div>
                <?php endif; ?>
                <div class="links">
        <a href="forgotpass.php" style="color:#5089f3;">Forgot password?</a><br><br>
                   
                    <center>Don't have an account? <a href="register.php" style="color:blue">Sign up Now</a></center><br>

                    <center>
                    <img src="images/back.png" style="vertical-align: middle; height: 15px;width:20px;margin-right:6px; "/><a href="index.php" style="color:rgb(99, 95, 95); text-decoration: none;margin-right:10px;">Back to Home</a></center>

                </div>
            </form>
        </div>
    </div>
    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');

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
