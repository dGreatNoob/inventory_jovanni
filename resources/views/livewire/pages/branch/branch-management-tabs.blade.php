<div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 mb-6 overflow-hidden">
    <div class="flex items-center border-b border-zinc-200 dark:border-zinc-700">

        <!-- Tab: Create Branch -->
        <a href="{{ route('branch.profile') }}"
           class="flex items-center px-6 py-4 text-sm font-medium
           {{ request()->routeIs('branch.profile') ? 'border-b-2 border-blue-600 text-blue-600 dark:text-blue-400' : 'border-b-2 border-transparent text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100' }}
           transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Create Branch
        </a>

        <!-- Tab: Branch Inventory -->
        <a href="{{ route('branch.inventory') }}"
           class="flex items-center px-6 py-4 text-sm font-medium
           {{ request()->routeIs('branch.inventory') ? 'border-b-2 border-blue-600 text-blue-600 dark:text-blue-400' : 'border-b-2 border-transparent text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100' }}
           transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
            </svg>
            Branch Inventory
        </a>

        <!-- Tab: Sales Track -->
         <!-- <a href="#" -->
        {{-- <a href="{{ route('branch.salesTrack') }}"
            class="flex items-center px-6 py-4 text-sm font-medium
            {{ request()->routeIs('branch.salesTrack') ? 'border-b-2 border-blue-600 text-blue-600 dark:text-blue-400' : 'border-b-2 border-transparent text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100' }}
            transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h4l3 10h8l3-10H3"/>
            </svg>
            Sales Track
        </a> --}}

        <!-- Tab: Stock Transfer -->
        <!-- <a href="#"
           class="flex items-center px-6 py-4 text-sm font-medium
           {{ request()->routeIs('branch.stockTransfer') ? 'border-b-2 border-blue-600 text-blue-600 dark:text-blue-400' : 'border-b-2 border-transparent text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100' }}
           transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-5h6v5"/>
            </svg>
            Stock Transfer
        </a> -->
    </div>
</div>