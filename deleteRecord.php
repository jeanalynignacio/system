<?php
include("php/config.php");
if ($_SERVER["REQUEST_METHOD"] == "POST") {
$beneficiaryId = $_POST['Beneficiary_Id'];
$sql2 = "DELETE FROM beneficiary WHERE Beneficiary_Id='$beneficiaryId'";
if ($con->query($sql2) === TRUE) {
?>
<script>
alert("Record deleted successfully");
window.location.href = 'patients-records.php';</script>
<?php
} else {
echo "Error: " . $con->error;
}
$con->close();
exit();

}
?>