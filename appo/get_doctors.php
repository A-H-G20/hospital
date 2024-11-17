<?php
include 'connection.php';

if (isset($_GET['specialty_id'])) {
    $specialty_id = $_GET['specialty_id'];

    // Prepare the SQL statement to fetch doctors based on the specialty ID
    $stmt = $conn->prepare("SELECT docid, docname FROM doctor WHERE specialties = ?");
    $stmt->bind_param("i", $specialty_id); // Ensure the specialty ID is bound correctly
    $stmt->execute();
    
    $result = $stmt->get_result();
    $doctors = [];
    
    while ($row = $result->fetch_assoc()) {
        $doctors[] = $row;
    }
    
    echo json_encode($doctors); // Return JSON response
}

$stmt->close();
$conn->close();
?>