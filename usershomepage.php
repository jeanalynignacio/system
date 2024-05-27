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
      $res_profile = $result['userIDpic'];
}
      }
            
                 
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>1BM - Special Assistance Program</title>
    <link rel="stylesheet" href="usershomepage.css" />
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC"
      crossorigin="anonymous"
    />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    />
    </head>

  <body>
    

    <div class="all-content">
      <!-- navbar !-->

      <nav
        class="navbar navbar-expand-lg navbar-light"
        style="background-color: #1477d2"
      >
        <div class="container-fluid">
          <a
            class="navbar-brand"   href="#" id="logo"  style="font-size: 15px; color: white">
            <img src="images/background.png" /> Provincial Government of Bataan- Special Assistance Program
          </a>
          <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent"
            aria-expanded="false"
            aria-label="Toggle navigation"
          >
            <span>
              <i
                class="fa-solid fa-bars"
                style="color: white; font-size: 23px"
              ></i
            ></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
              <li class="nav-item">
                <a style = " color: white; padding-left:10px;" class="nav-link" aria-current="page" href="#">
                     Home 
                </a>
              </li>
              <li class="nav-item">
                <a
                  class="nav-link"
                  href="#Services"
                  onclick="showServices('header')"
                  style="color: white; padding-left:10px;"
                >
                  Services Available
                </a>
              </li>
            
              
             <li class="nav-item">
                <a style = " color: white; padding-left:10px;" class="nav-link"  onclick="toggleMenu()" style="color: white" >Profile </a>
              </li>

<div class="sub-menu-wrap" id="subMenu">
    <div class="sub-menu">
        <div class="user-info">
             <img src="profile_images/<?php echo $res_profile; ?>" style="height: 50px; width: 50px;" alt="fas fa-user">

            <h2><?php echo $res_Fname; ?>, <?php echo $res_Lname; ?></h2>
  
            </div>
            <hr>

   

<form action="edit.php" method="POST" class="sub-menu-link">
    <input type="hidden" name="userId" value="<?php echo $res_Id; ?>">
    <button type="submit" class="btn-edit-profile" >
        <img src="images/profile.png">
        <p>Edit Profile</p>
        
    </button>
</form>
   
        <a href="logout.php" class="sub-menu-link">
                <img src="images/logout.png">
                <p> Log out</p>
           
        </a>
            
             </ul>
          </div>
        </div>
      </nav>
      <!-- navbar end -->
      <!-- home section -->
      
