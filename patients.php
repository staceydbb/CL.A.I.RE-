<?php

ob_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$pathologist_id = $_SESSION['user_id'];
$search = trim($_GET['search'] ?? '');

// FETCH: Search and Read patients for this specific doctor
try {
    if (!empty($search)) {
        $stmt = $pdo->prepare("SELECT * FROM Patients_Table WHERE pathologist_id = ? AND (full_name LIKE ? OR patient_id LIKE ?) ORDER BY created_at DESC");
        $stmt->execute([$pathologist_id, "%$search%", "%$search%"]);
    } else {
        $stmt = $pdo->prepare("SELECT * FROM Patients_Table WHERE pathologist_id = ? ORDER BY created_at DESC");
        $stmt->execute([$pathologist_id]);
    }
    $patients = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error fetching patients: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CLA.I.RE - Manage Cases</title>
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
        .table-row-hover:hover {
            background-color: rgba(214, 0, 110, 0.03);
        }
    </style>
</head>
<body class="min-h-screen bg-brand-light font-sans text-brand-dark">

    <!-- Navbar -->
    <nav class="bg-white py-4 px-6 md:px-12 flex justify-between items-center shadow-sm sticky top-0 z-50">
        <div class="flex items-center gap-4">
            <a href="dashboard.php" class="flex items-center gap-2 no-underline">
                <div class="bg-brand-pink text-white p-1.5 rounded-md w-8 h-8 flex items-center justify-center">
                    <i class="fa-solid fa-microscope text-sm"></i>
                </div>
                <span class="font-bold text-xl text-brand-pink tracking-tight">CLA.I.RE</span>
            </a>
            <div class="hidden md:block h-6 w-px bg-gray-200"></div>
            <span class="hidden md:block text-sm font-medium text-gray-500">Case Management</span>
        </div>
        <a href="dashboard.php" class="text-sm font-semibold text-brand-dark hover:text-brand-pink transition flex items-center gap-2">
            <i class="fa-solid fa-house"></i> Dashboard
        </a>
    </nav>

    <main class="container mx-auto px-4 py-8 lg:py-12 max-w-6xl">
        
        <!-- Header & Top Actions -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-6">
            <div>
                <h1 class="text-3xl font-extrabold tracking-tight">Patient Cases</h1>
                <p class="text-gray-500">Review demographics, update profiles, or initiate new AI screenings.</p>
            </div>
            <a href="add_patient.php" class="bg-brand-pink text-white font-bold py-3.5 px-8 rounded-full shadow-lg shadow-brand-pink/20 hover:bg-pink-700 transition transform active:scale-95 flex items-center gap-2">
                <i class="fa-solid fa-user-plus"></i> Register New Patient
            </a>
        </div>

        <!-- Unified Search & Table Container -->
        <div class="glass-card rounded-3xl shadow-sm overflow-hidden mb-12">
            
            <!-- Search Context -->
            <div class="p-6 border-b border-brand-border bg-white/50">
                <form method="GET" class="flex gap-3">
                    <div class="flex-grow relative group">
                        <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-300 group-focus-within:text-brand-pink transition"></i>
                        <input type="text" name="search" placeholder="Filter by Patient ID (PT-00XXX) or Full Name..." 
                               value="<?php echo htmlspecialchars($search); ?>" 
                               class="w-full pl-12 pr-4 py-3 rounded-2xl border border-gray-200 focus:border-brand-pink focus:ring-4 focus:ring-brand-light outline-none transition bg-white/80">
                    </div>
                    <button type="submit" class="bg-brand-dark text-white px-8 rounded-2xl font-bold hover:opacity-90 transition">Search</button>
                    <?php if($search): ?>
                        <a href="patients.php" class="bg-gray-100 text-gray-500 px-6 flex items-center rounded-2xl hover:bg-gray-200 transition">
                            <i class="fa-solid fa-xmark"></i>
                        </a>
                    <?php endif; ?>
                </form>
            </div>

            <!-- Results Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-brand-light/20 text-[11px] font-black uppercase tracking-widest text-gray-400">
                            <th class="px-8 py-5">Patient Identity</th>
                            <th class="px-8 py-5">Contact & History</th>
                            <th class="px-8 py-5">Added On</th>
                            <th class="px-8 py-5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-brand-border/30">
                        <?php if (empty($patients)): ?>
                            <tr>
                                <td colspan="4" class="px-8 py-20 text-center">
                                    <div class="opacity-20 mb-4">
                                        <i class="fa-solid fa-folder-open text-6xl"></i>
                                    </div>
                                    <p class="font-bold text-gray-400">No active cases found.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($patients as $p): ?>
                            <tr class="table-row-hover transition">
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-full bg-brand-pink/10 text-brand-pink flex items-center justify-center font-black text-xs border border-brand-pink/20">
                                            <?php echo substr($p['full_name'], 0, 1); ?>
                                        </div>
                                        <div>
                                            <p class="font-bold text-brand-dark leading-none mb-1"><?php echo htmlspecialchars($p['full_name']); ?></p>
                                            <p class="text-[10px] font-mono font-bold text-brand-pink uppercase tracking-tighter"><?php echo $p['patient_id']; ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="text-xs text-gray-600 mb-1">
                                        <i class="fa-solid fa-phone text-[10px] mr-1 opacity-50"></i> <?php echo htmlspecialchars($p['contact_number'] ?: 'No Contact'); ?>
                                    </div>
                                    <p class="text-[11px] italic text-gray-400 line-clamp-1 max-w-xs"><?php echo htmlspecialchars($p['diagnosis_history'] ?: 'Clear clinical history'); ?></p>
                                </td>
                                <td class="px-8 py-6">
                                    <span class="text-xs text-gray-500"><?php echo date('M d, Y', strtotime($p['created_at'])); ?></span>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="analyze.php?patient_id=<?php echo $p['patient_id']; ?>" class="w-9 h-9 rounded-xl bg-brand-pink text-white flex items-center justify-center hover:bg-brand-accent transition shadow-md shadow-brand-pink/10" title="Start Analysis">
                                            <i class="fa-solid fa-microscope text-sm"></i>
                                        </a>
                                        <a href="edit_patient.php?id=<?php echo $p['patient_id']; ?>" class="w-9 h-9 rounded-xl bg-white border border-gray-200 text-blue-500 flex items-center justify-center hover:bg-blue-50 transition" title="Edit Profile">
                                            <i class="fa-solid fa-user-pen text-sm"></i>
                                        </a>
                                        <a href="delete_patient.php?id=<?php echo $p['patient_id']; ?>" 
                                           onclick="return confirm('Delete all records for <?php echo addslashes($p['full_name']); ?>?')"
                                           class="w-9 h-9 rounded-xl bg-white border border-gray-200 text-red-500 flex items-center justify-center hover:bg-red-50 transition" title="Remove Case">
                                            <i class="fa-solid fa-trash-can text-sm"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pink Medical Notice -->
        <div class="bg-pink-300/30 border-l-8 border-brand-pink p-8 rounded-3xl relative overflow-hidden">
            <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center gap-6">
                <div class="p-4 bg-white rounded-2xl text-brand-pink shadow-sm">
                    <i class="fa-solid fa-shield-medical text-3xl"></i>
                </div>
                <div>
                    <h3 class="text-brand-pink font-extrabold text-xl mb-1">Important Medical Notice</h3>
                    <p class="text-gray-700 text-sm leading-relaxed max-w-4xl">
                        This AI analysis is an assistive tool for qualified healthcare professionals only. Results should be interpreted within clinical context and should not replace professional medical diagnosis, pathologist review, or clinical judgment. Always consult with qualified medical professionals for final diagnosis and treatment decisions.
                    </p>
                </div>
            </div>
            <!-- Decorative overlay -->
            <div class="absolute top-0 right-0 w-64 h-64 bg-white/20 rounded-full -mr-32 -mt-32 blur-3xl"></div>
        </div>

    </main>

     <footer class="py-6 text-center text-gray-400 text-xs">
        &copy; <?php echo date('Y'); ?> CLA.I.RE System. All rights reserved.
    </footer>
    
</body>
</html>
<?php ob_end_flush(); ?>
