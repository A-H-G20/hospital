<?php
/*include 'connection.php';  // Include your DB connection

// Check if email is provided in the request
if (isset($_GET['email'])) {
    $email = $_GET['email'];

    // Sanitize email input (best practice)
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);

    // SQL query to get the patient's name and phone from the patient table
    $stmt = $conn->prepare("SELECT pname, ptel FROM patient WHERE pemail = ?");
    $stmt->bind_param("s", $email); // "s" means it's a string
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the patient data is found
    if ($result->num_rows > 0) {
        // Fetch the data and return it as JSON
        $patient = $result->fetch_assoc();
        echo json_encode($patient);
    } else {
        // Return null or error if no data is found
        echo json_encode(null);
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>
