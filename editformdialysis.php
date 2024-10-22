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
    $Status = $_POST['Status'];
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
  header("Location: login.php");
  exit();
}

    $SQL = "SELECT b.*, t.*, m.*
            FROM beneficiary b
            INNER JOIN transaction t ON b.Beneficiary_Id = t.Beneficiary_Id
            INNER JOIN dialysis m ON b.Beneficiary_Id = m.Beneficiary_ID
            WHERE b.Beneficiary_Id = '$beneID'";

    $result = mysqli_query($con, $SQL);
   
   
    if(mysqli_num_rows($result) == 0) {
      echo "No data found for the given Beneficiary ID.";
      exit;
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

 
 $relationship = $_POST['relationship']; // Get the relationship from form data or adjust as necessary

if ($relationship === "" || $relationship === "Myself") {
    // If relationship is empty or "myself", set the required number of checks to 7
    $allChecked = count($checkedRequirements) === 5;
} else {
    // For other relationships, set the required number of checks to 8
    $allChecked = count($checkedRequirements) === 6;
}

    // Determine new status based on all requirements being checked
    if ($allChecked) {
        $newStatus = 'Pending for Release Assistance';
    } else {
        $newStatus = 'Pending for Requirements';
    }

$query = "UPDATE dialysis m
      INNER JOIN beneficiary b ON b.Beneficiary_Id = m.Beneficiary_ID
      INNER JOIN transaction t ON t.Beneficiary_Id = m.Beneficiary_ID
      SET t.Given_Sched  = '$Date',
          t.Given_Time = '$transaction_time',
          t.Emp_ID='$EmpID',
               m.branch='$branch1',
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
            window.location.href = "dialysis.php";
        }
    });
    </script>
    </body>';
} else {
    // Error in query execution
    echo "Error: " . mysqli_error($con);
}
}
elseif ($Status == "Pending due to Insufficient stocks") {
    $Status = "For Schedule";

}
        elseif ($Status == "For Schedule") {
            $transaction_time = $_POST['time'];
            $Date = ($_POST['Given_Sched'] != '') ? $_POST['Given_Sched'] : '0000-00-00'; // Set to '0000-00-00' if empty
            $timestamp = strtotime($transaction_time);
           $transaction_time_24hr = date("H:i", $timestamp);
           $number= $record['Contactnumber'];
          

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

            }else{
                $query = "UPDATE dialysis f
                INNER JOIN beneficiary b ON b.Beneficiary_Id = f.Beneficiary_ID
                INNER JOIN transaction t ON t.Beneficiary_Id = f.Beneficiary_ID
                SET t.Given_Sched = '$Date', t.Given_Time = '$transaction_time', t.Status = '$Status', t.Emp_ID='$EmpID',f.branch='$branch1'
                WHERE b.Beneficiary_Id = '$beneID'";
                  $result2 = mysqli_query($con, $query);
            
         if ($result2) {
            $Status = $_POST['Status'];
            if ($Status !== "Pending for Requirements" && $Status !== "For Validation" &&  $Status !== "Receive Payout") {    
                require 'PHPMailer/src/Exception.php';
                require 'PHPMailer/src/PHPMailer.php';
                require 'PHPMailer/src/SMTP.php';

            $mail = new PHPMailer(true);
            $lastName = $record['Lastname'];
            $transaction_time = $_POST['time'];
$transaction_time_12hr = date("g:i A", strtotime($transaction_time)); // Convert to 12-hour format

            $Email = $record['Email'];
            $status= $_POST['Status'];
            $stats = $record['Status'];
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
                $mail->isHTML(true);

                if($stats == 'For Schedule') {
                    $employeeName ="Mr.Chalor Howell S. Icban";
                    $mail->Subject = 'Schedule for requirements checking';
                    $mail->Body = "
                    <html>
                    <body>
                    <p>Dear Mr./Ms./Mrs. $lastName,</p>
                    <p>I am writing to inform you that your request for scheduling for applying assistance has been approved.</p>
                    <p>Your schedule has been set for $Date at  $transaction_time_12hr.</p>
                   <p> We kindly expect your presence on the said date.<br><br></p>
                   <p>   If you are unable to attend the scheduled appointment, you may request a new appointment by clicking on this  <a href='http://localhost/public_html/requestresched.php'> link. </a> Please ensure that your reasons are valid and clearly explained so that your request can be considered.<br> 
               Please note that your reasons may need to be verified to avoid any inconvenience to other clients and our schedule. Thank you for your understanding and cooperation.</p>

                   <p>Best regards,<br>$employeeName<br>
 Special Assistance Program Coordinator<br>
 Provincial Government of Bataan - Damayan Center</p>
                    </body>
                    </html>
                    ";

                }
                elseif($stats == 'Pending due to Insufficient stocks') {
                    date_default_timezone_set('Asia/Manila');
                    $Date = date('Y-m-d'); // Set the current date for Given_Sched
                    $transaction_time = $_POST['time'];
                    $transaction_time_12hr = date("g:i A", strtotime($transaction_time)); // Convert to 12-hour format
                          
                    $employeeName ="Mr.Chalor Howell S. Icban";
                    $mail->Subject = 'Schedule for requirements checking';
                    $mail->Body = "
                    <html>
                    <body>
                    <p>Dear Mr./Ms./Mrs. $lastName,</p>
                    <p>I am writing to inform you that we now have the necessary stocks available to proceed with your application.<br></p>
                    <p> However, we regret to inform you that your submitted requirements have expired. Kindly submit a new set of requirements on $Date at  $transaction_time_12hr to proceed with the validation of requirements to process your assistance.<br><br></p>
                   <p> Thank you for cooperation. God Bless!<br><br></p>
                  
                   <p>Best regards,<br>$employeeName<br>
 Special Assistance Program Coordinator<br>
 Provincial Government of Bataan - Damayan Center</p>
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
                                window.location.href = "dialysis.php";
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
                    window.location.href = "dialysis.php";
                }
            });
            </script>
            </body>';}

        } else {
            echo "Error updating records: " . mysqli_error($con);
            header("Location: dialysis.php");
            exit();
        }
         
            }
        }

        
    
    

    elseif ($Status == "Pending for Release Assistance") {
      date_default_timezone_set('Asia/Manila');
    $Date = date('Y-m-d'); // Set the current date for Given_Sched
    $transaction_time = date('H:i:s'); // Set the current date and time for transaction_time
    //$Status = "For Validation";

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
       // $result2 = null;
        $query = "UPDATE dialysis l
        INNER JOIN beneficiary b ON b.Beneficiary_Id = l.Beneficiary_ID
        INNER JOIN transaction t ON t.Beneficiary_Id = l.Beneficiary_ID
        SET t.Given_Sched  = '$Date',
            t.Given_Time = '$transaction_time',
            t.Emp_ID='$EmpID',
            t.Status = '$Status'
        
        WHERE b.Beneficiary_Id = '$beneID'";
     $result2 = mysqli_query($con, $query);
     if ($result2) {
    $Status = $_POST['Status'];
    if ($Status !== "Pending for Requirements" && $Status !== "For Validation" &&  $Status !== "Receive Guarantee Letter") {    
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
        if($status == 'Pending for Releasing Guarantee Letter') {
            $employeeName ="Mr.Chalor Howell S. Icban";
            $mail->Subject = 'Pending for Releasing Guarantee Letter';
            $mail->Body = "
                <html>
                <body>
                <p>Dear Mr./Ms./Mrs. $lastName,</p>
                <p>Your assistance request is currently pending for payout.</p>
                <p>We are processing your application, and you will receive your assistance soon.</p>
                <p>Thank you for your patience and cooperation.</p>
                 <p>Best regards,<br>$employeeName<br>
 Special Assistance Program Coordinator<br>
 Provincial Government of Bataan - Damayan Center</p>
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
                        window.location.href = "dialysis.php";
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
            window.location.href = "dialysis.php";
        }
    });
    </script>
    </body>';}

} else {
    echo "Error updating records: " . mysqli_error($con);
    header("Location: dialysis.php");
    exit();
}
    }
   
}

    
    elseif ($Status == "Receive Assistance") {
       
date_default_timezone_set('Asia/Manila');
$ReceivedDate = date('Y-m-d'); // Set the current date for Given_Sched
$ReceivedTime = date('H:i:s'); // Set the current date and time for transaction_time

$beneID = $_POST['Beneficiary_Id'];


$SQL = mysqli_query($con, "SELECT b.*, t.*, h.*
FROM beneficiary b
INNER JOIN transaction t ON b.Beneficiary_Id = t.Beneficiary_Id
INNER JOIN dialysis h ON b.Beneficiary_Id = h.Beneficiary_ID
WHERE b.Beneficiary_Id = '$beneID'");

if ($result = mysqli_fetch_assoc($SQL)) {
    $branch = $result['branch'];
    $MedicineType = $result['DialysisAssistanceType'];
    $TransactionType = $result['TransactionType'];
    $AssistanceType = $result['AssistanceType'];
    $ReceivedAssistance = $MedicineType;

    $EmpID = $_POST['Emp_ID']; // Assuming you have employee ID stored in session

    // Query to select from dialysisstocks
    $sql3 = "SELECT * FROM dialysisstocks 
             WHERE ItemName='$MedicineType' 
             AND branch='$branch' 
             AND ExpDate >= CONVERT_TZ(NOW(), '+00:00', '+08:00') 
            
             ORDER BY ExpDate ASC 
             ";

    $result3 = mysqli_query($con, $sql3);

        if ($resultbal = mysqli_fetch_assoc($result3)) {
            $exp = $resultbal['ExpDate'];
            $beneID = $_POST['Beneficiary_Id'];

            $branch = $resultbal['branch'];

            $sql4= "SELECT * FROM dialysisstocks 
            WHERE ItemName='$MedicineType' 
            AND branch='$branch' 
            AND ExpDate >= CONVERT_TZ(NOW(), '+00:00', '+08:00') 
           AND Quantity > 0
            ORDER BY ExpDate ASC 
            LIMIT 1 ";

             $result42 = mysqli_query($con, $sql4);

              if ($result42 && ($result12 = mysqli_fetch_assoc($result42))) {
          
                if ($result12['Quantity'] != 0) {
                // Update query to decrease the Quantity
                $updateQuery = "UPDATE dialysisstocks 
                                SET Quantity = Quantity - 1 
                                WHERE branch='$branch' 
                                AND ItemName='$MedicineType' 
                                AND ExpDate = '$exp'";
                
                $result4 = mysqli_query($con, $updateQuery);

                // Insert into history table
                $query = "INSERT INTO history (Beneficiary_ID, ReceivedDate, ReceivedTime, TransactionType, AssistanceType, ReceivedAssistance, Emp_ID, Amount, branch)
                          VALUES ('$beneID', '$ReceivedDate', '$ReceivedTime', '$TransactionType', '$AssistanceType', '$MedicineType', '$EmpID', '0', '$branch')";

if(mysqli_query($con, $query)){
// Send the email
$lastName = $result['Lastname'];  // Assuming 'Lastname' is part of the $result array
$Email = $result['Email'];  // Assuming 'Email' is part of the $result array
$employeeName ="Mr.Chalor Howell S. Icban";
$link= "http://localhost/public_html/feedback.php";
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

$mail = new PHPMailer(true);
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
    $mail->addAddress($Email);
 
    $mail->isHTML(true);
    $mail->Subject = 'Received Assistance';
    $mail->Body = "
        <html>
        <body>
        <p>Dear Mr./Ms./Mrs. $lastName,</p>
        <p>We have successfully provided your assistance in Dialysis. Please note that you may request another assistance after a period of 3 months.</p>
<p>If you have some extra time, kindly answer our feedback form through this <a href='$link'>link</a>. Your input is greatly appreciated and will help us improve our service.<br></p>
<p>Thank you for your cooperation. God Bless!<br><br></p>
 <p>Best regards,<br>$employeeName<br>
 Special Assistance Program Coordinator<br>
 Provincial Government of Bataan - Damayan Center</p>
</body>
</html>

    ";

    $mail->send();
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}

// Delete records from transaction and dialysis tables after email is sent
$sql1 = "DELETE FROM transaction WHERE Beneficiary_Id='$beneID'";
$sql2 = "DELETE FROM dialysis WHERE Beneficiary_ID='$beneID'";
$result1 = mysqli_query($con, $sql1);
$result2 = mysqli_query($con, $sql2);
            if ($result4 && mysqli_query($con, $sql1) && mysqli_query($con, $sql2) ) {
                echo '<body>
                      <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
                      <script>
                      swal("This beneficiary already received his/her assistance","","success")
                      .then((value) => {
                          if (value) {
                              window.location.href = "dialysis.php";
                          }
                      });
                      </script>
                      </body>';
            }
         
    } else {
        echo '<body>
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
        <script>
        swal("Error inserting into history","","error")
        .then((value) => {
            if (value) {
                window.location.href = "dialysis.php";
            }
        });
        </script>
        </body>';
    }
} else {
   

date_default_timezone_set('Asia/Manila');
$ReceivedDate = date('Y-m-d'); // Set the current date for Given_Sched
$ReceivedTime = date('H:i:s'); // Set the current date and time for transaction_time

$query = "UPDATE dialysis f
 INNER JOIN beneficiary b ON b.Beneficiary_Id = f.Beneficiary_ID
 INNER JOIN transaction t ON t.Beneficiary_Id = f.Beneficiary_ID
 SET t.Status = 'Pending due to Insufficient stocks', t.Emp_ID='$EmpID', t.Given_Sched = '$ReceivedDate',
     t.Given_Time = '$ReceivedTime'
 WHERE b.Beneficiary_Id = '$beneID'";

$result2 = mysqli_query($con, $query);
if($result2) {
$lastName = $result['Lastname'];  // Assuming 'Lastname' is part of the $result array
$Email = $result['Email'];  // Assuming 'Email' is part of the $result array
$employeeName ="Mr.Chalor Howell S. Icban";

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

$mail = new PHPMailer(true);
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
$mail->addAddress($Email);

// Content
$mail->isHTML(true);
$mail->Subject = 'Pending Application due to Insufficient Stocks';
$mail->Body = "
<html>
<body>
<p>Dear Mr./Ms./Mrs. $lastName,</p>
<p>We regret to inform you that we currently do not have sufficient stocks available to process your assistance application.<br></p>
<p>As a result, your application is pending at the moment. We will keep you updated as soon as stocks become available.<br><br></p>
<p>Thank you for your cooperation. God Bless!<br><br></p>
<p>Best regards,<br>$employeeName<br>
Special Assistance Program Coordinator<br>
Provincial Government of Bataan - Damayan Center</p>
</body>
</html>
";

$mail->send();
echo '<body>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script>
swal("This branch has no stocks","","error")
.then((value) => {
if (value) {
    window.location.href = "dialysis.php";
}
});
</script>
</body>';
} catch (Exception $e) {
echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
echo '<body>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script>
swal("Message could not be sent","","error")
.then((value) => {
if (value) {
    window.location.href = "dialysis.php";
}
});
</script>
</body>';
}

}

} 
} else{
   
date_default_timezone_set('Asia/Manila');
$ReceivedDate = date('Y-m-d'); // Set the current date for Given_Sched
$ReceivedTime = date('H:i:s'); // Set the current date and time for transaction_time

$query = "UPDATE dialysis f
 INNER JOIN beneficiary b ON b.Beneficiary_Id = f.Beneficiary_ID
 INNER JOIN transaction t ON t.Beneficiary_Id = f.Beneficiary_ID
 SET t.Status = 'Pending due to Insufficient stocks', t.Emp_ID='$EmpID', t.Given_Sched = '$ReceivedDate',
     t.Given_Time = '$ReceivedTime'
 WHERE b.Beneficiary_Id = '$beneID'";

$result2 = mysqli_query($con, $query);
if($result2) {
$lastName = $result['Lastname'];  // Assuming 'Lastname' is part of the $result array
$Email = $result['Email'];  // Assuming 'Email' is part of the $result array
$employeeName ="Mr.Chalor Howell S. Icban";

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

$mail = new PHPMailer(true);
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
$mail->addAddress($Email);

// Content
$mail->isHTML(true);
$mail->Subject = 'Pending Application due to Insufficient Stocks';
$mail->Body = "
<html>
<body>
<p>Dear Mr./Ms./Mrs. $lastName,</p>
<p>We regret to inform you that we currently do not have sufficient stocks available to process your assistance application.<br></p>
<p>As a result, your application is pending at the moment. We will keep you updated as soon as stocks become available.<br><br></p>
<p>Thank you for your cooperation. God Bless!<br><br></p>
<p>Best regards,<br>$employeeName<br>
Special Assistance Program Coordinator<br>
Provincial Government of Bataan - Damayan Center</p>
</body>
</html>
";

$mail->send();
echo '<body>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script>
swal("This branch has no stocks","","error")
.then((value) => {
if (value) {
    window.location.href = "dialysis.php";
}
});
</script>
</body>';
} catch (Exception $e) {
echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
echo '<body>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script>
swal("Message could not be sent","","error")
.then((value) => {
if (value) {
    window.location.href = "dialysis.php";
}
});
</script>
</body>';
}

}

}
} // dto

}
}


    elseif ($Status == "Decline Request for Re-schedule") {
          $reason= $_POST['reason'];
       $query = "UPDATE dialysis m
      INNER JOIN beneficiary b ON b.Beneficiary_Id = m.Beneficiary_ID
      INNER JOIN transaction t ON t.Beneficiary_Id = m.Beneficiary_ID
      SET t.Emp_ID='$EmpID',
          t.Status = '$Status'
      
      WHERE b.Beneficiary_Id = '$beneID'";
        $result2 = mysqli_query($con, $query);
        if ($result2) {
        $Status = $_POST['Status'];
        if ($Status !== "Pending for Requirements" && $Status !== "For Validation" &&  $Status !== "Receive Assistance") {    
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
                            window.location.href = "dialysis.php";
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
                window.location.href = "dialysis.php";
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
                window.location.href = "dialysis.php";
            }
        });
        </script>
        </body>';
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
            
            
        
       $query = "UPDATE dialysis m
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
        if ($Status !== "Pending for Requirements" && $Status !== "For Validation" &&  $Status !== "Receive Assistance") {    
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
                            window.location.href = "dialysis.php";
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
                window.location.href = "dialysis.php";
            }
        });
        </script>
        </body>';}

    } else {
        echo "Error updating records: " . mysqli_error($con);
        header("Location: dialysis.php");
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
       $query = "UPDATE dialysis m
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

      <input type = "hidden" id="relationship" name="relationship" required value = "<?php echo $record['Relationship']; ?>">
                 
      
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
</div><input disabled name="f" id="dt3"  style="color:#f5ca3b;background-color: transparent;border: none;outline: none; font-size:15px;margin-top:15px;" type = "hidden" required value = "Date Validated" > 
              
<input disabled name="f" id="dt2" type="hidden" style="background-color: transparent;border: none;outline: none; color:white;font-size:15px;margin-top:-9px;" required value="<?php echo $record['Given_Sched']; ?>">
           
        <div class="user-details">
                <div class="input-box">
                    <span class="details"style="color:  #f5ca3b;"> Types of Medicines </span>
                    <input disabled type="text" required value="<?php echo $record['DialysisAssistanceType']; ?>" name="MedicineType"/>
                </div>
                <input type = "hidden" id="stats" name="stats" required value = "<?php echo $record['Status']; ?>">
                     
                <div class="input-box">
                    <span class="details" style="color:  #f5ca3b; ">Status</span>
                   <!-- <select id="status" name="Status" onchange="handleStatusChange()">
                      
                        $status = array('For Schedule','For Validation','Pending for Requirements','Pending for Release Assistance' ,'Releasing of Assistance','Request for Re-schedule','For Re-schedule', 'Decline Request for Re-schedule', 'Receive Assistance');
                        foreach ($status as $stat) {
                            $selected = ($record['Status'] == $stat) ? 'selected' : '';
                            echo "<option $selected>$stat</option>";
                        }
                        ?>
                    </select> -->



                    <?php
                       
                       $status = array('For Schedule','For Validation','Pending for Requirements','Pending for Release Assistance' ,'Releasing of Assistance','Request for Re-schedule','For Re-schedule', 'Decline Request for Re-schedule','Pending due to Insufficient stocks', 'Receive Assistance');
                       if ($record['Status'] == 'Pending due to Insufficient stocks') {
                        $today = date('Y-m-d');
                      $oneMonthAgo = date('Y-m-d', strtotime('-1 month'));
                      $givenSched = $record['Given_Sched'];
                         
                          
                    if ($givenSched < $oneMonthAgo) {
                        echo "<input type='text' id='status' name='Status' value='For Schedule' readonly>";
                                }
                                else{
                                    echo "<input type='text' id='status' name='Status' value='Receive Assistance' readonly>";
                    
                                }
                       
                    }
    elseif ($record['Status'] == 'For Schedule') {
        // If the current status is "For Schedule", display an input field instead of a dropdown
        echo "<input type='text' id='status' name='Status' value='For Schedule' readonly>";
    } 
    elseif ($record['Status'] == 'For Validation') {
        // If the current status is "For Schedule", display an input field instead of a dropdown
        echo "<input type='text' id='status' name='Status' value='For Validation' readonly>";
    }
    elseif ($record['Status'] == 'Pending for Requirements') {
        // If the current status is "Pending for Requirements", display only "For Validation" in the dropdown
        echo "<input type='text' id='status' name='Status' value='For Validation' readonly>";
    }
    elseif ($record['Status'] == 'Pending for Release Assistance') {
        // If the current status is "Pending for Requirements", display only "For Validation" in the dropdown
        echo "<input type='text' id='status' name='Status' value='Receive Assistance' readonly>";
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
    elseif ($record['Status'] == 'Releasing of Assistance') {
        
    echo "<select id='status' name='Status' onchange='handleStatusChange()'>";
    echo "<option value='Releasing of Assistance'>Releasing of Assistance</option>";
    echo "<option value='Receive Assistance'>Receive Assistance</option>";
    echo "</select>";
       
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
       
if(stats === 'Pending due to Insufficient stocks') {
        emailFormat.innerHTML = `
         <div style = "color: black; padding:15px; background:white; margin-top:20px;"> 
            Dear Mr./Ms./Mrs. <?php echo $record['Lastname']; ?>,<br><br>
            <p>I am writing to inform you that we now have the necessary stocks available to proceed with your application.<br>
        However, we regret to inform you that your submitted requirements have expired. Kindly submit a new set of requirements on <input type="date" id="calendar" name="Given_Sched" min="<?php echo date('Y-m-d'); ?>" value="<?php echo $record['Given_Sched']; ?>" /> 
            at <input type="time" id="time" name="time" value="<?php echo date("H:i", strtotime($record['transaction_time'])); ?>" /> to proceed with the validation of requirements to process your assistance.<br><br>
       Thank you for your cooperation. God Bless!<br><br>
        
            <br>
         
            Best regards,<br>
            <input type="text" name="EmpName" style="margin-top:15px;" value="<?php echo isset($res_Fname) ? $res_Fname . ' ' . $res_Lname : ''; ?>" placeholder="Enter employee name" required><br><br>
            Provincial Government of Bataan - Special Assistance Program</p>
         </div> 
        `;


    }else{
      
        emailFormat.innerHTML = `
         <div style = "color: black; padding:15px; background:white; margin-top:20px;"> 
            Dear Mr./Ms./Mrs. <?php echo $record['Lastname']; ?>,<br><br>
            <p>I am writing to inform you that your request for scheduling for applying assistance has been approved.<br>
            Your schedule has been set for <input type="date" id="calendar" name="Given_Sched" min="<?php echo date('Y-m-d'); ?>" value="<?php echo $record['Given_Sched']; ?>" /> 
            at <input type="time" id="time" name="time" value="<?php echo date("H:i", strtotime($record['transaction_time'])); ?>" />. We kindly expect your presence on the said date.<br>
          <br>
         
            Best regards,<br>
            <input type="text" name="EmpName" style="margin-top:15px;" value="<?php echo isset($res_Fname) ? $res_Fname . ' ' . $res_Lname : ''; ?>" placeholder="Enter employee name" required><br><br>
            Provincial Government of Bataan - Special Assistance Program</p>
         </div> 
        `;
    }  
}
 else if (status === 'For Validation') {
               

                let relationship = document.getElementById('relationship').value;
                submitbtn.style.display = 'inline';
                requirements.style.display = 'block';
          
                

let requirementsHTML = `
    <div style="color: black; padding:10px; background:white; margin-top:10px;margin-bottom:-5px;">
       
        <h3 style="color: blue;">REQUIREMENTS FOR DIALYSIS ASSISTANCE VALIDATION</h3>
        <ul style="text-align: left; margin-left:40px;">
             <input type="checkbox" name="requirement[]" value="Updated Medical Certificate/Medical Abstract (1 ORIGINAL, 1 PHOTOCOPY)"> Updated Medical Certificate/Medical Abstract (1 ORIGINAL, 1 PHOTOCOPY) <br>
                            <input type="checkbox" name="requirement[]" value="Barangay Certificate of Indigency">  Reseta ng Gamot NOTE: 1st & 2nd checks same date, same doctor, same signature with Doctor's License No.<br> (2 PHOTOCOPIES) <br>
                            <input type="checkbox" name="requirement[]" value="Sulat (SULAT KAMAY) na humihingi ng tulong kay Gov. Joet S. Garcia"> Sulat (SULAT KAMAY) na humihingi ng tulong kay Gov. Joet S. Garcia <br>
                            <input type="checkbox" name="requirement[]" value="Xerox Valid ID ng Pasyente w/ 3 signatures or Xerox Valid ID ng Naglalakad w/ 3 signatures"> Xerox Valid ID ng Pasyente w/ 3 signatures or Xerox Valid ID ng Naglalakad w/ 3 signatures <br>
                            <input type="checkbox" name="requirement[]" value="Brgy. Indigency (Pasyente) / Brgy. Indigency (Representative)"> Brgy. Indigency (Pasyente) / Brgy. Indigency (Representative) <br>
                         </ul>`;

// Add supporting documents based on relationship
if (relationship === 'Mother' || relationship === 'Father' || relationship === 'Daughter/Son') {
    requirementsHTML += `
        <h3 style="color: blue;">SUPPORTING DOCUMENTS</h3>
        <ul style="text-align: left; margin-left:40px">
            <input type="checkbox" name="requirement[]" value="Xerox copy ng Birth Certificate (Kung anak o magulang ang pasyente)"> Xerox copy ng Birth Certificate (Kung anak o magulang ang pasyente) <br>
        </ul>`;

} else if (relationship === 'Spouse') {
    requirementsHTML += `
        <h3 style="color: blue;">SUPPORTING DOCUMENTS</h3>
        <ul style="text-align: left; margin-left:40px">
            <input type="checkbox" name="requirement[]" value="Xerox ng Marriage Certificate (Kung asawa ang pasyente)"> Xerox ng Marriage Certificate (Kung asawa ang pasyente) <br>
        </ul>`;
} else if (relationship === 'Sibling') {
    requirementsHTML += `
        <h3 style="color: blue;">SUPPORTING DOCUMENTS</h3>
        <ul style="text-align: left; margin-left:40px">
            <input type="checkbox" name="requirement[]" value="Birth Certificate and Marriage Certificate (ng magulang kung kapatid ang pasyente)"> Birth Certificate and Marriage Certificate (ng magulang kung kapatid ang pasyente) <br>
        </ul>`;
}

requirementsHTML += `
    </div>
    <input type="hidden" name="EmpName" style="margin-top:15px;" value="<?php echo isset($res_Fname) ? $res_Fname . ' ' . $res_Lname : ''; ?>" placeholder="Enter employee name" required><br><br>`;

// Set the innerHTML to the final HTML string
document.getElementById('requirements').innerHTML = requirementsHTML;

    

    
            } else if (status === 'Pending for Release Assistance') {
              
                submitbtn.style.display = 'inline';
                emailFormat.innerHTML = `
                    <div style="color: black; padding:15px; background:white; margin-top:20px;">
                        Dear Mr./Ms./Mrs. <?php echo $record['Lastname']; ?>,<br><br>
                        <p>Your assistance request is currently pending for payout.<br>
                        We are processing your application, and you will receive your assistance soon.<br><br>
                        Thank you for your patience and cooperation.<br><br>
                         Best regards,<br>
            Mr.Chalor Howell S. Icban<br>
          Special Assistance Program Coordinator<br>
        Provincial Government of Bataan - Damayan Center</p>
         </div>
                `;
               
            } else if (status === 'Request for Re-schedule') {
                requirements.style.display = 'block';
                requirements.innerHTML = `
                    <h3 style="color: white;">Click this <a href="https://mail.google.com/mail/u/0/?tab=rm&ogbl#inbox" target="_blank" style="color:  #3cd82e;">link</a> to check the email of beneficiary.</h3>
                `;
                submitbtn.style.display = 'none'; // Hide the submit button
           
            } else if (status === 'Receive Assistance') {
                submitbtn.style.display = 'inline';
emailFormat.innerHTML = `
           <div style = "color: black; padding:15px; background:white; margin-top:20px;">
        Dear Mr./Ms./Mrs. <?php echo $record['Lastname']; ?>,<br><br>
        <p>We have successfully provided your assistance in Dialysis. Please note that you may request another assistance after a period of 3 months. <br>
         If you have an extra time kindly answer our feedback form through this link.  Your input is greatly appreciated and will help us improve our service.<br> 
        Thank you for your cooperation. God Bless!<br><br>
        Best regards,<br>
     Mr.Chalor Howell S. Icban<br>
  Special Assistance Program Coordinator<br>
Provincial Government of Bataan - Damayan Center</p>
     </div> 
        `;  
      //  submitbtn.style.display = 'inline';
      //  pdf.style.display = 'none';     

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
    

        }
    
        // Call handleStatusChange on page load to set the initial state
        document.addEventListener('DOMContentLoaded', function() {
            handleStatusChange();
        });


        function cancelEdit() {
                window.location.href = "dialysis.php";
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