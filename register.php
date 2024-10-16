
<?php 
       use PHPMailer\PHPMailer\PHPMailer;
       use PHPMailer\PHPMailer\SMTP;
       use PHPMailer\PHPMailer\Exception;
       
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
                    $unameError = "";
                    $passError = "";
                     $profileError = "";
                    $Errors = "";
                    
                    
                   // $verification_code= substr($otp_str, 0, 6);//
                    $otp_str = "123456789"; // String na hindi naglalaman ng 0 sa unang character
$otp_str_with_zero = "0123456789"; // Buong range ng digits

do {
    $verification_code = substr(str_shuffle($otp_str_with_zero), 0, 6);
} while ($verification_code[0] == '0'); // Siguraduhin na hindi magsisimula sa 0

                     
                    if(isset($_POST['submit'])){
                    // receive all input values from the form

               
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
                    $Username = $_POST['Username'];
                    $Password = $_POST['password'];
                    $hashed_password = password_hash($Password, PASSWORD_BCRYPT);
                  
                $users1 = "SELECT * FROM users WHERE Username='$Username' LIMIT 1";
                $results = mysqli_query($con, $users1);
                $user1 = mysqli_fetch_assoc($results); 

                $users2 = "SELECT * FROM employees WHERE username='$Username' LIMIT 1";
                $results2 = mysqli_query($con, $users2);
                $user2 = mysqli_fetch_assoc($results2); 
                
                $users = "SELECT * FROM users WHERE Email='$Email' LIMIT 1";
                $results = mysqli_query($con, $users);
                $user = mysqli_fetch_assoc($results);
                    
                    
                    $user_Cn = "SELECT * FROM users WHERE Contactnumber='$Contactnumber' LIMIT 1";
                    $results = mysqli_query($con, $user_Cn);
                    $user_phone = mysqli_fetch_assoc($results);
                  
                    $minDate = new DateTime();
                    $maxDate = new DateTime();
$minDate->modify('-18 years');
$maxDate->modify('-100 years');
// Convert $Birthday to a DateTime object for comparison
$selectedDate = new DateTime($Birthday);

                    if(empty($Lastname))
                    {
                      array_push($errors, $lastError = "Lastname is required");
                    }
                    elseif (!preg_match('/^[a-zA-Z ]*$/', $Lastname)) {
                     
                      array_push($errors, $lastError = "Only alphabetic characters and spaces are allowed.");
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
                      array_push($errors, $bdayError = "You must be at least 18 years old");
                  }
                  elseif ($selectedDate < $maxDate) {
                    array_push($errors, $bdayError = "You must be at least 100 years old");
                }
                    if(empty($Contactnumber))
                    {
                      array_push($errors, $numError = "Contact Number is required");
                    }
                    elseif (strlen($Contactnumber) > 11) {
                      array_push($errors,  $numError = "Mobile number must be maximum of 11 characters");
                      }
                      elseif (!preg_match('/^\d+$/', $Contactnumber)) {
                        array_push($errors, $numError = "Mobile number should contain numbers only");
                    } 
                      elseif ($user_phone) { // if user contact exists
                        array_push($errors, $numError ="This number cannot be used anymore.");
                          }
                    elseif (strlen($Contactnumber) < 11) {
                            array_push($errors,  $numError = "Mobile number must be minimum of 11 characters");
                            }
                    if(empty($Province))
                    {
                      array_push($errors, $provError = "Province is required");
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
                    if(empty($Email))
                    {
                      array_push($errors, $emailError = "Email is required");
                    }
                   elseif ($user) { // If user exists
                      array_push($errors, $emailError ="This email address is already in use.");
                  
                  }
                    elseif (!filter_var($Email, FILTER_VALIDATE_EMAIL)) {
                  array_push($errors, $emailError = "Invalid email format");
                    }
                    if(empty($Username))
                    {
                      array_push($errors,  $unameError = "Username is required");
                    }
                    elseif (strlen($Username) < 6) {
                      array_push($errors,  $unameError = "Username must be minimum of 6 characters");
                      }
                      elseif ($user1) { // If user exists
                        array_push($errors, $unameError ="This username is already in use.");
                    
                    }
                    elseif ($user2) { // If user exists
                      array_push($errors, $unameError ="This username is already in use.");
                  
                  }
                    if(empty($Password))
                    {
                        array_push($errors, $passError = "Password is required");
                      } elseif (!preg_match('/^(?=.*\d)(?=.*[A-Z])(?=.*[a-z])(?=.*[^A-Za-z0-9]).{8,}$/', $Password)) {
                        array_push($errors, $passError = "Password must be at least 8 characters long and contain at least one number, one uppercase letter, one lowercase letter, and one special character.");
                    }
        
        $check_user = "SELECT * FROM users WHERE Email='$Email' LIMIT 1";
                    $result = mysqli_query($con, $check_user);

    

      if (empty($errors)) {

date_default_timezone_set('Asia/Manila');

// Get the current datetime in the correct format
$currentDateTime = date('Y-m-d H:i:s');

// Construct the SQL query to insert data into the database
$query = "INSERT INTO users (Lastname, Firstname, Middlename, Birthday, Contactnumber, Province, CityMunicipality, Barangay, HousenoStreet, Email, Username, Password,  signup_time,status, verification_code) 
          VALUES ('$Lastname', '$Firstname', '$Middlename', '$Birthday', '$Contactnumber', '$Province', '$CityMunicipality', '$Barangay', '$HousenoStreet', '$Email', '$Username', '$hashed_password', '$currentDateTime','0', '$verification_code')";

if(mysqli_query($con, $query)){
  if ($result) {
    //send verification email
   
    require 'PHPMailer/src/Exception.php';
    require 'PHPMailer/src/PHPMailer.php';
    require 'PHPMailer/src/SMTP.php';

   
    $mail = new PHPMailer(true);

        //Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'bataanpgbsap@gmail.com'; // Your Gmail address
        $mail->Password = 'cmpp hltn mxuc tcgl'; // Your Gmail password or App Password
        $mail->SMTPSecure = 'PHPMailer::ENCRYPTION_STARTTLS';
        $mail->Port = 587;
    
        //Recipients
        $mail->setFrom('bataanpgbsap@gmail.com', 'PGB-SAP');
        $mail->addAddress($Email);     //Add a recipient
    $link="http://localhost/public_html/verification.php";

        //Content
        $mail->isHTML(true); // Set email format to HTML
        $mail->Subject = 'Email Verification';
        $mail->Body = "Good Day! This is your verification code: $verification_code. You may access the verification field in our website or through this <a href='$link'>link</a>.<br>
             <p> If you did not request for this code. Please ignore this email.<br>
             Thank you and God Bless<br>
             PGB Damayan Center
             </p>";
          
    
       if($mail->send()){
        echo '<body>
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
        <script>
        swal("Registration successful!", "Please verify your email.", "success")
        </script>';
          echo '<script>
         setTimeout(function(){
            window.location.href="verification.php";
        } , 3000);
      </script>
      </body>';
       }
      }
      }else{
      }
    }
     
  $query = "SELECT * FROM users WHERE Email='$Email' LIMIT 1";
$result = mysqli_query($con, $query);
$user = mysqli_fetch_assoc($result);



  }


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
   
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="register.css">
</head>

<body>
               
    <div class = "container">
         
        <div class="box form-box">
    
         <header>Sign Up</header>
            <form action="" method= "post" enctype="multipart/form-data">
      
                <div class="field input">
                    
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
                    <input type="date" name="Birthday" id="Birthday" max="" value="<?php echo $_POST['Birthday'] ?? ''; ?>"> 
                    <p style="color: rgb(150, 26, 26); font-size: 18px;"><?php echo $bdayError ?></p>                  
                </div>
                <div class="field input">
                    <label for = "Contactnumber" style="font-size: 18px;">Contact Number</label>
                    <input type="text" name="Contactnumber" id="Contactnumber" autocomplete="off" value="<?php echo $_POST['Contactnumber'] ?? ''; ?>">                  
                    <p style="color: rgb(150, 26, 26); font-size: 18px;"><?php echo $numError ?></p>
                   
              
                  </div>
                <div class="field input">
                    <label for = "Province" style="font-size: 18px;">Province</label>
                    <input type="text" name="Province" id="Province" value="Bataan">                  
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
                <div class="field input">
                    <label for = "Email" style="font-size: 18px;">Email</label>
                    <input type="text" name="Email" id="Email" autocomplete="off" value="<?php echo $_POST['Email'] ?? ''; ?>">                  
                    <p style="color: rgb(150, 26, 26); font-size: 18px;"><?php echo $emailError ?></p>
                  </div>
                <div class="field input">
                    <label for = "Username" style="font-size: 18px;">Username</label>
                    <input type="text" name="Username" id="Username" autocomplete="off" value="<?php echo $_POST['Username'] ?? ''; ?>">                  
                    <p style="color: rgb(150, 26, 26); font-size: 18px;"><?php echo $unameError ?></p>
                  </div>
                  <div class="field input input-container">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" autocomplete="off" value="<?php echo $_POST['password'] ?? ''; ?>" required>
                   <i class="fas fa-eye toggle-password" onclick="togglePasswordVisibility()"></i>
                   <p style="color: rgb(150, 26, 26); font-size: 18px;"><?php echo $passError ?></p>                    
                </div>
  
                  
                     <form class="form-inline">
       
                <div class="field">
                    <input type="submit" class="btn" name="submit" value="Sign Up">                  
                </div>

       
                <div class="links">
            <center>    Already have an account? <a href="login.php">Log In here</a></center> <br>
            <center>   
            <a href="index.php" style="color:rgb(99, 95, 95); text-decoration: none; display: inline-flex; align-items: center;">
        <img src="images/back.png" style="height: 15px; width: 20px; margin-right: 6px;" />
        Back to Home
    </a>  </div>
            </form>
            
        </div>
       
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
  
  function togglePasswordVisibility() {
            const passwordField = document.getElementById('password');
            const togglePassword = document.querySelector('.toggle-password');
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                togglePassword.classList.remove('fa-eye');
                togglePassword.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                togglePassword.classList.remove('fa-eye-slash');
                togglePassword.classList.add('fa-eye');
            }
        }

    
        const today = new Date();

// Subtract 18 years from the current date
const maxDate = new Date(today.getFullYear() - 18, today.getMonth(), today.getDate());

// Format the date as YYYY-MM-DD
const formattedDate = maxDate.toISOString().split('T')[0];

// Set the max attribute of the input
document.getElementById('Birthday').max = formattedDate;
       
    </script>
</body>

</html>
