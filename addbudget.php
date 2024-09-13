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
 $role=$result['role'];
}
  }
  else{
    
    header("Location: employee-login.php");
}
if(isset($_POST['submit'])) {
// Check if the user confirmed the update
if(isset($_POST['confirmed']) && $_POST['confirmed'] === "yes") {
$Date=$_POST['Date'];
$time=$_POST['time'];
$Donor=$_POST['Donor'];
$Amount=$_POST['Amount'];
$assistancetype =$_POST ['assistancetype'];
$branch =$_POST ['branch'];

// Use the complete user ID in the INSERT query
$query ="INSERT INTO donation(Date,Time, DonorName, Amount, AssistanceType, Branch) VALUES ('$Date', '$time',
'$Donor', '$Amount', '$assistancetype', '$branch')";
$result2=mysqli_query($con,$query);
if ($result2) {
  $checkQuery = "SELECT * FROM budget WHERE branch = '$branch' && AssistanceType='$assistancetype'";
  $checkResult = mysqli_query($con, $checkQuery);

  if (mysqli_num_rows($checkResult) > 0) {
      // Branch already exists, update budget table
      $updateQuery = "UPDATE budget SET TotalAmount = TotalAmount + $Amount, RemainingBal = RemainingBal + $Amount WHERE branch = '$branch' && AssistanceType = '$assistancetype'";
      $result3=mysqli_query($con, $updateQuery);
  } else {
      // Branch does not exist, insert into budget table
      $insertQuery = "INSERT INTO budget(branch, TotalAmount,RemainingBal,AssistanceType) VALUES ('$branch', '$Amount','$Amount','$assistancetype')";
      $result3=mysqli_query($con, $insertQuery);
  }
  if ($result3) {
  echo '<body>
  <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
  <script>
  swal("Funds Added successfully","","success")
  .then((value) => {
      if (value) {
          window.location.href = "budget.php";
      }
  });
  </script>
  </body>';

}
} else {
echo "Error adding records: " . mysqli_error($con);
header("Location: patients-records.php");
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
<title>Add Form</title>
<link rel="stylesheet" href="addbeneficiary.css" />
</head>
<body>
<script>
        document.addEventListener('DOMContentLoaded', (event) => {
            var today = new Date().toISOString().split('T')[0];
            document.getElementsByName("date")[0].setAttribute('value', today);
        });
        
    </script>
<div class="container">
<div class="title"> Add Funds </div>
<form id="addForm" method="post">
<div class="user-details">
<div class="input-box">
<span class="details"> Date </span>
<input type="date" id="calendar" name="Date" />
<input type="hidden" name="Beneficiary_Id" disabled/>
</div>
<div class="input-box">
<span class="details"> Time </span>
<input type="text" id="time" required name="time" />
</div>
<div class="user-details">
<div class="input-box">
<span class="details"> Funder's Name: </span>
<input type="text" required name="Donor" autocomplete=off />
</div>
<div class="input-box">
<span class="details"> Amount </span>
<input type="text" required name="Amount" autocomplete=off />
</div>

<div class="input-box">
                <?php
// Define variables to store selected city and barangay
$selectedassistance = $_POST['assistancetype'] ?? 'Select';

?>
                    <span class="details">Assistance Type</span>
                    <select id="cityDropdown" name="assistancetype" >
                    <option value="Select" <?php if ($selectedassistance === 'Select') echo 'selected'; ?>>Select</option>
                    <option value="Financial Assistance-Burial" <?php if ($selectedassistance === 'Financial Assistance-Burial') echo 'selected'; ?>>Financial Assistance-Burial</option>
                    <option value="Financial Assistance-Chemotherapy & Radiation" <?php if ($selectedassistance === 'Financial Assistance-Chemotherapy & Radiation') echo 'selected'; ?>>Financial Assistance-Chemotherapy & Radiation</option>
                    <option value="Financial Assistance-Dialysis" <?php if ($selectedassistance === 'Financial Assistance-Dialysis') echo 'selected'; ?>>Financial Assistance-Dialysis</option>
                     <option value="Hospital Bills" <?php if ($selectedassistance === 'Hospital Bills') echo 'selected'; ?>>Hospital Bills</option>
                    </select>  
                             
                </div>
        
                <div class="input-box" style="margin-left:40px;">
                <?php
// Define variables to store selected city and barangay
$selectedbranch = $_POST['branch'] ?? 'Select';

?>
                    <span class="details">Branch</span>
                    <select id="cityDropdown" name="branch" >
                    <option value="Select" >Select</option>
                    <option value="PGB-Balanga Branch" <?php if ($selectedbranch === 'PGB-Balanga Branch') echo 'selected'; ?>>PGB-Balanga Branch</option>
                    <option value="PGB-Dinalupihan Branch" <?php if ($selectedbranch === 'PGB-Dinalupihan Branch') echo 'selected'; ?>>PGB-Dinalupihan Branch</option>
                    <option value="PGB-Hermosa Branch" <?php if ($selectedbranch === 'PGB-Hermosa Branch') echo 'selected'; ?>>PGB-Hermosa Branch</option>
                    <option value="PGB-Mariveles Branch" <?php if ($selectedbranch === 'PGB-Mariveles Branch') echo 'selected'; ?>>PGB-Mariveles Branch</option>
                  
                      </select>  
                             
                </div>           
                
                          

<br> <input type="hidden" name="confirmed" id="confirmed"
value="no">
<div class="button-row">
<!-- Submit button -->
<input type="submit" value="Add" name="submit" onclick="showConfirmation()" />
<!-- Cancel button -->
<input type="button" value="Cancel" name="cancel" onclick="cancelEdit()" />
</div>
</div>
</form>
</div>
<script type="text/javascript">
function cancelEdit() {
// Redirect to the previous page
window.location.href = "budget.php";
}
function showConfirmation() {
var confirmation = confirm("Are you sure you want to add?");
if (confirmation) {
// If user clicks OK, submit the form
document.getElementById("confirmed").value = "yes";
}
else {
document.getElementById("confirmed").value = "no"; }
}
document.addEventListener("DOMContentLoaded", function() {
var now = new Date();
var hours = now.getHours();
var minutes = now.getMinutes();
var seconds = now.getSeconds();
// Add a leading zero to single-digit minutes and seconds
minutes = minutes < 10 ? '0' + minutes : minutes;
seconds = seconds < 10 ? '0' + seconds : seconds;
var currentTime = hours + ':' + minutes + ':' + seconds;
document.getElementById('time').value = currentTime;
});
</script>
</body></html>
