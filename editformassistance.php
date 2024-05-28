<?php 
session_start();
include("php/config.php");

// Check if Beneficiary_Id is set in the URL parameter
if(isset($_POST['Beneficiary_Id'])) {
    // Retrieve the Beneficiary_Id from the URL parameter
    $beneID = $_POST['Beneficiary_Id'];
} else {
    echo "Useszr ID is not set.";
    exit; // Exit if ID is not set
}

if(isset($_SESSION['Emp_ID'])) {
  $EmpID = $_SESSION['Emp_ID'];

} else {
  echo "UserSSID is not set.";
  exit; // Exit if ID is not set
}
  
// Fetch data from the database
$SQL = "SELECT b.*, t.*, f.*
        FROM beneficiary b
        INNER JOIN transaction t ON b.Beneficiary_Id = t.Beneficiary_Id
        INNER JOIN financialassistance f ON b.Beneficiary_Id = f.Beneficiary_ID
        WHERE b.Beneficiary_Id = '$beneID'";

$result = mysqli_query($con, $SQL);

// Check if any rows are returned
if(mysqli_num_rows($result) == 0) {
    echo "No data found for the given Beneficiary ID.";
    exit; // Exit if no data found
}

// Fetch the first row (assuming there's only one beneficiary with the given ID)
$record = mysqli_fetch_assoc($result);

if(isset($_POST['submit'])) {
  // Check if the user confirmed the update
  if(isset($_POST['confirmed']) && $_POST['confirmed'] === "yes") {
      $beneID=$_POST['Beneficiary_Id'];
      $Date=$_POST['Date'];
      $transaction_time=$_POST['time'];
      $Given_Sched=($_POST['Given_Sched'] != '') ? $_POST['Given_Sched'] : '0000-00-00'; // Set to '0000-00-00' if empty
      $TransactionType=$_POST['TransactionType'];
      $FA_Type=$_POST['FA_Type'];
       $Status=$_POST['Status'];
      
  
      
      // Construct the update query
      $query = "UPDATE financialassistance f
      INNER JOIN beneficiary b ON b.Beneficiary_Id = f.Beneficiary_ID
      INNER JOIN transaction t ON t.Beneficiary_Id = f.Beneficiary_ID
      SET t.Date = '$Date',
          t.transaction_time = '$transaction_time',
          t.Given_Sched = '$Given_Sched',
          t.TransactionType = '$TransactionType',
          f.FA_Type = '$FA_Type',
          t.Status = '$Status',
      t.Emp_ID='$EmpID'

      WHERE b.Beneficiary_Id = '$beneID'";

$result2=mysqli_query($con,$query);
      // Execute the update query
     // Execute the update query
if ($result2) {?>
  <script>
alert("update successfully");
</script>
<?php

  header("Location: assistance.php");
  exit();
} else {
  echo "Error updating records: " . mysqli_error($con);
  header("Location: assistance.php");
  exit();
}

    
  }
}
if(isset($_POST['email'])) {
  $Status=$_POST['Status'];
 
  $_SESSION['Beneficiary_Id'] = $beneID;
    
  
  $_SESSION['Status'] = $_POST['Status'];
  header("Location: schedform.php");
  exit();
}

?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Edit Form</title>
    <link rel="stylesheet" href="editformassistance.css" />
  </head>
  <body>
    <div class="container">
      <div class="title"> Edit form </div>
      <form id="editForm"  method="post"> <!-- Changed method to POST -->
      <input type="hidden" name="Beneficiary_Id" value="<?php echo $beneID; ?>">
      <input type="hidden" name="Emp_ID" value="<?php echo $EmpID; ?>">


   
      
        <div class="user-details1">
          <div class="input-box">
            <span class="details" style="color:  #f5ca3b;"> Date of Application: </span>
             <span id="calendar" style="color:white; margin-top:10px;"><?php echo $record['Date']; ?></span>

        </div>

        <div class="input-box">
          <span class="details" style="color:  #f5ca3b;">Time of Application:</span>
          <span id="time" style="color:  white;"><?php echo date("h:i A", strtotime($record['transaction_time'])); ?></span>
            <input type="hidden" required value="<?php echo $record['Beneficiary_ID']; ?>" name="Beneficiary_ID" disabled/>
        </div>
