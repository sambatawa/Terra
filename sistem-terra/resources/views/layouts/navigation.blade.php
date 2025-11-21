<nav x-data="{ open: false }" class="bg-purple-900 border-b border-purple-800 sticky top-0 z-50 shadow-lg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            
            <div class="flex">
                <div class="shrink-0 flex items-center gap-3">
                    <a href="{{ route('dashboard') }}">
                        <img src="{{ asset('img/logo.png') }}" class="block h-10 w-auto brightness-0 invert" alt="Terra Logo" />
                    </a>
                    <span class="hidden md:block font-extrabold text-2xl text-white tracking-tight">Terra.</span>
                </div>

                <div class="hidden space-x-2 sm:-my-px sm:ml-8 sm:flex items-center">
                    
                    <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded-md text-base font-bold transition {{ request()->routeIs('dashboard') ? 'bg-purple-800 text-white' : 'text-purple-200 hover:bg-purple-800 hover:text-white' }}">
                        Dashboard
                    </a>

                    @if(Auth::user()->role == 'petani')
                        <a href="{{ route('robot') }}" class="px-4 py-2 rounded-md text-base font-bold transition {{ request()->routeIs('robot') ? 'bg-purple-800 text-white' : 'text-purple-200 hover:bg-purple-800 hover:text-white' }}">
                            Robot AI
                        </a>
                        <a href="{{ route('sensor') }}" class="px-4 py-2 rounded-md text-base font-bold transition {{ request()->routeIs('sensor') ? 'bg-purple-800 text-white' : 'text-purple-200 hover:bg-purple-800 hover:text-white' }}">
                            Sensor IoT
                        </a>
                        
                        <a href="{{ route('forum') }}" class="px-4 py-2 rounded-md text-base font-bold transition {{ request()->routeIs('forum') ? 'bg-purple-800 text-white' : 'text-purple-200 hover:bg-purple-800 hover:text-white' }}">
                            Forum
                        </a>

                        <a href="{{ route('marketplace') }}" class="px-4 py-2 rounded-md text-base font-bold transition {{ request()->routeIs('marketplace') ? 'bg-purple-800 text-white' : 'text-purple-200 hover:bg-purple-800 hover:text-white' }}">
                            Marketplace
                        </a>

                        <a href="{{ route('history') }}" class="px-4 py-2 rounded-md text-base font-bold transition {{ request()->routeIs('history') ? 'bg-purple-800 text-white' : 'text-purple-200 hover:bg-purple-800 hover:text-white' }}">
                            Riwayat
                        </a>
                        <a href="{{ route('reports.index') }}" class="px-4 py-2 rounded-md text-base font-bold transition {{ request()->routeIs('reports.index') ? 'bg-red-600 text-white' : 'text-red-200 hover:bg-red-700 hover:text-white' }}">
                            Lapor Masalah
                        </a>
                    @endif

                    @if(Auth::user()->role == 'penjual')
                        <a href="{{ route('marketplace') }}" class="px-4 py-2 rounded-md text-base font-bold transition {{ request()->routeIs('marketplace') ? 'bg-purple-800 text-white' : 'text-purple-200 hover:bg-purple-800 hover:text-white' }}">
                            Kelola Produk
                        </a>
                        <a href="{{ route('history') }}" class="px-4 py-2 rounded-md text-base font-bold transition {{ request()->routeIs('history') ? 'bg-purple-800 text-white' : 'text-purple-200 hover:bg-purple-800 hover:text-white' }}">
                            Statistik
                        </a>
                        <a href="{{ route('forum') }}" class="px-4 py-2 rounded-md text-base font-bold transition {{ request()->routeIs('forum') ? 'bg-purple-800 text-white' : 'text-purple-200 hover:bg-purple-800 hover:text-white' }}">
                            Forum
                        </a>
                    @endif

                    @if(Auth::user()->role == 'teknisi')
                        <a href="{{ route('reports.index') }}" class="relative px-4 py-2 rounded-md text-base font-bold transition {{ request()->routeIs('reports.index') ? 'bg-purple-800 text-white' : 'text-purple-200 hover:bg-purple-800 hover:text-white' }}">
                            Tiket Masuk
                            <span class="absolute top-1 right-1 h-2.5 w-2.5 rounded-full bg-red-500 animate-ping"></span>
                        </a>
                        <a href="{{ route('admin.users') }}" class="px-4 py-2 rounded-md text-base font-bold transition {{ request()->routeIs('admin.users') ? 'bg-purple-800 text-white' : 'text-purple-200 hover:bg-purple-800 hover:text-white' }}">
                            Manajemen User
                        </a>
                        <a href="{{ route('forum') }}" class="px-4 py-2 rounded-md text-base font-bold transition {{ request()->routeIs('forum') ? 'bg-purple-800 text-white' : 'text-purple-200 hover:bg-purple-800 hover:text-white' }}">
                            Forum
                        </a>
                    @endif

                    @if(Auth::user()->role == 'penyuluh')
                        <a href="{{ route('forum') }}" class="px-4 py-2 rounded-md text-base font-bold transition {{ request()->routeIs('forum') ? 'bg-purple-800 text-white' : 'text-purple-200 hover:bg-purple-800 hover:text-white' }}">
                            Forum
                        </a>
                        <a href="{{ route('history') }}" class="px-4 py-2 rounded-md text-base font-bold transition {{ request()->routeIs('history') ? 'bg-purple-800 text-white' : 'text-purple-200 hover:bg-purple-800 hover:text-white' }}">
                            Aktivitas
                        </a>
                    @endif

                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ml-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-4 py-2 border border-transparent text-sm leading-4 font-medium rounded-full text-purple-100 hover:bg-purple-800 focus:outline-none transition ease-in-out duration-150 gap-3">
                            
                            <div class="h-9 w-9 rounded-full bg-white flex items-center justify-center text-purple-900 font-extrabold text-sm shadow-sm">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>

                            <div class="text-left hidden lg:block">
                                <div class="font-bold text-base">{{ Auth::user()->name }}</div>
                                <div class="text-[10px] uppercase font-bold tracking-wider text-purple-300">{{ Auth::user()->role }}</div>
                            </div>

                            <div class="ml-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();" class="text-red-600 hover:bg-red-50">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-purple-200 hover:text-white hover:bg-purple-800 focus:outline-none transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-purple-800 border-b border-purple-700">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="text-white hover:bg-purple-700 font-bold">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            @if(Auth::user()->role == 'petani')
                <x-responsive-nav-link :href="route('robot')" :active="request()->routeIs('robot')" class="text-white hover:bg-purple-700 font-bold">
                    {{ __('Robot AI') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('sensor')" :active="request()->routeIs('sensor')" class="text-white hover:bg-purple-700 font-bold">
                    {{ __('Sensor IoT') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('forum')" :active="request()->routeIs('forum')" class="text-white hover:bg-purple-700 font-bold">
                    {{ __('Forum') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('marketplace')" :active="request()->routeIs('marketplace')" class="text-white hover:bg-purple-700 font-bold">
                    {{ __('Marketplace') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('history')" :active="request()->routeIs('history')" class="text-white hover:bg-purple-700 font-bold">
                    {{ __('Riwayat') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('reports.index')" :active="request()->routeIs('reports.index')" class="text-red-300 hover:bg-red-900 font-bold">
                    {{ __('Lapor Masalah') }}
                </x-responsive-nav-link>
            @endif

            @if(Auth::user()->role == 'teknisi')
                <x-responsive-nav-link :href="route('reports.index')" :active="request()->routeIs('reports.index')" class="text-white hover:bg-purple-700 font-bold">
                    {{ __('Tiket Masuk') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.users')" :active="request()->routeIs('admin.users')" class="text-white hover:bg-purple-700 font-bold">
                    {{ __('Manajemen User') }}
                </x-responsive-nav-link>
            @endif
        </div>

        <div class="pt-4 pb-1 border-t border-purple-700 bg-purple-900">
            <div class="px-4 flex items-center gap-3">
                <div class="h-10 w-10 rounded-full bg-white flex items-center justify-center text-purple-900 font-bold text-lg">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div>
                    <div class="font-bold text-base text-white">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-purple-300">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')" class="text-purple-200 hover:text-white hover:bg-purple-800">
                    {{ __('Profile') }}
                </x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();" class="text-red-300 hover:text-red-100 hover:bg-red-900">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>