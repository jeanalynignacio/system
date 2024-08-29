<?php 
session_start();
include("php/config.php");
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
require 'dompdf/autoload.inc.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$options = new Options();
$options->set('chroot', realpath(''));
$dompdf = new Dompdf($options);

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
        $branch1 = $result['Office'];
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

    $errors = []; 
    $profileError = "";
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['submit']) || isset($_POST['Release']))) {

  // Check if the user confirmed the update
  if(isset($_POST['confirmed']) && $_POST['confirmed'] === "yes") {
   
    $beneID=$_POST['Beneficiary_Id'];
        $Status=$_POST['Status'];
       $EmpID = $_POST['Emp_ID'];
      
      // Construct the update query
   
if ($Status == "For Validation") {
    date_default_timezone_set('Asia/Manila');
    $Date = date('Y-m-d'); // Set the current date for Given_Sched
    $transaction_time = date('H:i:s'); // Set the current date and time for transaction_time
    $billamount = $_POST['billamount'];
    $checkedRequirements = isset($_POST['requirement']) ? $_POST['requirement'] : array();
    $EmpID = $_POST['Emp_ID'];
    $newStatus = $_POST['Status'];

    // Determine if all requirements are checked
    $allChecked = count($checkedRequirements) === 10; // Replace 8 with the actual number of requirements

    // Determine new status based on all requirements being checked
    if ($allChecked) {
        $newStatus = 'Pending for Release of Guarantee Letter';
    } else {
        $newStatus = 'Pending for Requirements';
    }

    $query = "UPDATE hospitalbill h
        INNER JOIN beneficiary b ON b.Beneficiary_Id = h.Beneficiary_ID
        INNER JOIN transaction t ON t.Beneficiary_Id = h.Beneficiary_ID
        SET 
            t.Given_Sched = '$Date',
            t.Given_Time = '$transaction_time',
            t.Emp_ID='$EmpID',
            h.branch='$branch1',
            h.billamount = '$billamount',
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
            window.location.href = "hospital.php";
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
    
date_default_timezone_set('Asia/Manila');
$ReceivedDate = date('Y-m-d');
$ReceivedTime = date('H:i:s');
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

                $SQL = mysqli_query($con, "SELECT b.*, t.*, h.*
                                           FROM beneficiary b
                                           INNER JOIN transaction t ON b.Beneficiary_Id = t.Beneficiary_Id
                                           INNER JOIN hospitalbill h ON b.Beneficiary_Id = h.Beneficiary_ID
                                           WHERE b.Beneficiary_Id = '$beneID'");

                if ($result = mysqli_fetch_assoc($SQL)) {
                    $branch = $result['branch'];
                    $TransactionType = $result['TransactionType'];
                    $AssistanceType = $result['AssistanceType'];
                    $hospitals = $result['PartneredHospital'];
                    $escaped_hospitals = addslashes($hospitals);
                    $AssistanceType2 = $AssistanceType . '-' . $escaped_hospitals;
                    $ReceivedAssistance = "Guarantee Letter";
                    $Amount = $result['Amount'];
                    $EmpID = $_SESSION['EmpID']; // Assuming you have employee ID stored in session

                    $query = "INSERT INTO history (Beneficiary_ID, ReceivedDate, ReceivedTime, TransactionType, AssistanceType, ReceivedAssistance, Emp_ID, Amount, branch, proofGL)
                              VALUES ('$beneID', '$ReceivedDate', '$ReceivedTime', '$TransactionType', '$AssistanceType2', '$ReceivedAssistance', '$EmpID', '$Amount', '$branch', '$newImageName')";

                    if (mysqli_query($con, $query)) {
                        $sql1 = "DELETE FROM transaction WHERE Beneficiary_Id='$beneID'";
                        $sql2 = "DELETE FROM hospitalbill WHERE Beneficiary_ID='$beneID'";
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
                                                      window.location.href = "hospital.php";
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
                                                  window.location.href = "hospital.php";
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
}else{
    $beneID = $_POST['Beneficiary_Id'];

    $SQL = mysqli_query($con, "SELECT b.*, t.*, h.*
    FROM beneficiary b
    INNER JOIN transaction t ON b.Beneficiary_Id = t.Beneficiary_Id
    INNER JOIN hospitalbill h ON b.Beneficiary_Id = h.Beneficiary_ID
    WHERE b.Beneficiary_Id = '$beneID'");

    if($result = mysqli_fetch_assoc($SQL)){
        $branch = $result['branch'];
                    $TransactionType = $result['TransactionType'];
                    $AssistanceType = $result['AssistanceType'];
                    $hospitals = $result['PartneredHospital'];
                    $escaped_hospitals = addslashes($hospitals);
                    $AssistanceType2 = $AssistanceType . '-' . $escaped_hospitals;
                    $ReceivedAssistance = "Guarantee Letter";
                    $Amount = $result['Amount'];
                    $EmpID = $_POST['Emp_ID']; // Assuming you have employee ID stored in session
  // Insert into history table
  $query = "INSERT INTO history (Beneficiary_ID, ReceivedDate, ReceivedTime, TransactionType, AssistanceType, ReceivedAssistance, Emp_ID, Amount, branch)
  VALUES ('$beneID', '$ReceivedDate', '$ReceivedTime', '$TransactionType', '$AssistanceType2', '$ReceivedAssistance', '$EmpID', '$Amount', '$branch')";

        if(mysqli_query($con, $query)){
            // Send the email
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
                $mail->Subject = 'Released Guarantee Letter';
                $mail->Body = "
                    <html>
                    <body>
                    <p>Dear Mr./Ms./Mrs. $lastName,</p>
                    <p>We have successfully provided your Guarantee Letter. Please note that you may request another assistance after a period of 3 months.</p>
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
            $sql2 = "DELETE FROM hospitalbill WHERE Beneficiary_ID='$beneID'";
            $sql3 = "SELECT RemainingBal FROM budget WHERE AssistanceType='$AssistanceType' && branch='$branch'";

           
            $result3 = mysqli_query($con, $sql3);

          
            if ($result3) {
                if ($resultbal = mysqli_fetch_assoc($result3)) {
                    if ($resultbal['RemainingBal'] != 0) {
                        $updateQuery = "UPDATE budget SET RemainingBal = RemainingBal - $Amount WHERE branch = '$branch' AND AssistanceType = '$AssistanceType'";
                        $result4 = mysqli_query($con, $updateQuery);

                        if ($result4 && mysqli_query($con, $sql1) && mysqli_query($con, $sql2) ) {
                            echo '<body>
                                  <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
                                  <script>
                                  swal("This beneficiary already received his/her assistance","","success")
                                  .then((value) => {
                                      if (value) {
                                          window.location.href = "hospital.php";
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
                                      window.location.href = "hospital.php";
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
/*
date_default_timezone_set('Asia/Manila');
$ReceivedDate = date('Y-m-d');
$ReceivedTime = date('H:i:s');
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

                $SQL = mysqli_query($con, "SELECT b.*, t.*, h.*
                                           FROM beneficiary b
                                           INNER JOIN transaction t ON b.Beneficiary_Id = t.Beneficiary_Id
                                           INNER JOIN hospitalbill h ON b.Beneficiary_Id = h.Beneficiary_ID
                                           WHERE b.Beneficiary_Id = '$beneID'");

                if ($result = mysqli_fetch_assoc($SQL)) {
                    $branch = $result['branch'];
                    $TransactionType = $result['TransactionType'];
                    $AssistanceType = $result['AssistanceType'];
                    $hospitals = $result['PartneredHospital'];
                    $escaped_hospitals = addslashes($hospitals);
                    $AssistanceType2 = $AssistanceType . '-' . $escaped_hospitals;
                    $ReceivedAssistance = "Guarantee Letter";
                    $Amount = $result['Amount'];
                    $EmpID = $_SESSION['EmpID']; // Assuming you have employee ID stored in session

                    $query = "INSERT INTO history (Beneficiary_ID, ReceivedDate, ReceivedTime, TransactionType, AssistanceType, ReceivedAssistance, Emp_ID, Amount, branch, proofGL)
                              VALUES ('$beneID', '$ReceivedDate', '$ReceivedTime', '$TransactionType', '$AssistanceType2', '$ReceivedAssistance', '$EmpID', '$Amount', '$branch', '$newImageName')";

                    if (mysqli_query($con, $query)) {
                        $sql1 = "DELETE FROM transaction WHERE Beneficiary_Id='$beneID'";
                        $sql2 = "DELETE FROM hospitalbill WHERE Beneficiary_ID='$beneID'";
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
                                                      window.location.href = "hospital.php";
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
                                                  window.location.href = "hospital.php";
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
}
*/
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
               $query = "UPDATE hospitalbill h
        INNER JOIN beneficiary b ON b.Beneficiary_Id = h.Beneficiary_ID
        INNER JOIN transaction t ON t.Beneficiary_Id = h.Beneficiary_ID
        SET 
            t.Given_Sched = '$Date',
            t.Given_Time = '$transaction_time',
            t.Emp_ID='$EmpID',
              h.branch='$branch1',
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
        elseif ($Status == "Pending for Release of Guarantee Letter") {
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
                  $query = "UPDATE hospitalbill h
        INNER JOIN beneficiary b ON b.Beneficiary_Id = h.Beneficiary_ID
        INNER JOIN transaction t ON t.Beneficiary_Id = h.Beneficiary_ID
        SET 
            t.Given_Sched = '$Date',
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
                if($status == 'Pending for Release of Guarantee Letter') {
                    $employeeName = $_POST['EmpName'];
                    $mail->Subject = 'Pending for Release of Guarantee Letter';
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
        
        elseif ($Status == "Releasing Of Guarantee Letter") {
            date_default_timezone_set('Asia/Manila');
            $Date = date('Y-m-d'); // Set the current date for Given_Sched
            $transaction_time = date('H:i:s'); // Set the current date and time for transaction_time
            $amount = $_POST['amount'];
            $branch = $_POST['branch'];
            $PayoutType = "Guarantee Letter";
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
                 $query = "UPDATE hospitalbill h
        INNER JOIN beneficiary b ON b.Beneficiary_Id = h.Beneficiary_ID
        INNER JOIN transaction t ON t.Beneficiary_Id = h.Beneficiary_ID
        SET t.Given_Sched = '$Date', t.Given_Time = '$transaction_time', t.Status = '$Status', t.Emp_ID='$EmpID',h.Amount='$amount',h.receivedassistance='$PayoutType', h.branch='$branch'
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
            $branch= $_POST['branch'];
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
                if($status == 'Releasing Of Guarantee Letter') {
                    $employeeName = $_POST['EmpName'];
                    $mail->Subject = 'Releasing Of Guarantee Letter';
                    $amount = $_POST['amount'];
                    $mail->Body = "
                        <html>
                        <body>
                        <p>Dear Mr./Ms./Mrs. $lastName,</p>
                        <p>Your request for hospital bills assistance has been approved. You may go on $Date at  $transaction_time_12hr.
                         You will receive a guarantee letter with the approved amount of $amount.<br>
                        Kindly proceed to $branch to claim your guarantee letter.<br>
                        Please bring a valid ID and show this email upon arrival.<br>
                        Thank you for your patience and cooperation.</p>
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
        
        elseif ($Status == "Decline Request for Re-schedule") {
             $reason= $_POST['reason'];
                 $query = "UPDATE hospitalbill h
        INNER JOIN beneficiary b ON b.Beneficiary_Id = h.Beneficiary_ID
        INNER JOIN transaction t ON t.Beneficiary_Id = h.Beneficiary_ID
        SET 
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
                
                
            
                 $query = "UPDATE hospitalbill h
        INNER JOIN beneficiary b ON b.Beneficiary_Id = h.Beneficiary_ID
        INNER JOIN transaction t ON t.Beneficiary_Id = h.Beneficiary_ID
        SET 
            t.Given_Sched = '$Date',
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
         }
        }
    }
}

      
elseif ($Status == "Pending for Requirements"  ) {
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
            window.location.href = "hospital.php";
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
    <link rel="stylesheet" href="editformhospitals.css" />
  </head>

  <body>
    
    <div class="container">
      <div class="title" style="margin-bottom:10px; margin-left:220px;"> Edit form </div>
      <form id="editForm"  method="post"> <!-- Changed method to POST -->
      <input type="hidden" name="Beneficiary_Id" value="<?php echo $beneID; ?>">
      <input type="hidden" name="Emp_ID" value="<?php echo $EmpID; ?>">

      <input type="hidden" name="role" id="role" value="<?php echo $role; ?>">
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
                    <span class="details">Status</span>
    <?php
    
    $status = array('For Schedule','For Validation','Pending for Requirements','Pending for Release of Guarantee Letter' ,'Releasing Of Guarantee Letter','Request for Re-schedule','For Re-schedule', 'Decline Request for Re-schedule','Release Guarantee Letter');

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
    elseif ($record['Status'] == 'Pending for Release of Guarantee Letter') {
        // If the current status is "Pending for Requirements", display only "For Validation" in the dropdown
        echo "<input type='text' id='status' name='Status' value='Releasing Of Guarantee Letter' readonly>";

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
    
    elseif ($record['Status'] == 'Releasing Of Guarantee Letter') {
        if ($role === 'Admin'){
    
            // If the current status is "Pending for Requirements", display only "For Validation" in the dropdown
            echo "<select id='status' name='Status' onchange='handleStatusChange()'>";
            echo "<option value='Releasing Of Guarantee Letter'>Releasing Of Guarantee Letter</option>";
            echo "<option value='Release Guarantee Letter'>Release Guarantee Letter</option>";
            echo "</select>";
         
       }else{
        echo "<input type='text' id='status' name='Status' value='Releasing Guarantee Letter' readonly>";
       
       }
    }
    /*
    elseif ($record['Status'] == 'Release Guarantee Letter') {
        // If the current status is "Pending for Requirements", display only "For Validation" in the dropdown
       if ($role === 'Admin'){
    
            // If the current status is "Pending for Requirements", display only "For Validation" in the dropdown
            echo "<select id='status' name='Status' onchange='handleStatusChange()'>";
            echo "<option value='Releasing Of Guarantee Letter'>Releasing Of Guarantee Letter</option>";
            echo "<option value='Release Guarantee Letter'>Release Guarantee Letter</option>";
            echo "</select>";
                }else{
        echo "<input type='text' id='status' name='Status' value='Release Guarantee Letter' readonly>";
        echo "<input type='file' class='image' name='image' id='img' accept='.jpg, .jpeg, .png' >";
       echo" <p style='color: rgb(150, 26, 26); font-size: 18px;'><?php echo $profileError ?></p>";
       
                }
    }
                */
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
                <input type="submit" value="Submit" id="submitbtn" name="submit" onclick="showConfirmation()" />
                <input type="button" value="Cancel" name="cancel" onclick="cancelEdit()" />
              <input type="button" id="download-pdf" value="PDF" name="download-pdf" style="background:green;" onclick="PDF()">
              <!--<input type="file" name="uploadedFile" accept="image/*" style="margin-top:10px;">
-->
            </div>
            
        </form>
    </div>
    
  <script>
     function PDF() { 
        var sponsor = document.getElementById('sponsor').value;
    window.location.href = "download_pdf.php?sponsor=" + encodeURIComponent(sponsor);
}
    
          function cancelEdit() {
           window.location.href = "hospital.php";
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
   
  document.addEventListener('DOMContentLoaded', function() {
            handleStatusChange();
        });

  document.getElementById('calendar').min = new Date().toISOString().split("T")[0];

 /* function updateButtonText() {
    var status = document.getElementById("status").value;
    var submitBtn = document.getElementById("submitbtn");
    
   if (status == 'Release Guarantee Letter') {
        submitBtn.value = 'Release';
    }else{
        submitBtn.value = 'Submit';
 
    }
}*/

      function handleStatusChange() {
    
    var status = document.getElementById('status').value;
    var emailFormat = document.getElementById('emailFormat');
    var requirements = document.getElementById('requirements');
    var submitbtn = document.getElementById('submitbtn');
    var pdf = document.getElementById('download-pdf');
    var role = document.getElementById('role').value;
    
    var beneID = document.querySelector('input[name="Beneficiary_Id"]').value;
var empID = document.querySelector('input[name="Emp_ID"]').value;

    emailFormat.innerHTML = '';
    requirements.style.display = 'none'; 
   
    if (status === 'For Schedule') {
        pdf.style.display = 'none'; 
        submitbtn.style.display = 'inline';
        emailFormat.innerHTML = `
           <div style = "color: black; padding:10px; background:white; margin-top:20px;"> 
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
        pdf.style.display = 'none'; 
        submitbtn.style.display = 'inline';
        requirements.style.display = 'block';
        requirements.innerHTML = `
            <div style="color: black; padding:10px; background:white; margin-top:10px;margin-bottom:-5px;">
                <div class="input-box">
                    <span class="details" style="color: blue;">Hospital Bill Amount</span>
                    <input type="text" style="padding:10px; height:30px;" required value="<?php echo $record['billamount']; ?>" name="billamount" />
                </div>
                <h3 style="color: blue;">REQUIREMENTS FOR HOSPITAL BILL ASSISTANCE VALIDATION</h3>
                <ul style="text-align: left; margin-left:40px;">
                    <input type="checkbox" name="requirement[]" value="Final Bill w/ Discharge Date (May pirma ng Billing Clerk)/Promissory Note"> Final Bill w/ Discharge Date (May pirma ng Billing Clerk)/Promissory Note <br>
                    <input type="checkbox" name="requirement[]" value="Medical Abstract/Medical Certificate (May Pangalan Pirma at License # ng Doctor)"> Medical Abstract/Medical Certificate (May Pangalan Pirma at License # ng Doctor) <br>
                    <input type="checkbox" name="requirement[]" value="Sulat (Sulat Kamay) na Humihingi ng tulong kay Gov. Joet S. Garcia"> Sulat (Sulat Kamay) na Humihingi ng tulong kay Gov. Joet S. Garcia <br>
                    <input type="checkbox" name="requirement[]" value="Xerox Valid ID ng Pasyente"> Xerox Valid ID ng Pasyente <br>
                    <input type="checkbox" name="requirement[]" value="Xerox Valid ID ng Maglalakad"> Xerox Valid ID ng Maglalakad <br>
                    <input type="checkbox" name="requirement[]" value="BRGY. INDIGENCY (PASYENTE)"> BRGY. INDIGENCY (PASYENTE) <br>
                    <input type="checkbox" name="requirement[]" value="SOCIAL CASE STUDY (MSWDO)"> SOCIAL CASE STUDY (MSWDO) <br>
                </ul>
                <h3 style="color: blue;">SUPPORTING DOCUMENTS</h3>
                <ul style="text-align: left; margin-left:40px;">
                    <input type="checkbox" name="requirement[]" value="XEROX COPY NG BIRTH CERTIFICATE (KUNG ANAK O MAGULANG ANG PASYENTE)"> XEROX COPY NG BIRTH CERTIFICATE (KUNG ANAK O MAGULANG ANG PASYENTE) <br>
                    <input type="checkbox" name="requirement[]" value="XEROX NG MARRIAGE (CERTIFICATE KUNG ASAWA ANG PASYENTE)"> XEROX NG MARRIAGE (CERTIFICATE KUNG ASAWA ANG PASYENTE) <br>
                    <input type="checkbox" name="requirement[]" value="BIRTH CERTIFICATE AND MARRIAGE CERTIFICATE (NG MAGULANG) KUNG KAPATID ANG PASYENTE"> BIRTH CERTIFICATE AND MARRIAGE CERTIFICATE (NG MAGULANG) KUNG KAPATID ANG PASYENTE <br>
                </ul>
                <input type="hidden" name="EmpName" style="margin-top:15px;" value="<?php echo isset($res_Fname) ? $res_Fname . ' ' . $res_Lname : ''; ?>" placeholder="Enter employee name" required><br><br>
            </div>
        `;
    

    
    } else if (status === 'Pending for Release of Guarantee Letter') {
        pdf.style.display = 'none'; 
        submitbtn.style.display = 'inline';
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
            submitbtn.style.display = 'none'; // Hide the submit button
            pdf.style.display = 'none'; 
           
    }


      

  
 else if (status === 'Releasing Of Guarantee Letter' && role==='Admin') { 
        pdf.style.display = 'none'; 
        submitbtn.style.display = 'inline'; 
            emailFormat.innerHTML = `
                <div style="color: black; padding:15px; background:white; margin-top:20px;">
                    Dear Mr./Ms./Mrs. <?php echo $record['Lastname']; ?>,<br><br>
                    <p>Your request for hospital bills assistance has been approved. You may go on <input type="date" id="calendar" name="Given_Sched" min="<?php echo date('Y-m-d'); ?>" value="<?php echo $record['Given_Sched']; ?>" /> 
                    at <input type="time" id="time" name="time" value="<?php echo date("H:i", strtotime($record['time'])); ?>" />.<br>
                    You will receive a guarantee letter with the approved amount of <input type="text" autocomplete="off" name="amount" style="margin-top:10px;" placeholder="Enter amount" value="<?php echo $record['Amount']; ?>">.<br><br>
                    Kindly proceed to <select name="branch" id="branch" style="margin-top:5px;">
    <option value="PGB-Balanga Branch" <?php if ($record['branch'] == "PGB-Balanga Branch") echo 'selected="selected"'; ?>>PGB-Balanga Branch</option>
    <option value="PGB-Dinalupihan Branch" <?php if ($record['branch'] == "PGB-Dinalupihan Branch") echo 'selected="selected"'; ?>>PGB-Dinalupihan Branch</option>
    <option value="PGB-Hermosa Branch" <?php if ($record['branch'] == "PGB-Hermosa Branch") echo 'selected="selected"'; ?>>PGB-Hermosa Branch</option>
    <option value="PGB-Mariveles Branch" <?php if ($record['branch'] == "PGB-Mariveles Branch") echo 'selected="selected"'; ?>>PGB-Mariveles Branch</option>
</select>to claim your guarantee letter. <br>

                    Please bring a valid ID and show this email upon arrival.<br><br>
                    Thank you for your patience and cooperation.<br><br>    
                    Best regards,<br>
                    <input type="text" name="EmpName" style="margin-top:15px;" value="<?php echo isset($res_Fname) ? $res_Fname . ' ' . $res_Lname : ''; ?>" placeholder="Enter employee name" required><br><br>
                    Provincial Government of Bataan - Special Assistance Program
                </p>
                </div>
            `;
          

    }

 
    else if (status === 'For Re-schedule') { 
        pdf.style.display = 'none'; 
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
    pdf.style.display = 'none'; 
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

    else if (status === 'Release Guarantee Letter') { 
   
        emailFormat.innerHTML = `
                <div style = "color: black; padding:15px; background:white; margin-top:20px;">
                Dear Mr./Ms./Mrs. <?php echo $record['Lastname']; ?>,<br><br>
                <p>We have successfully provided your Guarantee Letter. Please note that you may request another assistance after a period of 3 months. <br>
                Thank you for your cooperation. God Bless!<br> 
                
                Best regards,<br>
                <input type="text" name="EmpName" style="margin-top:15px;" value="<?php echo isset($res_Fname) ? $res_Fname . ' ' . $res_Lname : ''; ?>" placeholder="Enter employee name" required><br><br>
                Provincial Government of Bataan - Special Assistance Program</p>
            </div> 
                `;  
                submitbtn.style.display = 'inline';
                pdf.style.display = 'none';     }

}

  
      
     
    
  </script>
  </body>
</html>
