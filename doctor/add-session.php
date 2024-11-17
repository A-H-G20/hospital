<?php
session_start();

if (isset($_SESSION["user"])) {
    if (($_SESSION["user"]) == "" || $_SESSION['usertype'] != 'a') {
        header("location: ../login.php");
    }
} else {
    header("location: ../login.php");
}

if ($_POST) {
    // Import database
    include("../connection.php");

    // Check and initialize variables
    $title = $_POST["title"] ?? '';
    $docid = $_POST["docid"] ?? '';
    $nop = $_POST["nop"] ?? 0;
    $date = $_POST["date"] ?? '';
    $time = $_POST["time"] ?? '';

    // Ensure values are valid before running SQL
    if ($docid && $title && $date && $time && $nop) {
        // Use prepared statements to avoid SQL injection
        $stmt = $database->prepare("INSERT INTO schedule (docid, title, scheduledate, scheduletime, nop) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("isssi", $docid, $title, $date, $time, $nop);

        if ($stmt->execute()) {
            header("location: schedule.php?action=session-added&title=$title");
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Please ensure all fields are filled out correctly.";
    }
}
?>
