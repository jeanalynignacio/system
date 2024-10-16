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

   if(isset($_SESSION['valid'])){
    $id = $_SESSION['id'];
    $query = mysqli_query($con, "SELECT * FROM users WHERE Id=$id");

if($result = mysqli_fetch_assoc($query)){
$res_Id = $result['Id'];
$res_Fname = $result['Firstname'];
$res_Lname = $result['Lastname'];
}
}
if(isset($_POST['hospitals'])){
$_SESSION['hospitals'] = $_POST['hospitals'];
}
   if(isset($_POST['serviceType'])){
    $_SESSION['serviceType'] = $_POST['serviceType'];
    
   
    $serviceType = $_SESSION['serviceType'];

} elseif (!isset($_SESSION['serviceType'])) {
    echo "Service type is not set.";
    exit();
}

if (isset($_SESSION['serviceType'])) {
    $serviceType = $_SESSION['serviceType']; // Use the existing session variable
} elseif (isset($_POST['serviceType'])) {
    $_SESSION['serviceType'] = $_POST['serviceType']; // Store in session from form submission
    $serviceType = $_SESSION['serviceType'];
} else {
    echo "Service type is not set.";
    exit();
}
if (isset($_POST['myself']) || isset($_POST['relative'])) {
    $serviceType = $_SESSION['serviceType'];
}
$serviceType = $_SESSION['serviceType'] ?? 'Not selected';


   
// Query to count the transactions
$sql = "SELECT COUNT(*) as transaction_count FROM transaction WHERE  Date = CURDATE() AND AssistanceType NOT IN ('Hospital Bills', 'Laboratories')";
$result = mysqli_query($con, $sql);
$row = mysqli_fetch_assoc($result);

