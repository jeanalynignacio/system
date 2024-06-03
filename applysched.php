<?php
    session_start();

    include("php/config.php");
    if(!isset($_SESSION['valid'])){
        header("Location: index.php");
    }

    if(isset($_SESSION['valid'])){
        $id = $_SESSION['id'];
        $query = mysqli_query($con, "SELECT * FROM beneficiary WHERE Representative_ID=$id");

        if($result = mysqli_fetch_assoc($query)){
            $res_Id = $result['Beneficiary_Id'];
            $res_REPID = $result['Representative_ID'];
        }
    }
    if(isset($_SESSION['valid'])){
        $id = $_SESSION['id'];
        $query = mysqli_query($con, "SELECT * FROM users WHERE Id=$id");
    
    if($result = mysqli_fetch_assoc($query)){
    $res_Id2 = $result['Id'];
    $res_Fname = $result['Firstname'];
    $res_Lname = $result['Lastname'];
    $res_profile = $result['userIDpic'];
    }
    }
    if(isset($_SESSION['hospitals'])){
        $hospitals = $_SESSION['hospitals'];
   }

    if(isset($_SESSION['serviceType'])){
        $serviceType = $_SESSION['serviceType'];
        
    }else{
        echo "Service type not set.";
        exit;
    }

    if(isset($_POST['submit'])) {
        $serviceType = $_SESSION['serviceType'];
        $BENEID = $_POST['Beneficiary_ID'];
      
        switch ($serviceType) {
            case 'dialysis':
                $serviceType= "Dialysis";
                $query = "INSERT INTO financialassistance (Beneficiary_ID, FA_Type) VALUES ('$BENEID', '$serviceType')";
                $query2 = "INSERT INTO transaction (Beneficiary_Id, TransactionType, AssistanceType, Status, Date,transaction_time) VALUES ('$BENEID', 'Online', 'Financial Assistance', 'For Schedule', CURDATE(),CURTIME())";
                break;
            case 'Burial':
                $serviceType = "Burial";
                $query = "INSERT INTO financialassistance (Beneficiary_ID, FA_Type) VALUES ('$BENEID', '$serviceType')";
                $query2 = "INSERT INTO transaction (Beneficiary_Id, TransactionType, AssistanceType, Status, Date,transaction_time) VALUES ('$BENEID', 'Online', 'Financial Assistance', 'For Schedule', CURDATE(),CURTIME())";
                break;
            case 'chemrad':
                $serviceType = "Chemotherapy & Radiation";
                $query = "INSERT INTO financialassistance (Beneficiary_ID, FA_Type) VALUES ('$BENEID', '$serviceType')";
                $query2 = "INSERT INTO transaction (Beneficiary_Id, TransactionType, AssistanceType, Status, Date,transaction_time) VALUES ('$BENEID', 'Online', 'Financial Assistance', 'For Schedule', CURDATE(),CURTIME())";
                break;
   
            case 'medicines':
                
                $medType=$_POST['medType'];
                $serviceType = "Medicine";
                $query = "INSERT INTO medicines (Beneficiary_ID, MedicineType) VALUES ('$BENEID', '$medType')";
                $query2 = "INSERT INTO transaction (Beneficiary_Id, TransactionType, AssistanceType, Status, Date,transaction_time) VALUES ('$BENEID', 'Online', '$serviceType', 'For Schedule', CURDATE(),CURTIME())";
                break;
            case 'hospitalbills':
                $hospitals = $_SESSION['hospitals'];
                $hospitals=$_POST['hospitals'];
                $escaped_hospitals = addslashes($hospitals);

                $serviceType = "Hospital Bills";
                $query = "INSERT INTO hospitalbill (Beneficiary_ID, PartneredHospital, billamount) VALUES ('$BENEID', '$escaped_hospitals','0')";
                $query2 = "INSERT INTO transaction (Beneficiary_Id, TransactionType, AssistanceType, Status, Date,transaction_time) VALUES ('$BENEID', 'Online', '$serviceType', 'For Schedule', CURDATE(),CURTIME())";
                break;
                case 'laboratories':
                    $labtype=$_POST['labType'];
                    $serviceType = "Laboratories";
                    $query = "INSERT INTO laboratories (Beneficiary_ID, LabType) VALUES ('$BENEID', '$labtype')";
                    $query2 = "INSERT INTO transaction (Beneficiary_Id, TransactionType, AssistanceType, Status, Date,transaction_time) VALUES ('$BENEID', 'Online', '$serviceType', 'For Schedule', CURDATE(),CURTIME())";
                    break;
            default:
                die("Invalid category selected");
        }
    
        $result1 = mysqli_query($con, $query);
        $result2 = mysqli_query($con, $query2);
        
        if($result1 && $result2) {
                 echo '<body>
                        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
                        <script>
                        swal("Thank you for submitting your request. Please wait for an email with your scheduled appointment","","success")
                        .then((value) => {
                            if (value) {
                                window.location.href = "usershomepage.php";
                            }
                        });
                        </script>
                        </body>';
    }
}  
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="applyingoptions.css" />
    <title>Applying Schedule</title>
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
    <style>
    .scheduleButton {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
        }
        .scheduleButton:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }
        </style>
