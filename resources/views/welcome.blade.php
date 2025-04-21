<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $settings->title }} - Creative Solutions</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gradient-to-br from-purple-50 to-indigo-50">
    <!-- Navigation -->
    <nav class="fixed w-full bg-white/80 backdrop-blur-md shadow-sm z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex-shrink-0">
                    <span class="text-2xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">
                        {{ $settings->name }}
                    </span>
                </div>
                <div class="hidden md:flex space-x-8">
                    <a href="#features" class="text-gray-600 hover:text-purple-600 transition">Features</a>
                    {{-- <a href="#" class="text-gray-600 hover:text-purple-600 transition">Solutions</a> --}}
                    {{-- <a href="#" class="text-gray-600 hover:text-purple-600 transition">Pricing</a> --}}
                    <a href="#about" class="text-gray-600 hover:text-purple-600 transition">About</a>
                </div>
                <a href="https://jadesdev.com.ng"
                    class="hidden md:block bg-gradient-to-r from-purple-600 to-pink-600 text-white px-6 py-2 rounded-full hover:shadow-lg transition">
                    Get Started
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="pt-32 pb-24 px-4">
        <div class="max-w-7xl mx-auto text-center">
            <h1 class="text-5xl md:text-6xl font-bold mb-8 bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">
                Transform Your Digital Presence
            </h1>
            <p class="text-xl text-gray-600 mb-12 max-w-2xl mx-auto">
                Create stunning digital experiences with our all-in-one platform. Empower your business with cutting-edge solutions.
            </p>
            <div class="flex justify-center space-x-4">
                <a href="https://jadesdev.com.ng"
                    class="bg-gradient-to-r from-purple-600 to-pink-600 text-white px-8 py-4 rounded-full text-lg hover:shadow-xl transition">
                    Start Free Trial
                </a>
                {{-- <button class="border-2 border-purple-600 text-purple-600 px-8 py-4 rounded-full text-lg hover:bg-purple-50 transition">
                    Watch Demo
                </button> --}}
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-20 bg-white" id="features">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Amazing Features</h2>
                <p class="text-gray-600 max-w-xl mx-auto">Everything you need to build modern digital experiences</p>
            </div>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="p-6 bg-white rounded-xl shadow-lg hover:shadow-xl transition">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-rocket text-purple-600 text-xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Lightning Fast</h3>
                    <p class="text-gray-600">Optimized performance for seamless user experiences</p>
                </div>
                <div class="p-6 bg-white rounded-xl shadow-lg hover:shadow-xl transition">
                    <div class="w-12 h-12 bg-pink-100 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-paint-brush text-pink-600 text-xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Beautiful Design</h3>
                    <p class="text-gray-600">Modern and intuitive interface out of the box</p>
                </div>
                <div class="p-6 bg-white rounded-xl shadow-lg hover:shadow-xl transition">
                    <div class="w-12 h-12 bg-teal-100 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-shield-alt text-teal-600 text-xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Secure Platform</h3>
                    <p class="text-gray-600">Enterprise-grade security for your peace of mind</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section class="py-20" id="pricing" hidden>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Simple Pricing</h2>
                <p class="text-gray-600 max-w-xl mx-auto">Choose the plan that works best for your business</p>
            </div>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="p-8 bg-white rounded-2xl shadow-lg border border-purple-100">
                    <h3 class="text-xl font-bold mb-4">Starter</h3>
                    <div class="text-4xl font-bold mb-6">$29<span class="text-lg text-gray-500">/mo</span></div>
                    <ul class="space-y-4 mb-8">
                        <li class="flex items-center"><i class="fas fa-check text-purple-600 mr-2"></i>Basic Features</li>
                        <li class="flex items-center"><i class="fas fa-check text-purple-600 mr-2"></i>5 Projects</li>
                        <li class="flex items-center"><i class="fas fa-check text-purple-600 mr-2"></i>3 Team Members</li>
                    </ul>
                    <button class="w-full py-3 text-purple-600 border border-purple-600 rounded-lg hover:bg-purple-50 transition">
                        Get Started
                    </button>
                </div>
                <div class="p-8 bg-purple-600 text-white rounded-2xl shadow-lg transform md:scale-105 relative">
                    <div class="absolute top-0 right-0 bg-pink-500 text-white px-4 py-1 rounded-bl-lg text-sm">Popular</div>
                    <h3 class="text-xl font-bold mb-4">Professional</h3>
                    <div class="text-4xl font-bold mb-6">$99<span class="text-lg opacity-75">/mo</span></div>
                    <ul class="space-y-4 mb-8">
                        <li class="flex items-center"><i class="fas fa-check mr-2"></i>Advanced Features</li>
                        <li class="flex items-center"><i class="fas fa-check mr-2"></i>Unlimited Projects</li>
                        <li class="flex items-center"><i class="fas fa-check mr-2"></i>10 Team Members</li>
                    </ul>
                    <button class="w-full py-3 bg-white text-purple-600 rounded-lg hover:bg-opacity-90 transition">
                        Get Started
                    </button>
                </div>
                <div class="p-8 bg-white rounded-2xl shadow-lg border border-purple-100">
                    <h3 class="text-xl font-bold mb-4">Enterprise</h3>
                    <div class="text-4xl font-bold mb-6">$299<span class="text-lg text-gray-500">/mo</span></div>
                    <ul class="space-y-4 mb-8">
                        <li class="flex items-center"><i class="fas fa-check text-purple-600 mr-2"></i>Premium Features</li>
                        <li class="flex items-center"><i class="fas fa-check text-purple-600 mr-2"></i>Unlimited Everything</li>
                        <li class="flex items-center"><i class="fas fa-check text-purple-600 mr-2"></i>Priority Support</li>
                    </ul>
                    <button class="w-full py-3 text-purple-600 border border-purple-600 rounded-lg hover:bg-purple-50 transition">
                        Contact Sales
                    </button>
                </div>
            </div>
        </div>
    </section>
    <!-- About Section -->
