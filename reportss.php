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



// Query to get the count of each response
$sql = "SELECT 
    SUM(CASE WHEN CC1 = 'Alam ko ang CC at nakita ko ito sa napuntahang opisina' THEN 1 ELSE 0 END) AS response1,
    SUM(CASE WHEN CC1 = 'Alam ko ang CC pero hindi ko ito nakita sa napuntahang opisina' THEN 1 ELSE 0 END) AS response2,
    SUM(CASE WHEN CC1 = 'Nalaman ko ang CC nang makita ko ito sa napuntahang opisina' THEN 1 ELSE 0 END) AS response3,
    SUM(CASE WHEN CC1 = 'Hindi ko alam kung ano ang CC at wala akong nakita sa napuntahang opisina' THEN 1 ELSE 0 END) AS response4,
    COUNT(*) AS total_responses1,
    SUM(CASE WHEN CC1 IS NULL OR CC1 = '' THEN 1 ELSE 0 END) AS skipped_responses1
FROM feedback"; 

$result = $con->query($sql);

if ($result->num_rows > 0) {
    // Fetch data
    $data = $result->fetch_assoc();
    $R1= $data['response1'];
    $R2= $data['response2'];
    $R3= $data['response3'];
    $R4= $data['response4'];
    $t1= $data['total_responses1'];
    $s1= $data['skipped_responses1'];
}


// Query to get the count of each response
$sql = "SELECT 
    SUM(CASE WHEN CC2 = 'Madaling makita' THEN 1 ELSE 0 END) AS response5,
    SUM(CASE WHEN CC2 = 'Medyo madaling makita' THEN 1 ELSE 0 END) AS response6,
    SUM(CASE WHEN CC2 = 'Mahirap makita' THEN 1 ELSE 0 END) AS response7,
    SUM(CASE WHEN CC2 = 'Hindi makita' THEN 1 ELSE 0 END) AS response8,
       SUM(CASE WHEN CC2 = 'N/A' THEN 1 ELSE 0 END) AS response9,
    COUNT(*) AS total_responses2,
    SUM(CASE WHEN CC2 IS NULL OR CC2 = '' THEN 1 ELSE 0 END) AS skipped_responses2
FROM feedback";  

$result1 = $con->query($sql);

if ($result1->num_rows > 0) {
    // Fetch data
    $data = $result1->fetch_assoc();
    $R5= $data['response5'];
    $R6= $data['response6'];
    $R7= $data['response7'];
    $R8= $data['response8'];
    $R9= $data['response9'];
    $t2= $data['total_responses2'];
    $s2= $data['skipped_responses2'];
}


$sql = "SELECT 
    SUM(CASE WHEN CC3 = 'Sobrang nakatulong' THEN 1 ELSE 0 END) AS response10,
    SUM(CASE WHEN CC3 = 'Nakatulong naman' THEN 1 ELSE 0 END) AS response11,
    SUM(CASE WHEN CC3 = 'Hindi nakatulong' THEN 1 ELSE 0 END) AS response12,
    SUM(CASE WHEN CC3 = 'N/A' THEN 1 ELSE 0 END) AS response13,
    COUNT(*) AS total_responses3,
    SUM(CASE WHEN CC3 IS NULL OR CC3 = '' THEN 1 ELSE 0 END) AS skipped_responses3
FROM feedback";  

$result1 = $con->query($sql);

if ($result1->num_rows > 0) {
    // Fetch data
    $data = $result1->fetch_assoc();
    $R10= $data['response10'];
    $R11= $data['response11'];
    $R12= $data['response12'];
    $R13= $data['response13'];
   
    $t3= $data['total_responses3'];
    $s3= $data['skipped_responses3'];
}

$sql = "SELECT 
    SUM(CASE WHEN SQD0 = 'Sobrang saya' THEN 1 ELSE 0 END) AS response14,
    SUM(CASE WHEN SQD0 = 'Masaya' THEN 1 ELSE 0 END) AS response15,
    SUM(CASE WHEN SQD0 = 'Neutral' THEN 1 ELSE 0 END) AS response16,
    SUM(CASE WHEN SQD0 = 'Malungkot' THEN 1 ELSE 0 END) AS response17,
     SUM(CASE WHEN SQD0 = 'Dismayado' THEN 1 ELSE 0 END) AS response18,
    COUNT(*) AS total_responses4,
    SUM(CASE WHEN SQD0 IS NULL OR SQD0 = '' THEN 1 ELSE 0 END) AS skipped_responses4
