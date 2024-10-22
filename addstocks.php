<?php 
session_start();
include("php/config.php");


if(isset($_SESSION['Emp_ID'])) {
  $EmpID = $_SESSION['Emp_ID'];
  $query = mysqli_query($con, "SELECT * FROM employees WHERE Emp_ID=$EmpID");

  if($result = mysqli_fetch_assoc($query)){
      $res_Id = $result['Emp_ID'];
      $res_Fname = $result['Firstname'];
      $res_Lname = $result['Lastname'];
      $role = $result['role'];
      $branch1 = $result['Office'];
  }
} else {
  header("Location: login.php");
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
   
    <title>Register</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  
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
                  

                    $otp_str = str_shuffle("0123456789");
                    $verification_code= substr($otp_str, 0, 6);
                 
                    if(isset($_POST['submit'])){
                    // receive all input values from the form

               
                    $item = $_POST['item'];
                    $Quantity = $_POST['Quantity'];
                    
                    $expdate = $_POST['expdate'];
                   
            if(empty($Quantity))
                    {
                      array_push($errors, $firstError = "Quantity is required");
                    }
                    if (empty($expdate)) {
                        array_push($errors, $emailError = "Expiration date is required");
                    }
                    
                    if($item=="Select")
                    {
                        array_push($errors, $lastError = "Please select Item to add");
                    }


                    
            
      if (empty($errors)) {


// Use the complete user ID in the INSERT query
$query ="INSERT INTO dialysisstocks( ItemName, Quantity, ExpDate,branch) VALUES ('$item', '$Quantity', '$expdate','$branch1')";
if(mysqli_query($con, $query)){
  
  echo '<body>
  <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
  <script>
  swal("Stocks added successfully", "", "success")
  </script>';
    echo '<script>
   setTimeout(function(){
      window.location.href="dialysis.php";
  } ,3000);
</script>
</body>';
} else {
  // If the query fails, push the error message into the $errors array
  array_push($errors, mysqli_error($con));
}

  }

}            ?>
           
            <header>Add Stocks</header>
            <form action="" method= "post">
                 
            <div class="field input">
                <?php
// Define variables to store selected city and barangay
$selectedRole = $_POST['role'] ?? 'Select';
$selectedBarangay = $_POST['role'] ?? 'Select';
?>
                    <label for = "role" style="font-size: 18px;">Item Name</label>
                    <select id="cityDropdown" name="item" onchange="populateBarangays()">
                    <option value="Select" <?php if ($selectedRole === 'Select') echo 'selected'; ?>>Select</option>
                    <option value="Epogen" <?php if ($selectedRole === 'Epogen') echo 'selected'; ?>>Epogen</option>
                    <option value="Dialyzer" <?php if ($selectedRole === 'Dialyzer') echo 'selected'; ?>>Dialyzer</option>
                   
                      </select>  
                      <p style="color: rgb(150, 26, 26); font-size: 18px;"><?php echo $lastError ?></p>
                             
                <div class="field input">
                    <label for = "Quantity" style="font-size: 18px;">Quantity</label>
                    <input type="text" name="Quantity" id="Quantity" autocomplete="off" value="<?php echo $_POST['Quantity'] ?? ''; ?>">
                    <p style="color: rgb(150, 26, 26); font-size: 18px;"><?php echo $firstError ?></p>
                </div>
                <div class="field input">
    <label for="expdate" style="font-size: 18px;">Select Expiration Date</label>
    <input type="date" name="expdate" id="expdate" 
           autocomplete="off" 
           min="<?php echo date('Y-m-d'); ?>" 
           value="<?php echo $_POST['ExpDate'] ?? ''; ?>">
           <p style="color: rgb(150, 26, 26); font-size: 18px;"><?php echo $emailError ?></p>
</div>

                   
                <div class="field">
                    <input type="submit" class="btn" name="submit" value="Add Stocks">                  
                </div>

                <div class="links">
                <center>
                    <img src="images/back.png" style="vertical-align: middle; height: 15px;width:20px;margin-right:6px; "/><a href="dialysis.php" style="color:rgb(99, 95, 95); text-decoration: none;margin-right:10px;">Back to Home</a></center>

                 </div>
            </form>
            
        </div>
       
    </div>
   
   
</body>
</html>

   