if ($row['transaction_count'] > 100) {
  
    echo '<body>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>
          swal("Daily Application Limit Reached", "We regret to inform you that we have reached our daily quota for applications. Please try again tomorrow.", "error")
          .then((value) => {
        if (value) {
            window.location.href = "usershomepage.php";
        }
    });
    </script>
    </body>';
} 
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
                    $query3 = "SELECT * FROM history WHERE Beneficiary_ID = $beneficiaryId  AND ReceivedDate >= '$threeMonthsAgo'";
                    $result3 = mysqli_query($con, $query3);
                   
                    if (mysqli_num_rows($result3) > 0) {
                            // Fetch the last transaction date
                            $row = mysqli_fetch_assoc($result3);
                            $lastTransactionDate = $row['ReceivedDate'];
                            $date = new DateTime($lastTransactionDate);
                            $formattedDate = $date->format('m/d/Y');
                        
                            echo '<body>
                            <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
                            <script>
                            swal("Notification", "You cannot apply at this time. You need atleast three months before applying again. Your last transaction was on ' . $formattedDate . '","info")
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
                        swal("Notification", "You already requested a schedule. Please wait for the email to know when your schedule of appearance to the office is. Thank you.","info")
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
                        swal("Notification", "You already pending for requirements. Please wait for the email to know when your schedule of appearance to the office is. Thank you.")
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
                        swal("Notification", "You already pending for payout. Please wait for the email to know when your schedule of appearance to the office is. Thank you.","info")
                        .then((value) => {
                            if (value) {
                                     window.location.href = "usershomepage.php";
                            }
                        });
                        </script>
                        </body>';
                        exit();
                    }
                    $query4 = "SELECT * FROM transaction WHERE Beneficiary_Id = $beneficiaryId AND Status = 'Request for Re-schedule' ";
                    $result4 = mysqli_query($con, $query4);

                    if  (mysqli_num_rows($result4) > 0) {
                        echo '<body>
                        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
                        <script>
                        swal("Notification", "You already request for reschedule. Please wait to an email to know if it is accepted or not.")
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
                        swal("Notification", "You already pending for validation. Please wait for the email to know when your schedule of appearance to the office is. Thank you.","info")
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
                    $threeMonthsAgo = date('Y-m-d', strtotime('-3 months'));
                    $query3 = "SELECT * FROM history WHERE Beneficiary_ID = '$beneficiaryId' AND ReceivedDate >= '$threeMonthsAgo'";
                    $result3 = mysqli_query($con, $query3);
                    
                    if (mysqli_num_rows($result3) > 0) {
                        $row = mysqli_fetch_assoc($result3);
                        $lastTransactionDate = $row['ReceivedDate'];
                        $date = new DateTime($lastTransactionDate);
                        $formattedDate = $date->format('m/d/Y');
                    
                    
                        echo '<body>
                        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
                        <script>
                           swal("Notification", "You cannot apply at this time. You need at least three months before applying again. Your last transaction was on ' . $formattedDate . '", "info")
                            .then((value) => {
                            if (value) {
                                   window.location.href = "usershomepage.php";
                            }
                        });
                        </script>
                        </body>';
                        exit();
                    } else {
                       
                echo '<body>
                <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
                <script>
                swal("Notification", "You already applied for your relative. Please wait for further instructions. Always check your email for further updates. Thank you.","info")
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

                date_default_timezone_set('Asia/Manila');
                $Date = date('Y-m-d'); 
                $TIME = date('H:i:s'); 
            }

            $query = "INSERT INTO beneficiary (Lastname, Firstname, Middlename, Birthday, Contactnumber, Province, CityMunicipality, Barangay, HousenoStreet, Email, Representative_ID, Date, time) VALUES ('$Lastname', '$Firstname', '$Middlename', '$Birthday', '$Contactnumber', '$Province', '$CityMunicipality', '$Barangay', '$HousenoStreet', '$Email', '$Representative_ID', '$Date', '$TIME')";

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
                if ($userLastname === $beneficiaryLastname && $userFirstname !== $beneficiaryFirstname) {
                    // Check for recent transactions
                    $query5 = "SELECT * FROM transaction WHERE Beneficiary_Id = '$beneficiaryId'";
                    $result5 = mysqli_query($con, $query5);
        
                    if ($result5 && mysqli_num_rows($result5) > 0) {
                       
                            echo '<body>
                            <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
                            <script>
                             swal("Notification", "You have an existing application. Please wait for further instructions. Always check your email for further updates. Thank you.", "info")
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
                        $query3 = "SELECT * FROM history WHERE Beneficiary_ID = '$beneficiaryId' AND ReceivedDate >= '$threeMonthsAgo'";
                        $result3 = mysqli_query($con, $query3);
                        
                        if (mysqli_num_rows($result3) > 0) {
                            $row = mysqli_fetch_assoc($result3);
                            $lastTransactionDate = $row['ReceivedDate'];
                            $date = new DateTime($lastTransactionDate);
                            $formattedDate = $date->format('m/d/Y');
                        
                        
                            echo '<body>
                            <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
                            <script>
                               swal("Notification", "You cannot apply at this time. You need at least three months before applying again. Your last transaction was on ' . $formattedDate . '", "info")
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
    }else{
        $threeMonthsAgo = date('Y-m-d', strtotime('-3 months'));
        $query3 = "SELECT * FROM history WHERE Beneficiary_ID = '$beneficiaryId' AND ReceivedDate >= '$threeMonthsAgo'";
        $result3 = mysqli_query($con, $query3);
        
        if (mysqli_num_rows($result3) > 0) {
            $row = mysqli_fetch_assoc($result3);
            $lastTransactionDate = $row['ReceivedDate'];
            $date = new DateTime($lastTransactionDate);
            $formattedDate = $date->format('m/d/Y');
        
        
            echo '<body>
            <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
            <script>
               swal("Notification", "You cannot apply at this time. You need at least three months before applying again. Your last transaction was on ' . $formattedDate . '", "info")
                .then((value) => {
                if (value) {
                        window.location.href = "usershomepage.php";
                }
            });
            </script>
            </body>';
            exit();
        } else {
           
    echo '<body>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>
    swal("Notification", "You already applied for yourself. Please wait for further instructions. Always check your email for further updates. Thank you.","info")
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
}
        }
      else {
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
     <title>Apply</title>
    <link rel="stylesheet" href="applyingoptions.css" />
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC"
      crossorigin="anonymous"
    />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    />
</head>
<body>
<div class = "all-content">

<nav
    class="navbar navbar-expand-lg navbar-light"
    style="background-color: white;"
  >
    <div class="container-fluid">
      <a
        class="navbar-brand" href="#" id="logo"  style="font-size: 15px; color: #1477d2;">
        <img src="images/background.png"/> Provincial Government of Bataan- Special Assistance Program
      </a>
      <button
        class="navbar-toggler"
        type="button"
        data-bs-toggle="collapse"
        data-bs-target="#navbarSupportedContent"
        aria-controls="navbarSupportedContent"
        aria-expanded="false"
        aria-label="Toggle navigation"
      >
        <span>
          <i
            class="fa-solid fa-bars"
            style="color: #1477d2; font-size: 23px"
          ></i
        ></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a style = " color: #1477d2; padding-left:10px;  margin-left:30px;" class="nav-link" aria-current="page" href="usershomepage.php">
                 Home 
            </a>
          </li>
         
          
         <li class="nav-item">
            <a style = " color: #1477d2; padding-left:10px;" class="nav-link"  onclick="toggleMenu()" style="color: white" >Profile </a>
          </li>

<div class="sub-menu-wrap" id="subMenu">
<div class="sub-menu">
    <div class="user-info">
    <img src="images/profile.png">
        <h2><?php echo $res_Fname; ?> <?php echo $res_Lname; ?></h2>

        </div>
        <hr>



<form action="edit.php" method="POST" class="sub-menu-link">
<input type="hidden" name="userId" value="<?php echo $res_Id; ?>">
<button type="submit" class="btn-edit-profile" >
    <img src="images/profile.png">
    <p>Edit Profile</p>
    
</button>
</form>

    <a href="logout.php" class="sub-menu-link">
            <img src="images/logout.png">
            <p> Log out</p>
       
    </a>
        
         </ul>
      </div>
    </div>
  </nav>

<div id = "home">



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
                  <input type="hidden" required name="serviceType" value="<?php echo $serviceType; ?>">

                  
                     <?php if ($serviceType !== 'Burial'): ?>
                    <input type="submit"  id="submitButton" class="btn1" name="myself" value="For Myself / Para sa Sarili" required >     
                    <?php endif; ?>
                  </div>
                
                <div class="field">
                    <input type="submit" class="btn2" name="relative" value="For my relative / Para sa Kamag-anak" required >                  
                 </div>
        </div>
    </div>
    </div>
    </div>
    <script>
           src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
      crossorigin="anonymous" ></script>

<script >
        let subMenu= document.getElementById("subMenu");
        function toggleMenu(){
            subMenu.classList.toggle("open-menu");
   }


    </script>
</body>
</html>
