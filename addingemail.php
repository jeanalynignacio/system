<?php 
session_start();

include("php/config.php");
if(!isset($_SESSION['Emp_ID'])){
    header("Location: login.php");
    exit();
}

if(isset($_SESSION['Emp_ID'])){
    $id = $_SESSION['Emp_ID'];
    $query = mysqli_query($con, "SELECT * FROM employees WHERE Emp_ID=$id");

    if($result = mysqli_fetch_assoc($query)){
        $res_Id = $result['Emp_ID']; // Assign Emp_ID to $res_Id
      //  echo "Employee ID: $res_Id<br>"; // For debugging, to ensure Emp_ID is fetched
    } else {
        echo "No employee found with this ID.<br>"; // Debugging message
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Email</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="emp_registration.css">
</head>

<body>
    <div class="container">
        <div class="box form-box">
        <?php 
        // Define variables to store selected city and barangay
        use PHPMailer\PHPMailer\PHPMailer;
        use PHPMailer\PHPMailer\SMTP;
        use PHPMailer\PHPMailer\Exception;

        include ("php/config.php");
        $errors = []; // Initialize $errors as an empty array
        $lastError = "";
        $Errors = "";

        if(isset($_POST['submit'])){
            // receive all input values from the form
            $Email = $_POST['Email'];
            $check_user = "SELECT * FROM employees WHERE Email='$Email' LIMIT 1";
            $result = mysqli_query($con, $check_user);
            $user = mysqli_fetch_assoc($result);



            if(empty($Email)){
                array_push($errors, $lastError = "Email is required");
            } else if ($user) { // If user exists
                array_push($errors, $lastError ="This email address is already in use.");
            } elseif (!filter_var($Email, FILTER_VALIDATE_EMAIL)) {
                array_push($errors, $lastError = "Invalid email format");
            }

            if (empty($errors)) {
                // Use the complete user ID in the INSERT query
                $otp_str = str_shuffle("0123456789");
                $verification_code = substr($otp_str, 0, 6);
              //  echo "Verification Code: $verification_code<br>"; // For debugging, to ensure verification code is generated

                $query1 = "UPDATE employees SET Email ='$Email', verification_code='$verification_code', status='0' WHERE Emp_ID='$res_Id'";
                
                if(mysqli_query($con, $query1)){
                    // Send verification email
                    require 'PHPMailer/src/Exception.php';
                    require 'PHPMailer/src/PHPMailer.php';
                    require 'PHPMailer/src/SMTP.php';
                    $mail = new PHPMailer(true);

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
                    $mail->Subject = 'Email Verification';
                    $mail->Body = "Good Day! This is your verification code: $verification_code 
                    <p> If you did not request for this code. Please ignore this email.</p>";
                 
    
                    if($mail->send()){
                        echo '<body>
                        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
                        <script>
                        swal("Email added successfully! Please check your email to verify")
                        .then((value) => {
                            if (value) {
                                window.location.href = "verifyEmpEmail.php";
                            }
                        });
                        </script>
                        </body>';
                        exit();


                    }
                } else {
                    array_push($errors, mysqli_error($con));
                }
            } else {
                // If the query fails, push the error message into the $errors array
                array_push($errors, mysqli_error($con));
            }
        }
        ?>
           
            <header>Add Email</header>
            <form action="" method="post">
                <div class="field input">
                    <label for="Email" style="font-size: 18px;">Email</label>
                    <input type="text" name="Email" id="Email" autocomplete="off" value="<?php echo $_POST['Email'] ?? ''; ?>">
                    <p style="color: rgb(150, 26, 26); font-size: 18px;"><?php echo $lastError ?></p>          
                </div>
                <div class="field">
                    <input type="submit" class="btn" name="submit" value="Add Email">
                </div>
            </form>
            <center>
                    <img src="images/back.png" style="vertical-align: middle; height: 15px;width:20px;margin-right:6px; "/><a href="index.php" style="color: #414344; text-decoration: none;margin-right:10px;">Back to Home</a></center>

        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
