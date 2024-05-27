<?php 
session_start();

include("php/config.php");
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
        $result = mysqli_query($con, "SELECT * FROM users WHERE BINARY Username = '$Username' AND BINARY Password = '$Password'") or die("Select Error");
        $row = mysqli_fetch_assoc($result);

        if(is_array($row) && !empty($row)){
            if($Password ==$row['Password']){
                if ($row['status'] == 1){
                    echo '<body>
                    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
                    <script>
                    swal("Log in successful!","","success" )
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
} else{
    $passError = "User not found. Please register.";
}

}}



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
                    <center>Don't have an account? <a href="register.php" style="color:blue">Sign up Now</a></center>
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