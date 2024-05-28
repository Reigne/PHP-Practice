<?php
session_start();

// Redirect to login if user is not logged in
if (empty($_SESSION['user']["email"])) {
    header("Location: login.php");
    exit();
}

// Handle logout
if (isset($_POST["logout"])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

require_once "db_conn.php";
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Handle delete user
if (isset($_POST['delete_user'])) {
    $email_to_delete = $_POST['delete_user'];
    $sql_delete = "DELETE FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql_delete);
    $stmt->bind_param("s", $email_to_delete);
    $stmt->execute();
    $_SESSION['delete_success'] = true; // Set session variable for successful deletion
    header("Location: profile.php"); // Redirect to profile page to prevent form resubmission
    exit();
}

// Fetch user info
$email = $_SESSION['user']['email'];
$sqlUserInfo = "SELECT * FROM users WHERE email = ?";
$stmtUserInfo = $conn->prepare($sqlUserInfo);
$stmtUserInfo->bind_param("s", $email);
$stmtUserInfo->execute();
$userResult = $stmtUserInfo->get_result();
$user = $userResult->fetch_assoc();

if (isset($_POST["update_user"])) {
    $to = $_POST["update_user"];
    $subject = "Update Alert";
    $message = "
        <p>Did you want to update your profile?</p>
        <p><a href='http://localhost/CRUD/profileUpdate.php?email=$to' style='padding: 10px 15px; background-color: #007BFF; color: white; text-decoration: none; border-radius: 5px;'>Update Profile</a></p>
    ";

    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'reignelegend18@gmail.com'; // Your email
        $mail->Password = 'rbytxrlkmvitqxgj'; // Your email password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('your-email@gmail.com', 'System Alert');
        $mail->addAddress($to);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $message;

        $mail->send();
        // echo 'Email Sent';
        
        header('Location: email_sent.php'); 
        exit();
    } catch (Exception $e) {
        echo "Email Failed. Mailer Error: {$mail->ErrorInfo}";
    }
}

// if (isset($_POST["update_user"])) {
//     $to = $user["email"];
//     $subject = "Update Alert";
//     $message = "Did you want to update your profile?";
//     $headers = "From: no-reply@yourdomain.com" . "\r\n" .
//                "Reply-To: no-reply@yourdomain.com" . "\r\n" .
//                "X-Mailer: PHP/" . phpversion();

//     if (mail($to, $subject, $message, $headers)) {
//         echo "Email Sent";
//     } else {
//         echo "Email Failed";
//     }
// }

// Fetch all users for the table
$sql = "SELECT * FROM users";
$result = $conn->query($sql);

if ($result) {
    $tableUser = $result->fetch_all(MYSQLI_ASSOC);
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>


<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>User Profile</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/v/dt/dt-1.10.18/datatables.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-100">
    <div class="container mx-auto py-5 space-y-4">
        <div class="flex flex-row gap-4">
            <div class="p-8 rounded-xl shadow bg-white w-[24rem] h-fit">
                <div class="space-y-6 flex flex-col items-center justify-center">
                    <div>
                        <?php if (!empty($user["image"])): ?>
                            <img src="images/<?php echo $user["image"]; ?>" alt="Profile Image" class="w-32 h-32 rounded-full">
                        <?php else: ?>
                            <p>No image uploaded.</p>
                        <?php endif; ?>
                    </div>

                    <div class="flex flex-col items-center justify-center">
                        <p class="text-xl font-semibold"><?php echo $user["email"]; ?></p>
                        <p><?php echo $user["first_name"] . " " . $user["last_name"] . " (" . $user["gender"] . ")"; ?></p>
                    </div>

                    <div class="w-full space-y-2">
                        <form action="profile.php" method="post">
                            <input type="hidden" name="update_user" value="<?php echo $user['email']; ?>">
                            <button type="submit" class="py-2 px-4 rounded-xl bg-sky-500 text-white w-full">Update Profile</button>
                        </form>

                        <form action="" method="post">
                            <button type="submit" name="logout" value="Logout" class="py-2 px-4 rounded-xl border border-red-500 text-red-500 w-full">Logout</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="p-8 shadow-xl rounded-xl bg-white w-full space-y-3">
                <div>
                    <h4 class="text-3xl font-bold">Users Table</h4>
                </div>

                <table id="myTable" class="display">
                    <thead>
                        <tr>
                            <th>First name</th>
                            <th>Last name</th>
                            <th>Gender</th>
                            <th>Email</th>
                            <th>Image</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tableUser as $tableUserRow): ?>
                            <tr>
                                <td><?php echo $tableUserRow['first_name']; ?></td>
                                <td><?php echo $tableUserRow['last_name']; ?></td>
                                <td><?php echo $tableUserRow['gender']; ?></td>
                                <td><?php echo $tableUserRow['email']; ?></td>
                                <td>
                                    <?php if (!empty($tableUserRow["image"])): ?>
                                        <img src="images/<?php echo $tableUserRow["image"]; ?>" alt="Profile Image" class="w-12 h-12 rounded-xl">
                                    <?php else: ?>
                                        <p>No image uploaded.</p>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <form action='profileUpdate.php' method='post' style='display:inline;'>
                                        <input type='hidden' name='update_user' value='<?php echo $tableUserRow['email']; ?>'>
                                        <button type='submit' class='py-1 px-4 bg-blue-500 rounded-lg text-white text-xs'>Update</button>
                                    </form>
                                    <form action='profile.php' method='post' class='delete-form' style='display:inline;'>
                                        <input type='hidden' name='delete_user' value='<?php echo $tableUserRow['email']; ?>'>
                                        <button type='button' class='delete-button py-1 px-4 bg-red-500 rounded-lg text-white text-xs'>Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            $('#myTable').DataTable({
                "paging": true,
                "ordering": true,
                "info": true,
                "searching": true // Enable search
            });

            $('.delete-button').on('click', function (e) {
                e.preventDefault();
                const form = $(this).closest('form');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });

            <?php if (isset($_SESSION['delete_success'])): ?>
                Swal.fire({
                    icon: 'success',
                    title: 'Deleted!',
                    text: 'The user has been deleted successfully.',
                    confirmButtonColor: '#3085d6'
                }).then(() => {
                    <?php unset($_SESSION['delete_success']); ?>
                });
            <?php endif; ?>
        });
    </script>
</body>

</html>
