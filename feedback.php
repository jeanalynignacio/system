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

$query1 = mysqli_query($con, "SELECT * FROM beneficiary WHERE Representative_ID=$res_Id");

if($result2 = mysqli_fetch_assoc($query1)){
$Ben_Id = $result2['Beneficiary_Id'];
}
$query3 = mysqli_query($con, "SELECT * FROM history WHERE Beneficiary_ID=$Ben_Id");
if($result3 = mysqli_fetch_assoc($query3)){
    $Ben_Id = $result3['Beneficiary_ID'];
    $AssistanceType=$result3['AssistanceType'];
    $branch=$result3['branch'];

    }


if (isset($_POST['submit'])) {
    
    //   $name = mysqli_real_escape_string($con, $_POST['name2']);
        $date = mysqli_real_escape_string($con, $_POST['date']);
        $email = mysqli_real_escape_string($con, $_POST['email2']);
    // $office = mysqli_real_escape_string($con, $_POST['office']);
        $assistance = mysqli_real_escape_string($con, $_POST['service']);
        $CC1 = mysqli_real_escape_string($con, $_POST['CC1']);
        $CC2 = mysqli_real_escape_string($con, $_POST['CC2']);
        $CC3 = mysqli_real_escape_string($con, $_POST['CC3']);
        $SQD0 = mysqli_real_escape_string($con, $_POST['SQD0']);
        $SQD1 = mysqli_real_escape_string($con, $_POST['SQD1']);
        $SQD2 = mysqli_real_escape_string($con, $_POST['SQD2']);
        $SQD3 = mysqli_real_escape_string($con, $_POST['SQD3']);
        $SQD4 = mysqli_real_escape_string($con, $_POST['SQD4']);
        $SQD5 = mysqli_real_escape_string($con, $_POST['SQD5']);
        $SQD6 = mysqli_real_escape_string($con, $_POST['SQD6']);
        $SQD7 = mysqli_real_escape_string($con, $_POST['SQD7']);
        $SQD8 = mysqli_real_escape_string($con, $_POST['SQD8']);

        $comments = mysqli_real_escape_string($con, $_POST['comments']);
       

        // Check if there's an existing record with the same beneficiary ID, assistance, and within the last 2 months
        $two_months_ago = date('Y-m-d', strtotime('-3 months', strtotime($date)));
    
        $query = "SELECT * FROM feedback WHERE Beneficiary_ID = '$Ben_Id' AND ServiceType = '$assistance' AND Date > '$two_months_ago'";
        $result2 = mysqli_query($con, $query);
    
        if (mysqli_num_rows($result2) > 0) {
         
            echo '<body>
            <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
            <script>
            swal("You have already submitted feedback. Thank you for your input!", "", "info");
            </script>';
            echo '<script>
            setTimeout(function(){
                window.location.href="usershomepage.php";
            }, 3000);
            </script>
            </body>';
        } else {
  
        $query = "INSERT INTO feedback (Beneficiary_ID, Date, Email, Office, ServiceType, CC1, CC2, CC3,SQD0,SQD1,SQD2,SQD3,SQD4,SQD5,SQD6,SQD7,SQD8, Comments) 
                  VALUES ( '$Ben_Id','$date', '$email', '$branch', '$assistance', '$CC1', '$CC2', '$CC3', '$SQD0','$SQD1','$SQD2','$SQD3','$SQD4','$SQD5','$SQD6','$SQD7','$SQD8', '$comments')";

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
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
   
</head>
<body>
    <style>   
    

    /* Apply styles only to elements with class .smiley-radio */
  .smiley-radio + label {
    border: 0 solid transparent;
    transition: border 0.3s ease, box-shadow 0.3s ease;
    cursor: pointer;
  }

  /* When the radio button with class .smiley-radio is checked, style the associated label */
  .smiley-radio:checked + label {
    border: 3px solid #00f; /* Blue border for selected face */
    box-shadow: 0 0 3px #00f; /* Blue glow effect */
    border-radius: 50%; /* Rounded border */
  }

  /* Optional: add hover effect */
   .smiley-radio + label:hover {
    border: 0px solid #999; /* Gray border on hover */
  }
  
  

        </style>
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
            <center><h2 style="margin-top:10px;">TULUNGAN MO KAMI MAS MAPABUTI ANG AMING MGA PROSESO AT SERBISYO</h2></center>

            <p class="eng" style="margin-top:50px;">
                Ang Client Satisfaction Measurement (CSM) ay naglalayong masubaybayan ang karanasan ng taumbayan hinggil sa kanilang
                pakikipagtransaksyon sa mga tanggapan ng gobyerno. Makatutulong ang inyong kasagutan ukol sa inyong naging karanasan sa kakatapos
                lamang na transaksyon, upang mas mapabuti at lalong mapahusay ang aming serbisyo publiko. Ang personal na impormasyon an iyong 
                ibabahagi ay mananatiling kumpidensyal. Maaari ring piliin na hindi sagutan ang serbey na ito.
           
           
            </p>
        </div>
        <div class="namee">
         <!--<h1 style="font-size: 15px; margin-top: 35px; font-size: 15px; margin-left: 35px;"><strong>Name</strong></h1>
 
                    <input type="text"  disabled name="name" id="name" autocomplete="off"  style="font-size: 15px;  font-size: 15px; margin-left: 35px;" value="<?php echo isset($res_Fname) ? $res_Fname . ' ' . $res_Lname : ''; ?>">                  
                     <input type="hidden"   name="name2" id="name2" autocomplete="off"   value="<?php echo isset($res_Fname) ? $res_Fname . ' ' . $res_Lname : ''; ?>">                  

                     <input type="checkbox" id="anonymousCheck" name="anonymousCheck" onclick="toggleAnonymous()"> 
<label for="anonymousCheck">Check if you want to remain anonymous</label><br>-->
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
                   
                  
                    <!-- <input type="hidden"   name="usercity" id="usercity" autocomplete="off" value="<?php echo isset($res_City) ? $res_City:''; ?>">-->                  
                     <input type="hidden"   name="service" id="service" autocomplete="off" value="<?php echo isset($AssistanceType) ? $AssistanceType:''; ?>">                  
                     <input type="hidden"   name="office" id="office" autocomplete="off" value="<?php echo isset($branch) ? $branch:''; ?>">                  
                   
                  
     
    <div class="office-info" style="margin-top: 10px;">
  
    <input type="hidden" id="office" name="office" style="margin-left:40px; font-size: 15px; font-weight: bold;">

</div><br>
<label style="margin-bottom: 5px;  margin-left: 35px; font-size: 20px; margin-top: -12px;">
        <strong>INSTRUCTIONS: Choose your answer to the following questions about the Citizen's Charter (CC), It is an official document that contains the services of a government agency/office, the required documents can be found here , corresponding fees, and total processing time</strong>
    </label> <br><br>


    <div class="radio-group">
    <label style="margin-bottom: 5px; margin-left: 65px; font-size: 16px; margin-top: -10px;">
    (<em>CC1</em>)  <span style="text-decoration: underline;">Alin sa mga sumusunod ang naglalarawan sa iyong kaalaman sa CC (Citizen's Charter)?</span>
    </label> <br>

    <div style="margin-left: 90px; border:none;box-shadow:none; ">
        <input type="radio" id="alam" name="CC1" style="border:none;box-shadow:none;" value="Alam ko ang CC at nakita ko ito sa napuntahang opisina">
        <label for="alam" style="border:none; ">1. Alam ko ang CC at nakita ko ito sa napuntahang opisina</label><br>

        <input type="radio" id="wala" name="CC1" value="Alam ko ang CC pero hindi ko ito nakita sa napuntahang opisina">
        <label for="wala">2. Alam ko ang CC pero hindi ko ito nakita sa napuntahang opisina</label><br>

        <input type="radio" id="nalaman" name="CC1" value="Nalaman ko ang CC nang makita ko ito sa napuntahang opisina">
        <label for="nalaman">3. Nalaman ko ang CC nang makita ko ito sa napuntahang opisina</label><br>

        <input type="radio" id="hindialam" name="CC1" value="Hindi ko alam kung ano ang CC at wala akong nakita sa napuntahang opisina">
        <label for="hindialam">4. Hindi ko alam kung ano ang CC at wala akong nakita sa napuntahang opisina</label>
        (<em>Piliin ang opsyon 5 sa CC1 at CC2 kung ito ang pinili</em>)<br><br>
    </div>
</div>
<div class="radio-group">
    <label style="margin-bottom: 5px;  margin-left: 65px; font-size: 16px; margin-top: -10px;">
    (<em>CC2</em>) <span style="text-decoration: underline;">Kung alam ang CC</span> (<em>Pinili ang opsyon 1-3 sa CC1</em>)<span style="text-decoration: underline;">, masasabi mo ba na ang CC nang napuntahang opisina ay...</span>
    
    </label> <br>

    <div style="margin-left: 90px;">
        <input type="radio" id="alam1" name="CC2" value="Madaling makita">
        <label for="alam1">1. Madaling makita</label><br>

        <input type="radio" id="wala1" name="CC2" value="Medyo madaling makita">
        <label for="wala1">2. Medyo madaling makita</label><br>

        <input type="radio" id="nalaman1" name="CC2" value="Mahirap makita">
        <label for="nalaman1">3. Mahirap makita</label><br>

        <input type="radio" id="hindialam1" name="CC2" value="Hindi makita">
        <label for="hindialam1">4. Hindi makita</label><br>

        <input type="radio" id="na1" name="CC2" value="N/A">
        <label for="na1">5. N/A</label><br><br>
    </div>
</div>
<div class="radio-group1">
    <label style="margin-bottom: 5px;  margin-left: 65px; font-size: 16px; margin-top: -10px;">
    (<em>CC3</em>) <span style="text-decoration: underline;">Kung alam ang CC</span> (<em>Pinili ang opsyon 1-3 sa CC1</em>)<span style="text-decoration: underline;">,  gaano nakatulong ang CC sa transaksyon mo?    </span>
    </label> <br>

    <div class="help" style="margin-left: 90px;">
        <input type="radio" id="c1" name="CC3" value="Sobrang nakatulong">
        <label for="c1">1. Sobrang nakatulong</label><br>

        <input type="radio" id="c2" name="CC3" value="Medyo madaling makita">
        <label for="c2">2. Nakatulong naman</label><br>

        <input type="radio" id="c3" name="CC3" value="Mahirap makita">
        <label for="c3">3. Hindi nakatulong</label><br>

        <input type="radio" id="c4" name="CC3" value="N/A">
        <label for="c4">4. N/A</label><br><br>
    </div>
</div>

<label style="margin-bottom: 5px;  margin-left: 40px; font-size: 20px; margin-top: 20px;">
    (<em>PANUTO:</em>) <span style="text-decoration: underline;">Para sa SQD 0-8, piliin ang pinakaangkop sa iyong sagot  </span>
    </label> <br>

  <h1 style="font-size: 15px; margin-top: 5px; font-size: 15px; margin-left: 35px;"><strong>SQDO. Nasiyahan ako sa serbisyo na aking natanggap sa napuntahan na tanggapan.</strong></h1><br>
  <div class="emotion" style="margin-left: 55px;margin-top: -15px;margin-bottom: -15px;">
  <input type="radio" name="SQD0" id="SAD11" class="smiley-radio" style="display: none;" value="Dismayado" />
  <label for="SAD11" style="background-image: url('images/1.png'); width: 55px; height: 55px; display: inline-block; background-size: cover;margin-right:15px;cursor:pointer;"></label>

  <input type="radio" name="SQD0" id="SAD22" class="smiley-radio" style="display: none;" value="Malungkot"/>
  <label for="SAD22" style="background-image: url('images/2.png'); width: 50px; height: 50px; display: inline-block; background-size: cover;margin-right:15px;cursor:pointer;"></label>

  <input type="radio" name="SQD0" id="NEUTRAL1" class="smiley-radio" style="display: none;" value="Neutral"/>
  <label for="NEUTRAL1" style="background-image: url('images/3.png'); width: 50px; height: 50px; display: inline-block; background-size: cover;margin-right:15px;cursor:pointer;"></label>

  <input type="radio" name="SQD0" id="HAPPY11" class="smiley-radio" style="display: none;" value="Masaya" />
  <label for="HAPPY11" style="background-image: url('images/4.png'); width: 50px; height: 50px; display: inline-block; background-size: cover;margin-right:15px;cursor:pointer;"></label>

  <input type="radio" name="SQD0" id="HAPPY22" class="smiley-radio" style="display: none;" value="Sobrang saya"/>
  <label for="HAPPY22" style="background-image: url('images/5.png'); width: 50px; height: 50px; display: inline-block; background-size: cover;margin-right:15px;cursor:pointer;"></label>
  </div>


  <h1 style="font-size: 15px; margin-top: 25px; font-size: 15px; margin-left: 35px;"><strong>SQD1. Makatwiran ang oras na aking ginugol para sa pagproseso ng aking transaksyon.</strong></h1><br>
  <div class="emotion" style="margin-left: 55px;margin-top: -15px;margin-bottom: -15px;">
  <input type="radio" name="SQD1" id="SAD12" class="smiley-radio" style="display: none;" value="Dismayado" />
  <label for="SAD12" style="background-image: url('images/1.png'); width: 55px; height: 55px; display: inline-block; background-size: cover;margin-right:15px;cursor:pointer;"></label>

  <input type="radio" name="SQD1" id="SAD23" class="smiley-radio" style="display: none;" value="Malungkot"/>
  <label for="SAD23" style="background-image: url('images/2.png'); width: 50px; height: 50px; display: inline-block; background-size: cover;margin-right:15px;cursor:pointer;"></label>

  <input type="radio" name="SQD1" id="NEUTRAL12" class="smiley-radio" style="display: none;" value="Neutral"/>
  <label for="NEUTRAL12" style="background-image: url('images/3.png'); width: 50px; height: 50px; display: inline-block; background-size: cover;margin-right:15px;cursor:pointer;"></label>

  <input type="radio" name="SQD1" id="HAPPY12" class="smiley-radio" style="display: none;"  value="Masaya" />
  <label for="HAPPY12" style="background-image: url('images/4.png'); width: 50px; height: 50px; display: inline-block; background-size: cover;margin-right:15px;cursor:pointer;"></label>

  <input type="radio" name="SQD1" id="HAPPY23" class="smiley-radio" style="display: none;" value="Sobrang saya"/>
  <label for="HAPPY23" style="background-image: url('images/5.png'); width: 50px; height: 50px; display: inline-block; background-size: cover;margin-right:15px;cursor:pointer;"></label>
</div>

<h1 style="font-size: 15px; margin-top: 25px; font-size: 15px; margin-left: 35px;"><strong>SQD2. Ang opisina ay sumusunod sa mga kinakailangang dokumento at mga hakbang batay sa impormasyong ibinigay.</strong></h1><br>
  <div class="emotion" style="margin-left: 55px;margin-top: -15px;margin-bottom: -15px;">
  <input type="radio" name="SQD2" id="SAD123" class="smiley-radio" style="display: none;" value="Dismayado"/>
  <label for="SAD123" style="background-image: url('images/1.png'); width: 55px; height: 55px; display: inline-block; background-size: cover;margin-right:15px;cursor:pointer;"></label>

  <input type="radio" name="SQD2" id="SAD234" class="smiley-radio" style="display: none;" value="Malungkot" />
  <label for="SAD234" style="background-image: url('images/2.png'); width: 50px; height: 50px; display: inline-block; background-size: cover;margin-right:15px;cursor:pointer;"></label>

  <input type="radio" name="SQD2" id="NEUTRAL123" class="smiley-radio" style="display: none;" value="Neutral"/>
  <label for="NEUTRAL123" style="background-image: url('images/3.png'); width: 50px; height: 50px; display: inline-block; background-size: cover;margin-right:15px;cursor:pointer;"></label>

  <input type="radio" name="SQD2" id="HAPPY123" class="smiley-radio" style="display: none;"  value="Masaya" />
  <label for="HAPPY123" style="background-image: url('images/4.png'); width: 50px; height: 50px; display: inline-block; background-size: cover;margin-right:15px;cursor:pointer;"></label>

  <input type="radio" name="SQD2" id="HAPPY234" class="smiley-radio" style="display: none;" value="Sobrang saya" />
  <label for="HAPPY234" style="background-image: url('images/5.png'); width: 50px; height: 50px; display: inline-block; background-size: cover;margin-right:15px;cursor:pointer;"></label>
</div>

<h1 style="font-size: 15px; margin-top: 25px; font-size: 15px; margin-left: 35px;"><strong>SQD3. Ang mga hakbang sa pagproseso, kasama na ang pagbayad ay madali at simple lamang.</strong></h1><br>
  <div class="emotion" style="margin-left: 55px;margin-top: -15px;margin-bottom: -15px;">
  <input type="radio" name="SQD3" id="SAD1234" class="smiley-radio" style="display: none;"  value="Dismayado" />
  <label for="SAD1234" style="background-image: url('images/1.png'); width: 55px; height: 55px; display: inline-block; background-size: cover;margin-right:15px;cursor:pointer;"></label>

  <input type="radio" name="SQD3" id="SAD2345" class="smiley-radio" style="display: none;"  value="Malungkot"/>
  <label for="SAD2345" style="background-image: url('images/2.png'); width: 50px; height: 50px; display: inline-block; background-size: cover;margin-right:15px;cursor:pointer;"></label>

  <input type="radio" name="SQD3" id="NEUTRAL1234" class="smiley-radio" style="display: none;" value="Neutral"/>
  <label for="NEUTRAL1234" style="background-image: url('images/3.png'); width: 50px; height: 50px; display: inline-block; background-size: cover;margin-right:15px;cursor:pointer;"></label>

  <input type="radio" name="SQD3" id="HAPPY1234" class="smiley-radio" style="display: none;"  value="Masaya" />
  <label for="HAPPY1234" style="background-image: url('images/4.png'); width: 50px; height: 50px; display: inline-block; background-size: cover;margin-right:15px;cursor:pointer;"></label>

  <input type="radio" name="SQD3" id="HAPPY2345" class="smiley-radio" style="display: none;" value="Sobrang saya"/>
  <label for="HAPPY2345" style="background-image: url('images/5.png'); width: 50px; height: 50px; display: inline-block; background-size: cover;margin-right:15px;cursor:pointer;"></label>
</div>

<h1 style="font-size: 15px; margin-top: 25px; font-size: 15px; margin-left: 35px;"><strong>SQD4. Mabilis at madali akong nakahanap ng impormasyon tungkol sa aking transaksyon mula sa opisina o sa website nito.</strong></h1><br>
  <div class="emotion" style="margin-left: 55px;margin-top: -15px;margin-bottom: -15px;">
  <input type="radio" name="SQD4" id="SAD12345" class="smiley-radio" style="display: none;" value="Dismayado" />
  <label for="SAD12345" style="background-image: url('images/1.png'); width: 50px; height: 50px; display: inline-block; background-size: cover;margin-right:15px;cursor:pointer;"></label>

  <input type="radio" name="SQD4" id="SAD23456" class="smiley-radio" style="display: none;"value="Malungkot" />
  <label for="SAD23456" style="background-image: url('images/2.png'); width: 50px; height: 50px; display: inline-block; background-size: cover;margin-right:15px;cursor:pointer;"></label>

  <input type="radio" name="SQD4" id="NEUTRAL12345" class="smiley-radio" style="display: none;" value="Neutral"/>
  <label for="NEUTRAL12345" style="background-image: url('images/3.png'); width: 50px; height: 50px; display: inline-block; background-size: cover;margin-right:15px;cursor:pointer;"></label>

  <input type="radio" name="SQD4" id="HAPPY12345" class="smiley-radio" style="display: none;"  value="Masaya"/>
  <label for="HAPPY12345" style="background-image: url('images/4.png'); width: 50px; height: 50px; display: inline-block; background-size: cover;margin-right:15px;cursor:pointer;"></label>

  <input type="radio" name="SQD4" id="HAPPY23456" class="smiley-radio" style="display: none;" value="Sobrang saya"/>
  <label for="HAPPY23456" style="background-image: url('images/5.png'); width: 50px; height: 50px; display: inline-block; background-size: cover;margin-right:15px;cursor:pointer;"></label>
</div>

<h1 style="font-size: 15px; margin-top: 25px; font-size: 15px; margin-left: 35px;"><strong>SQD5. Nagbayad ako ng makatwirang halaga para sa aking transaksyon. (Kung ang sebisyo ay ibinigay ng libre. Piliin ang N/A.)</strong></h1><br>
  <div class="emotion" style="margin-left: 55px;margin-top: -15px;margin-bottom: -15px;">
  <input type="radio" name="SQD5" id="SAD123456" class="smiley-radio" style="display: none;" value="Dismayado"/>
  <label for="SAD123456" style="background-image: url('images/1.png'); width: 50px; height: 50px; display: inline-block; background-size: cover;margin-right:15px;cursor:pointer;"></label>

  <input type="radio" name="SQD5" id="SAD234567" class="smiley-radio" style="display: none;" value="Malungkot"/>
  <label for="SAD234567" style="background-image: url('images/2.png'); width: 50px; height: 50px; display: inline-block; background-size: cover;margin-right:15px;cursor:pointer;"></label>

  <input type="radio" name="SQD5" id="NEUTRAL123456" class="smiley-radio" style="display: none;" value="Neutral" />
  <label for="NEUTRAL123456" style="background-image: url('images/3.png'); width: 50px; height: 50px; display: inline-block; background-size: cover;margin-right:15px;cursor:pointer;"></label>

  <input type="radio" name="SQD5" id="HAPPY123456" class="smiley-radio" style="display: none;"  value="Masaya"/>
  <label for="HAPPY123456" style="background-image: url('images/4.png'); width: 50px; height: 50px; display: inline-block; background-size: cover;margin-right:15px;cursor:pointer;"></label>

  <input type="radio" name="SQD5" id="HAPPY234567" class="smiley-radio" style="display: none;" value="Sobrang saya" />
  <label for="HAPPY234567" style="background-image: url('images/5.png'); width: 50px; height: 50px; display: inline-block; background-size: cover;margin-right:15px;cursor:pointer;"></label>

  <input type="radio" name="SQD5" id="NA" class="smiley-radio" style="display: none;" value="N/A" />
  <label for="NA" style="background-image: url('images/6.png'); width: 55px; height: 54px; display: inline-block; background-size: cover;margin-right:15px;cursor:pointer;"></label>

</div>

<h1 style="font-size: 15px; margin-top: 25px; font-size: 15px; margin-left: 35px;"><strong>SQD6. Pakiramdam ko ay patas ang opisina sa lahat. o walang palakasan", sa aking transaksyon</strong></h1><br>
  <div class="emotion" style="margin-left: 55px;margin-top: -15px;margin-bottom: -15px;">
  <input type="radio" name="SQD6" id="SAD1234567" class="smiley-radio" style="display: none;" value="Dismayado"/>
  <label for="SAD1234567" style="background-image: url('images/1.png'); width: 50px; height: 50px; display: inline-block; background-size: cover;margin-right:15px;cursor:pointer;"></label>

  <input type="radio" name="SQD6" id="SAD2345678" class="smiley-radio" style="display: none;" value="Malungkot" />
  <label for="SAD2345678" style="background-image: url('images/2.png'); width: 50px; height: 50px; display: inline-block; background-size: cover;margin-right:15px;cursor:pointer;"></label>

  <input type="radio" name="SQD6" id="NEUTRAL1234567" class="smiley-radio" style="display: none;" value="Neutral" />
  <label for="NEUTRAL1234567" style="background-image: url('images/3.png'); width: 50px; height: 50px; display: inline-block; background-size: cover;margin-right:15px;cursor:pointer;"></label>

  <input type="radio" name="SQD6" id="HAPPY1234567" class="smiley-radio" style="display: none;"   value="Masaya"/>
  <label for="HAPPY1234567" style="background-image: url('images/4.png'); width: 50px; height: 50px; display: inline-block; background-size: cover;margin-right:15px;cursor:pointer;"></label>

  <input type="radio" name="SQD6" id="HAPPY2345678" class="smiley-radio" style="display: none;" value="Sobrang saya" />
  <label for="HAPPY2345678" style="background-image: url('images/5.png'); width: 50px; height: 50px; display: inline-block; background-size: cover;margin-right:15px;cursor:pointer;"></label>
</div>

<h1 style="font-size: 15px; margin-top: 25px; font-size: 15px; margin-left: 35px;"><strong>SQD7. Magalang akong trinato ng mga tauhan, at (kung sakali ako ay humingi ng tulong) alam ko na sila ay handang tumulong sa akin.</strong></h1><br>
  <div class="emotion" style="margin-left: 55px;margin-top: -15px;margin-bottom: -15px;">
  <input type="radio" name="SQD7" id="SAD12345678" class="smiley-radio" style="display: none;" value="Dismayado"/>
  <label for="SAD12345678" style="background-image: url('images/1.png'); width: 50px; height: 50px; display: inline-block; background-size: cover;margin-right:15px;cursor:pointer;"></label>

  <input type="radio" name="SQD7" id="SAD234567890" class="smiley-radio" style="display: none;"  value="Malungkot"/>
  <label for="SAD234567890" style="background-image: url('images/2.png'); width: 50px; height: 50px; display: inline-block; background-size: cover;margin-right:15px;cursor:pointer;"></label>

  <input type="radio" name="SQD7" id="NEUTRAL12345678"class="smiley-radio"  style="display: none;" value="Neutral" />
  <label for="NEUTRAL12345678" style="background-image: url('images/3.png'); width: 50px; height: 50px; display: inline-block; background-size: cover;margin-right:15px;cursor:pointer;"></label>

  <input type="radio" name="SQD7" id="HAPPY12345678" class="smiley-radio"  style="display: none;"  value="Masaya" />
  <label for="HAPPY12345678" style="background-image: url('images/4.png'); width: 50px; height: 50px; display: inline-block; background-size: cover;margin-right:15px;cursor:pointer;"></label>

  <input type="radio" name="SQD7" id="HAPPY23456789" class="smiley-radio" style="display: none;"  value="Sobrang saya"/>
  <label for="HAPPY23456789" style="background-image: url('images/5.png'); width: 50px; height: 50px; display: inline-block; background-size: cover;margin-right:15px;cursor:pointer;"></label>
</div>

<h1 style="font-size: 15px; margin-top: 25px; font-size: 15px; margin-left: 35px;"><strong>SQD8. Nakuha ko ang kinakallangan ko mula sa tanggapan ng gobyemo. kung tinanggihan man. Ito ay sapat na ipinaliwanag sa akin.</strong></h1><br>
  <div class="emotion" style="margin-left: 55px;margin-top: -15px;margin-bottom: -15px;">
  <input type="radio" name="SQD8" id="SAD123456789" class="smiley-radio" style="display: none;"value="Dismayado" />
  <label for="SAD123456789" style="background-image: url('images/1.png'); width: 50px; height: 50px; display: inline-block; background-size: cover;margin-right:15px;cursor:pointer;"></label>

  <input type="radio" name="SQD8" id="SAD2345678901" class="smiley-radio" style="display: none;" value="Malungkot"/>
  <label for="SAD2345678901" style="background-image: url('images/2.png'); width: 50px; height: 50px; display: inline-block; background-size: cover;margin-right:15px;cursor:pointer;"></label>

  <input type="radio" name="SQD8" id="NEUTRAL123456789" class="smiley-radio" style="display: none;" value="Neutral"/>
  <label for="NEUTRAL123456789" style="background-image: url('images/3.png'); width: 50px; height: 50px; display: inline-block; background-size: cover;margin-right:15px;cursor:pointer;"></label>

  <input type="radio" name="SQD8" id="HAPPY123456789" class="smiley-radio" style="display: none;"  value="Masaya"/>
  <label for="HAPPY123456789" style="background-image: url('images/4.png'); width: 50px; height: 50px; display: inline-block; background-size: cover;margin-right:15px;cursor:pointer;"></label>

  <input type="radio" name="SQD8" id="HAPPY234567890" class="smiley-radio" style="display: none;" value="Sobrang saya" />
  <label for="HAPPY234567890" style="background-image: url('images/5.png'); width: 50px; height: 50px; display: inline-block; background-size: cover;margin-right:15px;cursor:pointer;"></label>
</div>
<br>


    
  <div class="input-box">
    <h1 style="font-size: 15px; margin-top: -10px; font-size: 15px;"><strong>Comments/Suggestions for the improvement of the services. (Mga Komento/Suhestiyon para sa pagpapabuti ng Serbisyo)</strong></h1>
    <textarea name="comments"></textarea>
  </div>
  <button type="submit" name="submit" style="margin-left:250px; height:40px; background:green; color:white; width:100px">Submit</button>
</form>
   
    <script>
 

   
       
let subMenu= document.getElementById("subMenu");
        function toggleMenu(){
            subMenu.classList.toggle("open-menu");
   }
   /*
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
    }*/
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
