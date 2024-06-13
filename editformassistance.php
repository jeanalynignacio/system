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
        $FA_Type = $_POST['FA_Type'];
        $Status = $_POST['Status'];
        $EmpID = $_POST['Emp_ID'];

        if ($Status == "For Validation") {
            date_default_timezone_set('Asia/Manila');
                $Date = date('Y-m-d'); // Set the current date for Given_Sched
                $transaction_time = date('H:i:s'); // Set the current date and time for transaction_time
    
               
                $query = "UPDATE financialassistance f
                INNER JOIN beneficiary b ON b.Beneficiary_Id = f.Beneficiary_ID
                INNER JOIN transaction t ON t.Beneficiary_Id = f.Beneficiary_ID
                SET t.Status = '$Status', t.Emp_ID='$EmpID',t.Given_Sched = '$Date',
            t.Given_Time = '$transaction_time'
                WHERE b.Beneficiary_Id = '$beneID'";
       
    }
    
         
    elseif ($Status == "Done") {
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

$TransactionType = $result['TransactionType'];
$AssistanceType1 = $result['AssistanceType'];
$FA_Type = $result['FA_Type'];
$AssistanceType = $AssistanceType1 . "-" . $FA_Type;
$ReceivedAssistance = $result['Amount'];
$beneID = $_POST['Beneficiary_Id'];
$query ="INSERT INTO history( Beneficiary_ID, ReceivedDate, ReceivedTime,TransactionType,AssistanceType,ReceivedAssistance,Emp_ID) VALUES ('$beneID', '$ReceivedDate', '$ReceivedTime', ' $TransactionType', '$AssistanceType', '$ReceivedAssistance','$EmpID' )";
if(mysqli_query($con, $query)){
    
    $sql1 = "DELETE FROM transaction  WHERE Beneficiary_Id='$beneID'";
    $sql2 = "DELETE FROM financialassistance  WHERE Beneficiary_ID='$beneID'";

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
                        window.location.href="assistance.php";
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
            }else{
                $query = "UPDATE financialassistance f
                INNER JOIN beneficiary b ON b.Beneficiary_Id = f.Beneficiary_ID
                INNER JOIN transaction t ON t.Beneficiary_Id = f.Beneficiary_ID
                SET t.Given_Sched = '$Date', t.Given_Time = '$transaction_time', t.Status = '$Status', t.Emp_ID='$EmpID'
                WHERE b.Beneficiary_Id = '$beneID'";
                  $result2 = mysqli_query($con, $query);
            
         if ($result2) {
            $Status = $_POST['Status'];
            if ($Status !== "Pending for Requirements" && $Status !== "For Validation" &&  $Status !== "Done") {    
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
                    <p>   If you are unable to attend the scheduled appointment, you may request a new appointment by clicking on this  <a href='https://pgbataansap24.000webhostapp.com/requestresched.php'> link. </a> Please ensure that your reasons are valid and clearly explained so that your request can be considered.<br> 
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
            $query = "UPDATE financialassistance f
            INNER JOIN beneficiary b ON b.Beneficiary_Id = f.Beneficiary_ID
            INNER JOIN transaction t ON t.Beneficiary_Id = f.Beneficiary_ID
            SET t.Given_Sched = '$Date', t.Given_Time = '$transaction_time', t.Status = '$Status', t.Emp_ID='$EmpID'
            WHERE b.Beneficiary_Id = '$beneID'";
             $result2 = mysqli_query($con, $query);
             if ($result2) {
            $Status = $_POST['Status'];
            if ($Status !== "Pending for Requirements" && $Status !== "For Validation" &&  $Status !== "Done") {    
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
        
        elseif ($Status == "For Payout") {
            date_default_timezone_set('Asia/Manila');
            $Date = date('Y-m-d'); // Set the current date for Given_Sched
            $transaction_time = date('H:i:s'); // Set the current date and time for transaction_time
            $amount = $_POST['amount'];
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
            $query = "UPDATE financialassistance f
            INNER JOIN beneficiary b ON b.Beneficiary_Id = f.Beneficiary_ID
            INNER JOIN transaction t ON t.Beneficiary_Id = f.Beneficiary_ID
            SET t.Given_Sched = '$Date', t.Given_Time = '$transaction_time', t.Status = '$Status', t.Emp_ID='$EmpID',f.Amount='$amount'
            WHERE b.Beneficiary_Id = '$beneID'";
             $result2 = mysqli_query($con, $query);
             if ($result2) {
            $Status = $_POST['Status'];
            if ($Status !== "Pending for Requirements" && $Status !== "For Validation" &&  $Status !== "Done") {    
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
                if($status == 'For Payout') {
                    $employeeName = $_POST['EmpName'];
                    $amount = $_POST['amount'];
                    $mail->Subject = 'For Payout';
                    $mail->Body = "
                        <html>
                        <body>
                        <p>Dear Mr./Ms./Mrs. $lastName,</p>
                        <p>Your assistance request is currently for payout on $Date at $transaction_time.</p>
                         You will received a total amount of $amount<br>
                        Kindly proceed to PGB-Hermosa Branch<br>
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
            if ($Status !== "Pending for Requirements" && $Status !== "For Validation" &&  $Status !== "Done") {    
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
              
            $query = "UPDATE financialassistance f
            INNER JOIN beneficiary b ON b.Beneficiary_Id = f.Beneficiary_ID
            INNER JOIN transaction t ON t.Beneficiary_Id = f.Beneficiary_ID
            SET t.Given_Sched = '$Date', t.Given_Time = '$transaction_time', t.Status = '$Status', t.Emp_ID='$EmpID'
            WHERE b.Beneficiary_Id = '$beneID'";
            
             $result2 = mysqli_query($con, $query);
            if ($result2) {
            $Status = $_POST['Status'];
            if ($Status !== "Pending for Requirements" && $Status !== "For Validation" &&  $Status !== "Done") {    
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
            $query = "UPDATE financialassistance f
                      INNER JOIN beneficiary b ON b.Beneficiary_Id = f.Beneficiary_ID
                      INNER JOIN transaction t ON t.Beneficiary_Id = f.Beneficiary_ID
                      SET  t.Given_Sched = '$Date',
        t.Given_Time = '$transaction_time', t.Status = '$Status', t.Emp_ID='$EmpID'
                      WHERE b.Beneficiary_Id = '$beneID'";
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
            </div>
            
             <div class="user-details">
                <div class="input-box">
                    <span class="details">Financial Assistance Type</span>
                    <input disabled type = "text" required value = "<?php echo $record['FA_Type']; ?>">
                       <input type = "hidden" name="FA_Type" required value = "<?php echo $record['FA_Type']; ?>">
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
                       $status = array('For Schedule','For Validation','Pending for Requirements','Pending for Payout' ,'For Payout','Request for Re-schedule','For Re-schedule', 'Decline Request for Re-schedule', 'Done');
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

       
            <div id="payouttype" class="emailformat">
                    </div>
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
      
      function handleStatusChange() {
    var status = document.getElementById('status').value;
    var emailFormat = document.getElementById('emailFormat');
    var requirements = document.getElementById('requirements');
    var faType = "<?php echo $record['FA_Type']; ?>";
    var payouttype = document.getElementById('payouttype');
    payouttype.innerHTML = '';
    emailFormat.innerHTML = '';
    requirements.style.display = 'none'; // Hide requirements by default

    if (status === 'For Schedule') {
        emailFormat.innerHTML = `
         <div style = "color: black; padding:15px; background:white; margin-top:20px;"> 
            Dear Mr./Ms./Mrs. <?php echo $record['Lastname']; ?>,<br><br>
            <p>I am writing to inform you that your request for scheduling has been approved.<br>
            Your schedule has been set for <input type="date" id="calendar" name="Given_Sched" value="<?php echo $record['Given_Sched']; ?>" /> 
            at <input type="time" id="time" name="time" value="<?php echo date("H:i", strtotime($record['transaction_time'])); ?>" />. We kindly expect your presence on the said date.<br>
          <br>
          If you are unable to attend the scheduled appointment, you may request a new appointment by clicking on this  <a href='https://pgbataansap24.000webhostapp.com/requestresched.php' style = "color:  blue;"> link. </a> Please ensure that your reasons are valid and clearly explained so that your request can be considered.
Please note that your reasons may need to be verified to avoid any inconvenience to other clients and our schedule. Thank you for your understanding and cooperation.
 
            Best regards,<br>
            <input type="text" name="EmpName" style="margin-top:15px;" value="<?php echo isset($res_Fname) ? $res_Fname . ' ' . $res_Lname : ''; ?>" placeholder="Enter employee name" required><br><br>
            Provincial Government of Bataan - Special Assistance Program</p>
         </div> 
        `;
    } else if (status === 'For Validation') {
        requirements.style.display = 'block'; 
        if (faType === 'Burial') {
            requirements.innerHTML = `
            
             <div style = "color: black; padding:15px; background:white; margin-top:20px;">
                <h3 style = "color: blue;">REQUIREMENTS FOR BURIAL ASSISTANCE VALIDATION</h3>
                  <ul style = "text-align: left; margin-left:60px">
                    <input type="checkbox" name="requirement" value="Registered Death Certificate (2 PHOTOCOPIES)"> Registered Death Certificate (2 PHOTOCOPIES) <br>
                    <input type="checkbox" name="requirement" value="Funeral Contract with Balance (2 PHOTOCOPIES)"> Funeral Contract with Balance (2 PHOTOCOPIES) <br>
                    <input type="checkbox" name="requirement" value="Promissory Note or Certification with Balance (1 ORIGINAL, 1 PHOTOCOPY)"> Promissory Note or Certification with Balance (1 ORIGINAL, 1 PHOTOCOPY) <br>
                    <input type="checkbox" name="requirement" value="Sulat (SULAT KAMAY) na humihingi ng tulong kay Gov. Joet S. Garcia"> Sulat (SULAT KAMAY) na humihingi ng tulong kay Gov. Joet S. Garcia <br>
                    <input type="checkbox" name="requirement" value="Xerox Valid ID ng Pasyente w/ 3 signatures or Xerox Valid ID ng naglalakad"> Xerox Valid ID ng Pasyente w/ 3 signatures or Xerox Valid ID ng naglalakad <br>
                    <input type="checkbox" name="requirement" value="Brgy. Indigency (Pasyente) & Brgy. Indigency (Naglalakad)"> Brgy. Indigency (Pasyente) & Brgy. Indigency (Naglalakad) <br>
                    </ul>
                    <h3 style = "color: blue;">SUPPORTING DOCUMENTS</h3>
                    <ul style = "text-align: left; margin-left:60px">
                    <input type="checkbox" name="requirement" value="Xerox copy ng Birth Certificate (Kung anak o magulang ang pasyente)"> Xerox copy ng Birth Certificate (Kung anak o magulang ang pasyente) <br>
                    <input type="checkbox" name="requirement" value="Xerox ng Marriage Certificate (Kung asawa ang pasyente)"> Xerox ng Marriage Certificate (Kung asawa ang pasyente) <br>
                    <input type="checkbox" name="requirement" value="Birth Certificate and Marriage Certificate (ng magulang kung kapatid ang pasyente)"> Birth Certificate and Marriage Certificate (ng magulang kung kapatid ang pasyente) <br>
                    </ul>
             </div>
            `;
            
        } else if (faType === 'Chemotherapy & Radiation') {
            requirements.innerHTML = `
            
                  <div style = "color: black; padding:15px; background:white; margin-top:20px;">
                <h3 style = "color: blue;">REQUIREMENTS FOR CHEMOTHERAPY & RADIATION ASSISTANCE VALIDATIONS</h3>
                  <ul style = "text-align: left; margin-left:60px">
                    <input type="checkbox" name="requirement" value="Medical Abstract"> Medical Abstract <br>
                    <input type="checkbox" name="requirement" value="Request Letter from Barangay Health Center"> Request Letter from Barangay Health Center <br>
                    <input type="checkbox" name="requirement" value="Xerox Valid ID ng Pasyente"> Xerox Valid ID ng Pasyente <br>
                    <input type="checkbox" name="requirement" value="Xerox Valid ID ng Maglalakad"> Xerox Valid ID ng Maglalakad <br>
                    <input type="checkbox" name="requirement" value="BRGY. INDIGENCY (PASYENTE)"> BRGY. INDIGENCY (PASYENTE) <br>
                    </ul>
                    <h3 style = "color: blue;">SUPPORTING DOCUMENTS</h3>
                    <ul style = "text-align: left; margin-left:60px">
                    <input type="checkbox" name="requirement" value="Xerox copy ng Birth Certificate (Kung anak o magulang ang pasyente)"> Xerox copy ng Birth Certificate (Kung anak o magulang ang pasyente) <br>
                    <input type="checkbox" name="requirement" value="Xerox ng Marriage Certificate (Kung asawa ang pasyente)"> Xerox ng Marriage Certificate (Kung asawa ang pasyente) <br>
                    <input type="checkbox" name="requirement" value="Birth Certificate and Marriage Certificate (ng magulang kung kapatid ang pasyente)"> Birth Certificate and Marriage Certificate (ng magulang kung kapatid ang pasyente) <br>
                    </ul>
             </div>
            `;
        }
        else if (faType === 'Dialysis') {
            requirements.innerHTML = `
             <div style = "color: black; padding:15px; background:white; margin-top:20px;">
                <h3 style = "color: blue;" >REQUIREMENTS FOR DIALYSIS</h3>
                <ul style = "text-align: left; margin-left:60px"><br>
                       <input type="checkbox" name="requirement" value="Medical Abstract"> Medical Abstract<br>
                    <input type="checkbox" name="requirement" value="Reseta ng Gamot NOTE: 1st & 2nd checks same date, same doctor, same signature with Doctor's License No.<br> (2 PHOTOCOPIES)"> Reseta ng Gamot NOTE: 1st & 2nd checks same date, same doctor, same signature with Doctor's License No.<br> (2 PHOTOCOPIES)<br>
                   <input type="checkbox" name="requirement" value="Brgy. Indigency (Pasyente) & Brgy. Indigency (Naglalakad)"> Brgy. Indigency (Pasyente) & Brgy. Indigency (Naglalakad)<br>
                    <input type="checkbox" name="requirement" value="Sulat (SULAT KAMAY) na humihingi ng tulong kay Gov. Joet S. Garcia"> Sulat (SULAT KAMAY) na humihingi ng tulong kay Gov. Joet S. Garcia<br>
                   <input type="checkbox" name="requirement" value="Xerox Valid ID ng Pasyente w/ 3 signatures or Xerox Valid ID ng naglalakad"> Xerox Valid ID ng Pasyente w/ 3 signatures or Xerox Valid ID ng naglalakad<br>
                </ul><br>
                <h3  style = "color: blue;">SUPPORTING DOCUMENTS</h3>
                    <ul style = "text-align: left; margin-left:60px"><br>
                    <input type="checkbox" name="requirement" value="Xerox copy ng Birth Certificate (Kung anak o magulang ang pasyente)"> Xerox copy ng Birth Certificate (Kung anak o magulang ang pasyente) <br>
                    <input type="checkbox" name="requirement" value="Xerox ng Marriage Certificate (Kung asawa ang pasyente)"> Xerox ng Marriage Certificate (Kung asawa ang pasyente) <br>
                    <input type="checkbox" name="requirement" value="Birth Certificate and Marriage Certificate (ng magulang kung kapatid ang pasyente)"> Birth Certificate and Marriage Certificate (ng magulang kung kapatid ang pasyente) <br>
                    </ul>
             </div>
            `;
        }
    } else if (status === 'Pending for Payout') {
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
    } else if (status === 'For Payout') { 

   

        emailFormat.innerHTML = `
          
     
         <div style = "color: black; padding:15px; background:white; margin-top:20px;">
            Dear Mr./Ms./Mrs. <?php echo $record['Lastname']; ?>,<br><br>
            <p>Your assistance request is currently for payout on <input type="date" id="calendar" name="Given_Sched" value="<?php echo $record['Given_Sched']; ?>" /> 
            at <input type="time" id="time" name="time" value="<?php echo date("H:i", strtotime($record['time'])); ?>" />.<br>
            You will received a total amount of  <input type="text" name="amount" style="margin-top:15px;" placeholder="Enter amount" value="<?php echo $record['Amount']; ?>">.<br><br>
            Kindly proceed to PGB-Hermosa Branch<br>
            Thank you for your patience and cooperation.<br><br>    
            Best regards,<br>
            <input type="text" name="EmpName" style="margin-top:15px;" value="<?php echo isset($res_Fname) ? $res_Fname . ' ' . $res_Lname : ''; ?>" placeholder="Enter employee name" required><br><br>
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
            <input type="text" name="EmpName" style="margin-top:15px;" value="<?php echo isset($res_Fname) ? $res_Fname . ' ' . $res_Lname : ''; ?>" placeholder="Enter employee name" required><br><br>
            Provincial Government of Bataan - Special Assistance Program</p>
         </div> 
        `;
}
else if (status === 'Decline Request for Re-schedule') {
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
