<?php

ob_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$pathologist_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patient_id = trim($_POST['patient_id'] ?? '');
    $full_name  = trim($_POST['full_name'] ?? '');
    $dob        = $_POST['dob'] ?? '';
    $gender     = $_POST['gender'] ?? 'F';
    $contact    = trim($_POST['contact_number'] ?? '');
    $history    = trim($_POST['diagnosis_history'] ?? '');

    if (!empty($patient_id) && !empty($full_name) && !empty($dob)) {
        try {
            // Check if Patient ID already exists
            $check = $pdo->prepare("SELECT COUNT(*) FROM Patients_Table WHERE patient_id = ?");
            $check->execute([$patient_id]);
            
            if ($check->fetchColumn() > 0) {
                $error = "Patient ID <strong>$patient_id</strong> is already registered in the system.";
            } else {
                // Insert New Patient
                $stmt = $pdo->prepare("INSERT INTO Patients_Table (patient_id, pathologist_id, full_name, dob, gender, contact_number, diagnosis_history) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$patient_id, $pathologist_id, $full_name, $dob, $gender, $contact, $history]);
                
                $success = "Patient <strong>$full_name</strong> has been successfully registered.";
                // Optional: Redirect to patients list after a delay or provide link
            }
        } catch (PDOException $e) {
            $error = "Database Error: " . $e->getMessage();
        }
    } else {
        $error = "Please fill in all required fields (ID, Name, and Date of Birth).";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CLA.I.RE - Register Patient</title>
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
        .custom-input {
            transition: all 0.3s ease;
        }
        .custom-input:focus-within {
            border-color: #d6006e;
            box-shadow: 0 0 0 4px #ffeef2;
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
            <span class="hidden md:block text-sm font-medium text-gray-500">Patient Registration</span>
        </div>
        <a href="patients.php" class="text-sm font-semibold text-brand-dark hover:text-brand-pink transition flex items-center gap-2">
            <i class="fa-solid fa-arrow-left"></i> Back to Cases
        </a>
    </nav>

    <main class="container mx-auto px-4 py-8 lg:py-12 max-w-5xl">
        
        <!-- Header Info -->
        <div class="mb-8">
            <h1 class="text-3xl font-extrabold tracking-tight">Register New Patient</h1>
            <p class="text-gray-500">Create a permanent medical record for AI screening and longitudinal tracking.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <!-- Left: Form Input (7 Columns) -->
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

                <form action="add_patient.php" method="POST" class="glass-card p-6 md:p-8 rounded-3xl shadow-sm space-y-8">
                    
                    <!-- Section 1: Identity -->
                    <div class="space-y-4">
                        <div class="flex items-center gap-3 text-brand-pink">
                            <span class="w-8 h-8 rounded-full border-2 border-brand-pink flex items-center justify-center font-bold text-sm">1</span>
                            <h2 class="font-bold text-lg">Identity Details</h2>
                        </div>
                        <div class="ml-11 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5 ml-1">Unique Patient ID</label>
                                <div class="custom-input border border-brand-border rounded-2xl flex items-center px-4 py-3 bg-white">
                                    <i class="fa-solid fa-id-card text-brand-pink/50 w-5"></i>
                                    <input type="text" name="patient_id" placeholder="e.g. PT-00895" class="flex-grow ml-3 outline-none text-sm" required>
                                </div>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5 ml-1">Full Legal Name</label>
                                <div class="custom-input border border-brand-border rounded-2xl flex items-center px-4 py-3 bg-white">
                                    <i class="fa-solid fa-user text-brand-pink/50 w-5"></i>
                                    <input type="text" name="full_name" placeholder="Juana Dela Cruz" class="flex-grow ml-3 outline-none text-sm" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Demographics -->
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
                                    <input type="date" name="dob" class="flex-grow ml-3 outline-none text-sm bg-transparent" required>
                                </div>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5 ml-1">Sex/Gender</label>
                                <div class="custom-input border border-brand-border rounded-2xl flex items-center px-4 py-3 bg-white relative">
                                    <i class="fa-solid fa-venus-mars text-brand-pink/50 w-5"></i>
                                    <select name="gender" class="flex-grow ml-3 outline-none text-sm bg-transparent appearance-none" required>
                                        <option value="F">Female</option>
                                        <option value="M">Male</option>
                                    </select>
                                    <i class="fa-solid fa-chevron-down absolute right-4 text-[10px] text-gray-400"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Contact & History -->
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
                                    <input type="text" name="contact_number" placeholder="09XX-XXX-XXXX" class="flex-grow ml-3 outline-none text-sm">
                                </div>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5 ml-1">Clinical History (e.g. HPV status, previous smears)</label>
                                <textarea name="diagnosis_history" rows="3" placeholder="Record relevant medical history..." class="w-full p-4 rounded-2xl border border-brand-border bg-white focus:border-brand-pink focus:ring-4 focus:ring-brand-light outline-none transition resize-none text-sm"></textarea>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-brand-pink text-white font-bold py-4 rounded-full shadow-lg shadow-brand-pink/20 hover:bg-brand-accent transition transform active:scale-95 flex items-center justify-center gap-3 text-lg">
                        <i class="fa-solid fa-user-check"></i> 
                        Confirm Registration
                    </button>
                </form>
            </div>

            <!-- Right: Instructions & Notice (5 Columns) -->
            <div class="lg:col-span-5 space-y-6">
                
                <!-- Guidelines -->
                <div class="glass-card rounded-3xl p-6 shadow-sm relative overflow-hidden">
                    <div class="mb-4">
                        <span class="bg-brand-pink/10 text-brand-pink px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-widest">Registration Guide</span>
                    </div>
                    
                    <ul class="space-y-4">
                        <li class="flex gap-3">
                            <div class="w-5 h-5 rounded bg-green-100 text-green-600 flex-shrink-0 flex items-center justify-center mt-0.5">
                                <i class="fa-solid fa-check text-[10px]"></i>
                            </div>
                            <p class="text-xs text-gray-600">Ensure the <strong>Patient ID</strong> matches your laboratory's physical filing system.</p>
                        </li>
                        <li class="flex gap-3">
                            <div class="w-5 h-5 rounded bg-green-100 text-green-600 flex-shrink-0 flex items-center justify-center mt-0.5">
                                <i class="fa-solid fa-check text-[10px]"></i>
                            </div>
                            <p class="text-xs text-gray-600">Include previous <strong>HPV infection dates</strong> in the history for better diagnostic context.</p>
                        </li>
                        <li class="flex gap-3">
                            <div class="w-5 h-5 rounded bg-green-100 text-green-600 flex-shrink-0 flex items-center justify-center mt-0.5">
                                <i class="fa-solid fa-check text-[10px]"></i>
                            </div>
                            <p class="text-xs text-gray-600">Double-check the <strong>Date of Birth</strong>; age is a significant risk factor in cervical screening.</p>
                        </li>
                    </ul>

                    <div class="absolute bottom-0 right-0 w-32 h-32 bg-brand-pink/5 rounded-full -mr-16 -mb-16"></div>
                </div>

                <!-- Pink Medical Notice -->
                <div class="bg-pink-300/30 border-l-4 border-brand-pink p-6 rounded-3xl relative overflow-hidden">
                    <div class="relative z-10">
                        <div class="flex items-center gap-2 mb-2 text-brand-pink">
                            <i class="fa-solid fa-circle-exclamation text-lg"></i>
                            <h4 class="font-bold text-sm uppercase tracking-wider">Important Medical Notice</h4>
                        </div>
                        <p class="text-[11px] leading-relaxed text-gray-700">
                            Patient data stored in CLA.I.RE is confidential and protected under healthcare privacy standards. This information is used strictly to assist qualified healthcare professionals in early detection. AI interpretations must always be cross-referenced with a pathologist's review before final clinical decisions are made.
                        </p>
                    </div>
                    <div class="absolute top-0 right-0 w-24 h-24 bg-white/20 rounded-full -mr-12 -mt-12 blur-xl"></div>
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
