<?php
session_start();
include("php/config.php");
require_once('vendor/tecnickcom/tcpdf/tcpdf.php');

// Redirect to login if not authenticated
if (!isset($_SESSION['Emp_ID'])) {
    header("Location: login.php");
    exit;
}


// Set PHP and MySQL timezone
date_default_timezone_set('Asia/Manila');
mysqli_query($con, "SET time_zone = '+08:00'");

// Fetch user's information
$id = intval($_SESSION['Emp_ID']); 
$query = mysqli_query($con, "SELECT * FROM employees WHERE Emp_ID=$id");
if (!$query) {
    die("Error fetching user data: " . mysqli_error($con));
}
$user = mysqli_fetch_assoc($query);
$branch = mysqli_real_escape_string($con, $user['Office']);
$role = mysqli_real_escape_string($con, $user['role']);

// Initialize selected dates
$selectedDate = isset($_POST['dailyDate']) ? $_POST['dailyDate'] : date("Y-m-d");
$selectedYearDaily = date("Y", strtotime($selectedDate));
$selectedMonthDaily = date("m", strtotime($selectedDate));

$selectedYearMonthly = isset($_POST['yearMonthly']) ? intval($_POST['yearMonthly']) : date("Y");
$selectedMonthMonthly = isset($_POST['month']) ? intval($_POST['month']) : date("m");

$selectedYearYearly = isset($_POST['yearYearly']) ? intval($_POST['yearYearly']) : date("Y");

// Function to consolidate data
function consolidateData($data) {
    $consolidated = [
        'Hospital Bills' => 0,
        'Laboratories' => 0,
        'Medicine' => 0,
        'Financial Assistance' => 0
    ];

    $hospitalMapping = [
        'Hospital Bills-Bataan Doctor\'s Hospital & Medical Center',
        'Hospital Bills-Balanga Medical Center Corporation',
        'Hospital Bills-Bataan Peninsula Medical Center',
        'Hospital Bills-Bataan St. Joseph Hospital & Medical Center',
        'Hospital Bills-Isaac & Catalina Medical Center',
        'Hospital Bills-Mt. Samat Medical Center',
        'Hospital Bills-Orion St. Michael Hospital',
        'Hospital Bills-Jose B. Lingad Memorial General Hospital',
        'Hospital Bills-Lung Center of the Philippines',
        'Hospital Bills-National Children\'s Hospital',
        'Hospital Bills-National Kidney & Transplant Institute',
        'Hospital Bills-Philippine General Hospital',
        'Hospital Bills-Philippine Heart Center',
        'Hospital Bills-The Philippines Children Medical Center'
    ];

    $faMapping = [
        'Financial Assistance-Burial',
        'Financial Assistance-Chemotherapy & Radiation',
        'Financial Assistance-Dialysis'
    ];

    foreach ($data as $type => $count) {
        if (in_array($type, $hospitalMapping)) {
            $consolidated['Hospital Bills'] += $count;
        } elseif (in_array($type, $faMapping)) {
            $consolidated['Financial Assistance'] += $count;
        } elseif (isset($consolidated[$type])) {
            $consolidated[$type] += $count;
        }
    }

    return $consolidated;
}

// Fetch report data function
function fetchReportData($con, $branch, $type, $year = null, $month = null, $day = null) {
    $branch = mysqli_real_escape_string($con, $branch);
    $sql = "SELECT AssistanceType, COUNT(*) AS count FROM history WHERE branch='$branch'";

    if ($type === 'daily' && $day) {
        $sql .= " AND DATE(ReceivedDate) = '" . mysqli_real_escape_string($con, $day) . "'";
    } elseif ($type === 'monthly' && $year && $month) {
        $sql .= " AND YEAR(ReceivedDate) = $year AND MONTH(ReceivedDate) = $month";
    } elseif ($type === 'yearly' && $year) {
        $sql .= " AND YEAR(ReceivedDate) = $year";
    }

    $sql .= " GROUP BY AssistanceType";

    $result = mysqli_query($con, $sql);
    if (!$result) {
        return ["error" => "Error fetching $type report: " . mysqli_error($con)];
    }

    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[$row['AssistanceType']] = intval($row['count']);
    }

    return consolidateData($data);
}

