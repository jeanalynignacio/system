<?php 

    session_start();
    
                include("php/config.php");
                $userError = "";
                $passError = "";
                $logError = "";
                if(isset($_POST['submit'])){
                    $Username = mysqli_real_escape_string($con, $_POST['username']);
                    $Password = mysqli_real_escape_string($con, $_POST['password']);
                   
                    $query = "SELECT * FROM employees WHERE username = '$Username' " ;
                    $result = mysqli_query($con, $query);
                   
                    if (mysqli_num_rows($result) == 1) {
                        $row = mysqli_fetch_assoc($result);
                        if($Password ==$row['password_hash']){
                          if ($row['Email'] !== NULL && $row['Email'] !== "" ) {
                            $_SESSION['valid'] = $row['username'];

                            $_SESSION['Emp_ID'] = $row['Emp_ID'];
                            
                           if ($row['status'] == 1){
                                // Login successful   $_SESSION['Email'] = $Email;
                                if ($row["role"] == "Admin") {
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
                                  
                                } else {
                                    
                                  echo '<body>
                                  <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
                                  <script>
                                  swal("Log in successful!", "", "success")
                                  </script>';
                                    echo '<script>
                                   setTimeout(function(){
                                      window.location.href="dashboard.php";
                                  } , 3000);
                                </script>
                                </body>'; 
                                }
                            
                           } else{
                              $logError = "Email not verified. Please verify your email.";
              
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
                                 $logError = "Incorrect Password. Please try again";               
                                             } 
                            } 
                          
                            else{
                              
                       $logError = "User not found. Please register.";
                  }
                
                
                
                    if(empty($userError) && empty($passError) && empty($logError) ) {
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
       ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login Form</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="employee-login.css" />
</head>
<style>
    @import url("https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,600;1,600&display=swap");
</style>
<body>
    <div class="container">
        <div class="title">
            <img src="images/logo-png.png" style="width: 60px; height: auto" />
            Log In
        </div>
        <form action="#" method="POST">
            <div class="user-details">
                <div class="input-box">
                    <span class="details" style="margin-top:30px;margin-left:8px;"> Username </span>
                    <input type="text" name="username" id="username" autocomplete="off" value="<?php echo $_POST['username'] ?? ''; ?>" required>
                    <p style="color:red;"><?php echo $userError ?></p>  
                </div>
            </div>
           
            <div class="field input input-container">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" autocomplete="off" value="<?php echo $_POST['password'] ?? ''; ?>" required > 
                    <i class="fas fa-eye toggle-password" onclick="togglePasswordVisibility()"></i>
                    <p style="color:red;"><?php echo $passError ?></p>                    
                </div>
                <?php if(isset($logError)): ?>
            <div class="message">
                <p style="color:red; margin-top:20px;"><?php echo $logError; ?></p>
            </div>
            <?php endif; ?>

            <a href="forgotpassemp.php" style="color:#ffeacb; margin-left:20px;">Forgot password?</a><br><br>
       
            <div class="button" style="margin-bottom:10px;margin-top:-2px; margin-bottom:15px; ">
                <input type="submit" style="width:210px; margin-left:32px;" class="btn" name="submit" value="Login" required>  
            </div>
            <center>  <a href="index.php" style="color: white;font-size:14px;text-decoration: none; display: inline-flex; align-items: center;margin-top:7px;">
        <img src="images/back2.png" style="height: 15px; width: 20px; margin-right: 6px; margin-bottom:1px;"  />
        Back to Home
    </a>  <center>
           
        </form>
    </div>
    <script>
        function togglePasswordVisibility() {
            const passwordField = document.getElementById('password');
            const togglePassword = document.querySelector('.toggle-password');
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                togglePassword.classList.remove('fa-eye');
                togglePassword.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                togglePassword.classList.remove('fa-eye-slash');
                togglePassword.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>
