<?php
require_once 'db_connect.php';


if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    session_write_close(); 
    header("Location: login.php");
    exit();
}

$fullName = $_SESSION['full_name'] ?? 'Pathologist';
$firstName = explode(' ', str_replace('Dr. ', '', $fullName))[0];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CLA.I.RE - Dashboard</title>


    <script src="https://cdn.tailwindcss.com"></script>

   
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

   
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
                            hover: '#be0062',
                        }
                    }
                }
            }
        }
    </script>
</head>

<body class="min-h-screen flex flex-col bg-brand-light">

    <!-- Navbar -->
    <nav class="bg-white py-4 px-6 md:px-12 flex justify-between items-center shadow-sm sticky top-0 z-50">
        <a href="dashboard.php" class="flex items-center gap-2 select-none no-underline">
            <div class="bg-brand-pink text-white p-1.5 rounded-md w-8 h-8 flex items-center justify-center shadow-sm">
                <i class="fa-solid fa-microscope text-sm"></i>
            </div>
            <span class="font-bold text-xl tracking-wide text-brand-pink">CLA.I.RE</span>
        </a>

        <!-- User Profile & Logout -->
        <div class="flex items-center gap-6">
            <!-- User Dropdown Area -->
            <div class="flex items-center gap-3 group relative cursor-pointer">
                <div class="text-right hidden sm:block leading-tight">
                    <p class="text-sm font-bold text-brand-dark"><?php echo htmlspecialchars($fullName); ?></p>
                    <p class="text-xs text-gray-500">Pathologist</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-brand-pink text-white flex items-center justify-center font-bold shadow-md ring-2 ring-transparent group-hover:ring-brand-border transition">
                    <?php echo strtoupper(substr($firstName, 0, 1)); ?>
                </div>

                <!-- Dropdown Menu -->
                <div class="absolute right-0 top-12 w-48 bg-white rounded-lg shadow-lg py-2 hidden group-hover:block border border-gray-100 z-50">
                    <a href="profile.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-brand-light hover:text-brand-pink">
                        <i class="fa-solid fa-user mr-2"></i> Profile
                    </a>
                    <a href="settings.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-brand-light hover:text-brand-pink">
                        <i class="fa-solid fa-gear mr-2"></i> Settings
                    </a>
                    <div class="border-t border-gray-100 my-1"></div>
                    <a href="logout.php" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                        <i class="fa-solid fa-right-from-bracket mr-2"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow container mx-auto px-4 py-8 md:py-12 max-w-6xl">

        <!-- Welcome Header -->
        <div class="mb-10">
            <h1 class="text-3xl font-bold text-brand-dark">Welcome back, <?php echo htmlspecialchars($firstName); ?></h1>
            <p class="text-gray-500 mt-1 italic">Upload and analyze Pap smear cells using AI-powered classification</p>
        </div>

        <!-- Stats Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
            <!-- Total Analyses -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-brand-border relative overflow-hidden h-32 flex flex-col justify-between group hover:shadow-md transition duration-300">
                <p class="text-gray-500 text-xs font-bold uppercase tracking-widest">Total Analyses</p>
                <h3 class="text-3xl font-bold text-brand-dark">0</h3>
                <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-brand-pink"></div>
            </div>

            <!-- Normal Cells -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-brand-border relative overflow-hidden h-32 flex flex-col justify-between group hover:shadow-md transition duration-300">
                <p class="text-gray-500 text-xs font-bold uppercase tracking-widest">Normal Cells</p>
                <div class="flex justify-between items-end">
                    <h3 class="text-3xl font-bold text-brand-dark">0</h3>
                    <span class="text-xs text-gray-400 font-medium italic">This Week <span class="text-brand-dark font-bold ml-1">0</span></span>
                </div>
                <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-green-500"></div>
            </div>

            <!-- Abnormal Cells -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-brand-border relative overflow-hidden h-32 flex flex-col justify-between group hover:shadow-md transition duration-300">
                <p class="text-gray-500 text-xs font-bold uppercase tracking-widest">Abnormal Cells</p>
                <div class="flex justify-between items-end">
                    <h3 class="text-3xl font-bold text-brand-dark">0</h3>
                    <span class="text-xs text-gray-400 font-medium italic">This Week <span class="text-brand-dark font-bold ml-1">0</span></span>
                </div>
                <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-red-500"></div>
            </div>
        </div>

        <!-- Action Buttons (Positioned Above Recent Analysis Section) -->
        <div class="flex flex-col sm:flex-row justify-end gap-3 mb-6">
             <a href="patients.php" class="bg-white border border-brand-pink text-brand-pink hover:bg-brand-light font-semibold py-2.5 px-6 rounded-lg transition shadow-sm flex items-center justify-center gap-2 text-sm active:scale-95">
                <i class="fa-solid fa-folder-open"></i> Manage Cases
            </a>
            <a href="analyze.php" class="bg-brand-pink hover:bg-brand-hover text-white font-semibold py-2.5 px-6 rounded-lg transition shadow-md flex items-center justify-center gap-2 text-sm active:scale-95">
                <i class="fa-solid fa-plus"></i> New Analysis
            </a>
        </div>

        <!-- Recent Analyses Section -->
        <div>
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-brand-dark">Recent Analyses</h2>
                <button class="text-sm text-brand-pink hover:underline font-medium">View All</button>
            </div>

            <!-- Empty State Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-brand-border border-dashed p-12 text-center flex flex-col items-center justify-center min-h-[300px]">
                <div class="mb-6 bg-brand-light p-6 rounded-full inline-block">
                    <i class="fa-regular fa-image text-4xl text-brand-pink opacity-80"></i>
                </div>
                
                <h3 class="text-xl font-bold text-brand-dark mb-2">No Analyses yet</h3>
                <p class="text-gray-500 max-w-sm mx-auto mb-8 text-sm leading-relaxed">
                    You haven't analyzed any cell images yet. Upload your first Pap smear image to get started with the AI screening process.
                </p>

                <a href="analyze.php" class="bg-brand-pink hover:bg-brand-hover text-white font-bold py-3 px-8 rounded-full shadow-md transition transform hover:-translate-y-0.5 flex items-center gap-2 active:scale-95">
                    Start Analyzing
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </div>

    </main>

    <!-- Simple footer -->
    <footer class="py-6 text-center text-gray-400 text-xs">
        &copy; <?php echo date('Y'); ?> CLA.I.RE System. All rights reserved.
    </footer>

</body>
</html>
