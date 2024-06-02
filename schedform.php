<?php 
session_start();
include("php/config.php");

// Check if Beneficiary_Id is set in the POST parameter
if(isset($_SESSION['Beneficiary_Id'])) {
    $beneID = $_SESSION['Beneficiary_Id'];
  
} else {
    echo "User ID is not set.";
    exit; // Exit if ID is not set
}
if(isset($_SESSION['Status'])) {
    $Status = $_SESSION['Status'];
  
} else {
    echo "User Ibgds not set.";
    exit; // Exit if ID is not set
}


    $query = mysqli_query($con, "SELECT * FROM beneficiary WHERE Beneficiary_Id=$beneID");

    // Check if any rows are returned
    if($result = mysqli_fetch_assoc($query)){
        $beneID = $result['Beneficiary_Id'];

        $res_Lname1 = $result['Lastname'];
        $res_Fname1 = $result['Firstname'];
        $Email = $result['Email']; // Assuming you have an email field in the users table
  
    }
if(isset($_SESSION['Emp_ID'])){
    $id = $_SESSION['Emp_ID'];
    $query = mysqli_query($con, "SELECT * FROM employees WHERE Emp_ID=$id");

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

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['sendmail'])) {
    $lastName = $_POST['Lastname'];
    $Date = $_POST['sched'];
    $employeeName = $_POST['EmpName'];
    $mail = new PHPMailer(true);
    $Status = $_POST['Status']; 
    $beneID=$_POST['Beneficiary_Id']; 

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
        if($Status == 'For Schedule') {
        $mail->Subject = 'Schedule for requirements checking';
        $mail->Body = "
        <html>
        <body>
        <p>Dear Mr./Ms./Mrs. $lastName,</p>
        <p>I am writing to inform you that your request for scheduling has been approved.</p>
        <p>Your schedule has been set for $Date. We kindly expect your presence on the said date.</p>
        <p>Thank you for your cooperation.</p>
        <p>Best regards,</p>
        <p>$employeeName</p>
        <p>Provincial Government of Bataan - Special Assistance Program</p>
        </body>
        </html>
        ";

        }elseif($Status == 'Pending for Requirements') {
            // Set the email body for pending requirements
            $mail->Subject = 'Pending for Requirements';
            // Modify the message as needed
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
        }  elseif($Status == 'Pending for Payout') {
            // Set the email body for pending payout
            $mail->Body = "
                <html>
                <body>
                <p>Dear Mr./Ms./Mrs. $lastName,</p>
                <p>     Your assistance request is currently pending for payout.<br>  </p>
                <p> We are processing your application, and you will receive your financial assistance soon.</p>
                <p>Thank you for your patience and cooperation.</p>
                <p>Best regards,</p>
                <p>$employeeName</p>
                <p>Provincial Government of Bataan - Special Assistance Program</p>
                </body>
                </html>
            ";
        } else {
            // Default message if Status doesn't match any condition
            $mail->Body = "$defaultmsg";
        }

        $mail->send();
        $_SESSION['Beneficiary_Id'] = $_POST['Beneficiary_Id'];
        echo '<body>
                        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
                        <script>
                        swal("Email has been sent to the beneficiary","","success")
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <link rel="stylesheet" href="schedform.css" />
</head>
<body>
    <section class="container">
        <header>Scheduling Form</header>
        <form action="#" class="form" method="POST">
        <input type="hidden" value="<?php echo isset($Status) ? $Status : ''; ?>" name="Status" placeholder="Enter last name" required />
        <input type="hidden" name="Beneficiary_Id" value="<?php echo $beneID; ?>" required>
            <p class="emailformat">
    Dear Mr./Ms./Mrs. <input type="text" value="<?php echo isset($res_Lname1) ? $res_Lname1 : ''; ?>" name="Lastname" placeholder="Enter last name" required />.<br><br>
    <?php if($Status == 'For Schedule'): ?>
        I am writing to inform you that your request for rescheduling has been approved.<br>
        Your new schedule has been set for <input type="date" name="sched" required />. We kindly expect your presence on the said date.<br><br>
    <?php elseif($Status == 'Pending for Requirements'): ?>
        Your assistance request is currently pending for requirements.<br>
        Please submit the necessary documents on <input type="date" name="sched" required /> to proceed with your request.<br><br>
    <?php elseif($Status == 'Pending for Payout'): ?>
        Your assistance request is currently pending for payout.<br>
        We are processing your application, and you will receive your financial assistance soon.<br><br>

    <?php else: ?>
        <!-- Default message if Status doesn't match any condition -->
         <textarea id="multilineInput" name="multilineInput" rows="10" cols="50"></textarea><br><br>
       
        <?php endif; ?>
    Thank you for your cooperation.<br><br>
    Best regards,<br>
    <input type="text" name="EmpName" value="<?php echo isset($res_Fname) ? $res_Fname . ' ' . $res_Lname : ''; ?>" placeholder="Enter employee name" required /><br><br>
    Provincial Government of Bataan - Special Assistance Program
</p>

            <div class="column">
                <button name="sendmail">Send Email to beneficiary</button>
            </div>
        </form>
    </section>
</body>
</html>