<section class="py-20 bg-white" id='about'>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Who We Are</h2>
            <p class="text-gray-600 max-w-xl mx-auto">A team of passionate innovators creating digital excellence</p>
        </div>

        <!-- Mission Statement -->
        <div class="grid grid-cols-1 gap-12 items-center mb-20">

            <div>
                <h3 class="text-2xl font-bold text-gray-900 mb-6">Our Mission</h3>
                <p class="text-gray-600 mb-6">
                    We exist to empower businesses through innovative digital solutions. Our mission is to
                    bridge the gap between technology and creativity, helping organizations thrive in the
                    digital age.
                </p>
                <div class="grid grid-cols-2 gap-4">
                    <div class="p-4 bg-purple-50 rounded-lg">
                        <div class="text-purple-600 text-2xl mb-2">
                            <i class="fas fa-bullseye"></i>
                        </div>
                        <h4 class="font-semibold mb-2">Clear Vision</h4>
                        <p class="text-sm text-gray-600">Strategic digital transformation roadmap</p>
                    </div>
                    <div class="p-4 bg-pink-50 rounded-lg">
                        <div class="text-pink-600 text-2xl mb-2">
                            <i class="fas fa-hand-holding-heart"></i>
                        </div>
                        <h4 class="font-semibold mb-2">Core Values</h4>
                        <p class="text-sm text-gray-600">Integrity, innovation, and customer success</p>
                    </div>
                </div>
            </div>
        </div>


        <!-- Stats -->
        <div class="grid md:grid-cols-3 gap-8 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-2xl p-8 shadow-xl">
            <div class="text-center p-4">
                <div class="text-4xl font-bold mb-2">150+</div>
                <div class="text-sm">Successful Projects</div>
            </div>
            <div class="text-center p-4 border-x border-white/20">
                <div class="text-4xl font-bold mb-2">98%</div>
                <div class="text-sm">Client Satisfaction</div>
            </div>
            <div class="text-center p-4">
                <div class="text-4xl font-bold mb-2">10+</div>
                <div class="text-sm">Years Experience</div>
            </div>
        </div>


    </div>
</section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-300 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-8">
                <div>
                    <h4 class="text-white font-bold mb-4">{{ $settings->name }}</h4>
                    <p class="text-sm">Making digital transformation accessible for everyone</p>
                </div>
                <div>
                    <h5 class="text-white font-semibold mb-4">Product</h5>
                    <ul class="space-y-2 text-sm">
                        <li><a href="https://jadesdev.com.ng" class="hover:text-white transition">Features</a></li>
                        <li><a href="https://jadesdev.com.ng" class="hover:text-white transition">Pricing</a></li>
                    </ul>
                </div>
                <div>
                    <h5 class="text-white font-semibold mb-4">Company</h5>
                    <ul class="space-y-2 text-sm">
                        <li><a href="https://jadesdev.com.ng" class="hover:text-white transition">About</a></li>
                        <li><a href="https://jadesdev.com.ng" class="hover:text-white transition">Blog</a></li>
                    </ul>
                </div>
                <div>
                    <h5 class="text-white font-semibold mb-4">Stay Connected</h5>
                    <div class="flex space-x-4">
                        <a href="#" class="hover:text-white transition"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="hover:text-white transition"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="hover:text-white transition"><i class="fab fa-linkedin"></i></a>
                        <a href="#" class="hover:text-white transition"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-12 pt-8 text-center text-sm">
                <p>&copy; {{ date('Y') }} {{ $settings->title }}. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>

</html>
