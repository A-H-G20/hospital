<?php
/*if ($_SERVER["REQUEST_METHOD"] === "POST") {
    include 'connection.php';

    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $disease_type = $_POST['disease_type'];
    $doctor = $_POST['doctor'];
    $date = $_POST['date'];
    $time = $_POST['time'];

   

    // Prepare the SQL statement
    $stmt = $conn->prepare("INSERT INTO appointments (name, email, phone, disease_type, doctor, date, time) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $name, $email, $phone, $disease_type, $doctor, $date, $time);
    
    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }

  
    $stmt->close();
  
    



    $conn->close();
}
?>

<?php
// Database connection (assuming config.php contains your connection setup)
require 'connection.php';

// Fetch docname from the database
$query = "SELECT docname FROM doctor"; // replace your_table_name with the actual table name
$result = $mysqli->query($query);

// Check for errors
if (!$result) {
    die("Query failed: " . $mysqli->error);
}
?>*/



if ($_SERVER["REQUEST_METHOD"] === "POST") {
    include 'connection.php';

    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $disease_type = $_POST['disease_type'];
    $doctor = $_POST['doctor'];
    $date = $_POST['date'];
    $time = $_POST['time'];

    // Prepare the SQL statement
    $stmt = $conn->prepare("INSERT INTO appointments (name, email, phone, disease_type, doctor, date, time) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $name, $email, $phone, $disease_type, $doctor, $date, $time);
    
    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }

    $stmt->close();
    $conn->close();
}
?>


