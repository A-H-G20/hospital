<?php
    session_start();
    include('../connection.php'); // Database connection

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require '../PHPMailer/src/Exception.php';
    require '../PHPMailer/src/PHPMailer.php';
    require '../PHPMailer/src/SMTP.php';

    // Check if 'id' is set in the URL and retrieve it
    $patient_id = isset($_GET['id']) ? intval($_GET['id']) : null;

    // Initialize an empty array to store patient data
    $patient = [];

    // Fetch patient details if a valid ID is provided
    if ($patient_id) {
        $query = "SELECT pid, pemail, pname, ppassword, paddress, pnic, pdob, ptel FROM patient WHERE pid = ?";
        $stmt = $database->prepare($query);
        $stmt->bind_param("i", $patient_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $patient = $result->fetch_assoc();
        } else {
            echo "No patient found with the provided ID.";
            exit;
        }
    } else {
        echo "Invalid or missing patient ID.";
        exit;
    }

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_FILES['certificate']) && $_FILES['certificate']['error'] == 0) {
            $upload_dir = '../uploads/certificates/';
            
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $certificate_path = $upload_dir . basename($_FILES['certificate']['name']);
            move_uploaded_file($_FILES['certificate']['tmp_name'], $certificate_path);

            // Check if the patient already has a doctor request entry
            $check_query = "SELECT pid FROM doctor_req WHERE pid = ?";
            $check_stmt = $database->prepare($check_query);
            $check_stmt->bind_param("i", $patient['pid']);
            $check_stmt->execute();
            $check_stmt->store_result();

            if ($check_stmt->num_rows > 0) {
                echo "You have already submitted a request to be a doctor.";
            } else {
                $insert_query = "INSERT INTO doctor_req (pid, pemail, pname, ppassword, paddress, pnic, pdob, ptel, certificate)
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $database->prepare($insert_query);
                $stmt->bind_param("issssssss", 
                    $patient['pid'], 
                    $patient['pemail'], 
                    $patient['pname'], 
                    $patient['ppassword'], 
                    $patient['paddress'], 
                    $patient['pnic'], 
                    $patient['pdob'], 
                    $patient['ptel'], 
                    $certificate_path
                );
                $stmt->execute();

                if ($stmt->affected_rows > 0) {
                    // Email notification
                    $mail = new PHPMailer(true);
                    try {
                        $mail->isSMTP();
                        $mail->Host = 'smtp.gmail.com';
                        $mail->SMTPAuth = true;
                        $mail->Username = '22131383@students.liu.edu.lb'; // Your Gmail address
                        $mail->Password = 'wgwqkhbvjwmclphf'; // Your Gmail password or App Password
                        $mail->Port = 587;
                        $mail->SMTPSecure = 'tls';

                        $mail->setFrom('your_email@gmail.com', 'Administrator');
                        $mail->addAddress($patient['pemail']);

                        $mail->isHTML(true);
                        $mail->Subject = 'Doctor Request Submission Confirmation';
                        $mail->Body = "Dear {$patient['pname']},<br>Your request to become a doctor has been successfully submitted. Our team will review your request and notify you of the outcome.";

                        $mail->send();
                        echo "Request submitted successfully. A confirmation email has been sent.";
                        
                        // Redirect to patient settings page after submission
                        header('Location: settings.php');
                        exit; // Make sure to exit after the redirect
                    } catch (Exception $e) {
                        echo "Request submitted, but email notification failed: {$mail->ErrorInfo}";
                    }
                } else {
                    echo "Failed to submit request.";
                }
            }
        } else {
            echo "Please upload a valid certificate image.";
        }
    }
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Doctor Request</title>
    </head>
    <body>
        <div class="modal">
            <h2>View Details.</h2>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($patient['pname'] ?? 'N/A'); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($patient['pemail'] ?? 'N/A'); ?></p>
            <p><strong>NIC:</strong> <?php echo htmlspecialchars($patient['pnic'] ?? 'N/A'); ?></p>
            <p><strong>Telephone:</strong> <?php echo htmlspecialchars($patient['ptel'] ?? 'N/A'); ?></p>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($patient['paddress'] ?? 'N/A'); ?></p>
            <p><strong>Date of Birth:</strong> <?php echo htmlspecialchars($patient['pdob'] ?? 'N/A'); ?></p>
            <form method="post" action="" enctype="multipart/form-data">
                <input type="hidden" name="patient_id" value="<?php echo $patient['pid']; ?>">
                <label for="certificate">Upload Certificate:</label>
                <input type="file" name="certificate" id="certificate" accept="image/*" required>
                <button type="submit" name="submit_request">Submit Request</button>
            </form>
        </div>
    </body>
    </html>