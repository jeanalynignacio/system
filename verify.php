<?php 
    session_start();

    include("php/config.php");
if(isset($_GET['Email']) && isset($_GET['code'])){
    $query = "SELECT * FROM users where Email='$_GET[Email]' AND verification_code='$_GET[code]'";
    $result = mysqli_query($con, $query);
    if($result)
    {
        if(mysqli_num_rows($result)==1)
        {                                                                                                                   
        $result_fetch=mysqli_fetch_assoc($result);
        if($result_fetch['status']==0)
        {
            $update = "UPDATE users SET status = 1 WHERE Email='$result_fetch[Email]'";
            if(mysqli_query($con, $update)){
                
                echo '<body>
                        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
                        <script>
                        swal("Registration successful!", "You may now login","","success")
                        .then((value) => {
                            if (value) {
                                window.location.href = "login.php";
                            }
                        });
                        </script>
                        </body>'; 
                
             } else {
                 echo '<div class="alert alert-warning" role="alert">
                 An error occurred while updating the status. Please try again later.
                 </div>';
             }
             } else {
                 echo '<!DOCTYPE html>
                 <html>
                 <head>
                     <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
                     <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
                     <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
                     <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
                 </head>
                 <body>
                     <div class="alert alert-warning" role="alert">
                         Your account is already verified. You can <a href="login.php" class="alert-link">login here</a>.
                     </div>
                 </body>
                 </html>';
             }
             } else {
             echo '<div class="alert alert-danger" role="alert">
             Invalid verification link or account already verified. Please <a href="login.php" class="alert-link">login here</a>.
             </div>';
             }
             } else {
             echo '<div class="alert alert-danger" role="alert">
             Email verification failed! Please try again later.
             </div>';
             }
             } else {
             echo '<div class="alert alert-danger" role="alert">
             Invalid request. Please check your verification link.
             </div>';
             }
             ?>
