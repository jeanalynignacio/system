<?php
 session_start();

 include("php/config.php");
 use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

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
                 $res_Id = $result['Id'];
            }

    }

    
    
  $SQL="SELECT * FROM beneficiary WHERE Representative_ID='$id'";

  $result=mysqli_query($con,$SQL);
   $res_ID= $result->fetch_assoc();
   


   if(isset($_POST['submit'])){
    $Lastname = $_POST['Lastname'];
    $Firstname = $_POST['Firstname'];
   $reason =$_POST['reason'];
   $beneID =$_POST['ID'];
$Email=$_POST['email'];
   $query = "UPDATE transaction 
   SET Status = 'Request for Re-schedule'
   WHERE Beneficiary_Id = '$beneID'";
   $result2 = mysqli_query($con, $query);
   if ($result2) {
    require 'PHPMailer/src/Exception.php';
    require 'PHPMailer/src/PHPMailer.php';
    require 'PHPMailer/src/SMTP.php';
   
    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'bataanpgbsap@gmail.com'; // Your Gmail address
        $mail->Password = 'cmpp hltn mxuc tcgl'; // Your Gmail password or App Password
        $mail->SMTPSecure = 'PHPMailer::ENCRYPTION_STARTTLS';
        $mail->Port = 587;
    
        //Recipients
        $mail->setFrom($Email); 
        $mail->addAddress ('bataanpgbsap@gmail.com', 'PGB-SAP');      
        //Content
        $mail->isHTML(true); // Set email format to HTML
        $mail->Subject = 'Request for Re-Schedule';
        $mail->Body =  "<html><body><p>$reason</p></body></html>";
    
       $mail->send();
    
      echo '<body>
      <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
      <script>
      swal("Email sent successfully!", "Please wait for an email notification to know if your request is accepted or not.", "success")
      .then((value) => {
          if (value) {
              window.location.href = "usershomepage.php";
          }
      });
      </script>
      </body>';
    
  }catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
      
    }
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
            <input name="Lastname"
              type="text"
              placeholder="Enter last name"
              required value="<?php echo "{$res_ID['Lastname']}"; ?>"
              style="color: black; background: white; border-color: gray"
            />
          </div>

          <div class="input-box">
            <label>First Name</label>
            <input  name="Firstname"
              type="text"
              placeholder="Enter First name"
              required value="<?php echo "{$res_ID['Firstname']}"; ?>"
              style="color: black; background: white; border-color: gray"
            />
            <input  name="email"
              type="hidden"
              value="<?php echo "{$res_ID['Email']}"; ?>"
                 />
                 <input  name="ID"
              type="hidden"
              value="<?php echo "{$res_ID['Beneficiary_Id']}"; ?>"
                 />
          </div>
        </div>

        <div class="input-box">
          <label>Reason for re-scheduling</label>
          <textarea name="reason" required> </textarea>
        </div>

        <div class="column">
          <button  class="btn" name="submit" >Send Request</button>
         
        </div>
      </form>
    </section>
  </body>
</html>
