<?php
  session_start();
include("php/config.php");

if(isset($_SESSION['valid'])){
  $id = $_SESSION['id'];
  $query = mysqli_query($con, "SELECT * FROM users WHERE Id=$id");

if($result = mysqli_fetch_assoc($query)){
$res_Id = $result['Id'];
$res_Fname = $result['Firstname'];
$res_Lname = $result['Lastname'];
$res_Email = $result['Email'];
$res_City = $result['CityMunicipality'];
}
}
if(isset($_SESSION['serviceType'])){
  $serviceType = $_SESSION['serviceType'];
  
}else{
  echo "Service type not set.";
  exit;
}

if (isset($_POST['submit'])) {
    // Escape user inputs for security
    $name = mysqli_real_escape_string($con, $_POST['name2']);
    $date = mysqli_real_escape_string($con, $_POST['date']);
    $email = mysqli_real_escape_string($con, $_POST['email2']);
    $office = mysqli_real_escape_string($con, $_POST['office']);
    $assistance = mysqli_real_escape_string($con, $_POST['service']);
    $ease = mysqli_real_escape_string($con, $_POST['easy']);
    $effectiveness = mysqli_real_escape_string($con, $_POST['well']);
    $office_rating = mysqli_real_escape_string($con, $_POST['office_rating']);
    $website_rating = mysqli_real_escape_string($con, $_POST['website_rating']);
    $comments = mysqli_real_escape_string($con, $_POST['comments']);

  
        $query = "INSERT INTO feedback (Name, Date, Email, Office, AssistanceType, Ease, Effectiveness, OfficeRate, WebsiteRate, Comments) 
                  VALUES ('$name', '$date', '$email', '$office', '$assistance', '$ease', '$effectiveness', '$office_rating', '$website_rating', '$comments')";

        if (mysqli_query($con, $query)) {
            echo '<body>
            <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
            <script>
            swal("Feedback submitted!", "Thank you for your feedback.", "success");
            </script>';
            echo '<script>
            setTimeout(function(){
                window.location.href="usershomepage.php";
            }, 3000);
            </script>
            </body>';
        } else {
            echo "ERROR: Could not execute $query. " . mysqli_error($con);
        }
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Feedback Form</title>
    <link rel="stylesheet" href="feedback.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
</head>
<body>
    <script>
        document.addEventListener('DOMContentLoaded', (event) => {
            var today = new Date().toISOString().split('T')[0];
            document.getElementsByName("date")[0].setAttribute('value', today);
        });
    </script>
<?php
if(isset($_SESSION['valid'])): ?>
<div class="all-content">
    <!-- navbar for logged-in users -->
    <nav class="navbar navbar-expand-lg navbar-light" style="background-color: white;">
        <div class="container-fluid">
            <a class="navbar-brand" href="#" id="logo" style="font-size: 15px; color: #1477d2;">
                <img src="images/background.png" alt="Logo" /> Provincial Government of Bataan- Special Assistance Program
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span>
                    <i class="fa-solid fa-bars" style="color: #1477d2; font-size: 23px"></i>
                </span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a style="color: #1477d2; padding-left:10px; margin-left:200px" class="nav-link" aria-current="page"  href="usershomepage.php">Home</a>
                    </li>
                  
                    <li class="nav-item">
                        <a style="color: #1477d2; padding-left:10px;" class="nav-link" onclick="toggleMenu()">Profile</a>
                    </li>
                </ul>
                <div class="sub-menu-wrap" id="subMenu">
                    <div class="sub-menu">
                        <div class="user-info">
                            <img src="images/profile.png" alt="Profile Image">
                            <h2><?php echo htmlspecialchars($res_Fname); ?> <?php echo htmlspecialchars($res_Lname); ?></h2>
                        </div>
                        <hr>
                        <form action="edit.php" method="POST" class="sub-menu-link">
                            <input type="hidden" name="userId" value="<?php echo htmlspecialchars($res_Id); ?>">
                            <button type="submit" class="btn-edit-profile">
                                <img src="images/profile.png" alt="Edit Profile Image">
                                <p style="color:white;">Edit Profile</p>
                            </button>
                        </form>
                        <a href="logout.php" class="sub-menu-link">
                            <img src="images/logout.png" alt="Logout Image">
                            <p style="color:white;">Log out</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</div>
<?php else: ?>
<div class="all-content">
    <!-- navbar for guests -->
    <nav class="navbar navbar-expand-lg navbar-light" style="background-color: white;">
        <div class="container-fluid">
            <a class="navbar-brand" href="#" id="logo" style="font-size: 13px; color: #1477d2;">
                <img src="images/background.png" alt="Logo" /> PGB - Special Assistance Program
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span>
                    <i class="fa-solid fa-bars" style="color: #1477d2; font-size: 23px"></i>
                </span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0" >
                    <li class="nav-item" style="margin-left:300px;">
                        <a style="color: #1477d2" class="nav-link" aria-current="page" href="index.php">Home</a>
                    </li>
                     
                    <li class="nav-item">
                        <a style="color: #1477d2" class="nav-link" href="register.php">Sign Up</a>
                    </li>
                    <li class="nav-item">
                        <a style="color: #1477d2" class="nav-link" href="login.php">Log in</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</div>
<?php endif; ?>
    <form action="" method="POST">
        <div class="feedback">
            <center><h1>Feedback Form</h1></center>

            <p class="eng" style="margin-top:50px;">
                This feedback form monitors the experience of clients of
                the government office. Your feedback on your recently completed
                transaction will help this office provide better service. The personal
                information shared will be kept confidential, and you always have the
                option not to answer this form.
            <p class="fil">
                (Ang feedback form na ito ay sinusubaybayan ang karanasan ng mga kliyente ng tanggapan ng gobyerno.
                Ang iyong feedback sa iyong kamakailang natapos na transaksyon sa PGB-SAP ay makakatulong sa opisinang ito na
                makapagbigay ng mas mahusay na serbisyo. Ang personal na impormasyong ibinahagi ay pananatilihing kompidensyal at palagi kang may opsyon na
                hindi sagutin ang form na ito.)
            </p>
            </p>
        </div>
        <div class="namee">
        <h1 style="font-size: 15px; margin-top: 35px; font-size: 15px; margin-left: 35px;"><strong>Name</strong></h1>
 
                     <input type="text"  disabled name="name" id="name" autocomplete="off"  style="font-size: 15px;  font-size: 15px; margin-left: 35px;" value="<?php echo isset($res_Fname) ? $res_Fname . ' ' . $res_Lname : ''; ?>">                  
                     <input type="hidden"   name="name2" id="name2" autocomplete="off"   value="<?php echo isset($res_Fname) ? $res_Fname . ' ' . $res_Lname : ''; ?>">                  
                    
                     <input type="checkbox" id="anonymousCheck" name="anonymousCheck" onclick="toggleAnonymous()"> 
<label for="anonymousCheck">Check if you want to remain anonymous</label><br>
</div>
<div class="emaill">
        <h1 style="font-size: 15px; margin-top: 35px; font-size: 15px; margin-left: 35px;"><strong>Email</strong></h1>
 
        <input type="text" disabled name="email" id="email" style="font-size: 15px; font-size: 15px; margin-left: 35px; width:300px;" autocomplete="off" value="<?php echo isset($res_Email) ? $res_Email : ''; ?>">                  
        <input type="hidden"  name="email2" id="email2" autocomplete="off" value="<?php echo isset($res_Email) ? $res_Email : ''; ?>">                  
      
        <input type="checkbox" id="anonymousCheck2" name="anonymousCheck2" onclick="toggleAnonymous2()"> 
<label for="anonymousCheck2">Check if you want to remain anonymous</label><br>
</div>
                      <input type="hidden"  name="date" id="date"  required value="<?php echo htmlspecialchars($_POST['date'] ?? '', ENT_QUOTES); ?>" />
       
                      <input type="hidden"  name="id" id="id" autocomplete="off" value="<?php echo isset($res_Id) ? $res_Id:''; ?>">                  
                   
                  
                     <input type="hidden"   name="usercity" id="usercity" autocomplete="off" value="<?php echo isset($res_City) ? $res_City:''; ?>">                  
                     <input type="hidden"   name="service" id="service" autocomplete="off" value="<?php echo isset($serviceType) ? $serviceType:''; ?>">                  
                   
                
       

  
   <!--
                     <div class="dropdown" style="margin-top: 10px;">
                       <label style="margin-bottom: 5px; margin-left: 35px; font-size: 15px;"><strong>Office Visited/Assistance Acquired (Opisinang Pinuntahan/Pinagkuhanan ng Tulong)</strong></label><br>
    <select name="office" style="margin-left:40px; height:40px; width:400px" required>
      <option value="">Select Here</option>
      <option value="1BM - Balanga">1BM - Balanga</option>
      <option value="1BM - Dinalupihan">1BM - Dinalupihan</option>
      <option value="1BM - Hermosa">1BM - Hermosa</option>
      <option value="1BM - Mariveles">1BM - Mariveles</option>
    </select>
      <label style="margin-bottom: 5px; margin-left: 35px; font-size: 15px;">
        <strong>Office Visited/Assistance Acquired (Opisinang Pinuntahan/Pinagkuhanan ng Tulong)</strong>
    </label><br>
     </div><br>
       <div class="dropdown">
    <label style="margin-bottom: 5px; margin-left: 35px; font-size: 15px;"><strong>Type of Assistance. Choose one. (Uri ng Tulong. Pumili ng isa.)</strong></label><br>
    <select name="assistance" style="margin-left:40px; height:40px; width:400px" required>
      <option value="">Select Here</option>
      <option value="Financial Assistance/Burial Assistance">Financial Assistance/Burial Assistance</option>
      <option value="Medicines Assistance">Medicines Assistance</option>
      <option value="Hospital Bills Assistance">Hospital Bills Assistance</option>
      <option value="Radiation and Chemotherapy">Radiation and Chemotherapy</option>
      <option value="Dialysis Patients">Dialysis Patients</option>
    </select>
  </div><br>-->
    <div class="office-info" style="margin-top: 10px;">
  
    <input type="hidden" id="office" name="office" style="margin-left:40px; font-size: 15px; font-weight: bold;">

</div><br>
 


  

  <div class="dropdown">
      <label style = "margin-bottom: 5px; margin-left: 35px; font-size: 15px; margin-top: 20px;"> <strong> How easy was it to find the special assistance you were looking for on our website? (Gaano kadali mong natagpuan ang espesyal na tulong na hinahanap mo sa aming website?) </strong> </label> <br>
      
        <select name="easy" style="margin-left:40px; height:40px; width:400px" required>
      <option value="">Select Here</option>
      <option value="Extremely easy">Extremely easy</option>
      <option value="Very easy">Very easy</option>
      <option value="Somewhat easy">Somewhat easy</option>
      <option value="Not easy">Not easy</option>
      <option value="Not at all easy">Not at all easy</option>
    </select>
  </div><br>

  <div class="dropdown">
      <label style = "margin-bottom:  5px;margin-left: 35px; font-size: 15px; margin-top: 20px;"> <strong> How effectively does our website fulfill your needs? (Gaano kahusay na natugunan ng aming website ang iyong mga pangangailangan?) </strong> </label> <br>
      <select name="well" style="margin-left:40px; height:40px; width:400px" required>
      <option value="">Select Here</option>
      <option value="Extremely easy">Extremely well</option>
      <option value="Very easy">Very well</option>
      <option value="Somewhat easy">Somewhat well</option>
      <option value="Not easy">Not so well</option>
      <option value="Not at all easy">Not at all well</option>
    </select>
  </div><br>

  <h1 style="font-size: 15px; margin-top: 35px; font-size: 15px; margin-left: 35px;"><strong>Please rate the performance of the office. (Mangyaring magbigay ng marka sa tanggapan.)</strong></h1><br>
  <div class="star-rating" style="margin-left: 35px;">
    <input type="radio" name="office_rating" id="office_star1" value="5"><label for="office_star1"></label>
    <input type="radio" name="office_rating" id="office_star2" value="4"><label for="office_star2"></label>
    <input type="radio" name="office_rating" id="office_star3" value="3"><label for="office_star3"></label>
    <input type="radio" name="office_rating" id="office_star4" value="2"><label for="office_star4"></label>
    <input type="radio" name="office_rating" id="office_star5" value="1"><label for="office_star5"></label>
  </div>

  <h1 style="font-size: 15px; margin-top: -5px; font-size: 15px; margin-left: 35px;"><strong>Please rate the performance of the website. (Mangyaring magbigay ng marka sa karanasan sa paggamit ng website.)</strong></h1><br>
  <div class="star-rating" style="margin-left: 35px;">
    <input type="radio" name="website_rating" id="website_star1" value="5"><label for="website_star1"></label>
    <input type="radio" name="website_rating" id="website_star2" value="4"><label for="website_star2"></label>
    <input type="radio" name="website_rating" id="website_star3" value="3"><label for="website_star3"></label>
    <input type="radio" name="website_rating" id="website_star4" value="5"><label for="website_star4"></label>
    <input type="radio" name="website_rating" id="website_star5" value="1"><label for="website_star5"></label>
  </div>

  <div class="input-box">
    <h1 style="font-size: 15px; margin-top: -10px; font-size: 15px;"><strong>Comments/Suggestions for the improvement of the services. (Mga Komento/Suhestiyon para sa pagpapabuti ng Serbisyo)</strong></h1>
    <textarea name="comments"></textarea>
  </div>
  <button type="submit" name="submit" style="margin-left:250px; height:40px; background:green; color:white; width:100px">Submit</button>
</form>
   
    <script>
  function toggleDropdown() {
  var dropdownContent = document.getElementById("myDropdown");
  if (dropdownContent.style.display === "block") {
    dropdownContent.style.display = "none";
  } else {
    dropdownContent.style.display = "block";
  }
  }

// Close the dropdown if the user clicks outside of it
  window.onclick = function(event) {
  if (!event.target.matches('.dropbtn')) {
    var dropdowns = document.getElementsByClassName("dropdown-content");
    for (var i = 0; i < dropdowns.length; i++) {
      var openDropdown = dropdowns[i];
      if (openDropdown.style.display === "block") {
        openDropdown.style.display = "none";
      }
    }
  }
  }

  function toggleDropdown1() {
  var dropdownContent1 = document.getElementById("myDropdown1");
  if (dropdownContent1.style.display === "block") {
    dropdownContent1.style.display = "none";
  } else {
    dropdownContent1.style.display = "block";
  }
}

// Close the dropdown if the user clicks outside of it
window.onclick = function(event) {
  if (!event.target.matches('.dropbtn')) {
    var dropdowns = document.getElementsByClassName("dropdown-content");
    for (var i = 0; i < dropdowns.length; i++) {
      var openDropdown = dropdowns[i];
      if (openDropdown.style.display === "block") {
        openDropdown.style.display = "none";
      }
    }
  }
}

function toggleDropdown2() {
  var dropdownContent1 = document.getElementById("myDropdown2");
  if (dropdownContent1.style.display === "block") {
    dropdownContent1.style.display = "none";
  } else {
    dropdownContent1.style.display = "block";
  }
}

// Close the dropdown if the user clicks outside of it
window.onclick = function(event) {
  if (!event.target.matches('.dropbtn')) {
    var dropdowns = document.getElementsByClassName("dropdown-content2");
    for (var i = 0; i < dropdowns.length; i++) {
      var openDropdown = dropdowns[i];
      if (openDropdown.style.display === "block") {
        openDropdown.style.display = "none";
      }
    }
  }
}

function toggleDropdown3() {
  var dropdownContent1 = document.getElementById("myDropdown3");
  if (dropdownContent1.style.display === "block") {
    dropdownContent1.style.display = "none";
  } else {
    dropdownContent1.style.display = "block";
  }
}

// Close the dropdown if the user clicks outside of it
window.onclick = function(event) {
  if (!event.target.matches('.dropbtn')) {
    var dropdowns = document.getElementsByClassName("dropdown-content2");
    for (var i = 0; i < dropdowns.length; i++) {
      var openDropdown = dropdowns[i];
      if (openDropdown.style.display === "block") {
        openDropdown.style.display = "none";
      }
    }
  }
}

   
       

   
       
let subMenu= document.getElementById("subMenu");
        function toggleMenu(){
            subMenu.classList.toggle("open-menu");
   }
   
   const userCityMunicipality =document.getElementById('usercity').value;

const officeText = document.getElementById('office');

// Determine the office based on the user's city/municipality
let officeValue = '';

switch(userCityMunicipality) {
    case 'Balanga':
    case 'Abucay':
    case 'Limay':
    case 'Orion':
    case 'Pilar':

        officeValue = '1BM - Balanga';
        break;

    case 'Hermosa':
      case 'Orani':
        case 'Samal':
        officeValue = '1BM - Hermosa';
        break;

        case 'Bagac':
        case 'Mariveles':
        case 'Morong':
        officeValue = '1BM - Mariveles';
        break;

        case 'Dinalupihan':
        officeValue = '1BM - Dinalupihan';
        break;
    // You can add more cases if needed
    default:
        officeValue = 'Office not found'; // Default message if no match
}

// Display the office in the paragraph
officeText.value = officeValue;

 
const service =document.getElementById('service').value;

// Get the paragraph element
const serviceText = document.getElementById('service');

// Determine the office based on the user's city/municipality
let serviceValue = '';

switch(service) {
    case 'Burial':
 
        serviceValue = 'Financial Assistance-Burial';
        break;

    case 'dialysis':
    
        serviceValue = 'Financial Assistance-Dialysis';
        break;

        case 'Radiation & Chemotherapy':
       
        serviceValue = 'Financial Assistance-Radiation & Chemotherapy';
        break;

        case 'medicines':
        serviceValue = 'Medicines';
        break;
        case 'hospitalbills':
        serviceValue = 'Hospital Bills Assistance';
        break;

        case 'laboratories':
        serviceValue = 'Laboratories Assistance';
        break;
    // You can add more cases if needed
    default:
       serviceValue = 'service not found'; // Default message if no match
}

// Display the office in the paragraph
serviceText.value =serviceValue;
function toggleAnonymous() {
        const nameInput = document.getElementById('name');
        const nameInput2 = document.getElementById('name2');
        const anonymousCheck = document.getElementById('anonymousCheck');

        if (anonymousCheck.checked) {
            nameInput.value = '***********';
            nameInput2.value = '***********';
        } else {
            // Reset the value to the original name
            nameInput.value = "<?php echo isset($res_Fname) ? $res_Fname . ' ' . $res_Lname : ''; ?>";
            nameInput2.value = "<?php echo isset($res_Fname) ? $res_Fname . ' ' . $res_Lname : ''; ?>";
        }
    }
    function toggleAnonymous2() {
        const emailInput = document.getElementById('email');
        const emailInput2 = document.getElementById('email2');
        const anonymousCheck2 = document.getElementById('anonymousCheck2');

        if (anonymousCheck2.checked) {
            emailInput.value = '***********';
            emailInput2.value = '***********';
        } else {
            // Reset the value to the original name
            emailInput.value = "<?php echo isset($res_Email) ? $res_Email  : ''; ?>";
            emailInput2.value = "<?php echo isset($res_Email) ? $res_Email  : ''; ?>";
        }
    }
</script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>

<script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
      crossorigin="anonymous" >
    
 
    </script>

  </body>
</html>
