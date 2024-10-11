<?php
    session_start();

    include("php/config.php");
    if(!isset($_SESSION['valid'])){
      
    }
/*
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
    $res_Id = $result['Id'];
    $res_Fname = $result['Firstname'];
    $res_Lname = $result['Lastname'];
    $res_profile = $result['userIDpic'];
    }
    }*/

    if(isset($_SESSION['serviceType'])){
        $serviceType = $_SESSION['serviceType'];
        echo"<?php$serviceType?>";
    }else{
        $password = 'Jeana29!';

        // Hash the password using bcrypt
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        
        // Display the hashed password
        echo $hashedPassword;
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
                $serviceType = "Medicine";
                $query = "INSERT INTO medicines (Beneficiary_ID, MedicineType) VALUES ('$BENEID', 'Medicine')";
                $query2 = "INSERT INTO transaction (Beneficiary_Id, TransactionType, AssistanceType, Status, Date,transaction_time) VALUES ('$BENEID', 'Online', '$serviceType', 'For Schedule', CURDATE(),CURTIME())";
                break;
            case 'hospitalbills':
                $serviceType = "Hospital Bills";
                $query = "INSERT INTO hospitalbill (Beneficiary_ID, PartneredHospital, billamount) VALUES ('$BENEID', 'Medicine','0')";
                $query2 = "INSERT INTO transaction (Beneficiary_Id, TransactionType, AssistanceType, Status, Date,transaction_time) VALUES ('$BENEID', 'Online', '$serviceType', 'For Schedule', CURDATE(),CURTIME())";
                break;
                case 'laboratories':
                    $serviceType = "Laboratories";
                    $query = "INSERT INTO laboratories (Beneficiary_ID, LabType) VALUES ('$BENEID', 'Medicine')";
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
             
            exit();
        } else {
            echo "Error: " . mysqli_error($con);
        }
    }
   
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            <a style = " color: #1477d2; padding-left:10px;" class="nav-link" aria-current="page" href="usershomepage.php">
                 Home 
            </a>
          </li>
         
          
         <li class="nav-item">
            <a style = " color: #1477d2; padding-left:10px;" class="nav-link"  onclick="toggleMenu()" style="color: white" >Profile </a>
          </li>

