<?php
session_start();
include("php/config.php");
// Check if Beneficiary_Id is set in the URL parameter
if(isset($_POST['Beneficiary_Id'])) {
$beneID = $_POST['Beneficiary_Id'];
} else {
echo "User ID is not set.";
exit();
}
$SQL = "SELECT * FROM beneficiary WHERE Beneficiary_Id = '$beneID'";
$result = mysqli_query($con, $SQL);
$res_data = array();
while($row = mysqli_fetch_assoc($result)) {
$res_data[] = $row;
}
if(isset($_POST['submit'])) {
if(isset($_POST['confirmed']) && $_POST['confirmed'] === "yes") {

$Lastname = $_POST['Lastname'];
$Firstname = $_POST['Firstname'];
$Middlename = $_POST['Middlename'];
$Birthday = $_POST['Birthday'];
$Contactnumber = $_POST['Contactnumber'];
$CityMunicipality = $_POST['CityMunicipality'];
$Barangay = $_POST['Barangay'];
$HousenoStreet = $_POST['HouseNoStreet'];

$Province = "Bataan";
date_default_timezone_set('Asia/Manila');
$currentDateTime = date('Y-m-d H:i:s');
$query = "UPDATE beneficiary SET
Lastname = '$Lastname',
Firstname = '$Firstname',
Middlename = '$Middlename',
Birthday = '$Birthday',
Contactnumber = '$Contactnumber',
Province = '$Province',
CityMunicipality = '$CityMunicipality',
Barangay = '$Barangay',
HousenoStreet = '$HousenoStreet',
Representative_ID = NULL
WHERE Beneficiary_Id = '$beneID'";
$result2 = mysqli_query($con, $query);
if ($result2) {
echo '<script>
alert("Record Saved successfully");
window.location.href = "patients-records.php";
</script>';
exit();
} else {
echo "Error updating records: " . mysqli_error($con);
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
<title>Edit Form</title>
<link rel="stylesheet" href="editpatientrecords.css">
</head>
<body>
<div class="container">
<div class="title">Edit form</div>
<form id="editForm" method="post">
<input type="hidden" name="Beneficiary_Id" value="<?php echo $beneID;
?>">
<?php foreach($res_data as $record): ?>
<div class="user-details">
<div class="input-box">
<span class="details">Date</span>
<input type="date" disabled id="calendar" name="Date" required
value="<?php echo $record['Date']; ?>">
</div>
<div class="input-box">
<span class="details">Time</span>
<input type="text" id="time" required disabled value="<?php echo date('h:i A', strtotime($record['time'])); ?>" name="time">

</div>
<div class="input-box">
<span class="details">Last Name</span>
<input type="text" required value="<?php echo
$record['Lastname']; ?>" name="Lastname" autocomplete="off">
</div>
<div class="input-box">
<span class="details">First Name</span>
<input type="text" required value="<?php echo
$record['Firstname']; ?>" name="Firstname" autocomplete="off">
</div>
<div class="input-box">
<span class="details">Middle Name</span>
<input type="text" required value="<?php echo
$record['Middlename']; ?>" name="Middlename" autocomplete="off">
</div>
<div class="input-box">
<span class="details">Birthday</span>
<input type="date" id="calendar" name="Birthday" required
value="<?php echo $record['Birthday']; ?>">
</div>
<div class="input-box">
<span class="details">Contact Number</span>
<input type="text" required value="<?php echo
$record['Contactnumber']; ?>" name="Contactnumber" autocomplete="off">
</div>
<div class="input-box">
                        <span class="details">City/Municipality</span>
                        <select id="cityDropdown" name="CityMunicipality" onchange="populateBarangays()">
                            <option value="Select">Select</option>
                            <?php
                            $cityOptions = array("Select", "Abucay", "Bagac", "Balanga", "Dinalupihan", "Hermosa", "Limay", "Mariveles", "Morong", "Orani", "Orion", "Pilar", "Samal");
                            $selectedCity = $record['CityMunicipality'];
                            foreach ($cityOptions as $option) {
                                $selected = ($option == $selectedCity) ? 'selected' : '';
                                echo "<option value='$option' $selected>$option</option>";
                            }
                            ?>
                        </select>
                             
                </div>
                
                <div class="input-box">
               
                <span class="details">Barangay</span>
                    <select id="barangayDropdown" name="Barangay" value="Select">
                        <option value="Select">Select</option> 
                    
                    
                        <?php

$selectedBarangay = $record['Barangay'];

foreach ($cityOptions as $option) {
    if ($option == $selectedBarangay) {
        $selected = 'selected';
    } else {
        $selected = '';
    }
    echo "<option value='" . htmlspecialchars($option) . "' $selected>" . htmlspecialchars($option) . "</option>";
}
?>
                    
                    </select>
                  

                    <input type="hidden" id="selectedBarangay" name="selectedBarangay" value="<?php echo htmlspecialchars($selectedBarangay); ?>">

                    
                </div>


                
                <script>
                       window.onload = function() {
                     populateBarangays();
                    }
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
<span class="details">House No/Street</span>
<input type="text" required value="<?php echo
$record['HousenoStreet']; ?>" name="HouseNoStreet" autocomplete="off">
</div>
<div class="input-box">
<span class="details">Email</span>
<input type="text" required disabled value="<?php echo
$record['Email']; ?>" name="Email" autocomplete="off">
</div>
</div>
<?php endforeach; ?>
<input type="hidden" name="confirmed" id="confirmed" value="no">
<div class="button-row">
<input type="submit" value="Save" name="submit"
onclick="showConfirmation()">
<input type="button" value="Cancel" name="cancel"
onclick="cancelEdit()">
</div>
</form>
</div>
<script type="text/javascript">
function cancelEdit() {
window.history.back();
}
function showConfirmation() {
var confirmation = confirm("Are you sure you want to update?");
if (confirmation) {
document.getElementById("confirmed").value = "yes";
} else {
document.getElementById("confirmed").value = "no";
}
}
</script>
</body>
</html>
