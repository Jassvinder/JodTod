<header class="sticky top-0 z-50 bg-white border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <!-- Logo -->
            <a href="/" class="text-xl font-bold text-primary-600">JodTod</a>

            <!-- Desktop Navigation -->
            <nav class="hidden md:flex items-center gap-6">
                <a href="/" class="text-gray-600 hover:text-gray-900 transition-colors">Home</a>
                <a href="/features" class="text-gray-600 hover:text-gray-900 transition-colors">Features</a>
                <a href="/blog" class="text-gray-600 hover:text-gray-900 transition-colors">Blog</a>
                <a href="/tools/expense-splitter" class="text-gray-600 hover:text-gray-900 transition-colors">Free Tools</a>
                <a href="/login" class="px-4 py-2 rounded-lg bg-primary-600 text-white hover:bg-primary-700 transition-colors">Login</a>
            </nav>

            <!-- Mobile Menu Button -->
            <button id="mobile-menu-btn" class="md:hidden p-2 rounded-lg text-gray-500 hover:bg-gray-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>

        <!-- Mobile Navigation -->
        <div id="mobile-menu" class="hidden md:hidden pb-4">
            <nav class="flex flex-col gap-2">
                <a href="/" class="px-4 py-2 rounded-lg text-gray-600 hover:bg-gray-100">Home</a>
                <a href="/features" class="px-4 py-2 rounded-lg text-gray-600 hover:bg-gray-100">Features</a>
                <a href="/blog" class="px-4 py-2 rounded-lg text-gray-600 hover:bg-gray-100">Blog</a>
                <a href="/tools/expense-splitter" class="px-4 py-2 rounded-lg text-gray-600 hover:bg-gray-100">Free Tools</a>
                <a href="/login" class="px-4 py-2 rounded-lg bg-primary-600 text-white text-center hover:bg-primary-700">Login</a>
            </nav>
        </div>
    </div>
</header>

<script>
    document.getElementById('mobile-menu-btn')?.addEventListener('click', function() {
        document.getElementById('mobile-menu')?.classList.toggle('hidden');
    });
</script>
