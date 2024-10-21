<?php
session_start();
include("php/config.php");
require_once('vendor/tecnickcom/tcpdf/tcpdf.php');

// Redirect to login if not authenticated
if (!isset($_SESSION['Emp_ID'])) {
    header("Location: login.php");
    exit;
}

// Set the timezone to Manila
date_default_timezone_set('Asia/Manila');

// Handle form submission for PDF generation
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $reportType = ucfirst($_POST['reportType'] ?? 'N/A'); // Capitalize the first letter
    $data = json_decode($_POST['data'], true) ?? [];
    $total = $_POST['total'] ?? 'N/A';

    if (empty($data)) {
        die("No data available for the report.");
    }

    // Create a new PDF document
    $pdf = new TCPDF();
    $pdf->AddPage();
    $pdf->SetFont('helvetica', '', 12);

    // Add generation date and time
    $currentDateTime = date('F j, Y, g:i A'); // Format the date and time
    $pdf->Cell(0, 10, "Date and Time Generated: " . $currentDateTime, 0, 1);
    $pdf->Ln(5); // Add a line break

    // Add report content
    $pdf->Cell(0, 10, "Report Type: " . htmlspecialchars($reportType), 0, 1);
    $pdf->Cell(0, 10, "Total of Beneficiaries: " . htmlspecialchars($total), 0, 1);
    $pdf->Ln(10);

    // Table header
    $pdf->Cell(90, 10, 'Assistance Type', 1, 0, 'C');
    $pdf->Cell(90, 10, 'Number of Beneficiaries', 1, 1, 'C');

    // Table rows
    foreach ($data as $type => $count) {
        $pdf->Cell(90, 10, htmlspecialchars($type), 1, 0, 'C');
        $pdf->Cell(90, 10, htmlspecialchars($count), 1, 1, 'C');
    }

    // Add space before footer
    $pdf->Ln(20);

    // Footer with the system-generated note
    $pdf->SetFont('helvetica', 'I', 10); // Italic font for the footer
    $pdf->Cell(0, 10, "This is a system-generated report.", 0, 1, 'C');

    // Close and output PDF document
    $pdf->Output('report.pdf', 'I'); // 'I' for inline display
    exit; // Prevent any further output
} else {
    die("Invalid request method.");
}
?>
