<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (move_uploaded_file($_FILES['the_file']['tmp_name'], "./uploads/" . $_FILES['the_file']['name'])) {
        echo "<p>Your file has been uploaded.</p>";
    } else {
        echo "<p style='color:red;'>Your file could not be uploaded because: ";
        switch ($_FILES['the_file']['error']) {
            case 4:
                echo "No file was uploaded.";
                break;
            case 6:
                echo "The temporary folder does not exist.";
                break;
            default:
                echo "Something unforeseen happened.";
        }
        echo "</p>";
    }
}
?>
