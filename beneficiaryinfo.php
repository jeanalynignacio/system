<?php 
    session_start();

    include("php/config.php");
    if(!isset($_SESSION['valid'])){
        header("Location: index.php");

    }
 

                 if(isset($_SESSION['valid'])){
            $id = $_SESSION['id'];
            $query = mysqli_query($con, "SELECT * FROM users WHERE Id=$id");

if($result = mysqli_fetch_assoc($query)){
    $res_Id = $result['Id'];
    $res_Fname = $result['Firstname'];
     $res_Lname = $result['Lastname'];
     $res_Email= $result['Email'];
     $Contactnumber = $result['Contactnumber'];
     $street = $result['HousenoStreet'];
     $brgy= $result['Barangay'];
     $city = $result['CityMunicipality'];
}
                }
                if (isset($_POST['serviceType'])) {
                  $_SESSION['serviceType'] = $_POST['serviceType']; // Store in session
              
                    
                 
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
   
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="beneficiaryinfo.css">
</head>

<body>
               
    <div class = "container">
         
        <div class="box form-box">
   

        <?php 
       
        // Define variables to store selected city and barangay
    
  
                include ("php/config.php");
                $errors = []; // Initialize $errors as an empty array

                    $lastError = "";
                    $firstError = "";
                    $middleError = "";
                    $bdayError = "";
                    $numError = "";
                    $provError = "";
                    $cityError = "";
                    $brgyError = "";
                    $stError = "";
                    $emailError = "";
                $benelnameError = "";
                $relationError="";
                  
                    $Errors = "";
                    if(isset($_POST['submit'])){
                    // receive all input values from the form

    $BeneficiaryLastname = $_POST['BeneficiaryLastname'];
   
               
                    $Lastname = $_POST['Lastname'];
                    $Firstname = $_POST['Firstname'];
                    $Middlename = $_POST['Middlename'];
                    $Birthday = $_POST['Birthday'];
                    $Contactnumber = $_POST['Contactnumber'];
                    $Province = $_POST['Province'];
                    $CityMunicipality = $_POST['CityMunicipality'];
                    $Barangay = $_POST['Barangay'];
                    $HousenoStreet = $_POST['HousenoStreet'];
                    $Email = $_POST['Email'];
                    $relationship = $_POST['relationship'];
                    $Representative_ID = $_POST['Representative_ID'];
                    $Lnamebeneficiary = $_POST['BeneficiaryLastname'];
                    $check_user = "SELECT * FROM beneficiary WHERE Email='$Email' LIMIT 1";
                    $result = mysqli_query($con, $check_user);
                    $user = mysqli_fetch_assoc($result);
                    $user_Cn = "SELECT * FROM beneficiary WHERE Contactnumber='$Contactnumber' LIMIT 1";
                    $results = mysqli_query($con, $user_Cn);
                    $user_phone = mysqli_fetch_assoc($results);
                  
                    date_default_timezone_set('Asia/Manila');
                    $Date = date('Y-m-d'); 
                    $TIME = date('H:i:s'); 

                    $minDate = new DateTime();
                    $maxDate = new DateTime();
                    $minDate->modify('-0 day');
$maxDate->modify('-150 years');
// Convert $Birthday to a DateTime object for comparison
$selectedDate = new DateTime($Birthday);
                  
                    if(empty($Lastname))
                    {
                      array_push($errors, $lastError = "Lastname is required");
                    }
                    elseif (!preg_match('/^[a-zA-Z ]*$/', $Lastname)) {
                     
                      array_push($errors, $lastError = "Only alphabetic characters and spaces are allowed.");
                  }
                     elseif ($Lastname == $_POST['BeneficiaryLastname']|| $Middlename == $_POST['BeneficiaryLastname']) {
  
}

else {
    array_push($errors, $lastError = "Only immediate family member can apply for assistance");
}

                    
                    if(empty($Firstname))
                    {
                      array_push($errors, $firstError = "Firstname is required");
                    }
                    elseif (!preg_match('/^[a-zA-Z ]*$/', $Firstname)) {
                     
                      array_push($errors, $firstError = "Only alphabetic characters and spaces are allowed.");
                  }
                    if(empty($Middlename))
                    {
                      array_push($errors, $middleError = "Middlename is required");
                    }
                    elseif (!preg_match('/^[a-zA-Z ]*$/', $Middlename)) {
                     
                      array_push($errors, $middleError = "Only alphabetic characters and spaces are allowed.");
                  }
                    if(empty($Birthday))
                    {
                      array_push($errors, $bdayError = "Birthday is required");
                    }
                      
                  elseif ($selectedDate > $minDate) {
                    array_push($errors, $bdayError = "The beneficiary must be at least one day old; <br> A birthdate today is acceptable.");

                }
                elseif ($selectedDate < $maxDate) {
                  array_push($errors, $bdayError = "You must be at least 100 years old");
              }
                
                  
                      
              if($relationship=="Select")
              {
                array_push($errors, $relationError = "Relationship is required");
              }
                    if(empty($Province))
                    {
                      array_push($errors, $provError = "Province is required");
                    }
                    if(empty($relationship))
                    {
                      array_push($errors, $relationError = "Province is required");
                    }
                    if($CityMunicipality=="Select")
                    {
                      array_push($errors, $cityError = "City/Municipality is required");
                    }
                    if($Barangay=="Select")
                    {
                      array_push($errors, $brgyError = "Barangay is required");
                    }
                    if(empty($HousenoStreet))
                    {
                      array_push($errors, $stError = "House No/Street is required");
                    }
                  
                    
                  
            
      if (empty($errors)) {


// Use the complete user ID in the INSERT query
$query ="INSERT INTO beneficiary( Lastname, Firstname, Middlename, Birthday, Contactnumber, Province, CityMunicipality, Barangay, HousenoStreet, Email, Representative_ID,Date,time,Relationship) VALUES ('$Lastname', '$Firstname', '$Middlename', '$Birthday', '$Contactnumber', '$Province', '$CityMunicipality', '$Barangay', '$HousenoStreet', '$Email', '$Representative_ID','$Date','$TIME','$relationship')";
if(mysqli_query($con, $query)){
  ?>
  <script>
     
      window.location.href = "applysched.php";
  </script>
  <?php
} else {
  // If the query fails, push the error message into the $errors array
  array_push($errors, mysqli_error($con));
}

  }
}
       ?>
           
         <header>Beneficiary Information</header>
            <form action="" method= "post">
                <div class="field input">
                       <input type="hidden" name="Representative_ID" value="<?php echo $res_Id; ?>">
                        <input type="hidden" name="BeneficiaryLastname" value="<?php echo $res_Lname; ?>">
             
                    <label for = "Lastname" style="font-size: 18px;">Last Name</label>
                    <input type="text" name="Lastname" id="Lastname" autocomplete="off" value="<?php echo $_POST['Lastname'] ?? ''; ?>">
                    <p style="color: rgb(150, 26, 26); font-size: 18px;"><?php echo $lastError ?></p>          
                </div>
                <div class="field input">
                    <label for = "Firstname" style="font-size: 18px;">First Name</label>
                    <input type="text" name="Firstname" id="Firstname" autocomplete="off" value="<?php echo $_POST['Firstname'] ?? ''; ?>">  
                    <p style="color: rgb(150, 26, 26); font-size: 18px;"><?php echo $firstError ?></p>                    
                </div>
                <div class="field input">
                    <label for = "Middlename" style="font-size: 18px;">Middle Name</label>
                    <input type="text" name="Middlename" id="Middlename" autocomplete="off" value="<?php echo $_POST['Middlename'] ?? ''; ?>">                  
                    <p style="color: rgb(150, 26, 26); font-size: 18px;"><?php echo $middleError ?></p> 
                  </div>
                <div class="field input">
                    <label for = "Birthday" style="font-size: 18px;">Birthday</label>
                    <input type="date" name="Birthday" id="Birthday" value="<?php echo $_POST['Birthday'] ?? ''; ?>"> 
                    <p style="color: rgb(150, 26, 26); font-size: 18px;"><?php echo $bdayError ?></p>                  
                </div>
             
                    <input type="hidden" name="Contactnumber" id="Contactnumber" autocomplete="off" value="<?php echo isset($Contactnumber) ? $Contactnumber : ''; ?>">                  
                    <p style="color: rgb(150, 26, 26); font-size: 18px;"><?php echo $numError ?></p>
               
                    


                <div class="field input">
                    <label for = "Province" style="font-size: 18px;">Province</label>
                    <input type="text" name="Province" id="Province" value="Bataan">                  
                  </div>
                  <div class="field input" style="display: flex; align-items: left;">
    <input type="checkbox" id="sameAddress" onclick="copyAddress()" 
           style="transform: scale(0.8); margin-left: -180px;  height:25px;   cursor: pointer;"> 
    <label for="sameAddress" style="font-size: 16px; font-style: italic; color: blue; cursor: pointer;">
        Same as Representative Address
    </label>
</div>
                <div class="field input">
                <?php
// Define variables to store selected city and barangay
$selectedCity = $_POST['CityMunicipality'] ?? 'Select';
$selectedBarangay = $_POST['Barangay'] ?? 'Select';
?>
                    <label for = "CityMunicipality" style="font-size: 18px;">City/Municipality</label>
                    <select id="cityDropdown" name="CityMunicipality" onchange="populateBarangays()">
                    <option value="Select" <?php if ($selectedCity === 'Select') echo 'selected'; ?>>Select</option>
                    <option value="Abucay" <?php if ($selectedCity === 'Abucay') echo 'selected'; ?>>Abucay</option>
                    <option value="Bagac" <?php if ($selectedCity === 'Bagac') echo 'selected'; ?>>Bagac</option>
                    <option value="Balanga" <?php if ($selectedCity === 'Balanga') echo 'selected'; ?>>Balanga</option>
                    <option value="Dinalupihan" <?php if ($selectedCity === 'Dinalupihan') echo 'selected'; ?>>Dinalupihan</option>
                    <option value="Hermosa" <?php if ($selectedCity === 'Hermosa') echo 'selected'; ?>>Hermosa</option>
                    <option value="Limay" <?php if ($selectedCity === 'Limay') echo 'selected'; ?>>Limay</option>
                    <option value="Mariveles" <?php if ($selectedCity === 'Mariveles') echo 'selected'; ?>>Mariveles</option>
                    <option value="Morong" <?php if ($selectedCity === 'Morong') echo 'selected'; ?>>Morong</option>
                    <option value="Orani" <?php if ($selectedCity === 'Orani') echo 'selected'; ?>>Orani</option>
                    <option value="Orion" <?php if ($selectedCity === 'Orion') echo 'selected'; ?>>Orion</option>
                    <option value="Pilar" <?php if ($selectedCity === 'Pilar') echo 'selected'; ?>>Pilar</option>
                    <option value="Samal" <?php if ($selectedCity === 'Samal') echo 'selected'; ?>>Samal</option>

                      </select>  
                      <p style="color: rgb(150, 26, 26); font-size: 18px;"><?php echo $cityError ?></p>
                             
                </div>

                <div class="field input">
                <input type="hidden" name="serviceType" value="<?php echo htmlspecialchars($serviceType); ?>">

                    <label for="Barangay" style="font-size: 18px;">Barangay</label>
                    <select id="barangayDropdown" name="Barangay" value="Select">
                        <option value="Select">Select</option> 
                    </select>
                  

                    <input type="hidden" id="selectedBarangay" name="selectedBarangay" value="<?php echo htmlspecialchars($selectedBarangay); ?>">

                    <p style="color: rgb(150, 26, 26); font-size: 18px;"><?php echo $brgyError ?></p>
                </div>
                
                <script>
                function populateBarangays() {
                    var cityDropdown = document.getElementById("cityDropdown");
                    var barangayDropdown = document.getElementById("barangayDropdown");
                    var selectedCity = cityDropdown.value;
                    var selectedBarangay = document.getElementById("selectedBarangay").value; // Retrieve selected barangay value


                    var barangays = [];
                
                    // Clear existing options
                    barangayDropdown.innerHTML = "";
                    if (selectedCity === "Select") {
                      barangays = ["Select"]
                    }

                    if (selectedCity === "Balanga") {
                        barangays = ["Select","Bagong Silang","Bagumbayan", "Cabog-Cabog","Camacho", "Cataning","Central", "Cupang North", "Cupang Proper", "Cupang West","Dangcol","Doña Francisca", "Ibayo","Lote", "Malabia","Munting Batangas","Poblacion", "Pto. Rivas Ibaba", "Pto. Rivas Itaas","San Jose", "Sibacan", "Talisay","Tanato", "Tenejero", "Tortugas","Tuyo"]; // Add barangays for Balanga
                    } 
                    if (selectedCity === "Abucay") {
                         barangays = ["Select","Bangkal","Calaylayan", "Capitangan","Gabon", "Laon","Mabatang", "Omboy", "Salian", "Wawa"]; // Add barangays for Balanga
                   
                    }
                    if (selectedCity === "Bagac") {
                              barangays = ["Select","Atilano L. Ricardo","Bagumbayan", "Banawang","Binukawan", "Ibaba","Ibis", "Pag-asa", "Parang", "Paysawan","Quinawan", "San Antonio", "Saysain", "Tabing-Ilog"]; // Add barangays for Balanga
                    }
                    if (selectedCity === "Dinalupihan") {
                              barangays = ["Select","Aquino","Bangal", "Bayan-bayanan","Bonifacio", "Burgos","Colo", "Daang Bago", "Dalao", "Del Pilar","Gen. Luna", "Gomez", "Happy Valley", "Jose C. Payumo, Jr.","Kataasan","Layac","Luacan","Mabini Ext.","Mabini Proper","Magsaysay","Maligaya","Naparing","New San Jose","Old San Jose","Padre Dandan","Pagalanggang","Pag-asa","Payangan","Pentor","Pinulot","Pita","Rizal","Roosevelt","Roxas","Saguing","San Benito","San Isidro","San Pablo","San Ramon","San Simon","Santa Isabel","Santo Niño","Sapang Balas","Torres Bugauen","Tubo-tubo","Tucop","Zamora"]; // Add barangays for Balanga
                    }
                    if (selectedCity === "Hermosa") {
                     barangays = ["Select","A. Rivera", "Almacen", "Bacong", "Balsic", "Bamban", "Burgos-Soliman", "Cataning", "Culis", "Daungan", "Judge Roman Cruz Sr.", "Mabiga", "Mabuco", "Maite", "Mambog-Mandama", "Palihan", "Pandatung", "Pulo", "Saba", "Sacrifice Valley", "San Pedro", "Santo Cristo", "Sumalo", "Tipo"];
                    }
                    if (selectedCity === "Limay") {
                    barangays = ["Select","Alangan","Duale","Kitang 2 & Luz","Kitang I","Lamao","Landing","Poblacion","Reformista","Saint Francis II","San Francisco de Asis","Townsite","Wawa" ];
                    }
                    if (selectedCity === "Mariveles") {
                    barangays = ["Select","Alas-asin", "Alion", "Alon-Anito", "Baseco Country", "Batangas II", "Biaan", "Cabcaben", "Camaya", "Ipag", "Lucanin", "Malaya", "Maligaya", "Mt. View", "Poblacion", "San Carlos", "San Isidro", "Sisiman", "Townsite"];
                    }
                    if (selectedCity === "Morong") {
                     barangays = ["Select","Binaritan", "Mabayo", "Nagbalayong", "Poblacion", "Sabang"];
                    }
                    if (selectedCity === "Orani") {
                    barangays = ["Select","Apollo", "Bagong Paraiso", "Balut", "Bayan", "Calero", "Centro I", "Centro II", "Dona", "Kabalutan", "Kaparangan", "Maria Fe", "Masantol", "Mulawin", "Pag-asa", "Paking-Carbonero", "Palihan", "Pantalan Bago", "Pantalan Luma", "Parang Parang", "Puksuan", "Sibul", "Silahis", "Tagumpay", "Tala", "Talimundoc", "Tapulao", "Tenejero", "Tugatog", "Wawa"];
                    }
                    if (selectedCity === "Orion") {
                    barangays = ["Select","Arellano", "Bagumbayan"," Balagtas", "Balut", "Bantan", "Bilolo","Calungusan", "Camachile", "Daang Bago", "Daang Bilolo", "Daang Pare", "General Lim", "Kapunitan", "Lati", "Lusungan", "Puting Buhangin", "Sabatan", "San Vicente", "Santa Elena", "Santo Domingo", "Villa Angeles", "Wakas", "Wawa"];
                    }
                    if (selectedCity === "Pilar") {
                    barangays = ["Select","Ala-uli", "Bagumbayan", "Balut I", "Balut II", "Bantan Munti", "Burgos", "Del Rosario", "Diwa", "Landing", "Liyang", "Nagwaling", "Panilao", "Pantingan", "Poblacion", "Rizal", "Santa Rosa", "Wakas North", "Wakas South", "Wawa"];
                    }
                    if (selectedCity === "Samal") {
                    barangays = ["Select","East Calaguiman", "East Daang Bago", "Gugo", "Ibaba", "Imelda", "Lalawigan", "Palili", "San Juan", "San Roque", "Santa Lucia", "Sapa", "Tabing Ilog", "West Calaguiman", "West Daang Bago"];
                    }
                    // Populate barangay dropdown
                    barangays.forEach(function(Barangay) {
                        var option = document.createElement("option");
                        option.text = Barangay;
                        option.value = Barangay;
                        if (Barangay === selectedBarangay) { // Check if the barangay matches the selected barangay value
                option.selected = true; // Pre-select the option
            }
                        barangayDropdown.add(option);

                      });
                        
                        // Set the name attribute of the select element to Barangay
barangayDropdown.setAttribute("name", "Barangay");
                    };
           
               
    
    window.onload = function () {
        populateBarangays();
    };

              
                </script>
                <div class="field input">
                    <label for = "HousenoStreet" style="font-size: 18px;">House No /Street</label>
                    <input type="text" name="HousenoStreet" id="HousenoStreet" autocomplete="off" value="<?php echo $_POST['HousenoStreet'] ?? ''; ?>">                  
                    <p style="color: rgb(150, 26, 26); font-size: 18px;"><?php echo $stError ?></p>
                  </div>
               
                     <input type="hidden" name="Email" id="Email" autocomplete="off" value="<?php echo isset($res_Email) ? $res_Email : ''; ?>">                  
                    <p style="color: rgb(150, 26, 26); font-size: 18px;"><?php echo $emailError ?></p>
                

                  <div class="field input">
                    <label for = "Contactnumber" style="font-size: 18px;">Representative Name</label>
                    <input type="text" disabled name="repname" id="Contactnumber" autocomplete="off" value="<?php echo isset($res_Fname) ? $res_Fname . ' ' . $res_Lname : ''; ?>">                  
                    
                  </div>
                
                  <div class="field input" style="margin-top:20px;">
                <?php
// Define variables to store selected city and barangay
$relationshipselect = $_POST['relationship'] ?? 'Select';
$selectedBarangay = $_POST['Barangay'] ?? 'Select';
?>
                    <label for = "CityMunicipality" style="font-size: 18px;">Relationship to Beneficiary</label>
                    <select id="relationDropdown" name="relationship" style="height:40px;">
                    <option value="Select" <?php if ($relationshipselect === 'Select') echo 'selected'; ?>>Select</option>
                    <option value="Mother" <?php if ($relationshipselect === 'Mother') echo 'selected'; ?>>Mother</option>
                    <option value="Father" <?php if ($relationshipselect === 'Father') echo 'selected'; ?>>Father</option>
                    <option value="Sibling" <?php if ($relationshipselect === 'Sibling') echo 'selected'; ?>>Sibling</option>
                    <option value="Spouse" <?php if ($relationshipselect=== 'Spouse') echo 'selected'; ?>>Spouse</option>
                   
                   
                      </select>  
                      <p style="color: rgb(150, 26, 26); font-size: 18px;"><?php echo $relationError ?></p>
                             
                </div>
              
                   <div class="field">
                    <input type="submit" class="btn" name="submit" value="Apply for Assistance">                  
                </div>

       
               
            </form>
            <center>   
            <a href="applyingoptions.php" style="color:rgb(99, 95, 95); text-decoration: none; display: inline-flex; align-items: center;">
        <img src="images/back.png" style="height: 15px; width: 20px; margin-right: 6px;" />
        Back to home
    </a>  </div>
        </div>
       
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  <script>
    function copyAddress() {
    const isChecked = document.getElementById('sameAddress').checked;
    const repProvince = 'Bataan'; // Replace with dynamic value if needed
    const repCity = '<?php echo isset($city) ? $city : ''; ?>';
    const repBarangay = '<?php echo isset($brgy) ? $brgy : ''; ?>';
    const repHouseNo = '<?php echo isset($street) ? $street : ''; ?>';

    if (isChecked) {
        document.getElementById('Province').value = repProvince;
        document.getElementById('cityDropdown').value = repCity;
        populateBarangays(); // Populate barangay dropdown based on selected city
        document.getElementById('barangayDropdown').value = repBarangay;
        document.getElementById('HousenoStreet').value = repHouseNo;
    } else {
        document.getElementById('Province').value = '';
        document.getElementById('cityDropdown').value = 'Select';
        populateBarangays(); // Reset barangay options
        document.getElementById('barangayDropdown').value = 'Select';
        document.getElementById('HousenoStreet').value = '';
    }
}
</script>
  </body>
</html>
