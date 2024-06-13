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
if(isset($_POST['Beneficiary_Id'])) {
    $beneID = $_POST['Beneficiary_Id'];
    
    $query = mysqli_query($con, "SELECT * FROM beneficiary WHERE Beneficiary_Id=$beneID");

    if($result = mysqli_fetch_assoc($query)){
    $res_Id = $result['Beneficiary_Id'];
    $res_Fname = $result['Firstname'];
     $res_Lname = $result['Lastname'];
     $role=$result['CityMunicipality'];
    }
} else {
    echo "User ID is not set.";
    exit; // Exit if ID is not set
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> History </title>
    <link rel = "stylesheet" href = "assistance.css"/>
    <link rel = "stylesheet" href = "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
</head>
<body>
    <div class = "sidebar">
        <div class = "logo">
        </div>
        <ul class = "menu">

            <li>
                <a href = "#" onclick="dashboard()">
                    <i class = "fas fa-tachometer-alt"> </i>
                    <span> Dashboard </span>
                </a>
            </li>

            <li>
                <a href = "#" onclick="records()">
                    <i class = "fas fa-chart-bar"> </i>
                    <span> Beneficiary's Records </span>
                </a>
            </li>

            <li >
                <a href = "#" onclick="assistance()">
                    <i class = "fas fa-handshake-angle"> </i>
                    <span> Financial Assistance </span>
                </a>
            </li>


            <li>
                <a href = "#" onclick="hospital()">
                    <i class = "fas fa-hospital"> </i>
                    <span> Hospitals </span>
                </a>
            </li>

            <li>
            <a href="#" onclick="medicines()">
                <i class="fa-solid fa-capsules"></i>
                <span>Medicines</span>
            </li>
            <li>
                <a href="#" onclick="laboratories()">
                <i class="fa-solid fa-flask-vial"></i>
                    <span>Laboratories</span>
                </a>
            </li>

            <?php if ($role === 'Admin'): ?>
            <li>
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
                <h2> History of Assistance </h2>
            </div>
            <div class="user--info">
                <div class="search--box1">
                
            </div>
            <img src = "images/background.png" alt = "" />
            </div>
        </div>

        <div class="card--container">
            <h3 class="main--title"> History 
               
                </li>
            </ul>
            </h3>
        </div>

        <div class="tabular--wrapper">
            
            <h3 class="main--title">Beneficiary Name: <?php echo $res_Fname; ?> <?php echo $res_Lname; ?></h3>
         
  
            
            <div class="table--container">
            <form id="assistance" action="http://localhost/public_html/assistance.php" method="POST">
                    
                    <input type="hidden" name="beneficiary_id" id="beneficiaryIdInput">
         
                </form>
                <table>
                    <thead>
                        <tr>
                        <th> Transaction Type: </th>
                            <th>  Assistance Type: </th>
                            <th> Received Assistance: </th>
                             <th> Received Date:  </th>
                            <th> Received Time:  </th>
                            <th> Assisted By:  </th>
                        </tr>
                        <tbody>
                        <?php
include("php/config.php");

$sql = "SELECT h.* , b.*
        FROM history h 
       INNER JOIN beneficiary b ON b.Beneficiary_Id = h.Beneficiary_ID
        
where h.Beneficiary_ID=$beneID 
       ORDER BY h.ReceivedDate ASC";

$result = $con->query($sql);

if (!$result) {
    die("Invalid query: " . $con->error);
}

while ($row = $result->fetch_assoc()) {
    $Emp_Id = $row["Emp_ID"]; 

    $sql2 = "SELECT *  FROM employees
    
where Emp_ID=$Emp_Id";

$result2 = $con->query($sql2);
while ($row2 = $result2->fetch_assoc()) {

     $givenTime = "";
if ($row["ReceivedDate"] !== NULL) {
    $givenTime = date("h:i A", strtotime($row["ReceivedDate"]));
}
      $transaction_time = date("h:i A", strtotime($row["ReceivedTime"]));
    echo "<tr>
 <td>" . $row["TransactionType"] . " </td>
 <td>" . $row["AssistanceType"] . " </td>
 <td>" . $row["ReceivedAssistance"] . " </td>

 <td>" . $row["ReceivedDate"] . " </td>
<td>" . $transaction_time . " </td>
  <td>" . $row2["Firstname"]. " " . $row2["Lastname"] . " </td>
           
            
              </tr>";
}
}
?>
<input type="hidden" id="confirmed" name="confirmed" value="">



                        </tbody>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    
<script type = "text/javascript">


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

var nameCell = row.cells[2];
var cityCell = row.cells[3];
var assistanceTypeCell = row.cells[7];
var statusCell = row.cells[8];
var schedCell = row.cells[4];
var transactionTypeCell = row.cells[6];
if (dateCell && transaction_timeCell  && nameCell && cityCell && assistanceTypeCell && statusCell && schedCell && transactionTypeCell) {

// Get the text content of the cells and convert them to uppercase

var dateText = dateCell.textContent.toUpperCase();
var transaction_timeText =
transaction_timeCell.textContent.toUpperCase();


var nameText = nameCell.textContent.toUpperCase();
var cityText = cityCell.textContent.toUpperCase();
var assistanceTypeText =
assistanceTypeCell.textContent.toUpperCase();

var statusText = statusCell.textContent.toUpperCase();
var schedText = schedCell.textContent.toUpperCase();
var transactionTypeText =
transactionTypeCell.textContent.toUpperCase();

// Check if the search input value matches any of the columns
if (dateText.indexOf(input) > -1 || transaction_timeText.indexOf(input) > -1
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
    function dashboard(){
    window.location = "http://localhost/public_html/dashboard.php"
}

function records(){
    window.location = "http://localhost/public_html/patients-records.php"
}

function assistance(){
    window.location = "http://localhost/public_html/assistance.php"
}

function hospital(){
    window.location = "http://localhost/public_html/hospital.php"
}
function medicines(){
    window.location = "http://localhost/public_html/fa-medicines.php"
}

function burial(){
    window.location = "http://localhost/public_html/fa-burial.php"
}

function chemrad(){
    window.location = "http://localhost/public_html/fa-chemrad.php"
}

function dialysis(){
    window.location = "http://localhost/public_html/fa-dialysis.php"
}


function employees(){
        window.location = "http://localhost/public_html/employeeRecords.php"
    }
    
    function medicines() {
        window.location = "http://localhost/public_html/medicines.php";
    }
    function laboratories() {
        window.location = "http://localhost/public_html/laboratories.php";
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
function editRecord(beneficiaryId) {
            document.getElementById('beneficiaryIdInput').value = beneficiaryId;
     
    // Create a hidden form field
    var form = document.createElement('form');
    form.method = 'post';
    form.action = 'editformassistance.php'; // Corrected action to 'edit-form.php'
    
    // Create an input field to store the Beneficiary_Id
    var input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'Beneficiary_Id';
    input.value = beneficiaryId;
    
    // Append the input field to the form
    form.appendChild(input);
    
    // Append the form to the document body and submit it
    document.body.appendChild(form);
    form.submit();
}




</script>
</body>
</html>