<div class="sub-menu-wrap" id="subMenu">
<div class="sub-menu">
    <div class="user-info">
         <img src="profile_images/<?php echo $res_profile; ?>" style="height: 50px; width: 50px;" alt="fas fa-user">

        <h2><?php echo $res_Fname; ?>, <?php echo $res_Lname; ?></h2>

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
    <form action="#" class="form-email" method="POST">
        <input type="hidden" name="Beneficiary_ID" value="<?php echo $res_Id; ?>">
        <input type="hidden" name="serviceType" value="<?php echo htmlspecialchars($serviceType); ?>">
        <div>
            <label class="bur">
             Please check your requirements if complete you can proceed to requesting schedule<br>
                <center><h3>(Ano ang gusto mong applyan na assistance? Maari kang pumili sa baba)</h3></center>
            </label><br><br>
           <!-- <?php if ($serviceType === 'medicines' || $serviceType === 'hospital') : ?>
                <label>
                    <input type="radio" name="category" value="medicine" onclick="showRequirements('medicine')">
                    ASSISTANCE FOR MEDICINES
                </label><br>
                <label>
                    <input type="radio" name="category" value="medicine" onclick="showRequirements('medicine')">
                    ASSISTANCE FOR LABORATORIES
                </label><br>
                <label>
                    <input type="radio" name="category" value="medicine" onclick="showRequirements('medicine')">
                    ASSISTANCE FOR HOSPITAL BILLS
                </label><br>
            <?php endif; ?> -->


            <?php if ($serviceType === 'Burial') : ?>
                <label>
                   <!-- <input type="radio" name="category" value="Burial" onclick="showRequirements('Burial')">
                    FINANCIAL ASSISTANCE FOR BURIAL
                    
                </label><br>-->
                <h1>FINANCIAL ASSISTANCE FOR BURIAL REQUIREMENTS</h1>
            <ul style = "text-align: left; margin-left:20px">
                <input type="checkbox" onclick="checkAllChecked()"> Registered Death Certificate (2 PHOTOCOPIES)<br>
                <input type="checkbox" onclick="checkAllChecked()"> Funeral Contract with Balance (2 PHOTOCOPIES)<br>
                <input type="checkbox" onclick="checkAllChecked()"> Promissory Note or Certification with Balance (1 ORIGINAL, 1 PHOTOCOPY)<br>
                <input type="checkbox" onclick="checkAllChecked()"> Sulat (SULAT KAMAY) na humihingi ng tulong kay Gov. Joet S. Garcia<br>
                <input type="checkbox" onclick="checkAllChecked()"> Xerox Valid ID ng Pasyente w/ 3 signatures or Xerox Valid ID ng naglalakad<br>
                <input type="checkbox" onclick="checkAllChecked()"> Brgy. Indigency (Pasyente) & Brgy. Indigency (Naglalakad)<br>
            </ul>
            <h1>SUPPORTING DOCUMENTS</h1>
            <ul style = "text-align: left; margin-left:20px">
                <input type="checkbox" onclick="checkAllChecked()"> Xerox copy ng Birth Certificate (Kung anak o magulang ang pasyente)<br>
               <input type="checkbox" onclick="checkAllChecked()"> Xerox ng Marriage Certificate (Kung asawa ang pasyente)<br>
               <input type="checkbox" onclick="checkAllChecked()"> Birth Certificate and Marriage Certificate (ng magulang kung kapatid ang pasyente)<br>
            </ul>
        </div>
            <?php endif; ?>

            <?php if ($serviceType === 'dialysis') : ?>
                <label>
                 
                <h1>FINANCIAL ASSISTANCE FOR DIALYSIS REQUIREMENTS</h1>
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
                <label>
                   <!-- <input type="radio" name="category" value="Burial" onclick="showRequirements('Burial')">
                    FINANCIAL ASSISTANCE FOR BURIAL
                    
                </label><br>-->
                <input type="text" name="hospital" id="labtype"  autocomplete="off"   required>  
                <h1>ASSISTANCE FOR HOSPITAL BILLS REQUIREMENTS</h1>
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
          

            <?php if ($serviceType === 'laboratories') : ?>
                <label>
                   <!-- <input type="radio" name="category" value="Burial" onclick="showRequirements('Burial')">
                    FINANCIAL ASSISTANCE FOR BURIAL
                    
                </label><br>-->
                <div class="field input">
            <label for = "labtype">Please type what kind of laboratory you want to apply</label>
            <input type="text" name="labtype" id="labtype"  autocomplete="off"   required>                      
        </div>
                <h1>ASSISTANCE FOR LABORATORIES REQUIREMENTS</h1>
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

            <?php if ($serviceType === 'medicines') : ?>
             
                   <!-- <input type="radio" name="category" value="Burial" onclick="showRequirements('Burial')">
                    FINANCIAL ASSISTANCE FOR BURIAL
                    
                </label><br>-->
                <label for = "Lastname">Type of medicine you want to apply for:</label>
                        <input type="text" name="medtype" id="Lastname" placeholder="Medicine Type" autocomplete="off"  required>                      
                   
                <h1>ASSISTANCE FOR MEDICINES REQUIREMENTS</h1>
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



       <!-- <div class="sub-menu-wrap" id="subMenu">
            <div class="sub-menu">
                <div class="user-info" style="background: none; margin-left: 10px;">
                    <h3>REQUIREMENTS NEEDED:</h3>
                    <div class="requirements-container">
                        <ul id="requirement-list"></ul>
                    </div>
                </div>
            </div>
        </div>-->
    
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


       
        let subMenu= document.getElementById("subMenu");
        function toggleMenu(){
            subMenu.classList.toggle("open-menu");
   }



</script>
</body>
</html>