<p class="welcome-message">Hello and welcome to PGB-SAP, <?php echo $res_Fname; ?>.</p>


      <div id="home">
      <div class="w3-centerw3-section" >
        <img class="mySlides" src="images/sap1.png" style="width: auto" height="1000px">
        <img class="mySlides" src="images/sap2.png" style="width: auto" height="1000px">
        <img class="mySlides" src="images/sap3.jpg" style="width: auto" height="1000px">
        <img class="mySlides" src="images/sap4.png" style="width: auto" height="1000px">
      </div>

        <h3>PGB - Special Assistance Program</h3>
        <p>
          With the help of this project, social assistance programs in the
          province should be more effective in their distribution and recipients
          will be better targeted. With this law, the Provincial Government of
          Bataan underlines its dedication to meeting the needs of its citizens,
          especially those who need the most financial support for medical care.
          Our assistance program offers a range of crucial support services,
          including financial assistance through AICS, Eposino and Dialyser for
          Dialysis patients, medical assistance, guarantee letters for hospital
          bills and laboratories
        </p>
      </div>

      <div class="header" id="header">
        <h3>Available Services offered by PGB-SAP</h3>
      </div>

      <div class="services">
           
        <div class="service">
          <img
            src="images/burial.png"
            style="
              width: 80px;
              height: auto;
              margin-right: 5px;
              margin-top: 30px;
              margin-bottom: -30px;
            "
          />
          <h3>Burial Assistance</h3>
          <p>
            Funeral and cremation expenses can be high for some people, and
            burial assistance prpograms are meant to help them pay for their
            loved ones' funerals and burials. Helping with money during what is
            often a hard and stressful time is the goal of this service. 
          </p>
       
        </div>

        <div class="service">
          <img
            src="images/dialysis.png"
            style="
              width: 80px;
              height: auto;
              margin-top: 20px;
              margin-bottom: -20px;
            "
          />
          <h3>Dialysis Patients</h3>

          <p style="font-size: 12.5px">
            The SAP's help for dialysis patients is essential for people with
            kidney failure because it makes sure they get the care they need
            without having to worry about the high expenses of dialysis. This
            service aims to enhance their quality of life, ease their financial
            burdens, and guarantee their ongoing access to medical treatment
            that they needed.
          </p>
          </div>

        <div class="service">
          <img
            src="images/hospital-bills.png"
            style="
              width: 72px;
              height: auto;
              margin-top: 20px;
              margin-bottom: -15px;
            "
          />
          <h3>Hospital Bills</h3>
          <p style="font-size: 13px">
            The service goal is to reduce hospital costs so that people can get
            the medical treatment they need without worrying about money. The
            service that can help with hospital bills is a support for people
            and families in the Philippines who are having a hard time paying
            for medical treatment and hospitalizations in hospitals.
          </p>
         
        </div>

        <div class="service">
          <h3>
            Implant <br />
            (Bakal)
          </h3>
          <p>
            Driven by a dedication to enhancing quality of life and promoting
            inclusivity, this program provides a glimmer of hope to individuals
            who may otherwise lack the financial means to access steel implant
            medical operations. 
          </p>
        
        </div>

        <div class="service">
          <img
            src="images/labs.png"
            style="
              width: 80px;
              height: auto;
              margin-top: 10px;
              margin-bottom: -30px;
            "
          />
          <h3>Laboratories</h3>
          <p style="font-size: 13px">
            The financial aid offered by PGB-SAP enables the citizens of Bataan
            to avail high-quality healthcare services at reduced costs, hence
            assuring better health conditions for the community. Minimizing the
            costs connected with testing in laboratories is important for
            enhancing the accessibility and cost-efficiency of healthcare.
          </p>
         
        </div>

        <div class="service">
          <img
            src="images/medicines.png"
            style="
              width: 80px;
              height: auto;
              margin-top: 15px;
              margin-bottom: -8px;
            "
          />
          <h3>Medicines</h3>
          <p>
            This service can help people and families who need medications but
            are unable to afford them. This assistance ensures that financial
            constraints do not prevent individuals from accessing necessary
            medications.
          </p>
          
        </div>

        <div class="service">
          <img
            src="images/r&chemo.png"
            style="
              width: 80px;
              height: auto;
              margin-top: 5px;
              margin-bottom: -30px;
            "
          />
          <h3>
            Radiation & <br />
            Chemotherapy
          </h3>
          <p style="font-size: 12px; margin-top: -15px">
            Medicines, treatments, and hospitalizations can be very expensive
            during cancer treatment, especially radiation therapy and
            chemotherapy. The goal of the program is to make cancer treatment
            more accessible so that patients can get the radiation therapy and
            medicines they need without having to worry about financial
            problems.
          </p>
      
        </div>
      </div>
       <center> <button class="btn1" onclick="window.location.href = 'applyingoptions.php';">Apply for Assistance</button></center>  
    </div>
    
<h1 id = "partnered"> Partnered Hospitals inside Bataan </h1>

<div class="insideHospital">
    <div class="hospitals">
      <img src = "images/bdhmc.jpg" alt="" class="card-image">
      <h3> Bataan Doctor's Hospital & Medical Center </h3>
    </div>

  <div class="hospitals">
    <img src = "images/bmcc.png" alt="" class="card-image">
    <h3> Balanga Medical Center Corporation </h3>
  
  </div>

  <div class="hospitals">
    <img src = "images/bpmc.png" alt="" class="card-image">
    <h3> Bataan Peninsula Medical Center </h3>
  </div>

  <div class="hospitals">
    <img src = "images/bsjmc.jpg" alt="" class="card-image">
    <h3> Bataan St. Joseph Hospital & Medical Center </h3>
  </div>

  <div class="hospitals">
    <img src = "images/icmc.jpg" alt="" class="card-image">
    <h3> Isaac & Catalina Medical Center </h3>
  </div>

  <div class="hospitals">
    <img src = "images/mtsamat.png" alt="" class="card-image">
    <h3> Mt. Samat Medical Center </h3>
  </div>

  <div class="hospitals">
    <img src = "images/bmcc.png" alt="" class="card-image">
    <h3> Orion St. Michael Hospital </h3>
  </div>
