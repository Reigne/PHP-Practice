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
    <div class="h-screen flex flex-col bg-slate-100">
        <div class="flex flex-1 justify-center items-center">
            <div class="shadow p-10 bg-white rounded-xl space-y-10 w-[35rem]">
                <div class="space-y-2">
                    <h2 class="text-3xl font-bold text-center text-sky-500">Sign Up</h2>

                    <?php
                    session_start();

                    if (isset($_SESSION['user']["email"]) && !empty($_SESSION['user']["email"])) {
                        header("Location: profile.php");
                        exit; // Make sure to exit after redirection
                    }

                    $first_name = $last_name = $gender = $email = $pass = $confirm_password = "";

                    $first_nameErr = $last_nameErr = $genderErr = $emailErr = $passwordErr = $confirm_passwordErr = "";

                    function test_input($data)
                    {
                        $data = trim($data);
                        $data = stripslashes($data);
                        $data = htmlspecialchars($data);
                        return $data;
                    }

                    if (isset($_POST["submit"])) {
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

                        if (empty($_POST["email"])) {
                            $emailErr = "Email is required";
                        } else {
                            $email = test_input($_POST["email"]);
                        }
                        if (empty($_POST["password"])) {
                            $passwordErr = "Password is required";
                        } else {
                            $pass = test_input($_POST["password"]);
                        }

                        if (empty($_POST["confirm_password"])) {
                            $confirm_passwordErr = "Confirm password is required";
                        } else {
                            $confirm_password = test_input($_POST["confirm_password"]);
                            if ($pass != $confirm_password) {
                                $confirm_passwordErr = "Password did not match";
                            }
                        }

                        if ($pass != $confirm_password) {
                            $confirm_passwordErr = "Password did not match";
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
                        }


                        if (empty($first_nameErr) && empty($last_nameErr) && empty($genderErr) && empty($confirm_passwordErr) && empty($passwordErr) && empty($emailErr)) {

                            require_once "db_conn.php";


                            $sql = "SELECT * FROM users WHERE email = '$email'";
                            $result = mysqli_query($conn, $sql);
                            $rowCount = mysqli_num_rows($result);

                            if ($rowCount > 0) {
                                $emailErr = "Email already exists!";
                            } else {
                                $sql = "INSERT INTO users (first_name, last_name, gender, email, password, image) VALUES (?, ?, ?, ?, ?, ?)";
                                $stmt = mysqli_stmt_init($conn);
                                $prepareStmt = mysqli_stmt_prepare($stmt, $sql);

                                if ($prepareStmt) {
                                    // mysqli_stmt_bind_param($stmt, "sssss", $first_name, $last_name, $gender, $email, $passwordHash);
                                    $passwordHash = md5($pass);

                                    mysqli_stmt_bind_param($stmt, "ssssss", $first_name, $last_name, $gender, $email, $passwordHash, $file_name);

                                    mysqli_stmt_execute($stmt);

                                    move_uploaded_file($temp_name, $folder);

                                    echo "<div class='p-2 rounded-xl bg-green-500'><p class='text-white'>Successfully Registered</p></div>";
                                }
                            }

                        }
                    }

                    ?>
                </div>

                <form action="signup.php" method="post" enctype="multipart/form-data">
                    <div class="space-y-2">
                        <div class="flex flex-col">
                            <p>First Name <span class="error">*</span></p>
                            <input type="text" name="first_name" class="bg-zinc-100 rounded-xl p-2">
                            <span class="error"><?php echo $first_nameErr; ?></span>
                        </div>

                        <div class="flex flex-col">
                            <p>Last Name <span class="error">*</span></p>
                            <input type="text" name="last_name" class="bg-zinc-100 rounded-xl p-2">
                            <span class="error"><?php echo $last_nameErr; ?></span>
                        </div>

                        <div>
                            <p>Gender <span class="error">*</span></p>

                            <div class="flex flex-row gap-2">
                                <input type="radio" name="gender" value="female">Female
                                <input type="radio" name="gender" value="male">Male
                                <input type="radio" name="gender" value="other">Other
                            </div>
                            <span class="error"><?php echo $genderErr; ?></span>
                        </div>
                        <!-- 
                        <p>Gender:
                            <input type="radio" name="gender" value="female">Female
                            <input type="radio" name="gender" value="male">Male
                            <input type="radio" name="gender" value="other">Other
                            <span class="error">* <?php echo $genderErr; ?></span>
                        </p> -->

                        <div class="flex flex-col">
                            <p>Email <span class="error">*</span></p>
                            <input type="text" name="email" class="bg-zinc-100 rounded-xl p-2">
                            <span class="error"><?php echo $emailErr; ?></span>
                        </div>

                        <div class="flex flex-col">
                            <p>Password <span class="error">*</span></p>
                            <input type="password" name="password" class="bg-zinc-100 rounded-xl p-2">
                            <span class="error"><?php echo $passwordErr; ?></span>
                        </div>

                        <div class="flex flex-col">
                            <p>Confirm Password <span class="error">*</span></p>
                            <input type="password" name="confirm_password" class="bg-zinc-100 rounded-xl p-2">
                            <span class="error"><?php echo $confirm_passwordErr; ?></span>
                        </div>

                        <div class="flex flex-col">
                            <p>Upload Image <span class="error">*</span></p>
                            <input type="file" name="image" class="bg-zinc-100 rounded-xl p-2">
                        </div>


                        <div class="pt-3">
                            <Button type="submit" value="Sign Up" name="submit"
                                class="bg-sky-500 p-3 w-full rounded-xl text-white">Sign Up</Button>
                        </div>

                        <p>Already have an account?
                            <a class="login" href="login.php">Login</a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>