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

    if(isset($_SESSION['serviceType'])){
        $serviceType = $_SESSION['serviceType'];
        echo"<?php$serviceType?>";
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
            case 'implantbakal':
                $serviceType = "Implant";
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
            </script>';
            echo '<script>
             setTimeout(function(){
                window.location.href="usershomepage.php";
            }, 3000);
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
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>PGB - Special Assistance Program</title>
    <link rel="stylesheet" href="applysched.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
        .requirements-container {
            display: flex;
            justify-content: left;
        }
        .requirements-container ul {
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="all-content">
        <nav class="navbar navbar-expand-lg navbar-light" style="background-color: white;">
            <div class="container-fluid">
                <a class="navbar-brand" href="#" id="logo" style="font-size: 15px; color: #1477d2; background: white;">
                    <img src="images/background.png" /> Provincial Government of Bataan - Special Assistance Program
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span>
                        <i class="fa-solid fa-bars" style="color: white; font-size: 23px"></i>
                    </span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a style="background: white; color: blue; padding-left:10px; margin-left:50px;" class="nav-link" aria-current="page" href="usershomepage.php">
                                Home 
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
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

                    <?php if ($serviceType === 'dialysis') : ?>
                        <label>
                           <!-- <input type="radio" name="category" value="Burial" onclick="showRequirements('Burial')">
                            FINANCIAL ASSISTANCE FOR BURIAL
                            
                        </label><br>-->
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
                    <?php if ($serviceType === 'implantbakal') : ?>
                        <label>
                           <!-- <input type="radio" name="category" value="Burial" onclick="showRequirements('Burial')">
                            FINANCIAL ASSISTANCE FOR BURIAL
                            
                        </label><br>-->
                        <h1>ASSISTANCE FOR IMPLANT(BAKAL) REQUIREMENTS</h1>
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
                        <label>
                           <!-- <input type="radio" name="category" value="Burial" onclick="showRequirements('Burial')">
                            FINANCIAL ASSISTANCE FOR BURIAL
                            
                        </label><br>-->
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
                    <input class="applysched" type="submit" value="Apply for Schedule" name="submit">
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
    </script>
</body>
</html>
