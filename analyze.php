<?php
session_start();
ob_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$pathologist_id = $_SESSION['user_id'];
// Persistent selection logic
$pre_selected_id = $_POST['patient_id'] ?? ($_GET['patient_id'] ?? '');
$error = '';
$success = '';
$result_data = null;

// Fetch list of patients for the dropdown selection
$patients_stmt = $pdo->prepare("SELECT patient_id, full_name FROM Patients_Table WHERE pathologist_id = ?");
$patients_stmt->execute([$pathologist_id]);
$patient_list = $patients_stmt->fetchAll();

// Fetch the currently selected patient's name for the UI dashboard (Persistence)
$selected_patient_name = '';
if (!empty($pre_selected_id)) {
    $stmt_name = $pdo->prepare("SELECT full_name FROM Patients_Table WHERE patient_id = ? AND pathologist_id = ?");
    $stmt_name->execute([$pre_selected_id, $pathologist_id]);
    $selected_patient_name = $stmt_name->fetchColumn();
}

// Handle Form Submission (Create Analysis)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patient_id = $_POST['patient_id'];
    $report_date = date('Y-m-d');
    $findings = trim($_POST['findings']);
    
    // Mock AI Prediction logic
    $predictions = ['Normal', 'Superficial-Intermediate', 'Parabasal', 'Koilocytotic', 'Dyskeratotic', 'Metaplastic'];
    $ai_prediction = $predictions[array_rand($predictions)];
    
    // Handle File Upload
    if (isset($_FILES['cell_image']) && $_FILES['cell_image']['error'] === 0) {
        $upload_dir = '../uploads/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        
        $file_ext = pathinfo($_FILES['cell_image']['name'], PATHINFO_EXTENSION);
        $filename = $patient_id . "_" . time() . "." . $file_ext;
        $target_path = $upload_dir . $filename;
        
        if (move_uploaded_file($_FILES['cell_image']['tmp_name'], $target_path)) {
            try {
                $stmt = $pdo->prepare("INSERT INTO Patient_Records_Table (patient_id, report_date, image_path, ai_prediction, findings) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$patient_id, $report_date, $filename, $ai_prediction, $findings]);
                
                $success = "Analysis Completed Successfully.";
                $result_data = [
                    'prediction' => $ai_prediction,
                    'image' => $filename,
                    'findings' => $findings,
                    'patient_name' => $selected_patient_name,
                    'patient_id' => $patient_id
                ];
            } catch (PDOException $e) {
                $error = "Database error: " . $e->getMessage();
            }
        } else {
            $error = "Failed to upload image.";
        }
    } else {
        $error = "Please upload a valid Pap smear cell image.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CLA.I.RE - AI Analysis Portal</title>
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
        .step-active { color: #d6006e; border-color: #d6006e; }
        .result-pulse { animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
        @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: .7; } }
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
            <span class="hidden md:block text-sm font-medium text-gray-500">Analysis Portal</span>
        </div>
        <a href="dashboard.php" class="text-sm font-semibold text-brand-dark hover:text-brand-pink transition flex items-center gap-2">
            <i class="fa-solid fa-house"></i> Dashboard
        </a>
    </nav>

    <main class="container mx-auto px-4 py-8 lg:py-12 max-w-5xl">
        
        <!-- Header Info -->
        <div class="mb-8">
            <h1 class="text-3xl font-extrabold tracking-tight">Perform New Screening</h1>
            <p class="text-gray-500">Intelligent cervical cancer risk assessment for clinical decision support.</p>
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

                <form action="analyze.php" method="POST" enctype="multipart/form-data" class="glass-card p-6 md:p-8 rounded-3xl shadow-sm space-y-8">
                    
                    <!-- Section 1: Patient Association -->
                    <div class="space-y-4">
                        <div class="flex items-center gap-3 text-brand-pink">
                            <span class="w-8 h-8 rounded-full border-2 border-brand-pink flex items-center justify-center font-bold text-sm">1</span>
                            <h2 class="font-bold text-lg">Patient Information</h2>
                        </div>
                        <div class="ml-11">
                            <label class="block text-xs font-bold uppercase tracking-widest text-gray-400 mb-2">Select Registered Patient</label>
                            <div class="relative">
                                <i class="fa-solid fa-user-tag absolute left-4 top-1/2 -translate-y-1/2 text-brand-pink"></i>
                                <select name="patient_id" onchange="this.form.action='analyze.php'; this.form.method='GET'; this.form.submit();" class="w-full pl-12 pr-4 py-3.5 rounded-2xl border border-brand-border bg-white focus:border-brand-pink focus:ring-4 focus:ring-brand-light outline-none transition appearance-none" required>
                                    <option value="" disabled <?php echo empty($pre_selected_id) ? 'selected' : ''; ?>>Search by ID or Name...</option>
                                    <?php foreach($patient_list as $pl): ?>
                                        <option value="<?php echo $pl['patient_id']; ?>" <?php echo ($pre_selected_id === $pl['patient_id']) ? 'selected' : ''; ?>>
                                            [<?php echo $pl['patient_id']; ?>] <?php echo htmlspecialchars($pl['full_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Image Upload -->
                    <div class="space-y-4">
                        <div class="flex items-center gap-3 text-brand-pink">
                            <span class="w-8 h-8 rounded-full border-2 border-brand-pink flex items-center justify-center font-bold text-sm">2</span>
                            <h2 class="font-bold text-lg">Cell Image Upload</h2>
                        </div>
                        <div class="ml-11">
                            <label class="block text-xs font-bold uppercase tracking-widest text-gray-400 mb-2">SIPaKMeD Compliant Slide</label>
                            <div class="relative group">
                                <div id="drop-zone" class="border-2 border-dashed border-brand-border rounded-3xl p-10 text-center hover:border-brand-pink hover:bg-white transition cursor-pointer relative overflow-hidden">
                                    <input type="file" name="cell_image" id="file-input" class="absolute inset-0 opacity-0 cursor-pointer z-10" required>
                                    <div id="upload-placeholder">
                                        <div class="w-16 h-16 bg-brand-light rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition duration-300">
                                            <i class="fa-solid fa-cloud-arrow-up text-brand-pink text-2xl"></i>
                                        </div>
                                        <p class="font-bold text-brand-dark">Click to browse or drag & drop</p>
                                        <p class="text-xs text-gray-400 mt-1">Supports JPG, PNG, GIF (Max 5MB)</p>
                                    </div>
                                    <div id="preview-container" class="hidden absolute inset-0 bg-white">
                                        <img id="image-preview" src="#" alt="Preview" class="w-full h-full object-contain">
                                        <button type="button" id="remove-img" class="absolute top-2 right-2 bg-brand-dark text-white w-8 h-8 rounded-full hover:bg-brand-pink transition z-20">
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Clinical Notes -->
                    <div class="space-y-4">
                        <div class="flex items-center gap-3 text-brand-pink">
                            <span class="w-8 h-8 rounded-full border-2 border-brand-pink flex items-center justify-center font-bold text-sm">3</span>
                            <h2 class="font-bold text-lg">Clinical Findings</h2>
                        </div>
                        <div class="ml-11">
                            <label class="block text-xs font-bold uppercase tracking-widest text-gray-400 mb-2">Observations (Optional)</label>
                            <textarea name="findings" rows="3" placeholder="Enter nuclear shape, texture, or inflammatory observations..." class="w-full p-4 rounded-2xl border border-brand-border bg-white focus:border-brand-pink focus:ring-4 focus:ring-brand-light outline-none transition resize-none"><?php echo isset($_POST['findings']) ? htmlspecialchars($_POST['findings']) : ''; ?></textarea>
                        </div>
                    </div>

                    <button type="submit" id="submit-btn" onclick="this.form.action='analyze.php'; this.form.method='POST';" class="w-full bg-brand-pink text-white font-bold py-4 rounded-full shadow-lg shadow-brand-pink/20 hover:bg-brand-accent transition transform active:scale-95 flex items-center justify-center gap-3 text-lg">
                        <i class="fa-solid fa-wand-magic-sparkles"></i> 
                        <span id="btn-text">Initiate AI Assessment</span>
                    </button>
                </form>
            </div>

            <!-- Right: Results Dashboard (5 Columns) -->
            <div class="lg:col-span-5 space-y-6">
                
                <!-- Result Information -->
                <div class="glass-card rounded-3xl p-6 shadow-sm min-h-[400px] flex flex-col items-center justify-center text-center relative overflow-hidden">
                    
                    <?php if($result_data): ?>
                        <!-- Active Result View -->
                        <div class="mb-4">
                            <span class="bg-brand-pink/10 text-brand-pink px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-widest">Prediction Outcome</span>
                        </div>
                        
                        <div class="mb-6">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">Current Evaluation</p>
                            <h4 class="text-xl font-bold text-brand-dark"><?php echo htmlspecialchars($result_data['patient_name']); ?></h4>
                            <p class="text-xs font-mono text-brand-pink"><?php echo htmlspecialchars($result_data['patient_id']); ?></p>
                        </div>

                        <div class="w-32 h-32 rounded-3xl overflow-hidden shadow-lg border-4 border-white mb-6 mx-auto">
                            <img src="../uploads/<?php echo $result_data['image']; ?>" class="w-full h-full object-cover">
                        </div>

                        <h3 class="text-4xl font-black text-brand-dark mb-2 tracking-tight"><?php echo $result_data['prediction']; ?></h3>
                        
                        <?php 
                        $is_abnormal = !in_array($result_data['prediction'], ['Normal', 'Superficial-Intermediate', 'Parabasal']);
                        $status_color = $is_abnormal ? 'text-red-500' : 'text-green-500';
                        $status_bg = $is_abnormal ? 'bg-red-50' : 'bg-green-50';
                        $icon = $is_abnormal ? 'fa-triangle-exclamation' : 'fa-circle-check';
                        ?>

                        <div class="<?php echo $status_bg; ?> <?php echo $status_color; ?> px-6 py-3 rounded-2xl flex items-center gap-3 mb-8 font-bold mx-auto w-fit">
                            <i class="fa-solid <?php echo $icon; ?>"></i>
                            <?php echo $is_abnormal ? 'Abnormal Morphology' : 'No Significant Abnormalities'; ?>
                        </div>

                        <div class="w-full text-left bg-gray-50 p-4 rounded-2xl border border-gray-100">
                            <p class="text-[10px] font-bold text-gray-400 uppercase mb-1">Pathologist Note</p>
                            <p class="text-sm italic text-gray-600 leading-relaxed">
                                "<?php echo !empty($result_data['findings']) ? htmlspecialchars($result_data['findings']) : 'No clinical notes provided.'; ?>"
                            </p>
                        </div>

                        <div class="mt-8 flex gap-3 w-full">
                            <a href="report.php"
                            class="flex-1 bg-brand-dark text-white py-3 rounded-xl font-bold text-sm hover:opacity-90 transition flex items-center justify-center">
                                <i class="fa-solid fa-print mr-1"></i>
                                View Report
                            </a>
                            <button class="flex-1 bg-white border border-gray-200 py-3 rounded-xl font-bold text-sm hover:bg-gray-50 transition flex items-center justify-center">
                                <i class="fa-solid fa-share-nodes mr-1"></i>
                                Share
                            </button>
                        </div>

                    <?php elseif(!empty($pre_selected_id)): ?>
                        <!-- Patient Selected but Not Yet Analyzed -->
                        <div class="mb-4">
                            <span class="bg-blue-50 text-blue-600 px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-widest">Awaiting Analysis</span>
                        </div>
                        
                        <div class="mb-8">
                            <p class="text-[10px] font-bold text-gray-400 uppercase">Selected Patient</p>
                            <h4 class="text-xl font-bold text-brand-dark"><?php echo htmlspecialchars($selected_patient_name); ?></h4>
                            <p class="text-xs font-mono text-brand-pink"><?php echo htmlspecialchars($pre_selected_id); ?></p>
                        </div>

                        <div class="bg-brand-light p-8 rounded-full mb-6">
                            <i class="fa-solid fa-file-arrow-up text-5xl text-brand-pink"></i>
                        </div>
                        
                        <p class="text-sm text-gray-500 max-w-[240px] leading-relaxed italic">
                            Record loaded. Please upload the Pap smear slide for <strong><?php echo explode(' ', $selected_patient_name)[0]; ?></strong> to initiate the AI screening.
                        </p>

                    <?php else: ?>
                        <!-- Idle View -->
                        <div class="opacity-20 flex flex-col items-center">
                            <i class="fa-solid fa-microscope text-8xl text-brand-pink mb-4"></i>
                            <h3 class="text-xl font-bold">Awaiting Input</h3>
                            <p class="text-xs max-w-[200px] mt-2 leading-relaxed">Patient details and AI predictions will appear here once the record is selected.</p>
                        </div>
                    <?php endif; ?>

                    <!-- Decorative elements -->
                    <div class="absolute bottom-0 right-0 w-32 h-32 bg-brand-pink/5 rounded-full -mr-16 -mb-16"></div>
                </div>

                <!-- Pink Medical Notice -->
                <div class="bg-pink-300/30 border-l-4 border-brand-pink p-6 rounded-3xl relative overflow-hidden">
                    <div class="relative z-10">
                        <div class="flex items-center gap-2 mb-2 text-brand-pink">
                            <i class="fa-solid fa-circle-exclamation text-lg"></i>
                            <h4 class="font-bold text-sm uppercase tracking-wider">Clinical Integrity</h4>
                        </div>
                        <p class="text-[11px] leading-relaxed text-gray-700">
                            Results are linked to specific Patient IDs for longitudinal tracking. Ensure the uploaded cell image corresponds exactly to the selected patient profile.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        const fileInput = document.getElementById('file-input');
        const preview = document.getElementById('image-preview');
        const previewContainer = document.getElementById('preview-container');
        const removeImg = document.getElementById('remove-img');
        const placeholder = document.getElementById('upload-placeholder');
        const form = document.querySelector('form');
        const submitBtn = document.getElementById('submit-btn');
        const btnText = document.getElementById('btn-text');

        fileInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    previewContainer.classList.remove('hidden');
                    placeholder.classList.add('hidden');
                }
                reader.readAsDataURL(file);
            }
        });

        removeImg.addEventListener('click', (e) => {
            e.stopPropagation();
            fileInput.value = "";
            preview.src = "#";
            previewContainer.classList.add('hidden');
            placeholder.classList.remove('hidden');
        });

        form.addEventListener('submit', (e) => {
            // Only show loader if we are actually POSTing (not on select change)
            if (form.method.toUpperCase() === 'POST') {
                submitBtn.classList.add('opacity-80', 'cursor-not-allowed');
                submitBtn.disabled = true;
                btnText.innerHTML = '<i class="fa-solid fa-circle-notch animate-spin"></i> Analyzing...';
            }
        });
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
