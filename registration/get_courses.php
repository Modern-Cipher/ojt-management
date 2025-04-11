<?php
include '../0config/database.php';

if (isset($_POST['institute'])) {
    $institute = $_POST['institute'];
    $courses = [];

    if ($institute == 'Institute of Engineering and Applied Technology') {
        $courses = ['BS in Agriculture and Biosystems Engineering', 'BS in Geodetic Engineering', 'BS in Food Technology', 'BS in Information Technology'];
    } elseif ($institute == 'Institute of Management') {
        $courses = ['BS in Business Administration', 'BS in Hospitality Management'];
    }

    $output = '<option value="">Select Course..</option>';
    foreach ($courses as $course) {
        $output .= '<option value="' . htmlspecialchars($course, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($course, ENT_QUOTES, 'UTF-8') . '</option>';
    }
    echo $output;
}
?>
