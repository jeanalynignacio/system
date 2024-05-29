<?php 
    session_start();

    include("php/config.php");
    if(!isset($_SESSION['valid'])){
        header("Location: index.php");

    }
    if(isset($_POST['Back'])){
        header("Location: usershomepage.php");
       exit;
   }
   
  /*if (isset($_POST['myself'])) {
    
    $userId = $_SESSION['id'];
    $threeMonthsAgo = date('Y-m-d', strtotime('-3 months'));

    // Query to find the beneficiary
    $query = "SELECT * FROM beneficiary WHERE Representative_ID = $userId";
    $result = mysqli_query($con, $query);


    if (mysqli_num_rows($result) > 0) {
        // Beneficiary found, get beneficiary ID
        $row = mysqli_fetch_assoc($result);
        $beneficiaryId = $row['Beneficiary_Id'];

        // Check for transactions with status 'Done' in the last 3 months
        $query = "SELECT * FROM transaction WHERE Beneficiary_Id = $beneficiaryId AND Status = 'Done' AND Given_Sched >= '$threeMonthsAgo'";
        $result = mysqli_query($con, $query);

        if (mysqli_num_rows($result) > 0) {
            echo "<script>alert('You cannot apply at this time.');</script>";
        } else {
            // Check for transactions with status 'For Schedule'
            $query = "SELECT * FROM transaction WHERE Beneficiary_Id = $beneficiaryId AND Status = 'For Schedule'";
            $result = mysqli_query($con, $query);

            if (mysqli_num_rows($result) > 0) {
                echo "<script>alert('You already requested a schedule. Please wait for the email to know when your schedule of appearance to the office is. Thank you');</script>";
            } else {
                // No conflicting transactions found, proceed with the application process
                header("Location: applysched.php");
                exit();
            }
        }
    } 
}*/
if (isset($_POST['myself'])) {
    $userId = $_SESSION['id'];
    $threeMonthsAgo = date('Y-m-d', strtotime('-3 months'));

    // Query to find the beneficiary
    $query2 = "SELECT * FROM beneficiary WHERE Representative_ID = $userId";
    $result2 = mysqli_query($con, $query2);

    $query1 = "SELECT * FROM users WHERE Id = $userId";
    $result1 = mysqli_query($con, $query1);

    if ($result1 && mysqli_num_rows($result1) > 0) {
        // User found, get user details
        $row = mysqli_fetch_assoc($result1);
        $userLastname = $row['Lastname'];
        $userFirstname = $row['Firstname'];

        if ($result2 && mysqli_num_rows($result2) > 0) {
            while ($beneficiary = mysqli_fetch_assoc($result2)) {
                $beneficiaryLastname = $beneficiary['Lastname'];
                $beneficiaryFirstname = $beneficiary['Firstname'];
                $beneficiaryId = $beneficiary['Beneficiary_Id'];

                if ($userLastname === $beneficiaryLastname && $userFirstname === $beneficiaryFirstname) {
                    // Check for transactions with status 'Done' in the last 3 months
                    $query3 = "SELECT * FROM transaction WHERE Beneficiary_Id = $beneficiaryId AND Status = 'Done' AND Given_Sched >= '$threeMonthsAgo'";
                    $result3 = mysqli_query($con, $query3);

                    if (mysqli_num_rows($result3) > 0) {
                        echo '<body>
                        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
                        <script>
                        swal("Notification", "You cannot apply at this time.")
                        .then((value) => {
                            if (value) {
                                window.location.href = "usershomepage.php";
                            }
                        });
                        </script>
                        </body>';
                        exit();
                    }

                    // Check for transactions with status 'For Schedule'
                    $query4 = "SELECT * FROM transaction WHERE Beneficiary_Id = $beneficiaryId AND Status = 'For Schedule' ";
                    $result4 = mysqli_query($con, $query4);

                    if (mysqli_num_rows($result4) > 0) {
                        echo '<body>
                        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
                        <script>
                        swal("Notification", "You already requested a schedule. Please wait for the email to know when your schedule of appearance to the office is. Thank you.")
                        .then((value) => {
                            if (value) {
                                window.location.href = "usershomepage.php";
                            }
                        });
                        </script>
                        </body>';
                        exit();
                    }
                    $query4 = "SELECT * FROM transaction WHERE Beneficiary_Id = $beneficiaryId AND Status = 'Pending for Requirements' ";
                    $result4 = mysqli_query($con, $query4);

                    if  (mysqli_num_rows($result4) > 0) {
                        echo '<body>
                        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
                        <script>
                        swal("Notification", "You already pendingh for req. Please wait for the email to know when your schedule of appearance to the office is. Thank you.")
                        .then((value) => {
                            if (value) {
                                window.location.href = "usershomepage.php";
                            }
                        });
                        </script>
                        </body>';
                        exit();
                    }
                    $query4 = "SELECT * FROM transaction WHERE Beneficiary_Id = $beneficiaryId AND Status = 'For Payout' ";
                    $result4 = mysqli_query($con, $query4);

                    if  (mysqli_num_rows($result4) > 0) {
                        echo '<body>
                        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
                        <script>
                        swal("Notification", "You already pending for payout. Please wait for the email to know when your schedule of appearance to the office is. Thank you.")
                        .then((value) => {
                            if (value) {
                                window.location.href = "usershomepage.php";
                            }
                        });
                        </script>
                        </body>';
                        exit();
                    }
                    $query4 = "SELECT * FROM transaction WHERE Beneficiary_Id = $beneficiaryId AND Status = 'For Validation' ";
                    $result4 = mysqli_query($con, $query4);

                    if  (mysqli_num_rows($result4) > 0) {
                        echo '<body>
                        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
                        <script>
                        swal("Notification", "You already pending for validationx. Please wait for the email to know when your schedule of appearance to the office is. Thank you.")
                        .then((value) => {
                            if (value) {
                                window.location.href = "usershomepage.php";
                            }
                        });
                        </script>
                        </body>';
                        exit();
                    }

                } else {
                    echo '<body>
                    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
                    <script>
                    swal("Notification", "You already applied for your relative.")
                    .then((value) => {
                        if (value) {
                            window.location.href = "usershomepage.php";
                        }
                    });
                    </script>
                    </body>';
                    exit();
                }
            }
        } else {
            // No beneficiary found, create a new beneficiary record
            $query1 = "SELECT * FROM users WHERE Id = $userId";
            $result = mysqli_query($con, $query1);

            if ($result) {
                $row = mysqli_fetch_assoc($result);
                $Lastname = $row['Lastname'];
                $Firstname = $row['Firstname'];
                $Middlename = $row['Middlename'];
                $Birthday = $row['Birthday'];
                $Contactnumber = $row['Contactnumber'];
                $Province = $row['Province'];
                $CityMunicipality = $row['CityMunicipality'];
                $Barangay = $row['Barangay'];
                $HousenoStreet = $row['HousenoStreet'];
                $Email = $row['Email'];
                $Representative_ID = $row['Id'];
            }

            $query = "INSERT INTO beneficiary (Lastname, Firstname, Middlename, Birthday, Contactnumber, Province, CityMunicipality, Barangay, HousenoStreet, Email, Representative_ID, Date, time) VALUES ('$Lastname', '$Firstname', '$Middlename', '$Birthday', '$Contactnumber', '$Province', '$CityMunicipality', '$Barangay', '$HousenoStreet', '$Email', '$Representative_ID', CURDATE(), CURTIME())";

            if (mysqli_query($con, $query)) {
                echo "<script>window.location.href = 'applysched.php';</script>";
                exit();
            }
        }
    } else {
        // No user found
        echo "<script>window.location.href = 'beneficiaryinfo.php';</script>";
        exit();
    }

    // No conflicting transactions found, proceed with the application process
    header("Location: applysched.php");
    exit();
}
       
