<?php

ob_start();
require_once 'db_connect.php';

// Security Check: Redirect to login if not authenticated
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$pathologist_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Get Patient ID from URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: patients.php");
    exit();
}

$target_id = $_GET['id'];

// Fetch Current Data to pre-fill the form
try {
    $stmt = $pdo->prepare("SELECT * FROM Patients_Table WHERE patient_id = ? AND pathologist_id = ?");
    $stmt->execute([$target_id, $pathologist_id]);
    $patient = $stmt->fetch();

    if (!$patient) {
        die("Patient record not found or access denied.");
    }
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}

// Handle Update Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name  = trim($_POST['full_name'] ?? '');
    $dob        = $_POST['dob'] ?? '';
    $gender     = $_POST['gender'] ?? 'F';
    $contact    = trim($_POST['contact_number'] ?? '');
    $history    = trim($_POST['diagnosis_history'] ?? '');

    if (!empty($full_name) && !empty($dob)) {
        try {
            $update = $pdo->prepare("UPDATE Patients_Table SET full_name = ?, dob = ?, gender = ?, contact_number = ?, diagnosis_history = ? WHERE patient_id = ? AND pathologist_id = ?");
            $update->execute([$full_name, $dob, $gender, $contact, $history, $target_id, $pathologist_id]);
            
            $success = "Patient record updated successfully.";
            // Refresh local data for display
            $patient['full_name'] = $full_name;
            $patient['dob'] = $dob;
            $patient['gender'] = $gender;
            $patient['contact_number'] = $contact;
            $patient['diagnosis_history'] = $history;
            
        } catch (PDOException $e) {
            $error = "Update Failed: " . $e->getMessage();
        }
    } else {
        $error = "Required fields (Name and Date of Birth) cannot be empty.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CLA.I.RE - Edit Patient</title>
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
                            accent: '#cf007f'
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
        .custom-input { transition: all 0.3s ease; }
        .custom-input:focus-within { border-color: #d6006e; box-shadow: 0 0 0 4px #ffeef2; }
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
            <span class="hidden md:block text-sm font-medium text-gray-500">Edit Patient Profile</span>
        </div>
        <a href="patients.php" class="text-sm font-semibold text-brand-dark hover:text-brand-pink transition flex items-center gap-2">
            <i class="fa-solid fa-arrow-left"></i> Cancel and Return
        </a>
    </nav>

    <main class="container mx-auto px-4 py-8 lg:py-12 max-w-5xl">
        
        <!-- Header Info -->
        <div class="mb-8">
            <h1 class="text-3xl font-extrabold tracking-tight">Update Patient Record</h1>
            <p class="text-gray-500">Editing profile for ID: <span class="font-mono text-brand-pink font-bold"><?php echo htmlspecialchars($target_id); ?></span></p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <!-- Left: Form Input -->
            <div class="lg:col-span-7 space-y-6">
                
                <?php if($error): ?>
                    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r-xl flex items-center gap-3">
                        <i class="fa-solid fa-circle-exclamation text-red-500 text-xl"></i>
                        <div class="text-sm font-semibold text-red-800"><?php echo $error; ?></div>
                    </div>
                <?php endif; ?>

                <?php if($success): ?>
                    <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-r-xl flex items-center gap-3">
                        <i class="fa-solid fa-circle-check text-green-500 text-xl"></i>
                        <div class="text-sm font-semibold text-green-800"><?php echo $success; ?></div>
                    </div>
                <?php endif; ?>

                <form action="edit_patient.php?id=<?php echo $target_id; ?>" method="POST" class="glass-card p-6 md:p-8 rounded-3xl shadow-sm space-y-8">
                    
                    <!-- Identity (ReadOnly ID) -->
                    <div class="space-y-4">
                        <div class="flex items-center gap-3 text-brand-pink">
                            <span class="w-8 h-8 rounded-full border-2 border-brand-pink flex items-center justify-center font-bold text-sm">1</span>
                            <h2 class="font-bold text-lg">Identity Details</h2>
                        </div>
                        <div class="ml-11 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5 ml-1">Patient ID (Locked)</label>
                                <div class="bg-gray-100 border border-gray-200 rounded-2xl flex items-center px-4 py-3 cursor-not-allowed">
                                    <i class="fa-solid fa-lock text-gray-400 w-5"></i>
                                    <input type="text" value="<?php echo htmlspecialchars($target_id); ?>" class="flex-grow ml-3 outline-none text-sm bg-transparent text-gray-500" readonly>
                                </div>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5 ml-1">Full Legal Name</label>
                                <div class="custom-input border border-brand-border rounded-2xl flex items-center px-4 py-3 bg-white">
                                    <i class="fa-solid fa-user text-brand-pink/50 w-5"></i>
                                    <input type="text" name="full_name" value="<?php echo htmlspecialchars($patient['full_name']); ?>" class="flex-grow ml-3 outline-none text-sm" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Demographics -->
                    <div class="space-y-4">
                        <div class="flex items-center gap-3 text-brand-pink">
                            <span class="w-8 h-8 rounded-full border-2 border-brand-pink flex items-center justify-center font-bold text-sm">2</span>
                            <h2 class="font-bold text-lg">Demographics</h2>
                        </div>
                        <div class="ml-11 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5 ml-1">Date of Birth</label>
                                <div class="custom-input border border-brand-border rounded-2xl flex items-center px-4 py-3 bg-white">
                                    <i class="fa-solid fa-calendar text-brand-pink/50 w-5"></i>
                                    <input type="date" name="dob" value="<?php echo $patient['dob']; ?>" class="flex-grow ml-3 outline-none text-sm bg-transparent" required>
                                </div>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5 ml-1">Sex/Gender</label>
                                <div class="custom-input border border-brand-border rounded-2xl flex items-center px-4 py-3 bg-white relative">
                                    <i class="fa-solid fa-venus-mars text-brand-pink/50 w-5"></i>
                                    <select name="gender" class="flex-grow ml-3 outline-none text-sm bg-transparent appearance-none" required>
                                        <option value="F" <?php echo $patient['gender'] == 'F' ? 'selected' : ''; ?>>Female</option>
                                        <option value="M" <?php echo $patient['gender'] == 'M' ? 'selected' : ''; ?>>Male</option>
                                    </select>
                                    <i class="fa-solid fa-chevron-down absolute right-4 text-[10px] text-gray-400"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact & History -->
                    <div class="space-y-4">
                        <div class="flex items-center gap-3 text-brand-pink">
                            <span class="w-8 h-8 rounded-full border-2 border-brand-pink flex items-center justify-center font-bold text-sm">3</span>
                            <h2 class="font-bold text-lg">Contact & History</h2>
                        </div>
                        <div class="ml-11 space-y-4">
                            <div>
                                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5 ml-1">Phone Number</label>
                                <div class="custom-input border border-brand-border rounded-2xl flex items-center px-4 py-3 bg-white">
                                    <i class="fa-solid fa-phone text-brand-pink/50 w-5"></i>
                                    <input type="text" name="contact_number" value="<?php echo htmlspecialchars($patient['contact_number']); ?>" class="flex-grow ml-3 outline-none text-sm">
                                </div>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5 ml-1">Clinical History</label>
                                <textarea name="diagnosis_history" rows="3" class="w-full p-4 rounded-2xl border border-brand-border bg-white focus:border-brand-pink focus:ring-4 focus:ring-brand-light outline-none transition resize-none text-sm"><?php echo htmlspecialchars($patient['diagnosis_history']); ?></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-4 pt-4">
                        <button type="submit" class="flex-grow bg-brand-pink text-white font-bold py-4 rounded-full shadow-lg hover:bg-brand-accent transition transform active:scale-95 flex items-center justify-center gap-3">
                            <i class="fa-solid fa-save"></i> Save Changes
                        </button>
                        <a href="patients.php" class="flex-grow bg-white border border-gray-200 text-gray-500 font-bold py-4 rounded-full text-center hover:bg-gray-50 transition">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>

            <!-- Right: Contextual Info -->
            <div class="lg:col-span-5 space-y-6">
                <div class="glass-card rounded-3xl p-6 shadow-sm relative overflow-hidden">
                    <span class="bg-blue-50 text-blue-600 px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-widest">Record Status</span>
                    <div class="mt-6 flex items-center gap-4">
                        <div class="w-16 h-16 rounded-2xl bg-brand-pink/10 text-brand-pink flex items-center justify-center text-2xl font-black">
                            <?php echo strtoupper(substr($patient['full_name'], 0, 1)); ?>
                        </div>
                        <div>
                            <p class="font-bold text-lg leading-tight"><?php echo htmlspecialchars($patient['full_name']); ?></p>
                            <p class="text-xs text-gray-400 italic">Registered since <?php echo date('M Y', strtotime($patient['created_at'])); ?></p>
                        </div>
                    </div>
                    <div class="mt-8 pt-6 border-t border-gray-100 flex justify-between items-center">
                        <p class="text-[10px] font-bold uppercase text-gray-400">Total Analyses</p>
                        <span class="bg-brand-pink text-white px-3 py-1 rounded-lg text-xs font-bold">0 Active</span>
                    </div>
                </div>

                <!-- Pink Medical Notice -->
                <div class="bg-pink-300/30 border-l-4 border-brand-pink p-6 rounded-3xl relative overflow-hidden">
                    <div class="relative z-10">
                        <div class="flex items-center gap-2 mb-2 text-brand-pink">
                            <i class="fa-solid fa-circle-exclamation text-lg"></i>
                            <h4 class="font-bold text-sm uppercase tracking-wider">Clinical Integrity</h4>
                        </div>
                        <p class="text-[11px] leading-relaxed text-gray-700">
                            Updating patient demographics ensures that screening results are interpreted correctly based on age-specific risk factors. Always verify identity before confirming record changes.
                        </p>
                    </div>
                </div>
            </div>
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
<?php ob_end_flush(); ?>
