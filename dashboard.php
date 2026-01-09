<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    session_write_close(); 
    header("Location: login.php");
    exit();
}

$pathologist_id = $_SESSION['user_id'];
$fullName = $_SESSION['full_name'] ?? 'Pathologist';
$firstName = explode(' ', str_replace('Dr. ', '', $fullName))[0];

try {
    // 1. Fetch Total Analyses for this Pathologist
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM Patient_Records_Table pr 
                           JOIN Patients_Table p ON pr.patient_id = p.patient_id 
                           WHERE p.pathologist_id = ?");
    $stmt->execute([$pathologist_id]);
    $totalAnalyses = $stmt->fetchColumn() ?: 0;

    // 2. Fetch Normal Cells Count
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM Patient_Records_Table pr 
                           JOIN Patients_Table p ON pr.patient_id = p.patient_id 
                           WHERE p.pathologist_id = ? 
                           AND pr.ai_prediction IN ('Normal', 'Superficial-Intermediate', 'Parabasal', 'Superficial', 'Intermediate')");
    $stmt->execute([$pathologist_id]);
    $totalNormal = $stmt->fetchColumn() ?: 0;

    // 3. Fetch Abnormal Cells Count
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM Patient_Records_Table pr 
                           JOIN Patients_Table p ON pr.patient_id = p.patient_id 
                           WHERE p.pathologist_id = ? 
                           AND pr.ai_prediction IN ('Dyskeratotic', 'Koilocytotic', 'Metaplastic', 'Abnormal')");
    $stmt->execute([$pathologist_id]);
    $totalAbnormal = $stmt->fetchColumn() ?: 0;

    // 4. Fetch Recent Analyses (Top 5)
    $stmt = $pdo->prepare("SELECT pr.*, p.full_name as patient_name 
                           FROM Patient_Records_Table pr 
                           JOIN Patients_Table p ON pr.patient_id = p.patient_id 
                           WHERE p.pathologist_id = ? 
                           ORDER BY pr.created_at DESC LIMIT 5");
    $stmt->execute([$pathologist_id]);
    $recentAnalyses = $stmt->fetchAll();

} catch (PDOException $e) {
    $totalAnalyses = $totalNormal = $totalAbnormal = 0;
    $recentAnalyses = [];
}
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
            
            <!-- Horizontal Navigation Links removed for a cleaner look -->
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

                <!-- Dropdown Menu -->
                <div class="absolute right-0 top-12 w-56 bg-white rounded-lg shadow-lg py-2 hidden group-hover:block border border-gray-100 z-50">
                    <a href="profile.php"
                    class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-brand-light hover:text-brand-pink leading-none">
                        <i class="fa-solid fa-user w-4 text-gray-500"></i>
                        <span class="font-medium">Profile</span>
                    </a>

                    <a href="user_list.php"
                    class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-brand-light hover:text-brand-pink leading-none">
                        <i class="fa-solid fa-users w-4 text-gray-500"></i>
                        <span class="font-medium">User List</span>
                    </a>

                    <a href="logout.php"
                    class="flex items-center gap-3 px-4 py-2 text-sm text-red-600 hover:bg-red-50 leading-none">
                        <i class="fa-solid fa-right-from-bracket w-4 text-red-500"></i>
                        <span class="font-medium">Logout</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <main class="flex-grow container mx-auto px-4 py-8 md:py-12 max-w-6xl">

        <div class="mb-10">
            <h1 class="text-3xl font-extrabold tracking-tight">Welcome back, <?php echo htmlspecialchars($firstName); ?></h1>
            <p class="text-gray-500 mt-1 italic">Clinical assessment hub for AI-powered cervical screening</p>
        </div>

        <!-- Stats Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-brand-border relative overflow-hidden h-32 flex flex-col justify-between group hover:shadow-md transition duration-300">
                <p class="text-gray-500 text-xs font-bold uppercase tracking-widest">Total Analyses</p>
                <h3 class="text-3xl font-bold text-brand-dark"><?php echo number_format($totalAnalyses); ?></h3>
                <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-brand-pink"></div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-brand-border relative overflow-hidden h-32 flex flex-col justify-between group hover:shadow-md transition duration-300">
                <p class="text-gray-500 text-xs font-bold uppercase tracking-widest">Normal Cells</p>
                <h3 class="text-3xl font-bold text-green-600"><?php echo number_format($totalNormal); ?></h3>
                <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-green-500"></div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-brand-border relative overflow-hidden h-32 flex flex-col justify-between group hover:shadow-md transition duration-300">
                <p class="text-gray-500 text-xs font-bold uppercase tracking-widest">Abnormal Cells</p>
                <h3 class="text-3xl font-bold text-red-600"><?php echo number_format($totalAbnormal); ?></h3>
                <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-red-500"></div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row justify-end gap-3 mb-6">
             <a href="patients.php" class="bg-white border border-brand-pink text-brand-pink hover:bg-brand-light font-semibold py-2.5 px-6 rounded-lg transition shadow-sm flex items-center justify-center gap-2 text-sm active:scale-95">
                <i class="fa-solid fa-folder-open"></i> Manage Patients
            </a>
            <a href="analyze.php" class="bg-brand-pink hover:bg-brand-hover text-white font-semibold py-2.5 px-6 rounded-lg transition shadow-md flex items-center justify-center gap-2 text-sm active:scale-95">
                <i class="fa-solid fa-plus"></i> New Analysis
            </a>
        </div>

        <!-- Recent Analyses Section -->
        <div>
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-brand-dark">Recent Analyses</h2>
            </div>

            <?php if(empty($recentAnalyses)): ?>
                <!-- Empty State Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-brand-border border-dashed p-12 text-center flex flex-col items-center justify-center min-h-[300px]">
                    <div class="mb-6 bg-brand-light p-6 rounded-full inline-block">
                        <i class="fa-regular fa-image text-4xl text-brand-pink opacity-80"></i>
                    </div>
                    <h3 class="text-xl font-bold text-brand-dark mb-2">No Analyses yet</h3>
                    <p class="text-gray-500 max-w-sm mx-auto mb-8 text-sm leading-relaxed">
                        You haven't analyzed any cell images yet. Upload your first Pap smear image to get started.
                    </p>
                    <a href="analyze.php" class="bg-brand-pink hover:bg-brand-hover text-white font-bold py-3 px-8 rounded-full shadow-md transition transform hover:-translate-y-0.5 flex items-center gap-2 active:scale-95">
                        Start Analyzing
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            <?php else: ?>
                <!-- Recent Analyses Table -->
                <div class="bg-white rounded-2xl shadow-sm border border-brand-border overflow-hidden">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 border-b border-gray-100">
                            <tr class="text-[11px] font-black uppercase tracking-widest text-gray-400">
                                <th class="px-6 py-4">Patient</th>
                                <th class="px-6 py-4">Classification</th>
                                <th class="px-6 py-4">Date</th>
                                <th class="px-6 py-4 text-right">Details</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <?php foreach($recentAnalyses as $record): ?>
                            <tr class="hover:bg-brand-light/30 transition">
                                <td class="px-6 py-4">
                                    <p class="font-bold text-brand-dark leading-none"><?php echo htmlspecialchars($record['patient_name']); ?></p>
                                    <p class="text-[10px] font-mono font-bold text-brand-pink uppercase tracking-tighter mt-1"><?php echo $record['patient_id']; ?></p>
                                </td>
                                <td class="px-6 py-4">
                                    <?php 
                                    $is_abnormal = !in_array($record['ai_prediction'], ['Normal', 'Superficial-Intermediate', 'Parabasal', 'Superficial', 'Intermediate']);
                                    $badge_color = $is_abnormal ? 'bg-red-50 text-red-600 border-red-100' : 'bg-green-50 text-green-600 border-green-100';
                                    ?>
                                    <span class="px-3 py-1 rounded-full border text-[10px] font-bold uppercase tracking-wider <?php echo $badge_color; ?>">
                                        <?php echo htmlspecialchars($record['ai_prediction']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-400 font-medium">
                                    <?php echo date('M d, Y', strtotime($record['created_at'])); ?>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="report.php?id=<?php echo urlencode($record['record_id']); ?>"
                                        class="text-brand-pink hover:text-brand-hover font-bold text-xs">
                                        View Report
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </main>

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
