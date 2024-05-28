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
    if(isset($_POST['submit'])) {
        $selectedCategory = $_POST['category']; // Corrected variable name
       $BENEID = $_POST['Beneficiary_ID'];

       
    
        switch ($selectedCategory) {
             
            case 'dialysis':
                $selectedCategory="Dialysis";
                $query = "INSERT INTO financialassistance  (Beneficiary_ID, FA_Type) VALUES ('$BENEID','$selectedCategory')";
                $query2 = "INSERT INTO transaction (Beneficiary_Id, TransactionType, AssistanceType,Status,Date) VALUES ('$BENEID', 'Online','Financial Assistance','For Schedule', CURDATE())";
                break;
          
            case 'burial':
                $selectedCategory="Burial";
                $query = "INSERT INTO financialassistance  (Beneficiary_ID, FA_Type) VALUES ('$BENEID','$selectedCategory')";
                $query2 = "INSERT INTO transaction (Beneficiary_Id, TransactionType, AssistanceType,Status,Date) VALUES ('$BENEID', 'Online','Financial Assistance','For Schedule', CURDATE())";
                break;
                case 'chemrad':
                    $selectedCategory="Chemotheraphy & Radiation";
                    $query = "INSERT INTO financialassistance  (Beneficiary_ID, FA_Type) VALUES ('$BENEID','$selectedCategory')";
                    $query2 = "INSERT INTO transaction (Beneficiary_Id, TransactionType, AssistanceType,Status,Date) VALUES ('$BENEID', 'Online','Financial Assistance','For Schedule', CURDATE())";
                  break;

                case 'implant':
                $selectedCategory="Implant";
                $query = "INSERT INTO financialassistance  (Beneficiary_ID, FA_Type) VALUES ('$BENEID','$selectedCategory')";
                $query2 = "INSERT INTO transaction (Beneficiary_Id, TransactionType, AssistanceType,Status,Date) VALUES ('$BENEID', 'Online','Financial Assistance','For Schedule', CURDATE())";
              break;
              case 'medicine':
                $selectedCategory="Medicine";
                $query = "INSERT INTO medicines (Beneficiary_ID, MedicineType) VALUES ('$BENEID', 'Medicine')";
                $query2 = "INSERT INTO transaction (Beneficiary_Id, TransactionType, AssistanceType,Status,Date) VALUES ('$BENEID', 'Online','$selectedCategory','For Schedule', CURDATE())";
                break;
                case 'hospital':
                    $selectedCategory="Medicine";
                    $query = "INSERT INTO medicines (Beneficiary_ID, MedicineType) VALUES ('$BENEID', 'Medicine')";
                    $query2 = "INSERT INTO transaction (Beneficiary_Id, TransactionType, AssistanceType,Status,Date) VALUES ('$BENEID', 'Online','$selectedCategory','For Schedule', CURDATE())";
                    break;
            default:
                // Handle unexpected categories
                die("Invalid category selected");
        }
    
        $result1 = mysqli_query($con, $query);
        $result2 = mysqli_query($con, $query2);
        
        // Check if both queries were successful
        if($result1 && $result2) {
            echo '<body>
            <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
            <script>
            swal("Thank you for submitting your request. Please wait for an email with your scheduled appointment","","success")
            </script>';
              echo '<script>
             setTimeout(function(){
                window.location.href="usershomepage.php";
            } ,3000);
          </script>
          </body>'; 
          
            exit(); // Make sure to exit after redirect
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
        /* Center-align containers */
        .requirements-container {
            display: flex;
            justify-content: left;
        }

        /* Adjust margin for individual requirement */
        .requirements-container ul {
            margin: 10px 0;
        }
    </style>

</head>
<body>
    <div class="all-content">
        <!-- navbar !-->
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
        <!-- navbar end -->
        <!-- home section -->
        <div class="container">
            <form action="#" class="form-email" method="POST">
                <!-- Radio buttons for categories -->
          

                <div>
                <input type="hidden" name="Beneficiary_ID" value="<?php echo $res_Id;?>">

                     <label class="bur">
                        
                      What assistance are you applying for? Please choose below.<br>
                      
                    <center><h3> (Ano ang gusto mong applyan na assistance? Maari kang pumili sa baba)</h3></center> 
                    </label><br><br>
                    
                   
                    <label>
                        <input type="radio" name="category" value="medicine" onclick="showRequirements('medicine')" >
                      ASSISTANCE FOR MEDICINES
                    </label><br>
                    <label>
                        <input type="radio" name="category" value="medicine" onclick="showRequirements('medicine')" >
                      ASSISTANCE FOR LABORATORIES
                    </label><br>
                    <label>
                        <input type="radio" name="category" value="medicine" onclick="showRequirements('medicine')" >
                      ASSISTANCE FOR HOSPITAL BILLS
                    </label><br>
                    <label >
                        <input type="radio" name="category" value="burial" onclick="showRequirements('burial')" >
                        FINANCIAL ASSISTANCE FOR BURIAL 
                    </label><br>
                    <label >
                      <input type="radio" name="category" value="dialysis" onclick="showRequirements('dialysis')" >
                      FINANCIAL ASSISTANCE FOR DIALYSIS
                    </label><br>

                    <label >
                        <input type="radio" name="category" value="chemrad" onclick="showRequirements('chemrad')" >
                        FINANCIAL ASSISTANCE FOR CHEMOTHERAPHY & RADIATION
                    </label><br>
                </div>

                <div class="sub-menu-wrap" id="subMenu">
    <div class="sub-menu">
        <div class="user-info" style="background: none; margin-left: 10px; color: white; padding: 10px;">
            <h4 style="background: none;">NOTIFICATION</h4>
        </div>
        <hr>
            <h4 style="background: none;">Thank you for submitting your request. Please wait for an email with your scheduled appointment</h4>
            <center><input type="submit" class="okButton1" name="apply" value="OK"></center>
            
        
    </div>
</div>

                <!-- Requirements sections -->
               <div id="dialysis" class="requirements" style="display: none;">
                    <h1>FINANCIAL ASSISTANCE FOR MEDICINES REQUIREMENTS</h1>
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

                <div id="burial" class="requirements" style="display: none;">
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

                <div id="implant" class="requirements" style="display: none;">
                    <h1>REQUIREMENTS FOR IMPLANT BAKAL</h1>
                    <ul style = "text-align: left; margin-left:60px" >
                        <input type="checkbox" onclick="checkAllChecked()"> Request ng Doctor para sa Implant (may pirma at license # ng Doctor)<br>
                        <input type="checkbox" onclick="checkAllChecked()"> Quotation at halaga ng Bakal (may pirma at license # ng Doctor)<br>
                        <input type="checkbox" onclick="checkAllChecked()"> Medical Abstract (may pirma at license # ng Doctor)<br>
                        <input type="checkbox" onclick="checkAllChecked()"> Sulat (SULAT KAMAY) na humihingi ng tulong kay Gov. Joet S. Garcia<br>
                        <input type="checkbox" onclick="checkAllChecked()"> Xerox Valid ID ng Pasyente<br>
                       <input type="checkbox" onclick="checkAllChecked()"> Brgy. Indigency (Pasyente)<br>
                        <input type="checkbox" onclick="checkAllChecked()"> Social Case Study (MSWDO) <br>
                    </ul>
                    <h1>SUPPORTING DOCUMENTS</h1>
                    <ul style = "text-align: left; margin-left:60px">
                    

                        <input type="checkbox" onclick="checkAllChecked()"> Xerox copy ng Birth Certificate (Kung anak o magulang ang pasyente)<br>
                        <input type="checkbox" onclick="checkAllChecked()"> Xerox ng Marriage Certificate (Kung asawa ang pasyente)<br>
                        <input type="checkbox" onclick="checkAllChecked()"> Birth Certificate and Marriage Certificate (ng magulang kung kapatid ang pasyente)<br>
                        <input type="checkbox" onclick="checkAllChecked()"> Affidavit of Cohabitation kung mag live-in partner o katunayag nagsasama ngunit hindi pa kasal<br>
                        <input type="checkbox" onclick="checkAllChecked()"> Xerox ng Cedula<br>
                        <input type="checkbox" onclick="checkAllChecked()"> Xerox Valid ID ng Maglalakad<br>
                      <input type="checkbox" onclick="checkAllChecked()"> Authorization Letter ng Pasyente
                    </ul> 
                    </div> 
                    </div>     
<br>

<input type="hidden" id="selectedCategoryInput" name="selectedCategory1">        
<center><button type="submit" id="scheduleButton" name="submit" class="scheduleButton" disabled onclick="getradioval()">Get a schedule</button><br>      </center>
            <br>
            </form>
        </div>
    </div>
    <script>
         function showRequirements(category) {
    // Hide all sections
    document.querySelectorAll('.requirements').forEach(section => {
        section.style.display = 'none';
    });

    // Show the selected section
    document.getElementById(category).style.display = 'block';

    // Reset the checkboxes and button state
    document.querySelectorAll('.requirements input[type="checkbox"]').forEach(checkbox => {
        checkbox.checked = false;
    });
    document.getElementById('scheduleButton').disabled = true;

    // Set the hidden field value
    document.getElementById('selectedCategory').value = category;
}

function checkAllChecked() {
    const category = document.querySelector('input[name="category"]:checked').value;
    const checkboxes = document.querySelectorAll(`#${category} input[type="checkbox"]`);
    const allChecked = Array.from(checkboxes).every(checkbox => checkbox.checked);
    document.getElementById('scheduleButton').disabled = !allChecked;
}

        

//let subMenu= document.getElementById("subMenu");
       
    function getradioval() {
       
          // subMenu.classList.toggle("open-menu");
        
      
        var radios = document.getElementsByName('selectedCategory');
        var selectedValue = "";
        for (var i = 0; i < radios.length; i++) {
            if (radios[i].checked) {
                selectedValue = radios[i].value;
                break; // Exit the loop once a radio button is checked
            }
        }
        // Set the value of the hidden input box
        document.getElementById('selectedCategoryInput').value = selectedValue;
     
   }
</script>


</body>
</html>
