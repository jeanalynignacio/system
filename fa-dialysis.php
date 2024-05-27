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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Financial Assistance </title>
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
                    <span> Patient's Records </span>
                </a>
            </li>

            <li class = "active">
                <a href = "#"onclick="assistance()">
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
</a>
            </li>

            <li class = "logout">
                <a href = "#">
                    <i class = "fas fa-sign-out-alt"> </i>
                    <span> Logout </span>
                </a>
            </li>

            <li class = "user">
                <a href = "#">
                    <i class = "fas fa-user"> </i>
                    <span> Profile </span>
                </a>
            </li>
        </ul>
    </div>

    <div class="main--content">
        <div class="header--wrapper">
            <div class="header--title">
                <span> 1Bataan Malasakit - Special Assistance Program </span>
                <h2> Financial Assistance </h2>
            </div>
            <div class="user--info">
                <div class="search--box">
                <i class = "fa-solid fa-search"> </i>
                <input type="text" id="Search" oninput="search()" placeholder="Search " autocomplete="off"/>
            </div>
            <img src = "images/background.png" alt = "" />
            </div>
        </div>

        <div class="card--container">
            <h3 class="main--title"> Type of Financial Assistance 
                <ul class = "drop"> 
                <li>
                    <a href = "#" onclick="dialysis()"> Dialysis Patients <span> <i class="fa-solid fa-caret-down"> </i> </span> </a>
                    <ul class = "dropdown">
                        <li> <a href = "#" onclick="assistance()"> Assistance </a></li>
                        
                        <li> <a href = "#" onclick="burial()"> Burial </a></li>
                        <li> <a href = "#" onclick="chemrad()"> Chemotheraphy & Radiation </a></li>
                    </ul>
                    </ul>
                </li>
            </ul>
            </h3>
        </div>

        <div class="tabular--wrapper">
            <h3 class="main--title"> Financial Assistance - Dialysis Patients Data </h3>
            <div class="table--container">
                <table>
                    <thead>
                        <tr>
                            <th> Date: </th>
                            <th> Time: </th>
                            <th> Beneficiary No: </th>
                            <th> Name: </th>
                            <th> Municipality: </th>
                            <th> Schedule: <br> (if online) </th>
                            <th> Transaction Type: </th>
                            <th> Amount Received: </th>
                            <th> Status: </th>
                            <th> Action: </th>
                        </tr>
                        <tbody>
                        <?php
include("php/config.php");

$sql = "SELECT t.Date, t.transaction_time, b.Beneficiary_Id, b.Lastname, b.Firstname, b.CityMunicipality, t.Given_Sched, t.TransactionType, f.Amount, t.Status  
        FROM financialassistance f 
        INNER JOIN beneficiary b ON b.Beneficiary_Id = f.Beneficiary_ID
        INNER JOIN transaction t ON t.Beneficiary_Id = f.Beneficiary_ID
        where FA_type= 'Dialysis'
        ORDER BY t.Date DESC"; 

$result = $con->query($sql);

if (!$result) {
    die("Invalid query: " . $con->error);
}

while ($row = $result->fetch_assoc()) {
    echo "<tr>
            <td>" . $row["Date"] . " </td>
            <td>" . $row["transaction_time"] . " </td>
            <td>" . $row["Beneficiary_Id"] . " </td>
            <td>" . $row["Lastname"] . ", " . $row["Firstname"] . " </td>
            <td>" . $row["CityMunicipality"] . " </td>
            <td>" . $row["Given_Sched"] . " </td>
            <td>" . $row["TransactionType"] . " </td>
            <td>" . $row["Amount"] . " </td>
             <td>" . $row["Status"] . " </td>
             <td>".
             "<form method='post' action='editformfassistance.php'>" .
             "<input type='hidden' name='Beneficiary_Id' value='" . $row['Beneficiary_Id'] . "'>" .
             "<button type='submit'>Edit</button>" .
             "</form>" .
             "</td>  </tr>";
             
}

?>
                        </tbody>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    
<script type = "text/javascript">

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
        var schedCell = row.cells[5];
        var transactionTypeCell = row.cells[6];
        var assistanceTypeCell = row.cells[7];
        var statusCell = row.cells[8];
        
        if (dateCell && transaction_timeCell && beneficiaryIdCell && nameCell && cityCell && assistanceTypeCell && statusCell && schedCell && transactionTypeCell) {
            // Get the text content of the cells and convert them to uppercase
            var dateText = dateCell.textContent.toUpperCase();
            var transaction_timeText = transaction_timeCell.textContent.toUpperCase();
            var beneficiaryIdText = beneficiaryIdCell.textContent.toUpperCase();
            var nameText = nameCell.textContent.toUpperCase();
            var cityText = cityCell.textContent.toUpperCase();
            var assistanceTypeText = assistanceTypeCell.textContent.toUpperCase();
            var statusText = statusCell.textContent.toUpperCase();
            var schedText = schedCell.textContent.toUpperCase();
            var transactionTypeText = transactionTypeCell.textContent.toUpperCase();

            // Check if the search input value matches any of the columns
            if (dateText.indexOf(input) > -1 || beneficiaryIdText.indexOf(input) > -1 || nameText.indexOf(input) > -1 || cityText.indexOf(input) > -1 || assistanceTypeText.indexOf(input) > -1 || statusText.indexOf(input) > -1 || schedText.indexOf(input) > -1 || transactionTypeText.indexOf(input) > -1) {
                // If there's a match, display the table row
                row.style.display = "";
            } else {
                // If there's no match, hide the table row
                row.style.display = "none";
            }
        }
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
    
   
    form.appendChild(input);
    
    // Append the form to the document body and submit it
    document.body.appendChild(form);
    form.submit();
}
</script>
</body>
</html>