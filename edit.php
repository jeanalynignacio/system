<?php 
    session_start();

    include("php/config.php");
      if(!isset($_SESSION['valid'])){
        header("Location: index.php");

    }
    if(isset($_SESSION['valid'])){
  $id = $_SESSION['id'];
            $query = mysqli_query($con, "SELECT * FROM users WHERE Id=$id");

            while($result = mysqli_fetch_assoc($query)){
                $res_Lname = $result['Lastname'];
                $res_Fname = $result['Firstname'];
                $res_Mname = $result['Middlename'];
                $res_Birthday = $result['Birthday'];
                $res_Cnumber = $result['Contactnumber'];
                $res_Province = $result['Province'];
                $res_CityMunicipality = $result['CityMunicipality'];
                $res_Barangay = $result['Barangay'];
                $res_HousenoStreet = $result['HousenoStreet'];
                $res_Email = $result['Email'];
                $res_Uname = $result['Username'];
                $res_Password = $result['Password'];
                $res_Id = $result['Id'];
            }

    }
    if (isset($_POST['userId'])) {
        $Id = $_POST['userId'];
             
                  // Proceed with processing using $Id
    } else {
        echo "User ID is not set.";
    }
    
  
  $SQL="SELECT * FROM users WHERE Id='$Id'";
  $errors = []; // Initialize $errors as an empty array

  $result=mysqli_query($con,$SQL);
   $res_ID= $result->fetch_assoc();
   $passError = "";
   $Errors = ""; 
             if(isset($_POST['update'])) {
    
    if(isset($_POST['confirmed']) && $_POST['confirmed'] === "yes") {
        $userID=$_POST['userId'];
        $Lastname=$_POST['Lastname'];
        $Firstname=$_POST['Firstname'];
        $Middlename=$_POST['Middlename'];
        $Birthday=$_POST['Birthday'];
        $Contactnumber=$_POST['Contactnumber'];
      
        $CityMunicipality=$_POST['CityMunicipality'];
        $Barangay=$_POST['Barangay'];
        $HousenoStreet=$_POST['HousenoStreet'];
      
        $Password=$_POST['Password'];


        if(empty($Password))
        {
            array_push($errors, $passError = "Password is required");
        } elseif (!preg_match('/^(?=.*\d)(?=.*[A-Z])(?=.*[a-z])(?=.*[^A-Za-z0-9]).{8,}$/', $Password)) {
            array_push($errors, $passError = "Password must be at least 8 characters long and contain at least one number, one uppercase letter, one lowercase letter, and one special character.");
        }

        if (empty($errors)) {
        $query = "UPDATE users SET Lastname='$Lastname', Firstname='$Firstname', Middlename='$Middlename', Birthday='$Birthday', Contactnumber='$Contactnumber',  CityMunicipality='$CityMunicipality', Barangay='$Barangay', HousenoStreet='$HousenoStreet', Password='$Password' WHERE Id='$userID'";

        $result2=mysqli_query($con,$query);

        if($result2){  
            echo '<body>
            <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
            <script>
                swal({
                    title: "Information updated successfully",
                    icon: "success",
                    button: "Okay"
                }).then(function() {
                    window.location.href="usershomepage.php";
                });
            </script>
        </body>';
        
        } else {
            echo '<body>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>
        swal({
            title: "Update error",
            icon: "error",
            button: "Okay"
        }).then(function() {
            window.location.href="usershomepage.php";
        });
    </script>
</body>';

        }
    } 
}
             }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
   
    <link rel="stylesheet" href="editstyle.css">
    <title>Change Profile</title>
