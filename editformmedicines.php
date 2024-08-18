<?php 
session_start();
include("php/config.php");
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

// Check if Beneficiary_Id is set in the URL parameter
if(isset($_POST['Beneficiary_Id'])) {
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
      $branch1 = $result['Office'];
  }
} else {
  header("Location: employee-login.php");
  exit();
}

    $SQL = "SELECT b.*, t.*, m.*
            FROM beneficiary b
            INNER JOIN transaction t ON b.Beneficiary_Id = t.Beneficiary_Id
            INNER JOIN medicines m ON b.Beneficiary_Id = m.Beneficiary_ID
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
      $beneID=$_POST['Beneficiary_Id'];
      $Status=$_POST['Status'];
      $EmpID = $_POST['Emp_ID'];
  
      
if ($Status == "For Validation") {
  date_default_timezone_set('Asia/Manila');
 $Date = date('Y-m-d'); // Set the current date for Given_Sched
 $transaction_time = date('H:i:s'); // Set the current date and time for transaction_time
 
 $checkedRequirements = isset($_POST['requirement']) ? $_POST['requirement'] : array();
 $EmpID = $_POST['Emp_ID'];
 $newStatus = $_POST['Status'];

 
    // Determine if all requirements are checked
    $allChecked = count($checkedRequirements) === 8; // Replace 8 with the actual number of requirements

    // Determine new status based on all requirements being checked
    if ($allChecked) {
        $newStatus = 'Pending for Release Of Medicine';
    } else {
        $newStatus = 'Pending for Requirements';
    }

$query = "UPDATE medicines m
      INNER JOIN beneficiary b ON b.Beneficiary_Id = m.Beneficiary_ID
      INNER JOIN transaction t ON t.Beneficiary_Id = m.Beneficiary_ID
      SET t.Given_Sched  = '$Date',
          t.Given_Time = '$transaction_time',
          t.Emp_ID='$EmpID',
        
            t.Status = '$newStatus'
        WHERE b.Beneficiary_Id = '$beneID'";

    $result = mysqli_query($con, $query);

   
if ($result) {
    // Query executed successfully
    echo '<body>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>
    swal("Update successfully","","success")
    .then((value) => {
        if (value) {
            window.location.href = "medicines.php";
        }
    });
    </script>
    </body>';
} else {
    // Error in query execution
    echo "Error: " . mysqli_error($con);
}
}

         
elseif ($Status == "Release Medicine") {
    date_default_timezone_set('Asia/Manila');
    $ReceivedDate = date('Y-m-d'); // Set the current date for Given_Sched
    $ReceivedTime = date('H:i:s'); // Set the current date and time for transaction_time
    
 $beneID = $_POST['Beneficiary_Id'];
 
 $SQL = mysqli_query($con,"SELECT b.*, t.*, m.*
 FROM beneficiary b
 INNER JOIN transaction t ON b.Beneficiary_Id = t.Beneficiary_Id
 INNER JOIN medicines m ON b.Beneficiary_Id = m.Beneficiary_ID
 WHERE b.Beneficiary_Id = '$beneID'");

if($result = mysqli_fetch_assoc($SQL)){
    $branch = $result['branch'];
$TransactionType = $result['TransactionType'];
$AssistanceType = $result['AssistanceType'];
$MedicineType = $result['MedicineType'];
$EmpID = $_POST['Emp_ID'];
$ReceivedAssistance = "Medicine";
$beneID = $_POST['Beneficiary_Id'];
$query ="INSERT INTO history( Beneficiary_ID, ReceivedDate, ReceivedTime,TransactionType,AssistanceType,ReceivedAssistance,Emp_ID,branch) VALUES ('$beneID', '$ReceivedDate', '$ReceivedTime', ' $TransactionType', '$AssistanceType','$MedicineType','$EmpID','$branch' )";
if(mysqli_query($con, $query)){

$sql1 = "DELETE FROM transaction  WHERE Beneficiary_Id='$beneID'";
$sql2 = "DELETE FROM medicines  WHERE Beneficiary_ID='$beneID'";

$result1 = mysqli_query($con, $sql1);
$result2 = mysqli_query($con, $sql2);

if($result1 && $result2) {

                echo '<body>
                <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
                <script>
                swal("This beneficiary already received his/her assistance","","success")
               
                </script>';
                  echo '<script>
                 setTimeout(function(){
                    window.location.href="medicines.php";
                } , 2000);
              </script>
              </body>';
}
}

}
}
    elseif ($Status == "For Schedule") {

        $transaction_time = $_POST['time'];
        $Date = ($_POST['Given_Sched'] != '') ? $_POST['Given_Sched'] : '0000-00-00'; // Set to '0000-00-00' if empty
        $timestamp = strtotime($transaction_time);
     $transaction_time_24hr = date("H:i", $timestamp);
     
        $Status = "For Validation";
          $overlapQuery = "SELECT * FROM transaction WHERE Given_Sched = '$Date' AND Given_Time = '$transaction_time_24hr' AND Beneficiary_Id != '$beneID'";
        $overlapResult = mysqli_query($con, $overlapQuery);

        if(mysqli_num_rows($overlapResult) > 0) {
             echo '<body>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>
    swal("The selected date and time are already taken. Please choose a different time.","","error")
    .then((value) => {
        if (value) {
            exit(); // Prevent further execution
        }
    });
    </script>
    </body>';
        } else{
            
       $query = "UPDATE medicines m
      INNER JOIN beneficiary b ON b.Beneficiary_Id = m.Beneficiary_ID
      INNER JOIN transaction t ON t.Beneficiary_Id = m.Beneficiary_ID
      SET t.Given_Sched  = '$Date',
          t.Given_Time = '$transaction_time',
          t.Emp_ID='$EmpID',
          t.Status = '$Status'
      
      WHERE b.Beneficiary_Id = '$beneID'";
          $result2 = mysqli_query($con, $query);
    
    if ($result2) {
        $Status = $_POST['Status'];
        if ($Status !== "Pending for Requirements" && $Status !== "For Validation" &&  $Status !== "Release Medicine") {    
        require 'phpmailer/src/Exception.php';
        require 'phpmailer/src/PHPMailer.php';
        require 'phpmailer/src/SMTP.php';

        $mail = new PHPMailer(true);
        $lastName = $record['Lastname'];
        $transaction_time = $_POST['time'];
        $transaction_time_12hr = date("g:i A", strtotime($transaction_time)); // Convert to 12-hour format          
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
                <p>Your schedule has been set for $Date at $transaction_time_12hr. We kindly expect your presence on the said date.</p>
                <p> We kindly expect your presence on the said date.<br><br></p>
                <p>   If you are unable to attend the scheduled appointment, you may request a new appointment by clicking on this  <a href='http://localhost/public_html/requestresched.php'> link. </a> Please ensure that your reasons are valid and clearly explained so that your request can be considered.<br> 
               Please note that your reasons may need to be verified to avoid any inconvenience to other clients and our schedule. Thank you for your understanding and cooperation.</p>
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
                            window.location.href = "medicines.php";
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
                window.location.href = "medicines.php";
            }
        });
        </script>
        </body>';}

    } else {
        echo "Error updating records: " . mysqli_error($con);
        header("Location: medicines.php");
        exit();
    }
     
        }
    }
    elseif ($Status == "Pending for Release Of Medicine") {
        date_default_timezone_set('Asia/Manila');
        $Date = date('Y-m-d'); // Set the current date for Given_Sched
        $transaction_time = date('H:i:s'); // Set the current date and time for transaction_time
        $EmpID = $_POST['Emp_ID'];

        $overlapQuery = "SELECT * FROM transaction WHERE Given_Sched = '$Date' AND Given_Time = '$transaction_time' AND Beneficiary_Id != '$beneID'";
        $overlapResult = mysqli_query($con, $overlapQuery);

        if(mysqli_num_rows($overlapResult) > 0) {
            echo '<body>
            <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
            <script>
            swal("The selected date and time are already taken. Please choose a different time.","","error")
            .then((value) => {
                if (value) {
                    exit(); // Prevent further execution
                }
            });
            </script>
            </body>';
        }else{
       $query = "UPDATE medicines m
      INNER JOIN beneficiary b ON b.Beneficiary_Id = m.Beneficiary_ID
      INNER JOIN transaction t ON t.Beneficiary_Id = m.Beneficiary_ID
      SET t.Given_Sched  = '$Date',
          t.Given_Time = '$transaction_time',
          t.Emp_ID='$EmpID',
          t.Status = '$Status'
      
      WHERE b.Beneficiary_Id = '$beneID'";
          $result2 = mysqli_query($con, $query);
         if ($result2) {
        $Status = $_POST['Status'];
        if ($Status !== "Pending for Requirements" && $Status !== "For Validation" &&  $Status !== "Release Medicine") {    
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
            if($status == 'Pending for Release Of Medicine') {
                $employeeName = $_POST['EmpName'];
                $mail->Subject = 'Pending for Release Of Medicine';
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
        

            $mail->send();
            echo '<body>
                    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
                    <script>
                    swal("Update and email send successful","","success")
                    .then((value) => {
                        if (value) {
                            window.location.href = "medicines.php";
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
                window.location.href = "medicines.php";
            }
        });
        </script>
        </body>';}

    } else {
        echo "Error updating records: " . mysqli_error($con);
        header("Location: medicines.php");
        exit();
    }
        }
       
    }
    
    elseif ($Status == "Releasing Of Medicines") {
        date_default_timezone_set('Asia/Manila');
        $Date = date('Y-m-d'); // Set the current date for Given_Sched
        $transaction_time = date('H:i:s'); // Set the current date and time for transaction_time
        $branch = $_POST['branch'];
        $PayoutType = "medicines";
        $overlapQuery = "SELECT * FROM transaction WHERE Given_Sched = '$Date' AND Given_Time = '$transaction_time' AND Beneficiary_Id != '$beneID' ";
        $overlapResult = mysqli_query($con, $overlapQuery);

        if(mysqli_num_rows($overlapResult) > 0) {
            echo '<body>
            <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
            <script>
            swal("The selected date and time are already taken. Please choose a different time.","","error")
            .then((value) => {
                if (value) {
                    exit(); // Prevent further execution
                }
            });
            </script>
            </body>';
            
        }else{
       $query = "UPDATE medicines m
      INNER JOIN beneficiary b ON b.Beneficiary_Id = m.Beneficiary_ID
      INNER JOIN transaction t ON t.Beneficiary_Id = m.Beneficiary_ID
      SET t.Given_Sched = '$Date', t.Given_Time = '$transaction_time', t.Status = '$Status', t.Emp_ID='$EmpID',m.receivedassistance='$PayoutType', m.branch='$branch'
    
      
      WHERE b.Beneficiary_Id = '$beneID'";
          $result2 = mysqli_query($con, $query);
         if ($result2) {
        $Status = $_POST['Status'];
        if ($Status !== "Pending for Requirements" && $Status !== "For Validation" &&  $Status !== "Release Medicine") {    
        require 'phpmailer/src/Exception.php';
        require 'phpmailer/src/PHPMailer.php';
        require 'phpmailer/src/SMTP.php';

        $mail = new PHPMailer(true);
        $lastName = $record['Lastname'];
       
        $Email = $record['Email'];
        $status= $_POST['Status'];
        $transaction_time = $_POST['time'];
        $transaction_time_12hr = date("g:i A", strtotime($transaction_time)); // Convert to 12-hour format
      
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
            if($status == 'Releasing Of Medicines') {
                $employeeName = $_POST['EmpName'];
                $mail->Subject = 'Releasing Of Medicines';
                  $mail->Body = "
                    <html>
                    <body>
                    <p>Dear Mr./Ms./Mrs. $lastName,</p>
                    <p>Your request for Medicine assistance has been approved. You may go on $Date at  $transaction_time_12hr.
                     You will receive a Medicine<br>
                    Kindly proceed to $branch to claim your Medicine.<br>
                    Please bring a valid ID and show this email upon arrival.<br>
                    Thank you for your patience and cooperation.</p>
                    <p>Best regards,<br>
                    $employeeName</p>
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
                            window.location.href = "medicines.php";
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
                window.location.href = "medicines.php";
            }
        });
        </script>
        </body>';}

    } else {
        echo "Error updating records: " . mysqli_error($con);
        header("Location: medicines.php");
        exit();
    }
        }
       
    }
    
    elseif ($Status == "Decline Request for Re-schedule") {
          $reason= $_POST['reason'];
       $query = "UPDATE medicines m
      INNER JOIN beneficiary b ON b.Beneficiary_Id = m.Beneficiary_ID
      INNER JOIN transaction t ON t.Beneficiary_Id = m.Beneficiary_ID
      SET t.Emp_ID='$EmpID',
          t.Status = '$Status'
      
      WHERE b.Beneficiary_Id = '$beneID'";
        $result2 = mysqli_query($con, $query);
        if ($result2) {
        $Status = $_POST['Status'];
        if ($Status !== "Pending for Requirements" && $Status !== "For Validation" &&  $Status !== "Release Medicine") {    
        require 'phpmailer/src/Exception.php';
        require 'phpmailer/src/PHPMailer.php';
        require 'phpmailer/src/SMTP.php';

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
           if($status == 'Decline Request for Re-schedule') {
                $employeeName = $_POST['EmpName'];
                $mail->Subject = 'Request For Re-scheduled Declined';
                $mail->Body = "
                    <html>
                    <body>
                    <p>Dear Mr./Ms./Mrs. $lastName,</p>
                    <p>We have received your request for rescheduling. Unfortunately, we regret to inform you that your request cannot be accommodated at this time.</p>
                    <p>  Please be assured that we are doing our best to process all applications and requests efficiently. However, due to the following reason, we are unable to grant your rescheduling request.<br><br></p>
                <p>  Reason:$reason<br><br></p>
                <p>   We appreciate your understanding and patience in this matter. If you have any further questions or need additional assistance, please do not hesitate to contact us.<br><br></p>
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
                            window.location.href = "medicines.php";
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
                window.location.href = "medicines.php";
            }
        });
        </script>
        </body>';}

    } else {
        echo '<body>
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
        <script>
        swal("Error updating records: " . mysqli_error($con),"","success")
        .then((value) => {
            if (value) {
                window.location.href = "medicines.php";
            }
        });
        </script>
        </body>';
    } 
    }
    }

    elseif ($Status == "For Re-schedule") {
        $transaction_time = $_POST['time'];
        $Date = ($_POST['Given_Sched'] != '') ? $_POST['Given_Sched'] : '0000-00-00'; // Set to '0000-00-00' if empty
     
        $Status = "For Validation";

        $overlapQuery = "SELECT * FROM transaction WHERE Given_Sched = '$Date' AND Given_Time = '$transaction_time' AND Beneficiary_Id != '$beneID'";
        $overlapResult = mysqli_query($con, $overlapQuery);

        if(mysqli_num_rows($overlapResult) > 0) {
            
               echo '<body>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>
    swal("The selected date and time are already taken. Please choose a different time.","","error")
    .then((value) => {
        if (value) {
            exit(); // Prevent further execution
        }
    });
    </script>
    </body>';
        }else{
            
            
        
       $query = "UPDATE medicines m
      INNER JOIN beneficiary b ON b.Beneficiary_Id = m.Beneficiary_ID
      INNER JOIN transaction t ON t.Beneficiary_Id = m.Beneficiary_ID
      SET t.Given_Sched  = '$Date',
          t.Given_Time = '$transaction_time',
          t.Emp_ID='$EmpID',
          t.Status = '$Status'
      
      WHERE b.Beneficiary_Id = '$beneID'";
        $result2 = mysqli_query($con, $query);
        if ($result2) {
        $Status = $_POST['Status'];
        if ($Status !== "Pending for Requirements" && $Status !== "For Validation" &&  $Status !== "Release Medicine") {    
        require 'phpmailer/src/Exception.php';
        require 'phpmailer/src/PHPMailer.php';
        require 'phpmailer/src/SMTP.php';

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
           if($status == 'For Re-schedule') {
                $employeeName = $_POST['EmpName'];
                $mail->Subject = 'Re-schedule';
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
                            window.location.href = "medicines.php";
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
                window.location.href = "medicines.php";
            }
        });
        </script>
        </body>';}

    } else {
        echo "Error updating records: " . mysqli_error($con);
        header("Location: medicines.php");
        exit();
    } 
    }
    }

    else{
        date_default_timezone_set('Asia/Manila');
        $Date = date('Y-m-d'); // Set the current date for Given_Sched
        $transaction_time = date('H:i:s'); // Set the current date and time for transaction_time

        $overlapQuery = "SELECT * FROM transaction WHERE Given_Sched = '$Date' AND Given_Time = '$transaction_time' AND Beneficiary_Id != '$beneID'";
        $overlapResult = mysqli_query($con, $overlapQuery);

        if(mysqli_num_rows($overlapResult) > 0) {
            echo "The selected date and time are already booked. Please choose a different time.";
            exit();
        }
       $query = "UPDATE medicines m
      INNER JOIN beneficiary b ON b.Beneficiary_Id = m.Beneficiary_ID
      INNER JOIN transaction t ON t.Beneficiary_Id = m.Beneficiary_ID
      SET t.Given_Sched  = '$Date',
          t.Given_Time = '$transaction_time',
          t.Emp_ID='$EmpID',
          t.Status = '$Status'
      
      WHERE b.Beneficiary_Id = '$beneID'";
    }
    // Construct the update query
}

