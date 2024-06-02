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
    
    header("Location: employee-login.php");
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


$sql = "SELECT COUNT(*) AS totalEntries FROM employees";
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
$sql = "SELECT * FROM employees where role='Employee'  LIMIT $recordsPerPage OFFSET $offset";
$transactionResult = $con->query($sql);

if (!$transactionResult) {
    die("Invalid query: " . $con->error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title> Patient's Records </title>
<link rel="stylesheet" href="patients-records.css"/>

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
<h2> Employee's Records </h2>
</div>
<div id="currentDate"></div>
<div class="user--info">
<div class="search--box">
<i class="fa-solid fa-search"> </i>
<input type="text" id="Search" oninput="search()" placeholder="Search " autocomplete="off"/>

</div>
<img src="images/background.png" alt=""/>
</div>

</div>

        <div class="tabular--wrapper">  
        <div class="card--container">
            <h3 class="main--title"> Overall Data
           
             
            </h3>

        </div>
<div class="table--container">


<!--<button class="btn1" onclick="window.location.href ='addbeneficiary.php';">Add Beneficiary</button>-->
<button class="btn1" onclick="window.location.href ='employeeregistration.php';">Add Employee Account</button>

<table>
<thead>
<tr>
<th>Last Name:</th>
<th>First Name:</th>
<th>Email:</th>
<th>Role:</th>

<th>Action:</th>
</tr>
</thead>
<tbody>
<?php
include("php/config.php");
$sql = "SELECT * FROM employees where role='Community Affairs Officer'";
$result = $con->query($sql);
if (!$result) {
die("Invalid query: " . $con->error);
}
while ($row = $result->fetch_assoc()) {
echo "<tr>

<td>" . $row["Lastname"]." </td>
<td>" . $row["Firstname"] . "</td>
<td>" . $row["Email"] . "</td>
<td>" . $row["role"] . "</td>
<td>
<form method='post'

action='editemployee.php'>

<input type='hidden'
name='Emp_ID' value='" . $row['Emp_ID'] . "'>

<button type='submit' style='color:green'>View</button>

</form>
</td>
</tr>";
}
?>
</tbody>
</table>

<nav aria-label="Page navigation example">
            <ul class="pagination justify-content-end">
                <?php if ($currentPage > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $currentPage - 1; ?>" tabindex="-1">Previous</a>
                    </li>
                <?php else: ?>
                    <li class="page-item disabled">
                        <a class="page-link" href="#" tabindex="-1">Previous</a>
                    </li>
                <?php endif; ?>

                <?php for ($page = 1; $page <= $totalPages; $page++): ?>
                    <li class="page-item <?php if ($page == $currentPage) echo 'active'; ?>">
                        <a class="page-link" href="?page=<?php echo $page; ?>"><?php echo $page; ?></a>
                    </li>
                <?php endfor; ?>

                <?php if ($currentPage < $totalPages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $currentPage + 1; ?>">Next</a>
                    </li>
                <?php else: ?>
                    <li class="page-item disabled">
                        <a class="page-link" href="#">Next</a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
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

var dateCell = row.cells[0];
var transaction_timeCell = row.cells[1];
var beneficiaryIdCell = row.cells[2];
var nameCell = row.cells[3];
var cityCell = row.cells[4];
var assistanceTypeCell = row.cells[5];
var statusCell = row.cells[6];
var schedCell = row.cells[7];
var transactionTypeCell = row.cells[8];
if (dateCell && transaction_timeCell && beneficiaryIdCell && nameCell && cityCell && assistanceTypeCell && statusCell && schedCell && transactionTypeCell) {

// Get the text content of the cells and convert them to uppercase

var dateText = dateCell.textContent.toUpperCase();
var transaction_timeText =
transaction_timeCell.textContent.toUpperCase();
var beneficiaryIdText =
beneficiaryIdCell.textContent.toUpperCase();

var nameText = nameCell.textContent.toUpperCase();
var cityText = cityCell.textContent.toUpperCase();
var assistanceTypeText =
assistanceTypeCell.textContent.toUpperCase();

var statusText = statusCell.textContent.toUpperCase();
var schedText = schedCell.textContent.toUpperCase();
var transactionTypeText =
transactionTypeCell.textContent.toUpperCase();

// Check if the search input value matches any of the columns
if (dateText.indexOf(input) > -1 || transaction_timeText.indexOf(input) > -1 || beneficiaryIdText.indexOf(input) > -1
|| nameText.indexOf(input) > -1 || cityText.indexOf(input) > -1 ||
assistanceTypeText.indexOf(input) > -1 || statusText.indexOf(input) > -1 ||
schedText.indexOf(input) > -1 || transactionTypeText.indexOf(input) > -1) {
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