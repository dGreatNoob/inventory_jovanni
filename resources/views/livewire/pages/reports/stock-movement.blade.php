<x-slot:header>Stock Movement Report</x-slot:header>
<x-slot:subheader>Track stock ins, outs, and inventory flow patterns</x-slot:subheader>

<div class="space-y-6">
    <!-- Movement Overview KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Stock Ins -->
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Stock Ins (This Month)</p>
                    <p class="text-3xl font-bold">1,247</p>
                    <p class="text-xs text-green-100 mt-1">+15% from last month</p>
                </div>
                <svg class="w-12 h-12 text-green-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                </svg>
            </div>
        </div>

        <!-- Total Stock Outs -->
        <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-lg p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-red-100 text-sm font-medium">Stock Outs (This Month)</p>
                    <p class="text-3xl font-bold">892</p>
                    <p class="text-xs text-red-100 mt-1">-8% from last month</p>
                </div>
                <svg class="w-12 h-12 text-red-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l3-3m0 0l-3-3m3 3H9"></path>
                </svg>
            </div>
        </div>

        <!-- Net Movement -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Net Movement</p>
                    <p class="text-3xl font-bold">+355</p>
                    <p class="text-xs text-blue-100 mt-1">Stock increase</p>
                </div>
                <svg class="w-12 h-12 text-blue-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </div>
        </div>

        <!-- Most Active Product -->
        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium">Most Active Product</p>
                    <p class="text-2xl font-bold">Medical Gloves</p>
                    <p class="text-xs text-purple-100 mt-1">245 movements</p>
                </div>
                <svg class="w-12 h-12 text-purple-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Movement Analytics -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Daily Movement Trend -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                Daily Movement Trend (Last 7 Days)
            </h3>
            
            <!-- Line Chart -->
            <div class="relative h-64">
                <svg class="w-full h-full" viewBox="0 0 500 250">
                    <!-- Sample data for 7 days -->
                    <!-- In: Green, Out: Red -->
                    <g class="text-gray-200 dark:text-gray-600" stroke="currentColor" stroke-width="0.5">
                        <line x1="50" y1="200" x2="450" y2="200" />
                        <line x1="50" y1="50" x2="50" y2="200" />
                        
                        <!-- Grid lines -->
                        <line x1="50" y1="170" x2="450" y2="170" opacity="0.3" />
                        <line x1="50" y1="140" x2="450" y2="140" opacity="0.3" />
                        <line x1="50" y1="110" x2="450" y2="110" opacity="0.3" />
                        <line x1="50" y1="80" x2="450" y2="80" opacity="0.3" />
                    </g>
                    
                    <!-- Stock In Line (Green) -->
                    <path d="M70,180 L130,160 L190,140 L250,150 L310,130 L370,145 L430,125" fill="none" stroke="#10b981" stroke-width="3" stroke-linecap="round" />
                    
                    <!-- Stock Out Line (Red) -->
                    <path d="M70,190 L130,175 L190,165 L250,170 L310,160 L370,155 L430,150" fill="none" stroke="#ef4444" stroke-width="3" stroke-linecap="round" />
                    
                    <!-- Data points -->
                    <circle cx="70" cy="180" r="4" fill="#10b981" />
                    <circle cx="130" cy="160" r="4" fill="#10b981" />
                    <circle cx="190" cy="140" r="4" fill="#10b981" />
                    <circle cx="250" cy="150" r="4" fill="#10b981" />
                    <circle cx="310" cy="130" r="4" fill="#10b981" />
                    <circle cx="370" cy="145" r="4" fill="#10b981" />
                    <circle cx="430" cy="125" r="4" fill="#10b981" />
                    
                    <circle cx="70" cy="190" r="4" fill="#ef4444" />
                    <circle cx="130" cy="175" r="4" fill="#ef4444" />
                    <circle cx="190" cy="165" r="4" fill="#ef4444" />
                    <circle cx="250" cy="170" r="4" fill="#ef4444" />
                    <circle cx="310" cy="160" r="4" fill="#ef4444" />
                    <circle cx="370" cy="155" r="4" fill="#ef4444" />
                    <circle cx="430" cy="150" r="4" fill="#ef4444" />
                    
                    <!-- X-axis labels -->
                    <text x="70" y="220" text-anchor="middle" class="text-xs fill-gray-600 dark:fill-gray-400">Mon</text>
                    <text x="130" y="220" text-anchor="middle" class="text-xs fill-gray-600 dark:fill-gray-400">Tue</text>
                    <text x="190" y="220" text-anchor="middle" class="text-xs fill-gray-600 dark:fill-gray-400">Wed</text>
                    <text x="250" y="220" text-anchor="middle" class="text-xs fill-gray-600 dark:fill-gray-400">Thu</text>
                    <text x="310" y="220" text-anchor="middle" class="text-xs fill-gray-600 dark:fill-gray-400">Fri</text>
                    <text x="370" y="220" text-anchor="middle" class="text-xs fill-gray-600 dark:fill-gray-400">Sat</text>
                    <text x="430" y="220" text-anchor="middle" class="text-xs fill-gray-600 dark:fill-gray-400">Sun</text>
                </svg>
            </div>
            
            <!-- Legend -->
            <div class="mt-4 flex justify-center space-x-6">
                <div class="flex items-center text-sm">
                    <div class="w-4 h-4 bg-green-500 rounded mr-2"></div>
                    <span class="text-gray-700 dark:text-gray-300">Stock In</span>
                </div>
                <div class="flex items-center text-sm">
                    <div class="w-4 h-4 bg-red-500 rounded mr-2"></div>
                    <span class="text-gray-700 dark:text-gray-300">Stock Out</span>
                </div>
            </div>
        </div>

        <!-- Top Moving Products -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
                Most Active Products
            </h3>
            
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Medical Gloves</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">245 movements</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-20 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                            <div class="bg-purple-600 h-2 rounded-full" style="width: 100%"></div>
                        </div>
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400 w-8">245</span>
                    </div>
                </div>
                
                <div class="flex items-center justify-between">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Surgical Masks</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">198 movements</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-20 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                            <div class="bg-purple-600 h-2 rounded-full" style="width: 81%"></div>
                        </div>
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400 w-8">198</span>
                    </div>
                </div>
                
                <div class="flex items-center justify-between">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Bandages</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">156 movements</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-20 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                            <div class="bg-purple-600 h-2 rounded-full" style="width: 64%"></div>
                        </div>
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400 w-8">156</span>
                    </div>
                </div>
                
                <div class="flex items-center justify-between">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Syringes</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">134 movements</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-20 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                            <div class="bg-purple-600 h-2 rounded-full" style="width: 55%"></div>
                        </div>
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400 w-8">134</span>
                    </div>
                </div>
                
                <div class="flex items-center justify-between">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Antiseptic</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">89 movements</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-20 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                            <div class="bg-purple-600 h-2 rounded-full" style="width: 36%"></div>
                        </div>
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400 w-8">89</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Movements -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Recent Stock Movements
        </h3>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th class="px-6 py-3">Product</th>
                        <th class="px-6 py-3">Type</th>
                        <th class="px-6 py-3">Quantity</th>
                        <th class="px-6 py-3">Date</th>
                        <th class="px-6 py-3">Department/Supplier</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="bg-white border-b dark:bg-gray-900 dark:border-gray-700">
                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">Medical Gloves</td>
                        <td class="px-6 py-4"><span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded dark:bg-green-900 dark:text-green-300">IN</span></td>
                        <td class="px-6 py-4">+500</td>
                        <td class="px-6 py-4">Sep 12, 2025</td>
                        <td class="px-6 py-4">MedSupply Co.</td>
                    </tr>
                    <tr class="bg-gray-50 border-b dark:bg-gray-800 dark:border-gray-700">
                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">Surgical Masks</td>
                        <td class="px-6 py-4"><span class="bg-red-100 text-red-800 text-xs font-medium px-2 py-1 rounded dark:bg-red-900 dark:text-red-300">OUT</span></td>
                        <td class="px-6 py-4">-150</td>
                        <td class="px-6 py-4">Sep 12, 2025</td>
                        <td class="px-6 py-4">Emergency Dept.</td>
                    </tr>
                    <tr class="bg-white border-b dark:bg-gray-900 dark:border-gray-700">
                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">Bandages</td>
                        <td class="px-6 py-4"><span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded dark:bg-green-900 dark:text-green-300">IN</span></td>
                        <td class="px-6 py-4">+200</td>
                        <td class="px-6 py-4">Sep 11, 2025</td>
                        <td class="px-6 py-4">HealthEquip Ltd.</td>
                    </tr>
                    <tr class="bg-gray-50 border-b dark:bg-gray-800 dark:border-gray-700">
                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">IV Bags</td>
                        <td class="px-6 py-4"><span class="bg-red-100 text-red-800 text-xs font-medium px-2 py-1 rounded dark:bg-red-900 dark:text-red-300">OUT</span></td>
                        <td class="px-6 py-4">-80</td>
                        <td class="px-6 py-4">Sep 11, 2025</td>
                        <td class="px-6 py-4">ICU Department</td>
                    </tr>
                    <tr class="bg-white dark:bg-gray-900">
                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">Thermometers</td>
                        <td class="px-6 py-4"><span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded dark:bg-green-900 dark:text-green-300">IN</span></td>
                        <td class="px-6 py-4">+25</td>
                        <td class="px-6 py-4">Sep 10, 2025</td>
                        <td class="px-6 py-4">TechMed Inc.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>