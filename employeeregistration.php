
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
   
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="emp_registration.css">
</head>

<body>
            
    <div class = "container">
        <div class="box form-box">
    

        <?php 
       
        // Define variables to store selected city and barangay
    
  
                include ("php/config.php");
                $errors = []; // Initialize $errors as an empty array

                    $lastError = "";
                    $firstError = "";
                    $emailError = "";
                    $unameError = "";
                    $passError = "";
                    $roleError = "";
                    $Errors = "";

                    $otp_str = str_shuffle("0123456789");
                    $verification_code= substr($otp_str, 0, 7);
                 
                    if(isset($_POST['submit'])){
                    // receive all input values from the form

               
                    $Lastname = $_POST['Lastname'];
                    $Firstname = $_POST['Firstname'];
                     $Email = NULL;
                    $Username = $_POST['Username'];
                    $Password = $_POST['Password'];
                    $role = $_POST['role'];
                   
            if(empty($Lastname))
                    {
                      array_push($errors, $lastError = "Lastname is required");
                    }
                    if(empty($Firstname))
                    {
                      array_push($errors, $firstError = "Firstname is required");
                    }
                    
                    if(empty($Username))
                    {
                      array_push($errors,  $unameError = "Username is required");
                    }
                    elseif (strlen($Username) < 6) {
                      array_push($errors,  $unameError = "Username must be minimum of 6 characters");
                      }
                    if(empty($Password))
                    {
                        array_push($errors, $passError = "Password is required");
                    } elseif (!preg_match('/^(?=.*\d)(?=.*[A-Za-z])(?=.*[^A-Za-z0-9]).{8,}$/', $Password)) {
                        array_push($errors, $passError = "Password must be at least 8 characters long and contain at least one number, one letter, and one special character.");
                    }
                    if($role=="Select")
                    {
                        array_push($errors, $roleError = "Please select Role");
                    }


                    
            
      if (empty($errors)) {


// Use the complete user ID in the INSERT query
$query ="INSERT INTO employees( Lastname, Firstname, Username, password_hash,role,verification_code,status) VALUES ('$Lastname', '$Firstname', '$Username', '$Password','$role','$verification_code',0)";
if(mysqli_query($con, $query)){
  
   echo '<body>
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
        <script>
        swal("Employee account added successfully!", "Please verify your email.", "success")
        </script>';
          echo '<script>
         setTimeout(function(){
            window.location.href="employeeRecords.php";
        } , 5000);
      </script>
      </body>';
       
 
} else {
  // If the query fails, push the error message into the $errors array
  array_push($errors, mysqli_error($con));
}

  }

}            ?>
           
            <header>Add employee account</header>
            <form action="" method= "post">
                <div class="field input">
                    <label for = "Lastname" style="font-size: 18px;">Last Name</label>
                    <input type="text" name="Lastname" id="Lastname" autocomplete="off" value="<?php echo $_POST['Lastname'] ?? ''; ?>">
                    <p style="color: rgb(150, 26, 26); font-size: 18px;"><?php echo $lastError ?></p>          
                </div>
                <div class="field input">
                    <label for = "Firstname" style="font-size: 18px;">First Name</label>
                    <input type="text" name="Firstname" id="Firstname" autocomplete="off" value="<?php echo $_POST['Firstname'] ?? ''; ?>">  
                    <p style="color: rgb(150, 26, 26); font-size: 18px;"><?php echo $firstError ?></p>                    
                </div>
                
                <div class="field input">
                <?php
// Define variables to store selected city and barangay
$selectedRole = $_POST['role'] ?? 'Select';
$selectedBarangay = $_POST['role'] ?? 'Select';
?>
                    <label for = "role" style="font-size: 18px;">Role</label>
                    <select id="cityDropdown" name="role" onchange="populateBarangays()">
                    <option value="Select" <?php if ($selectedRole === 'Select') echo 'selected'; ?>>Select</option>
                    <option value="Admin" <?php if ($selectedRole === 'Admin') echo 'selected'; ?>>Admin</option>
                    <option value="Community Affairs Officer" <?php if ($selectedRole === 'Community Affairs Officer') echo 'selected'; ?>>Community Affairs Officer</option>
                    
                      </select>  
                      <p style="color: rgb(150, 26, 26); font-size: 18px;"><?php echo $roleError ?></p>
                             
               
              <!--  <div class="field input">
                    <label for = "Email" style="font-size: 18px;">Email</label>
                    <input type="text" name="Email" id="Email" autocomplete="off" value="<?php echo $_POST['Email'] ?? ''; ?>">                  
                    <p style="color: rgb(150, 26, 26); font-size: 18px;"><?php echo $emailError ?></p>
                  </div>-->
                <div class="field input">
                    <label for = "Username" style="font-size: 18px;">Username</label>
                    <input type="text" name="Username" id="Username" autocomplete="off" value="<?php echo $_POST['Username'] ?? ''; ?>">                  
                    <p style="color: rgb(150, 26, 26); font-size: 18px;"><?php echo $unameError ?></p>
                  </div>
                <div class="field input">
                    <label for = "Password" style="font-size: 18px;">Password</label>
                    <input type="password" name="Password" id="Password" autocomplete="off" value="<?php echo $_POST['Password'] ?? ''; ?>">                  
                    <p style="color: rgb(150, 26, 26); font-size: 18px;"><?php echo $passError ?></p>
                  </div>
                <div class="field">
                    <input type="submit" class="btn" name="submit" value="Add account">                  
                </div>

                <div class="links">
                 </div>
            </form>
            
        </div>
       
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>

   