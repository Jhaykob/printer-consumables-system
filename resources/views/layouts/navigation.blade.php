@php
    $user = auth()->user();
    // Safely grab the user's permission names, or an empty array if they have none
    $userPerms = $user && $user->permissions ? $user->permissions->pluck('name')->toArray() : [];
    $isSuper = $user ? $user->is_superuser : false;
@endphp

<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                        <div class="flex items-center justify-center w-8 h-8 bg-red-50 text-red-600 rounded">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        </div>
                        <span class="font-black text-gray-900 tracking-tight uppercase leading-none hidden sm:block">Printer <span class="text-red-600">Consumables</span></span>
                    </a>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    <x-nav-link :href="route('requests.index')" :active="request()->routeIs('requests.*')">
                        {{ __('My Requests') }}
                    </x-nav-link>

                    @if($isSuper || in_array('manage-printers', $userPerms) || in_array('manage-system', $userPerms))
                    <div class="hidden sm:flex sm:items-center sm:ms-6">
                        <x-dropdown align="left" width="48">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-red-600 focus:outline-none transition ease-in-out duration-150 h-full mt-1">
                                    <div>Manage Assets</div>
                                    <div class="ms-1">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                    </div>
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                @if($isSuper || in_array('manage-system', $userPerms))
                                    <x-dropdown-link :href="route('departments.index')">{{ __('Departments') }}</x-dropdown-link>
                                    <x-dropdown-link :href="route('locations.index')">{{ __('Locations') }}</x-dropdown-link>
                                    <hr class="my-1 border-gray-100">
                                    <x-dropdown-link :href="route('categories.index')">{{ __('Categories') }}</x-dropdown-link>
                                    <x-dropdown-link :href="route('colors.index')">{{ __('Colors') }}</x-dropdown-link>
                                    <x-dropdown-link :href="route('types.index')">{{ __('Consumable Types') }}</x-dropdown-link>
                                @endif

                                @if($isSuper || in_array('manage-printers', $userPerms))
                                    @if($isSuper || in_array('manage-system', $userPerms)) <hr class="my-1 border-gray-100"> @endif
                                    <x-dropdown-link :href="route('printers.index')" class="font-bold text-gray-800">{{ __('Printers') }}</x-dropdown-link>
                                @endif
                            </x-slot>
                        </x-dropdown>
                    </div>
                    @endif

                    @if($isSuper || in_array('manage-inventory', $userPerms) || in_array('manage-requests', $userPerms) || in_array('generate-reports', $userPerms) || in_array('view-dashboard', $userPerms))
                    <div class="hidden sm:flex sm:items-center sm:ms-2">
                        <x-dropdown align="left" width="48">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-red-600 focus:outline-none transition ease-in-out duration-150 h-full mt-1">
                                    <div>Operations</div>
                                    <div class="ms-1">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                    </div>
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                @if($isSuper || in_array('manage-inventory', $userPerms))
                                    <x-dropdown-link :href="route('inventory.index')" class="font-bold text-gray-800">{{ __('Inventory Stock') }}</x-dropdown-link>
                                @endif

                                @if($isSuper || in_array('manage-requests', $userPerms))
                                    <x-dropdown-link :href="route('admin.requests.index')">{{ __('Fulfillment Dashboard') }}</x-dropdown-link>
                                @endif

                                @if($isSuper || in_array('generate-reports', $userPerms) || in_array('view-dashboard', $userPerms))
                                    <hr class="my-1 border-gray-100">
                                    <x-dropdown-link :href="route('admin.reports.index')">{{ __('Reports & Analytics') }}</x-dropdown-link>
                                @endif
                            </x-slot>
                        </x-dropdown>
                    </div>
                    @endif

                    @if($isSuper || in_array('manage-users', $userPerms))
                    <div class="hidden sm:flex sm:items-center sm:ms-2">
                        <x-dropdown align="left" width="48">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-red-600 focus:outline-none transition ease-in-out duration-150 h-full mt-1">
                                    <div>System</div>
                                    <div class="ms-1">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                    </div>
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                <x-dropdown-link :href="route('users.index')">{{ __('User Management') }}</x-dropdown-link>
                                <x-dropdown-link :href="route('admin.logs.index')">{{ __('Audit Logs') }}</x-dropdown-link>
                            </x-slot>
                        </x-dropdown>
                    </div>
                    @endif
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">{{ __('Profile') }}</x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();" class="text-red-600 font-bold">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('requests.index')" :active="request()->routeIs('requests.*')">
                {{ __('My Requests') }}
            </x-responsive-nav-link>

            @if($isSuper || in_array('manage-printers', $userPerms) || in_array('manage-system', $userPerms))
                <div class="px-4 py-2 mt-2 text-xs font-bold text-gray-500 uppercase tracking-wider bg-gray-50">Manage Assets</div>
                @if($isSuper || in_array('manage-system', $userPerms))
                    <x-responsive-nav-link :href="route('departments.index')">{{ __('Departments') }}</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('locations.index')">{{ __('Locations') }}</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('categories.index')">{{ __('Categories') }}</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('colors.index')">{{ __('Colors') }}</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('types.index')">{{ __('Consumable Types') }}</x-responsive-nav-link>
                @endif
                @if($isSuper || in_array('manage-printers', $userPerms))
                    <x-responsive-nav-link :href="route('printers.index')">{{ __('Printers') }}</x-responsive-nav-link>
                @endif
            @endif

            @if($isSuper || in_array('manage-inventory', $userPerms) || in_array('manage-requests', $userPerms) || in_array('generate-reports', $userPerms) || in_array('view-dashboard', $userPerms))
                <div class="px-4 py-2 mt-2 text-xs font-bold text-gray-500 uppercase tracking-wider bg-gray-50">Operations</div>
                @if($isSuper || in_array('manage-inventory', $userPerms))
                    <x-responsive-nav-link :href="route('inventory.index')" class="font-bold">{{ __('Inventory Stock') }}</x-responsive-nav-link>
                @endif
                @if($isSuper || in_array('manage-requests', $userPerms))
                    <x-responsive-nav-link :href="route('admin.requests.index')">{{ __('Fulfillment Dashboard') }}</x-responsive-nav-link>
                @endif
                @if($isSuper || in_array('generate-reports', $userPerms) || in_array('view-dashboard', $userPerms))
                    <x-responsive-nav-link :href="route('admin.reports.index')">{{ __('Reports & Analytics') }}</x-responsive-nav-link>
                @endif
            @endif

            @if($isSuper || in_array('manage-users', $userPerms))
                <div class="px-4 py-2 mt-2 text-xs font-bold text-gray-500 uppercase tracking-wider bg-gray-50">System</div>
                <x-responsive-nav-link :href="route('users.index')">{{ __('User Management') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.logs.index')">{{ __('Audit Logs') }}</x-responsive-nav-link>
            @endif
        </div>

        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();" class="text-red-600 font-bold">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
