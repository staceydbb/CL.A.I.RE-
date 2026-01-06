<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CLA.I.RE - AI Cervical Cancer Screening</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
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
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-brand-light text-slate-800">

    <!-- Navbar -->
    <nav class="bg-white py-4 px-6 md:px-12 flex justify-between items-center shadow-sm">
        <div class="flex items-center gap-2">
            <div class="bg-brand-pink text-white p-1.5 rounded-md flex items-center justify-center w-8 h-8">
                <i class="fa-solid fa-microscope text-sm"></i>
            </div>
            <span class="font-bold text-xl tracking-wide text-brand-pink">CLA.I.RE</span>
        </div>
        <!-- Redirect to login.php -->
        <a href="login.php" class="bg-brand-pink hover:bg-pink-700 text-white font-semibold py-2 px-6 rounded-md transition duration-300 no-underline">
            Log in
        </a>
    </nav>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-12 md:py-16 max-w-6xl">
        
        <!-- Hero Section -->
        <div class="text-center flex flex-col items-center mb-16">
            <!-- Brain Icon -->
            <div class="bg-brand-pink text-white p-3 rounded-lg mb-6 shadow-lg inline-flex">
                <i class="fa-solid fa-brain text-4xl"></i>
            </div>

            <!-- Headline -->
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold mb-4 text-slate-900 leading-tight">
                AI-Powered <span class="text-brand-pink">Cervical Cancer</span>
                <br>
                <span class="text-slate-900">Screening Assistant</span>
            </h1>

            <!-- Subtitle -->
            <p class="text-brand-pink/90 font-medium text-lg md:text-xl max-w-3xl mx-auto mb-10 leading-relaxed">
                An AI Technology to assist healthcare workers in early detection of cervical cancer by analyzing Pap smear cell images. Designed for resource-limited healthcare settings.
            </p>

          <div class="flex flex-col sm:flex-row gap-4 justify-center w-full sm:w-auto">

                <!-- Get Started Button (linked to sign-up.php) -->
                <a href="sign-up.php"
                   class="bg-brand-pink hover:bg-pink-700 text-white font-bold py-3 px-8 rounded-lg shadow-md 
                          transition transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                    Get Started 
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
                <button class="bg-white border-2 border-brand-pink text-brand-pink hover:bg-pink-50 font-bold py-3 px-8 rounded-lg shadow-sm transition transform hover:-translate-y-0.5">
                    Learn More
                </button>
            </div>
        </div>

    
        <!-- Features Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-20">
            
            <!-- Card 1 -->
            <div class="bg-white p-6 rounded-2xl border border-pink-200 shadow-sm hover:shadow-md transition duration-300">
                <div class="bg-brand-pink text-white w-12 h-12 rounded-lg flex items-center justify-center mb-4 text-xl">
                    <i class="fa-solid fa-microscope"></i>
                </div>
                <h3 class="text-brand-pink font-bold text-lg mb-2">Advanced AI Analysis</h3>
                <p class="text-gray-600 text-sm leading-relaxed">
                    Classify Pap smear cells into Normal, Dyskeratotic, Koilocytotic, Metaplastic, and Parabasal categories with high accuracy.
                </p>
            </div>

            <!-- Card 2 -->
            <div class="bg-white p-6 rounded-2xl border border-pink-200 shadow-sm hover:shadow-md transition duration-300">
                <div class="bg-green-400 text-white w-12 h-12 rounded-lg flex items-center justify-center mb-4 text-xl">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <h3 class="text-brand-pink font-bold text-lg mb-2">Healthcare Assistant</h3>
                <p class="text-gray-600 text-sm leading-relaxed">
                    Designed to assist healthcare workers, not replace professional medical diagnosis and clinical judgment.
                </p>
            </div>

            <!-- Card 3 -->
            <div class="bg-white p-6 rounded-2xl border border-pink-200 shadow-sm hover:shadow-md transition duration-300">
                <div class="bg-indigo-500 text-white w-12 h-12 rounded-lg flex items-center justify-center mb-4 text-xl">
                    <i class="fa-solid fa-user-doctor"></i>
                </div>
                <h3 class="text-brand-pink font-bold text-lg mb-2">For Healthcare Workers</h3>
                <p class="text-gray-600 text-sm leading-relaxed">
                    Specifically designed for medical professionals, nurses, and healthcare workers in clinical settings.
                </p>
            </div>

            <!-- Card 4 -->
            <div class="bg-white p-6 rounded-2xl border border-pink-200 shadow-sm hover:shadow-md transition duration-300">
                <div class="bg-orange-400 text-white w-12 h-12 rounded-lg flex items-center justify-center mb-4 text-xl">
                    <i class="fa-solid fa-earth-americas"></i>
                </div>
                <h3 class="text-brand-pink font-bold text-lg mb-2">Global Healthcare</h3>
                <p class="text-gray-600 text-sm leading-relaxed">
                    Especially valuable in resource-limited settings where advanced diagnostic tools may not be readily available.
                </p>
            </div>
        </div>

        <!-- Medical Notice -->
        <div class="bg-pink-300/40 rounded-lg p-6 md:p-8 border-l-8 border-brand-pink relative overflow-hidden">
            
            <div class="absolute top-0 right-0 w-32 h-32 bg-white opacity-10 rounded-full -mr-16 -mt-16 blur-2xl"></div>

            <h3 class="text-brand-pink font-extrabold text-xl mb-3">Important Medical Notice</h3>
            <p class="text-gray-800 text-sm md:text-base leading-relaxed">
                This AI analysis is an assistive tool for qualified healthcare professionals only. Results should be interpreted within clinical context and should not replace professional medical diagnosis, pathologist review, or clinical judgment. Always consult with qualified medical professionals for final diagnosis and treatment decisions.
            </p>
        </div>

    </main>

</body>
</html>
