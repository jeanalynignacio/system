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
        $branch1 = $result['Office'];
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
        $FA_Type = $_POST['FA_Type'];
        $Status = $_POST['Status'];
        $EmpID = $_POST['Emp_ID'];

        if ($Status == "For Validation") {
            date_default_timezone_set('Asia/Manila');
                $Date = date('Y-m-d'); // Set the current date for Given_Sched
                $transaction_time = date('H:i:s'); // Set the current date and time for transaction_time
                $newStatus = $_POST['Status'];
                $checkedRequirements = isset($_POST['requirement']) ? $_POST['requirement'] : array();
                $EmpID = $_POST['Emp_ID'];
                // Determine if all requirements are checked
                $allChecked = !in_array(false, $checkedRequirements);
                
                // Determine new status based on all requirements being checked
                if ($allChecked) {
                    $newStatus = 'Pending for Payout';
                } else {
                    $newStatus = 'Pending for Requirements';
                }
   // Build and execute the query to update database
   $query = "UPDATE financialassistance f
             INNER JOIN beneficiary b ON b.Beneficiary_Id = f.Beneficiary_ID
             INNER JOIN transaction t ON t.Beneficiary_Id = f.Beneficiary_ID
             SET t.Status = '$newStatus', t.Emp_ID='$EmpID', t.Given_Sched = '$Date',
                 t.Given_Time = '$transaction_time'
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
               window.location.href = "assistance.php";
           }
       });
       </script>
       </body>';
   }
   else {
       // Error in query execution
       echo "Error: " . mysqli_error($con);
   }
}

    
         
    elseif ($Status == "Release Payout") {
        date_default_timezone_set('Asia/Manila');
        $ReceivedDate = date('Y-m-d'); // Set the current date for Given_Sched
        $ReceivedTime = date('H:i:s'); // Set the current date and time for transaction_time
        
     $beneID = $_POST['Beneficiary_Id'];
     
     $SQL = mysqli_query($con,"SELECT b.*, t.*, f.*
     FROM beneficiary b
     INNER JOIN transaction t ON b.Beneficiary_Id = t.Beneficiary_Id
     INNER JOIN financialassistance f ON b.Beneficiary_Id = f.Beneficiary_ID
     WHERE b.Beneficiary_Id = '$beneID'");

if($result = mysqli_fetch_assoc($SQL)){
 $branch = $result['branch'];
$TransactionType = $result['TransactionType'];
$AssistanceType1 = $result['AssistanceType'];
$AssistanceType2 = $result['AssistanceType'] . '-' . $result['FA_Type'];

$FA_Type = $result['FA_Type'];
$AssistanceType = $AssistanceType1 . "-" . $FA_Type;
$Amount = $result['Amount'];
$EmpID = $_POST['Emp_ID'];
$ReceivedAssistance = $result['PayoutType'];
$beneID = $_POST['Beneficiary_Id'];
         
    $sql3="Select RemainingBal From budget WHERE AssistanceType='$AssistanceType2' && branch='$branch'";
$result3 = mysqli_query($con, $sql3);

if ($result3) {
    // Fetch the result as an associative array
    if ($resultbal = mysqli_fetch_assoc($result3)) {
        // Check if RemainingBal is not 0
        if ($resultbal['RemainingBal'] != 0 || $resultbal['RemainingBal'] > 0) {
            $updateQuery = "UPDATE budget SET RemainingBal = RemainingBal - $Amount WHERE branch = '$branch1' && AssistanceType = '$AssistanceType2'";
            $result4=mysqli_query($con, $updateQuery);
            $query ="INSERT INTO history( Beneficiary_ID, ReceivedDate, ReceivedTime,TransactionType,AssistanceType,ReceivedAssistance,Emp_ID,Amount,branch) VALUES ('$beneID', '$ReceivedDate', '$ReceivedTime', ' $TransactionType', '$AssistanceType', '$ReceivedAssistance','$EmpID','$Amount','$branch' )";
            if(mysqli_query($con, $query)){
                $lastName = $result['Lastname'];  // Assuming 'Lastname' is part of the $result array
                $Email = $result['Email'];  // Assuming 'Email' is part of the $result array
                $employeeName = $_POST['EmpName'];
    
                require 'phpmailer/src/Exception.php';
                require 'phpmailer/src/PHPMailer.php';
                require 'phpmailer/src/SMTP.php';
    
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
                    $mail->Subject = 'Released Payout';
                    $mail->Body = "
                        <html>
                        <body>
                        <p>Dear Mr./Ms./Mrs. $lastName,</p>
                        <p>We have successfully provided your Financial Assistance. Please note that you may request another assistance after a period of 3 months.</p>
                        <p>Thank you for your cooperation. God Bless!<br><br></p>
                        <p>Best regards,<br>$employeeName</p>
                        <p>Provincial Government of Bataan - Special Assistance Program</p>
                        </body>
                        </html>
                    ";
    
                    $mail->send();
                } catch (Exception $e) {
                    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                }
    
                $sql1 = "DELETE FROM transaction  WHERE Beneficiary_Id='$beneID'";
                $sql2 = "DELETE FROM financialassistance  WHERE Beneficiary_ID='$beneID'";
            }    
            $result1 = mysqli_query($con, $sql1);
            $result2 = mysqli_query($con, $sql2);
            
            if($result1 && $result2 && $result4) {
        
                            echo '<body>
                            <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
                            <script>
                            swal("This beneficiary already received his/her assistance","","success")
                           
                            </script>';
                              echo '<script>
                             setTimeout(function(){
                                window.location.href="assistance.php";
                            } , 2000);
                          </script>
                          </body>';
        }        
        } else {
            date_default_timezone_set('Asia/Manila');
            $ReceivedDate = date('Y-m-d'); // Set the current date for Given_Sched
            $ReceivedTime = date('H:i:s'); // Set the current date and time for transaction_time
            
            $query = "UPDATE financialassistance f
             INNER JOIN beneficiary b ON b.Beneficiary_Id = f.Beneficiary_ID
             INNER JOIN transaction t ON t.Beneficiary_Id = f.Beneficiary_ID
             SET t.Status = 'Pending due to Insufficient funds', t.Emp_ID='$EmpID', t.Given_Sched = '$ReceivedDate',
                 t.Given_Time = '$ReceivedTime'
             WHERE b.Beneficiary_Id = '$beneID'";
   
   $result2 = mysqli_query($con, $query);
   if($result2) {
    $lastName = $result['Lastname'];  // Assuming 'Lastname' is part of the $result array
    $Email = $result['Email'];  // Assuming 'Email' is part of the $result array
    $employeeName = $_POST['EmpName'];

    require 'phpmailer/src/Exception.php';
    require 'phpmailer/src/PHPMailer.php';
    require 'phpmailer/src/SMTP.php';

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
        $mail->Subject = 'Pending Application due to Insufficient Funds';
        $mail->Body = "
            <html>
            <body>
            <p>Dear Mr./Ms./Mrs. $lastName,</p>
            <p>I am sorry to inform you that we currently have insufficient funds available to process your application for assistance.<br></p>
           <p>As a result, your application is pending at the moment. We will keep you updated as soon as funds become available.<br><br></p>
            <p>Thank you for your cooperation. God Bless!<br><br></p>
            <p>Best regards,<br>$employeeName</p>
            <p>Provincial Government of Bataan - Special Assistance Program</p>
            </body>
            </html>
        ";

        $mail->send();
        echo '<body>
            <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
            <script>
            swal("You have insufficient balance","","error")
            .then((value) => {
                if (value) {
                    window.location.href = "assistance.php";
                }
            });
            </script>
            </body>';
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }

            

        }
    
    }
} else {
        echo '<body>
            <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
            <script>
            swal("This branch has no budget","","error")
            .then((value) => {
                if (value) {
                    window.location.href = "assistance.php";
                }
            });
            </script>
            </body>';


    }
} else {
    // Query failed
    // Add your code here to handle this case
}
   
}

}
elseif ($Status == "Pending due to Insufficient funds") {
    $Status = "For Schedule";

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
            }else{
                $query = "UPDATE financialassistance f
                INNER JOIN beneficiary b ON b.Beneficiary_Id = f.Beneficiary_ID
                INNER JOIN transaction t ON t.Beneficiary_Id = f.Beneficiary_ID
                SET t.Given_Sched = '$Date', t.Given_Time = '$transaction_time', t.Status = '$Status', t.Emp_ID='$EmpID',f.branch='$branch1'
                WHERE b.Beneficiary_Id = '$beneID'";
                  $result2 = mysqli_query($con, $query);
            
         if ($result2) {
            $Status = $_POST['Status'];
            if ($Status !== "Pending for Requirements" && $Status !== "For Validation" &&  $Status !== "Release Payout") {    
            require 'phpmailer/src/Exception.php';
            require 'phpmailer/src/PHPMailer.php';
            require 'phpmailer/src/SMTP.php';

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
                    $employeeName = $_POST['EmpName'];
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

                   <p>Best regards,<br>$employeeName</p>
                   
                    <p>Provincial Government of Bataan - Special Assistance Program</p>
                    </body>
                    </html>
                    ";

                }
                elseif($stats == 'Pending due to Insufficient funds') {
                    date_default_timezone_set('Asia/Manila');
                    $Date = date('Y-m-d'); // Set the current date for Given_Sched
                    $transaction_time = $_POST['time'];
                    $transaction_time_12hr = date("g:i A", strtotime($transaction_time)); // Convert to 12-hour format
                          
                    $employeeName = $_POST['EmpName'];
                    $mail->Subject = 'Schedule for requirements checking';
                    $mail->Body = "
                    <html>
                    <body>
                    <p>Dear Mr./Ms./Mrs. $lastName,</p>
                    <p>I am writing to inform you that we now have the necessary funds available to proceed with your application.<br></p>
                    <p> However, we regret to inform you that your submitted requirements have expired. Kindly submit a new set of requirements on $Date at  $transaction_time_12hr to proceed with the validation of requirements to process your assistance.<br><br></p>
                   <p> Thank you for cooperation. God Bless!<br><br></p>
                  
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
                                window.location.href = "assistance.php";
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
                    window.location.href = "assistance.php";
                }
            });
            </script>
            </body>';}

        } else {
            echo "Error updating records: " . mysqli_error($con);
            header("Location: assistance.php");
            exit();
        }
         
            }
        }

        
    
        elseif ($Status == "Pending for Payout") {
            date_default_timezone_set('Asia/Manila');
            $Date = date('Y-m-d'); // Set the current date for Given_Sched
            $transaction_time = date('H:i:s'); // Set the current date and time for transaction_time
            $EmpID = $_POST['Emp_ID'];
           
            
            $query = "UPDATE financialassistance f
            INNER JOIN beneficiary b ON b.Beneficiary_Id = f.Beneficiary_ID
            INNER JOIN transaction t ON t.Beneficiary_Id = f.Beneficiary_ID
            SET t.Given_Sched = '$Date', t.Given_Time = '$transaction_time', t.Status = '$Status', t.Emp_ID='$EmpID'
            WHERE b.Beneficiary_Id = '$beneID'";
             $result2 = mysqli_query($con, $query);
             if ($result2) {
            $Status = $_POST['Status'];
            if ($Status !== "Pending for Requirements" && $Status !== "For Validation" &&  $Status !== "Release Payout") {    
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
                if($status == 'Pending for Payout') {
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

                }
            

                $mail->send();
                echo '<body>
                        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
                        <script>
                        swal("Email send successfully","","success")
                        .then((value) => {
                            if (value) {
                                window.location.href = "assistance.php";
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
                    window.location.href = "assistance.php";
                }
            });
            </script>
            </body>';}

        } else {
            echo "Error updating records: " . mysqli_error($con);
            header("Location: assistance.php");
            exit();
        }
            
           
        
        }
        
        elseif ($Status == "For Payout") {
            $SQL = "SELECT b.*, t.*, f.*
            FROM beneficiary b
            INNER JOIN transaction t ON b.Beneficiary_Id = t.Beneficiary_Id
            INNER JOIN financialassistance f ON b.Beneficiary_Id = f.Beneficiary_ID
            WHERE b.Beneficiary_Id = '$beneID'";
    
    $result2 = mysqli_query($con, $SQL);
    
    if ($result2 && mysqli_num_rows($result2) > 0) {
        // Fetch the data as an associative array
        $row = mysqli_fetch_assoc($result2);
        
        $AssistanceType2 = $row['AssistanceType'] . '-' . $row['FA_Type'];
        $FA_Type = $row['FA_Type'];
     
    }
            date_default_timezone_set('Asia/Manila');
            $Date = date('Y-m-d'); // Set the current date for Given_Sched
            $transaction_time = date('H:i:s'); // Set the current date and time for transaction_time
            $amount = $_POST['amount'];
            $pswd = $_POST['pswd'];
            $PayoutType = $_POST['payouttypeSelect'];
            $overlapQuery = "SELECT * FROM transaction WHERE Given_Sched = '$Date' AND Given_Time = '$transaction_time' AND Status = '$Status' AND Beneficiary_Id != '$beneID' ";
            $overlapResult = mysqli_query($con, $overlapQuery);
          
 
         if(mysqli_num_rows($overlapResult) > 0) {
                echo '<body>
                <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
                <script>
                swal("The selected date and time are already taken. Please choose a different time.","","error")
                .then((value) => {
                    if (value) {
                      window.location.href = "assistance.php";
                    }
                });
                </script>
                </body>';
            }else{
                $sql3="Select RemainingBal From budget WHERE AssistanceType='$AssistanceType2' && branch='$branch1'";
            $resultbal = mysqli_query($con, $sql3);

            if ($resultbal && $row = mysqli_fetch_assoc($resultbal)) {
                if ($row['RemainingBal'] > 0) {
            
                $amount = $_POST['amount'];
                if ( $PayoutType=="Cash"){
                if ($amount < 1 || $amount > 5001) {
                    
                    echo '<body>
                <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
                <script>
                 swal("The amount must be up to 5000 Pesos only. If you need a higher amount, kindly choose a cheque.","","error")
                .then((value) => {
                    if (value) {
                   exit(); // Prevent further execution
                    }
                });
                </script>
                </body>';
                   
                }               
                else{
                    $amount = $_POST['amount'];
            $query = "UPDATE financialassistance f
            INNER JOIN beneficiary b ON b.Beneficiary_Id = f.Beneficiary_ID
            INNER JOIN transaction t ON t.Beneficiary_Id = f.Beneficiary_ID
            SET t.Given_Sched = '$Date', t.Given_Time = '$transaction_time', t.Status = '$Status', t.Emp_ID='$EmpID',f.Amount='$amount',f.PayoutType='$PayoutType'
            WHERE b.Beneficiary_Id = '$beneID'";
             $result2 = mysqli_query($con, $query);
             if ($result2) {
            $Status = $_POST['Status'];
            if ($Status !== "Pending for Requirements" && $Status !== "For Validation" &&  $Status !== "Release Payout") {    
            require 'phpmailer/src/Exception.php';
            require 'phpmailer/src/PHPMailer.php';
            require 'phpmailer/src/SMTP.php';

            $mail = new PHPMailer(true);
            $lastName = $record['Lastname'];
            $transaction_time = $_POST['time'];
            $transaction_time_12hr = date("g:i A", strtotime($transaction_time)); // Convert to 12-hour format
            
            $Email = $record['Email'];
            $status= $_POST['Status'];
            $pswd= $_POST['pswd'];
            $amount = $_POST['amount'];
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
                if($status == 'For Payout') {
                    $employeeName = $_POST['EmpName'];
                    $amount = $_POST['amount'];
                    $mail->Subject = 'For Payout';
                    $mail->Body = "
                        <html>
                        <body>
                        <p>Dear Mr./Ms./Mrs. $lastName,</p>
                        <p>Your assistance request is currently for payout on $Date at  $transaction_time_12hr.
                         You will received a total amount of $amount.<br>
                        Kindly proceed to $pswd.<br>
                        Please bring a valid ID and show this email upon arrival.<br>
                        Thank you for your patience and cooperation.</p><br>
                        <i>Important Reminder: You may request assistance again after 3 months  </i>


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
                        swal("Email send successful","","success")
                        .then((value) => {
                            if (value) {
                                window.location.href = "assistance.php";
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
                    window.location.href = "assistance.php";
                }
            });
            </script>
            </body>';}

        } else {
            echo "Error updating records: " . mysqli_error($con);
            header("Location: assistance.php");
            exit();
        }
            }
           
        }elseif ($PayoutType == "Cheque") {
            if ($amount < 5000) {
                $error = "";
                echo '<body>
            <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
            <script>
            swal("The amount must be at least 5,000. If you need a lower amount, kindly choose cash","","error")
            .then((value) => {
                if (value) {
                    exit(); // Prevent further execution
                }
            });
            </script>
            </body>';
            
            }
            else{
        $query = "UPDATE financialassistance f
        INNER JOIN beneficiary b ON b.Beneficiary_Id = f.Beneficiary_ID
        INNER JOIN transaction t ON t.Beneficiary_Id = f.Beneficiary_ID
        SET t.Given_Sched = '$Date', t.Given_Time = '$transaction_time', t.Status = '$Status', t.Emp_ID='$EmpID',f.Amount='$amount',f.PayoutType='$PayoutType'
        WHERE b.Beneficiary_Id = '$beneID'";
         $result2 = mysqli_query($con, $query);
         if ($result2) {
        $Status = $_POST['Status'];
        if ($Status !== "Pending for Requirements" && $Status !== "For Validation" &&  $Status !== "Release Payout") {    
        require 'phpmailer/src/Exception.php';
        require 'phpmailer/src/PHPMailer.php';
        require 'phpmailer/src/SMTP.php';

        $mail = new PHPMailer(true);
        $lastName = $record['Lastname'];
        $transaction_time = $_POST['time'];
        $transaction_time_12hr = date("g:i A", strtotime($transaction_time)); // Convert to 12-hour format
        
        $Email = $record['Email'];
        $status= $_POST['Status'];
        $pswd= $_POST['pswd'];
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
            if($status == 'For Payout') {
                $employeeName = $_POST['EmpName'];
                $amount = $_POST['amount'];
                $mail->Subject = 'For Payout';
                $mail->Body = "
                    <html>
                    <body>
                    <p>Dear Mr./Ms./Mrs. $lastName,</p>
                    <p>Your assistance request is currently for payout on $Date at  $transaction_time_12hr.
                     You will received  a cheque with total amount of $amount.<br>
                    Kindly proceed to $pswd<br>
                    Please bring a valid ID and show this email upon arrival.<br>
                    Thank you for your patience and cooperation.</p><br>
                    <i>Important Reminder: You may request assistance again after 3 months  </i>


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
                            window.location.href = "assistance.php";
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
                window.location.href = "assistance.php";
            }
        });
        </script>
        </body>';}

    } else {
        echo "Error updating records: " . mysqli_error($con);
        header("Location: assistance.php");
        exit();
    }
        }
       
    } 
            
        }else{
            $query = mysqli_query($con, "SELECT * FROM employees WHERE Emp_ID=$EmpID");
    
            if($result = mysqli_fetch_assoc($query)){
                $res_Id = $result['Emp_ID'];
                $res_Fname = $result['Firstname'];
                $res_Lname = $result['Lastname'];
                $role = $result['role'];
                $branch1 = $result['Office'];
                
            
            date_default_timezone_set('Asia/Manila');
            $ReceivedDate = date('Y-m-d'); // Set the current date for Given_Sched
            $ReceivedTime = date('H:i:s'); // Set the current date and time for transaction_time
            $Email = $record['Email'];
            $lastName = $record['Lastname'];
    
            $query2 = "UPDATE financialassistance f
             INNER JOIN beneficiary b ON b.Beneficiary_Id = f.Beneficiary_ID
             INNER JOIN transaction t ON t.Beneficiary_Id = f.Beneficiary_ID
             SET t.Status = 'Pending due to Insufficient funds', t.Emp_ID='$EmpID', t.Given_Sched = '$ReceivedDate',
                 t.Given_Time = '$ReceivedTime'
             WHERE b.Beneficiary_Id = '$beneID'";
    
     $result2 = mysqli_query($con, $query2);
    if($result2) {
    $lastName = $result['Lastname'];  // Assuming 'Lastname' is part of the $result array
    $Email = $result['Email'];  // Assuming 'Email' is part of the $result array
    $employeeName = $_POST['EmpName'];
    
    require 'phpmailer/src/Exception.php';
    require 'phpmailer/src/PHPMailer.php';
    require 'phpmailer/src/SMTP.php';
    
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
        $mail->Subject = 'Pending Application due to Insufficient Funds';
        $mail->Body = "
            <html>
            <body>
            <p>Dear Mr./Ms./Mrs. $lastName,</p>
            <p>I am sorry to inform you that we currently have insufficient funds available to process your application for assistance.<br></p>
           <p>As a result, your application is pending at the moment. We will keep you updated as soon as funds become available.<br><br></p>
            <p>Thank you for your cooperation. God Bless!<br><br></p>
            <p>Best regards,<br>$employeeName</p>
            <p>Provincial Government of Bataan - Special Assistance Program</p>
            </body>
            </html>
        ";
    
        $mail->send();
        echo '<body>
            <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
            <script>
            swal("You have insufficient balance","","error")
            .then((value) => {
                if (value) {
                    window.location.href = "assistance.php";
                }
            });
            </script>
            </body>';
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
    
            
        
    
    } 
    }else{
        echo '<body>
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
        <script>
        swal("This branch has no budget","","error")
        .then((value) => {
            if (value) {
                window.location.href = "assistance.php";
            }
        });
        </script>
        </body>';
            
    }
    }
            }
        }
    
    } 
    
        elseif ($Status == "Decline Request for Re-schedule") {
         $reason= $_POST['reason'];
            $query = "UPDATE financialassistance f
            INNER JOIN beneficiary b ON b.Beneficiary_Id = f.Beneficiary_ID
            INNER JOIN transaction t ON t.Beneficiary_Id = f.Beneficiary_ID
            SET  t.Status = '$Status', t.Emp_ID='$EmpID'
            WHERE b.Beneficiary_Id = '$beneID'";
             $result2 = mysqli_query($con, $query);
            if ($result2) {
            $Status = $_POST['Status'];
            if ($Status !== "Pending for Requirements" && $Status !== "For Validation" &&  $Status !== "Release Payout") {    
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
                        swal("Email send successful","","success")
                        .then((value) => {
                            if (value) {
                                window.location.href = "assistance.php";
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
                    window.location.href = "assistance.php";
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
                window.location.href = "assistance.php";
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

            $overlapQuery = "SELECT * FROM transaction WHERE Given_Sched = '$Date' AND Given_Time = '$transaction_time'AND Status = '$Status' AND Beneficiary_Id != '$beneID'";
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
              
            $query = "UPDATE financialassistance f
            INNER JOIN beneficiary b ON b.Beneficiary_Id = f.Beneficiary_ID
            INNER JOIN transaction t ON t.Beneficiary_Id = f.Beneficiary_ID
            SET t.Given_Sched = '$Date', t.Given_Time = '$transaction_time', t.Status = '$Status', t.Emp_ID='$EmpID'
            WHERE b.Beneficiary_Id = '$beneID'";
            
             $result2 = mysqli_query($con, $query);
            if ($result2) {
            $Status = $_POST['Status'];
            if ($Status !== "Pending for Requirements" && $Status !== "For Validation" &&  $Status !== "Release Payout") {    
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
               if($status == 'For Re-schedule') {
                    $employeeName = $_POST['EmpName'];
                    $mail->Subject = 'Re-schedule';
                    $mail->Body = "
                        <html>
                        <body>
                        <p>Dear Mr./Ms./Mrs. $lastName,</p>
                        <p>Your request for re-schedule has been accepted. Your new schedule is on $Date at  $transaction_time_12hr.</p>
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
                        swal("Email send successful","","success")
                        .then((value) => {
                            if (value) {
                                window.location.href = "assistance.php";
                            }
                        });
                        </script>
                        </body>';
            } catch (Exception $e) {
                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
         }
        }
    }
}  elseif ($Status == "Pending for Requirements"  ) {
    date_default_timezone_set('Asia/Manila');
    $Date = date('Y-m-d'); // Set the current date for Given_Sched
    $transaction_time = date('H:i:s'); // Set the current date and time for transaction_time

  
    $query = "UPDATE financialassistance f
              INNER JOIN beneficiary b ON b.Beneficiary_Id = f.Beneficiary_ID
              INNER JOIN transaction t ON t.Beneficiary_Id = f.Beneficiary_ID
              SET  t.Given_Sched = '$Date',
t.Given_Time = '$transaction_time', t.Status = '$Status', t.Emp_ID='$EmpID'
              WHERE b.Beneficiary_Id = '$beneID'";
  $result2 = mysqli_query($con, $query);
  if ($result2) {
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
  
  }

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
   
</head>
<body>
<div class="container">
        <div class="title" style = "margin-top: -10px;">Edit form</div>
        <form id="editForm" method="post">
            <input type="hidden" name="Beneficiary_Id" value="<?php echo $beneID; ?>">
            <input type="hidden" name="Emp_ID" value="<?php echo $EmpID; ?>">

            <div class="user-details">
                <div class="input-box">
                    <span class="details" style="color:#f5ca3b;">Date of Application:</span>
                    <span id="calendar" style="color:white; margin-top:10px;"><?php echo $record['Date']; ?></span>
                </div>
                <div class="input-box">
                <input disabled name="f" id="dt"  style="color:#f5ca3b;background-color: transparent;border: none;outline: none; font-size:15px;margin-top:15px;" type = "hidden" required value = "Date Validated" > 
              
                <input disabled name="f" id="dt2" type="hidden" style="background-color: transparent;border: none;outline: none; color:white;font-size:15px;margin-top:-9px;" required value="<?php echo $record['Given_Sched']; ?>">
                </div>
             
            </div>
        

            <div class="user-details">
                <div class="input-box">
                    <span class="details" style="color:  #f5ca3b; margin-top:2px;">Full Name</span>
                    <input disabled name="f" type = "text" required value = "<?php echo $record['Firstname'] . " " . $record['Lastname']; ?>" > 
                </div>

                <div class="input-box">
                    <span class="details"style="margin-top:2px;">Transaction Type</span>
                    <input disabled name="ff" type = "text" required value = "<?php echo $record['TransactionType']; ?>">
                </div>
            </div>
            
             <div class="user-details">
                <div class="input-box">
                    <span class="details">Financial Assistance Type</span>
                    <input disabled type = "text" required value = "<?php echo $record['FA_Type']; ?>">
                       <input type = "hidden" id="d" name="FA_Type" required value = "<?php echo $record['FA_Type']; ?>">
                       <input type = "hidden" id="stats" name="stats" required value = "<?php echo $record['Status']; ?>">
                
                    
                </div>
                


              <div class="input-box">
    <span class="details">Status</span>
    <?php
    $status = array('For Schedule','For Validation','Pending for Requirements','Pending for Payout' ,'For Payout','Request for Re-schedule','For Re-schedule', 'Decline Request for Re-schedule','Pending due to Insufficient funds');
 if ($record['Status'] == 'Pending due to Insufficient funds') {
    $today = date('Y-m-d');
  $oneMonthAgo = date('Y-m-d', strtotime('-1 month'));
  $givenSched = $record['Given_Sched'];
        if ($role === 'Community Affairs Officer'){
      
if ($givenSched < $oneMonthAgo) {
    echo "<input type='text' id='status' name='Status' value='For Schedule' readonly>";
            }
            else{
                echo "<input type='text' id='status' name='Status' value='For Payout' readonly>";

            }
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
    elseif ($record['Status'] == 'Pending for Payout') {
        // If the current status is "Pending for Requirements", display only "For Validation" in the dropdown
        echo "<input type='text' id='status' name='Status' value='For Payout' readonly>";
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
    elseif ($record['Status'] == 'For Payout') {
        // If the current status is "Pending for Requirements", display only "For Validation" in the dropdown
       if ($role === 'DSWD Employee'){
    
            // If the current status is "Pending for Requirements", display only "For Validation" in the dropdown
            echo "<select id='status' name='Status' onchange='handleStatusChange()'>";
            echo "<option value='For Payout'>For Payout</option>";
            echo "<option value='Release Payout'>Release Payout</option>";
            echo "</select>";
         
       }
       elseif ($role === 'PSWD Employee'){
    
        // If the current status is "Pending for Requirements", display only "For Validation" in the dropdown
        echo "<select id='status' name='Status' onchange='handleStatusChange()'>";
        echo "<option value='For Payout'>For Payout</option>";
        echo "<option value='Release Payout'>Release Payout</option>";
        echo "</select>";
     
   }else{
        echo "<input type='text' id='status' name='Status' value='For Payout' readonly>";
       
       }
    }
 
    ?>
                    </select>
                </div>

            </div>
            
            <input type="hidden" name="confirmed" id="confirmed" value="no">
            <br>
            <div id="payoutContainer" style="display: none;">
    <label for="payouttypeSelect" style="color:white;">Payout Type</label>
    <select id="payouttypeSelect" style="width:100px;" name="payouttypeSelect">
    <option value="Cash" <?php if ($record['PayoutType'] == "Cash") echo 'selected="selected"'; ?>>Cash</option>
    <option value="Cheque" <?php if ($record['PayoutType'] == "Cheque") echo 'selected="selected"'; ?>>Cheque</option>
    </select>
</div>
            <div id="requirements" style="display: none;"></div>
            <div id="emailFormat" class="emailformat">    </div>
           

            <div class="button-row">
                <input type="submit" style="margin-top:1px;" value="<?php echo $submitButtonText; ?>" id="submitbtn"name="submit" onclick="showConfirmation()" />
                <input type="button" style="margin-top:1px;"value="Cancel" name="cancel" onclick="cancelEdit()" />
            </div>
        </form>
    </div>
    <script>
        document.getElementById('calendar').min = new Date().toISOString().split("T")[0];
      function updateButtonText() {
    var status = document.getElementById("status").value;
    var submitBtn = document.getElementById("submitbtn");
    
    if (status == 'Release Payout') {
        submitbtn.style.display = 'inline';
        submitBtn.value = 'Release Payout';

    }
    else{submitBtn.value = 'Submit';}

}
      function handleStatusChange() {
        updateButtonText();
        var status = document.getElementById('status').value;
    var emailFormat = document.getElementById('emailFormat');
    var requirements = document.getElementById('requirements');
    var faType = "<?php echo $record['FA_Type']; ?>";
    var payoutType = document.getElementById('payouttypeSelect');
    var payoutContainer = document.getElementById('payoutContainer');
    var submitbtn = document.getElementById('submitbtn');
    var stats = document.getElementById('stats').value;
    var beneID = document.querySelector('input[name="Beneficiary_Id"]').value;
var empID = document.querySelector('input[name="Emp_ID"]').value;

    emailFormat.innerHTML = '';
    requirements.style.display = 'none'; 
    payoutContainer.style.display = 'none';

      

    if (status === 'For Schedule') {
        submitbtn.style.display = 'inline';
        document.getElementById('dt').type = 'text';
        document.getElementById('dt2').type = 'text';
if(stats === 'Pending due to Insufficient funds') {
        emailFormat.innerHTML = `
         <div style = "color: black; padding:15px; background:white; margin-top:20px;"> 
            Dear Mr./Ms./Mrs. <?php echo $record['Lastname']; ?>,<br><br>
            <p>I am writing to inform you that we now have the necessary funds available to proceed with your application.<br>
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

        submitbtn.addEventListener('click', function() {
    let newStatus;
    let checkedRequirements;

    // Determine which checkboxes to check based on faType
    if (faType === 'Burial') {
        checkedRequirements = getCheckedRequirements('burial_requirement[]');
    } else if (faType === 'Chemotherapy & Radiation') {
        checkedRequirements = getCheckedRequirements('chemo_requirement[]');
    } else if (faType === 'Dialysis') {
        checkedRequirements = getCheckedRequirements('dialysis_requirement[]');
    }

    // Debug: Log the checked requirements
    console.log("Checked Requirements: ", checkedRequirements);

    // Check if all checkboxes are checked
    let allChecked = areAllChecked(checkedRequirements);

    // Debug: Log the allChecked value
    console.log("All Checked: ", allChecked);

    // Set the new status based on whether all checkboxes are checked
    if (allChecked) {
        newStatus = 'Pending for Payout';
    } else {
        newStatus = 'Pending for Requirements';
    }

    // Update the status in the form
    document.getElementById('status').value = newStatus;
    document.getElementById('requirements').value = JSON.stringify(checkedRequirements);
    
    // Submit the form
    document.getElementById('statusForm').submit();
});

function areAllChecked(checkedRequirements) {
    for (let i = 0; i < checkedRequirements.length; i++) {
        if (!checkedRequirements[i]) {  // Check if the value is false
            return false;
        }
    }
    return true;
}

function getCheckedRequirements(name) {
    let checkboxes = document.getElementsByName(name);
    let checkedRequirements = [];
    for (let i = 0; i < checkboxes.length; i++) {
        checkedRequirements.push(checkboxes[i].checked); // Store the checked state (true/false)
    }
    return checkedRequirements;
}
        if (faType === 'Burial') {
            submitbtn.style.display = 'inline';
        requirements.style.display = 'block'; 
      
            requirements.innerHTML = `
            
             <div style = "color: black; padding:15px; background:white; margin-top:20px;">
                <h3 style = "color: blue;">REQUIREMENTS FOR BURIAL ASSISTANCE VALIDATION</h3>
                  <ul style = "text-align: left; margin-left:60px">

                    <input type="checkbox" name="burial_requirement[]" value="Registered Death Certificate (2 PHOTOCOPIES)"> Registered Death Certificate (2 PHOTOCOPIES) <br>
                    <input type="checkbox" name="burial_requirement[]" value="Funeral Contract with Balance (2 PHOTOCOPIES)"> Funeral Contract with Balance (2 PHOTOCOPIES) <br>
                    <input type="checkbox" name="burial_requirement[]" value="Promissory Note or Certification with Balance (1 ORIGINAL, 1 PHOTOCOPY)"> Promissory Note or Certification with Balance (1 ORIGINAL, 1 PHOTOCOPY) <br>
                    <input type="checkbox" name="burial_requirement[]" value="Sulat (SULAT KAMAY) na humihingi ng tulong kay Gov. Joet S. Garcia"> Sulat (SULAT KAMAY) na humihingi ng tulong kay Gov. Joet S. Garcia <br>
                    <input type="checkbox" name="burial_requirement[]" value="Xerox Valid ID ng Pasyente w/ 3 signatures or Xerox Valid ID ng naglalakad"> Xerox Valid ID ng Pasyente w/ 3 signatures or Xerox Valid ID ng naglalakad <br>
                    <input type="checkbox" name="burial_requirement[]" value="Brgy. Indigency (Pasyente) & Brgy. Indigency (Naglalakad)"> Brgy. Indigency (Pasyente) & Brgy. Indigency (Naglalakad) <br>
                    </ul>
                    <h3 style = "color: blue;">SUPPORTING DOCUMENTS</h3>
                    <ul style = "text-align: left; margin-left:60px">
                    <input type="checkbox" name="burial_requirement[]" value="Xerox copy ng Birth Certificate (Kung anak o magulang ang pasyente)"> Xerox copy ng Birth Certificate (Kung anak o magulang ang pasyente) <br>
                    <input type="checkbox" name="burial_requirement[]" value="Xerox ng Marriage Certificate (Kung asawa ang pasyente)"> Xerox ng Marriage Certificate (Kung asawa ang pasyente) <br>
                    <input type="checkbox" name="burial_requirement[]" value="Birth Certificate and Marriage Certificate (ng magulang kung kapatid ang pasyente)"> Birth Certificate and Marriage Certificate (ng magulang kung kapatid ang pasyente) <br>
                    </ul>
             </div>
              <input type="hidden"  name="EmpName" style="margin-top:15px;" value="<?php echo isset($res_Fname) ? $res_Fname . ' ' . $res_Lname : ''; ?>" placeholder="Enter employee name" required><br><br>
          
            `;
                 
   
        }

      
 else if (faType === 'Chemotherapy & Radiation') {
    submitbtn.style.display = 'inline';
    requirements.style.display = 'block'; 
            requirements.innerHTML = `
            
                  <div style = "color: black; padding:15px; background:white; margin-top:20px;">
                <h3 style = "color: blue;">REQUIREMENTS FOR CHEMOTHERAPY & RADIATION ASSISTANCE VALIDATIONS</h3>
                  <ul style = "text-align: left; margin-left:60px">
                    <input type="checkbox" name="chemo_requirement[]" value="Medical Abstract"> Medical Abstract <br>
                    <input type="checkbox" name="chemo_requirement[]" value="Request Letter from Barangay Health Center"> Request Letter from Barangay Health Center <br>
                    <input type="checkbox" name="chemo_requirement[]" value="Xerox Valid ID ng Pasyente"> Xerox Valid ID ng Pasyente <br>
                    <input type="checkbox" name="chemo_requirement[]" value="Xerox Valid ID ng Maglalakad"> Xerox Valid ID ng Maglalakad <br>
                    <input type="checkbox" name="chemo_requirement[]" value="BRGY. INDIGENCY (PASYENTE)"> BRGY. INDIGENCY (PASYENTE) <br>
                    </ul>
                    <h3 style = "color: blue;">SUPPORTING DOCUMENTS</h3>
                    <ul style = "text-align: left; margin-left:60px">
                    <input type="checkbox" name="chemo_requirement[]" value="Xerox copy ng Birth Certificate (Kung anak o magulang ang pasyente)"> Xerox copy ng Birth Certificate (Kung anak o magulang ang pasyente) <br>
                    <input type="checkbox" name="chemo_requirement[]" value="Xerox ng Marriage Certificate (Kung asawa ang pasyente)"> Xerox ng Marriage Certificate (Kung asawa ang pasyente) <br>
                    <input type="checkbox" name="chemo_requirement[]" value="Birth Certificate and Marriage Certificate (ng magulang kung kapatid ang pasyente)"> Birth Certificate and Marriage Certificate (ng magulang kung kapatid ang pasyente) <br>
                    </ul>
             </div>
               <input type="hidden"  name="EmpName" style="margin-top:15px;" value="<?php echo isset($res_Fname) ? $res_Fname . ' ' . $res_Lname : ''; ?>" placeholder="Enter employee name" required><br><br>
          
            `;
        }
        else if (faType === 'Dialysis') {
            submitbtn.style.display = 'inline';
            requirements.style.display = 'block'; 
            requirements.innerHTML = `
             <div style = "color: black; padding:15px; background:white; margin-top:-10px;">
                <h3 style = "color: blue;" >REQUIREMENTS FOR DIALYSIS</h3>
                <ul style = "text-align: left; margin-left:60px"><br>
                       <input type="checkbox"  name="dialysis_requirement[]"  value="Medical Abstract"> Medical Abstract<br>
                    <input type="checkbox"  name="dialysis_requirement[]"  value="Reseta ng Gamot NOTE: 1st & 2nd checks same date, same doctor, same signature with Doctor's License No.<br> (2 PHOTOCOPIES)"> Reseta ng Gamot NOTE: 1st & 2nd checks same date, same doctor, same signature with Doctor's License No.<br> (2 PHOTOCOPIES)<br>
                   <input type="checkbox"  name="dialysis_requirement[]" value="Brgy. Indigency (Pasyente) & Brgy. Indigency (Naglalakad)"> Brgy. Indigency (Pasyente) & Brgy. Indigency (Naglalakad)<br>
                    <input type="checkbox"  name="dialysis_requirement[]"  value="Sulat (SULAT KAMAY) na humihingi ng tulong kay Gov. Joet S. Garcia"> Sulat (SULAT KAMAY) na humihingi ng tulong kay Gov. Joet S. Garcia<br>
                   <input type="checkbox"  name="dialysis_requirement[]"  value="Xerox Valid ID ng Pasyente w/ 3 signatures or Xerox Valid ID ng naglalakad"> Xerox Valid ID ng Pasyente w/ 3 signatures or Xerox Valid ID ng naglalakad<br>
                </ul><br>
                <h3  style = "color: blue;">SUPPORTING DOCUMENTS</h3>
                    <ul style = "text-align: left; margin-left:60px"><br>
                    <input type="checkbox"  name="dialysis_requirement[]"  value="Xerox copy ng Birth Certificate (Kung anak o magulang ang pasyente)"> Xerox copy ng Birth Certificate (Kung anak o magulang ang pasyente) <br>
                    <input type="checkbox"  name="dialysis_requirement[]"  value="Xerox ng Marriage Certificate (Kung asawa ang pasyente)"> Xerox ng Marriage Certificate (Kung asawa ang pasyente) <br>
                    <input type="checkbox"  name="dialysis_requirement[]"  value="Birth Certificate and Marriage Certificate (ng magulang kung kapatid ang pasyente)"> Birth Certificate and Marriage Certificate (ng magulang kung kapatid ang pasyente) <br>
                    </ul>
             </div>
               <input type="hidden"  name="EmpName" style="margin-top:15px;" value="<?php echo isset($res_Fname) ? $res_Fname . ' ' . $res_Lname : ''; ?>" placeholder="Enter employee name" required><br><br>
          
            `;
        }
    } else if (status === 'Pending for Payout') {
        submitbtn.style.display = 'inline';
        emailFormat.innerHTML = `
         <div style = "color: black; padding:15px; background:white; margin-top:20px;">
            Dear Mr./Ms./Mrs. <?php echo $record['Lastname']; ?>,<br><br>
            <p>Your assistance request is currently pending for payout.<br>
            We are processing your application, and you will receive your financial assistance soon.<br><br>
            Thank you for your patience and cooperation.<br><br>
            Best regards,<br>
            <input type="text" name="EmpName" style="margin-top:15px;" value="<?php echo isset($res_Fname) ? $res_Fname . ' ' . $res_Lname : ''; ?>" placeholder="Enter employee name" required><br><br>
           Provincial Government of Bataan - Special Assistance Program</p>
         </div>
        `;
    }
    else if (status === 'Request for Re-schedule') {
        
        requirements.style.display = 'block'; 
        requirements.innerHTML = `
           
                <h3 style = "color: white;">Click this   <a href="https://mail.google.com/mail/u/0/?tab=rm&ogbl#inbox" target="_blank" style = "color:  #3cd82e;">link</a> to check the email of beneficiary.</h3>
           
            `;
            submitbtn.style.display = 'none'; // Hide the submit button
           
               
    } 
    
    else if (status === 'For Payout') { 
        function updateEmailFormat() {
        var selectedPayoutType = payoutType.value;
       
       
       
        if (selectedPayoutType === 'Cash') {
           
           // Branch field should be enabled regardless of amount field value
          
                   emailFormat.innerHTML = `
                       <div style="color: black; padding:15px; background:white; margin-top:20px;">
                           Dear Mr./Ms./Mrs. <?php echo $record['Lastname']; ?>,<br><br>
                                            <p>Your assistance request is currently for payout on <input type="date" id="calendar2" name="Given_Sched" min="<?php echo date('Y-m-d'); ?>" value="<?php echo $record['Given_Sched']; ?>" /> 
                    at <input type="time" id="time" name="time" value="<?php echo date("H:i", strtotime($record['time'])); ?>" />.<br>
                    You will receive a total amount of <input type="text" autocomplete="off" min="1" max="9999" name="amount" style="margin-top:10px;" placeholder="Enter amount" value="<?php echo $record['Amount']; ?>">.<br><br>
                         Kindly proceed to <span style="font-weight:bold;">PSWD. </span><input type="hidden" name="pswd" value="PSWD">   <br>
       
                           Please bring a valid ID and show this email upon arrival.<br><br>
                           Thank you for your patience and cooperation.<br><br>    
                           Best regards,<br>
                           <input type="text" id="empname" name="EmpName" style="margin-top:15px;" value="<?php echo isset($res_Fname) ? $res_Fname . ' ' . $res_Lname : ''; ?>" placeholder="Enter employee name" required><br><br>
                           Provincial Government of Bataan - Special Assistance Program
                       </p>
                       </div>
                   `;
                   var amountField = document.getElementsByName('amount')[0];
                  console.log('Amount field:', amountField);
           var branchField = document.getElementById('branch');
           var selectedPayoutType2 = document.getElementById('payouttypeSelect');
           var date2 = document.getElementById('calendar2');
           var time= document.getElementById('time');
           var empname= document.getElementById('empname');
           // Check if the amount field is empty or equal to 0
           if (amountField.value.trim() === '' || amountField.value.trim() === '0') {
               amountField.disabled = false;
              
               selectedPayoutType2.disabled=false;
               time.disabled = false;
               date2.disabled = false;
               empname.disabled = false;
               submitbtn.style.display = 'inline';
           } else {
               amountField.disabled = true;
              
               selectedPayoutType2.disabled=true;
               time.disabled = true;
              date2.disabled = true
             
              submitbtn.style.display = 'none';
           }
               
               } else if (selectedPayoutType === 'Cheque') {
                   submitbtn.style.display = 'inline';
                   emailFormat.innerHTML = `
                       <div style="color: black; padding:15px; background:white; margin-top:20px;">
                           Dear Mr./Ms./Mrs. <?php echo $record['Lastname']; ?>,<br><br>
                           <p>Your assistance request is currently for cheque payout on <input type="date" id="calendar3" name="Given_Sched" min="<?php echo date('Y-m-d'); ?>" value="<?php echo $record['Given_Sched']; ?>" /> 
                           at <input type="time" id="time" name="time" value="<?php echo date("H:i", strtotime($record['time'])); ?>" />.<br>
                           You will receive a cheque with a total amount of <input type="text" autocomplete="off" name="amount" style="margin-top:10px;" placeholder="Enter amount" value="<?php echo $record['Amount']; ?>">.<br>
                            Kindly proceed to <span style="font-weight:bold;">DSWD-Orani </span>
<input type="hidden" name="pswd" value="DSWD-Orani"> to collect your cheque.<br>
                           Please bring a valid ID and show this email upon arrival.<br><br>
                           Thank you for your patience and cooperation.<br><br>    
                           Best regards,<br>
                           <input type="text" name="EmpName" style="margin-top:15px;" value="<?php echo isset($res_Fname) ? $res_Fname . ' ' . $res_Lname : ''; ?>" placeholder="Enter employee name" required><br><br>
                           Provincial Government of Bataan - Special Assistance Program
                     
                           </p>
                       </div>
                   `;
                   var amountField2 = document.getElementsByName('amount')[0];
           var selectedPayoutType3 = document.getElementById('payouttypeSelect');
           var date3 = document.getElementById('calendar3');
           var time3= document.getElementById('time');
           var empname= document.getElementById('empname');
           // Check if the amount field is empty or equal to 0
           if (amountField2.value.trim() === '' || amountField2.value.trim() === '0') {
               amountField2.disabled = false;
              selectedPayoutType3.disabled=false;
               time3.disabled = false;
               date3.disabled = false;
               empname.disabled = false;
               submitbtn.style.display = 'inline';
           } else {
               amountField2.disabled = true;
                selectedPayoutType3.disabled=true;
               time3.disabled = true;
              date3.disabled = true
              
              submitbtn.style.display = 'none';
           }
                 
                   }
               }
       
               payoutContainer.style.display = 'block';
               updateEmailFormat(); // Initial call to update email format based on selected payout type
       
               // Event listener to update email format when payout type changes
               payoutType.addEventListener('change', function() {
                   updateEmailFormat();
               });
           }
       
    else if (status === 'For Re-schedule') { 
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
         <div style = "color: black; padding:15px; background:white; margin-top:-8px;">
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
    else if (status === 'Release Payout') { 
   
   emailFormat.innerHTML = `
           <div style = "color: black; padding:15px; background:white; margin-top:20px;">
           Dear Mr./Ms./Mrs. <?php echo $record['Lastname']; ?>,<br><br>
           <p>We have successfully provided your Financial Assistance. Please note that you may request another assistance after a period of 3 months. <br>
           Thank you for your cooperation. God Bless!<br> 
           
           Best regards,<br>
           <input type="text" name="EmpName" style="margin-top:15px;" value="<?php echo isset($res_Fname) ? $res_Fname . ' ' . $res_Lname : ''; ?>" placeholder="Enter employee name" required><br><br>
           Provincial Government of Bataan - Special Assistance Program</p>
       </div> 
           `;  
           submitbtn.style.display = 'inline';
           pdf.style.display = 'none';     }

}





    function cancelEdit() {
             window.location.href = "assistance.php";
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
</body>
</html>
