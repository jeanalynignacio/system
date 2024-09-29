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
  header("Location: employee-login.php");
  exit();
}

    $SQL = "SELECT b.*, t.*, l.*
            FROM beneficiary b
            INNER JOIN transaction t ON b.Beneficiary_Id = t.Beneficiary_Id
            INNER JOIN laboratories l ON b.Beneficiary_Id = l.Beneficiary_ID
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

 
 $allChecked = count($checkedRequirements) === 8; // Replace 8 with the actual number of requirements

    // Determine new status based on all requirements being checked
    if ($allChecked) {
        $newStatus = 'Pending for Releasing Guarantee Letter';
    } else {
        $newStatus = 'Pending for Requirements';
    }

$query = "UPDATE laboratories l
      INNER JOIN beneficiary b ON b.Beneficiary_Id = l.Beneficiary_ID
      INNER JOIN transaction t ON t.Beneficiary_Id = l.Beneficiary_ID
      SET t.Given_Sched  = '$Date',
          t.Given_Time = '$transaction_time',
          t.Emp_ID='$EmpID',
           l.branch='$branch1',
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
            window.location.href = "laboratories.php";
        }
    });
    </script>
    </body>';
} else {
    // Error in query execution
    echo "Error: " . mysqli_error($con);
}
}


elseif ($Status == "Release Guarantee Letter") {
    date_default_timezone_set('Asia/Manila');
    $ReceivedDate = date('Y-m-d'); // Set the current date for Given_Sched
    $ReceivedTime = date('H:i:s'); // Set the current date and time for transaction_time
    
    $beneID = $_POST['Beneficiary_Id'];
    $maxFileSize = 5000000; // 5MB in bytes

    if ($role == "Community Affairs Officer") {
        if ($_FILES["image"]["error"] === 4) {
            array_push($errors, $profileError = "Image does not exist");
        } elseif ($_FILES["image"]["size"] > $maxFileSize) {
            array_push($errors, $profileError = "Image size is too large. Please upload an image smaller than 5MB.");
        } else {
            $filename = $_FILES["image"]["name"];
            $tmpName = $_FILES["image"]["tmp_name"];
            $validImageExtension = ['jpg', 'jpeg', 'png'];
            $imageExtension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    
            if (!in_array($imageExtension, $validImageExtension)) {
                array_push($errors, $profileError = "Invalid image extension");
            } else {
                $newImageName = uniqid() . '.' . $imageExtension;
    
                if (empty($errors)) {
                    move_uploaded_file($tmpName, 'proofGL/' . $newImageName);
    
    $SQL = mysqli_query($con, "SELECT b.*, t.*, l.*
                               FROM beneficiary b
                               INNER JOIN transaction t ON b.Beneficiary_Id = t.Beneficiary_Id
                               INNER JOIN laboratories l ON b.Beneficiary_Id = l.Beneficiary_ID
                               WHERE b.Beneficiary_Id = '$beneID'");

    if($result = mysqli_fetch_assoc($SQL)){
        $branch = $result['branch'];
        $TransactionType = $result['TransactionType'];
        $AssistanceType = $result['AssistanceType'];
        $LabType = $result['LabType'];
        $EmpID = $_POST['Emp_ID'];  // Assuming Emp_ID is passed via POST
$Amount=$result['Amount'];
$EmpID = $_SESSION['EmpID'];
        // Insert into history table
        $query = "INSERT INTO history (Beneficiary_ID, ReceivedDate, ReceivedTime, TransactionType, AssistanceType, ReceivedAssistance, Emp_ID, branch,Amount)
                  VALUES ('$beneID', '$ReceivedDate', '$ReceivedTime', '$TransactionType', '$AssistanceType', 'Guarantee Letter', '$EmpID', '$branch','$Amount')";

        if(mysqli_query($con, $query)){
            $sql1 = "DELETE FROM transaction WHERE Beneficiary_Id='$beneID'";
            $sql2 = "DELETE FROM laboratories WHERE Beneficiary_ID='$beneID'";
            $sql5 = "DELETE FROM beneficiary WHERE Beneficiary_ID='$beneID'";
            $sql3 = "SELECT RemainingBal FROM budget WHERE AssistanceType='$AssistanceType' AND branch='$branch'";

            $result3 = mysqli_query($con, $sql3);

            if ($result3) {
                if ($resultbal = mysqli_fetch_assoc($result3)) {
                    if ($resultbal['RemainingBal'] != 0) {
                        $updateQuery = "UPDATE budget SET RemainingBal = RemainingBal - $Amount WHERE branch = '$branch' AND AssistanceType = '$AssistanceType'";
                        $result4 = mysqli_query($con, $updateQuery);

                        if ($result4 && mysqli_query($con, $sql1) && mysqli_query($con, $sql2) && mysqli_query($con, $sql5)) {
                            echo '<body>
                                  <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
                                  <script>
                                  swal("This beneficiary already received his/her assistance","","success")
                                  .then((value) => {
                                      if (value) {
                                          window.location.href = "laboratories.php";
                                      }
                                  });
                                  </script>
                                  </body>';
                        } else {
                            echo "Error updating budget or deleting records.";
                        }
                    } else {
                        echo '<body>
                              <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
                              <script>
                              swal("You have insufficient balance","","error")
                              .then((value) => {
                                  if (value) {
                                      window.location.href = "laboratories.php";
                                  }
                              });
                              </script>
                              </body>';
                    }
                } else {
                    echo '<body>
                          <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
                          <script>
                          swal("This branch has no budget","","error")
                          .then((value) => {
                              if (value) {
                                  exit();
                              }
                          });
                          </script>
                          </body>';
                }
            } else {
                echo '<body>
                      <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
                      <script>
                      swal("No existing balance","","error")
                      .then((value) => {
                          if (value) {
                              exit();
                          }
                      });
                      </script>
                      </body>';
            }
        } else {
            echo "Error inserting data into history table: " . mysqli_error($con);
        }
    }
}
}
}
} else{
date_default_timezone_set('Asia/Manila');
$ReceivedDate = date('Y-m-d'); // Set the current date for Given_Sched
$ReceivedTime = date('H:i:s'); // Set the current date and time for transaction_time

$beneID = $_POST['Beneficiary_Id'];

$SQL = mysqli_query($con, "SELECT b.*, t.*, h.*
FROM beneficiary b
INNER JOIN transaction t ON b.Beneficiary_Id = t.Beneficiary_Id
INNER JOIN laboratories h ON b.Beneficiary_Id = h.Beneficiary_ID
WHERE b.Beneficiary_Id = '$beneID'");

if($result = mysqli_fetch_assoc($SQL)){
$branch = $result['branch'];
        $TransactionType = $result['TransactionType'];
        $AssistanceType = $result['AssistanceType'];
        $ReceivedAssistance = "Guarantee Letter";
        $Amount = $result['Amount'];
        $EmpID = $_POST['Emp_ID']; // Assuming you have employee ID stored in session

        // Insert into history table
        $sql3 = "SELECT RemainingBal FROM budget WHERE AssistanceType='$AssistanceType' && branch='$branch'";
        $result3 = mysqli_query($con, $sql3);

      
        if ($result3) {
            if ($resultbal = mysqli_fetch_assoc($result3)) {
                if ($resultbal['RemainingBal'] != 0) {
                    $updateQuery = "UPDATE budget SET RemainingBal = RemainingBal - $Amount WHERE branch = '$branch' AND AssistanceType = '$AssistanceType'";
                    $result4 = mysqli_query($con, $updateQuery);

$query = "INSERT INTO history (Beneficiary_ID, ReceivedDate, ReceivedTime, TransactionType, AssistanceType, ReceivedAssistance, Emp_ID, Amount, branch)
VALUES ('$beneID', '$ReceivedDate', '$ReceivedTime', '$TransactionType', '$AssistanceType', 'Guarantee Letter', '$EmpID', '$Amount', '$branch')";

if(mysqli_query($con, $query)){
// Send the email
$lastName = $result['Lastname'];  // Assuming 'Lastname' is part of the $result array
$Email = $result['Email'];  // Assuming 'Email' is part of the $result array
$employeeName = $_POST['EmpName'];
$link= "http://localhost/public_html/feedback.php";
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
    $mail->Subject = 'Released Guarantee Letter';
    $mail->Body = "
        <html>
        <body>
        <p>Dear Mr./Ms./Mrs. $lastName,</p>
        <p>We have successfully provided your Guarantee Letter. Please note that you may request another assistance after a period of 3 months.</p>
<p>If you have some extra time, kindly answer our feedback form through this <a href='$link'>link</a>. Your input is greatly appreciated and will help us improve our service.<br></p>
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

// Delete records from transaction and laboratories tables after email is sent
$sql1 = "DELETE FROM transaction WHERE Beneficiary_Id='$beneID'";
$sql2 = "DELETE FROM laboratories WHERE Beneficiary_ID='$beneID'";
$result1 = mysqli_query($con, $sql1);
$result2 = mysqli_query($con, $sql2);
            if ($result4 && mysqli_query($con, $sql1) && mysqli_query($con, $sql2) ) {
                echo '<body>
                      <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
                      <script>
                      swal("This beneficiary already received his/her assistance","","success")
                      .then((value) => {
                          if (value) {
                              window.location.href = "laboratories.php";
                          }
                      });
                      </script>
                      </body>';
            }
         
    } else {
        echo '<body>
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
        <script>
        swal("You have insufficient balance","","error")
        .then((value) => {
            if (value) {
                window.location.href = "laboratories.php";
            }
        });
        </script>
        </body>';
    }
} else {
    echo '<body>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>
    swal("This branch has no budget","","error")
    .then((value) => {
        if (value) {
            window.location.href = "laboratories.php";
        }
    });
    </script>
    </body>';
}
} else {
echo "Error inserting data into history table: " . mysqli_error($con);
}
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
       
        $query = "UPDATE laboratories l
        INNER JOIN beneficiary b ON b.Beneficiary_Id = l.Beneficiary_ID
        INNER JOIN transaction t ON t.Beneficiary_Id = l.Beneficiary_ID
        SET t.Given_Sched  = '$Date',
            t.Given_Time = '$transaction_time',
            t.Emp_ID='$EmpID',
              l.branch='$branch1',
            t.Status = '$Status'
        
        WHERE b.Beneficiary_Id = '$beneID'";
      $result2 = mysqli_query($con, $query);
if ($result2) {
    $Status = $_POST['Status'];
    if ($Status !== "Pending for Requirements" && $Status !== "For Validation" &&  $Status !== "Release Guarantee Letter") {    
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
            <p>Best regards,<br>$employeeName<br>
            Provincial Government of Bataan - Special Assistance Program</p>
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
                        window.location.href = "laboratories.php";
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
            window.location.href = "laboratories.php";
        }
    });
    </script>
    </body>';}

} else {
    echo "Error updating records: " . mysqli_error($con);
    header("Location: laboratories.php");
    exit();
}
 
    }
}
elseif ($Status == "Pending for Releasing Guarantee Letter") {
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
        $query = "UPDATE laboratories l
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
    if ($Status !== "Pending for Requirements" && $Status !== "For Validation" &&  $Status !== "Release Guarantee Letter") {    
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
            $employeeName = $_POST['EmpName'];
            $mail->Subject = 'Pending for Releasing Guarantee Letter';
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
                        window.location.href = "laboratories.php";
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
            window.location.href = "laboratories.php";
        }
    });
    </script>
    </body>';}

} else {
    echo "Error updating records: " . mysqli_error($con);
    header("Location: laboratories.php");
    exit();
}
    }
   
}

