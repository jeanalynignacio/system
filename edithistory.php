<?php
  session_start();
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
    
    header("Location: login.php");
}
if(isset($_POST['beneid'])) {
    $beneid = $_POST['beneid'];
    $res_date= $_POST['rdate'];
    $res_time= $_POST['rtime'];
    $res_Assist= $_POST['assist'];
 
}
 else {
    echo "User ID is not set.";
}

$query = mysqli_query($con, "SELECT * FROM history WHERE Beneficiary_ID=$beneid");

if($result = mysqli_fetch_assoc($query)){
$res_ID = $result['Beneficiary_ID'];
$res_date2 = $result['ReceivedDate'];
$res_time2 = $result['ReceivedTime'];
$res_Assist2= $result['AssistanceType'];

}

$query="SELECT * FROM employees where role='Community Affairs Officer'";
        $result = mysqli_query($con, $query);




// Get the current date in the format YYYY-MM-DD
$currentDate = date("Y-m-d");


// Define pagination variables
$records_per_page = 10; // Number of records to display per page
$current_page = isset($_GET['page']) ? $_GET['page'] : 1; // Get current page number, default to 1 if not set

// Calculate LIMIT and OFFSET
$offset = ($current_page - 1) * $records_per_page;


$sql = "SELECT COUNT(*) AS totalEntries FROM employees where role='Community Affairs Officer' ";
$result = $con->query($sql);

if (!$result) {
    die("Invalid query: " . $con->error);
}

// Fetch the result
$row = $result->fetch_assoc();
$totalEntries = $row['totalEntries'];


$recordsPerPage = 10;

$totalPages = ceil($totalEntries / $recordsPerPage);
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($currentPage - 1) * $recordsPerPage;
$sql = "SELECT * FROM employees where role='Community Affairs Officer'  LIMIT $recordsPerPage OFFSET $offset";
$transactionResult = $con->query($sql);

if (!$transactionResult) {
    die("Invalid query: " . $con->error);
}

if(isset($_POST['submit'])) {
$upload_folder='proofGL/';
$uploaded_file=$upload_folder . basename($_FILES['myfile']['name']);
if(file_exists($uploaded_file)){
    echo '<body>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>
    swal("File already exist","","error")
    .then((value) => {
    if (value) {
    window.location.href = "historyproof.php";
    }
    });
    </script>
    </body>';
    }
    else{
    if(move_uploaded_file($_FILES['myfile']['tmp_name'], $uploaded_file)){
        $filename = basename($_FILES['myfile']['name']);
        $res_ID = $_POST['beneid'];
$res_date2 = $_POST['rdate'];
$res_time2 = $_POST['rtime'];
$res_Assist2= $_POST['assist'];
        $query = "UPDATE history SET proofGL = '$filename' WHERE Beneficiary_ID = '$res_ID' AND ReceivedDate='$res_date2' AND ReceivedTime ='$res_time2' AND AssistanceType= '$res_Assist2'";
        if (mysqli_query($con, $query)) {
    echo '<body>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>
    swal("File has been uploaded","","success")
    .then((value) => {
    if (value) {
    window.location.href = "historyproof.php";
    }
    });
    </script>
    </body>';
    }
}else{
    echo'Error';
    }
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
<h2> Uploading Files</h2>
</div>
<div id="currentDate"></div>
<div class="user--info">
<div class="search--box">
<i class="fa-solid fa-search"> </i>
<input type="text" id="Search" oninput="search()" placeholder="Search "
autocomplete="off"/>
</div>
<img src="images/background.png" alt=""/>
</div>
</div>
<div class="tabular--wrapper">
<div class="card--container">
<h3 class="main--title"> Upload File
</h3>
</div>
<div class="table--container">
<!--<button class="btn1" onclick="window.location.href
='addbeneficiary.php';">Add Beneficiary</button>-->
<form action="" method="post" enctype="multipart/form-data">
<input type="hidden" name="beneid" value="<?php echo $res_ID; ?>">
<input type="hidden" name="assist" value="<?php echo $res_Assist; ?>">
<input type="hidden" name="rdate" value="<?php echo $res_date; ?>">
<input type="hidden" name="rtime" value="<?php echo $res_time; ?>">
<input type="file" style="color: blue; padding: 10px; border-radius:
5px;margin-bottom:10px;" name="myfile"/><br>

<input type="submit"  id="submitbtn" style="color: blue; background-color: lightgray; padding:
10px; border-radius: 5px; margin-right:10px;" value="Submit" name="submit" onclick="showConfirmation()" />
                  
<input type="reset" style="color: white; background-color: red; padding: 10px;
border-radius: 5px;"/>
</div>
</div>
</div>
<input type="hidden" id="confirmed" name="confirmed" value="">

                    

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
function showConfirmation() {
            var confirmation = confirm("Are you sure you want to update?");
            if (confirmation) {
                // If user clicks OK, submit the form
                document.getElementById("confirmed").value = "yes";
            } else {
                document.getElementById("confirmed").value = "no";
            }
        }

function toggleForm() {
var form = document.getElementById("addForm");
if (form.style.display === "none") {
form.style.display = "block";
} else {
form.style.display = "none";
}
}
// Function to get the current date in the format: Month Day, Year (e.g.,April 14, 2024)
function getCurrentDate() {
var months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

var currentDate = new Date();
var month = months[currentDate.getMonth()];
var day = currentDate.getDate();
var year = currentDate.getFullYear();
return month + " " + day + ", " + year;
}
// Update the current date element with the current date
document.getElementById("currentDate").innerText = getCurrentDate();

function search() {
// Get the search input value
var input = document.getElementById("Search").value.toUpperCase();
// Get the table rows
var rows = document.querySelectorAll(".table--container table tbody tr");

// Loop through all table rows
for (var i = 0; i < rows.length; i++) {
var row = rows[i];
// Get the cells containing the Date, Beneficiary ID, Name, City, Assistance Type, Status, Schedule, and Transaction Type

var lnamecell = row.cells[0];
var fnamecell = row.cells[1];
var emailcell = row.cells[2];
var officecell = row.cells[4];
if (lnamecell && fnamecell && emailcell && officecell) {

// Get the text content of the cells and convert them to uppercase

var lnametext = lnamecell.textContent.toUpperCase();
var fnametext =
fnamecell.textContent.toUpperCase();
var emailtext =
emailcell.textContent.toUpperCase();
var officetext =
officecell.textContent.toUpperCase();


// Check if the search input value matches any of the columns
if (lnametext.indexOf(input) > -1 || fnametext.indexOf(input) > -1 || emailtext.indexOf(input) > -1|| officetext.indexOf(input) > -1) {
// If there's a match, display the table row
row.style.display = "";
} else {
// If there's no match, hide the table row
row.style.display = "none";
}
}
}
}

</script>
</body>
</html>