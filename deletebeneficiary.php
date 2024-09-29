<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $beneficiaryIdToDelete = $_POST['Beneficiary_Id'];

    // Load the XML file
    $xml = simplexml_load_file('beneficiary_records.xml') or die('Error: Cannot load XML file.');

    // Find the beneficiary with the specified ID and remove them
    $found = false;  // Track whether a match is found
    foreach ($xml->beneficiary as $beneficiary) {
        if ((string)$beneficiary->Beneficiary_Id === $beneficiaryIdToDelete) {
            // Remove the beneficiary from its parent (using DOM)
            $dom = dom_import_simplexml($beneficiary);
            $dom->parentNode->removeChild($dom);
            $found = true;
            break;
        }
    }

    if ($found) {
        // Save the updated XML file
        $xml->asXML('beneficiary_records.xml');
     
        echo '<body>
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
        <script>
        swal("Beneficiary has been deleted.","","success")
        .then((value) => {
            if (value) {
                window.location.href = "beneficiaryrecords.php";
            }
        });
        </script>
        </body>';
    } else {
        
        echo '<body>
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
        <script>
        swal("Beneficiary not found.","","error")
        .then((value) => {
            if (value) {
                window.location.href = "beneficiaryrecords.php";
            }
        });
        </script>
        </body>';
    }
}
?>