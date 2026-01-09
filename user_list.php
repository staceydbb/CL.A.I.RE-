<?php

require_once 'db_connect.php';

// Security: If the user is not logged in, redirect them back to the login page
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    session_write_close(); 
    header("Location: login.php");
    exit();
}

$pathologist_id = $_SESSION['user_id'];
$fullName = $_SESSION['full_name'] ?? 'Pathologist';
$firstName = explode(' ', str_replace('Dr. ', '', $fullName))[0];

// FETCH: Retrieve all pathologists (Users) from the database
try {
    $stmt = $pdo->prepare("SELECT pathologist_id, full_name, email, created_at FROM Pathologist_Table ORDER BY full_name ASC");
    $stmt->execute();
    $users = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error fetching user list: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CLA.I.RE - Pathologist Directory</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
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
    <style>
        .glass-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid #fbcfe8;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col bg-brand-light font-sans text-brand-dark">

    <!-- Navbar -->
    <nav class="bg-white py-4 px-6 md:px-12 flex justify-between items-center shadow-sm sticky top-0 z-50">
        <div class="flex items-center gap-8">
            <a href="dashboard.php" class="flex items-center gap-2 select-none no-underline">
                <div class="bg-brand-pink text-white p-1.5 rounded-md w-8 h-8 flex items-center justify-center shadow-sm">
                    <i class="fa-solid fa-microscope text-sm"></i>
                </div>
                <span class="font-bold text-xl tracking-wide text-brand-pink">CLA.I.RE</span>
            </a>
        </div>

        <div class="flex items-center gap-6">
            <div class="flex items-center gap-3 group relative cursor-pointer">
                <div class="text-right hidden sm:block leading-tight">
                    <p class="text-sm font-bold text-brand-dark"><?php echo htmlspecialchars($fullName); ?></p>
                    <p class="text-xs text-gray-500">Pathologist</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-brand-pink text-white flex items-center justify-center font-bold shadow-md transition group-hover:scale-105">
                    <?php echo strtoupper(substr($firstName, 0, 1)); ?>
                </div>

                <!-- Dropdown Menu (TC003 and Navigation) -->
                <div class="absolute right-0 top-12 w-48 bg-white rounded-lg shadow-lg py-2 hidden group-hover:block border border-gray-100 z-50 overflow-hidden">
                    <a href="dashboard.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-brand-light hover:text-brand-pink">
                        <i class="fa-solid fa-house mr-2"></i> Dashboard
                    </a>
                    <a href="" class="block px-4 py-2 text-sm text-gray-700 hover:bg-brand-light hover:text-brand-pink">
                        <i class="fa-solid fa-folder-open mr-2"></i> Profile
                    </a>
                    <a href="user_list.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-brand-light hover:text-brand-pink">
                        <i class="fa-solid fa-users mr-2"></i> User List
                    </a>
                    <div class="border-t border-gray-100 my-1"></div>
                    <a href="logout.php" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                        <i class="fa-solid fa-right-from-bracket mr-2"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-4 py-12 max-w-5xl">
        <div class="mb-10">
            <h1 class="text-3xl font-extrabold tracking-tight">Pathologist Directory</h1>
            <p class="text-gray-500 mt-1 italic">Clinical medical professionals registered in the system.</p>
        </div>

        <!-- User Table -->
        <div class="glass-card rounded-3xl shadow-sm overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-brand-light/20 text-[11px] font-black uppercase tracking-widest text-gray-400">
                    <tr>
                        <th class="px-8 py-5">Full Name</th>
                        <th class="px-8 py-5">Professional ID</th>
                        <th class="px-8 py-5">Email Address</th>
                        <th class="px-8 py-5">Date Joined</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-border/30">
                    <?php foreach ($users as $user): ?>
                    <tr class="hover:bg-brand-pink/[0.02] transition">
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-full bg-brand-pink/10 text-brand-pink flex items-center justify-center font-black text-xs border border-brand-pink/20">
                                    <?php echo strtoupper(substr(str_replace('Dr. ', '', $user['full_name']), 0, 1)); ?>
                                </div>
                                <span class="font-bold text-brand-dark"><?php echo htmlspecialchars($user['full_name']); ?></span>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <span class="font-mono text-xs font-bold text-brand-pink bg-brand-light px-2 py-1 rounded"><?php echo $user['pathologist_id']; ?></span>
                        </td>
                        <td class="px-8 py-6 text-sm text-gray-600">
                            <?php echo htmlspecialchars($user['email']); ?>
                        </td>
                        <td class="px-8 py-6 text-xs text-gray-400 font-medium">
                            <?php echo date('M d, Y', strtotime($user['created_at'])); ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Medical Notice Footer -->
        <div class="mt-12 bg-pink-300/30 border-l-8 border-brand-pink p-6 rounded-3xl">
            <p class="text-[11px] leading-relaxed text-gray-700 font-medium">
                <strong>Access Log:</strong> All user list viewings are logged for audit purposes to ensure clinical data integrity and professional privacy standards.
            </p>
        </div>
    </main>

    <footer class="py-6 text-center text-gray-400 text-xs mt-auto">
        &copy; <?php echo date('Y'); ?> CLA.I.RE System • Professional Directory
    </footer>

</body>
</html>
