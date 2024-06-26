<?php
session_start();
include("php/config.php");

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
        $Email = $_POST['Email'];

        $query = "UPDATE employees SET role = '$role' WHERE Emp_ID = '$Emp_ID'";
        $result2 = mysqli_query($con, $query);

        if ($result2) {
            echo '<script>
                    alert("Record Saved successfully");
                    window.location.href = "employeeRecords.php";
                  </script>';
            exit();
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
                    <span class="details">Last Name:  </span>
                  
                   <input type="text" style="color:black;"  class="details"name="Lastname" value="<?php echo $record['Lastname']; ?>">
                
                </div>
                <div class="input-box">
                    <span class="details" >First Name:  </span>
                  
                    <input type="text" name="Firstname" value="<?php echo $record['Firstname']; ?>">
                </div>
                <div class="input-box">
                    <span class="details" >Email:  </span>
                   
                    <input type="text" name="Email" value="<?php echo $record['Email']; ?>">
                </div>
                <div class="input-box" >
                    <span class="details">Role:  </span>
                    <select name="role" >
                        <?php
                        $roles = array(
                            'Admin',
                            'Community Affairs Officer',
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
                <input type="submit" value="Save" name="submit" onclick="showConfirmation()">
                <input type="button" value="Cancel" name="cancel" onclick="cancelEdit()">
            </div>
        </form>
    </div>
 
    <script type="text/javascript">
        function cancelEdit() {
            window.history.back();
        }
        function showConfirmation() {
            var confirmation = confirm("Are you sure you want to update?");
            if (confirmation) {
                document.getElementById("confirmed").value = "yes";
            } else {
                document.getElementById("confirmed").value = "no";
            }
        }
    </script>
</body>
</html>