</head>
<body>
<div class = "main">
        <div class = "page">
            <div class = "icon">
                <h2 class = "logo"> 1Bataan Malasakit - Special Assistance Program </h2>
            </div>

        
    <div class = "container">

            <header>Change Profile </header>
         
          
            <form action="#" class="form" method= "POST" onload="populateBarangays()" >
            <div class="field input">
                    
                <div class = "column">
                    <input type="hidden" name="userId" value="<?php echo "{$res_ID['Id']}"; ?>">                  
                <div class="field input">
                    <label for = "Lastname">Last Name</label>
                    <input type="text" name="Lastname" id="Lastname"  autocomplete="off" value="<?php echo "{$res_ID['Lastname']}"; ?>"  required>                      
                </div>
                <div class="field input">
                    <label for = "Firstname">First Name</label>
                    <input type="text" name="Firstname" id="Firstname" autocomplete="off"value="<?php echo "{$res_ID['Firstname']}"; ?>" required>                   
                </div>
                <div class="field input">
                    <label for = "Middlename">Middle Name</label>
                    <input type="text" name="Middlename" id="Middlename" autocomplete="off" value="<?php echo "{$res_ID['Middlename']}"; ?>" required>                 
                </div>
                </div>
                <div class = "column">
                <div class="field input">
                    <label for = "Birthday">Birthday</label>
                    <input type="date" name="Birthday" id="Birthday"value="<?php echo "{$res_ID['Birthday']}"; ?>" required> 
                </div>
                <div class="field input">
                    <label for = "Contactnumber">Contact Number</label>
                    <input type="text" name="Contactnumber" id="Contactnumber" autocomplete="off"value="<?php echo "{$res_ID['Contactnumber']}"; ?>" required>
                </div>
                <div class="field input">
                    <label for = "Province">Province</label>
                    <input type="text" disabled name="Province" id="Province" value="Bataan"value="<?php echo "{$res_ID['Middlename']}"; ?>" required>
                </div>
                </div>
                <div class = "column">
                <div class="field input">
                    <label for = "HousenoStreet">House No /Street</label>
                    <input type="text" name="HousenoStreet" id="HousenoStreet" autocomplete="off"value="<?php echo "{$res_ID['HousenoStreet']}"; ?>" required>  
                </div>
                <div class="field input">
                    <label for = "CityMunicipality">City/Municipality</label>
                    <select id="cityDropdown"  name="CityMunicipality" onchange="populateBarangays()" required>
                    
                    <?php
        
                  $selectedValue = $res_ID['CityMunicipality'];
                  $cityOptions = array("Select", "Abucay", "Bagac", "Balanga", "Dinalupihan", "Hermosa", "Limay", "Mariveles", "Morong", "Orani", "Orion", "Pilar", "Samal");
                   foreach ($cityOptions as $option) {
                   if ($option == $selectedValue) {
                 $selected = 'selected';
                  } else {
                $selected = '';
                 }
                    echo "<option value='" . htmlspecialchars($option) . "' $selected>" . htmlspecialchars($option) . "</option>";
                  }
                
                 ?>
                      </select>  
                      
                  
            
                             
                </div>
                <div class="field input">
                    <label for="Barangay">Barangay</label>
                    <select id="barangayDropdown" required>
                        
                    <?php

    $selectedBrgy = $res_ID['Barangay'];
   
    foreach ($cityOptions as $option) {
        if ($option == $selectedBrgy) {
            $selected = 'selected';
        } else {
            $selected = '';
        }
        echo "<option value='" . htmlspecialchars($option) . "' $selected>" . htmlspecialchars($option) . "</option>";
    }
    ?>
</select>
 
                    
                </div>
               
                <script>
                    window.onload = function() {
                     populateBarangays();
                    }
                function populateBarangays() {
                    var cityDropdown = document.getElementById("cityDropdown");
                    var barangayDropdown = document.getElementById("barangayDropdown");
                    var selectedCity = cityDropdown.value;
                    var barangays = [];
                
                    // Clear existing options
                    barangayDropdown.innerHTML = "";

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
                    
                    barangays.forEach(function(Barangay) {
                        var option = document.createElement("option");
                        option.text = Barangay;
                        option.value = Barangay;
                        barangayDropdown.add(option);

                        var selectedbrgy = "<?php echo "{$res_ID['Barangay']}"; ?>";
        barangayDropdown.value = selectedbrgy; 

                    barangayDropdown.setAttribute("name", "Barangay");
                    });
           
                }
              
                </script>
                </div>
                <div class = "column">
                <div class="field input">
                    <label for = "Email">Email</label>
                    <input type="text" name="Email" id="Email" autocomplete="off"value="<?php echo "{$res_ID['Email']}"; ?>" disabled>       
                </div>
                <div class="field input">
                    <label for = "Username">Username</label>
                    <input type="text" disabled name="Username" id="Username" autocomplete="off"value="<?php echo "{$res_ID['Username']}"; ?>" required>  
                </div>
                <div class="field input">
                    <label for = "Password">Password</label>
                    <input type="password" name="Password" id="Password" autocomplete="off"value="<?php echo "{$res_ID['Password']}"; ?>" required>
              <span class="fas fa-eye toggle-password" onclick="togglePasswordVisibility(this)"></span>
                    <p style="color:  rgb(146, 16, 16); font-size: 18px;"><?php echo $passError ?></p>                    
              
                </div>
                </div>
                <input type="hidden" name="confirmed" id="confirmed" value="no">
                <div class="button-row">
                <input type="button" class="cancelbtn"  name="update" value="Cancel" id="cancel" onclick="cancelEdit()">
          
                <input type="submit" class="btn"  name="update" value="Update" onclick="showConfirmation()" required>
               
            </form>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
   
    <script>
  function cancelEdit() {
            // Redirect to the previous page <script type="text/javascript">
            window.history.back();
        }

    function validateForm() {
    // Perform form validation here
    var isValid = true;
    // Example validation: Check if required fields are filled
    var requiredFields = document.querySelectorAll('input[required], select[required]');
    requiredFields.forEach(function(field) {
        if (field.value.trim() === '') {
            isValid = false;
        }
    });
    return isValid;
}


function showConfirmation() {
    if (!validateForm()) {
        // If form validation fails, don't proceed to confirmation
        alert("Please fill in all required fields.");
        return false; // Prevent form submission
    } else {
        var confirmation = confirm("Are you sure you want to update?");
        if (confirmation) {
            document.getElementById("confirmed").value = "yes";
            return true; // Allow form submission
        } else {
            document.getElementById("confirmed").value = "no";
            return false; // Prevent form submission
        }
    }
}
function togglePasswordVisibility(icon) {
    var passwordInput = icon.previousElementSibling;
    if (passwordInput.type === "password") {
        passwordInput.type = "text";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
    } else {
        passwordInput.type = "password";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
    }
}




</script>
</body>
</html>