elseif ($Status == "Releasing Guarantee Letter") {
    date_default_timezone_set('Asia/Manila');
    $Date = date('Y-m-d'); // Set the current date for Given_Sched
    $transaction_time = date('H:i:s'); // Set the current date and time for transaction_time
    $branch = $_POST['branch'];
    $PayoutType = "laboratories";
    $Amount = $_POST['Amount'];
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
        $query = "UPDATE laboratories l
        INNER JOIN beneficiary b ON b.Beneficiary_Id = l.Beneficiary_ID
        INNER JOIN transaction t ON t.Beneficiary_Id = l.Beneficiary_ID
        SET t.Given_Sched  = '$Date',
            t.Given_Time = '$transaction_time',
            t.Emp_ID='$EmpID',
            t.Status = '$Status',
            l.branch='$branch',
            l.Amount='$Amount',
            l.receivedassistance='Guarantee Letter'
        
        WHERE b.Beneficiary_Id = '$beneID'";
     $result2 = mysqli_query($con, $query);
     if ($result2) {
    $Status = $_POST['Status'];
    if ($Status !== "Pending for Requirements" && $Status !== "For Validation" &&  $Status !== "Release Guarantee Letter") {    
    require 'phpmailer/src/Exception.php';
    require 'phpmailer/src/PHPMailer.php';
    require 'phpmailer/src/SMTP.php';

    $mail = new PHPMailer(true);
    $lastName = $record['Lastname'];
    $transaction_time = $_POST['time'];
            $transaction_time_12hr = date("g:i A", strtotime($transaction_time)); // Convert to 12-hour format

    $Email = $record['Email'];
    $status= $_POST['Status'];
    $Amount= $_POST['Amount'];
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
        if($status == 'Releasing Guarantee Letter') {
            $employeeName = $_POST['EmpName'];
            $branch=$_POST['branch'];
            $mail->Subject = 'Releasing Guarantee Letter';
            $mail->Body = "
                <html>
                <body>
                <p>Dear Mr./Ms./Mrs. $lastName,</p>
                <p>You are currently set to receive your requested assistance. You may go on $Date at $transaction_time_12hr.</p>
                <p> You will receive a guarantee letter with the approved amount of $Amount>.<br><br>
                   <br></p>
               <p> Kindly proceed to $branch to claim your guarantee letter.<br></p>
                <p> Please bring a valid ID and show this email upon arrival.<br></p>
               <p>Thank you for your patience and cooperation.</p>
                <p>Best regards,<br>
                $employeeName<br>
                Provincial Government of Bataan - Special Assistance Program</p>
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
                        window.location.href = "laboratories.php";
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
            window.location.href = "laboratories.php";
        }
    });
    </script>
    </body>';}

} else {
    echo "Error updating records: " . mysqli_error($con);
    header("Location: laboratories.php");
    exit();
}
    }
   
}

