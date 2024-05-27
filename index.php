<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title> PGB - Special Assistance Program </title>
    <link rel="stylesheet" href="overview.css" />
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC"
      crossorigin="anonymous"
    />
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
         style="background-color: white;"
      >
        <div class="container-fluid">
          <a class="navbar-brand" href="#" id="logo" style = "font-size: 13px; color: #1477d2;">
            <img src="images/background.png" /> Provincial Government of Bataan - Special Assistance Program </a>
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
                    style="color: #1477d2; font-size: 23px"></i
            ></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
              <li class="nav-item">
               <a style = " color: #1477d2;" class="nav-link" aria-current="page" href="#">
                  Home
                </a>
              </li>
               <li class="nav-item">
                <a style = " color: #1477d2;" class="nav-link" href="#Services"  onclick="showServices('header')" > Services Available </a>
              </li>
              <li class="nav-item">
                <a style = " color: white;" class="nav-link" href="#PartneredHospitals" onclick="showHospitals('partnered')"> Partnered Hospitals </a>
              </li>
              <li class="nav-item">
                <a style = " color: #1477d2;" class="nav-link" href="#footer" onclick="showContact('footer')"> Contact </a>
              </li>
              <li class="nav-item">
              <a style = " color: #1477d2;" class="nav-link" href="register.php"> Sign Up </a>
              </li>
              <li class="nav-item">
              <a style = " color: #1477d2;" class="nav-link" href="index.php"> Log in </a>
              </li>
            </ul>
          </div>
        </div>
      </nav>
      <!-- navbar end -->

      <!-- home section -->
    <div id="home">
          <h3> PGB - Special Assistance Program</h3>
          <p>
            With the help of this project, social assistance programs in the province should be more effective in their distribution and recipients will be better targeted. With this law, the Provincial Government of Bataan underlines its dedication to meeting the needs of its citizens, especially those who need the most financial support for medical care.  Our assistance program offers a range of crucial support services, including financial assistance through AICS, Eposino and Dialyser for Dialysis patients, medical assistance, guarantee letters for
                 hospital bills and laboratories
          </p>

      <div class="card-container">
        <h3> Offices around Bataan </h3>
        <p>
          These satellite offices are established with the sole purpose of
          providing accessible assistance to all Bataeños of our province. With
          these satellite offices in place, we aim to ensure that every Bataeño
          has convenient access to the assistance and services they require,
          fostering a stronger, more connected community.
        </p>
        <div class="card">
          <img src="images/balanga-office.png" />
          <div class="card-content">
            <h3> Balanga Office </h3>
            <p>
              Location: <br />
              Capitol Compound, Balanga City
            </p>
            <p>
              Call Center Hotline: <br />
              0998-562-7784
            </p>
          </div>
        </div>

        <div class="card">
          <img src="images/dinalupihan-office.png" />
          <div class="card-content">
            <h3>Dinalupihan Office</h3>
            <p>Location: <br />Tala St. Ramon, Dinalupihan Bataan</p>
            <p>
              Call Center Hotline: <br />
              0919-006-5181
            </p>
          </div>
        </div>

        <div class="card">
          <img src="images/hermosa-office.png" />
          <div class="card-content">
            <h3>Hermosa Office</h3>
            <p>Lorem ipsum dolor sit etc etc</p>
          </div>
        </div>

        <div class="card">
          <img src="images/mariveles-office.png" />
          <div class="card-content">
            <h3>Mariveles Office</h3>
            <p>
              Location: <br />
              Malasakit Center Avenue of the Philippines AFAB, Mariveles Bataan
            </p>
            <p>
              Call Center Hotline: <br />
              0998-869-1458
            </p>
          </div>
        </div>
      </div>
    </div>

    <div class="header" id = "header">
       <h3> Services Offered by PGB-SAP </h3>
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
              " />
        <h3> Burial Assistance </h3>
        <p>Funeral and cremation expenses can be high for some people, and burial assistance prpograms are meant to help them pay for their loved ones' funerals and burials. Helping with money during what is often a hard and stressful time is the goal of this service.  </p>
      </div>
      
      <div class="service">
        <img
              src="images/dialysis.png"
              style="width: 80px; height: auto; margin-top: 20px; margin-bottom: -20px;"
            />
        <h3> Dialysis Patients </h3>
        <p> The SAP's help for dialysis patients is essential for people with kidney failure because it makes sure they get the care they need without having to worry about the high expenses of dialysis. This service aims to enhance their quality of life, ease their financial burdens, and guarantee their ongoing access to medical treatment that they needed. </p>
      </div>

      <div class="service">
        <img
              src="images/hospital-bills.png"
              style="width: 72px; height: auto; margin-top: 20px; margin-bottom: -10px;"
            /> 
        <h3> Hospital Bills </h3>
        <p>The service goal is to reduce hospital costs so that people can get the medical treatment they need without worrying about money. The service that can help with hospital bills is a support for people and families in the Philippines who are having a hard time paying for medical treatment and hospitalizations in hospitals. </p>
      </div>

      <div class="service">
        <h3> Implant <br> (Bakal) </h3>
        <p> Driven by a dedication to enhancing quality of life and promoting inclusivity, this program provides a glimmer of hope to individuals who may otherwise lack the financial means to access steel implant medical operations.  </p>
      </div>

      <div class="service">
        <img
              src="images/labs.png"
              style="
                width: 80px;
                height: auto;
                margin-top: 10px; 
                margin-bottom: -30px;
              " />
        <h3> Laboratories </h3>
        <p> The financial aid offered by PGB-SAP enables the citizens of Bataan to avail high-quality healthcare services at reduced costs, hence assuring better health conditions for the community. Minimizing the costs connected with testing in laboratories is important for enhancing the accessibility and cost-efficiency of healthcare.  </p>
      </div>

      <div class="service">
        <img
              src="images/medicines.png"
              style="width: 80px; height: auto;  margin-top: 15px; 
                margin-bottom: -8px;"
            />
        <h3> Medicines </h3>
        <p> This service can help people and families who need medications but are unable to afford them. This assistance ensures that financial constraints do not prevent individuals from accessing necessary medications. </p>
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
        <h3> Radiation & <br> Chemotherapy </h3>
        <p> Medicines, treatments, and hospitalizations can be very expensive during cancer treatment, especially radiation therapy and chemotherapy. The goal of the program is to make cancer treatment more accessible so that patients can get the radiation therapy and medicines they need without having to worry about financial problems. </p>
      </div>
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
<!-- home section end -->
<script type = "text/javascript">
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
</script>
</body>

</html>