FROM feedback";  

$result1 = $con->query($sql);

if ($result1->num_rows > 0) {
    // Fetch data
    $data = $result1->fetch_assoc();
    $R14= $data['response14'];
    $R15= $data['response15'];
    $R16= $data['response16'];
    $R17= $data['response17'];
    $R18= $data['response18'];
    $t4= $data['total_responses4'];
    $s4= $data['skipped_responses4'];
}

$sql = "SELECT 
    SUM(CASE WHEN SQD1 = 'Sobrang saya' THEN 1 ELSE 0 END) AS response19,
    SUM(CASE WHEN SQD1 = 'Masaya' THEN 1 ELSE 0 END) AS response20,
    SUM(CASE WHEN SQD1 = 'Neutral' THEN 1 ELSE 0 END) AS response21,
    SUM(CASE WHEN SQD1 = 'Malungkot' THEN 1 ELSE 0 END) AS response22,
     SUM(CASE WHEN SQD1 = 'Dismayado' THEN 1 ELSE 0 END) AS response23,
    COUNT(*) AS total_responses5,
    SUM(CASE WHEN SQD1 IS NULL OR SQD1 = '' THEN 1 ELSE 0 END) AS skipped_responses5
FROM feedback";  

$result1 = $con->query($sql);

if ($result1->num_rows > 0) {
    // Fetch data
    $data = $result1->fetch_assoc();
    $R19= $data['response19'];
    $R20= $data['response20'];
    $R21= $data['response21'];
    $R22= $data['response22'];
    $R23= $data['response23'];
    $t5= $data['total_responses5'];
    $s5= $data['skipped_responses5'];
}

$sql = "SELECT 
    SUM(CASE WHEN SQD2 = 'Sobrang saya' THEN 1 ELSE 0 END) AS response24,
    SUM(CASE WHEN SQD2 = 'Masaya' THEN 1 ELSE 0 END) AS response25,
    SUM(CASE WHEN SQD2 = 'Neutral' THEN 1 ELSE 0 END) AS response26,
    SUM(CASE WHEN SQD2 = 'Malungkot' THEN 1 ELSE 0 END) AS response27,
     SUM(CASE WHEN SQD2 = 'Dismayado' THEN 1 ELSE 0 END) AS response28,
    COUNT(*) AS total_responses6,
    SUM(CASE WHEN SQD2 IS NULL OR SQD2 = '' THEN 1 ELSE 0 END) AS skipped_responses6
FROM feedback";  

$result1 = $con->query($sql);

if ($result1->num_rows > 0) {
    // Fetch data
    $data = $result1->fetch_assoc();
    $R24= $data['response24'];
    $R25= $data['response25'];
    $R26= $data['response26'];
    $R27= $data['response27'];
    $R28= $data['response28'];
    $t6= $data['total_responses6'];
    $s6= $data['skipped_responses6'];
}


$sql = "SELECT 
    SUM(CASE WHEN SQD3 = 'Sobrang saya' THEN 1 ELSE 0 END) AS response29,
    SUM(CASE WHEN SQD3 = 'Masaya' THEN 1 ELSE 0 END) AS response30,
    SUM(CASE WHEN SQD3 = 'Neutral' THEN 1 ELSE 0 END) AS response31,
    SUM(CASE WHEN SQD3 = 'Malungkot' THEN 1 ELSE 0 END) AS response32,
     SUM(CASE WHEN SQD3 = 'Dismayado' THEN 1 ELSE 0 END) AS response33,
    COUNT(*) AS total_responses7,
    SUM(CASE WHEN SQD3 IS NULL OR SQD3 = '' THEN 1 ELSE 0 END) AS skipped_responses7
FROM feedback";  

$result1 = $con->query($sql);

if ($result1->num_rows > 0) {
    // Fetch data
    $data = $result1->fetch_assoc();
    $R29= $data['response29'];
    $R30= $data['response30'];
    $R31= $data['response31'];
    $R32= $data['response32'];
    $R33= $data['response33'];
    $t7= $data['total_responses7'];
    $s7= $data['skipped_responses7'];
}

