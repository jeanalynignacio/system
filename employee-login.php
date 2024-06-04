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
                                  } , 5000);
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
                          
                            if ($row["role"] == "Admin") {
                                echo '<body>
                                <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
                                <script>
                                swal("Log in successful!", "", "success")
                                </script>';
                                  echo '<script>
                                 setTimeout(function(){
                                    window.location.href="dashboard.php";
                                } , 5000);
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
                              } , 5000);
                            </script>
                            </body>'; 
                            }
                          }
                        else {

                           
                            $errorMessage = "Wrong Username or Password";
                          
                        }
                       
                }
              }
                
       ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Edit Form</title>
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
                    <span class="details"> Username </span>
                    <input type="text" name="username" id="username" autocomplete="off" value="<?php echo $_POST['username'] ?? ''; ?>" required>
                    <p style="color:red;"><?php echo $userError ?></p>  
                </div>
            </div>
            <div class="user-details">
                <div class="input-box">
                    <span class="details"> Password </span>
                    <input type="password" name="password" id="password" autocomplete="off" required>  
                    <p style="color:red;"><?php echo $passError ?></p>                    
                </div>
            </div>
            <div class="button">
                <input type="submit" class="btn" name="submit" value="Login" required>  
            </div>
            <?php if(isset($logError)): ?>
            <div class="message">
                <p style="color:red;"><?php echo $logError; ?></p>
            </div>
            <?php endif; ?>
        </form>
    </div>
</body>
</html>