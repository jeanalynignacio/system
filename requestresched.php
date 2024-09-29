<?php
session_start();
include("php/config.php");

if (!isset($_SESSION['valid'])) {
    header("Location: index.php");
    exit;
}

$id = $_SESSION['id'];
$query = mysqli_query($con, "SELECT * FROM users WHERE Id='$id'");
$user = mysqli_fetch_assoc($query);

$SQL = "SELECT * FROM beneficiary WHERE Representative_ID='$id'";
$result = mysqli_query($con, $SQL);
$res_ID = mysqli_fetch_assoc($result);
$lastError = "";

// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['beneficiaryId'])) {
    $beneID = mysqli_real_escape_string($con, $_POST['beneficiaryId']);
    $reason = mysqli_real_escape_string($con, $_POST['reason']);
    
    // Update transaction status
    $query = "UPDATE transaction SET Status = 'Request for Re-schedule' WHERE Beneficiary_Id = '$beneID'";

    if (mysqli_query($con, $query)) {
      echo json_encode(['success' => true]);
      exit; // End the script after successful AJAX response
  } else {
      $lastError = "Failed to update status: " . mysqli_error($con);
      echo json_encode(['success' => false, 'error' => $lastError]);
      exit; // End the script after error response
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <link rel="stylesheet" href="requestedresched.css" />
    <style>
        a.button-link {
            display: inline-block;
            padding: 10px 20px;
            font-size: 16px;
            color: white;
            background-color: #007bff; /* Button background color */
            border: none;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
        }

        a.button-link:hover {
            background-color: #0056b3; /* Darker shade on hover */
        }

        a.button-link:active {
            background-color: #004080; /* Even darker on click */
        }
    </style>
</head>
<body>
    <section class="container">
        <header>Request Re-schedule</header>
        <form action="#" class="form">
            <div class="column">
                <div class="input-box">
                    <label>Last Name</label>
                    <input name="Lastname" disabled type="text" required value="<?php echo htmlspecialchars($res_ID['Lastname']); ?>" style="color: black; background: white; border-color: gray" />
                </div>
                <div class="input-box">
                    <label>First Name</label>
                    <input name="Firstname" disabled type="text" required value="<?php echo htmlspecialchars($res_ID['Firstname']); ?>" style="color: black; background: white; border-color: gray" />
                    <input name="Lastname" type="hidden" id="lname" value="<?php echo htmlspecialchars($res_ID['Lastname']); ?>" />
                    <input name="Firstname" type="hidden" id="fname" value="<?php echo htmlspecialchars($res_ID['Firstname']); ?>" />
                    <input name="email" type="hidden" value="<?php echo htmlspecialchars($res_ID['Email']); ?>" />
                    <input name="ID" type="hidden" id="beneficiaryId" value="<?php echo htmlspecialchars($res_ID['Beneficiary_Id']); ?>" />
                </div>
            </div>

            <div class="input-box">
                <label>Reason for Re-schedule</label>
                <textarea name="reason" id="reason" required></textarea>
                <p style="color: rgb(150, 26, 26); font-size: 18px;"><?php echo htmlspecialchars($lastError); ?></p>
            </div>

            <div class="column">
                <a href="#" class="button-link" onclick="openGmailAndUpdate(); return false;">Send Request for Re-schedule</a>
            </div>
        </form>
    </section>
  <script>
    function openGmailAndUpdate() {
        const firstName = document.getElementById('fname').value;
        const lastName = document.getElementById('lname').value;
        const beneficiaryId = document.getElementById('beneficiaryId').value;
        const reason = document.getElementById('reason').value;

        // Validate reason before proceeding
        if (!reason) {
            alert('Please provide a reason to proceed.');
            return;
        }

        // Create email body with salutation
        const email = 'bataanpgbsap@gmail.com';
        const subject = 'Request for Re-Schedule';
        const body = `Good day, My name is ${firstName} ${lastName}, and I would like to kindly request a reschedule for my appointment.\n\nReason for rescheduling: ${reason}\n\n`;

        // Open Gmail link in a new tab
        const gmailLink = `https://mail.google.com/mail/?view=cm&fs=1&to=${email}&su=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`;
        window.open(gmailLink, '_blank');

        // Send AJAX request to update transaction status
        const xhr = new XMLHttpRequest();
        xhr.open('POST', '', true); // Send to the same file
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        xhr.onload = function () {
            const response = JSON.parse(xhr.responseText);
            if (response.success) {
                // Redirect to users homepage after successful update
                window.location.href = 'usershomepage.php';
            } else {
                // Handle errors here
                console.error('Error updating status:', response.error);
                alert('Failed to update status: ' + response.error);
            }
        };

        xhr.send(`beneficiaryId=${encodeURIComponent(beneficiaryId)}&reason=${encodeURIComponent(reason)}`);
    }
</script>

</body>
</html>
