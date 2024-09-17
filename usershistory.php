<?php
session_start();
include("php/config.php");

// Redirect to login page if session is not valid
if (!isset($_SESSION['valid'])) {
    header("Location: index.php");
    exit();
}

if (isset($_SESSION['valid'])) {
  $id = $_SESSION['id'];
  $query = mysqli_query($con, "SELECT * FROM beneficiary WHERE Representative_ID = '$id'");
if ($result = mysqli_fetch_assoc($query)) {
    $res_Id = $result['Beneficiary_Id'];

}
}

// Fetch user details
$query = mysqli_query($con, "SELECT * FROM users WHERE Id = '$id'");
if ($result = mysqli_fetch_assoc($query)) {
    $res_Id = $result['Id'];
    $res_Fname = $result['Firstname'];
    $res_Lname = $result['Lastname'];
}

$currentDate = date("Y-m-d");

// Define pagination variables
$records_per_page = 10; // Number of records to display per page
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Get current page number, default to 1 if not set
$offset = ($current_page - 1) * $records_per_page;

// Get the total number of entries
$sql = "SELECT COUNT(*) AS totalEntries FROM beneficiary";
$result = $con->query($sql);

if (!$result) {
    die("Invalid query: " . $con->error);
}

$row = $result->fetch_assoc();
$totalEntries = $row['totalEntries'];

// Calculate total pages
$totalPages = ceil($totalEntries / $records_per_page);
$Sql2=mysqli_query($con,"Select * FROM beneficiary WHERE Representative_ID='$id'");
if ($result = mysqli_fetch_assoc($Sql2)) {
  $BeneID = $result['Beneficiary_Id'];

}
// Query to check if the beneficiary exists
$checkBeneficiaryQuery = "SELECT COUNT(*) as count FROM history WHERE Beneficiary_Id = '$BeneID'";
$beneficiaryResult = $con->query($checkBeneficiaryQuery);
$beneficiaryCount = $beneficiaryResult->fetch_assoc()['count'];

$transactionResult = [];
if ($beneficiaryCount > 0) {
    // Fetch paginated beneficiaries
    $sql = "
        SELECT * FROM history
        WHERE Beneficiary_ID = '$BeneID'
        ORDER BY ReceivedDate ASC, ReceivedTime ASC
        LIMIT $records_per_page OFFSET $offset
    ";
    $transactionResult = $con->query($sql);
    if (!$transactionResult) {
        die("Invalid query: " . $con->error);
    }
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>History</title>
<link rel="stylesheet" href="usershistory.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>
<body>

<div class="all-content" style="background:white;">
  <!-- navbar !-->

  <nav
    class="navbar navbar-expand-lg navbar-light"
    style="background-color: white;"  >
    <div class="container-fluid">
    <a
        class="navbar-brand"   href="#" id="logo"  style="font-size: 15px; color: #1477d2;">
        <img src="images/background.png" /> Provincial Government of Bataan- Special Assistance Program
      </a>
      <button
        class="navbar-toggler"
        type="button"
        data-bs-toggle="collapse"
        data-bs-target="#navbarSupportedContent"
        aria-controls="navbarSupportedContent"
        aria-expanded="false"
        aria-label="Toggle navigation"
      >
        <span>
          <i
            class="fa-solid fa-bars"
            style="color: #1477d2; font-size: 23px"
          ></i
        ></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a style = " color: #1477d2; padding-left:10px;" class="nav-link" aria-current="page" href="usershomepage.php">
                 Home 
            </a>
          </li>
          
        
          
         <li class="nav-item">
            <a style = " color: #1477d2; padding-left:10px;" class="nav-link"  onclick="toggleMenu()" style="color: white" >Profile </a>
          </li>

<div class="sub-menu-wrap" id="subMenu">
<div class="sub-menu">
    <div class="user-info">
    <img src="images/profile.png">
        <h2><?php echo $res_Fname; ?> <?php echo $res_Lname; ?></h2>

        </div>
        <hr>



<form action="edit.php" method="POST" class="sub-menu-link">
<input type="hidden" name="userId" value="<?php echo $res_Id; ?>">
<button type="submit" class="btn-edit-profile" >
    <img src="images/profile.png">
    <p>Edit Profile</p>
    
</button>
</form>
<form action="status.php" method="POST" class="sub-menu-link">
<input type="hidden" name="userId" value="<?php echo $res_Id; ?>">
<button type="submit" class="btn-edit-profile" >
    <img src="images/profile.png">
    <p>Status </p>
    
</button>
</form>

    <a href="logout.php" class="sub-menu-link">
            <img src="images/logout.png">
            <p> Log out</p>
       
    </a>
        
         </ul>
      </div>
    </div>
  </nav>

    
              

  <?php
  $date = date('Y-m-d');

  // Calculate the date from two months ago
  $two_months_ago = date('Y-m-d', strtotime('-2 months', strtotime($date)));
  
  // OR simply use:
  $two_months_ago = date('Y-m-d', strtotime('-2 months'));
$query = "SELECT * FROM history WHERE Beneficiary_ID = '$BeneID'  AND ReceivedDate > '$two_months_ago'";

// Execute the query
$feedbackResult = $con->query($query);

// Check if the query returned any rows
if ($feedbackResult && $feedbackResult->num_rows > 0): ?>
    <div class="table--container" style="width:1500px; margin-top:-20px;"> 
        <form action="feedback.php" method="POST">
            <input type="hidden" name="userId" value="<?php echo $res_Id; ?>">
            <button type="submit" style="width: 250px; height: 40px; margin-top:20px; margin-bottom: -60px; background-color: transparent; border: none;" value="Feedback">
                <span style="color: rgb(42, 121, 35); font-size: 20px; border: none;">Give us Feedback</span>
            </button>
        </form>
    </div>
<?php endif; ?>

            <table style="margin-top:10px; width:1500px;">
                <thead>
                    <tr>
                    <th>Assistance Type:</th>
                    <th>Received Assistance:</th>
                        <th>Received Date:</th>
                        <th>Received Time:</th>
                        
                    </tr>
                </thead>
                <tbody>
                <?php if ($transactionResult): ?>
            <?php while ($row = $transactionResult->fetch_assoc()): ?>
                <?php $time = date("h:i A", strtotime($row["ReceivedTime"])); ?>
              
                <tr>
                <td><?= htmlspecialchars($row['AssistanceType']); ?></td>
                <td>  <?= htmlspecialchars($row['ReceivedAssistance']); ?>
                  <?php if (!is_null($row['Amount'])): ?>
               - ₱<?= htmlspecialchars($row['Amount']); ?>
               <?php endif; ?></td>
                  <td> <?php 
               $receivedDate = new DateTime($row['ReceivedDate']);
               echo $receivedDate->format('m/d/Y'); ?>
               </td>
                   <td><?= htmlspecialchars($time); ?></td>
                  
                    
                </tr>
             
                <?php endwhile; ?>
    <?php else: ?>
        <tr>
            <td colspan="5">No transaction history found</td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>
    
        
<div>
<script type="text/javascript">
    let subMenu= document.getElementById("subMenu");
        function toggleMenu(){
            subMenu.classList.toggle("open-menu");
   }
</script>
</body>
</html>
