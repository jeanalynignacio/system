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


date_default_timezone_set('Asia/Manila');
// Get the current date in the format YYYY-MM-DD
$currentDate = date("Y-m-d");

//TOTAL PATIENTS
// Query to count the total number of entries in the database for the current date
$sql = "SELECT COUNT(*) AS totalEntries FROM transaction WHERE Date = '$currentDate'";
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
$sql = "SELECT * FROM transaction WHERE Date = '$currentDate' LIMIT $recordsPerPage OFFSET $offset";
$transactionResult = $con->query($sql);

if (!$transactionResult) {
    die("Invalid query: " . $con->error);
}

//FA CODE
$assistanceType = "Financial Assistance";

// Query to count the total number of entries in the database for the current date and specific assistance type
$sql = "SELECT COUNT(*) AS totalEntriesFA FROM transaction WHERE Date = '$currentDate' AND AssistanceType = '$assistanceType'";
$result = $con->query($sql);

if (!$result) {
    die("Invalid query: " . $con->error);
}

// Fetch the result
$row = $result->fetch_assoc();
$totalEntriesFA = $row['totalEntriesFA'];

//hb code
$assistanceType2 = "Hospital Bills";

// Query to count the total number of entries in the database for the current date and specific assistance type
$sql = "SELECT COUNT(*) AS totalEntriesHB FROM transaction WHERE Date = '$currentDate' AND AssistanceType = '$assistanceType2'";
$result = $con->query($sql);

if (!$result) {
    die("Invalid query: " . $con->error);
}

// Fetch the result
$row = $result->fetch_assoc();
$totalEntriesHB = $row['totalEntriesHB'];

//lab code
$assistanceType3 = "Laboratory";

// Query to count the total number of entries in the database for the current date and specific assistance type
$sql = "SELECT COUNT(*) AS totalEntriesLAB FROM transaction WHERE Date = '$currentDate' AND AssistanceType = '$assistanceType3'";
$result = $con->query($sql);

if (!$result) {
    die("Invalid query: " . $con->error);
}

// Fetch the result
$row = $result->fetch_assoc();
$totalEntriesLAB = $row['totalEntriesLAB'];

//pending patients
$status= "For Schedule";

// Query to count the total number of entries in the database for the current date and specific assistance type
$sql = "SELECT COUNT(*) AS totalEntriesSTAT FROM transaction WHERE Date = '$currentDate' AND Status = '$status'";
$result = $con->query($sql);

if (!$result) {
    die("Invalid query: " . $con->error);
}

