<?php
  session_start();
include("php/config.php");

  if(isset($_SESSION['Emp_ID'])){
        $id = $_SESSION['Emp_ID'];
        $query = mysqli_query($con, "SELECT * FROM employees WHERE Emp_ID=$id");

if($result = mysqli_fetch_assoc($query)){
$res_Id = $result['Emp_ID'];
$res_Fname = $result['Firstname'];
 $res_Lname = $result['Lastname'];
 $res_Email = $result['Email'];
 $res_username = $result['username'];
 $res_password = $result['password_hash'];
}
  }
  else{
    
    header("Location: login.php");
}


if(isset($_POST['submit'])) {
    if(isset($_POST['confirmed']) && $_POST['confirmed'] === "yes") {


    // Check if the user confirmed the update
   // if(isset($_POST['confirmed']) && $_POST['confirmed'] === "yes") {
        $EmpID = $_POST['Emp_ID'];
        $Lastname=$_POST['Lastname'];
        $Firstname=$_POST['Firstname'];
      //  $Email=$_POST['Email'];
        $username=$_POST['username'];
       
        // Construct the update query
        $query = "UPDATE employees
            SET Lastname = '$Lastname',
                Firstname = '$Firstname',
                username = '$username'
              
            WHERE Emp_ID = '$EmpID'";

        $result=mysqli_query($con,$query);

        // Execute the update query
        if ($result) {
           
            echo '<body>
                         <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
                         <script>
                         swal("Updated successfully","","success")
                         .then((value) => {
                             if (value) {
                                 window.location.href = "dashboard.php";
                             }
                         });
                         </script>
                         </body>';   
            exit();
        } else {
           echo "Error updating records: " . mysqli_error($con);
    ?>
    <script>
        window.location.href = "profileadmin.php";
    </script>
    <?php
    exit();
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
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="profileadmin.css" />
</head>
<body>
    <div class="container">
        <div class="title">Edit form</div>
        <form id="editForm" method="post"> 
          
            <div class="user-details">
          
                <div class="input-box">
                
                    <span class="details">Last Name</span>
                    <input type="text" autocomplete="off" required value="<?php echo $res_Lname ?? ''; ?>" id="Lastname" name="Lastname" disabled>
                </div>

                <div class="input-box">
                    <span class="details">First Name</span>
                    <input type="text" autocomplete="off" required value="<?php echo $res_Fname ?? ''; ?>" id="Firstname" name="Firstname" disabled>
                    <input type="hidden" required value="<?php echo $Emp_ID; ?>" name="Emp_ID" />
                </div>
            </div>

            <div class="user-details">
                <div class="input-box">
                    <span class="details">Email</span>
                    <input type="text"  autocomplete="off" required value="<?php echo $res_Email ?? ''; ?>" id="Email"  name="Email" disabled/>
                </div>

                <div class="input-box">
                    <span class="details">Username</span>
                    <input type="text"  autocomplete="off" required value="<?php echo $res_username ?? ''; ?>" id="username"  name="username" disabled/>
                </div>

                
            </div>

            <br> 

            <div class="button-row">
 <input type="button" id="enableFieldsButton" name="btn2" value="EDIT" onclick="enableFields()" />
        
                <!-- Submit button -->
    <input type="submit" value="Done Edit" name="submit" id="submit" onclick="showConfirmation()"  class="hidden" />
<input type="button" value="Back" name="cancel" id="cancel" onclick="cancelEdit()"/>


               
<input type="hidden" name="Emp_ID" value="<?php echo $res_Id; ?>" />

<input type="hidden" name="confirmed" id="confirmed" value="no">

            </div>
        </form>
    </div>
 
        <script>
        function cancelEdit() {
             window.location.href = "dashboard.php";
        }

        function showConfirmation() {
            var confirmation = confirm("Are you sure you want to update?");
        if (confirmation) {
            document.getElementById("confirmed").value = "yes";
            return true; // Allow form submission
        } else {
            document.getElementById("confirmed").value = "no";
            return false; // Prevent form submission
        }
        }
       
        function enableFields() {
        document.getElementById("Lastname").disabled = false;
        document.getElementById("Firstname").disabled = false;
        document.getElementById("Email").disabled = true;
        document.getElementById("username").disabled = false;
       
        
    document.getElementById("submit").classList.remove("hidden");
        document.getElementById("cancel").classList.remove("hidden");
    
    
    }


    </script>
</body>
</html>