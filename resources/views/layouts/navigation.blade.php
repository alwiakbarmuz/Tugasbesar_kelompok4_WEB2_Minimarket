<nav x-data="{ open: false, openAudit: false, openReport: false }" class="bg-gradient-to-r from-blue-800 to-indigo-900 shadow-lg sticky top-0 z-20">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 group">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                        <div class="hidden md:block">
                            <span class="text-white font-bold text-lg">Minimarket</span>
                            <span class="text-white/80 text-xs block -mt-1">Jayusman</span>
                        </div>
                    </a>
                </div>

                <!-- Navigation Links - Desktop -->
                <div class="hidden space-x-1 sm:-my-px sm:ms-10 sm:flex sm:items-center">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="text-white/90 hover:text-white hover:bg-white/10 rounded-lg">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    @can('view branches')
                    <x-nav-link :href="route('branches.index')" :active="request()->routeIs('branches.*')" class="text-white/90 hover:text-white hover:bg-white/10 rounded-lg">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        {{ __('Cabang') }}
                    </x-nav-link>
                    @endcan

                    @can('view products')
                    <x-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')" class="text-white/90 hover:text-white hover:bg-white/10 rounded-lg">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        {{ __('Produk') }}
                    </x-nav-link>
                    @endcan

                    @can('view transactions')
                    <x-nav-link :href="route('transactions.index')" :active="request()->routeIs('transactions.*')" class="text-white/90 hover:text-white hover:bg-white/10 rounded-lg">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-1.5 6M17 13l1.5 6M9 21h6M4 3h16"></path>
                        </svg>
                        {{ __('Transaksi') }}
                    </x-nav-link>
                    @endcan

                    <!-- Audit Dropdown (Owner only) -->
                    @role('owner')
                    <div class="relative" x-data="{ openAudit: false }">
                        <button @click="openAudit = !openAudit" 
                                class="inline-flex items-center px-3 py-2 text-sm font-medium leading-5 text-white/90 hover:text-white hover:bg-white/10 focus:outline-none transition duration-150 ease-in-out rounded-lg">
                            <i class="fas fa-clipboard-list mr-1"></i>
                            Audit
                            <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        
                        <div x-show="openAudit" @click.away="openAudit = false" 
                             class="absolute left-0 mt-2 w-56 bg-white rounded-lg shadow-lg py-1 z-50"
                             style="display: none;">
                            <a href="{{ route('audit.transactions') }}" 
                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-receipt mr-2 text-red-500 w-4"></i> Transaksi Dibatalkan
                            </a>
                            <a href="{{ route('audit.products') }}" 
                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-box-open mr-2 text-orange-500 w-4"></i> Produk Terhapus
                            </a>
                        </div>
                    </div>
                    @endrole

                    <!-- Reports Dropdown -->
                    @can('view reports')
                    <div class="relative" x-data="{ openReport: false }">
                        <button @click="openReport = !openReport" 
                                class="inline-flex items-center px-3 py-2 text-sm font-medium leading-5 text-white/90 hover:text-white hover:bg-white/10 focus:outline-none transition duration-150 ease-in-out rounded-lg">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            Laporan
                            <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        
                        <div x-show="openReport" @click.away="openReport = false" 
                             class="absolute left-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-1 z-50"
                             style="display: none;">
                            <a href="{{ route('reports.daily') }}" 
                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition">
                                <i class="fas fa-calendar-day mr-2 text-blue-500"></i>
                                Laporan Harian
                            </a>
                            <a href="{{ route('reports.monthly') }}" 
                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition">
                                <i class="fas fa-calendar-alt mr-2 text-green-500"></i>
                                Laporan Bulanan
                            </a>
                            <a href="{{ route('reports.stock') }}" 
                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition">
                                <i class="fas fa-boxes mr-2 text-orange-500"></i>
                                Laporan Stok
                            </a>
                        </div>
                    </div>
                    @endcan
                </div>
            </div>

            <!-- Right side menu -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 space-x-3">
                @can('create transactions')
                <a href="{{ route('transactions.create') }}" 
                   class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition flex items-center space-x-2 shadow-md">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span class="hidden lg:inline">Transaksi Baru</span>
                </a>
                @endcan

                <!-- User Dropdown -->
                <div class="relative" x-data="{ dropdownOpen: false }">
                    <button @click="dropdownOpen = !dropdownOpen" @click.away="dropdownOpen = false"
                            class="flex items-center space-x-3 focus:outline-none group">
                        <div class="w-9 h-9 rounded-full bg-gradient-to-r from-blue-500 to-indigo-500 flex items-center justify-center shadow-md">
                            <span class="text-white text-sm font-bold">{{ substr(Auth::user()->name, 0, 1) }}</span>
                        </div>
                        <div class="text-left hidden md:block">
                            <div class="text-xs text-blue-200">{{ ucfirst(Auth::user()->roles->first()->name ?? 'User') }}</div>
                            <div class="text-sm font-medium text-white">{{ Auth::user()->name }}</div>
                        </div>
                        <svg class="w-4 h-4 text-white/70 transition-transform" :class="{ 'rotate-180': dropdownOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <div x-show="dropdownOpen" x-transition
                         class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-1 z-50"
                         style="display: none;">
                        @if(Auth::user()->branch_id)
                        <div class="px-4 py-2 border-b border-gray-100">
                            <p class="text-xs text-gray-500">Cabang</p>
                            <p class="text-sm font-medium text-gray-800">{{ Auth::user()->branch->name ?? '-' }}</p>
                        </div>
                        @endif

                        <a href="{{ route('profile.edit') }}" 
                           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            {{ __('Profile') }}
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" 
                                    class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100 transition">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                                {{ __('Log Out') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Hamburger Menu (Mobile) -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = !open" 
                        class="inline-flex items-center justify-center p-2 rounded-md text-white/70 hover:text-white hover:bg-white/10 focus:outline-none transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': !open}" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': !open, 'inline-flex': open}" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu (Mobile) -->
    <div :class="{'block': open, 'hidden': !open}" class="hidden sm:hidden bg-white/95 backdrop-blur-sm">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                <span>{{ __('Dashboard') }}</span>
            </x-responsive-nav-link>

            @can('view branches')
            <x-responsive-nav-link :href="route('branches.index')" :active="request()->routeIs('branches.*')" class="flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                <span>{{ __('Cabang') }}</span>
            </x-responsive-nav-link>
            @endcan

            @can('view products')
            <x-responsive-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')" class="flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
                <span>{{ __('Produk') }}</span>
            </x-responsive-nav-link>
            @endcan

            @can('view transactions')
            <x-responsive-nav-link :href="route('transactions.index')" :active="request()->routeIs('transactions.*')" class="flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-1.5 6M17 13l1.5 6M9 21h6M4 3h16"></path>
                </svg>
                <span>{{ __('Transaksi') }}</span>
            </x-responsive-nav-link>
            @endcan

            <!-- Audit Menu Mobile (Owner only) -->
            @role('owner')
            <div x-data="{ openAuditMobile: false }">
                <button @click="openAuditMobile = !openAuditMobile" 
                        class="w-full flex items-center justify-between px-3 py-2 text-base font-medium text-gray-700 hover:bg-gray-100 rounded-md">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-clipboard-list w-5"></i>
                        <span>Audit</span>
                    </div>
                    <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': openAuditMobile }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div x-show="openAuditMobile" class="pl-8 space-y-1">
                    <x-responsive-nav-link :href="route('audit.transactions')" :active="request()->routeIs('audit.transactions')" class="flex items-center space-x-2">
                        <i class="fas fa-receipt w-4 text-red-500"></i>
                        <span>Transaksi Dibatalkan</span>
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('audit.products')" :active="request()->routeIs('audit.products')" class="flex items-center space-x-2">
                        <i class="fas fa-box-open w-4 text-orange-500"></i>
                        <span>Produk Terhapus</span>
                    </x-responsive-nav-link>
                </div>
            </div>
            @endrole

            <!-- Reports Menu Mobile -->
            @can('view reports')
            <div x-data="{ openReportMobile: false }">
                <button @click="openReportMobile = !openReportMobile" 
                        class="w-full flex items-center justify-between px-3 py-2 text-base font-medium text-gray-700 hover:bg-gray-100 rounded-md">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <span>Laporan</span>
                    </div>
                    <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': openReportMobile }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div x-show="openReportMobile" class="pl-8 space-y-1">
                    <x-responsive-nav-link :href="route('reports.daily')" :active="request()->routeIs('reports.daily')" class="flex items-center space-x-2">
                        <i class="fas fa-calendar-day w-4 text-blue-500"></i>
                        <span>Laporan Harian</span>
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('reports.monthly')" :active="request()->routeIs('reports.monthly')" class="flex items-center space-x-2">
                        <i class="fas fa-calendar-alt w-4 text-green-500"></i>
                        <span>Laporan Bulanan</span>
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('reports.stock')" :active="request()->routeIs('reports.stock')" class="flex items-center space-x-2">
                        <i class="fas fa-boxes w-4 text-orange-500"></i>
                        <span>Laporan Stok</span>
                    </x-responsive-nav-link>
                </div>
            </div>
            @endcan
        </div>

        <!-- Mobile: Tombol Transaksi Baru -->
        @can('create transactions')
        <div class="px-4 py-2">
            <a href="{{ route('transactions.create') }}" 
               class="block w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-center font-medium transition">
                + Transaksi Baru
            </a>
        </div>
        @endcan

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-3 border-t border-gray-200">
            <div class="flex items-center px-4">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-r from-blue-500 to-indigo-500 flex items-center justify-center">
                        <span class="text-white text-md font-bold">{{ substr(Auth::user()->name, 0, 1) }}</span>
                    </div>
                </div>
                <div class="ms-3">
                    <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                    @if(Auth::user()->branch_id)
                    <div class="text-xs text-gray-400">{{ Auth::user()->branch->name ?? '' }}</div>
                    @endif
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')" class="flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span>{{ __('Profile') }}</span>
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();" class="flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        <span>{{ __('Log Out') }}</span>
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>