// Fetch the result
$row = $result->fetch_assoc();
$totalEntriesSTAT = $row['totalEntriesSTAT'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Main Dashboard</title>
    <link rel="stylesheet" href="dashboard.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>
<body>
    <div class="sidebar">
        <div class="logo"></div>
        <ul class="menu">
            <li class="active">
                <a href="#" onclick="dashboard()">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="#" onclick="records()">
                    <i class="fas fa-chart-bar"></i>
                    <span>Beneficiary's Records</span>
                </a>
            </li>
            <li>
                <a href="#" onclick="assistance()">
                    <i class="fas fa-handshake-angle"></i>
                    <span>Financial Assistance</span>
                </a>
            </li>
            <li>
                <a href="#" onclick="hospital()">
                    <i class="fas fa-hospital"></i>
                    <span>Hospitals</span>
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
                <span>Primary</span>
                <h2>Dashboard</h2>
            </div>
            <div class="user--info">
            <?php if ($role === 'Admin'): ?>
                <form action="budget.php" method="post">
    <input type="submit" style="background:none; margin-right:30px; color:#003399;" value="Budget" name="submit" />
</form>
<form action="reports.php" method="post">
    <input type="submit" style="background:none; margin-right:30px; color:#003399;" value="Reports" name="submit" />
</form>
<form action="feedbackreports.php" method="post">
    <input type="submit" style="background:none; margin-right:30px; color:#003399;" value="Feedbacks" name="submit" />
</form>
        <?php endif; ?>
        <?php if ($role === 'Community Affairs Officer'|| $role === 'Admin'): ?>
                <form action="historyproof.php" method="post">
    <input type="submit" style="background:none; margin-right:30px; color:#003399;" value="History" name="submit" />
</form>
        <?php endif; ?>
                <img src="images/background.png" alt=""/>
            </div>
        </div>

        <div class="card--container">
            <h3 class="main--title">Today's Data</h3>
            <div id="currentDate"></div> 
            <div class="card-wrapper">
                <div class="patients">
                    <div class="card--header">
                        <div class="card--column">
                            <span class="title">Total numbers of Patient:</span>
                        </div>
                        <i class="fa-solid fa-hospital-user icon"></i>
                    </div>
                    <span class="numbers"><?php echo $totalEntries; ?></span>
                </div>
                <div class="patients">
                    <div class="card--header">
                        <div class="card--column">
                            <span class="title">Financial Assistance Beneficiaries:</span>
                        </div>
                        <i class="fas fa-handshake-angle icon"></i>
                    </div>
                    <span class="numbers"><?php echo $totalEntriesFA; ?></span>
                </div>
                <div class="patients">
                    <div class="card--header">
                        <div class="card--column">
                            <span class="title">Hospital Bills Assistance Beneficiaries:</span>
                        </div>
                        <i class="fa-solid fa-hospital-user icon"></i>
                    </div>
                    <span class="numbers"><?php echo $totalEntriesHB; ?></span>
                </div>
                <div class="patients">
                    <div class="card--header">
                        <div class="card--column">
                            <span class="title">Laboratory Assistance Beneficiaries:</span>
                        </div>
                        <i class="fa-solid fa-hospital-user icon"></i>
                    </div>
                    <span class="numbers"><?php echo $totalEntriesLAB; ?></span>
                </div>
                <div class="patients">
                    <div class="card--header">
                        <div class="card--column">
                            <span class="title">Pending patients for scheduling:</span>
                        </div>
                        <i class="fa-solid fa-hourglass-half icon"></i>
                    </div>
                    <span class="numbers"><?php echo $totalEntriesSTAT; ?></span>
                </div>
            </div>
        </div>

        <div class="tabular--wrapper">
            <h3 class="main--title">Overall Data</h3>
            <div class="user--info">
            <div class="search--box" style="margin-bottom:20px;border-color:black;  ">
                    <i class="fa-solid fa-search"></i>
                    <input  style="width:500px; height: 40px;"type="text" id="Search" oninput="search()" placeholder="Search " autocomplete="off"/>
                </div>
                </div>
            <div class="table--container">
                <table>
                    <thead>
                        <tr>
                            <th>Date:</th>
                            <th>Time:</th>
                            <th>Beneficiary No:</th>
                            <th>Transaction Type:</th>
                            <th>Assistance Given:</th>
                            <th>Status:</th>
                        </tr>
                    </thead>
                    <tbody>
                   
                        <?php
                   
                       
                        while($row = $transactionResult->fetch_assoc()) {
                            $beneficiary_id = $row["Beneficiary_Id"];
                            $SQL = "SELECT * FROM beneficiary WHERE Beneficiary_Id='$beneficiary_id'";
                            $result = mysqli_query($con, $SQL);
                        
                            if ($result) {
                                $beneficiary = $result->fetch_assoc();
                                
                        $transaction_time = date("h:i A", strtotime($row["transaction_time"]));
                            echo "<tr>
                            <td>". $row["Date"] ."</td>
                           <td>" . $transaction_time . " </td>
                            <td>". $beneficiary["Lastname"]  . ", " . $beneficiary["Firstname"] . " </td>
                            <td>". $row["TransactionType"] ."</td>
                            <td>". $row["AssistanceType"] ."</td>
                            <td>". $row["Status"] ."</td>
                            </tr>";
                        }
                    }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
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
<input type="hidden" id="confirmed" name="confirmed" value="">


<script type="text/javascript">
    // Function to get the current date in the format: Month Day, Year (e.g., April 14, 2024)
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
            // Get the cells containing the Date, Beneficiary ID, Status, Assistance Type, and Transaction Type
            var dateCell = row.cells[0];
            var transaction_timeCell = row.cells[1];
            var beneficiaryIdCell = row.cells[2];
            var statusCell = row.cells[5];
            var assistanceTypeCell = row.cells[4];
            var transactionTypeCell = row.cells[3];

            if (dateCell && transaction_timeCell && beneficiaryIdCell && statusCell && assistanceTypeCell && transactionTypeCell) {
                // Get the text content of the cells and convert them to uppercase
                var dateText = dateCell.textContent.toUpperCase();
                var transaction_timeText = transaction_timeCell.textContent.toUpperCase();
                var beneficiaryIdText = beneficiaryIdCell.textContent.toUpperCase();
                var statusText = statusCell.textContent.toUpperCase();
                var assistanceTypeText = assistanceTypeCell.textContent.toUpperCase();
                var transactionTypeText = transactionTypeCell.textContent.toUpperCase();

                // Check if the search input value matches any of the columns
                if (dateText.indexOf(input) > -1 || transaction_timeText.indexOf(input) > -1 || beneficiaryIdText.indexOf(input) > -1 || statusText.indexOf(input) > -1 || assistanceTypeText.indexOf(input) > -1 || transactionTypeText.indexOf(input) > -1) {
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



    
</script>
</body>
</html>
