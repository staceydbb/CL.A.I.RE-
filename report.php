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

// Fetch specific record if id is provided, otherwise latest
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("
        SELECT record_id, patient_id, ai_prediction, findings, created_at
        FROM Patient_Records_Table
        WHERE record_id = ?
    ");
    $stmt->execute([$_GET['id']]);
    $record = $stmt->fetch();
} else {
    $stmt = $pdo->query("
        SELECT record_id, patient_id, ai_prediction, findings, created_at
        FROM Patient_Records_Table
        ORDER BY created_at DESC
        LIMIT 1
    ");
    $record = $stmt->fetch();
}

if (!$record) {
    echo "<h2>No analysis found.</h2>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CLA.I.RE - Analysis Report</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
          rel="stylesheet">
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
                            hover: '#be0062'
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="min-h-screen flex flex-col bg-brand-light font-sans text-brand-dark">

<!-- Navbar replicated from analyze.php -->
<nav class="bg-white py-4 px-6 md:px-12 flex justify-between items-center shadow-sm sticky top-0 z-50">
    <div class="flex items-center gap-4">
        <a href="dashboard.php" class="flex items-center gap-2 no-underline">
            <div class="bg-brand-pink text-white p-1.5 rounded-md w-8 h-8 flex items-center justify-center">
                <i class="fa-solid fa-microscope text-sm"></i>
            </div>
            <span class="font-bold text-xl text-brand-pink tracking-tight">CLA.I.RE</span>
        </a>
        <div class="hidden md:block h-6 w-px bg-gray-200"></div>
        <span class="hidden md:block text-sm font-medium text-gray-500">Analysis Portal</span>
    </div>
    <a href="dashboard.php" class="text-sm font-semibold text-brand-dark hover:text-brand-pink transition flex items-center gap-2">
        <i class="fa-solid fa-house"></i> Dashboard
    </a>
</nav>

<main class="container mx-auto px-4 py-8 lg:py-12 max-w-4xl">
    <h1 class="text-3xl font-extrabold tracking-tight mb-8">Analysis Report</h1>

    <div class="bg-white p-8 rounded-3xl shadow-sm border border-brand-border space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-sm font-bold text-gray-500 uppercase tracking-widest">Patient ID</p>
                <p class="text-lg font-semibold text-brand-dark">
                    <?php echo htmlspecialchars($record['patient_id']); ?>
                </p>
            </div>
            <div>
                <p class="text-sm font-bold text-gray-500 uppercase tracking-widest">Prediction</p>
                <p class="text-lg font-semibold text-brand-dark">
                    <?php echo htmlspecialchars($record['ai_prediction']); ?>
                </p>
            </div>
            <div>
                <p class="text-sm font-bold text-gray-500 uppercase tracking-widest">Pathologist Note</p>
                <p class="text-lg italic text-gray-600">
                    <?php echo htmlspecialchars($record['findings'] ?: 'None'); ?>
                </p>
            </div>
            <div>
                <p class="text-sm font-bold text-gray-500 uppercase tracking-widest">Timestamp</p>
                <p class="text-lg font-semibold text-brand-dark">
                    <?php echo date('M d, Y H:i', strtotime($record['created_at'])); ?>
                </p>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="mt-8 flex gap-3 w-full">
            <!-- Update Button -->
            <a href="update_report.php?id=<?php echo urlencode($record['record_id']); ?>"
               class="flex-1 bg-brand-pink hover:bg-brand-hover text-white font-bold py-3 rounded-xl shadow-md transition transform active:scale-95 flex items-center justify-center gap-2 text-sm">
                <i class="fa-solid fa-pen-to-square"></i>
                Update
            </a>

            <!-- Delete Button -->
            <button onclick="confirmDelete(<?php echo htmlspecialchars($record['record_id']); ?>)"
                class="flex-1 bg-red-600 hover:bg-red-700 text-white font-bold py-3 rounded-xl shadow-md transition transform active:scale-95 flex items-center justify-center gap-2 text-sm">
                <i class="fa-solid fa-trash text-white"></i>
                Delete
            </button>
        </div>
    </div>
</main>

    <footer class="pt-4 pb-5 text-center text-gray-400 text-xs">
        <div class="flex justify-center items-center gap-1 flex-wrap text-gray-400 font-medium">
            <a href="privacy.php" target="_blank" rel="noopener noreferrer" class="hover:text-brand-pink transition">Privacy Policy</a>
            <span>&bull;</span>
            <a href="terms.php" target="_blank" rel="noopener noreferrer" class="hover:text-brand-pink transition">Terms of Use</a>
            <span>&bull;</span>
            <a href="legal.php" target="_blank" rel="noopener noreferrer" class="hover:text-brand-pink transition">Legal</a>
            <span>&bull;</span>
            <a href="developers.php" target="_blank" rel="noopener noreferrer" class="hover:text-brand-pink transition">About the Developers</a>
        </div>
        <div class="mt-1 text-gray-400 font-normal">
            &copy; <?php echo date('Y'); ?> CLA.I.RE System &bull; All rights Reserved
        </div>
    </footer>

<script>
function confirmDelete(recordId) {
    if (confirm("Are you sure you want to delete this report?")) {
        window.location.href = "delete_report.php?id=" + encodeURIComponent(recordId) + "&redirect=dashboard";
    }
}
</script>

</body>
</html>