</head>

<body >
<div class = "all-content"style="background:white;" >

<nav
    class="navbar navbar-expand-lg navbar-light"
    style="background-color: #1477d2;"
  >
    <div class="container-fluid">
      <a
        class="navbar-brand" href="#" id="logo"  style="font-size: 15px; color: white;">
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
            style="color:white; font-size: 23px"
          ></i
        ></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a style = " color: white; padding-left:10px;" class="nav-link" aria-current="page" href="usershomepage.php">
                 Home 
            </a>
          </li>
         
          
         <li class="nav-item">
            <a style = " color: white; padding-left:10px;" class="nav-link"  onclick="toggleMenu()" style="color: white" >Profile </a>
          </li>

<div class="sub-menu-wrap" id="subMenu">
<div class="sub-menu">
    <div class="user-info">
         <img src="profile_images/<?php echo $res_profile; ?>" style="height: 50px; width: 50px;" alt="fas fa-user">

        <h2><?php echo $res_Fname; ?>, <?php echo $res_Lname; ?></h2>

        </div>
        <hr>


<form action="edit.php" method="POST" class="sub-menu-link">
<input type="hidden" name="userId" value="<?php echo $res_Id2; ?>">
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
  <div id = "home" style="background:white;">

        <div class="container" style="background:white;">
            <form action="#" class="form-email" method="POST">
                <input type="hidden" name="Beneficiary_ID" value="<?php echo $res_Id; ?>">
                <input type="hidden" name="serviceType" value="<?php echo htmlspecialchars($serviceType); ?>">
                  <input type="hidden" name="hospitals" value="<?php echo htmlspecialchars($hospitals); ?>">
              
                <div>
                    <label class="bur" style="color:blue; font-size:30px; margin-top:50px;">
                     Please check your requirements if complete you can proceed to requesting schedule<br>
                        <center><h3>(Icheck kung kumpleto ang mga kinakailangan. Kung oo, maaari kang magpatuloy sa paghingi ng iskedyul.)</h3></center>
                    </label><br><br>
                    
                  
                   

                    <?php if ($serviceType === 'medicines') : ?>
                        <label>
                           <!-- <input type="radio" name="category" value="Burial" onclick="showRequirements('Burial')">
                            FINANCIAL ASSISTANCE FOR BURIAL
                            
                        </label><br>-->
                        <input type="text" name="medType" placeholder="Medicine Type" autocomplete="off"  required>                      
              
                  <h1>ASSISTANCE FOR MEDICINES REQUIREMENTS</h1>
                    <ul style = "text-align: left; margin-left:60px">
                        <input type="checkbox" onclick="checkAllChecked()"> Updated Medical Certificate/Medical Abstract (1 ORIGINAL, 1 PHOTOCOPY)<br>
                        <input type="checkbox" onclick="checkAllChecked()"> Reseta ng Gamot NOTE: 1st & 2nd checks same date, same doctor, same signature with Doctor's License No.<br> (2 PHOTOCOPIES)<br>
                       <input type="checkbox" onclick="checkAllChecked()"> Sulat (SULAT KAMAY) na humihingi ng tulong kay Gov. Joet S. Garcia<br>
                        <input type="checkbox" onclick="checkAllChecked()"> Xerox Valid ID ng Pasyente w/ 3 signatures or Xerox Valid ID ng Naglalakad w/ 3 signatures<br>
                       <input type="checkbox" onclick="checkAllChecked()"> Brgy. Indigency (Pasyente) / Brgy. Indigency (Representative)<br>
                    </ul>
                    <h1>SUPPORTING DOCUMENTS</h1>
                    <ul style = "text-align: left; margin-left:60px">
                        <input type="checkbox" onclick="checkAllChecked()"> Xerox copy ng Birth Certificate (Kung anak o magulang ang pasyente)<br>
                        <input type="checkbox" onclick="checkAllChecked()"> Xerox ng Marriage Certificate (Kung asawa ang pasyente)<br>
                        <input type="checkbox" onclick="checkAllChecked()"> Birth Certificate and Marriage Certificate (ng magulang kung kapatid ang pasyente)<br>
                    </ul>
                </div>
                <?php endif; ?>
                <?php if ($serviceType === 'Burial') : ?>
                    
                     <h1>FINANCIAL ASSISTANCE FOR BURIAL REQUIREMENTS</h1>
                    <ul style = "text-align: left; margin-left:60px">
                        <input type="checkbox" onclick="checkAllChecked()"> Registered Death Certificate (2 PHOTOCOPIES)<br>
                        <input type="checkbox" onclick="checkAllChecked()"> Funeral Contract with Balance (2 PHOTOCOPIES)<br>
                        <input type="checkbox" onclick="checkAllChecked()"> Promissory Note or Certification with Balance (1 ORIGINAL, 1 PHOTOCOPY)<br>
                        <input type="checkbox" onclick="checkAllChecked()"> Sulat (SULAT KAMAY) na humihingi ng tulong kay Gov. Joet S. Garcia<br>
                        <input type="checkbox" onclick="checkAllChecked()"> Xerox Valid ID ng Pasyente w/ 3 signatures or Xerox Valid ID ng naglalakad<br>
                        <input type="checkbox" onclick="checkAllChecked()"> Brgy. Indigency (Pasyente) & Brgy. Indigency (Naglalakad)<br>
                    </ul>
                    <h1>SUPPORTING DOCUMENTS</h1>
                    <ul style = "text-align: left; margin-left:60px">
                        <input type="checkbox" onclick="checkAllChecked()"> Xerox copy ng Birth Certificate (Kung anak o magulang ang pasyente)<br>
                       <input type="checkbox" onclick="checkAllChecked()"> Xerox ng Marriage Certificate (Kung asawa ang pasyente)<br>
                       <input type="checkbox" onclick="checkAllChecked()"> Birth Certificate and Marriage Certificate (ng magulang kung kapatid ang pasyente)<br>
                    </ul>
                </div>
                <?php endif; ?>

              
                <?php if ($serviceType === 'hospitalbills') : ?>
                     
                    <h1>GUARANTEE LETTER FOR HOSPITAL BILL</h1>
                    <ul style = "text-align: left; margin-left:60px" >
                        <input type="checkbox" onclick="checkAllChecked()"> Final Bill w/ Discharge Date (May pirma ng Billing Clerk)/Promissory Note<br>
                        <input type="checkbox" onclick="checkAllChecked()"> Medical Abstract/Medical Certificate (May Pangalan Pirma at License # ng Doctor)<br>
                        <input type="checkbox" onclick="checkAllChecked()"> Sulat (Sulat Kamay) na Humihingi ng tulong kay Gov. Joet S. Garcia<br>
                        <input type="checkbox" onclick="checkAllChecked()"> Xerox Valid ID ng Pasyente<br>
                        <input type="checkbox" onclick="checkAllChecked()"> Xerox Valid ID ng Maglalakad<br>
                        <input type="checkbox" onclick="checkAllChecked()"> BRGY. INDIGENCY (PASYENTE)<br>
                        <input type="checkbox" onclick="checkAllChecked()"> SOCIAL CASE STUDY (MSWDO)<br>
                </ul>
                        <h1>SUPPORTING DOCUMENTS</h1>
                        <ul style = "text-align: left; margin-left:60px" >
                   
                        <input type="checkbox" onclick="checkAllChecked()"> XEROX COPY NG BIRTH CERTIFICATE (KUNG ANAK O MAGULANG ANG PASYENTE)<br>
                        <input type="checkbox" onclick="checkAllChecked()"> XEROX NG MARRIAGE (CERTIFICATE KUNG ASAWA ANG PASYENTE)<br>
                        <input type="checkbox" onclick="checkAllChecked()"> BIRTH CERTIFICATE AND MARRIAGE CERTIFICATE (NG MAGULANG) KUNG KAPATID ANG PASYENTE<br>
                        <input type="checkbox" onclick="checkAllChecked()"> AFFIDAVIT OF COHABITATION KUNG MAG LIVE IN PARTNER O KATUNAYAN NAG SASAMA NGUNIT HINDI PA KASAL<br>       
                    
                    </ul>
                </div>
                <?php endif; ?>


                <?php if ($serviceType === 'laboratories') : ?>
                    <input type="text" name="labType" placeholder="Laboratory Type" autocomplete="off"  required>     
                     <h1>ASSISTANCE FOR LABORATORIES</h1>
                     <ul style = "text-align: left; margin-left:60px" >
                         <input type="checkbox" onclick="checkAllChecked()"> Laboratories result<br>
                         <input type="checkbox" onclick="checkAllChecked()"> Request Letter from Barangay Health Center<br>
                         <input type="checkbox" onclick="checkAllChecked()"> Xerox Valid ID ng Pasyente<br>
                         <input type="checkbox" onclick="checkAllChecked()"> Xerox Valid ID ng Maglalakad<br>
                         <input type="checkbox" onclick="checkAllChecked()"> BRGY. INDIGENCY (PASYENTE)<br>
                         
                 </ul>
                         <h1>SUPPORTING DOCUMENTS</h1>
                         <ul style = "text-align: left; margin-left:60px" >
                    
                         <input type="checkbox" onclick="checkAllChecked()"> XEROX COPY NG BIRTH CERTIFICATE (KUNG ANAK O MAGULANG ANG PASYENTE)<br>
                         <input type="checkbox" onclick="checkAllChecked()"> XEROX NG MARRIAGE (CERTIFICATE KUNG ASAWA ANG PASYENTE)<br>
                         <input type="checkbox" onclick="checkAllChecked()"> BIRTH CERTIFICATE AND MARRIAGE CERTIFICATE (NG MAGULANG) KUNG KAPATID ANG PASYENTE<br>
                         <input type="checkbox" onclick="checkAllChecked()"> AFFIDAVIT OF COHABITATION KUNG MAG LIVE IN PARTNER O KATUNAYAN NAG SASAMA NGUNIT HINDI PA KASAL<br>       
                     
                     </ul>
                 </div>
                 <?php endif; ?>
                 <?php if ($serviceType === 'dialysis') : ?>
                     
                     <h1>GUARANTEE LETTER FOR LABORATORIES</h1>
                     <ul style = "text-align: left; margin-left:60px" >
                         <input type="checkbox" onclick="checkAllChecked()"> Laboratories result<br>
                         <input type="checkbox" onclick="checkAllChecked()"> Request Letter from Barangay Health Center<br>
                         <input type="checkbox" onclick="checkAllChecked()"> Xerox Valid ID ng Pasyente<br>
                         <input type="checkbox" onclick="checkAllChecked()"> Xerox Valid ID ng Maglalakad<br>
                         <input type="checkbox" onclick="checkAllChecked()"> BRGY. INDIGENCY (PASYENTE)<br>
                         
                 </ul>
                         <h1>SUPPORTING DOCUMENTS</h1>
                         <ul style = "text-align: left; margin-left:60px" >
                    
                         <input type="checkbox" onclick="checkAllChecked()"> XEROX COPY NG BIRTH CERTIFICATE (KUNG ANAK O MAGULANG ANG PASYENTE)<br>
                         <input type="checkbox" onclick="checkAllChecked()"> XEROX NG MARRIAGE (CERTIFICATE KUNG ASAWA ANG PASYENTE)<br>
                         <input type="checkbox" onclick="checkAllChecked()"> BIRTH CERTIFICATE AND MARRIAGE CERTIFICATE (NG MAGULANG) KUNG KAPATID ANG PASYENTE<br>
                         <input type="checkbox" onclick="checkAllChecked()"> AFFIDAVIT OF COHABITATION KUNG MAG LIVE IN PARTNER O KATUNAYAN NAG SASAMA NGUNIT HINDI PA KASAL<br>       
                     
                     </ul>
                 </div>
                 <?php endif; ?>

              
            
                <div class="sub">
                    <!--<input class="applysched" type="submit" value="Apply for Schedule" name="submit">-->
                    <center><button type="submit" id="scheduleButton" name="submit" class="scheduleButton" disabled >Get a schedule</button><br>      </center>
                </div>
            </form>
        </div>
    </div>
    <script>
        function showRequirements(category) {
            var requirementList = document.getElementById("requirement-list");
            requirementList.innerHTML = "";
            var requirements = [];

            switch(category) {
                case 'medicine':
                    requirements = [
                        "Prescription",
                        "Laboratory request of laboratory procedure",
                        "Medical Certificate"
                    ];
                    break;
                case 'Burial':
                    requirements = [
                        "Death Certificate",
                        "Funeral Contract",
                        "Barangay Certificate"
                    ];
                    break;
                case 'dialysis':
                    requirements = [
                        "Hemodialysis Protocol",
                        "Medical Certificate",
                        "Barangay Certificate",
                        "Certificate of Philhealth Membership (if any)"
                    ];
                    break;
                case 'chemrad':
                    requirements = [
                        "Medical Certificate",
                        "Prescription of Chemotherapy or Radiation",
                        "Laboratory request of laboratory procedure",
                        "Barangay Certificate"
                    ];
                    break;
            }

            requirements.forEach(function(req) {
                var li = document.createElement("li");
                li.textContent = req;
                requirementList.appendChild(li);
            });
        }
        
        function checkAllChecked() {
            const checkboxes = document.querySelectorAll('input[type="checkbox"]');
            const allChecked = Array.from(checkboxes).every(checkbox => checkbox.checked);
            document.getElementById('scheduleButton').disabled = !allChecked;
        }

        
    </script>
</body>
</html>