elseif (isset($_POST['relative'])) {
    $userId = $_SESSION['id'];

    $userId = $_SESSION['id'];

    // Escape the userId to prevent SQL injection
    $userId = mysqli_real_escape_string($con, $userId);

    // Execute the first query to get user details
    $query1 = "SELECT * FROM users WHERE Id = '$userId'";
    $result1 = mysqli_query($con, $query1);

    if ($result1 && mysqli_num_rows($result1) > 0) {
        $user = mysqli_fetch_assoc($result1);
        $userLastname = $user['Lastname'];
        $userFirstname = $user['Firstname'];

        // Execute the second query to get beneficiary details
        $query2 = "SELECT * FROM beneficiary WHERE Representative_ID = '$userId'";
        $result2 = mysqli_query($con, $query2);

        if ($result2 && mysqli_num_rows($result2) > 0) {
            while ($beneficiary = mysqli_fetch_assoc($result2)) {
                $beneficiaryLastname = $beneficiary['Lastname'];
                $beneficiaryFirstname = $beneficiary['Firstname'];
                $beneficiaryId = $beneficiary['Beneficiary_Id'];

                // Check if the user and beneficiary names match
                if ($userLastname === $beneficiaryLastname && $userFirstname === $beneficiaryFirstname) {
                    echo '<body>
                    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
                    <script>
                    swal("Notification", "You already requested a schedule for yourself. Please wait for the email to know when your schedule of appearance to the office is. Thank you.")
                    .then((value) => {
                        if (value) {
                            window.location.href = "usershomepage.php";
                        }
                    });
                    </script>
                    </body>';
                    exit();
                } else {
                    $threeMonthsAgo = date('Y-m-d', strtotime('-3 months'));

                    // Check if there's any transaction for the beneficiary done more than 3 months ago
                    $query3 = "SELECT * FROM transaction WHERE Beneficiary_Id = '$beneficiaryId' AND Status = 'Done' AND Given_Sched >= '$threeMonthsAgo'";
                    $result3 = mysqli_query($con, $query3);

                    if ($result3 && mysqli_num_rows($result3) > 0) {
                        echo '<body>
                        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
                        <script>
                        swal("Notification", "You cannot apply at this time.")
                        .then((value) => {
                            if (value) {
                                window.location.href = "usershomepage.php";
                            }
                        });
                        </script>
                        </body>';
                        exit();
                    } else {
                        // Check for transactions with status 'For Schedule', 'Pending for Requirements', or 'Pending for Payout'
                        $query4 = "SELECT * FROM transaction WHERE Beneficiary_Id = '$beneficiaryId' AND (Status = 'For Schedule' OR Status = 'Pending for Requirements' OR Status = 'Pending for Payout')";
                        $result4 = mysqli_query($con, $query4);

                        if ($result4 && mysqli_num_rows($result4) > 0) {
                            echo '<body>
                            <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
                            <script>
                            swal("Notification", "You already requested a schedule. Please wait for the email to know when your schedule of appearance to the office is. Thank you.")
                            .then((value) => {
                                if (value) {
                                    window.location.href = "usershomepage.php";
                                }
                            });
                            </script>
                            </body>';
                            exit();
                        } else {
                            echo "<script>window.location.href = 'applysched.php';</script>";
                            exit();
                        }
                    }
                }
            }
        } else {
            // No beneficiary associated with the user
            echo "<script>window.location.href = 'beneficiaryinfo.php';</script>";
            exit();
        }
    } 
    
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
   
    <link rel="stylesheet" href="applyingoptions.css" />
</head>
<body>
<div class="container">
    <div class="box form-box">
        <div class="STATUS">
            <center>  
                <h3>For whom are you applying for?</h3>           
                <header>Para Kanino ka nag aapply?</header>
            </center>
        </div>
         
               
            <form id="" action="" method="post">
            
            <div class="field">
                    <input type="submit" class="btn1" name="myself" value="For Myself / Para sa Sarili" required >             
                </div>
                
                <div class="field">
                    <input type="submit" class="btn2" name="relative" value="For my relative / Para sa Kamag-anak" required >                  
                </div>
        </div>
    </div>
</body>
</html>