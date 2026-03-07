<footer class="bg-gray-900 text-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <!-- Brand -->
            <div>
                <h3 class="text-xl font-bold text-white">{{ config('site.app.name') }}</h3>
                <p class="mt-2 text-sm text-gray-400">{{ config('site.footer.description') }}</p>
            </div>

            <!-- Quick Links -->
            <div>
                <h4 class="text-sm font-semibold text-white uppercase tracking-wide">Quick Links</h4>
                <ul class="mt-3 space-y-2">
                    @foreach(config('site.footer.quick_links') as $link)
                    <li><a href="{{ url($link['url']) }}" class="text-sm hover:text-white transition-colors">{{ $link['label'] }}</a></li>
                    @endforeach
                </ul>
            </div>

            <!-- Legal -->
            <div>
                <h4 class="text-sm font-semibold text-white uppercase tracking-wide">Legal</h4>
                <ul class="mt-3 space-y-2">
                    @foreach(config('site.footer.legal_links') as $link)
                    <li><a href="{{ url($link['url']) }}" class="text-sm hover:text-white transition-colors">{{ $link['label'] }}</a></li>
                    @endforeach
                </ul>
            </div>

            <!-- Other -->
            <div>
                <h4 class="text-sm font-semibold text-white uppercase tracking-wide">More</h4>
                <ul class="mt-3 space-y-2">
                    @foreach(config('site.footer.other_links') as $link)
                    <li><a href="{{ url($link['url']) }}" class="text-sm hover:text-white transition-colors">{{ $link['label'] }}</a></li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="mt-8 pt-8 border-t border-gray-800 text-center text-sm text-gray-500">
            &copy; {{ date('Y') }} {{ config('site.app.name') }}. All rights reserved.
        </div>
    </div>
</footer>