// Fetch reports
$dailyData = fetchReportData($con, $branch, 'daily', $selectedYearDaily, $selectedMonthDaily, $selectedDate);
$monthlyData = fetchReportData($con, $branch, 'monthly', $selectedYearMonthly, $selectedMonthMonthly);
$yearlyData = fetchReportData($con, $branch, 'yearly', $selectedYearYearly);

// Handle errors
$dailyError = $dailyData['error'] ?? null;
$monthlyError = $monthlyData['error'] ?? null;
$yearlyError = $yearlyData['error'] ?? null;

unset($dailyData['error'], $monthlyData['error'], $yearlyData['error']);

// Calculate totals
$dailyTotal = array_sum($dailyData);
$monthlyTotal = array_sum($monthlyData);
$yearlyTotal = array_sum($yearlyData);



?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Requests for Assistance Breakdown</title>
    <link rel="stylesheet" href="dashboard.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">
    <style>
        /* Add any additional CSS here */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
        }
        .report-section {
            margin-bottom: 50px;
        }
    </style>
      <script>
        // Auto-submit the form when the date changes
        function autoSubmit() {
            document.getElementById("dateForm").submit();
        }
    </script>

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
                <h1>Assistance Reports</h1><hr><br><br>
 <!-- Daily Assistance Report -->
 <div class="report-section"  style="margin-left: 50px;">
            <h2 style="color:Black;">Daily Assistance Report</h2><hr>
            <form id="dateForm" method="POST">
            <label for="dailyDate" class="mr-2">Select Date:</label>
            <input type="date" name="dailyDate" value="<?= htmlspecialchars($selectedDate) ?>" onchange="autoSubmit()" required>
           
        </form>
        <form id="dailyForm" method="POST" action="generate_pdf.php" style="display:inline;" target="_blank">
    <input type="hidden" name="reportType" value="daily">
        <input type="hidden" name="data" value="<?= htmlspecialchars(json_encode($dailyData)) ?>">
        <input type="hidden" name="total" value="<?= $dailyTotal ?>">
        <button type="submit" style="background:green" class="btn btn-primary mb-2 ml-2">Generate Daily Report</button>
            </form >
    
            <?php if ($dailyError): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($dailyError) ?></div>
            <?php endif; ?>
            <table>
                <thead>
                    <tr>
                        <th style="background:#1477d2; padding-right: 170px; font-size: 20px;">Assistance Type</th>
                        <th style="background:#1477d2; padding-right: 170px; font-size: 20px;">Number of Beneficiary</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($dailyData)): ?>
                        <tr><td colspan="2">No data found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($dailyData as $type => $count): ?>
                            <tr>
                                <td style="font-size: 18px;"><?= htmlspecialchars($type) ?></td>
                                <td style="font-size: 18px;"><?= $count ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <tr>
                            <td style="color:Green; font-size: 20px;"><strong>TOTAL</strong></td>
                            <td style="color:Green; font-size: 20px;"><strong><?= $dailyTotal ?></strong></td> <!-- Display total -->
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Monthly Assistance Report -->
        <div class="report-section" style="margin-left: 50px;">
            <h2 style="color:black;">Monthly Assistance Report</h2><hr>
            <form id="monthlyForm" method="POST">
                <label for="yearMonthly" class="mr-2">Select Year:</label>
                <select name="yearMonthly" onchange="this.form.submit()">
                    <?php for ($i = date("Y") - 5; $i <= date("Y"); $i++): ?>
                        <option value="<?= $i ?>" <?= $i == $selectedYearMonthly ? 'selected' : '' ?>><?= $i ?></option>
                    <?php endfor; ?>
                </select>
                <label style="margin-left:20px;" for="month" class="mr-2">Select Month:</label>
                <select name="month" onchange="this.form.submit()">
                    <?php for ($m = 1; $m <= 12; $m++): ?>
                        <option value="<?= $m ?>" <?= $m == $selectedMonthMonthly ? 'selected' : '' ?>><?= date('F', mktime(0, 0, 0, $m, 1)) ?></option>
                    <?php endfor; ?>
                </select>
              
            </form>
            <form id="monthlyForm" method="POST" action="generate_pdf.php" style="display:inline;" target="_blank">
    <input type="hidden" name="reportType" value="monthly">
        <input type="hidden" name="data" value="<?= htmlspecialchars(json_encode($monthlyData)) ?>">
        <input type="hidden" name="total" value="<?= $monthlyTotal ?>">
        <button type="submit" style="background:green" class="btn btn-primary mb-2 ml-2">Generate Monthly Report</button>
            </form >
    
            <?php if ($monthlyError): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($monthlyError) ?></div>
            <?php endif; ?>
            <table>
                <thead>
                    <tr>
                        <th style="background:#1477d2; padding-right: 170px; font-size: 20px;">Assistance Type</th>
                        <th style="background:#1477d2; padding-right: 170px; font-size: 20px;">Number of Beneficiary</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($monthlyData)): ?>
                        <tr><td colspan="2">No data found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($monthlyData as $type => $count): ?>
                            <tr>
                                <td style="font-size: 18px;"><?= htmlspecialchars($type) ?></td>
                                <td style="font-size: 18px;"><?= $count ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <tr>
                            <td style="color:Green;font-size: 20px;"><strong>TOTAL</strong></td>
                            <td style="color:Green;font-size: 20px;"><strong><?= $monthlyTotal ?></strong></td> <!-- Display total -->
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
<!-- Yearly Assistance Report -->
<div class="report-section" style="margin-left: 50px;">
    <h2 style="color:black;">Yearly Assistance Report</h2><hr>
    <form id="yearlyForm" method="POST" action="" style="display:inline;">
        <label for="yearYearly" class="mr-2">Select Year:</label>
        <select name="yearYearly" onchange="this.form.submit()">
            <?php for ($i = date("Y") - 5; $i <= date("Y"); $i++): ?>
                <option value="<?= $i ?>" <?= $i == $selectedYearYearly ? 'selected' : '' ?>><?= $i ?></option>
            <?php endfor; ?>
        </select>
       
    </form>
    <form id="yearlyForm" method="POST" action="generate_pdf.php" style="display:inline;" target="_blank">
    <input type="hidden" name="reportType" value="yearly">
        <input type="hidden" name="data" value="<?= htmlspecialchars(json_encode($yearlyData)) ?>">
        <input type="hidden" name="total" value="<?= $yearlyTotal ?>">
        <button type="submit" style="background:green" class="btn btn-primary mb-2 ml-2">Generate Yearly Report</button>
            </form >
    
    <?php if ($yearlyError): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($yearlyError) ?></div>
    <?php endif; ?>
    <table>
        <thead>
            <tr>
                <th style="background:#1477d2;font-size: 20px; padding-right: 170px; font-size: 20px;">Assistance Type</th>
                <th style="background:#1477d2;font-size: 20px; padding-right: 170px;font-size: 20px;">Number of Beneficiary</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($yearlyData)): ?>
                <tr><td colspan="2">No data found.</td></tr>
            <?php else: ?>
                <?php foreach ($yearlyData as $type => $count): ?>
                    <tr>
                        <td style="font-size: 18px;"><?= htmlspecialchars($type) ?></td>
                        <td style="font-size: 18px;"><?= $count ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td style="color:Green; font-size: 20px;"><strong>TOTAL</strong></td>
                    <td style="color:Green; font-size: 20px;"><strong><?= $yearlyTotal ?></strong></td> <!-- Display total -->
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

    </div>
    <script>
        function dashboard() {
            // Implement dashboard navigation
            window.location.href = 'dashboard.php';
        }
        function records() {
            // Implement records navigation
            window.location.href = 'patients-records.php';
        }
        function assistance() {
            // Implement assistance navigation
            window.location.href = 'assistance.php';
        }
        function hospital() {
            // Implement hospital navigation
            window.location.href = 'hospital.php';
        }
        function medicines() {
            // Implement medicines navigation
            window.location.href = 'medicines.php';
        }
        function laboratories() {
            // Implement laboratories navigation
            window.location.href = 'laboratories.php';
        }
        function employees() {
            // Implement employees navigation
            window.location.href = 'employeeRecords.php';
        }
        function profile() {
            // Implement profile navigation
            window.location.href = 'profileadmin.php';
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
    </script>
</body>
</html>
