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
        $role = $result['role'];
        $branch = $result['Office'];
    }
} else {
    header("Location: login.php");
    exit;
}

// Get the current date in the format YYYY-MM-DD
$currentDate = date("Y-m-d");

// Define pagination variables
$records_per_page = 10; // Number of records to display per page
$current_page = isset($_GET['page']) ? $_GET['page'] : 1; // Get current page number, default to 1 if not set

// Calculate LIMIT and OFFSET
$offset = ($current_page - 1) * $records_per_page;

// Get the total number of entries
$sql = "SELECT COUNT(*) AS totalEntries FROM feedback";
$result = $con->query($sql);

if (!$result) {
    die("Invalid query: " . $con->error);
}

$row = $result->fetch_assoc();
$totalEntries = $row['totalEntries'];

// Calculate total pages
$totalPages = ceil($totalEntries / $records_per_page);

// Fetch paginated beneficiaries
$sql = "SELECT * FROM feedback ORDER BY Date ASC LIMIT $records_per_page OFFSET $offset";

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
<title>Feedback</title>
<link rel="stylesheet" href="feedbackreports.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>
<body>
<div class="sidebar">
    <div class="logo"></div>
    <ul class="menu">
        <li><a href="#" onclick="dashboard()"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
        <li class="active"><a href="#" onclick="records()"><i class="fas fa-chart-bar"></i><span>Beneficiary's Records</span></a></li>
        <li><a href="#" onclick="assistance()"><i class="fas fa-handshake-angle"></i><span>Financial Assistance</span></a></li>
        <li><a href="#" onclick="hospital()"><i class="fas fa-hospital"></i><span>Hospitals</span></a></li>
        <li><a href="#" onclick="medicines()"><i class="fa-solid fa-capsules"></i><span>Medicines</span></a></li>
        <li><a href="#" onclick="laboratories()"><i class="fa-solid fa-flask-vial"></i><span>Laboratories</span></a></li>
        <?php if ($role === 'Admin'): ?>
            <li><a href="#" onclick="employees()"><i class="fas fa-users"></i><span>Employees</span></a></li>
        <?php endif; ?>
        <li class="user"><a href="#" onclick="profile()"><i class="fas fa-user"></i><span>Profile</span></a></li>
        <li class="logout"><a href="#" onclick="logout()"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a></li>
    </ul>
