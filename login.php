<html>

<head>
    <style>
        .error {
            color: red;
        }
    </style>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>


    <div class="h-screen flex flex-col bg-slate-100">
        <div class="flex flex-1 justify-center items-center">
            <div class="shadow p-10 bg-white rounded-xl space-y-5 w-[35rem]">
                <div class="space-y-3">
                    <h2 class="text-4xl font-bold text-center text-sky-500">Login</h2>

                    <?php
                    session_start();


                    // if (empty($_SESSION['user']["email"])) {
                    //     header("Location: login.php");
                    
                    //     echo $_SESSION["email"];
                    // }
                    
                    if (isset($_SESSION['user']["email"]) && !empty($_SESSION['user']["email"])) {
                        header("Location: profile.php");
                        exit; // Make sure to exit after redirection
                    }

                    $email = $upass = "";
                    $emailErr = $passwordErr = "";


                    function test_input($data)
                    {
                        $data = trim($data);
                        $data = stripslashes($data);
                        $data = htmlspecialchars($data);
                        return $data;
                    }


                    if (isset($_POST["submit"])) {

                        if (empty($_POST["email"])) {
                            $emailErr = "Email is required";
                        } else {
                            $email = test_input($_POST["email"]);
                        }

                        if (empty($_POST["password"])) {
                            $passwordErr = "Password is required";
                        } else {
                            $upass = test_input($_POST["password"]);
                        }

                        if (empty($passwordErr) && empty($emailErr)) {
                            require_once "db_conn.php";

                            $passwordHash = md5($upass);

                            $sql = "SELECT * FROM users WHERE email = '$email' AND password = '$passwordHash'";
                            $result = mysqli_query($conn, $sql);
                            $user = mysqli_fetch_array($result, MYSQLI_ASSOC);

                            if ($user) {
                                $_SESSION["user"] = $user;
                                // $_SESSION["first_name"] = $user["first_name"];
                                // $_SESSION["last_name"] = $user["last_name"];
                                // $_SESSION["gender"] = $user["gender"];
                                // $_SESSION["email"] = $user["email"];
                                header("Location: profile.php");
                                die();
                            } else {
                                echo "<div class='p-2 rounded-xl bg-red-500'><p class='text-white'>Email and Password did not match</p></div>";
                            }

                            // if ($user) {
                            //     echo "User found. Password from form: $upass, Password from database: {$user["password"]}";
                    
                            //     if (password_verify($upqweqweass, $user["password"])) {
                            //         // Password verification logic
                            //         echo "<br>wow pumasok";
                            //     } else {
                            //         echo "Password did not match";
                            //     }
                            // } else {
                            //     echo "Email did not match";
                            // }
                    
                        }
                    }
                    ?>
                </div>
                <form action="login.php" method="post">
                    <div class="space-y-2">
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


                        <div class="pt-3">
                            <button type="submit" value="Login" name="submit"
                                class="bg-sky-500 p-3 w-full rounded-xl text-white">
                                Login
                            </button>
                        </div>

                        <p>Don't have account?
                            <a class="login" href="signup.php">Sign Up</a>
                        </p>
                    </div>

                </form>
            </div>
        </div>
    </div>
</body>

</html>