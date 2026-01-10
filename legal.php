<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Legal - CLA.I.RE System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-50 text-gray-700 font-sans">

    <main class="max-w-4xl mx-auto px-6 py-12">
        <!-- Title in brand pink -->
        <h1 class="text-2xl font-bold text-[#d6006e] mb-6">Legal Notice</h1>

        <section class="text-sm leading-relaxed text-gray-600 space-y-6">
            <p>
                This Legal Notice governs the use of the CLA.I.RE System. By accessing or using the system, 
                you acknowledge and agree to the following provisions.
            </p>

            <div class="space-y-2">
                <h2 class="text-lg font-semibold text-[#d6006e]">1. Disclaimer of Warranties</h2>
                <p>
                    The CLA.I.RE System is provided on an “as is” basis without warranties of any kind, 
                    whether express or implied. While efforts are made to ensure accuracy, the system does 
                    not guarantee error-free operation or uninterrupted availability.
                </p>
            </div>

            <div class="space-y-2">
                <h2 class="text-lg font-semibold text-[#d6006e]">2. Limitation of Liability</h2>
                <p>
                    Neither the developers nor affiliated institutions shall be held liable for any direct, 
                    indirect, incidental, or consequential damages arising from the use of the system. 
                    Clinical decisions remain the sole responsibility of licensed healthcare providers.
                </p>
            </div>

            <div class="space-y-2">
                <h2 class="text-lg font-semibold text-[#d6006e]">3. Intellectual Property</h2>
                <p>
                    All software, AI models, design elements, and branding associated with CLA.I.RE are the 
                    intellectual property of the system developers. Unauthorized reproduction, distribution, 
                    or modification is prohibited.
                </p>
            </div>

            <div class="space-y-2">
                <h2 class="text-lg font-semibold text-[#d6006e]">4. Governing Law</h2>
                <p>
                    This Legal Notice shall be governed by and construed in accordance with the laws of the 
                    applicable jurisdiction where the system is deployed. Any disputes shall be subject to 
                    the exclusive jurisdiction of the competent courts.
                </p>
            </div>

            <div class="space-y-2">
                <h2 class="text-lg font-semibold text-[#d6006e]">5. Compliance</h2>
                <p>
                    The CLA.I.RE System is designed to align with healthcare data protection and confidentiality 
                    standards. However, it does not replace professional medical judgment or institutional 
                    compliance obligations.
                </p>
            </div>
        </section>
    </main>

    <footer class="pt-4 pb-5 text-center text-gray-400 text-xs">
        <div class="flex justify-center items-center gap-1 flex-wrap text-gray-400 font-medium">
            <a href="privacy.php" target="_blank" rel="noopener noreferrer" class="hover:text-[#d6006e] transition">Privacy Policy</a>
            <span>&bull;</span>
            <a href="terms.php" target="_blank" rel="noopener noreferrer" class="hover:text-[#d6006e] transition">Terms of Use</a>
            <span>&bull;</span>
            <a href="legal.php" target="_blank" rel="noopener noreferrer" class="hover:text-[#d6006e] transition">Legal</a>
            <span>&bull;</span>
            <a href="developers.php" target="_blank" rel="noopener noreferrer" class="hover:text-[#d6006e] transition">About the Developers</a>
        </div>
        <div class="mt-1 text-gray-400 font-normal">
            &copy; <?php echo date('Y'); ?> CLA.I.RE System &bull; All rights Reserved
        </div>
    </footer>

</body>
</html>