elseif ($Status == "Decline Request for Re-schedule") {
   $reason= $_POST['reason'];
 
 $query = "UPDATE laboratories l
 INNER JOIN beneficiary b ON b.Beneficiary_Id = l.Beneficiary_ID
 INNER JOIN transaction t ON t.Beneficiary_Id = l.Beneficiary_ID
 SET  t.Emp_ID='$EmpID',
     t.Status = '$Status'
 
 WHERE b.Beneficiary_Id = '$beneID'";
  $result2 = mysqli_query($con, $query);
    if ($result2) {
    $Status = $_POST['Status'];
    if ($Status !== "Pending for Requirements" && $Status !== "For Validation" &&  $Status !== "Release Guarantee Letter") {    
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
                        window.location.href = "laboratories.php";
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
            window.location.href = "laboratories.php";
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
            window.location.href = "laboratories.php";
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
        $query = "UPDATE laboratories l
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
    if ($Status !== "Pending for Requirements" && $Status !== "For Validation" &&  $Status !== "Release Guarantee Letter") {    
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
                <p>Your request for re-schedule has been accepted. Your new schedule is on $Date at $transaction_time_12hr.</p>
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
                        window.location.href = "laboratories.php";
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
}

elseif ($Status == "Pending for Requirements"  ) {
    date_default_timezone_set('Asia/Manila');
    $Date = date('Y-m-d'); // Set the current date for Given_Sched
    $transaction_time = date('H:i:s'); // Set the current date and time for transaction_time

  
    $query = "UPDATE laboratories f
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
            window.location.href = "laboratories.php";
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
  
   <center> <title>Edit Form</title></center>
    <link rel="stylesheet" href="editformlab.css" />
  </head>
  
  <body>

    <div class="container">
      <div class="title"> Edit form </div>
      <form id="editForm"  method="post"> <!-- Changed method to POST -->
      <input type="hidden" name="Beneficiary_Id" value="<?php echo $beneID; ?>">
      <input type="hidden" name="Emp_ID" value="<?php echo $EmpID; ?>">

   
      <input type="hidden" name="role" id="role" value="<?php echo $role; ?>">
        
      
            <div class="user-details">
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
                    <span class="details" style="color:  #f5ca3b;"> Types of Laboratory </span>
                    <input disabled type="text" required value="<?php echo $record['LabType']; ?>" name="LabType"/>
                </div>

            <div class="input-box">
                    <span class="details" style="color:  #f5ca3b;margin: left 20px;">Status</span>
                        <?php
                       
    $status = array('For Schedule','For Validation','Pending for Requirements','Pending for Releasing Guarantee Letter' ,'Releasing Guarantee Letter','Request for Re-schedule','For Re-schedule', 'Decline Request for Re-schedule','Release Guarantee Letter');

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
    elseif ($record['Status'] == 'Pending for Releasing Guarantee Letter') {
        // If the current status is "Pending for Requirements", display only "For Validation" in the dropdown
        echo "<input type='text' id='status' name='Status' value='Releasing Guarantee Letter' readonly>";
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
    elseif ($record['Status'] == 'Releasing Guarantee Letter') {
        // If the current status is "Pending for Requirements", display only "For Validation" in the dropdown
       if ($role === 'Admin'){
    
            // If the current status is "Pending for Requirements", display only "For Validation" in the dropdown
            echo "<select id='status' name='Status' onchange='handleStatusChange()'>";
            echo "<option value='Releasing Guarantee Letter'>Releasing Guarantee Letter</option>";
            echo "<option value='Release Guarantee Letter'>Release Guarantee Letter</option>";
            echo "</select>";
         
       }else{
        echo "<input type='text' id='status' name='Status' value='Releasing Guarantee Letter' readonly>";
       
       }
    }
   
    ?>
                    </select>
             </div>
             </div>
                   <input type="hidden" name="confirmed" id="confirmed" value="no">
       
       
            <div id="requirements" style="display: none; "></div>
            <div id="emailFormat" class="emailformat">
                    <!-- Email content will be updated based on the selected status -->
              
              
                </div>
           
                <div class="button-row">
                <input type="submit" value="Submit" id="submitbtn" name="submit" onclick="showConfirmation()" />
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
            var role = document.getElementById('role').value;
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
                        
                        <h3 style="color:blue;">REQUIREMENTS FOR LABORATORY ASSISTANCE VALIDATION</h3>
                        <ul style = "text-align: left; margin-left:60px" >
                            <input type="checkbox" name="requirement[]" value="Death Laboratories result"> LABORATORIES RESULT <br>
                            <input type="checkbox" name="requirement[]" value="Request Letter from Barangay Health Center"> REQUEST LETTER FROM BARANGAY HEALTH CENTER <br>
                            <input type="checkbox" name="requirement[]" value="Xerox Valid ID ng Pasyente"> XEROX VALID ID NG PASYENTE <br>
                            <input type="checkbox" name="requirement[]" value="Xerox Valid ID ng Maglalakad"> XEROX VALID ID NG MAGLALAKAD<br>
                            <input type="checkbox" name="requirement[]" value="BRGY. INDIGENCY (PASYENTE)"> BRGY. INDIGENCY (PASYENTE) <br>
          
                        </ul>
                        <h3 style="color:blue;margin-top:15px;">SUPPORTING DOCUMENTS</h3>
                         <ul style = "text-align: left; margin-left:60px" >
                            <input type="checkbox" name="requirement[]" value="XEROX COPY NG BIRTH CERTIFICATE (KUNG ANAK O MAGULANG ANG PASYENTE)"> XEROX COPY NG BIRTH CERTIFICATE (KUNG ANAK O MAGULANG ANG PASYENTE) <br>
                            <input type="checkbox" name="requirement[]" value="XEROX NG MARRIAGE (CERTIFICATE KUNG ASAWA ANG PASYENTE)"> XEROX NG MARRIAGE (CERTIFICATE KUNG ASAWA ANG PASYENTE) <br>
                            <input type="checkbox" name="requirement[]" value="BIRTH CERTIFICATE AND MARRIAGE CERTIFICATE (NG MAGULANG) KUNG KAPATID ANG PASYENTE"> BIRTH CERTIFICATE AND MARRIAGE CERTIFICATE (NG MAGULANG) KUNG KAPATID ANG PASYENTE <br>
                           
                    </ul>
                    </div>
                `;
            } else if (status === 'Pending for Releasing Guarantee Letter') {
                submitbtn.style.display = 'inline';
                emailFormat.innerHTML = `
                    <div style="color: black; padding:15px; background:white; margin-top:20px;">
                        Dear Mr./Ms./Mrs. <?php echo $record['Lastname']; ?>,<br><br>
                        <p>Your assistance request is currently pending for payout.<br>
                        We are processing your application, and you will receive your financial assistance soon.<br><br>
                        Thank you for your patience and cooperation.<br><br>
                        Best regards,<br>
                        <input type="text" name="EmpName" style="margin-top:15px;"  value="<?php echo isset($res_Fname) ? $res_Fname . ' ' . $res_Lname : ''; ?>" placeholder="Enter employee name" required><br><br>
                        Provincial Government of Bataan - Special Assistance Program</p>
                    </div>
                `;
                } else if (status === 'Request for Re-schedule') {
                requirements.style.display = 'block';
                requirements.innerHTML = `
                    <h3 style="color: white; margin-top:15px;" >Click this <a href="https://mail.google.com/mail/u/0/?tab=rm&ogbl#inbox" target="_blank" style="color:  #3cd82e;">link</a> to check the email of beneficiary.</h3>
                `;
                submitbtn.style.display = 'none';
            } 
            else if (status === 'Releasing Guarantee Letter' && role==='Admin') { 
                submitbtn.style.display = 'inline';
              
                emailFormat.innerHTML = `
                    <div style="color: black; padding:15px; background:white; margin-top:20px;">
                        Dear Mr./Ms./Mrs. <?php echo $record['Lastname']; ?>,<br><br>
                        <p>You are currently set to receive your requested assistance. You may go on <input type="date" id="calendar2" name="Given_Sched" min="<?php echo date('Y-m-d'); ?>" value="<?php echo $record['Given_Sched']; ?>" /> 
                        at <input type="time" id="time" name="time" value="<?php echo date("H:i", strtotime($record['time'])); ?>" />.<br>
                       You will receive a guarantee letter with the approved amount of <input type="text" autocomplete="off" name="Amount" style="margin-top:10px;" placeholder="Enter amount" value="<?php echo $record['Amount']; ?>">.<br><br>
                                    Kindly proceed to <select name="branch" id="branch" style="margin-top:5px;">
    <option value="PGB-Balanga Branch" <?php if ($record['branch'] == "PGB-Balanga Branch") echo 'selected="selected"'; ?>>PGB-Balanga Branch</option>
    <option value="PGB-Dinalupihan Branch" <?php if ($record['branch'] == "PGB-Dinalupihan Branch") echo 'selected="selected"'; ?>>PGB-Dinalupihan Branch</option>
    <option value="PGB-Hermosa Branch" <?php if ($record['branch'] == "PGB-Hermosa Branch") echo 'selected="selected"'; ?>>PGB-Hermosa Branch</option>
    <option value="PGB-Mariveles Branch" <?php if ($record['branch'] == "PGB-Mariveles Branch") echo 'selected="selected"'; ?>>PGB-Mariveles Branch</option>
</select>to claim your guarantee letter. <br>

                    Please bring a valid ID and show this email upon arrival.<br><br>
                    Thank you for your patience and cooperation.<br><br>    
                    Best regards,<br>
                    <input type="text" id="empname" name="EmpName" style="margin-top:15px;" value="<?php echo isset($res_Fname) ? $res_Fname . ' ' . $res_Lname : ''; ?>" placeholder="Enter employee name" required><br><br>
                    Provincial Government of Bataan - Special Assistance Program
                </p>
                </div>
                `;
            var amountField = document.getElementsByName('Amount')[0];
    var branchField = document.getElementById('branch');
    var date2 = document.getElementById('calendar2');
    var time= document.getElementById('time');
    var empname= document.getElementById('empname');
    // Check if the amount field is empty or equal to 0
    if (amountField.value.trim() === '' || amountField.value.trim() === '0') {
        amountField.disabled = false;
        branchField.disabled = false;
        time.disabled = false;
        date2.disabled = false;
        empname.disabled = false;
        submitbtn.style.display = 'inline';
    } else {
        amountField.disabled = true;
        branchField.disabled = true;
        time.disabled = true;
       date2.disabled = true
       empname.disabled = true;
       submitbtn.style.display = 'none';
    }
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
            } else if (status === 'Pending for Requirements') {
                
               
                submitbtn.style.display = 'none';
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
    else if (status === 'Pending for Requirements') {
            submitbtn.style.display = 'none'; 
       
    }
    else if (status === 'Release Guarantee Letter') {
      
        emailFormat.innerHTML = `
                 <div style = "color: black; padding:15px; background:white; margin-top:20px;">
                Dear Mr./Ms./Mrs. <?php echo $record['Lastname']; ?>,<br><br>
                <p>We have successfully provided your Guarantee Letter. Please note that you may request another assistance after a period of 3 months. <br>
                 If you have an extra time kindly answer our feedback form through this link.  Your input is greatly appreciated and will help us improve our service.<br> 
                Thank you for your cooperation. God Bless!<br>
                Best regards,<br>
                <input type="text" name="EmpName" style="margin-top:15px;" value="<?php echo isset($res_Fname) ? $res_Fname . ' ' . $res_Lname : ''; ?>" placeholder="Enter employee name" required><br><br>
                Provincial Government of Bataan - Special Assistance Program</p>
            </div> 
                `;
                submitbtn.style.display = 'inline';
                pdf.style.display = 'none';     
    }
        }

        // Call handleStatusChange on page load to set the initial state
        document.addEventListener('DOMContentLoaded', function() {
            handleStatusChange();
        });


        function cancelEdit() {
            window.location.href = "laboratories.php";
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
    
    </script>

</body>
</html>