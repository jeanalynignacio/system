<?php
 session_start();

 include("php/config.php");
 if(!isset($_SESSION['valid'])){
     header("Location: index.php");

 }
 if(isset($_POST['Back'])){
     header("Location: usershomepage.php");
    exit;
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
?>
<!DOCTYPE html>
<!---Coding By CodingLab | www.codinglabweb.com--->
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <!---Custom CSS File--->
    <link rel="stylesheet" href="requestresched.css" />
  </head>
  <body>
    <section class="container">
      <header>Request Re-schedule</header>
      <form action="#" class="form" method= "POST">
        <div class="column">
          <div class="input-box">
            <label>Last Name</label>
            <input
              type="text"
              placeholder="Enter last name"
              required value="<?php echo "{$res_Lname['Lastname']}"; ?>"
              style="color: black; background: white; border-color: gray"
            />
          </div>

          <div class="input-box">
            <label>First Name</label>
            <input
              type="text"
              placeholder="Enter First name"
              required
              style="color: black; background: white; border-color: gray"
            />
          </div>
        </div>

        <div class="input-box">
          <label>Reason for re-scheduling</label>
          <textarea required> </textarea>
        </div>

        <div class="column">
          <button>Send Request</button>
        </div>
      </form>
    </section>
  </body>
</html>
