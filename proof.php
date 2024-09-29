<?php session_start();
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

if (isset($_POST['beneid'])) {
    $beneid = $_POST['beneid'];
    $res_date = $_POST['rdate'];
    $res_time = $_POST['rtime'];
    $res_Assist = $_POST['assist'];

    // Fetch proofGL file path from database
    $query = "SELECT proofGL FROM history WHERE Beneficiary_ID = '$beneid' AND ReceivedDate = '$res_date' AND ReceivedTime = '$res_time' AND AssistanceType = '$res_Assist'";
    $result = mysqli_query($con, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $file = $row['proofGL'];
       
    } else {
        die("Proof file not found for the given beneficiary.");
    }
    $query = "SELECT * FROM beneficiary WHERE Beneficiary_ID = '$beneid' ";
    $result = mysqli_query($con, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row2 = mysqli_fetch_assoc($result);
        $lastname = $row2['Lastname'];
        $firstname = $row2['Firstname'];
} 
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title> Patient's Records </title>
<link rel="stylesheet" href="employeeRecords.css"/>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-
awesome/6.4.0/css/all.min.css"/>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

</head>
<body>
<div class="sidebar">
<div class="logo"></div>

<ul class="menu">
<li>
<a href="#" onclick="dashboard()">
<i class="fas fa-tachometer-alt"> </i>
<span> Dashboard </span>
</a>
</li>
<li >
<a href="#" onclick="records()">
<i class="fas fa-chart-bar"> </i>
<span> Beneficiary's Records </span>
</a>
</li>
<li>
<a href="#" onclick="assistance()">
<i class="fas fa-handshake-angle"> </i>
<span> Financial Assistance </span>
</a>
</li>
<li>
<a href="#" onclick="hospital()">
<i class="fas fa-hospital"> </i>
<span> Hospitals </span>
</a>
</li>
<li>
<a href="#" onclick="medicines()">
<i class="fa-solid fa-capsules"></i>
<span>Medicines</span>
</a>
</li>
<li>
    <a href="#" onclick="laboratories()">
        <i class="fa-solid fa-flask-vial"></i>
            <span>Laboratories</span>
</a>
    </li>
<?php if ($role === 'Admin'): ?>
            <li class="active">
                <a href="#" onclick="employees()">
                    <i class="fas fa-users"></i>
                    <span>Employees</span>
                </a>
            </li>
            
        <?php endif; ?>
        <li class="user" >
            <a href="#" onclick="profile()">
                    <i class="fas fa-user"></i>
                                    
                <span>Profile</span>
                <input type="hidden" name="Emp_ID" value="<?php echo "{$resEmp_ID['Emp_ID']}"; ?>">
                </a>
            </li>
            <li class="logout">
                <a href="#" onclick="logout()">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </li>
</ul>

</div>
<div class="main--content">
<div class="header--wrapper">
<div class="header--title">
<span> 1Bataan Malasakit - Special Assistance Program </span>
<h2> Uploaded File</h2>
</div>
<div id="currentDate"></div>
<div class="user--info">
<img src="images/background.png" alt=""/>
</div>
</div>
<div class="tabular--wrapper">
<div class="card--container">
<h3 class="main--title"> Uploaded File
</h3>
</div>
<div class="container mt-5">
    <input type="hidden" name="beneid" value="<?php echo $res_ID; ?>">
<input type="hidden" name="assist" value="<?php echo $res_Assist; ?>">
<input type="hidden" name="rdate" value="<?php echo $res_date; ?>">
<input type="hidden" name="rtime" value="<?php echo $res_time; ?>">
<?php
// Set the timezone to Manila
date_default_timezone_set('Asia/Manila');

// Assuming $res_time is in 'H:i:s' format (24-hour format)
$res_time = '14:30:00'; // Example value

// Create a DateTime object with the given time
$dateTime = new DateTime($res_time);

// Format the time in 12-hour format with AM/PM
$formattedTime = $dateTime->format('h:i A'); // e.g., 02:30 PM
?>
        <h2>Proof of Assistance for : <?php echo $firstname . " " . $lastname; ?>  </h2>
        <h5>Assistance: <?php echo $res_Assist; ?></h5>
        <h5>Received Date: <?php echo $res_date; ?></h5>
        <h5>Received Time: <?php echo $formattedTime; ?></h5>
        <?php if (!empty($file)): ?>
            <div class="mt-3">
                
                <img src="proofGL/<?php echo $file; ?>" alt="Proof Image" style="max-width: 100%; height: auto;">
            </div>
        <?php else: ?>
            <p>No proof has been uploaded for this beneficiary.</p>
        <?php endif; ?>
        <a href="historyproof.php" class="btn btn-primary mt-3">Go Back</a>
    </div>

                    

<script type="text/javascript">
function dashboard() {
window.location ="http://localhost/public_html/dashboard.php"; }
function records() {

window.location = "http://localhost/public_html/patients-records.php";

}
function assistance() {
window.location ="http://localhost/public_html/assistance.php";
}
function hospital() {
window.location ="http://localhost/public_html/hospital.php";
}
function medicines() {
window.location ="http://localhost/public_html/medicines.php";
}
function laboratories() {
window.location = "http://localhost/public_html/laboratories.php";
    }
function employees(){
        window.location = "http://localhost/public_html/employeeRecords.php"
    }

function profile() {
        window.location = "http://localhost/public_html/profileadmin.php";
    }
    function logout() {
    var confirmation = confirm("Are you sure you want to Logout?");
    if (confirmation) {
        // If user clicks OK, set the value to "yes"
        document.getElementById("confirmed").value = "yes";
        // Redirect the user
        window.location.href = "http://localhost/public_html/logoutemp.php";
    } else {
        // If user cancels, set the value to "no"
        document.getElementById("confirmed").value = "no";
    }
}

</script>
</body>
</html>