</div>

<h1 class ="head2"> Partnered Hospitals outside Bataan </h1>

<div class="outsideHospital">
    <div class="hospitals">
      <img src = "images/jbl.jpg" alt="" class="card-image">
      <h3> Jose B. Lingad Memorial General Hospital </h3>
    </div>

  <div class="hospitals">
    <img src = "images/lcotp.png" alt="" class="card-image">
    <h3> Lung Center of the Philippines </h3>
  </div>

  <div class="hospitals">
    <img src = "images/nch.jpg" alt="" class="card-image">
    <h3> National Children's Hospital </h3>
  </div>

  <div class="hospitals">
    <img src = "images/nkti.png" alt="" class="card-image">
    <h3> National Kidney & Transplant Institute </h3>
  </div>

  <div class="hospitals">
    <img src = "images/pgh.png" alt="" class="card-image">
    <h3> Philippine General Hospital </h3>
  </div>

  <div class="hospitals">
    <img src = "images/phc.jfif" alt="" class="card-image">
    <h3> Philippine Heart Center </h3>
  </div>

  <div class="hospitals">
    <img src = "images/pcmc.jpg" alt="" class="card-image">
    <h3> The Philippines Children Medical Center </h3>

  </div>
</div>

<div class="footer" id=footer>
        <h1> Help us improve the PGB-SAP <br/> <input type="button" value="Answer Survey Here" class="styled"/></h1>
         <div class="Location">
           <div class="content1">
           <img src="images/logo-png.png"/>
           <h3> <i class="fa-solid fa-location-dot" style = "color: #1477d2;"></i> LOCATION </h3>
           <p> Bulwagan, Capitol Grounds</p>
           <p> Balanga City, Bataan </p>
           <p> <i class="fa-solid fa-phone" style = "color: #1477d2;"> </i> 0998 562 7784 </p>
          <p>
    <a href="https://www.facebook.com/pgo.sap/" style="color: #1477d2;">
       <i class="fa-brands fa-facebook" style = "color: #1477d2;" ></i> Province of Bataan - Special Assistance Program
    </a>
</p>

              
           <p>All content is in the public unless otherwise started</p>
       </div>
    </div>

<script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
      crossorigin="anonymous" ></script>

   
       
    <!-- home section end -->
    <script >



      function showServices(header) {
        var offeredservices = document.getElementById(header);
        if (offeredservices) {
          offeredservices.scrollIntoView({ behavior: "smooth" });
        }
      }

      function showHospitals(partnered) {
        var hospitals = document.getElementById(partnered);
        if (hospitals) {
          hospitals.scrollIntoView({ behavior: "smooth" });
        }
      }
      
   
        var myIndex= 0;
carousel();

function carousel() {
    var i;
    var x = document.getElementsByClassName("mySlides");
    for(i = 0; i< x.length; i++){
        x[i].style.display = "none";
    }
    myIndex++;
    if (myIndex > x.length) {myIndex = 1}
    x[myIndex-1].style.display = "block";
    setTimeout(carousel, 2000);//Change image every 2 seconds
}
function showServices(header) {
var offeredservices = document.getElementById(header);
if (offeredservices) {
  offeredservices.scrollIntoView({behavior: 'smooth'})
}
}

function showHospitals(partnered) {
var hospitals = document.getElementById(partnered);
if (hospitals) {
  hospitals.scrollIntoView({behavior: 'smooth'})
}
}
function showContact(footer) {
var contact = document.getElementById(footer);
if (contact) {
  contact.scrollIntoView({behavior: 'smooth'})
}
}
let subMenu= document.getElementById("subMenu");
        function toggleMenu(){
            subMenu.classList.toggle("open-menu");
   }



    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>


  </body>
</html>
