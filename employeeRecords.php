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
 $office =$result['Office'];
 
}
  }
  else{
    
    header("Location: login.php");
}
$office = mysqli_real_escape_string($con, $office);

// Second query to get employees in the same office
$query = "SELECT * FROM employees WHERE Office = '$office' AND Emp_ID != '$id'";

$result = mysqli_query($con, $query);



// Get the current date in the format YYYY-MM-DD
$currentDate = date("Y-m-d");


// Define pagination variables
$records_per_page = 10; // Number of records to display per page
$current_page = isset($_GET['page']) ? $_GET['page'] : 1; // Get current page number, default to 1 if not set

// Calculate LIMIT and OFFSET
$offset = ($current_page - 1) * $records_per_page;


$sql = "SELECT COUNT(*) AS totalEntries FROM employees where Office='$office' AND Emp_ID != '$id'";
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
$sql = "SELECT * FROM employees where Office='$office'  AND Emp_ID != '$id'  LIMIT $recordsPerPage OFFSET $offset";
$transactionResult = $con->query($sql);

if (!$transactionResult) {
    die("Invalid query: " . $con->error);
}

if (isset($_POST['Emp_ID']) && isset($_POST['password'])) {
    $EmpID = mysqli_real_escape_string($con, $_POST['Emp_ID']);
    $password = mysqli_real_escape_string($con, $_POST['password']);

    // Assuming you have a function to get the current user's hashed password
    $query = "SELECT password_hash FROM employees WHERE Emp_ID = '$id'";
    $result = mysqli_query($con, $query);

    if ($result && $row = mysqli_fetch_assoc($result)) {
        // Check if the entered password matches the hashed password in the database
        if (password_verify($password, $row['password_hash'])) {
            // Password is correct, proceed to delete
            $deleteQuery = "DELETE FROM employees WHERE Emp_ID = '$EmpID'";
            $deleteResult = mysqli_query($con, $deleteQuery);

            if ($deleteResult) {
                echo '<body>
                <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
                <script>
                swal("Record Deleted successfully","","success")
                .then((value) => {
                    if (value) {
                        window.location.href = "employeeRecords.php";
                    }
                });
                </script>
                </body>';  
            } else {
                echo "Error deleting record: " . mysqli_error($con);
            }
        } else {
            echo '<body>
                  <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
                  <script>
                  swal("Incorrect password.","Please try again.","error")
                  .then((value) => {
                      if (value) {
                          window.location.href = "employeeRecords.php";
                      }
                  });
                  </script>
                  </body>';
           
        }
    } else {
     
        echo '<script>alert("Error fetching user data: " ); window.location.href = "employeeRecords.php";</script>';
        
    }
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Employee's Records </title>
<link rel="stylesheet" href="employeeRecords.css"/>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-
awesome/6.4.0/css/all.min.css"/>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

</head>
<body>
<div class="sidebar">
        <div class="logo"  style="height: 2px;" ></div>
        <ul class="menu" style="margin-top: 15px; margin-left: -8px;" >
            <li>
                <a href="#" onclick="dashboard()"   style="font-size:14px;height:10px; ">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="#" onclick="records()"style="font-size:14px;height:10px; ">
                    <i class="fas fa-chart-bar"></i>
                    <span>Beneficiary's Records</span>
                </a>
            </li>
            <li>
                <a href="#" onclick="assistance()" style="font-size:14px;height:10px; ">
                    <i class="fas fa-handshake-angle"></i>
                    <span>Financial Assistance</span>
                </a>
            </li>
            <li>
                <a href="#" onclick="hospital()" style="font-size:14px;height:10px; ">
                    <i class="fas fa-hospital"></i>
                    <span>Hospitals</span>
                </a>
            </li>
            <li>
                <a href="#" onclick="medicines()" style="font-size:14px;height:10px; ">
                    <i class="fa-solid fa-capsules"></i>
                    <span>Medicines</span>
                </a>
            </li>
            <li  >
                <a href="#" onclick="laboratories()" style="font-size:14px;height:10px; padding-right:-2px; ">
                <i class="fa-solid fa-flask-vial"></i>
                    <span>Laboratories</span>
                </a>
            </li>
            <li>
            <a href="#" onclick="dialysis()" style="font-size:14px;height:10px; ">
            <i class="fa-solid fa-file-medical" style="margin-right:6px; margin-left:5px;"></i>
                    <span>Dialysis</span>
                </a>
            </li>
            <?php if ($role === 'Admin'): ?>
                <li class="active" >
                <a href="#" onclick="employees()" style="font-size:14px;height:10px; ">
                    <i class="fas fa-users"></i>
                    <span>Employees</span>
                </a>
            </li>
           
        <?php endif; ?>
       <br>
            <li class="user"  >
            <a href="#" onclick="profile()" style="font-size:14px;height:10px; ">
                    <i class="fas fa-user"></i>
                                    
                <span>Profile</span>
                <input type="hidden" name="Emp_ID" value="<?php echo "{$resEmp_ID['Emp_ID']}"; ?>">
                </a>
            </li>
            <li class="logout">
                <a href="#" onclick="logout()" style="font-size:14px;height:10px; ">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </li>
         
        </ul>
    </div>
<div class="main--content">
<div class="header--wrapper">
<div class="header--title">
<span> Provincial Government of Bataan-Damayan Center  </span>
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
<th>Office:</th>
<th>Action:</th>
<th>Action:</th>
</tr>
</thead>
<tbody>
<?php
include("php/config.php");
$sql = "SELECT * FROM employees where Office='$office' AND Emp_ID != '$id'";
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
<td>" . $row["Office"] . "</td>
<td>
<form method='post'

action='editemployee.php'>

<input type='hidden'
name='Emp_ID' value='" . $row['Emp_ID'] . "'>

<button type='submit' style='color:green'>View</button>

</form>
</td>
 <td>
            <form method='post' class='delete-form' id='form_" . $row['Emp_ID'] . "'>
                <input type='hidden' name='Emp_ID' value='" . $row['Emp_ID'] . "'>
                <button type='button' style='color:red' onclick='showConfirmation(" . $row['Emp_ID'] . ")'>DELETE</button>
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
    
<input type="hidden" name="confirmed" id="confirmed" value="no">

                    

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
    function dialysis(){
        window.location = "http://localhost/public_html/dialysis.php"
    }


function profile() {
        window.location = "http://localhost/public_html/profileadmin.php";
    }
    function logout() {
    // Load SweetAlert script if not already loaded
    if (typeof swal === 'undefined') {
        var script = document.createElement('script');
        script.src = 'https://unpkg.com/sweetalert/dist/sweetalert.min.js';
        document.head.appendChild(script);
    }

    // Show SweetAlert confirmation dialog
    swal({
        title: "Are you sure you want to Logout?",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    }).then((willLogout) => {
        if (willLogout) {
            // If user confirms, set the value to "yes"
            document.getElementById("confirmed").value = "yes";
            // Redirect the user
            window.location.href = "http://localhost/public_html/logoutemp.php";
        } else {
            // If user cancels, set the value to "no"
            document.getElementById("confirmed").value = "no";
        }
    });
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

function showConfirmation(empId) {
    swal({
        title: "Are you sure?",
        text: "Enter your password to confirm deletion:",
        content: {
            element: "input",
            attributes: {
                placeholder: "Password",
                type: "password",
            },
        },
        buttons: true,
        dangerMode: true,
    }).then((value) => {
        if (value) {
            const password = value; // Get the password entered by the user
            
            // Get the form by ID
            const form = document.getElementById('form_' + empId);
            
            // Create a hidden input for the password
            const passwordInput = document.createElement('input');
            passwordInput.type = 'hidden';
            passwordInput.name = 'password'; // Name for the password input
            passwordInput.value = password; // Set the password value
            
            // Append the password input to the form
            form.appendChild(passwordInput);
            
            // Submit the form
            form.submit();
        }
    });
}




</script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
</body>
</html>