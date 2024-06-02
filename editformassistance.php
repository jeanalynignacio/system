<?php 
session_start();
include("php/config.php");
 
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

    
// Check if Beneficiary_Id is set in the URL parameter
if(isset($_POST['Beneficiary_Id'])) {
    $beneID = $_POST['Beneficiary_Id'];
    $Status = $_POST['Status'];
} else {
    echo "User ID is not set.";
    exit; // Exit if ID is not set
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

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    if(isset($_POST['confirmed']) && $_POST['confirmed'] === "yes") {
        $beneID = $_POST['Beneficiary_Id'];
        //$Date = $_POST['Date'];
         //$TransactionType = $_POST['TransactionType'];
       // $FA_Type = $_POST['FA_Type'];
        $Status = $_POST['Status'];
        

        if ($Status == "For Validation") {
            date_default_timezone_set('Asia/Manila');
                $Date = date('Y-m-d'); // Set the current date for Given_Sched
                $transaction_time = date('H:i:s'); // Set the current date and time for transaction_time
    
                $requirements = isset($_POST['requirement']) ? (array)$_POST['requirement'] : [];
                $requiredItems = array("Death Certificate", "Barangay Certificate of Indigency", "Request Letter", "Photocopy of Beneficiary's ID");
                $missingItems = array_diff($requiredItems, $requirements);
                $Status = empty($missingItems) ? "For Payout" : "Incomplete Requirements";
    
                $query = "UPDATE financialassistance f
                INNER JOIN beneficiary b ON b.Beneficiary_Id = f.Beneficiary_ID
                INNER JOIN transaction t ON t.Beneficiary_Id = f.Beneficiary_ID
                SET t.Status = '$Status', t.Emp_ID='$EmpID'
                WHERE b.Beneficiary_Id = '$beneID'";
       
    }
        elseif ($Status == "For Schedule") {

            $transaction_time = $_POST['time'];
            $Date = ($_POST['Given_Sched'] != '') ? $_POST['Given_Sched'] : '0000-00-00'; // Set to '0000-00-00' if empty
         
            $Status = "For Validation";
            $query = "UPDATE financialassistance f
            INNER JOIN beneficiary b ON b.Beneficiary_Id = f.Beneficiary_ID
            INNER JOIN transaction t ON t.Beneficiary_Id = f.Beneficiary_ID
            SET t.Given_Sched = '$Date', t.Given_Time = '$transaction_time', t.Status = '$Status', t.Emp_ID='$EmpID'
            WHERE b.Beneficiary_Id = '$beneID'";
        }
        elseif ($Status == "Pending for Payout") {
            date_default_timezone_set('Asia/Manila');
            $Date = date('Y-m-d'); // Set the current date for Given_Sched
            $transaction_time = date('H:i:s'); // Set the current date and time for transaction_time
    
            $Status = "For Validation";
            $query = "UPDATE financialassistance f
            INNER JOIN beneficiary b ON b.Beneficiary_Id = f.Beneficiary_ID
            INNER JOIN transaction t ON t.Beneficiary_Id = f.Beneficiary_ID
            SET t.Given_Sched = '$Date', t.Given_Time = '$transaction_time', t.Status = '$Status', t.Emp_ID='$EmpID'
            WHERE b.Beneficiary_Id = '$beneID'";
        }
    
        else{
            $transaction_time = $_POST['time'];
            $Date = ($_POST['Given_Sched'] != '') ? $_POST['Given_Sched'] : '0000-00-00'; // Set to '0000-00-00' if empty
         
            $query = "UPDATE financialassistance f
                      INNER JOIN beneficiary b ON b.Beneficiary_Id = f.Beneficiary_ID
                      INNER JOIN transaction t ON t.Beneficiary_Id = f.Beneficiary_ID
                      SET t.Given_Sched = '$Date', t.Given_Time = '$transaction_time', t.Status = '$Status', t.Emp_ID='$EmpID'
                      WHERE b.Beneficiary_Id = '$beneID'";
        }
        // Construct the update query

        $result2 = mysqli_query($con, $query);

        if ($result2) {
            require 'PHPMailer/src/Exception.php';
            require 'PHPMailer/src/PHPMailer.php';
            require 'PHPMailer/src/SMTP.php';

            $mail = new PHPMailer(true);
            $lastName = $record['Lastname'];
            $employeeName = $_POST['EmpName'];
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
                    $mail->Subject = 'Schedule for requirements checking';
                    $mail->Body = "
                    <html>
                    <body>
                    <p>Dear Mr./Ms./Mrs. $lastName,</p>
                    <p>I am writing to inform you that your request for scheduling has been approved.</p>
                    <p>Your schedule has been set for $Date. We kindly expect your presence on the said date at $transaction_time</p>
                    <p> We kindly expect your presence on the said date.<br><br></p>
                    <p>   If you are unable to attend the scheduled appointment, you may request a new appointment by clicking on this  <a href='http://localhost/public_html/requestresched.php'> link. </a> Please ensure that your reasons are valid and clearly explained so that your request can be considered.<br> 
                   Please note that your reasons may need to be verified to avoid any inconvenience to other clients and our schedule. Thank you for your understanding and cooperation.</p>
                    <p>Best regards,<br>$employeeName</p>
                   
                    <p>Provincial Government of Bataan - Special Assistance Program</p>
                    </body>
                    </html>
                  
                   
                    
                    ";

                } elseif($status == 'Pending for Requirements') {
                    $mail->Subject = 'Pending for Requirements';
                    $mail->Body = "
                        <html>
                        <body>
                        <p>Dear Mr./Ms./Mrs. $lastName,</p>
                        <p>Your assistance request is currently pending for requirements.</p>
                        <p>Please submit the necessary documents on $Date to proceed with your request.</p>
                        <p>Thank you for your cooperation.</p>
                        <p>Best regards,</p>
                        <p>$employeeName</p>
                        <p>Provincial Government of Bataan - Special Assistance Program</p>
                        </body>
                        </html>
                    ";
                } elseif($status == 'Pending for Payout') {
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
                }
         

<<<<<<< HEAD
                 $mail->send();
                echo '<script>alert("Update and email send successful");</script>';
                echo '<script>
                        setTimeout(function(){
                            window.location.href="assistance.php";
                        }, 3000);
                      </script>';
                exit();

=======
                $mail->send();
                echo '<body>
                        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
                        <script>
                        swal("Update and email send successful","","success")
                        .then((value) => {
                            if (value) {
                                window.location.href = "assistance.php";
                            }
                        });
                        </script>
                        </body>';
>>>>>>> f3e2cb998df9eb664e8cae62f1d9743d8f3d7f79
            } catch (Exception $e) {
                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
        } else {
            echo "Error updating records: " . mysqli_error($con);
            header("Location: assistance.php");
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
    <link rel="stylesheet" href="editformassistance.css" />
    <script>
      
      function handleStatusChange() {
    var status = document.getElementById('status').value;
    var emailFormat = document.getElementById('emailFormat');
    var requirements = document.getElementById('requirements');
    var faType = "<?php echo $record['FA_Type']; ?>";
    
    emailFormat.innerHTML = '';
    requirements.style.display = 'none'; // Hide requirements by default

    if (status === 'For Schedule') {
        emailFormat.innerHTML = `
            Dear Mr./Ms./Mrs. <?php echo $record['Lastname']; ?>,<br><br>
            <p>I am writing to inform you that your request for scheduling has been approved.<br>
            Your schedule has been set for <input type="date" id="calendar" name="Given_Sched" value="<?php echo $record['Given_Sched']; ?>" /> 
            at <input type="time" id="time" name="time" value="<?php echo date("H:i", strtotime($record['transaction_time'])); ?>" />. We kindly expect your presence on the said date.<br><br>
          <br><br>
          If you are unable to attend the scheduled appointment, you may request a new appointment by clicking on this  <a href='http://localhost/public_html/requestresched.php'> link. </a> Please ensure that your reasons are valid and clearly explained so that your request can be considered.
Please note that your reasons may need to be verified to avoid any inconvenience to other clients and our schedule. Thank you for your understanding and cooperation.
 
            Best regards,<br>
            <input type="text" name="EmpName" value="<?php echo isset($res_Fname) ? $res_Fname . ' ' . $res_Lname : ''; ?>" placeholder="Enter employee name" required><br><br>
            Provincial Government of Bataan - Special Assistance Program</p>
        `;
    } else if (status === 'For Validation') {
        requirements.style.display = 'block'; 
        if (faType === 'Burial') {
            requirements.innerHTML = `
                <h3>Requirements for Burial Assistance Validation</h3>
                <ul>
                    <input type="checkbox" name="requirement" value="Death Certificate"> Death Certificate <br>
                    <input type="checkbox" name="requirement" value="Barangay Certificate of Indigency"> Barangay Certificate of Indigency <br>
                    <input type="checkbox" name="requirement" value="Request Letter"> Request Letter <br>
                    <input type="checkbox" name="requirement" value="Photocopy of Beneficiary's ID"> Photocopy of Beneficiary's ID <br>
                    </ul>
            `;
            
        } else if (faType === 'Chemotherapy & Radiation') {
            requirements.innerHTML = `
                <h3>Requirements for Chemotherapy & Radiation Assistance Validation</h3>
                <ul>
                    <li><input type="checkbox" name="requirement" value="Medical Certificate"> Medical Certificate</li>
                    <li><input type="checkbox" name="requirement" value="Barangay Certificate of Indigency"> Barangay Certificate of Indigency</li>
                    <li><input type="checkbox" name="requirement" value="Request Letter"> Request Letter</li>
                    <li><input type="checkbox" name="requirement" value="Photocopy of Beneficiary's ID"> Photocopy of Beneficiary's ID</li>
                </ul>
            `;
        }
        else if (faType === 'Dialysis') {
            requirements.innerHTML = `
                <h3>Requirements for Dialysis</h3>
                <ul>
                    <li><input type="checkbox" name="requirement" value="Medical Certificate"> Medical Certificate</li>
                    <li><input type="checkbox" name="requirement" value="Barangay Certificate of Indigency"> Barangay Certificate of Indigency</li>
                    <li><input type="checkbox" name="requirement" value="Request Letter"> Request Letter</li>
                    <li><input type="checkbox" name="requirement" value="Photocopy of Beneficiary's ID"> Photocopy of Beneficiary's ID</li>
                </ul>
            `;
        }
    } else if (status === 'Pending for Requirements') {
        emailFormat.innerHTML = `
            Dear Mr./Ms./Mrs. <?php echo $record['Lastname']; ?>,<br><br>
            <p>Your assistance request is currently pending for requirements.<br>
            Please submit the necessary documents on <input type="date" id="calendar" name="Given_Sched" value="<?php echo $record['Given_Sched']; ?>" /> 
            at <input type="time" id="time" name="time" value="<?php echo date("H:i", strtotime($record['transaction_time'])); ?>" /> to proceed with your request.<br><br>
            Thank you for your cooperation.<br><br>
            Best regards,<br>
            <input type="text" name="EmpName" value="<?php echo isset($res_Fname) ? $res_Fname . ' ' . $res_Lname : ''; ?>" placeholder="Enter employee name" required><br><br>
            Provincial Government of Bataan - Special Assistance Program</p>
        `;
    } else if (status === 'Pending for Payout') {
        emailFormat.innerHTML = `
            Dear Mr./Ms./Mrs. <?php echo $record['Lastname']; ?>,<br><br>
            <p>Your assistance request is currently pending for payout.<br>
            We are processing your application, and you will receive your financial assistance soon.<br><br>
            Thank you for your patience and cooperation.<br><br>
            Best regards,<br>
            <input type="text" name="EmpName" value="<?php echo isset($res_Fname) ? $res_Fname . ' ' . $res_Lname : ''; ?>" placeholder="Enter employee name" required><br><br>
            Provincial Government of Bataan - Special Assistance Program</p>
        `;
    } else if (status === 'Request for Re-schedule') {
        requirements.style.display = 'block'; 
        requirements.innerHTML = `
                <h3 >Open gmail to check the email of beneficiary </h3>
                <a href="https://mail.google.com/mail/u/0/?tab=rm&ogbl#inbox" target="_blank">Open Gmail</a>
            `;
    } else if (status === 'For Payout') { 
        emailFormat.innerHTML = `
            Dear Mr./Ms./Mrs. <?php echo $record['Lastname']; ?>,<br><br>
            <p>Your assistance request is currently for payout on <input type="date" id="calendar" name="Given_Sched" value="<?php echo $record['Given_Sched']; ?>" /> 
            at <input type="time" id="time" name="time" value="<?php echo date("H:i", strtotime($record['time'])); ?>" />.<br>
            Thank you for your patience and cooperation.<br><br>
            Best regards,<br>
            <input type="text" name="EmpName" value="<?php echo isset($res_Fname) ? $res_Fname . ' ' . $res_Lname : ''; ?>" placeholder="Enter employee name" required><br><br>
            Provincial Government of Bataan - Special Assistance Program</p>
        `;
    }else if (status === 'For Re-schedule') { 
        emailFormat.innerHTML = `
            Dear Mr./Ms./Mrs. <?php echo $record['Lastname']; ?>,<br><br>
            <p>Your assistance request for re-schedule has been accepted. Your new schedule is on <input type="date" id="calendar" name="Given_Sched" value="<?php echo $record['Given_Sched']; ?>" /> 
            at <input type="time" id="time" name="time"  />.<br>
            Thank you for your patience and cooperation.<br><br>
            Best regards,<br>
            <input type="text" name="EmpName" value="<?php echo isset($res_Fname) ? $res_Fname . ' ' . $res_Lname : ''; ?>" placeholder="Enter employee name" required><br><br>
            Provincial Government of Bataan - Special Assistance Program</p>
        `;
}
      }

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
        window.onload = handleStatusChange;
    </script>
</head>
<body>
    <div class="container">
        <div class="title">Edit form</div>
        <form id="editForm" method="post">
            <input type="hidden" name="Beneficiary_Id" value="<?php echo $beneID; ?>">
            <input type="hidden" name="Emp_ID" value="<?php echo $EmpID; ?>">

            <div class="user-details1">
                <div class="input-box">
                    <span class="details" style="color:#f5ca3b;">Date of Application:</span>
                    <span id="calendar" style="color:white; margin-top:10px;"><?php echo $record['Date']; ?></span>
                </div>

               <!-- <div class="input-box">
                    <span class="details" style="color:  #f5ca3b;">Time of Application:</span>
                    <span id="time" style="color:  white;"><?php echo date("h:i A", strtotime($record['transaction_time'])); ?></span>
                    <input type="hidden" required value="<?php echo $record['Beneficiary_ID']; ?>" name="Beneficiary_ID" disabled />
                </div>-->
            </div>

            <div class="user-details">
                <div class="input-box">
                    <span class="details" style="color:  #f5ca3b;">Full Name</span>
                    <input disabled type = "text" required value = "<?php echo $record['Firstname'] . " " . $record['Lastname']; ?>" > 
                </div>

                <div class="input-box">
                    <span class="details">Transaction Type</span>
                    <input disabled type = "text" required value = "<?php echo $record['TransactionType']; ?>">
                </div>

                <div class="input-box">
                    <span class="details">Financial Assistance Type</span>
                    <input disabled type = "text" required value = "<?php echo $record['FA_Type']; ?>">
                   <!-- <select name="FA_Type">
                       
                        $FA_type = array('Burial', 'Chemotherapy & Radiation', 'Dialysis', 'Medicine');
                        foreach ($FA_type as $FA) {
                            $selected = ($record['FA_Type'] == $FA) ? 'selected' : '';
                            echo "<option $selected>$FA</option>";
                        }
                        ?>
                    </select>-->
                </div>

                <div class="input-box">
                    <span class="details">Status</span>
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

                <!--<div class="input-box">
                    <span class="details">Given Schedule</span>
                    <input type="date" id="calendar" name="Given_Sched" value="<?php echo $record['Given_Sched']; ?>" />
                </div>-->
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
</body>
</html>