</div>

        <div class="user-details">
          <div class="input-box">
            <span class="details"  style="color:  #f5ca3b;"> Full Name </span>
            <span class="details"><?php echo $record['Firstname'] . " " . $record['Lastname']; ?></span>
  </div>

      
        
          <div class="input-box">
            <span class="details"> Transaction Type </span>
            <span class="details"> <?php echo $record['TransactionType'] ; ?> </span>
           
          <!--  <select name="TransactionType">
             
              <option echo ($record['TransactionType'] == 'Online Appointment') ? 'selected' : ''; ?>>Online Appointment</option>
        <option echo ($record['TransactionType'] == 'Walk-in') ? 'selected' : ''; ?>>Walk-in</option>
            </select>-->
         
</div>

          <div class="input-box">
            <span class="details"> Financial Assistance Type </span>
            <select name="FA_Type">
            <?php
// Array of hospitals
$FA_type = array(
    'Burial',
    'Chemotheraphy & Radiation',
    'Dialysis',
    'Medicine'
);

// Loop through the array to generate options
foreach ($FA_type as $FA_type) {
    // Check if the current hospital matches the record's hospital
    $selected = ($record['FA_Type'] == $FA_type) ? 'selected' : '';
    // Output the option with hospital name and selected attribute if matched
    echo "<option $selected>$FA_type</option>";
}
?>
            </select>
          </div>

          <div class="input-box">
            <span class="details">Status </span>
            <select name="Status">
            <?php
// Array of hospitals
$status = array(
    
    'Pending for Payout',
    'Pending for Requirements',
    'For Schedule',
    'Done'
  
);

// Loop through the array to generate options
foreach ($status as $status) {
    // Check if the current hospital matches the record's hospital
    $selected = ($record['Status'] == $status) ? 'selected' : '';
    // Output the option with hospital name and selected attribute if matched
    echo "<option $selected>$status</option>";
}
?>
            </select>

</div>
         
          <div class="input-box">
           
        <span class="details">Given Schedule </span>
        <input type="date" id="calendar" name="Given_Sched"  value="<?php echo $record['Given_Sched']; ?>"/>
        </div>
</div>
          <br>
          <input type="hidden" name="confirmed" id="confirmed" value="no">
          <br> 
      
          <div class="button-row">
  <!-- Submit button -->
  <input type="submit" value="Send Email" name="email" onclick="email()" />
 
  <input type="submit" value="Done Edit" name="submit" onclick="showConfirmation()" />
  <!-- Cancel button  php endforeach; ?>-->
  <input type="button" value="Cancel" name="cancel" onclick="cancelEdit()" />
</div>

       
        </div>
      </form>
    </div>

    
<script type="text/javascript">
    function cancelEdit() {
        // Redirect to the previous page
        window.history.back();
      }
     function editRecord(beneficiaryId) {
        // Set the value of the hidden input field
        document.getElementById('beneficiaryIdInput').value = beneficiaryId;
        // Submit the form
    }

    function showConfirmation() {
    var confirmation = confirm("Are you sure you want to update?");
    if (confirmation) {
        // If user clicks OK, submit the form
       
            document.getElementById("confirmed").value = "yes";
    } else {
      
            document.getElementById("confirmed").value = "no";    }
}

    document.getElementById('time').addEventListener('input', function() {
        var timeInput = document.getElementById('time').value;
        var time = new Date('1970-01-01T' + timeInput);
        var formattedTime = time.toLocaleTimeString('en-US', {hour: 'numeric', minute: 'numeric', hour12: true});
        document.getElementById('time').value = formattedTime;
    });



</script>
  </body>
</html>