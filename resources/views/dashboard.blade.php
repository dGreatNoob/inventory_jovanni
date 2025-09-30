<x-layouts.app :title="__('Dashboard')">
    <div class="space-y-6">
        <!-- Main KPI Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Stock Items -->
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-6 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-medium">Total Stock Items</p>
                        <p class="text-3xl font-bold">1,247</p>
                        <p class="text-xs text-blue-100 mt-1 flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L4.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            +12.5% from last month
                        </p>
                    </div>
                    <svg class="w-12 h-12 text-blue-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
            </div>

            <!-- Total Stock-ins This Month -->
            <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-6 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm font-medium">Stock-ins This Month</p>
                        <p class="text-3xl font-bold">342</p>
                        <p class="text-xs text-green-100 mt-1 flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L4.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            +8.3% from last month
                        </p>
                    </div>
                    <svg class="w-12 h-12 text-green-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                    </svg>
                </div>
            </div>

            <!-- Total Stock-outs This Month -->
            <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-lg p-6 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-red-100 text-sm font-medium">Stock-outs This Month</p>
                        <p class="text-3xl font-bold">189</p>
                        <p class="text-xs text-red-100 mt-1 flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 10.293a1 1 0 010 1.414l-6 6a1 1 0 01-1.414 0l-6-6a1 1 0 111.414-1.414L9 14.586V3a1 1 0 012 0v11.586l4.293-4.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            -5.2% from last month
                        </p>
                    </div>
                    <svg class="w-12 h-12 text-red-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l3-3m0 0l-3-3m3 3H9"></path>
                    </svg>
                </div>
            </div>

            <!-- Low Stock Alerts -->
            <div class="bg-gradient-to-r from-amber-500 to-amber-600 rounded-lg p-6 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-amber-100 text-sm font-medium">Low Stock Alerts</p>
                        <p class="text-3xl font-bold">23</p>
                        <p class="text-xs text-amber-100 mt-1">Items below reorder point</p>
                    </div>
                    <svg class="w-12 h-12 text-amber-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Charts and Analytics -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Monthly Stock In/Out Trend -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Monthly Stock In/Out Trend
                </h3>
                
                <!-- Bar Chart -->
                <div class="relative h-64">
                    <svg class="w-full h-full" viewBox="0 0 500 250">
                        @php
                            $monthlyData = [
                                ['month' => 'Jan', 'stockIn' => 320, 'stockOut' => 180],
                                ['month' => 'Feb', 'stockIn' => 280, 'stockOut' => 220],
                                ['month' => 'Mar', 'stockIn' => 360, 'stockOut' => 200],
                                ['month' => 'Apr', 'stockIn' => 310, 'stockOut' => 190],
                                ['month' => 'May', 'stockIn' => 380, 'stockOut' => 210],
                                ['month' => 'Jun', 'stockIn' => 342, 'stockOut' => 189]
                            ];
                            $maxValue = max(array_merge(array_column($monthlyData, 'stockIn'), array_column($monthlyData, 'stockOut')));
                        @endphp
                        
                        <!-- Grid lines -->
                        <g class="text-gray-200 dark:text-gray-600" stroke="currentColor" stroke-width="0.5">
                            <line x1="50" y1="200" x2="450" y2="200" />
                            <line x1="50" y1="50" x2="50" y2="200" />
                            
                            @for($i = 0; $i <= 4; $i++)
                                <line x1="50" y1="{{ 50 + ($i * 37.5) }}" x2="450" y2="{{ 50 + ($i * 37.5) }}" opacity="0.3" />
                            @endfor
                        </g>
                        
                        <!-- Bars -->
                        @foreach($monthlyData as $index => $data)
                            @php
                                $barWidth = 25;
                                $spacing = 60;
                                $groupX = 70 + ($index * $spacing);
                                
                                $stockInHeight = ($data['stockIn'] / $maxValue) * 150;
                                $stockOutHeight = ($data['stockOut'] / $maxValue) * 150;
                                
                                $stockInY = 200 - $stockInHeight;
                                $stockOutY = 200 - $stockOutHeight;
                            @endphp
                            
                            <!-- Stock In Bar -->
                            <rect x="{{ $groupX - $barWidth/2 }}" y="{{ $stockInY }}" width="{{ $barWidth }}" height="{{ $stockInHeight }}" fill="#10b981" rx="2" />
                            
                            <!-- Stock Out Bar -->
                            <rect x="{{ $groupX + $barWidth/2 + 2 }}" y="{{ $stockOutY }}" width="{{ $barWidth }}" height="{{ $stockOutHeight }}" fill="#ef4444" rx="2" />
                            
                            <!-- Month labels -->
                            <text x="{{ $groupX + $barWidth/4 }}" y="220" text-anchor="middle" class="text-xs fill-gray-600 dark:fill-gray-400 font-medium">
                                {{ $data['month'] }}
                            </text>
                        @endforeach
                        
                        <!-- Y-axis labels -->
                        @for($i = 0; $i <= 4; $i++)
                            @php
                                $value = ($maxValue / 4) * (4 - $i);
                                $y = 50 + ($i * 37.5);
                            @endphp
                            <text x="40" y="{{ $y + 5 }}" text-anchor="end" class="text-xs fill-gray-600 dark:fill-gray-400">
                                {{ round($value) }}
                            </text>
                        @endfor
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

            <!-- Top Products -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                    Top Products by Usage
                </h3>
                
                <div class="space-y-4">
                    @php
                        $topProducts = [
                            ['name' => 'Medical Gloves', 'usage' => 4250, 'percentage' => 92],
                            ['name' => 'Surgical Masks', 'usage' => 3800, 'percentage' => 84],
                            ['name' => 'Bandages', 'usage' => 2950, 'percentage' => 68],
                            ['name' => 'Syringes', 'usage' => 2200, 'percentage' => 52],
                            ['name' => 'Antiseptic', 'usage' => 1800, 'percentage' => 42]
                        ];
                    @endphp
                    
                    @foreach($topProducts as $product)
                        <div class="flex items-center justify-between">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $product['name'] }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ number_format($product['usage']) }} units used</p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <div class="w-24 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div class="bg-purple-600 h-2 rounded-full transition-all duration-300" style="width: {{ $product['percentage'] }}%"></div>
                                </div>
                                <span class="text-xs font-medium text-gray-500 dark:text-gray-400 w-8">{{ $product['percentage'] }}%</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Recent Activities and Top Performers -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Recent Stock In -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                    </svg>
                    Recent Stock In
                </h3>
                
                <div class="space-y-3 max-h-64 overflow-y-auto">
                    @php
                        $recentStockIn = [
                            ['product' => 'Medical Gloves', 'quantity' => 500, 'date' => '2025-09-11', 'supplier' => 'MedSupply Co.'],
                            ['product' => 'Surgical Masks', 'quantity' => 200, 'date' => '2025-09-11', 'supplier' => 'HealthEquip Ltd.'],
                            ['product' => 'Bandages', 'quantity' => 150, 'date' => '2025-09-10', 'supplier' => 'MedCare Supplies'],
                            ['product' => 'IV Bags', 'quantity' => 100, 'date' => '2025-09-10', 'supplier' => 'MedSupply Co.'],
                            ['product' => 'Thermometers', 'quantity' => 25, 'date' => '2025-09-09', 'supplier' => 'TechMed Inc.']
                        ];
                    @endphp
                    
                    @foreach($recentStockIn as $item)
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $item['product'] }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $item['supplier'] }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ \Carbon\Carbon::parse($item['date'])->format('M d, Y') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-green-600 dark:text-green-400">+{{ $item['quantity'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Recent Stock Out -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l3-3m0 0l-3-3m3 3H9"></path>
                    </svg>
                    Recent Stock Out
                </h3>
                
                <div class="space-y-3 max-h-64 overflow-y-auto">
                    @php
                        $recentStockOut = [
                            ['product' => 'Medical Gloves', 'quantity' => 300, 'date' => '2025-09-11', 'department' => 'Emergency'],
                            ['product' => 'Surgical Masks', 'quantity' => 150, 'date' => '2025-09-11', 'department' => 'Surgery'],
                            ['product' => 'Syringes', 'quantity' => 80, 'date' => '2025-09-10', 'department' => 'ICU'],
                            ['product' => 'Bandages', 'quantity' => 120, 'date' => '2025-09-10', 'department' => 'Emergency'],
                            ['product' => 'Antiseptic', 'quantity' => 45, 'date' => '2025-09-09', 'department' => 'Pediatrics']
                        ];
                    @endphp
                    
                    @foreach($recentStockOut as $item)
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $item['product'] }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $item['department'] }} Dept.</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ \Carbon\Carbon::parse($item['date'])->format('M d, Y') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-red-600 dark:text-red-400">-{{ $item['quantity'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Top Customers/Suppliers -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                <div class="mb-4">
                    <div class="flex border-b border-gray-200 dark:border-gray-700">
                        <button class="px-4 py-2 text-sm font-medium text-blue-600 border-b-2 border-blue-600" onclick="showTab('customers')">
                            Top Customers
                        </button>
                        <button class="px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700" onclick="showTab('suppliers')">
                            Top Suppliers
                        </button>
                    </div>
                </div>
                
                <!-- Top Customers -->
                <div id="customers-tab" class="space-y-3 max-h-64 overflow-y-auto">
                    @php
                        $topCustomers = [
                            ['name' => 'Emergency Department', 'orders' => 45, 'value' => 125000],
                            ['name' => 'Surgery Department', 'orders' => 32, 'value' => 98500],
                            ['name' => 'ICU Department', 'orders' => 28, 'value' => 87200],
                            ['name' => 'Pediatrics Department', 'orders' => 22, 'value' => 65300],
                            ['name' => 'Cardiology Department', 'orders' => 18, 'value' => 52800]
                        ];
                    @endphp
                    
                    @foreach($topCustomers as $customer)
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $customer['name'] }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $customer['orders'] }} orders</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-blue-600 dark:text-blue-400">${{ number_format($customer['value']) }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Top Suppliers (hidden by default) -->
                <div id="suppliers-tab" class="space-y-3 max-h-64 overflow-y-auto hidden">
                    @php
                        $topSuppliers = [
                            ['name' => 'MedSupply Co.', 'orders' => 28, 'value' => 185000],
                            ['name' => 'HealthEquip Ltd.', 'orders' => 22, 'value' => 142000],
                            ['name' => 'MedCare Supplies', 'orders' => 18, 'value' => 98500],
                            ['name' => 'TechMed Inc.', 'orders' => 15, 'value' => 76200],
                            ['name' => 'ProMed Solutions', 'orders' => 12, 'value' => 54800]
                        ];
                    @endphp
                    
                    @foreach($topSuppliers as $supplier)
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $supplier['name'] }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $supplier['orders'] }} orders</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-green-600 dark:text-green-400">${{ number_format($supplier['value']) }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function showTab(tab) {
            // Update tab buttons
            const buttons = document.querySelectorAll('button[onclick^="showTab"]');
            buttons.forEach(btn => {
                btn.classList.remove('text-blue-600', 'border-b-2', 'border-blue-600');
                btn.classList.add('text-gray-500', 'hover:text-gray-700');
            });
            
            // Show active tab button
            const activeButton = document.querySelector(`button[onclick="showTab('${tab}')"]`);
            if (activeButton) {
                activeButton.classList.remove('text-gray-500', 'hover:text-gray-700');
                activeButton.classList.add('text-blue-600', 'border-b-2', 'border-blue-600');
            }
            
            // Update tab content
            document.getElementById('customers-tab').classList.toggle('hidden', tab !== 'customers');
            document.getElementById('suppliers-tab').classList.toggle('hidden', tab !== 'suppliers');
        }
    </script>
</x-layouts.app>
