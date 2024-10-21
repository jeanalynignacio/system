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
        $roleemp = $result['role'];
        $branch1 = $result['Office'];
    }
  } else {
    header("Location: login.php");
    exit();
  }


// Check if Emp_ID is set in the URL parameter
if(isset($_POST['Emp_ID'])) {
    $Emp_ID = $_POST['Emp_ID'];

} else {
    echo "User ID is not set.";
    exit();
}

$SQL = "SELECT * FROM employees WHERE Emp_ID = '$Emp_ID'";
$result = mysqli_query($con, $SQL);
$res_data = array();
while($row = mysqli_fetch_assoc($result)) {
    $res_data[] = $row;
}

if(isset($_POST['submit'])) {
    if(isset($_POST['confirmed']) && $_POST['confirmed'] === "yes") {

        $Lastname = $_POST['Lastname'];
        $Firstname = $_POST['Firstname'];
        $role = $_POST['role'];
      

        $query = "UPDATE employees SET role = '$role' WHERE Emp_ID = '$Emp_ID'";
        $result2 = mysqli_query($con, $query);

        if ($result2) {
         
                  echo '<body>
                  <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
                  <script>
                  swal("Record Saved successfully","","success")
                  .then((value) => {
                      if (value) {
                          window.location.href = "employeeRecords.php";
                      }
                  });
                  </script>
                  </body>';
            
        } else {
            echo "Error updating records: " . mysqli_error($con);
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Form</title>
    <link rel="stylesheet" href="editemployee.css">
</head>
<body>
    <div class="container">
     <div class="title">Employee Record</div>
        <form id="editForm" method="post">
            <input type="hidden" name="Emp_ID" value="<?php echo $Emp_ID; ?>">
            <?php foreach($res_data as $record): ?>
                <div class="user-details">
<div class="input-box">
<input type="hidden" id="roleemp" name="roleemp"  disabled value="<?php echo isset($roleemp) ? htmlspecialchars($roleemp) : ''; ?>">
              
                    <span class="details">Last Name:  </span>
                  
                   <input type="text" id="Lastname" style="color:black; margin-top:2px"  class="details"name="Lastname" disabled value="<?php echo $record['Lastname']; ?>">
                
                </div>
                <div class="input-box">
                    <span class="details" >First Name:  </span>
                  
                    <input type="text" id="Firstname" name="Firstname"  disabled value="<?php echo $record['Firstname']; ?>">
                </div>
                <div class="input-box">
                    <span class="details" >Email:  </span>
                   
                    <input type="text" disabled id="Email" name="Email" value="<?php echo $record['Email']; ?>">
                </div>

                 <div class="input-box" >
                    <span class="details">Role:  </span>
                    <select id="role" disabled name="role" >
                        <?php
                        $roles = array(
                            'Admin',
                            'Community Affairs Officer',
                            'PSWDO Employee',
                            'DSWD Employee'
                        );

                        foreach ($roles as $role) {
                            $selected = ($record['role'] == $role) ? 'selected' : '';
                            echo "<option $selected>$role</option>";
                        }
                        ?>
                    </select>
                </div>
            <?php endforeach; ?>
            <input type="hidden" name="confirmed" id="confirmed" value="no">
            <div class="button-row">
            <input type="button" id="enableFieldsButton" name="btn2" value="EDIT" onclick="enableFields()" />
 
                <input type="submit" id="save" value="Save" name="submit" onclick="showConfirmation()" class="hidden"  style="display:none;">
                <input type="button" id="cancel" value="Cancel" name="cancel" onclick="cancelEdit()">
            </div>
        </form>
    </div>
 
    <script type="text/javascript">
        function cancelEdit() {
           window.location.href = "employeeRecords.php";
  
        }
        function showConfirmation() {
            var confirmation = confirm("Are you sure you want to update?");
            if (confirmation) {
                document.getElementById("confirmed").value = "yes";
            } else {
                document.getElementById("confirmed").value = "no";
            }
        }

        
        function enableFields() {
        document.getElementById("Lastname").disabled = false;
        document.getElementById("Firstname").disabled = false;
        document.getElementById("Email").disabled = true;
        document.getElementById("role").disabled = false;
       
        
  //  document.getElementById("save").classList.remove("hidden");
        document.getElementById("cancel").classList.remove("hidden");
        document.getElementById("save").style.display = "block";   document.getElementById("enableFieldsButton").style.display = "none"; 
     
    
    }
    </script>
</body>
</html>
