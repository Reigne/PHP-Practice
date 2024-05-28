<html>

<head>
    <style>
        .error {
            color: #FF0000;
        }

        .login {
            color: blue;
        }
    </style>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>
    <?php
    session_start();
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    if (empty($_SESSION['user']["email"])) {
        header("Location: login.php");
        exit(); // Make sure to exit after redirection
    }



    $email = $_SESSION['user']["email"];


    $first_nameErr = $last_nameErr = $genderErr = "";

    function test_input($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    $email_to_update = $_GET['email'] ?? $_POST['update_user'] ?? '';

    // if (isset($_POST['update_user'])) {
    if (isset($_GET['email']) or isset($_POST['update_user'])) {
        // $email_to_update = $_POST['update_user'];
    
        // echo $email_to_update;

        require_once "db_conn.php";

        $sqlUserInfo = "SELECT * FROM users WHERE email = ?";
        $stmtUserInfo = $conn->prepare($sqlUserInfo);
        $stmtUserInfo->bind_param("s", $email_to_update);
        $stmtUserInfo->execute();
        $userResult = $stmtUserInfo->get_result();
        $user = $userResult->fetch_assoc();

        if (!$user) {
            echo "User not found!";
            exit();
        }
    } else {
        echo "No user email provided for update.";
        exit();
    }

    if (isset($_POST["update"])) {

        if (empty($_POST["first_name"])) {
            $first_nameErr = "First name is required";
        } else {
            $first_name = test_input($_POST["first_name"]);
        }

        if (empty($_POST["last_name"])) {
            $last_nameErr = "Last name is required";
        } else {
            $last_name = test_input($_POST["last_name"]);
        }

        if (empty($_POST["gender"])) {
            $genderErr = "Gender is required";
        } else {
            $gender = test_input($_POST["gender"]);
        }

        $file_name = $temp_name = $folder = '';
        if (isset($_FILES['image']) && !empty($_FILES['image']['name'])) {
            $file_name = $_FILES['image']['name'];
            $temp_name = $_FILES['image']['tmp_name'];
            $folder = 'images/' . $file_name;
        } else {
            $file_name = $user['image'];
        }

        if (empty($first_nameErr) && empty($last_nameErr) && empty($genderErr)) {
            require_once "db_conn.php";



            $sql = "UPDATE users SET first_name = ?, last_name = ?, gender = ?, image = ? WHERE email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssss", $first_name, $last_name, $gender, $file_name, $email_to_update);

            if ($stmt->execute()) {
                if (!empty($file_name)) {
                    move_uploaded_file($temp_name, $folder);
                }
                // Update session data
                // $_SESSION['user']['first_name'] = $first_name;
                // $_SESSION['user']['last_name'] = $last_name;
                // $_SESSION['user']['gender'] = $gender;
                // $_SESSION['user']['image'] = $file_name;
    
                header("Location: profile.php");
                exit();
            } else {
                echo "Error updating record: " . $stmt->error;
            }
        }
    }





    ?>


    <div class="h-screen flex flex-col bg-slate-100">
        <div class="flex flex-1 justify-center items-center">
            <div class="shadow p-10 bg-white rounded-xl space-y-10 w-[35rem]">
                <div>
                    <h2 class="text-3xl font-bold text-center text-sky-500">Update Profile</h2>
                </div>

                <form action="profileUpdate.php" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="update_user" value="<?php echo htmlspecialchars($email_to_update); ?>">

                    <div class="space-y-2">
                        <div class="flex flex-col">
                            <p>First Name <span class="error">*</span></p>
                            <input type="text" name="first_name" class="bg-zinc-100 rounded-xl p-2"
                                value="<?php echo htmlspecialchars($user['first_name']); ?>">
                            <span class="error"><?php echo $first_nameErr; ?></span>
                        </div>

                        <div class="flex flex-col">
                            <p>Last Name <span class="error">*</span></p>
                            <input type="text" name="last_name" class="bg-zinc-100 rounded-xl p-2"
                                value="<?php echo htmlspecialchars($user['last_name']); ?>">
                            <span class="error"><?php echo $last_nameErr; ?></span>
                        </div>

                        <div>
                            <p>Gender <span class="error">*</span></p>

                            <div class="flex flex-row gap-2">
                                <input type="radio" name="gender" value="female" <?php echo ($user['gender'] == 'female') ? 'checked' : ''; ?>>Female
                                <input type="radio" name="gender" value="male" <?php echo ($user['gender'] == 'male') ? 'checked' : ''; ?>>Male
                                <input type="radio" name="gender" value="other" <?php echo ($user['gender'] == 'other') ? 'checked' : ''; ?>>Other
                            </div>
                            <span class="error"><?php echo $genderErr; ?></span>
                        </div>

                        <div>
                            <p>Image:</p>
                            <?php if (!empty($user['image'])): ?>
                                <img src="images/<?php echo htmlspecialchars($user['image']); ?>" alt="Profile Image"
                                    class="w-32 h-32 rounded-full">
                            <?php else: ?>
                                <p>No image uploaded.</p>
                            <?php endif; ?>
                            <input type="file" name="image" id="image">
                        </div>

                        <div class="space-y">
                            <div class="pt-3">
                                <Button type="submit" value="Update" name="update"
                                    class="bg-blue-500 p-3 w-full rounded-xl text-white">Update</Button>
                            </div>

                            <div class="pt-2">
                                <Button type="submit" value="Cancel" name="cancel"
                                    class="bg-zinc-400 p-3 w-full rounded-xl text-white">Cancel</Button>
                            </div>
                        </div>
                    </div>
                </form>


            </div>
        </div>
    </div>

</body>

</html>