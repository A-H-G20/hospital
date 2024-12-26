<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/animations.css">
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/signup.css">

    <title>Create Account</title>
    <style>
        .container {
            animation: transitionIn-X 0.5s;
        }

        header {
    position: absolute;
    top: 10px;
    right: 10px; /* Changed from left to right */
    z-index: 1000; /* Ensures it stays on top of other elements */
}

header a {
    display: inline-block;
    text-decoration: none;
}

.logo {
    width: 100px; /* Adjust size as needed */
    height: auto; /* Keeps aspect ratio */
    display: block;
    cursor: pointer;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3); /* Adds shadow */
    border-radius: 8px; /* Optional: Slightly rounds the edges */
    transition: box-shadow 0.3s ease; /* Smooth shadow transition on hover */
}

.logo:hover {
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.5); /* Shadow becomes stronger on hover */
}

    </style>
</head>

<body style="background: url('R.jpg') no-repeat center center fixed; background-size: cover;">

<header>
        <a href="index.php">
            <img src="logo.png" alt="Logo" class="logo">
        </a>
    </header>

<?php
session_start();

// Initialize session variables to avoid undefined index warnings
$_SESSION["user"] = $_SESSION["user"] ?? "";
$_SESSION["usertype"] = $_SESSION["usertype"] ?? "";
$_SESSION["personal"] = $_SESSION["personal"] ?? [];

date_default_timezone_set('Asia/Kolkata');
$date = date('Y-m-d');
$_SESSION["date"] = $date;

// Import database connection
include("connection.php"); // Make sure $conn or $database is defined here

// PHPMailer imports
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

$error = ""; // Initialize error message

if ($_POST) {
    $fname = $_SESSION['personal']['fname'] ?? '';
    $lname = $_SESSION['personal']['lname'] ?? '';
    $name = $fname . " " . $lname;
    $address = $_SESSION['personal']['address'] ?? '';
    $nic = $_SESSION['personal']['nic'] ?? '';
    $dob = $_SESSION['personal']['dob'] ?? '';
    $email = $_POST['newemail'];
    $tele = $_POST['tele'];
    $newpassword = $_POST['newpassword'];
    $cpassword = $_POST['cpassword'];

    // Password validation criteria
    $passwordPattern = "/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)[A-Za-z\d@$!%*?&]{8,}$/";

    if (!preg_match($passwordPattern, $newpassword)) {
        $error = '<label class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Password must be at least 8 characters long, include an uppercase letter, a lowercase letter, and a digit.</label>';
    } elseif ($newpassword !== $cpassword) {
        $error = '<label class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Passwords do not match.</label>';
    } else {
        $mail = new PHPMailer(true);
        try {
            // SMTP configuration
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = '22131383@students.liu.edu.lb'; // Your Gmail address
            $mail->Password = 'wgwqkhbvjwmclphf'; // Your Gmail password or App Password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Check if email already exists
            $result = $database->query("SELECT * FROM webuser WHERE email='$email'");
            if ($result->num_rows == 1) {
                $error = '<label class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Email already registered.</label>';
            } else {
                // Insert data into patient and webuser tables
                $stmt = $database->prepare("INSERT INTO patient (pemail, pname, ppassword, paddress, pnic, pdob, ptel, verification_code) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $verification_code = substr(number_format(time() * rand(), 0, '', ''), 0, 6);
                $stmt->bind_param("ssssssss", $email, $name, $newpassword, $address, $nic, $dob, $tele, $verification_code);

                if ($stmt->execute()) {
                    // Insert into webuser table
                    $stmt2 = $database->prepare("INSERT INTO webuser (email, usertype) VALUES (?, 'p')");
                    $stmt2->bind_param("s", $email);
                    $stmt2->execute();
                    $stmt2->close();

                    // Set up the email content
                    $mail->setFrom('your_email@gmail.com', 'Administrator');
                    $mail->addAddress($email, $name);
                    $mail->isHTML(true);
                    $mail->Subject = 'Email verification';
                    $mail->Body = '<p>Dear <b>' . htmlspecialchars($name) . '</b>,</p>
                                   <p>Your verification code is: <b style="font-size: 15px;">' . htmlspecialchars($verification_code) . '</b></p>
                                   <p>Regards,</p><p>Administrator</p>';

                    // Send email
                    $mail->send();

                    // Redirect to email verification page
                    header("Location: email-verification.php?email=" . urlencode($email));
                    exit();
                } else {
                    $error = "Error: " . $stmt->error;
                }
                $stmt->close();
            }
        } catch (Exception $e) {
            $error = "Mailer Error: {$mail->ErrorInfo}";
        } catch (mysqli_sql_exception $e) {
            $error = "Database Error: {$e->getMessage()}";
        }
    }
}
?>

    <center>
        <div class="container">
            <table border="0" style="width: 69%;">
                <tr>
                    <td colspan="2">
                        <p class="header-text">Let's Get Started</p>
                        <p class="sub-text">It's Okey, Now Create User Account.</p>
                    </td>
                </tr>
                <tr>
                    <form action="" method="POST">
                        <td class="label-td" colspan="2">
                            <label for="newemail" class="form-label">Email: </label>
                        </td>
                </tr>
                <tr>
                    <td class="label-td" colspan="2">
                        <input type="email" name="newemail" class="input-text" placeholder="Email Address" required>
                    </td>
                </tr>
                <tr>
                    <td class="label-td" colspan="2">
                        <label for="tele" class="form-label">Mobile Number: </label>
                    </td>
                </tr>
                <tr>
                    <td class="label-td" colspan="2">
                        <input type="tel" name="tele" class="input-text" placeholder="ex: 0712345678" >
                    </td>
                </tr>
                <tr>
                    <td class="label-td" colspan="2">
                        <label for="newpassword" class="form-label">Create New Password: </label>
                    </td>
                </tr>
                <tr>
                    <td class="label-td" colspan="2">
                        <input type="password" name="newpassword" class="input-text" placeholder="New Password" required>
                    </td>
                </tr>
                <tr>
                    <td class="label-td" colspan="2">
                        <label for="cpassword" class="form-label">Confirm Password: </label>
                    </td>
                </tr>
                <tr>
                    <td class="label-td" colspan="2">
                        <input type="password" name="cpassword" class="input-text" placeholder="Confirm Password" required>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <?php echo $error ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <input type="reset" value="Reset" class="login-btn btn-primary-soft btn">
                    </td>
                    <td>
                        <input type="submit" value="Sign Up" class="login-btn btn-primary btn">
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <br>
                        <label for="" class="sub-text" style="font-weight: 280;">Already have an account&#63; </label>
                        <a href="login.php" class="hover-link1 non-style-link">Login</a>
                        <br><br><br>
                    </td>
                </tr>
                </form>
                </tr>
            </table>
        </div>
    </center>
</body>

</html>