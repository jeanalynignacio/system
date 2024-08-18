<?php
// Check if the branch parameter is set
if (isset($_GET['sponsor'])) {
    $sponsor = $_GET['sponsor'];
    
    // Define the path to the PDF file based on the branch
    switch ($sponsor) {
        case 'PGB-Balanga Branch':
            $file = 'MAIP-Bataan.pdf';
            break;
        case 'PGB-Dinalupihan Branch':
            $file = 'MAIP-Velasco.pdf';
            break;
        case 'PGB-Hermosa Branch':
            $file = 'hermosa_branch.pdf';
            break;
        case 'PGB-Mariveles Branch':
            $file = 'mariveles_branch.pdf';
            break;
        default:
            echo "Invalid branch.";
            exit;
    }

    // Check if the file exists
    if (file_exists($file)) {
        // Set headers to trigger download
        header('Content-Description: File Transfer');
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="'.basename($file).'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        readfile($file);
        exit;
    } else {
        echo "The file does not exist.";
    }
} else {
    echo "Branch parameter is missing.";
}
?>