$sql = "SELECT 
    SUM(CASE WHEN SQD4 = 'Sobrang saya' THEN 1 ELSE 0 END) AS response34,
    SUM(CASE WHEN SQD4 = 'Masaya' THEN 1 ELSE 0 END) AS response35,
    SUM(CASE WHEN SQD4 = 'Neutral' THEN 1 ELSE 0 END) AS response36,
    SUM(CASE WHEN SQD4 = 'Malungkot' THEN 1 ELSE 0 END) AS response37,
     SUM(CASE WHEN SQD4 = 'Dismayado' THEN 1 ELSE 0 END) AS response38,
    COUNT(*) AS total_responses8,
    SUM(CASE WHEN SQD4 IS NULL OR SQD4 = '' THEN 1 ELSE 0 END) AS skipped_responses8
FROM feedback";  

$result1 = $con->query($sql);

if ($result1->num_rows > 0) {
    // Fetch data
    $data = $result1->fetch_assoc();
    $R34= $data['response34'];
    $R35= $data['response35'];
    $R36= $data['response36'];
    $R37= $data['response37'];
    $R38= $data['response38'];
    $t8= $data['total_responses8'];
    $s8= $data['skipped_responses8'];
}
$sql = "SELECT 
    SUM(CASE WHEN SQD5 = 'Sobrang saya' THEN 1 ELSE 0 END) AS response39,
    SUM(CASE WHEN SQD5 = 'Masaya' THEN 1 ELSE 0 END) AS response40,
    SUM(CASE WHEN SQD5 = 'Neutral' THEN 1 ELSE 0 END) AS response41,
    SUM(CASE WHEN SQD5 = 'Malungkot' THEN 1 ELSE 0 END) AS response42,
     SUM(CASE WHEN SQD5 = 'Dismayado' THEN 1 ELSE 0 END) AS response43,
    COUNT(*) AS total_responses9,
    SUM(CASE WHEN SQD5 IS NULL OR SQD5 = '' THEN 1 ELSE 0 END) AS skipped_responses9
FROM feedback";  

$result1 = $con->query($sql);

if ($result1->num_rows > 0) {
    // Fetch data
    $data = $result1->fetch_assoc();
    $R39= $data['response39'];
    $R40= $data['response40'];
    $R41= $data['response41'];
    $R42= $data['response42'];
    $R43= $data['response43'];
    $t9= $data['total_responses9'];
    $s9= $data['skipped_responses9'];
}

$sql = "SELECT 
    SUM(CASE WHEN SQD6 = 'Sobrang saya' THEN 1 ELSE 0 END) AS response44,
    SUM(CASE WHEN SQD6 = 'Masaya' THEN 1 ELSE 0 END) AS response45,
    SUM(CASE WHEN SQD6 = 'Neutral' THEN 1 ELSE 0 END) AS response46,
    SUM(CASE WHEN SQD6 = 'Malungkot' THEN 1 ELSE 0 END) AS response47,
     SUM(CASE WHEN SQD6 = 'Dismayado' THEN 1 ELSE 0 END) AS response48,
    COUNT(*) AS total_responses10,
    SUM(CASE WHEN SQD6 IS NULL OR SQD6 = '' THEN 1 ELSE 0 END) AS skipped_responses10
FROM feedback";  

$result1 = $con->query($sql);

if ($result1->num_rows > 0) {
    // Fetch data
    $data = $result1->fetch_assoc();
    $R44= $data['response44'];
    $R45= $data['response45'];
    $R46= $data['response46'];
    $R47= $data['response47'];
    $R48= $data['response48'];
    $t10= $data['total_responses10'];
    $s10= $data['skipped_responses10'];
}
$sql = "SELECT 
    SUM(CASE WHEN SQD7 = 'Sobrang saya' THEN 1 ELSE 0 END) AS response49,
    SUM(CASE WHEN SQD7 = 'Masaya' THEN 1 ELSE 0 END) AS response50,
    SUM(CASE WHEN SQD7 = 'Neutral' THEN 1 ELSE 0 END) AS response51,
    SUM(CASE WHEN SQD7 = 'Malungkot' THEN 1 ELSE 0 END) AS response52,
     SUM(CASE WHEN SQD7 = 'Dismayado' THEN 1 ELSE 0 END) AS response53,
    COUNT(*) AS total_responses11,
    SUM(CASE WHEN SQD7 IS NULL OR SQD7 = '' THEN 1 ELSE 0 END) AS skipped_responses11
