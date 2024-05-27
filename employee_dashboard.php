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
    <title> Main Dashboard </title>
    <link rel = "stylesheet" href = "dashboard.css"/>
    <link rel = "stylesheet" href = "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
</head>
<body>
    <div class = "sidebar">
        <div class = "logo">
        </div>
        <ul class = "menu">

            <li class = "active">
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

            <li>
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
                <a href = "#">
                    <i class="fa-regular fa-calendar-days"></i> </i>
                    <span> Medicines </span>
                </a>
            </li>

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
                <span> Primary </span>
                <h2> Dashboard </h2>
            </div>
            <div class="user--info">
                <div class="search--box">
                <i class = "fa-solid fa-search"> </i>
                <input type="text" id="Search" oninput="search()" placeholder="Search " autocomplete="off" />
            </div>
            <img src = "images/background.png" alt = "" />
            </div>
        </div>

        <div class="card--container">
            <h3 class="main--title"> Today's Data </h3>
            <div id="currentDate"></div> 
            <div class="card-wrapper">
                <div class="patients">
                    <div class="card--header">
                        <div class="card--column">
                            <span class="title"> Total numbers of Patient: </span>
                        </div>
                        <i class="fa-solid fa-hospital-user icon"> </i>
                    </div>
                    <span class="numbers"><?php echo $totalEntries; ?></span>
                </div>

                <div class="patients">
                    <div class="card--header">
                        <div class="card--column">
                            <span class="title"> Financial Assistance Beneficiaries: </span>
                        </div>
                        <i class="fas fa-handshake-angle icon"> </i>
                    </div>
                    <span class="numbers"><?php echo $totalEntriesFA; ?></span>
                </div>
                <div class="patients">
                    <div class="card--header">
                        <div class="card--column">
                            <span class="title"> Hospital Bills Assistance Beneficiaries: </span>
                        </div>
                        <i class="fa-solid fa-hospital-user icon"> </i>
                    </div>
                    <span class="numbers"><?php echo $totalEntriesHB; ?></span>
                </div>
                <div class="patients">
                    <div class="card--header">
                        <div class="card--column">
                            <span class="title"> Laboratory Assistance Beneficiaries: </span>
                        </div>
                        <i class="fa-solid fa-hospital-user icon"> </i>
                    </div>
                    <span class="numbers"><?php echo $totalEntriesLAB; ?></span>
                </div>

                <div class="patients">
                    <div class="card--header">
                        <div class="card--column">
                            <span class="title"> Pending patients for scheduling: </span>
                        </div>
                        <i class="fa-solid fa-hourglass-half icon"> </i>
                    </div>
                    <span class="numbers"><?php echo $totalEntriesSTAT; ?></span>
                </div>
            </div>
        </div>

        <div class="tabular--wrapper">
            <h3 class="main--title"> Overall Data </h3>
            <div class="table--container">
                <table>
                    <thead>
                    <tr>
                            <th> Date: </th>
                            <th> Beneficiary No: </th>
                            <th> Transaction Type: </th>
                            <th> Assistance Given: </th>
                            <th> Status: </th>
                            <th> Action: </th>
                        </tr>
                        

                    </thead>
                    <tbody>

               


                    <?php
 include("php/config.php");

                        $sql="SELECT * FROM transaction WHERE Date = '$currentDate'";
                        $result=$con->query($sql);

                        if(!$result){
                            die("Invalid query: ".$con->error);
                        }

                        while($row=$result->fetch_assoc())
                        {
                            echo "<tr>
                            <td>". $row["Date"] ." </td>
                            <td>". $row["Beneficiary_Id"] ." </td>
                            <td>". $row["TransactionType"] ." </td>
                            <td> ". $row["AssistanceType"] ."  </td>
                            <td>". $row["Status"] ." </td>
                           
                        
                                
                                <td><button> Edit </button></td>
                            </tr>";
                        }
                     
                        ?>
                        <input type="hidden" id="confirmed" name="confirmed" value="">
                           </tbody>
                </table>
            </div>
        </div>
    </div>

<script type = "text/javascript">

      
 
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
        var beneficiaryIdCell = row.cells[1];
        var statusCell = row.cells[4];
        var assistanceTypeCell = row.cells[3];
        var transactionTypeCell = row.cells[2];

        if (dateCell && beneficiaryIdCell && statusCell && assistanceTypeCell && transactionTypeCell) {
            // Get the text content of the cells and convert them to uppercase
            var dateText = dateCell.textContent.toUpperCase();
            var beneficiaryIdText = beneficiaryIdCell.textContent.toUpperCase();
            var statusText = statusCell.textContent.toUpperCase();
            var assistanceTypeText = assistanceTypeCell.textContent.toUpperCase();
            var transactionTypeText = transactionTypeCell.textContent.toUpperCase();

            // Check if the search input value matches any of the columns
            if (dateText.indexOf(input) > -1 || beneficiaryIdText.indexOf(input) > -1 || statusText.indexOf(input) > -1 || assistanceTypeText.indexOf(input) > -1 || transactionTypeText.indexOf(input) > -1) {
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

function medicines() {
        window.location = "http://localhost/public_html/medicines.php";
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