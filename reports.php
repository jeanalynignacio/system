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
    header("Location: employee-login.php");
    exit;
}

date_default_timezone_set('Asia/Manila');
// Get the current year
$currentYear = date("Y");

// Prepare an array to store monthly data for each assistance type
$assistance_types = [
    'Hospital',
    'Medicine',
    'Financial Assistance-Burial',
    'Financial Assistance-Chemotherapy & Radiation',
    'Financial Assistance-Dialysis',
    'Laboratories'
];

// Function to generate options for year dropdown
function generateYearOptions($startYear, $endYear, $selectedYear) {
    $options = '';
    for ($year = $startYear; $year <= $endYear; $year++) {
        $selected = ($year == $selectedYear) ? 'selected' : '';
        $options .= "<option value='$year' $selected>$year</option>";
    }
    return $options;
}
// Default to current year if no year is selected
$selectedYear = isset($_GET['year']) ? intval($_GET['year']) : $currentYear;

// Prepare the SQL query to fetch monthly data for each assistance type
$sql = "SELECT 
            MONTH(h.ReceivedDate) AS month,
            h.AssistanceType,
            COUNT(h.Beneficiary_ID) AS count
        FROM history h
        INNER JOIN beneficiary b ON b.Beneficiary_Id = h.Beneficiary_ID
        WHERE branch='$branch'
          AND YEAR(h.ReceivedDate) = $selectedYear
          AND h.AssistanceType IN (
              'Hospital Bills', 
              'Medicine', 
              'Financial Assistance-Burial', 
              'Financial Assistance-Chemotherapy & Radiation',
              'Financial Assistance-Dialysis',
              'Laboratories'
          )
        GROUP BY MONTH(h.ReceivedDate), h.AssistanceType";

$result = $con->query($sql);

if (!$result) {
    die("Invalid query: " . $con->error);
}

// Fetch monthly data from the result and store in the $monthly_data array
$monthly_data = array_fill(0, 12, [
    'Hospital Bills' => 0,
    'Medicine' => 0,
    'Financial Assistance-Burial' => 0,
    'Financial Assistance-Chemotherapy & Radiation' => 0,
    'Financial Assistance-Dialysis' => 0,
    'Laboratories' => 0
]);

while ($row = $result->fetch_assoc()) {
    $month = intval($row['month']) - 1;
    $assistanceType = $row['AssistanceType'];
    $monthly_data[$month][$assistanceType] = $row['count'];
}

$total_assistance = 0;
foreach ($monthly_data as $month_data) {
    $total_assistance += array_sum($month_data);
}
$yearly_totals = [
    'Hospital Bills' => 0,
    'Medicine' => 0,
    'Financial Assistance-Burial' => 0,
    'Financial Assistance-Chemotherapy & Radiation' => 0,
    'Financial Assistance-Dialysis' => 0,
    'Laboratories' => 0
];

foreach ($monthly_data as $month_data) {
    foreach ($yearly_totals as $type => $total) {
        $yearly_totals[$type] += $month_data[$type];
    }
}

// Close the database connection
$con->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Requests for Assistance Breakdown</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="dashboard.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>
<body>
    <div class="sidebar">
        <!-- Sidebar content -->
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
            <li class="user">
                <a href="#" onclick="profile()">
                    <i class="fas fa-user"></i>
                    <span>Profile</span>
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
                
                <h2>Reports</h2>
                <form id="yearForm" action="" method="GET">
                    <select name="year" id="yearDropdown" onchange="this.form.submit()">
                        <?php echo generateYearOptions(2023, 2040, $selectedYear); ?>
                    </select>
                </form>
            </div>
            <div class="user--info">
                
                <img src="images/background.png" alt=""/>
            </div>
            </div>
            <div class="header--wrapper">
            <div class="header--title">
        </div> <h3 style="margin-left:100px;"  name="Assistancetotal">Total Assistance for <?php echo $selectedYear; ?>: <?php echo $total_assistance; ?></h3>

        <ul style="margin-top:20px;margin-left:-20px;">
           <h3 style="margin-bottom:20px; font-size:18px; "> TOTAL BENEFICIARIES PER ASSISTANCE</h3>
            <?php foreach ($yearly_totals as $type => $total): ?>
                <li><?php echo $type . ": " . $total; ?></li>
            <?php endforeach; ?>
        </ul>
       
        <div class="card-container" style="position: relative; height:60vh; width:120%;  background: #ebe9e9; margin: 50px 50px 50px 50px;">
            <canvas id="myChart"></canvas>
            </div>
            </div>
        </div>
      

        <script>
            // PHP variables to JavaScript
            var monthly_data = <?php echo json_encode($monthly_data); ?>;
            var selectedYear = <?php echo $selectedYear; ?>;

            // Month labels for chart
            var months = [
                'January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'
            ];
// Calculate the maximum value among all datasets
var maxDataValue = Math.max(
    ...monthly_data.flatMap(data => Object.values(data))
);

// Determine the suggested maximum for the y-axis
var suggestedMax = Math.max(10, Math.ceil(maxDataValue / 10) * 10);

            // Chart.js code
            var ctx = document.getElementById('myChart').getContext('2d');
            var myChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: months,
                    datasets: [
                        {
                            label: 'Hospital Bills',
                            data: monthly_data.map(data => data['Hospital Bills']),
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Medicines',
                            data: monthly_data.map(data => data['Medicine']),
                            backgroundColor: 'rgba(54, 162, 235, 0.2)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Laboratories',
                            data: monthly_data.map(data => data['Laboratories']),
                            backgroundColor: 'rgba(255, 159, 64, 0.2)',
                            borderColor: 'rgba(255, 159, 64, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Financial Assistance-Burial',
                            data: monthly_data.map(data => data['Financial Assistance-Burial']),
                            backgroundColor: 'rgba(255, 206, 86, 0.2)',
                            borderColor:
                            'rgba(255, 206, 86, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Financial Assistance-Chemotherapy & Radiation',
                            data: monthly_data.map(data => data['Financial Assistance-Chemotherapy & Radiation']),
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Financial Assistance-Dialysis',
                            data: monthly_data.map(data => data['Financial Assistance-Dialysis']|| 0),
                            backgroundColor: 'rgba(153, 102, 255, 0.2)',
                            borderColor: 'rgba(153, 102, 255, 1)',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1, // Adjust as needed based on your data range
                    suggestedMax: suggestedMax // Set the suggested maximum value for the y-axis
                 
                }
            }
                    }
                }
            });

            // Function to handle year change
            function handleYearChange() {
                var yearDropdown = document.getElementById('yearDropdown');
                var selectedYear = yearDropdown.value;
                window.location.href = 'reports.php?year=' + selectedYear; // Redirect to the selected year
            }

            // Bind change event to the dropdown
            document.getElementById('yearDropdown').addEventListener('change', handleYearChange);
            
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
    </div>
</body>
</html>