?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Edit Form</title>
    <link rel="stylesheet" href="editformmedicines.css" />
  </head>
  
  <body>

    <div class="container">
      <div class="title"> Edit form </div>
      <form id="editForm"  method="post"> <!-- Changed method to POST -->
      <input type="hidden" name="Beneficiary_Id" value="<?php echo $beneID; ?>">
      <input type="hidden" name="Emp_ID" value="<?php echo $EmpID; ?>">

   
      
        <div class="user-details1">
                <div class="input-box">
                    <span class="details" style="color:#f5ca3b;">Date of Application:</span>
                    <span id="calendar" style="color:white; margin-top:10px;"><?php echo $record['Date']; ?></span>
                </div>
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
</div>
        
        <div class="user-details">
                <div class="input-box">
                    <span class="details"style="color:  #f5ca3b;"> Types of Medicines </span>
                    <input disabled type="text" required value="<?php echo $record['MedicineType']; ?>" name="MedicineType"/>
                </div>
     
                <div class="input-box">
                    <span class="details" style="color:  #f5ca3b; ">Status</span>
                   
                           <?php
                       
                       $status = array('For Schedule','For Validation','Pending for Requirements','Pending for Release Of Medicine' ,'Releasing Of Medicines','Request for Re-schedule','For Re-schedule', 'Decline Request for Re-schedule','Release Medicine');
                   
                       if ($record['Status'] == 'For Schedule') {
                           // If the current status is "For Schedule", display an input field instead of a dropdown
                           echo "<input type='text' id='status' name='Status' value='For Schedule' readonly>";
                       } 
                       if ($record['Status'] == 'For Validation') {
                           // If the current status is "For Schedule", display an input field instead of a dropdown
                           echo "<input type='text' id='status' name='Status' value='For Validation' readonly>";
                       }
                       elseif ($record['Status'] == 'Pending for Requirements') {
                           // If the current status is "Pending for Requirements", display only "For Validation" in the dropdown
                           echo "<input type='text' id='status' name='Status' value='For Validation' readonly>";
                       }
                       elseif ($record['Status'] == 'Pending for Release Of Medicine') {
                           // If the current status is "Pending for Requirements", display only "For Validation" in the dropdown
                           echo "<input type='text' id='status' name='Status' value='Releasing Of Medicines' readonly>";
                       }
                       elseif ($record['Status'] == 'Request for Re-schedule') {
                           // If the current status is "Pending for Requirements", display only "For Validation" in the dropdown
                           echo "<select id='status' name='Status' onchange='handleStatusChange()'>";
                           echo "<option value='For Re-schedule'>For Re-schedule</option>";
                           echo "<option value='Decline Request for Re-schedule'>Decline Request for Re-schedule</option>";
                           echo "</select>";
                       }
                       elseif ($record['Status'] == 'For Re-schedule') {
                           // If the current status is "Pending for Requirements", display only "For Validation" in the dropdown
                           echo "<input type='text' id='status' name='Status' value='For Validation' readonly>";
                       }
                       elseif ($record['Status'] == 'Decline Request for Re-schedule') {
                           // If the current status is "Pending for Requirements", display only "For Validation" in the dropdown
                           echo "<input type='text' id='status' name='Status' value='Request for Re-schedule Declined' readonly>";
                   
                       }
                       elseif ($record['Status'] == 'Releasing Of Medicines') {
                           // If the current status is "Pending for Requirements", display only "For Validation" in the dropdown
                          if ($role === 'Admin'){
                       
                               // If the current status is "Pending for Requirements", display only "For Validation" in the dropdown
                               echo "<select id='status' name='Status' onchange='handleStatusChange()'>";
                               echo "<option value='Releasing Of Medicines'>Releasing Of Medicines</option>";
                               echo "<option value='Release Medicine'>Release Medicine</option>";
                               echo "</select>";
                            
                          }else{
                           echo "<input type='text' id='status' name='Status' value='Releasing Of Medicines' readonly>";
                          
                          }
                       }
                      
                       ?>
                    </select>
                </div>
        </div>
                   <input type="hidden" name="confirmed" id="confirmed" value="no">
       
       
            <div id="requirements" style="display: none;"></div>
            <div id="emailFormat" class="emailformat">
                    <!-- Email content will be updated based on the selected status -->
            </div>
           
                <div class="button-row">
                    <input type="submit"  id="submitbtn" value="Submit" name="submit" onclick="showConfirmation()" />
                    <input type="button" value="Cancel" name="cancel" onclick="cancelEdit()" />
                </div>
      </form>
    </div>
    <script>
  function handleStatusChange() {
  
            var status = document.getElementById('status').value;
            var emailFormat = document.getElementById('emailFormat');
            var requirements = document.getElementById('requirements');
            var submitbtn = document.getElementById('submitbtn');

    var beneID = document.querySelector('input[name="Beneficiary_Id"]').value;
var empID = document.querySelector('input[name="Emp_ID"]').value;

            emailFormat.innerHTML = '';
            requirements.style.display = 'none'; // Hide requirements by default
            
            if (status === 'For Schedule') {
        submitbtn.style.display = 'inline';
        emailFormat.innerHTML = `
           <div style = "color: black; padding:15px; background:white; margin-top:20px;"> 
            Dear Mr./Ms./Mrs. <?php echo $record['Lastname']; ?>,<br><br>
            <p>I am writing to inform you that your request for scheduling has been approved.<br>
            Your schedule has been set for <input type="date" id="calendar" name="Given_Sched" min="<?php echo date('Y-m-d'); ?>" value="<?php echo $record['Given_Sched']; ?>" /> 
            at <input type="time" id="time" name="time" value="<?php echo date("H:i", strtotime($record['transaction_time'])); ?>" />. We kindly expect your presence on the said date.<br>
          <br>
         
            Best regards,<br>
            <input type="text" name="EmpName" style="margin-top:15px;" value="<?php echo isset($res_Fname) ? $res_Fname . ' ' . $res_Lname : ''; ?>" placeholder="Enter employee name" required><br><br>
            Provincial Government of Bataan - Special Assistance Program</p>
         </div> 
        `;
            } else if (status === 'For Validation') {
                submitbtn.style.display = 'inline';
                requirements.style.display = 'block';
                requirements.innerHTML = `
                    <div style="color: black; padding:15px; background:white; margin-top:20px;">
                        
                        <h3 style = "color: blue;">REQUIREMENTS FOR MEDICINES ASSISTANCE VALIDATION</h3>
                        <ul style = "text-align: left; margin-left:60px">
                            <input type="checkbox" name="requirement[]" value="Updated Medical Certificate/Medical Abstract (1 ORIGINAL, 1 PHOTOCOPY)"> Updated Medical Certificate/Medical Abstract (1 ORIGINAL, 1 PHOTOCOPY) <br>
                            <input type="checkbox" name="requirement[]" value="Barangay Certificate of Indigency">  Reseta ng Gamot NOTE: 1st & 2nd checks same date, same doctor, same signature with Doctor's License No.<br> (2 PHOTOCOPIES) <br>
                            <input type="checkbox" name="requirement[]" value="Sulat (SULAT KAMAY) na humihingi ng tulong kay Gov. Joet S. Garcia"> Sulat (SULAT KAMAY) na humihingi ng tulong kay Gov. Joet S. Garcia <br>
                            <input type="checkbox" name="requirement[]" value="Xerox Valid ID ng Pasyente w/ 3 signatures or Xerox Valid ID ng Naglalakad w/ 3 signatures"> Xerox Valid ID ng Pasyente w/ 3 signatures or Xerox Valid ID ng Naglalakad w/ 3 signatures <br>
                            <input type="checkbox" name="requirement[]" value="Brgy. Indigency (Pasyente) / Brgy. Indigency (Representative)"> Brgy. Indigency (Pasyente) / Brgy. Indigency (Representative) <br>
                       
                        </ul>
                        <h3 style = "color: blue;">SUPPORTING DOCUMENTS</h3>
                        <ul style = "text-align: left; margin-left:60px">
                        <input type="checkbox" name="requirement[]" value="Xerox copy ng Birth Certificate (Kung anak o magulang ang pasyente)"> Xerox copy ng Birth Certificate (Kung anak o magulang ang pasyente) <br>
                        <input type="checkbox" name="requirement[]" value="Xerox ng Marriage Certificate (Kung asawa ang pasyente)"> Xerox ng Marriage Certificate (Kung asawa ang pasyente) <br>
                        <input type="checkbox" name="requirement[]" value="Birth Certificate and Marriage Certificate (ng magulang kung kapatid ang pasyente)"> Birth Certificate and Marriage Certificate (ng magulang kung kapatid ang pasyente) <br>
                           
                        </ul>
                    </div>
                `;
            } else if (status === 'Pending for Release Of Medicine') {
                submitbtn.style.display = 'inline';
                emailFormat.innerHTML = `
                    <div style="color: black; padding:15px; background:white; margin-top:20px;">
                        Dear Mr./Ms./Mrs. <?php echo $record['Lastname']; ?>,<br><br>
                        <p>Your assistance request is currently pending for payout.<br>
                        We are processing your application, and you will receive your financial assistance soon.<br><br>
                        Thank you for your patience and cooperation.<br><br>
                        Best regards,<br>
                        <input type="text" name="EmpName" style="margin-top:15px;" value="<?php echo isset($res_Fname) ? $res_Fname . ' ' . $res_Lname : ''; ?>" placeholder="Enter employee name" required><br><br>
                        Provincial Government of Bataan - Special Assistance Program</p>
                    </div>
                `;
               
            } else if (status === 'Request for Re-schedule') {
                requirements.style.display = 'block';
                requirements.innerHTML = `
                    <h3 style="color: white;">Click this <a href="https://mail.google.com/mail/u/0/?tab=rm&ogbl#inbox" target="_blank" style="color:  #3cd82e;">link</a> to check the email of beneficiary.</h3>
                `;
                submitbtn.style.display = 'none'; // Hide the submit button
           
            } else if (status === 'Releasing Of Medicines') {
                submitbtn.style.display = 'inline';
                emailFormat.innerHTML = `
                    <div style="color: black; padding:15px; background:white; margin-top:20px;">
                        Dear Mr./Ms./Mrs. <?php echo $record['Lastname']; ?>,<br><br>
                        <p>Your request for Medicine assistance has been approved. You may go on <input type="date" id="calendar" name="Given_Sched" min="<?php echo date('Y-m-d'); ?>" value="<?php echo $record['Given_Sched']; ?>" /> 
                        at <input type="time" id="time" name="time" value="<?php echo date("H:i", strtotime($record['time'])); ?>" />.<br>
                        You will receive a Medicine<br>
                 Kindly proceed to <select name="branch" id="branch" style="margin-top:5px;">
    <option value="PGB-Balanga Branch" <?php if ($record['branch'] == "PGB-Balanga Branch") echo 'selected="selected"'; ?>>PGB-Balanga Branch</option>
    <option value="PGB-Dinalupihan Branch" <?php if ($record['branch'] == "PGB-Dinalupihan Branch") echo 'selected="selected"'; ?>>PGB-Dinalupihan Branch</option>
    <option value="PGB-Hermosa Branch" <?php if ($record['branch'] == "PGB-Hermosa Branch") echo 'selected="selected"'; ?>>PGB-Hermosa Branch</option>
    <option value="PGB-Mariveles Branch" <?php if ($record['branch'] == "PGB-Mariveles Branch") echo 'selected="selected"'; ?>>PGB-Mariveles Branch</option>
</select>to claim your medicine. <br>

                    Please bring a valid ID and show this email upon arrival.<br><br>
                    Thank you for your patience and cooperation.<br><br>    
                    Best regards,<br>
                    <input type="text" name="EmpName" style="margin-top:15px;" value="<?php echo isset($res_Fname) ? $res_Fname . ' ' . $res_Lname : ''; ?>" placeholder="Enter employee name" required><br><br>
                    Provincial Government of Bataan - Special Assistance Program
                </p>
                </div>
                `;
            } else if (status === 'For Re-schedule') {
                submitbtn.style.display = 'inline';
                emailFormat.innerHTML = `
            <div style = "color: black; padding:15px; background:white; margin-top:20px;">
            Dear Mr./Ms./Mrs. <?php echo $record['Lastname']; ?>,<br><br>
            <p>Your request for re-schedule has been accepted. Your new schedule is on <input type="date" id="calendar" name="Given_Sched" min="<?php echo date('Y-m-d'); ?>" value="<?php echo $record['Given_Sched']; ?>" /> 
            at <input type="time" id="time" name="time"  />.<br>
            We kindly expect your presence on the said date.<br><br>
            Best regards,<br>
            <input type="text" name="EmpName" style="margin-top:15px;" value="<?php echo isset($res_Fname) ? $res_Fname . ' ' . $res_Lname : ''; ?>" placeholder="Enter employee name" required><br><br>
            Provincial Government of Bataan - Special Assistance Program</p>
         </div> 
        `;
            }
        else if (status === 'Decline Request for Re-schedule') {
            submitbtn.style.display = 'inline';
            emailFormat.innerHTML = `
         <div style = "color: black; padding:15px; background:white; margin-top:20px;">
            Dear Mr./Ms./Mrs. <?php echo $record['Lastname']; ?>,<br><br>
            <p>We have received your request for rescheduling. Unfortunately, we regret to inform you that your request cannot be accommodated at this time.<br>
            Please be assured that we are doing our best to process all applications and requests efficiently. However, due to the following reason, we are unable to grant your rescheduling request.<br><br>
             
        <strong>Reason:</strong><br><textarea style="height:50px;width:620px;" name="reason" required value=""></textarea><br>

            We appreciate your understanding and patience in this matter. If you have any further questions or need additional assistance, please do not hesitate to contact us.<br><br>
            Best regards,<br>
            <input type="text" name="EmpName" style="margin-top:15px;" value="<?php echo isset($res_Fname) ? $res_Fname . ' ' . $res_Lname : ''; ?>" placeholder="Enter employee name" required><br><br>
            Provincial Government of Bataan - Special Assistance Program</p>
         </div>
        `;
    }
    else if (status === 'Pending for Requirements') {
            submitbtn.style.display = 'none'; 
       
    }
    else if (status === 'Release Medicine') {
        submitbtn.style.display = 'inline';
       
    }

        }
    
        // Call handleStatusChange on page load to set the initial state
        document.addEventListener('DOMContentLoaded', function() {
            handleStatusChange();
        });


        function cancelEdit() {
                window.location.href = "medicines.php";
        }

        function showConfirmation() {
            var confirmation = confirm("Are you sure you want to update?");
            if (confirmation) {
                // If user clicks OK, submit the form
                document.getElementById("confirmed").value = "yes";
            } else {
                document.getElementById("confirmed").value = "no";
            }
        }

    
        window.onload = handleStatusChange;
    </script>

</body>
</html>