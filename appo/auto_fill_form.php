<?php
// Start the session
session_start();

// Ensure the user is logged in and is of type 'p'
if (isset($_SESSION["user"])) {
    if ($_SESSION["user"] == "" || $_SESSION['usertype'] != 'p') {
        header("location: ../login.php");
        exit();
    } else {
        $useremail = $_SESSION["user"];
    }
} else {
    header("location: ../login.php");
    exit();
}

// Import the database connection
include("../connection.php");

// Fetch user data from the database
$userrow = $database->query("SELECT * FROM patient WHERE pemail='$useremail'");
if ($userrow->num_rows > 0) {
    $userfetch = $userrow->fetch_assoc();
    $username = $userfetch["pname"]; // Patient name
    $userphone = $userfetch["ptel"]; // Patient phone
} else {
    echo "User not found in the database.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auto-Fill Patient Form</title>
</head>
<body>
    <div class="container">
        <h1>Patient Profile</h1>
        <div class="profile">
            <table border="0">
                <tr>
                    <td width="30%" style="padding-right:20px;">
                        <img src="../img/user.png" alt="Profile Picture" width="100%" style="border-radius:50%">
                    </td>
                    <td>
                        <p class="profile-title"><?php echo htmlspecialchars(substr($username, 0, 13)); ?>..</p>
                        <p class="profile-subtitle"><?php echo htmlspecialchars(substr($useremail, 0, 22)); ?></p>
                    </td>
                </tr>
            </table>
        </div>

        <h2>Appointment Form</h2>
        <form action="submit_appointment.php" method="POST">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($username); ?>" readonly>
            <br><br>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($useremail); ?>" readonly>
            <br><br>

            <label for="phone">Phone:</label>
            <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($userphone); ?>" readonly>
            <br><br>

            <input type="submit" value="Submit">
        </form>
    </div>
</body>
</html>
