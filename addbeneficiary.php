<?php
  session_start();
include("php/config.php");

  if(isset($_SESSION['Emp_ID'])){
        $id = $_SESSION['Emp_ID'];
        $query = mysqli_query($con, "SELECT * FROM employees WHERE Emp_ID=$id");

if($result = mysqli_fetch_assoc($query)){
$res_Id = $result['Emp_ID'];
$res_Fname = $result['Firstname'];
 $res_Lname = $result['Lastname'];
 $role=$result['role'];
}
  }
  else{
    
    header("Location: employee-login.php");
}
if(isset($_POST['submit'])) {
// Check if the user confirmed the update
if(isset($_POST['confirmed']) && $_POST['confirmed'] === "yes") {
$Date=$_POST['Date'];
$time=$_POST['time'];
$Lastname=$_POST['Lastname'];
$Firstname=$_POST['Firstname'];
$Middlename =$_POST ['Middlename'];
$Birthday =$_POST ['Birthday'];
$Contactnumber =$_POST ['Contactnumber'];
$CityMunicipality =$_POST ['CityMunicipality'];
$Barangay =$_POST ['Barangay'];
$HousenoStreet =$_POST ['HouseNoStreet'];
$Email =$_POST ['Email'];
$Province = "Bataan";

// Use the complete user ID in the INSERT query
$query ="INSERT INTO beneficiary( Lastname, Firstname, Middlename, Birthday,
Contactnumber, Province, CityMunicipality, Barangay, HousenoStreet,
Email,time,Date,Representative_ID) VALUES ('$Lastname', '$Firstname',
'$Middlename', '$Birthday', '$Contactnumber', '$Province', '$CityMunicipality',
'$Barangay', '$HousenoStreet', '$Email','$time', '$Date',NULL)";
$result2=mysqli_query($con,$query);
if ($result2) {
header("Location: patients-records.php");
exit();
} else {
echo "Error adding records: " . mysqli_error($con);
header("Location: patients-records.php");
exit();
}
}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Add Form</title>
<link rel="stylesheet" href="addbeneficiary.css" />
</head>
<body>
<div class="container">
<div class="title"> Add beneficiary </div>
<form id="addForm" method="post">
<div class="user-details">
<div class="input-box">
<span class="details"> Date </span>
<input type="date" id="calendar" name="Date" />
<input type="hidden" name="Beneficiary_Id" disabled/>
</div>
<div class="input-box">
<span class="details"> Time </span>
<input type="text" id="time" required name="time" />
</div>
<div class="user-details">
<div class="input-box">
<span class="details"> Last Name </span>
<input type="text" required name="Lastname" autocomplete=off />
</div>
<div class="input-box">
<span class="details"> First Name </span>
<input type="text" required name="Firstname" autocomplete=off />
</div>
<div class="user-details">
<div class="input-box">
<span class="details"> Middle Name </span>
<input type="text" required name="Middlename" autocomplete=off/>
</div>
<div class="input-box">
<span class="details"> Birthday </span>
<input type="date" id="calendar" name="Birthday" required />
</div>
<div class="user-details">
<div class="input-box">
<span class="details"> Contact Number</span>
<input type="text" required name="Contactnumber" autocomplete=off
/>  </div>
<div class="input-box">
                <?php
// Define variables to store selected city and barangay
$selectedCity = $_POST['CityMunicipality'] ?? 'Select';
$selectedBarangay = $_POST['Barangay'] ?? 'Select';
?>
                    <span class="details">City/Municipality</span>
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
                             
                </div>
                
                <div class="input-box">
               
                <span class="details">Barangay</span>
                    <select id="barangayDropdown" name="Barangay" value="Select">
                        <option value="Select">Select</option> 
                    </select>
                  

                    <input type="hidden" id="selectedBarangay" name="selectedBarangay" value="<?php echo htmlspecialchars($selectedBarangay); ?>">

                    
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
                
<div class="input-box">
<span class="details"> House No/ Street </span>
<input type="text" required name="HouseNoStreet" autocomplete=off
/>
</div>
<div class="user-details">
<div class="input-box">
<span class="details"> Email </span>
<input type="text" required name="Email" autocomplete=off />
</div>
</div>
<br> <input type="hidden" name="confirmed" id="confirmed"
value="no">
<div class="button-row">
<!-- Submit button -->
<input type="submit" value="Add" name="submit" onclick="showConfirmation()" />
<!-- Cancel button -->
<input type="button" value="Cancel" name="cancel" onclick="cancelEdit()" />
</div>
</div>
</form>
</div>
<script type="text/javascript">
function cancelEdit() {
// Redirect to the previous page
window.history.back();
}
function showConfirmation() {
var confirmation = confirm("Are you sure you want to add?");
if (confirmation) {
// If user clicks OK, submit the form
document.getElementById("confirmed").value = "yes";
}
else {
document.getElementById("confirmed").value = "no"; }
}
document.addEventListener("DOMContentLoaded", function() {
var now = new Date();
var hours = now.getHours();
var minutes = now.getMinutes();
var seconds = now.getSeconds();
// Add a leading zero to single-digit minutes and seconds
minutes = minutes < 10 ? '0' + minutes : minutes;
seconds = seconds < 10 ? '0' + seconds : seconds;
var currentTime = hours + ':' + minutes + ':' + seconds;
document.getElementById('time').value = currentTime;
});
</script>
</body></html>
