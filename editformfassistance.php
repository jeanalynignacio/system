<?php 
session_start();
include("php/config.php");

use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;


// Check if Beneficiary_Id is set in the URL parameter
if (isset($_POST['Beneficiary_Id'])) {
    // Retrieve the Beneficiary_Id from the URL parameter
    $beneID = $_POST['Beneficiary_Id'];
    $Status = $_POST['Status'];
} else {
    echo "User ID is not set.";
    exit();
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
$SQL = "SELECT b.*, t.*, f.*
        FROM beneficiary b
        INNER JOIN transaction t ON b.Beneficiary_Id = t.Beneficiary_Id
        INNER JOIN financialassistance f ON b.Beneficiary_Id = f.Beneficiary_ID
        WHERE b.Beneficiary_Id = '$beneID'";

$result = mysqli_query($con, $SQL);
$res_data = mysqli_fetch_assoc($result); // Fetch a single record

if (!$res_data) {
    echo "No records found.";
    exit();
}

if (isset($_POST['submit'])) {
    // Check if the user confirmed the update
    if (isset($_POST['confirmed']) && $_POST['confirmed'] === "yes") {
        $beneId = $_POST['Beneficiary_Id'];
        
        $Amount = $_POST['Amount'];
        $Status = $_POST['Status'];

        // Construct the update query
       
       
       
       
       $query = "UPDATE financialassistance f
                  INNER JOIN beneficiary b ON b.Beneficiary_Id = f.Beneficiary_ID
                  INNER JOIN transaction t ON t.Beneficiary_Id = f.Beneficiary_ID
                  SET t.Date = '$Date',
                      t.transaction_time = '$transaction_time',
                      t.Given_Sched = '$Given_Sched',
                      t.TransactionType = '$TransactionType',
                      t.Status = '$Status',
                      f.Amount = '$Amount'
                  WHERE b.Beneficiary_Id = '$beneId'";

        $result2 = mysqli_query($con, $query);

        if ($result2) {
            
            header("Location: assistance.php");
            exit();
        } else {
            echo "Error updating records: " . mysqli_error($con);
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
</head>
<body>
    <div class="container">
        <div class="title"> Edit form </div>
        <form id="editForm" method="post">
            <input type="hidden" name="Beneficiary_Id" value="<?php echo $beneID; ?>">
            <div class="user-details">
                <div class="input-box">
                    <span class="details"> Date </span>
                    <input type="date" id="calendar" name="Date" required value="<?php echo $res_data['Date']; ?>"/>
                </div>

                <div class="input-box">
                    <span class="details">Time</span>
                    <input type="text" id="time" required value="<?php echo $res_data['transaction_time']; ?>" name="transaction_time">
                </div>

                <div class="input-box">
                    <span class="details"> Beneficiary ID </span>
                    <input type="text" required value="<?php echo $res_data['Beneficiary_Id']; ?>" name="Beneficiary_Id" disabled/>
                </div>

                <div class="input-box">
                    <span class="details"> Last Name </span>
                    <input type="text" required value="<?php echo $res_data['Lastname']; ?>" name="Lastname" disabled/>
                </div>

                <div class="input-box">
                    <span class="details"> First Name </span>
                    <input type="text" required value="<?php echo $res_data['Firstname']; ?>" name="Firstname" disabled/>
                </div>

             

                <div class="input-box">
                    <span class="details"> Transaction Type </span>
                    <select name="TransactionType">
                        <option <?php echo ($res_data['TransactionType'] == 'Online Appointment') ? 'selected' : ''; ?>>Online Appointment</option>
                        <option <?php echo ($res_data['TransactionType'] == 'Walk-in') ? 'selected' : ''; ?>>Walk-in</option>
                    </select>
                </div>

                <div class="input-box">
                    <span class="details"> Amount Received </span>
                    <input type="text" value="<?php echo $res_data['Amount']; ?>" name="Amount" />
                </div>

                <div class="input-box">
                    <span class="details">Status </span>
                    <select name="Status">
                        <?php
                        $statuses = array(
                            'Pending for Payout',
                            'Pending for Requirements',
                            'For Schedule',
                            'Done'
                        );

                        foreach ($statuses as $status) {
                            $selected = ($res_data['Status'] == $status) ? 'selected' : '';
                            echo "<option $selected>$status</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="input-box">
                    <span class="details">Given Schedule </span>
                    <input type="date" id="calendar" name="Given_Sched" value="<?php echo $res_data['Given_Sched']; ?>"/>
                </div>
            </div>
            <div class="input-box">
                    <span class="details">Given Time </span>
                    <input type="date" id="calendar" name="Given_Sched" value="<?php echo $res_data['Given_Sched']; ?>"/>
                </div>
            </div>
            
            
            <br>
            <input type="hidden" name="confirmed" id="confirmed" value="no">
            <br>

            <div class="button-row">
                <input type="submit" value="Done Edit" name="submit" onclick="showConfirmation()" />
                <input type="button" value="Cancel" name="cancel" onclick="cancelEdit()" />
            </div>
        </form>
    </div>

    <script type="text/javascript">
        function cancelEdit() {
            // Redirect to the previous page
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
    </script>
</body>
</html>
