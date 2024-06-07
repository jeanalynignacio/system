<?php 
session_start();
include("php/config.php");
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

// Check if Beneficiary_Id is set in the URL parameter
if(isset($_POST['Beneficiary_Id'])) {
    $Status = $_POST['Status'];
    // Retrieve the Beneficiary_Id from the URL parameter
    $beneID = $_POST['Beneficiary_Id'];
  } else {
    echo "User ID is not set.";
}
  
if(isset($_SESSION['Emp_ID'])) {
  $EmpID = $_SESSION['Emp_ID'];
  $query = mysqli_query($con, "SELECT * FROM employees WHERE Emp_ID=$EmpID");

  if($result = mysqli_fetch_assoc($query)){
      $res_Id = $result['Emp_ID'];
      $res_Fname = $result['Firstname'];
      $res_Lname = $result['Lastname'];
      $role = $result['role'];
  }
} else {
  header("Location: employee-login.php");
  exit();
}
    $SQL = "SELECT b.*, t.*, h.*
            FROM beneficiary b
            INNER JOIN transaction t ON b.Beneficiary_Id = t.Beneficiary_Id
            INNER JOIN hospitalbill h ON b.Beneficiary_Id = h.Beneficiary_ID
            WHERE b.Beneficiary_Id = '$beneID'";

    $result = mysqli_query($con, $SQL);
 
    if(mysqli_num_rows($result) == 0) {
        echo "No data found for the given Beneficiary ID.";
        exit; // Exit if no data found
    }
    $record = mysqli_fetch_assoc($result);


    if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
   
  // Check if the user confirmed the update
  if(isset($_POST['confirmed']) && $_POST['confirmed'] === "yes") {
   
    $beneId=$_POST['Beneficiary_Id'];
        $Status=$_POST['Status'];
       $EmpID = $_POST['Emp_ID'];
      
      // Construct the update query
   
if ($Status == "For Validation") {
         date_default_timezone_set('Asia/Manila');
        $Date = date('Y-m-d'); // Set the current date for Given_Sched
        $transaction_time = date('H:i:s'); // Set the current date and time for transaction_time
        $billamount=$_POST['billamount'];
    
        $query = "UPDATE hospitalbill h
        INNER JOIN beneficiary b ON b.Beneficiary_Id = h.Beneficiary_ID
        INNER JOIN transaction t ON t.Beneficiary_Id = h.Beneficiary_ID
        SET 
            t.Given_Sched = '$Date',
            t.Given_Time = '$transaction_time',
            t.Emp_ID='$EmpID',
            h.billamount = '$billamount',
            t.Status = '$Status'
           
        WHERE b.Beneficiary_Id = '$beneId'";

        
}
elseif ($Status == "For Schedule") {

    $transaction_time = $_POST['time'];
    $Date = ($_POST['Given_Sched'] != '') ? $_POST['Given_Sched'] : '0000-00-00'; // Set to '0000-00-00' if empty
 
    $Status = "For Validation";

    $query = "UPDATE hospitalbill h
    INNER JOIN beneficiary b ON b.Beneficiary_Id = h.Beneficiary_ID
    INNER JOIN transaction t ON t.Beneficiary_Id = h.Beneficiary_ID
    SET 
        t.Given_Sched = '$Date',
        t.Given_Time = '$transaction_time',
        t.Emp_ID='$EmpID',
        t.Status = '$Status'
       
    WHERE b.Beneficiary_Id = '$beneId'";


}
elseif ($Status == "Pending for Payout") {
    date_default_timezone_set('Asia/Manila');
    $Date = date('Y-m-d'); // Set the current date for Given_Sched
    $transaction_time = date('H:i:s'); // Set the current date and time for transaction_time
    $Status = "For Validation";
   
     $query = "UPDATE hospitalbill h
     INNER JOIN beneficiary b ON b.Beneficiary_Id = h.Beneficiary_ID
     INNER JOIN transaction t ON t.Beneficiary_Id = h.Beneficiary_ID
     SET 
         t.Given_Sched = '$Date',
         t.Given_Time = '$transaction_time',
         t.Emp_ID='$EmpID',
         t.Status = '$Status'
        
     WHERE b.Beneficiary_Id = '$beneId'";

}
elseif ($Status == "For Re-schedule") {
    $transaction_time = $_POST['time'];
    $Date = ($_POST['Given_Sched'] != '') ? $_POST['Given_Sched'] : '0000-00-00'; // Set to '0000-00-00' if empty
 
    $Status = "For Validation";
 
$query = "UPDATE hospitalbill h
INNER JOIN beneficiary b ON b.Beneficiary_Id = h.Beneficiary_ID
INNER JOIN transaction t ON t.Beneficiary_Id = h.Beneficiary_ID
SET 
    t.Given_Sched = '$Date',
    t.Given_Time = '$transaction_time',
    t.Emp_ID='$EmpID',
    t.Status = '$Status'
   
WHERE b.Beneficiary_Id = '$beneId'";

}


else{
    date_default_timezone_set('Asia/Manila');
    $Date = date('Y-m-d'); // Set the current date for Given_Sched
    $transaction_time = date('H:i:s'); // Set the current date and time for transaction_time

    $query = "UPDATE hospitalbill h
    INNER JOIN beneficiary b ON b.Beneficiary_Id = h.Beneficiary_ID
    INNER JOIN transaction t ON t.Beneficiary_Id = h.Beneficiary_ID
    SET 
        t.Given_Sched = '$Date',
        t.Given_Time = '$transaction_time',
        t.Emp_ID='$EmpID',
        t.Status = '$Status'
       
    WHERE b.Beneficiary_Id = '$beneId'";
}
// Construct the update query

$result2=mysqli_query($con,$query);
      
     // Execute the update query
if ($result2) {

  $Status = $_POST['Status'];
            if ($Status !== "Pending for Requirements" && $Status !== "For Validation") { // Check if status is not "Pending for Requirements" or "For Validation"
   
            require 'PHPMailer/src/Exception.php';
            require 'PHPMailer/src/PHPMailer.php';
            require 'PHPMailer/src/SMTP.php';

            $mail = new PHPMailer(true);
            $lastName = $record['Lastname'];
           
            $Email = $record['Email'];
            $status= $_POST['Status'];

            try {
                // Server settings
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'bataanpgbsap@gmail.com'; // Your Gmail address
                $mail->Password = 'cmpp hltn mxuc tcgl'; // Your Gmail password or App Password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                // Recipients
                $mail->setFrom('bataanpgbsap@gmail.com', 'PGB-SAP');
                $mail->addAddress($Email); // Add a recipient

                // Content
                $mail->isHTML(true); // Set email format to HTML
                if($status == 'For Schedule') {
                    $employeeName = $_POST['EmpName'];
                    $mail->Subject = 'Schedule for requirements checking';
                    $mail->Body = "
                    <html>
                    <body>
                    <p>Dear Mr./Ms./Mrs. $lastName,</p>
                    <p>I am writing to inform you that your request for scheduling has been approved.</p>
                    <p>Your schedule has been set for $Date at $transaction_time. We kindly expect your presence on the said date.</p>
                    <p> We kindly expect your presence on the said date.<br><br></p>
                    <p>   If you are unable to attend the scheduled appointment, you may request a new appointment by clicking on this  <a href='http://localhost/public_html/requestresched.php'> link. </a> Please ensure that your reasons are valid and clearly explained so that your request can be considered.<br> 
                   Please note that your reasons may need to be verified to avoid any inconvenience to other clients and our schedule. Thank you for your understanding and cooperation.</p>
                    <p>Best regards,<br>$employeeName</p>
                   
                    <p>Provincial Government of Bataan - Special Assistance Program</p>
                    </body>
                    </html>
                    ";

                
                } elseif($status == 'Pending for Payout') {
                    $employeeName = $_POST['EmpName'];
                    $mail->Subject = 'Pending for Payout';
                    $mail->Body = "
                        <html>
                        <body>
                        <p>Dear Mr./Ms./Mrs. $lastName,</p>
                        <p>Your assistance request is currently pending for payout.</p>
                        <p>We are processing your application, and you will receive your financial assistance soon.</p>
                        <p>Thank you for your patience and cooperation.</p>
                        <p>Best regards,</p>
                        <p>$employeeName</p>
                        <p>Provincial Government of Bataan - Special Assistance Program</p>
                        </body>
                        </html>
                    ";
                }elseif($status == 'For Payout') {
                    $employeeName = $_POST['EmpName'];
                    $mail->Subject = 'For Payout';
                    $mail->Body = "
                        <html>
                        <body>
                        <p>Dear Mr./Ms./Mrs. $lastName,</p>
                        <p>Your assistance request is currently for payout on $Date at $transaction_time.</p>
                        Kindly proceed to PGB-Hermosa Branch<br>
                        <p>Thank you for your patience and cooperation.</p>
                        <p>Best regards,</p>
                        <p>$employeeName</p>
                        <p>Provincial Government of Bataan - Special Assistance Program</p>
                        </body>
                        </html>
                    ";
                }elseif($status == 'For Re-schedule') {
                    $employeeName = $_POST['EmpName'];
                    $mail->Subject = 'For Re-schedule';
                    $mail->Body = "
                        <html>
                        <body>
                        <p>Dear Mr./Ms./Mrs. $lastName,</p>
                        <p>Your request for re-schedule has been accepted. Your new schedule is on $Date at $transaction_time.</p>
                        <p> We kindly expect your presence on the said date.<br><br></p>
<p>Best regards,<br>$employeeName</p>
                   
                    <p>Provincial Government of Bataan - Special Assistance Program</p>
                    </body>
                    </html>
                    ";
                }

                    $mail->send();
                echo '<body>
                        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
                        <script>
                        swal("Update and email send successful","","success")
                        .then((value) => {
                            if (value) {
                                window.location.href = "hospital.php";
                            }
                        });
                        </script>
                        </body>';
            } catch (Exception $e) {
                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
         } else{ 
            echo '<body>
            <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
            <script>
            swal("Updated successfully","","success")
            .then((value) => {
                if (value) {
                    window.location.href = "hospital.php";
                }
            });
            </script>
            </body>';}

        } else {
            echo "Error updating records: " . mysqli_error($con);
            header("Location: hospital.php");
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
      <div class="title" style="margin-bottom:10px; margin-left:270px;"> Edit form </div>
      <form id="editForm"  method="post"> <!-- Changed method to POST -->
      <input type="hidden" name="Beneficiary_Id" value="<?php echo $beneID; ?>">
      <input type="hidden" name="Emp_ID" value="<?php echo $EmpID; ?>">

                <div class="user-details1">
                <div class="input-box">
                    <span class="details" style="margin-top:20px; color:#f5ca3b;">Date of Application:</span>
                    <span id="calendar" style="color:white; margin-top:10px;"><?php echo $record['Date']; ?></span>
                </div>

         
                <div class="user-details">
                <div class="input-box">
                    <span class="details" style="color:  #f5ca3b;">Full Name</span>
                    <input disabled type = "text" required name="EmpName" value = "<?php echo $record['Firstname'] . " " . $record['Lastname']; ?>" > 
                    </div> 

          <div class="input-box">
                    <span class="details" style="color:  #f5ca3b;">Transaction Type</span>
                    <input disabled type = "text" required value = "<?php echo $record['TransactionType']; ?>">
                </div>
                
                <div class="input-box">
                    <span class="details" style="color:  #f5ca3b;">Hospital</span>
                    <input disabled type = "text" required value = "<?php echo $record['PartneredHospital']; ?>">
                 
                </div>
       
<div class="input-box">
                    <span class="details" style="color:  #f5ca3b;">Status</span>
                    <select id="status" name="Status" onchange="handleStatusChange()">
                        <?php
                        $status = array('For Schedule','For Validation','Pending for Requirements','Pending for Payout' ,'For Payout','Request for Re-schedule','For Re-schedule', 'Done');
                        foreach ($status as $stat) {
                            $selected = ($record['Status'] == $stat) ? 'selected' : '';
                            echo "<option $selected>$stat</option>";
                        }
                        ?>
                    </select>
                </div>
            
          </div>
          <input type="hidden" name="confirmed" id="confirmed" value="no">
          <br> 
       
          <div id="requirements" style="display: none;"></div>
       <div id="emailFormat" class="emailformat">
                    <!-- Email content will be updated based on the selected status -->
                </div>
           
                
           
                <div class="button-row">
                <input type="submit" value="Submit" name="submit" onclick="showConfirmation()" />
                <input type="button" value="Cancel" name="cancel" onclick="cancelEdit()" />
            </div>
            
        </form>
    </div>
    
  <script>
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
   
    
    function handleStatusChange() {
     
    var status = document.getElementById('status').value;
    var emailFormat = document.getElementById('emailFormat');
    var requirements = document.getElementById('requirements');
   
    emailFormat.innerHTML = '';
    requirements.style.display = 'none'; // Hide requirements by default
  
    if (status === 'For Schedule') {
        emailFormat.innerHTML = `
         <div style = "color: black; padding:15px; background:white; margin-top:20px;"> 
            Dear Mr./Ms./Mrs. <?php echo $record['Lastname']; ?>,<br><br>
            <p>I am writing to inform you that your request for scheduling has been approved.<br>
            Your schedule has been set for <input type="date" id="calendar" name="Given_Sched" value="<?php echo $record['Given_Sched']; ?>" /> 
            at <input type="time" id="time" name="time" value="<?php echo date("H:i", strtotime($record['transaction_time'])); ?>" />. We kindly expect your presence on the said date.<br><br>
          <br><br>
          If you are unable to attend the scheduled appointment, you may request a new appointment by clicking on this  <a href='http://localhost/public_html/requestresched.php' style = "color:  blue;"> link. </a> Please ensure that your reasons are valid and clearly explained so that your request can be considered.
  Please note that your reasons may need to be verified to avoid any inconvenience to other clients and our schedule. Thank you for your understanding and cooperation.
  
            Best regards,<br>
            <input type="text" name="EmpName" style="margin-top:15px;" style="margin-top:15px;"  value="<?php echo isset($res_Fname) ? $res_Fname . ' ' . $res_Lname : ''; ?>" placeholder="Enter employee name" required><br><br>
            Provincial Government of Bataan - Special Assistance Program</p>
         </div> 
        `;
    } else if (status === 'For Validation') {
        requirements.style.display = 'block'; 
        
            requirements.innerHTML = `
             <div style = "color: black; padding:15px; background:white; margin-top:20px;">
             <div class="input-box">
            <span class="details" style="color:  blue;"> Hospital Bill Amount </span>
            <input type="text" style="padding:10px; height:30px;"required value="<?php echo $record['billamount']; ?>" name="billamount" />
    
          </div>
                <h3 style = "color: blue;">REQUIREMENTS FOR BURIAL ASSISTANCE VALIDATION</h3>
                <ul style = "text-align: left; margin-left:40px" >
                    <input type="checkbox" name="requirement" value="Final Bill w/ Discharge Date (May pirma ng Billing Clerk)/Promissory Note"> Final Bill w/ Discharge Date (May pirma ng Billing Clerk)/Promissory Note <br>
                    <input type="checkbox" name="requirement" value="Medical Abstract/Medical Certificate (May Pangalan Pirma at License # ng Doctor)"> Medical Abstract/Medical Certificate (May Pangalan Pirma at License # ng Doctor) <br>
                    <input type="checkbox" name="requirement" value="Sulat (Sulat Kamay) na Humihingi ng tulong kay Gov. Joet S. Garcia"> Sulat (Sulat Kamay) na Humihingi ng tulong kay Gov. Joet S. Garcia <br>
                    <input type="checkbox" name="requirement" value="Xerox Valid ID ng Pasyente"> Xerox Valid ID ng Pasyente <br>
                    <input type="checkbox" name="requirement" value="Xerox Valid ID ng Maglalakad"> Xerox Valid ID ng Maglalakad <br>
                    <input type="checkbox" name="requirement" value="BRGY. INDIGENCY (PASYENTE)"> BRGY. INDIGENCY (PASYENTE) <br>
                    <input type="checkbox" name="requirement" value="SOCIAL CASE STUDY (MSWDO)"> SOCIAL CASE STUDY (MSWDO) <br>
                   
                    </ul>
                    <h3 style = "color: blue;">SUPPORTING DOCUMENTS</h3>
                    <ul style = "text-align: left; margin-left:40px" >
                    <input type="checkbox" name="requirement" value="XEROX COPY NG BIRTH CERTIFICATE (KUNG ANAK O MAGULANG ANG PASYENTE)"> XEROX COPY NG BIRTH CERTIFICATE (KUNG ANAK O MAGULANG ANG PASYENTE) <br>
                    <input type="checkbox" name="requirement" value="XEROX NG MARRIAGE (CERTIFICATE KUNG ASAWA ANG PASYENTE)"> XEROX NG MARRIAGE (CERTIFICATE KUNG ASAWA ANG PASYENTE) <br>
                    <input type="checkbox" name="requirement" value="BIRTH CERTIFICATE AND MARRIAGE CERTIFICATE (NG MAGULANG) KUNG KAPATID ANG PASYENTE"> BIRTH CERTIFICATE AND MARRIAGE CERTIFICATE (NG MAGULANG) KUNG KAPATID ANG PASYENTE <br>
                 
             </div>
            `;
        
    
    } else if (status === 'Pending for Payout') {
        emailFormat.innerHTML = `
         <div style = "color: black; padding:15px; background:white; margin-top:20px;">
            Dear Mr./Ms./Mrs. <?php echo $record['Lastname']; ?>,<br><br>
            <p>Your assistance request is currently pending for payout.<br>
            We are processing your application, and you will receive your financial assistance soon.<br><br>
            Thank you for your patience and cooperation.<br><br>
            Best regards,<br>
            <input type="text" name="EmpName" style="margin-top:15px;" style="margin-top:15px;"  value="<?php echo isset($res_Fname) ? $res_Fname . ' ' . $res_Lname : ''; ?>" placeholder="Enter employee name" required><br><br>
            Provincial Government of Bataan - Special Assistance Program</p>
         </div>
        `;
    } else if (status === 'Request for Re-schedule') {
        requirements.style.display = 'block'; 
        requirements.innerHTML = `
                <h3 style = "color: white;">Click this   <a href="https://mail.google.com/mail/u/0/?tab=rm&ogbl#inbox" target="_blank" style = "color:  #3cd82e;">link</a> to check the email of beneficiary.</h3>
              
            `;
    } else if (status === 'For Payout') { 
        emailFormat.innerHTML = `
         <div style = "color: black; padding:15px; background:white; margin-top:20px;">
            Dear Mr./Ms./Mrs. <?php echo $record['Lastname']; ?>,<br><br>
            <p>Your assistance request is currently for payout on <input type="date" id="calendar" name="Given_Sched" value="<?php echo $record['Given_Sched']; ?>" /> 
            at <input type="time" id="time" name="time" value="<?php echo date("H:i", strtotime($record['time'])); ?>" />.<br>
            Kindly proceed to PGB-Hermosa Branch<br>
            Thank you for your patience and cooperation.<br><br>
            Best regards,<br>
            <input type="text" name="EmpName" style="margin-top:15px;" style="margin-top:15px;"  value="<?php echo isset($res_Fname) ? $res_Fname . ' ' . $res_Lname : ''; ?>" placeholder="Enter employee name" required><br><br>
             Provincial Government of Bataan - Special Assistance Program</p>
         </div>
        `;
    }else if (status === 'For Re-schedule') { 
        emailFormat.innerHTML = `
        <div style = "color: black; padding:15px; background:white; margin-top:20px;">
            Dear Mr./Ms./Mrs. <?php echo $record['Lastname']; ?>,<br><br>
            <p>Your request for re-schedule has been accepted. Your new schedule is on <input type="date" id="calendar" name="Given_Sched" value="<?php echo $record['Given_Sched']; ?>" /> 
            at <input type="time" id="time" name="time"  />.<br>
            We kindly expect your presence on the said date.<br><br>
            Best regards,<br>
            <input type="text" name="EmpName" style="margin-top:15px;" style="margin-top:15px;"  value="<?php echo isset($res_Fname) ? $res_Fname . ' ' . $res_Lname : ''; ?>" placeholder="Enter employee name" required><br><br>   Provincial Government of Bataan - Special Assistance Program</p>
         </div> 
        `;
  }
      }

  
      
     
  window.onload = handleStatusChange;
    
  </script>
  </body>
</html>
