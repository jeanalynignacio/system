<?php 
session_start();
include("php/config.php");

// Check if Beneficiary_Id is set in the URL parameter
if(isset($_POST['Beneficiary_Id'])) {
    // Retrieve the Beneficiary_Id from the URL parameter
    $beneID = $_POST['Beneficiary_Id'];
  } else {
    echo "User ID is not set.";
}
  
    $SQL = "SELECT b.*, t.*, h.*
            FROM beneficiary b
            INNER JOIN transaction t ON b.Beneficiary_Id = t.Beneficiary_Id
            INNER JOIN hospitalbill h ON b.Beneficiary_Id = h.Beneficiary_ID
            WHERE b.Beneficiary_Id = '$beneID'";

    $result = mysqli_query($con, $SQL);
    $res_data = array(); // Array to store fetched records

    while($row = mysqli_fetch_assoc($result)){
        $res_data[] = $row; // Append each fetched row to the array
    }



if(isset($_POST['submit'])) {
  // Check if the user confirmed the update
  if(isset($_POST['confirmed']) && $_POST['confirmed'] === "yes") {
      $beneId=$_POST['Beneficiary_Id'];
       $Date=$_POST['Date'];
       $transaction_time=$_POST['transaction_time'];
      $TransactionType=$_POST['TransactionType'];
      $hospital=$_POST['PartneredHospital'];
      $billamount=$_POST['billamount'];
      $Status=$_POST['Status'];
    
       $Given_Sched=($_POST['Given_Sched'] != '') ? $_POST['Given_Sched'] : '0000-00-00'; // Set to '0000-00-00' if empty
     
      
      // Construct the update query
      $query = "UPDATE hospitalbill h
      INNER JOIN beneficiary b ON b.Beneficiary_Id = h.Beneficiary_ID
      INNER JOIN transaction t ON t.Beneficiary_Id = h.Beneficiary_ID
      SET t.Date = '$Date',
          t.transaction_time = '$transaction_time',
          t.TransactionType = '$TransactionType',
          h.PartneredHospital = '$hospital',
          h.billamount = '$billamount',
          t.Status = '$Status',
          t.Given_Sched = '$Given_Sched'
      WHERE b.Beneficiary_Id = '$beneId'";

$result2=mysqli_query($con,$query);
      // Execute the update query
     // Execute the update query
if ($result2) {

  header("Location: hospital.php");
  exit();
} else {
  echo "Error updating records: " . mysqli_error($con);
  header("Location: dashboard.php");
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
    <link rel="stylesheet" href="editformhospitals.css" />
  </head>
  <body>
    <div class="container">
      <div class="title"> Edit form </div>
      <form id="editForm"  method="post"> <!-- Changed method to POST -->
      <input type="hidden" name="Beneficiary_Id" value="<?php echo $beneID; ?>">
      <?php foreach($res_data as $record): ?>
      <?php 
               
              
               $query = mysqli_query($con, "SELECT t.Date, t.transaction_time, b.Beneficiary_Id, b.Lastname, b.Firstname, t.TransactionType, h.PartneredHospital, h.billamount, t.Status,t.Given_Sched  FROM hospitalbill h INNER JOIN beneficiary b ON b.Beneficiary_Id = h.Beneficiary_ID INNER JOIN transaction t ON t.Beneficiary_Id = h.Beneficiary_ID WHERE b.Beneficiary_Id='$beneID'" );
              
               while($result = mysqli_fetch_assoc($query)){
                   $res_Date = $result['Date'];
                   $res_transaction_time = $result['transaction_time'];
                   $res_beneID = $result['Beneficiary_Id'];
                   $res_Lname = $result['Lastname'];
                   $res_Fname = $result['Firstname'];
                   $res_transactype = $result['TransactionType'];
                   $res_hospitalname = $result['PartneredHospital'];
                   $res_bill = $result['billamount'];
                   $res_Status = $result['Status'];
                   $res_Given_Sched = $result['Given_Sched'];
                 
               }
                    ?>  
      
        <div class="user-details">
          <div class="input-box">
            <span class="details"> Date </span>
            <input type="date" id="calendar" name="Date" required value="<?php echo $record['Date']; ?>" />
        </div>
        
        <div class="input-box">
          <span class="details">Time</span>
            <input type="text" id="time" required value="<?php echo $record['transaction_time']; ?>" name="time">
            <input type="hidden" required value="<?php echo $record['Beneficiary_Id']; ?>" name="Beneficiary_Id" disabled/>
        </div>
 
        <div class="user-details">
          <div class="input-box">
            <span class="details"> Last Name </span>
            <input type="text" required value="<?php echo $record['Lastname']; ?>" name="Lastname" disabled/>
        </div>

          <div class="input-box">
            <span class="details"> First Name </span>
            <input type="text" required value="<?php echo $record['Firstname']; ?>" name="Firstname" disabled/>
          </div>

          <div class="input-box">
            <span class="details"> Transaction Type </span>
            <select name="TransactionType">
             
              <option <?php echo ($record['TransactionType'] == 'Online Appointment') ? 'selected' : ''; ?>>Online Appointment</option>
        <option <?php echo ($record['TransactionType'] == 'Walk-in') ? 'selected' : ''; ?>>Walk-in</option>
            </select>
          </div>

          <div class="input-box">
            <span class="details"> Hospital </span>
            <select name="PartneredHospital">
            <?php
// Array of hospitals
$hospitals = array(
    'Bataan Doctors Hospital & Medical Center',
    'Balanga Medical Center Corporation',
    'Bataan Peninsula Medical Center',
    'Bataan St. Joseph Hospital & Medical Center',
    'Isaac & Catalina Medical Center',
    'Mt. Samat Medical Center',
    'Orion St. Michael Hospital',
    'Jose B. Lingad Memorial General Hospital',
    'Lung Center of the Philippines',
    'National Children\'s Hospital',
    'National Kidney & Transplant Institute',
    'Philippine General Hospital',
    'Philippine Heart Center',
    'The Philippines Children Medical Center'
  
);

// Loop through the array to generate options
foreach ($hospitals as $hospital) {
    // Check if the current hospital matches the record's hospital
    $selected = ($record['PartneredHospital'] == $hospital) ? 'selected' : '';
    // Output the option with hospital name and selected attribute if matched
    echo "<option $selected>$hospital</option>";
}
?>
            </select>
                 </div>
                    <div class="user-details">
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
            <span class="details"> Hospital Bill </span>
            <input type="text" required value="<?php echo $record['billamount']; ?>" name="billamount" />
    
          </div>
        

          <div class="input-box">
           
        <span class="details">Given Schedule </span>
        <input type="date" id="calendar" name="Given_Sched"  value="<?php echo $record['Given_Sched']; ?>"/>
</div>

</div>
          <input type="hidden" name="confirmed" id="confirmed" value="no">
          <br> 
          <div class="button-row">
  <!-- Submit button -->
  <input type="submit" value="Done Edit" name="submit" onclick="showConfirmation()" />
  <!-- Cancel button -->
  <input type="button" value="Cancel" name="cancel" onclick="cancelEdit()" />
</div>

        <?php endforeach; ?>
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


</script>
  </body>
</html>