</div>
<div class="main--content">
    <div class="header--wrapper">
        <div class="header--title">
            <span>1Bataan Malasakit - Special Assistance Program</span>
            <h2>Beneficiary's Feedback</h2>
        </div>
        <div id="currentDate"></div>
        <div class="user--info">
            <div class="search--box">
                <i class="fa-solid fa-search"></i>
                <input type="text" id="Search" oninput="search()" placeholder="Search " autocomplete="off"/>
            </div>
            <img src="images/background.png" alt=""/>
        </div>
    </div>
    <div class="tabular--wrapper">
        <div class="card--container">
            <h3 class="main--title">Overall Data</h3>
        </div>
     <h1 style="font-size: 20px">Labels</h1> 
     <h1 style="font-size: 15px; margin-top: 2px; font-size: 15px; margin-left: 35px;"><strong>CC1: Alin sa mga sumusunod ang naglalarawan sa iyong kaalaman sa CC (Citizen's Charter)?</strong></h1><br>
     <h1 style="font-size: 15px; margin-top: -25px; font-size: 15px; margin-left: 35px;"><strong>CC2: Kung alam ang CC (Pinili ang opsyon 1-3 sa CC1), masasabi mo ba na ang CC nang napuntahang opisina ay...</strong></h1><br>
     <h1 style="font-size: 15px; margin-top: -25px; font-size: 15px; margin-left: 35px;"><strong>CC3: Kung alam ang CC (Pinili ang opsyon 1-3 sa CC1),  gaano nakatulong ang CC sa transaksyon mo?</strong></h1><br>
    
  <h1 style="font-size: 15px; margin-top: 2px; font-size: 15px; margin-left: 35px;"><strong>SQDO: Nasiyahan ako sa serbisyo na aking natanggap sa napuntahan na tanggapan.</strong></h1><br>
  <h1 style="font-size: 15px; margin-top: -25px; font-size: 15px; margin-left: 35px;"><strong>SQD1: Makatwiran ang oras na aking ginugol para sa pagproseso ng aking transaksyon.</strong></h1><br>
  
<h1 style="font-size: 15px; margin-top: -25px; font-size: 15px; margin-left: 35px;"><strong>SQD2: Ang opisina ay sumusunod sa mga kinakailangang dokumento at mga hakbang batay sa impormasyong ibinigay.</strong></h1><br>

<h1 style="font-size: 15px; margin-top: -25px; font-size: 15px; margin-left: 35px;"><strong>SQD3: Ang mga hakbang sa pagproseso. kasama na ang pagbayad ay madali at simple lamang.</strong></h1><br>
 
<h1 style="font-size: 15px; margin-top: -25px; font-size: 15px; margin-left: 35px;"><strong>SQD4: Mabilis at madali akong nakahanap ng impormasyon tungkol sa aking transaksyon mula sa opisina o sa website nito.</strong></h1><br>
<h1 style="font-size: 15px; margin-top: -25px; font-size: 15px; margin-left: 35px;"><strong>SQD5; Nagbayad ako ng makatwirang halaga para sa aking transaksyon. (Kung ang sebisyo ay ibinigay ng libre. Piliin ang N/A.)</strong></h1><br>
<h1 style="font-size: 15px; margin-top:-25px; font-size: 15px; margin-left: 35px;"><strong>SQD6: Pakiramdam ko ay patas ang opisina sa lahat. o walang palakasan", sa aking transaksyon</strong></h1><br>
<h1 style="font-size: 15px; margin-top: -25px; font-size: 15px; margin-left: 35px;"><strong>SQD7: Magalang akong trinato ng mga tauhan, at (kung sakali ako ay humingi ng tulong) alam ko na sila ay handang tumulong sa akin.</strong></h1><br>
<h1 style="font-size: 15px; margin-top: -25px; font-size: 15px; margin-left: 35px;"><strong>SQD8: Nakuha ko ang kinakallangan ko mula sa tanggapan ng gobyemo. kung tinanggihan man. Ito ay sapat na ipinaliwanag sa akin.</strong></h1><br>

  
        <div class="table--container">
             <table>
                <thead>
                    <tr>
                        <th>Date:</th>
                    
                        <th>Email:</th>
                        <th>Office:</th>
                        <th>Assistance Type:</th>
                        <th>CC1:</th>
                        <th>CC2:</th>
                        <th>CC3:</th>
                        <th> SQD0: </th>
                        <th> SQD1: </th>
                        <th> SQD2: </th>
                        <th> SQD3: </th>
                        <th> SQD4: </th>
                        <th> SQD5: </th>
                        <th> SQD6: </th>
                        <th> SQD7: </th>
                        <th> SQD8: </th>
                        <th> Comments: </th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($row = $transactionResult->fetch_assoc()) {
                 
                        echo "<tr>
                        <td>{$row['Date']}</td>
                         <td>{$row['Email']}</td>
                        <td>{$row['Office']}</td>
                        <td>{$row['ServiceType']}</td>
                        <td>{$row['CC1']}</td>
                        <td>{$row['CC2']}</td>
                         <td>{$row['CC3']}</td>
                        <td>{$row['SQD0']}</td>
                         <td>{$row['SQD1']}</td>
                          <td>{$row['SQD2']}</td>
                            <td>{$row['SQD3']}</td>
                         <td>{$row['SQD4']}</td>
                          <td>{$row['SQD5']}</td>
                            <td>{$row['SQD6']}</td>
                         <td>{$row['SQD7']}</td>
                          <td>{$row['SQD8']}</td>
                         <td>{$row['Comments']}</td>
                    </tr>";
                    }
                    ?>
                </tbody>
            </table>
            <nav aria-label="Page navigation example">
                <ul class="pagination justify-content-end">
                    <?php if ($current_page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $current_page - 1; ?>" tabindex="-1">Previous</a>
                        </li>
                    <?php else: ?>
                        <li class="page-item disabled">
                            <a class="page-link" href="#" tabindex="-1">Previous</a>
                        </li>
                    <?php endif; ?>
                    <?php for ($page = 1; $page <= $totalPages; $page++): ?>
                        <li class="page-item <?php if ($page == $current_page) echo 'active'; ?>">
                            <a class="page-link" href="?page=<?php echo $page; ?>"><?php echo $page; ?></a>
                        </li>
                    <?php endfor; ?>
                    <?php if ($current_page < $totalPages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $current_page + 1; ?>">Next</a>
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
function profile() {
    window.location = "http://localhost/public_html/profileadmin.php";
}
function logout() {
    var confirmation = confirm("Are you sure you want to Logout?");
    if (confirmation) {
        document.getElementById("confirmed").value = "yes";
        window.location.href = "http://localhost/public_html/logoutemp.php";
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
function getCurrentDate() {
    var months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    var currentDate = new Date();
    var month = months[currentDate.getMonth()];
    var day = currentDate.getDate();
    var year = currentDate.getFullYear();
    return month + " " + day + ", " + year;
}
document.getElementById("currentDate").innerText = getCurrentDate();
function search() {
    var input = document.getElementById("Search").value.toUpperCase();
    var rows = document.querySelectorAll(".table--container table tbody tr");
    for (var i = 0; i < rows.length; i++) {
        var row = rows[i];
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
            var dateText = dateCell.textContent.toUpperCase();
            var transaction_timeText = transaction_timeCell.textContent.toUpperCase();
            var beneficiaryIdText = beneficiaryIdCell.textContent.toUpperCase();
            var nameText = nameCell.textContent.toUpperCase();
            var cityText = cityCell.textContent.toUpperCase();
            var assistanceTypeText = assistanceTypeCell.textContent.toUpperCase();
            var statusText = statusCell.textContent.toUpperCase();
            var schedText = schedCell.textContent.toUpperCase();
            var transactionTypeText = transactionTypeCell.textContent.toUpperCase();
            if (dateText.indexOf(input) > -1 || transaction_timeText.indexOf(input) > -1 || beneficiaryIdText.indexOf(input) > -1 || nameText.indexOf(input) > -1 || cityText.indexOf(input) > -1 || assistanceTypeText.indexOf(input) > -1 || statusText.indexOf(input) > -1 || schedText.indexOf(input) > -1 || transactionTypeText.indexOf(input) > -1) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        }
    }
}
function employees(){
    window.location = "http://localhost/public_html/employeeRecords.php";
}
function profile() {
    window.location = "http://localhost/public_html/profileadmin.php";
}
</script>
</body>
</html>