FROM feedback";  

$result1 = $con->query($sql);

if ($result1->num_rows > 0) {
    // Fetch data
    $data = $result1->fetch_assoc();
    $R49= $data['response49'];
    $R50= $data['response50'];
    $R51= $data['response51'];
    $R52= $data['response52'];
    $R53= $data['response53'];
    $t11= $data['total_responses11'];
    $s11= $data['skipped_responses11'];
}

$sql = "SELECT 
    SUM(CASE WHEN SQD8 = 'Sobrang saya' THEN 1 ELSE 0 END) AS response54,
    SUM(CASE WHEN SQD8 = 'Masaya' THEN 1 ELSE 0 END) AS response55,
    SUM(CASE WHEN SQD8 = 'Neutral' THEN 1 ELSE 0 END) AS response56,
    SUM(CASE WHEN SQD8 = 'Malungkot' THEN 1 ELSE 0 END) AS response57,
     SUM(CASE WHEN SQD8 = 'Dismayado' THEN 1 ELSE 0 END) AS response58,
    COUNT(*) AS total_responses12,
    SUM(CASE WHEN SQD8 IS NULL OR SQD8 = '' THEN 1 ELSE 0 END) AS skipped_responses12
FROM feedback";  

$result1 = $con->query($sql);

if ($result1->num_rows > 0) {
    // Fetch data
    $data = $result1->fetch_assoc();
    $R54= $data['response54'];
    $R55= $data['response55'];
    $R56= $data['response56'];
    $R57= $data['response57'];
    $R58= $data['response58'];
    $t12= $data['total_responses12'];
    $s12= $data['skipped_responses12'];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Requests for Assistance Breakdown</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="a.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>
<body>
    <div class="sidebar">
        <div class="logo" style="height: 2px;"></div>
        <ul class="menu" style="margin-top: 15px; margin-left: -8px;">
            <li><a href="#" onclick="dashboard()" style="font-size:14px;height:10px;"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
            <li><a href="#" onclick="records()" style="font-size:14px;height:10px;"><i class="fas fa-chart-bar"></i><span>Beneficiary's Records</span></a></li>
            <li><a href="#" onclick="assistance()" style="font-size:14px;height:10px;"><i class="fas fa-handshake-angle"></i><span>Financial Assistance</span></a></li>
            <li><a href="#" onclick="hospital()" style="font-size:14px;height:10px;"><i class="fas fa-hospital"></i><span>Hospitals</span></a></li>
            <li><a href="#" onclick="medicines()" style="font-size:14px;height:10px;"><i class="fa-solid fa-capsules"></i><span>Medicines</span></a></li>
            <li><a href="#" onclick="laboratories()" style="font-size:14px;height:10px;padding-right:-2px;"><i class="fa-solid fa-flask-vial"></i><span>Laboratories</span></a></li>
            <li><a href="#" onclick="dialysis()" style="font-size:14px;height:10px;"><i class="fa-solid fa-flask-vial"></i><span>Dialysis</span></a></li>
            <?php if ($role === 'Admin'): ?>
                <li><a href="#" onclick="employees()" style="font-size:14px;height:10px;"><i class="fas fa-users"></i><span>Employees</span></a></li>
            <?php endif; ?>
            <br>
            <li class="user"><a href="#" onclick="profile()" style="font-size:14px;height:10px;"><i class="fas fa-user"></i><span>Profile</span></a></li>
            <li class="logout"><a href="#" onclick="logout()" style="font-size:14px;height:10px;"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a></li>
        </ul>
    </div>

    <div class="main--content">
        <div class="header--wrapper">
            <div class="header--title">
                <h2>Reports</h2>
            </div>
            </div>
            <div class="user--info" style="background:white; border-radius: 8px; padding:40px;">
            <div class="question" style="background:white; border-radius: 8px;">
            <form method="POST" action="">
                <label for="survey-select" style="font-size:22px;"><strong>Select a survey question: </strong></label>
                <select id="survey-select" onchange="updateGraph()" style="font-size:22px;">
                        <option value="CC1">CC1</option>
                        <option value="CC2">CC2</option>
                        <option value="CC3">CC3</option>
                        <option value="SQD0">SQD0</option>
                        <option value="SQD1">SQD1</option>
                        <option value="SQD2">SQD2</option>
                        <option value="SQD3">SQD3</option>
                        <option value="SQD4">SQD4</option>
                        <option value="SQD5">SQD5</option>
                        <option value="SQD6">SQD6</option>
                        <option value="SQD7">SQD7</option>
                        <option value="SQD8">SQD8</option>
                    </select>
              

                <!-- Display the dynamically changing question -->
                <p style="font-size:20px;text-decoration: underline;"><strong>Question: </strong><span id="survey-question">Select a question to see responses</span></p>

           
                <ul id="response-list" style="margin-left: 50px;"></ul> 

                <p> <strong>Responses:</strong> <span id="responses-count"><?php echo $t1; ?></span></p>  
                 
                <div class="bargraph" id="survey-responses" style="background:white;border-radius: 8px;">
                   
               
                    <div id="bar-chart">
                  
                        <div class="bar-container">
                       <div class="bar" id="bar1"></div><span class="bar-percent" id="percent1"></span>
                        </div>
                        <div class="bar-container">
                          <div class="bar" id="bar2"></div><span class="bar-percent" id="percent2"></span>
                        </div>
                        <div class="bar-container">
                            <div class="bar" id="bar3"></div><span class="bar-percent" id="percent3"></span>
                        </div>
                        <div class="bar-container">
                            <div class="bar" id="bar4"></div><span class="bar-percent" id="percent4"></span>
                        </div>

                        <div class="bar-container">
                            <div class="bar" id="bar5"></div><span class="bar-percent" id="percent5"></span>
                        </div>

                    </div>
                </div>
            </div>
        </div>
            <!-- cc1-->
        <input type="hidden" required name="r1" id="r1" value="<?php echo $R1; ?>">
        <input type="hidden" required name="r2" id="r2" value="<?php echo $R2; ?>">
        <input type="hidden" required name="r3" id="r3" value="<?php echo $R3; ?>">
        <input type="hidden" required name="r4" id="r4" value="<?php echo $R4; ?>">
        <input type="hidden" required name="t1" id="t1" value="<?php echo $t1; ?>">
       
          <!-- cc2-->
        <input type="hidden" required name="r5" id="r5" value="<?php echo $R5; ?>">
        <input type="hidden" required name="r6" id="r6" value="<?php echo $R6; ?>">
        <input type="hidden" required name="r7" id="r7" value="<?php echo $R7; ?>">
        <input type="hidden" required name="r8" id="r8" value="<?php echo $R8; ?>">
        <input type="hidden" required name="r9" id="r9" value="<?php echo $R9; ?>">
        <input type="hidden" required name="t2" id="t2" value="<?php echo $t2; ?>">
       
  <!-- cc3-->
        <input type="hidden" required name="r10" id="r10" value="<?php echo $R10; ?>">
        <input type="hidden" required name="r11" id="r11" value="<?php echo $R11; ?>">
        <input type="hidden" required name="r12" id="r12" value="<?php echo $R12; ?>">
        <input type="hidden" required name="r13" id="r13" value="<?php echo $R13; ?>">
        <input type="hidden" required name="t3" id="t3" value="<?php echo $t3; ?>">
       
  <!-- sqd0-->
        <input type="hidden" required name="r14" id="r14" value="<?php echo $R14; ?>">
        <input type="hidden" required name="r15" id="r15" value="<?php echo $R15; ?>">
        <input type="hidden" required name="r16" id="r16" value="<?php echo $R16; ?>">
        <input type="hidden" required name="r17" id="r17" value="<?php echo $R17; ?>">
        <input type="hidden" required name="r18" id="r18" value="<?php echo $R18; ?>">
        <input type="hidden" required name="t4" id="t4" value="<?php echo $t4; ?>">
        
 <!-- sqd1-->
        <input type="hidden" required name="r19" id="r19" value="<?php echo $R19; ?>">
        <input type="hidden" required name="r20" id="r20" value="<?php echo $R20; ?>">
        <input type="hidden" required name="r21" id="r21" value="<?php echo $R21; ?>">
        <input type="hidden" required name="r22" id="r22" value="<?php echo $R22; ?>">
        <input type="hidden" required name="r23" id="r23" value="<?php echo $R23; ?>">
        <input type="hidden" required name="t5" id="t5" value="<?php echo $t5; ?>">

 <!-- sqd2-->
        <input type="hidden" required name="r24" id="r24" value="<?php echo $R24; ?>">
        <input type="hidden" required name="r25" id="r25" value="<?php echo $R25; ?>">
        <input type="hidden" required name="r26" id="r26" value="<?php echo $R26; ?>">
        <input type="hidden" required name="r27" id="r27" value="<?php echo $R27; ?>">
        <input type="hidden" required name="r28" id="r28" value="<?php echo $R28; ?>">
        <input type="hidden" required name="t6" id="t6" value="<?php echo $t6; ?>">
 
<!-- sqd3-->
        <input type="hidden" required name="r29" id="r29" value="<?php echo $R29; ?>">
        <input type="hidden" required name="r30" id="r30" value="<?php echo $R30; ?>">
        <input type="hidden" required name="r31" id="r31" value="<?php echo $R31; ?>">
        <input type="hidden" required name="r32" id="r32" value="<?php echo $R32; ?>">
        <input type="hidden" required name="r33" id="r33" value="<?php echo $R33; ?>">
        <input type="hidden" required name="t7" id="t7" value="<?php echo $t7; ?>">
       
<!-- sqd4-->
<input type="hidden" required name="r34" id="r34" value="<?php echo $R34; ?>">
        <input type="hidden" required name="r35" id="r35" value="<?php echo $R35; ?>">
        <input type="hidden" required name="r36" id="r36" value="<?php echo $R36; ?>">
        <input type="hidden" required name="r37" id="r37" value="<?php echo $R37; ?>">
        <input type="hidden" required name="r38" id="r38" value="<?php echo $R38; ?>">
        <input type="hidden" required name="t8" id="t8" value="<?php echo $t8; ?>">
      
<!-- sqd5-->
<input type="hidden" required name="r39" id="r39" value="<?php echo $R39; ?>">
        <input type="hidden" required name="r40" id="r40" value="<?php echo $R40; ?>">
        <input type="hidden" required name="r41" id="r41" value="<?php echo $R41; ?>">
        <input type="hidden" required name="r42" id="r42" value="<?php echo $R42; ?>">
        <input type="hidden" required name="r43" id="r43" value="<?php echo $R43; ?>">
        <input type="hidden" required name="t9" id="t9" value="<?php echo $t9; ?>">
      
<!-- sqd6-->
<input type="hidden" required name="r44" id="r44" value="<?php echo $R44; ?>">
        <input type="hidden" required name="r45" id="r45" value="<?php echo $R45; ?>">
        <input type="hidden" required name="r46" id="r46" value="<?php echo $R46; ?>">
        <input type="hidden" required name="r47" id="r47" value="<?php echo $R47; ?>">
        <input type="hidden" required name="r48" id="r48" value="<?php echo $R48; ?>">
        <input type="hidden" required name="t10" id="t10" value="<?php echo $t10; ?>">
      
<!-- sqd7-->
<input type="hidden" required name="r49" id="r49" value="<?php echo $R49; ?>">
        <input type="hidden" required name="r50" id="r50" value="<?php echo $R50; ?>">
        <input type="hidden" required name="r51" id="r51" value="<?php echo $R51; ?>">
        <input type="hidden" required name="r52" id="r52" value="<?php echo $R52; ?>">
        <input type="hidden" required name="r53" id="r53" value="<?php echo $R53; ?>">
        <input type="hidden" required name="t11" id="t11" value="<?php echo $t11; ?>">
        
<!-- sqd8-->
<input type="hidden" required name="r54" id="r54" value="<?php echo $R54; ?>">
        <input type="hidden" required name="r55" id="r55" value="<?php echo $R55; ?>">
        <input type="hidden" required name="r56" id="r56" value="<?php echo $R56; ?>">
        <input type="hidden" required name="r57" id="r57" value="<?php echo $R57; ?>">
        <input type="hidden" required name="r58" id="r58" value="<?php echo $R58; ?>">
        <input type="hidden" required name="t12" id="t12" value="<?php echo $t12; ?>">
      

            </form>
        <script>
            function updateGraph() {
                const selectedSurvey = document.getElementById("survey-select").value;
                let responses, responseLabels, questionText;

                switch(selectedSurvey) {
                    case 'CC1':
                        responses = [<?php echo "$R1, $R2, $R3, $R4"; ?>];
                      
                        responseLabels = [
                            "Alam ko ang CC at nakita ko ito sa napuntahang opisina",
                            "Alam ko ang CC pero hindi ko ito nakita sa napuntahang opisina",
                            "Nalaman ko ang CC nang makita ko ito sa napuntahang opisina",
                            "Hindi ko alam kung ano ang CC at wala akong nakita sa napuntahang opisina"
                        ];
                        questionText = "Alin sa mga sumusunod ang naglalarawan sa iyong kaalaman sa CC (Citizen's Charter)?";
                        
                        break;
                       
                    case 'CC2':
                        responses = [<?php echo "$R5, $R6, $R7, $R8, $R9"; ?>];
                        responseLabels = [
                            "Madaling makita",
                            "Medyo madaling makita",
                            "Mahirap makita",
                            "Hindi makita",
                            "N/A"
                        ];
                        questionText = "Kung alam ang CC (Citizen's Charter)(Pinili ang opsyon 1-3 sa CC1), masasabi mo ba na ang CC nang napuntahang opisina ay...";
                        break;
                        case 'CC3':
                        responses = [<?php echo "$R10, $R11, $R12, $R13"; ?>];
                        responseLabels = [
                            "Sobrang nakatulong",
                            "Nakatulong naman",
                            "Hindi nakatulong",
                             "N/A"
                        ];
                        questionText = "Kung alam ang CC  (Citizen's Charter) (Pinili ang opsyon 1-3 sa CC1),  gaano nakatulong ang CC sa transaksyon mo?";
                        break;

                        case 'SQD0':
                        responses = [<?php echo "$R14, $R15, $R16, $R17,$R18"; ?>];
                        responseLabels = [
                            "Sobrang saya",
                            "Masaya",
                            "Neutral",
                             "Malungkot",
                             "Dismayado"
                        ];
                        questionText = "Nasiyahan ako sa serbisyo na aking natanggap sa napuntahan na tanggapan.";
                        break;

                        case 'SQD1':
                        responses = [<?php echo "$R19, $R20, $R21, $R22,$R23"; ?>];
                        responseLabels = [
                            "Sobrang saya",
                            "Masaya",
                            "Neutral",
                             "Malungkot",
                             "Dismayado"
                        ];
                        questionText = "Makatwiran ang oras na aking ginugol para sa pagproseso ng aking transaksyon.";
                        break;

                        case 'SQD2':
                        responses = [<?php echo "$R24, $R25, $R26, $R27,$R28"; ?>];
                        responseLabels = [
                            "Sobrang saya",
                            "Masaya",
                            "Neutral",
                             "Malungkot",
                             "Dismayado"
                        ];
                        questionText = "Ang opisina ay sumusunod sa mga kinakailangang dokumento at mga hakbang batay sa impormasyong ibinigay.";
                        break; 
                        
                        case 'SQD3':
                        responses = [<?php echo "$R29, $R30, $R31, $R32,$R33"; ?>];
                        responseLabels = [
                            "Sobrang saya",
                            "Masaya",
                            "Neutral",
                             "Malungkot",
                             "Dismayado"
                        ];
                        questionText = "Ang mga hakbang sa pagproseso, kasama na ang pagbayad ay madali at simple lamang.";
                        break; 
                        
                        case 'SQD4':
                        responses = [<?php echo "$R34, $R35, $R36, $R37,$R38"; ?>];
                        responseLabels = [
                            "Sobrang saya",
                            "Masaya",
                            "Neutral",
                             "Malungkot",
                             "Dismayado"
                        ];
                        questionText = "Mabilis at madali akong nakahanap ng impormasyon tungkol sa aking transaksyon mula sa opisina o sa website nito.";
                        break;
                        
                        case 'SQD5':
                        responses = [<?php echo "$R39, $R40, $R41, $R42,$R43"; ?>];
                        responseLabels = [
                            "Sobrang saya",
                            "Masaya",
                            "Neutral",
                             "Malungkot",
                             "Dismayado"
                        ];
                        questionText = "Nagbayad ako ng makatwirang halaga para sa aking transaksyon. (Kung ang sebisyo ay ibinigay ng libre. Piliin ang N/A.)";
                        break;
                        
                        
                        case 'SQD6':
                        responses = [<?php echo "$R44, $R45, $R46, $R47,$R48"; ?>];
                        responseLabels = [
                            "Sobrang saya",
                            "Masaya",
                            "Neutral",
                             "Malungkot",
                             "Dismayado"
                        ];
                        questionText = "Pakiramdam ko ay patas ang opisina sa lahat. o \"walang palakasan\", sa aking transaksyon";
                        break;
                        
                        case 'SQD7':
                        responses = [<?php echo "$R49, $R50, $R51, $R52,$R53"; ?>];
                        responseLabels = [
                            "Sobrang saya",
                            "Masaya",
                            "Neutral",
                             "Malungkot",
                             "Dismayado"
                        ];
                        questionText = "Magalang akong trinato ng mga tauhan, at (kung sakali ako ay humingi ng tulong) alam ko na sila ay handang tumulong sa akin.";
                        break; 
                        
                        case 'SQD8':
                        responses = [<?php echo "$R54, $R55, $R56, $R57,$R58"; ?>];
                        responseLabels = [
                            "Sobrang saya",
                            "Masaya",
                            "Neutral",
                             "Malungkot",
                             "Dismayado"
                        ];
                        questionText = "Nakuha ko ang kinakallangan ko mula sa tanggapan ng gobyemo. kung tinanggihan man. Ito ay sapat na ipinaliwanag sa akin.";
                        break;
                    // Add more cases for other surveys...
                    default:
                        responses = [0, 0, 0, 0, 0];
                        responseLabels = [];
                        questionText = "Select a valid survey question";
                }

                // Update the question text
                document.getElementById("survey-question").innerText = questionText;

                // Update the response list
                const responseList = document.getElementById("response-list");
                responseList.innerHTML = ''; // Clear existing list items
                responseLabels.forEach((label, index) => {
                    const listItem = document.createElement("li");
                    listItem.textContent = label;
                    responseList.appendChild(listItem);
                });
                if (selectedSurvey !== 'CC1' || selectedSurvey !== 'CC3') {
        document.getElementById('bar5').style.display = 'flex'; // Show bar5 for surveys other than CC1
    } else {
        document.getElementById('bar5').style.display = 'none'; // Hide bar5 for CC1
    }   

              

             const responsesCount = <?php echo $t1; ?>; // Total responses
              

                document.getElementById("responses-count").innerText = responsesCount;
             
                responses.forEach((value, index) => {
                    const bar = document.getElementById(`bar${index + 1}`);
                    const percentLabel = document.getElementById(`percent${index + 1}`);
                    const percentage = (responsesCount > 0) ? (value / responsesCount) * 100 : 0; // Prevent division by zero

                    // Update bar width
                    bar.style.width = `${percentage}%`;

                    // Update percentage label
                    percentLabel.innerText = `${percentage.toFixed(2)}%`;
                });


                const responsesCount2 = <?php echo $t2; ?>; // Total responses
                const skippedCount2 = <?php echo $s2; ?>; // Skipped responses

                document.getElementById("responses-count").innerText = responsesCount2;
                document.getElementById("skipped-count").innerText = skippedCount2;
                responses.forEach((value, index) => {
                    const bar = document.getElementById(`bar${index + 1}`);
                    const percentLabel = document.getElementById(`percent${index + 1}`);
                    const percentage = (responsesCount2 > 0) ? (value / responsesCount2) * 100 : 0; // Prevent division by zero

                    // Update bar width
                    bar.style.width = `${percentage}%`;

                    // Update percentage label
                    percentLabel.innerText = `${percentage.toFixed(2)}%`;
                });



            }
        
            // Initial load of the graph
            updateGraph();


            
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
        </script>
    </div>
</body>
</html>