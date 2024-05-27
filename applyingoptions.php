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
   
  if (isset($_POST['myself'])) {
    
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
    } else {
        // No beneficiary associated with the user
        echo "<script>window.location.href = 'applysched.php';</script>";
        exit();
    }
}
if (isset($_POST['myself'])) {
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
          
        }
    }elseif(mysqli_num_rows($result) > 0){
          // Check for transactions with status 'For Schedule'
          $query = "SELECT * FROM transaction WHERE Beneficiary_Id = $beneficiaryId AND Status = 'For Schedule'";
          $result = mysqli_query($con, $query);

          if (mysqli_num_rows($result) > 0) {
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
      
            } else {
              // No conflicting transactions found, proceed with the application process
              header("Location: applysched.php");
              exit();
          }
    }
     else {
        // No beneficiary associated with the user
        echo "<script>window.location.href = 'applysched.php';</script>";
        exit();
    }
}
elseif (isset($_POST['relative'])) {
    $userId = $_SESSION['id'];

   
    $query = "SELECT * FROM beneficiary WHERE Representative_ID = $userId";
    $result = mysqli_query($con, $query);


    if (mysqli_num_rows($result) > 0) {
        // Beneficiary found, get beneficiary ID
        $row = mysqli_fetch_assoc($result);
        $beneficiaryId = $row['Beneficiary_Id'];

        // Check if there's any transaction for the beneficiary done more than 3 months ago
        $threeMonthsAgo = date('Y-m-d', strtotime('-3 months'));
        $query = "SELECT * FROM transaction WHERE Beneficiary_Id = $beneficiaryId AND Status = 'Done' AND Given_Sched >= '$threeMonthsAgo'";
        $result = mysqli_query($con, $query);

        if (mysqli_num_rows($result) > 0) {
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
      
      
        } else {
          
          // Check for transactions with status 'For Schedule'
          $query = "SELECT * FROM transaction WHERE Beneficiary_Id = $beneficiaryId AND (Status = 'For Schedule' OR Status = 'Pending for Requirements' OR Status = 'Pending for Payout')";
         

          $result = mysqli_query($con, $query);

          if (mysqli_num_rows($result) > 0) {
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
        }
         
     else {
        // No beneficiary associated with the user
        echo "<script>window.location.href = 'applysched.php';</script>";
        exit();
    }
}
    }
else {
        // No beneficiary associated with the user
        
         echo "<script>window.location.href = 'beneficiaryinfo.php';</script>";
            exit();
    

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