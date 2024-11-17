<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/animations.css">  
    <link rel="stylesheet" href="../css/main.css">  
    <link rel="stylesheet" href="../css/admin.css">
        
    <title>Patients</title>
    <style>
        .popup {
            animation: transitionIn-Y-bottom 0.5s;
        }
        .sub-table {
            animation: transitionIn-Y-bottom 0.5s;
        }
    </style>
</head>
<body>
    <?php
    session_start();
    require '../PHPMailer/src/Exception.php';
    require '../PHPMailer/src/PHPMailer.php';
    require '../PHPMailer/src/SMTP.php';

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    if (isset($_SESSION["user"]) && $_SESSION["user"] != "" && $_SESSION['usertype'] == 'a') {
        // User is logged in and is an admin
    } else {
        header("location: ../login.php");
        exit;
    }

    include("../connection.php");

    if (isset($_POST['action']) && isset($_POST['id'])) {
        $id = $_POST['id'];
        $action = $_POST['action'];
        
        // Fetch patient email and name from doctor_req table
        $reqQuery = "SELECT * FROM doctor_req WHERE pid='$id'";
        $reqResult = $database->query($reqQuery);
        $row = $reqResult->fetch_assoc();

        if ($row) {
            $docEmail = $row['pemail'];
            $docName = $row['pname'];
        }

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = '22131383@students.liu.edu.lb';
            $mail->Password = 'wgwqkhbvjwmclphf';
            $mail->Port = 587;

            $mail->setFrom('your_email@gmail.com', 'Administrator');
            $mail->addAddress($docEmail);
            $mail->isHTML(true);

            if ($action == 'accept') {
                $docPassword = $row['ppassword'];
                $docNic = $row['pnic'];
                $docTel = $row['ptel'];
                $specialties = $row['specialties'];

                // Insert data into doctor table
                $insertDoctor = "INSERT INTO doctor (docemail, docname, docpassword, docnic, doctel, specialties) 
                                 VALUES ('$docEmail', '$docName', '$docPassword', '$docNic', '$docTel', '$specialties')";
                $database->query($insertDoctor);

                // Update usertype in webuser table
                $updateUserType = "UPDATE webuser SET usertype='d' WHERE email='$docEmail'";
                $database->query($updateUserType);

                // Remove the record from doctor_req and patient tables
                $deleteReq = "DELETE FROM doctor_req WHERE pid='$id'";
                $database->query($deleteReq);
                $deletePatient = "DELETE FROM patient WHERE pid='$id'";
                $database->query($deletePatient);

                // Prepare the acceptance email
                $mail->Subject = 'Doctor Application Accepted';
                $mail->Body = "Dear $docName, <br>Your application to become a doctor has been accepted. Welcome aboard!";
            } elseif ($action == 'decline') {
                $deleteReq = "DELETE FROM doctor_req WHERE pid='$id'";
                $database->query($deleteReq);

                // Prepare the decline email
                $mail->Subject = 'Doctor Application Declined';
                $mail->Body = "Dear $docName, <br>We regret to inform you that your application to become a doctor has been declined.";
            }

            // Send the email
            $mail->send();
        } catch (Exception $e) {
            echo "Error: {$mail->ErrorInfo}";
        }

        header("Location: doctor_req.php");
        exit;
    }

    $sqlmain = "SELECT * FROM doctor_req ORDER BY pid DESC";
    if (isset($_POST["search"])) {
        $keyword = $_POST["search"];
        $sqlmain = "SELECT * FROM doctor_req WHERE pemail LIKE '%$keyword%' OR pname LIKE '%$keyword%'";
    }
    $result = $database->query($sqlmain);
    ?>


    <div class="container">
        <div class="menu">
            <table class="menu-container" border="0">
                <tr>
                    <td style="padding:10px" colspan="2">
                        <table border="0" class="profile-container">
                            <tr>
                                <td width="30%" style="padding-left:20px">
                                    <img src="../img/user.png" alt="" width="100%" style="border-radius:50%">
                                </td>
                                <td style="padding:0px;margin:0px;">
                                    <p class="profile-title">Administrator</p>
                                    <p class="profile-subtitle">admin@edoc.com</p>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <a href="../logout.php"><input type="button" value="Log out" class="logout-btn btn-primary-soft btn"></a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-dashbord">
                        <a href="index.php" class="non-style-link-menu"><div><p class="menu-text">Dashboard</p></div></a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-doctor">
                        <a href="doctors.php" class="non-style-link-menu"><div><p class="menu-text">Doctors</p></div></a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-schedule">
                        <a href="schedule.php" class="non-style-link-menu"><div><p class="menu-text">Schedule</p></div></a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-appoinment">
                        <a href="appointment.php" class="non-style-link-menu"><div><p class="menu-text">Appointment</p></div></a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-patient menu-active menu-icon-patient-active">
                        <a href="doctor_req.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">Patients</p></div></a>
                    </td>
                </tr>
            </table>
        </div>
        <div class="dash-body">
            <table border="0" width="100%" style="border-spacing: 0;margin:0;padding:0;margin-top:25px;">
                <tr>
                    <td width="13%">
                        <a href="doctor_req.php"><button class="login-btn btn-primary-soft btn btn-icon-back" style="padding:11px;margin-left:20px;width:125px">Back</button></a>
                    </td>
                    <td>
                    <form action="" method="post" class="header-search">
                <input type="search" name="search" class="input-text header-searchbar" placeholder="Search Patient name or Email" list="patient">&nbsp;&nbsp;
                <input type="submit" value="Search" class="login-btn btn-primary btn" style="padding:10px 25px;">
            </form>
                    </td>
                    <td width="15%">
                        <p style="font-size: 14px;color: rgb(119, 119, 119);padding: 0;margin: 0;text-align: right;">
                            Today's Date
                        </p>
                        <p class="heading-sub12" style="padding: 0;margin: 0;">
                            <?php 
                            date_default_timezone_set('Asia/Kolkata');
                            echo date('Y-m-d');
                            ?>
                        </p>
                    </td>
                    <td width="10%">
                        <button class="btn-label" style="display: flex;justify-content: center;align-items: center;"><img src="../img/calendar.svg" width="100%"></button>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" style="padding-top:10px;">
                        <p class="heading-main12" style="margin-left: 45px;font-size:18px;color:rgb(49, 49, 49)">All Patients (<?php echo $result->num_rows; ?>)</p>
                    </td>
                </tr>
                <tr>
                   <td colspan="4">
                       <center>
                        <div class="abc scroll">
                        <table width="93%" class="sub-table scrolldown">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Events</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['pname']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['pemail']) . "</td>";
                            echo "<td>";
                            echo "<button onclick=\"showPopup('accept', " . $row['pid'] . ")\">Accept</button>";
                            echo "<button onclick=\"showPopup('decline', " . $row['pid'] . ")\">Decline</button>";
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3'>No doctor requests found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <script>
        function showPopup(action, id) {
            if (confirm(`Are you sure you want to ${action} this request?`)) {
                const form = document.createElement("form");
                form.method = "POST";
                form.action = ""; 
                form.innerHTML = `<input type="hidden" name="action" value="${action}">
                                  <input type="hidden" name="id" value="${id}">`;
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html>
