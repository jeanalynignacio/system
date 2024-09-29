<?php
session_start();
if (isset($_POST['Beneficiary_Id'])) {
    $beneficiaryId = $_POST['Beneficiary_Id'];

    // Load the XML file
    $xml = simplexml_load_file('beneficiary_records.xml') or die('Error: Cannot load XML file.');

    // Find the beneficiary with the specified ID
    $beneficiary = null;
    foreach ($xml->beneficiary as $item) {
        if ((string)$item->Beneficiary_Id === $beneficiaryId) {
            $beneficiary = $item;
            break;
        }
    }

    if ($beneficiary) {
        // Pre-fill the form with existing data
        $dateTime = (string)$beneficiary->Date; // Assume this includes both date and time
        $date = date('Y-m-d', strtotime($dateTime)); // Format date
        $time24 = date('H:i:s', strtotime($dateTime)); // Extract time in 24-hour format
    
        // Convert time to AM/PM format
        $time12 = date('h:i A', strtotime($time24));
    
     

        $Date = (string)$beneficiary->Date;
        $Time12 = (string)$beneficiary->time;
        $lastname = (string)$beneficiary->Lastname;
        $firstname = (string)$beneficiary->Firstname;
        $birthday = (string)$beneficiary->Birthday;
        $contactnumber = (string)$beneficiary->Contactnumber;
        $cityMunicipality = (string)$beneficiary->CityMunicipality;
        $barangay = (string)$beneficiary->Barangay;
        $email = (string)$beneficiary->Email;
        $middlename = (string)$beneficiary->Middlename;
        $housenum = (string)$beneficiary->HousenoStreet;
    } else {
        die('Beneficiary not found.');
    }
} else {
    die('No Beneficiary ID specified.');
}

if(isset($_POST['submit'])) {
    if(isset($_POST['confirmed']) && $_POST['confirmed'] === "yes") {
    
$beneficiaryIdToUpdate = $_POST['Beneficiary_Id'];

// Load the XML file
$xml = simplexml_load_file('beneficiary_records.xml') or die('Error: Cannot load XML file.');

// Find and update the beneficiary with the specified ID
$found = false;
foreach ($xml->beneficiary as $beneficiary) {
    if ((string)$beneficiary->Beneficiary_Id === $beneficiaryIdToUpdate) {
        date_default_timezone_set('Asia/Manila');
        $currentDate = date('Y-m-d'); // Format: YYYY-MM-DD
$currentTime24 = date('H:i:s'); // 24-hour format

// Convert time to AM/PM format
$currentTime12 = date('h:i A', strtotime($currentTime24));

        $beneficiary->Date = htmlspecialchars($currentDate);
        $beneficiary->time = htmlspecialchars($currentTime12);
        $beneficiary->Lastname = htmlspecialchars($_POST['Lastname']);
        $beneficiary->Firstname = htmlspecialchars($_POST['Firstname']);
        $beneficiary->Birthday = htmlspecialchars($_POST['Birthday']);
        $beneficiary->Contactnumber = htmlspecialchars($_POST['Contactnumber']);
        $beneficiary->CityMunicipality = htmlspecialchars($_POST['CityMunicipality']);
        $beneficiary->Barangay = htmlspecialchars($_POST['Barangay']);
        $beneficiary->Email = htmlspecialchars($_POST['Email']);

        $found = true;
        break;
    }
}

if ($found) {
    // Save the updated XML file
    $xml->asXML('beneficiary_records.xml');
    echo '<body>
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
        <script>
        swal("Beneficiary information has been updated.","","success")
        .then((value) => {
            if (value) {
                window.location.href = "beneficiaryrecords.php";
            }
        });
        </script>
        </body>';
} else {
    echo '<body>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>
    swal("Beneficiary bot found.","","error")
    .then((value) => {
        if (value) {
            window.location.href = "beneficiaryrecords.php";
        }
    });
    </script>
    </body>';
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
<input type="hidden" name="Beneficiary_Id" value="<?php echo htmlspecialchars($beneficiaryId); ?>">


<div class="user-details">
<div class="input-box">

<input type="hidden" disabled id="calendar" name="Date" required
value="<?php echo htmlspecialchars($Date); ?>">
</div>
<div class="input-box">

<input type="hidden" id="time" required disabled value="<?php echo htmlspecialchars($time12); ?>" name="time12">

</div>
<div class="input-box">
<span class="details">Last Name</span>
<input type="text" required value="<?php echo htmlspecialchars($lastname); ?>" name="Lastname" autocomplete="off">
</div>
<div class="input-box">
<span class="details">First Name</span>
<input type="text" required value="<?php echo htmlspecialchars($firstname); ?>" name="Firstname" autocomplete="off">
</div>
<div class="input-box">
<span class="details">Middle Name</span>
<input type="text" required value="<?php echo htmlspecialchars($middlename); ?>" name="Middlename" autocomplete="off">
</div>
<div class="input-box">
<span class="details">Birthday</span>
<input type="date" id="calendar" name="Birthday" required
value="<?php echo htmlspecialchars($birthday); ?>">
</div>
<div class="input-box">
<span class="details">Contact Number</span>
<input type="text" required value="<?php echo htmlspecialchars($contactnumber); ?>" name="Contactnumber" autocomplete="off">
</div>
<div class="input-box">
                        <span class="details">City/Municipality</span>
                        <select id="cityDropdown" name="CityMunicipality" onchange="populateBarangays()">
                            <option value="Select">Select</option>
                            <?php
                            $cityOptions = array("Select", "Abucay", "Bagac", "Balanga", "Dinalupihan", "Hermosa", "Limay", "Mariveles", "Morong", "Orani", "Orion", "Pilar", "Samal");
                            $selectedCity = $cityMunicipality;
                        
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

$selectedBarangay = $barangay;

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
<input type="text" required value="<?php echo htmlspecialchars($housenum); ?>" name="HouseNoStreet" autocomplete="off">
</div>
<div class="input-box">
<span class="details">Email</span>
<input type="text" required  value="<?php echo htmlspecialchars($email); ?>" name="Email" autocomplete="off">
</div>
</div>

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
