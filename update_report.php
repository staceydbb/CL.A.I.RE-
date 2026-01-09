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

// Get record ID from query
if (!isset($_GET['id'])) {
    header("Location: report.php");
    exit();
}

$record_id = $_GET['id'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prediction = $_POST['ai_prediction'] ?? '';
    $findings = $_POST['findings'] ?? '';

    $stmt = $pdo->prepare("UPDATE Patient_Records_Table SET ai_prediction = ?, findings = ? WHERE record_id = ?");
    $stmt->execute([$prediction, $findings, $record_id]);

    header("Location: report.php?id=" . urlencode($record_id));
    exit();
}

// Fetch current record
$stmt = $pdo->prepare("SELECT patient_id, ai_prediction, findings FROM Patient_Records_Table WHERE record_id = ?");
$stmt->execute([$record_id]);
$record = $stmt->fetch();

if (!$record) {
    echo "<h2>Record not found.</h2>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CLA.I.RE - Update Report</title>
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
    <h1 class="text-3xl font-extrabold tracking-tight mb-8">Update Analysis Report</h1>

    <form method="POST" class="bg-white p-8 rounded-3xl shadow-sm border border-brand-border space-y-6">
        <div>
            <label class="block text-sm font-bold text-gray-500 uppercase tracking-widest mb-1">Patient ID</label>
            <p class="text-lg font-semibold text-brand-dark"><?php echo htmlspecialchars($record['patient_id']); ?></p>
        </div>

        <div>
            <label for="ai_prediction" class="block text-sm font-bold text-gray-500 uppercase tracking-widest mb-1">Prediction</label>
            <input type="text" name="ai_prediction" id="ai_prediction"
                   value="<?php echo htmlspecialchars($record['ai_prediction']); ?>"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-brand-pink">
        </div>

        <div>
            <label for="findings" class="block text-sm font-bold text-gray-500 uppercase tracking-widest mb-1">Pathologist Note</label>
            <textarea name="findings" id="findings" rows="4"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-brand-pink"><?php echo htmlspecialchars($record['findings']); ?></textarea>
        </div>

        <div class="flex justify-end gap-3 pt-4">
            <a href="report.php?id=<?php echo urlencode($record_id); ?>"
               class="bg-gray-100 hover:bg-gray-200 text-gray-600 font-semibold py-2.5 px-6 rounded-lg transition shadow-sm text-sm">
                Cancel
            </a>
            <button type="submit"
                    class="bg-brand-pink hover:bg-brand-hover text-white font-semibold py-2.5 px-6 rounded-lg transition shadow-md text-sm active:scale-95">
                Save Changes
            </button>
        </div>
    </form>
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
