<?php
require_once 'db_connect.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (!empty($fullname) && !empty($email) && !empty($password)) {
        try {
            // 1. Check if email already exists
            $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM Pathologist_Table WHERE email = ?");
            $checkStmt->execute([$email]);
            
            if ($checkStmt->fetchColumn() > 0) {
                $error = "An account with this email already exists.";
            } else {
                // 2. Generate a unique Pathologist ID (Format: MD-YYYY-RANDOM)
                $year = date("Y");
                $randomNum = str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
                $pathologist_id = "MD-" . $year . "-" . $randomNum;

                // 3. Hash the password for security
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // 4. Insert into database
                $insertStmt = $pdo->prepare("INSERT INTO Pathologist_Table (pathologist_id, full_name, email, password) VALUES (?, ?, ?, ?)");
                $insertStmt->execute([$pathologist_id, $fullname, $email, $hashed_password]);

                $success = "Account created successfully! Redirecting to login...";
                header("refresh:2;url=login.php");
            }
        } catch (PDOException $e) {
            $error = "Registration failed: " . $e->getMessage();
        }
    } else {
        $error = "Please fill in all required fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CLA.I.RE - Create Account</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind Theme Configuration -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            pink: '#d6006e',
                            light: '#ffeef2',
                            dark: '#0f0f25',
                            border: '#fbcfe8',
                        }
                    }
                }
            }
        }
    </script>
    
    <style>
        .custom-input-container {
            border: 1px solid #fbcfe8;
            transition: all 0.3s ease;
            background-color: white;
        }
        .custom-input-container:focus-within {
            border-color: #d6006e;
            box-shadow: 0 0 0 4px #ffeef2;
        }
    </style>
</head>

<body class="min-h-screen flex flex-col bg-brand-light">

    <!-- Navbar -->
    <nav class="bg-white py-4 px-6 md:px-12 flex justify-between items-center shadow-sm sticky top-0 z-50">
        <a href="homepage.php" class="flex items-center gap-2 select-none no-underline">
            <div class="bg-brand-pink text-white p-1.5 rounded-md w-8 h-8 flex items-center justify-center shadow-sm">
                <i class="fa-solid fa-microscope text-sm"></i>
            </div>
            <span class="font-bold text-xl tracking-wide text-brand-pink">CLA.I.RE</span>
        </a>
    </nav>

    <!-- Main Section -->
    <main class="flex-grow flex items-center justify-center px-4 py-12">

        <div class="bg-transparent w-full max-w-md">

            <h2 class="text-3xl font-bold text-center text-brand-dark mb-10">
                Create an Account
            </h2>

            <!-- Feedback Messages -->
            <?php if ($error): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6 flex items-center gap-3">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span class="text-sm font-medium"><?php echo $error; ?></span>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6 flex items-center gap-3">
                    <i class="fa-solid fa-circle-check"></i>
                    <span class="text-sm font-medium"><?php echo $success; ?></span>
                </div>
            <?php endif; ?>

            <form action="sign-up.php" method="POST" class="space-y-5">

                <!-- Full Name -->
                <div class="custom-input-container rounded-full flex items-center px-5 py-3">
                    <i class="fa-solid fa-user text-brand-dark w-5 text-center"></i>
                    <input type="text" name="fullname" placeholder="Full Name"
                           class="flex-grow ml-3 bg-transparent outline-none text-brand-dark placeholder-gray-400"
                           required>
                </div>

                <!-- Email -->
                <div class="custom-input-container rounded-full flex items-center px-5 py-3">
                    <i class="fa-solid fa-envelope text-brand-dark w-5 text-center"></i>
                    <input type="email" name="email" placeholder="Email"
                           class="flex-grow ml-3 bg-transparent outline-none text-brand-dark placeholder-gray-400"
                           required>
                </div>

                <!-- Password -->
                <div class="custom-input-container rounded-full flex items-center px-5 py-3 relative">
                    <i class="fa-solid fa-lock text-brand-dark w-5 text-center"></i>
                    <input id="signup-password" type="password" name="password"
                           placeholder="Password"
                           class="flex-grow ml-3 bg-transparent outline-none text-brand-dark placeholder-gray-400"
                           required>

                    <button type="button"
                            onclick="togglePassword('signup-password', 'signup-eye')"
                            class="absolute right-5 text-gray-400 hover:text-gray-600">
                        <i id="signup-eye" class="fa-solid fa-eye-slash"></i>
                    </button>
                </div>

                <!-- Button -->
                <button type="submit"
                        class="w-full bg-brand-pink hover:bg-pink-700 text-white font-bold py-3 rounded-full shadow-md transition text-lg mt-4 active:scale-95">
                    Sign Up
                </button>

            </form>

            <!-- Divider -->
            <div class="relative flex items-center py-8">
                <div class="flex-grow border-t border-brand-pink/30"></div>
                <span class="mx-4 text-brand-dark font-medium text-sm text-gray-400">Or sign up with</span>
                <div class="flex-grow border-t border-brand-pink/30"></div>
            </div>

            <!-- Social Buttons -->
            <div class="flex gap-4 mb-8">
                <button type="button" class="flex-1 bg-white border border-brand-pink py-2.5 px-4 rounded-full shadow-sm flex items-center justify-center gap-2 hover:bg-pink-50 transition text-sm font-medium">
                    <img src="https://www.svgrepo.com/show/475656/google-color.svg" class="w-5 h-5">
                    Google
                </button>

                <button type="button" class="flex-1 bg-brand-pink text-white py-2.5 px-4 rounded-full shadow-sm flex items-center justify-center gap-2 hover:bg-pink-700 transition text-sm font-medium">
                    <i class="fa-brands fa-facebook-f"></i>
                    Facebook
                </button>
            </div>

            <!-- Login -->
            <p class="text-center text-brand-dark">
                Already have an account?
                <a href="login.php" class="font-bold text-brand-pink hover:underline">Login</a>
            </p>

        </div>
    </main>

    <!-- Toggle Password Script -->
    <script>
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);

            if (input.type === "password") {
                input.type = "text";
                icon.classList.replace("fa-eye-slash", "fa-eye");
            } else {
                input.type = "password";
                icon.classList.replace("fa-eye", "fa-eye-slash");
            }
        }
    </script>
     <footer class="pt-4 pb-5 text-center text-gray-400 text-xs">
        <div class="flex justify-center items-center gap-1 flex-wrap text-gray-400 font-medium">
            <a href="privacy.php" class="hover:text-brand-pink transition">Privacy Policy</a>
            <span>&bull;</span>
            <a href="terms.php" class="hover:text-brand-pink transition">Terms of Use</a>
            <span>&bull;</span>
            <a href="legal.php" class="hover:text-brand-pink transition">Legal</a>
            <span>&bull;</span>
            <a href="developers.php" class="hover:text-brand-pink transition">About the Developers</a>
        </div>
        <div class="mt-1 text-gray-400 font-normal">
            &copy; <?php echo date('Y'); ?> CLA.I.RE System &bull; All rights Reserved
        </div>
    </footer>

    </footer>
</body>
</html>
