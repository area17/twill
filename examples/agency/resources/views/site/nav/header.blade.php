<nav class="bg-slate-100">
    <div class="max-w-7xl mx-auto px-2 tablet-v:px-6 laptop:px-8">
        <div class="relative flex items-center justify-between h-16">
            <div class="absolute inset-y-0 left-0 flex items-center tablet-v:hidden">
                <button type="button" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white" aria-controls="mobile-menu" aria-expanded="false">
                    <span class="sr-only">Open main menu</span>
                    <svg class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg class="hidden h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="flex-1 flex items-center justify-center tablet-v:items-stretch tablet-v:justify-between">
                <div class="flex-shrink-0 flex items-center">
                    <h2 class="text-4xl font-bold">Twill</h2>
                </div>
                <div class="hidden tablet-v:block tablet-v:ml-6">
                    <div class="flex space-x-4">
                        <!-- Current: "bg-gray-900 text-white", Default: "text-gray-300 hover:bg-gray-700 hover:text-white" -->
                        <a href="#" class="{{ Request::segment(2) === 'work' ? "bg-gray-900 text-white" : "text-gray-900 hover:bg-gray-700 hover:text-white" }} px-3 py-2 rounded-md text-sm font-medium" aria-current="page">Work</a>

                        <a href="#" class="{{ Request::segment(2) === 'about' ? "bg-gray-900 text-white" : "text-gray-900 hover:bg-gray-700 hover:text-white" }} px-3 py-2 rounded-md text-sm font-medium">About</a>

                        <a href="#" class="{{ Request::segment(2) === 'news' ? "bg-gray-900 text-white" : "text-gray-900 hover:bg-gray-700 hover:text-white" }} px-3 py-2 rounded-md text-sm font-medium">News</a>

                        <a href="#" class="{{ Request::segment(2) === 'contact' ? "bg-gray-900 text-white" : "text-gray-900 hover:bg-gray-700 hover:text-white" }} px-3 py-2 rounded-md text-sm font-medium">Contact</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>
