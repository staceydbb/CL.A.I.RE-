<?php

ob_start();
require_once 'db_connect.php';


if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
    session_write_close(); 
    header("Location: dashboard.php");
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!empty($email) && !empty($password)) {
        try {
            // Find the pathologist by email
            $stmt = $pdo->prepare("SELECT * FROM Pathologist_Table WHERE email = ? LIMIT 1");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user) {
                // Check if password matches (Plain text for 'admin123' or Hashed)
                $is_valid = false;
                if ($password === $user['password']) {
                    $is_valid = true;
                } elseif (password_verify($password, $user['password'])) {
                    $is_valid = true;
                }

                if ($is_valid) {
                    // Start fresh session and store user info
                    $_SESSION['user_id'] = $user['pathologist_id'];
                    $_SESSION['full_name'] = $user['full_name'];
                    
                    // IMPORTANT: Force session to save before redirecting
                    session_write_close();
                    
                    header("Location: dashboard.php");
                    exit();
                } else {
                    $error = "Invalid password. Please try again.";
                }
            } else {
                $error = "No account found with that email address.";
            }
        } catch (PDOException $e) {
            $error = "System error: " . $e->getMessage();
        }
    } else {
        $error = "Please enter both email and password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CLA.I.RE - Login</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind Theme -->
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
        /* Custom Input Container Style */
        .custom-input-container {
            border: 1px solid #fbcfe8; /* brand-border */
            transition: all 0.3s ease;
            background-color: white;
        }
        .custom-input-container:focus-within {
            border-color: #d6006e; /* brand-pink */
            box-shadow: 0 0 0 4px #ffeef2; /* brand-light ring */
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
        
        <!-- Updated Login Button Link -->
        <a href="login.php" class="bg-brand-pink hover:bg-pink-700 text-white font-semibold py-2 px-6 rounded-md transition duration-300">
            Log in
        </a>
    </nav>

    <!-- Main Section -->
    <main class="flex-grow flex items-center justify-center px-4 py-12">

        <div class="bg-transparent w-full max-w-md">

            <div class="text-center mb-10">
                <h2 class="text-3xl font-bold text-brand-dark">
                    Welcome Back!
                </h2>
                <p class="text-gray-500 mt-2 text-sm">Please log in to your account</p>
            </div>

            <!-- Error Display -->
            <?php if ($error): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6 flex items-center gap-3">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span class="text-sm font-medium"><?php echo $error; ?></span>
                </div>
            <?php endif; ?>

            <form action="login.php" method="POST" class="space-y-5">

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
                    <input id="login-password" type="password" name="password"
                           placeholder="Password"
                           class="flex-grow ml-3 bg-transparent outline-none text-brand-dark placeholder-gray-400"
                           required>

                    <button type="button"
                            onclick="togglePassword('login-password', 'login-eye')"
                            class="absolute right-5 text-gray-400 hover:text-gray-600">
                        <i id="login-eye" class="fa-solid fa-eye-slash"></i>
                    </button>
                </div>

                <!-- Forgot Password Link -->
                <div class="text-right">
                    <a href="#" class="text-xs text-brand-pink hover:underline font-medium">Forgot Password?</a>
                </div>

                <!-- Button -->
                <button type="submit"
                        class="w-full bg-brand-pink hover:bg-pink-700 text-white font-bold py-3 rounded-full shadow-md transition text-lg mt-2 active:scale-95 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-right-to-bracket"></i> Log In
                </button>

            </form>

            <!-- Divider -->
            <div class="relative flex items-center py-8">
                <div class="flex-grow border-t border-brand-pink/30"></div>
                <span class="mx-4 text-brand-dark font-medium text-sm text-gray-500">Or log in with</span>
                <div class="flex-grow border-t border-brand-pink/30"></div>
            </div>

            <!-- Social Buttons -->
            <div class="flex gap-4 mb-8">

                <!-- Google -->
                <button class="flex-1 bg-white border border-brand-pink py-2.5 px-4 rounded-full shadow-sm flex items-center justify-center gap-2 hover:bg-pink-50 transition text-brand-dark font-medium text-sm">
                    <img src="https://www.svgrepo.com/show/475656/google-color.svg" class="w-5 h-5">
                    Google
                </button>

                <!-- Facebook -->
                <button class="flex-1 bg-brand-pink text-white py-2.5 px-4 rounded-full shadow-sm flex items-center justify-center gap-2 hover:bg-pink-700 transition font-medium text-sm">
                    <i class="fa-brands fa-facebook-f"></i>
                    Facebook
                </button>

            </div>

            <!-- Sign Up -->
            <p class="text-center text-brand-dark">
                Don't have an account?
                <a href="sign-up.php" class="font-bold text-brand-pink hover:underline">Sign up</a>
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

</body>
</html>
<?php ob_end_flush(); ?>
