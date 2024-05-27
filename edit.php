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

  $result=mysqli_query($con,$SQL);
   $res_ID= $result->fetch_assoc();
   
                
             if(isset($_POST['update'])) {
    
    if(isset($_POST['confirmed']) && $_POST['confirmed'] === "yes") {
        $userID=$_POST['userId'];
        $Lastname=$_POST['Lastname'];
        $Firstname=$_POST['Firstname'];
        $Middlename=$_POST['Middlename'];
        $Birthday=$_POST['Birthday'];
        $Contactnumber=$_POST['Contactnumber'];
        $Province=$_POST['Province'];
        $CityMunicipality=$_POST['CityMunicipality'];
        $Barangay=$_POST['Barangay'];
        $HousenoStreet=$_POST['HousenoStreet'];
        $Email=$_POST['Email'];
        $Username=$_POST['Username'];
        $Password=$_POST['Password'];

        $query = "UPDATE users SET Lastname='$Lastname', Firstname='$Firstname', Middlename='$Middlename', Birthday='$Birthday', Contactnumber='$Contactnumber', Province='$Province', CityMunicipality='$CityMunicipality', Barangay='$Barangay', HousenoStreet='$HousenoStreet', Email='$Email', Username='$Username', Password='$Password' WHERE Id='$userID'";

        $result2=mysqli_query($con,$query);

        if($result2){  
            $_SESSION['status']="Information updated successfully!";
            header("Location: success.php");
            exit(); // Make sure to stop executing the script after redirecting
        } else {
            $_SESSION['status']="Update error!" . mysqli_error($con);
            header("Location: success.php");
            exit(); // Make sure to stop executing the script after redirecting
        }
    } 
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        <div class="box form-box">

            <header>Change Profile</header>
         
          
            <form action="#" method= "POST" onload="populateBarangays() " >
            <div class="field input">
                    
                    
                    <input type="hidden" name="userId" value="<?php echo "{$res_ID['Id']}"; ?>">                  
                </div>
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
                    <input type="text" name="Province" id="Province" value="Bataan"value="<?php echo "{$res_ID['Middlename']}"; ?>" required>
                 
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


                <div class="field input">

                    <label for = "HousenoStreet">House No /Street</label>
                    <input type="text" name="HousenoStreet" id="HousenoStreet" autocomplete="off"value="<?php echo "{$res_ID['HousenoStreet']}"; ?>" required>  
                    
               
                </div>
                <div class="field input">
                    <label for = "Email">Email</label>
                    <input type="text" name="Email" id="Email" autocomplete="off"value="<?php echo "{$res_ID['Email']}"; ?>">       
                 
                </div>
                <div class="field input">
                    <label for = "Username">Username</label>
                    <input type="text" name="Username" id="Username" autocomplete="off"value="<?php echo "{$res_ID['Username']}"; ?>" required>  
                   
                 
                </div>
                <div class="field input">
                    <label for = "Password">Password</label>
                    <input type="password" name="Password" id="Password" autocomplete="off"value="<?php echo "{$res_ID['Password']}"; ?>" required>           
                     
                </div>

                <input type="hidden" name="confirmed" id="confirmed" value="no">
                <div class="field">
                <input type="submit" class="btn"  name="update" value="Update" onclick="showConfirmation()" required>
            </form>
        </div>
    </div>
    
    <script>
  

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


</script>
</body>
</html>