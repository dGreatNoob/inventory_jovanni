@php
    use App\Models\Product;
    use App\Models\Category;
    use App\Models\Supplier;
    use App\Services\ProductService;
    
    $productService = app(ProductService::class);
    $stats = $productService->getProductStats();
    
    // Get recent products for activity feed
    $recentProducts = Product::with(['category', 'supplier'])
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();
    
    // Get top categories by product count
    $topCategories = Category::withCount('products')
        ->having('products_count', '>', 0)
        ->orderBy('products_count', 'desc')
        ->limit(5)
        ->get();
@endphp

<x-layouts.app :title="__('Dashboard')">
    <div class="space-y-6">
        <!-- Header Section -->
        <div class="mb-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div class="flex-1">
                    <h1 class="text-2xl font-semibold text-gray-800 dark:text-white">Dashboard</h1>
                    <p class="mt-1 text-sm text-gray-600 dark:text-neutral-300">Overview of your inventory management system</p>
                </div>
            </div>
        </div>

        <!-- Main KPI Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <!-- Total Products -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Products</dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ number_format($stats['total_products']) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- In Stock Products -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">In Stock</dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ number_format($stats['in_stock_products']) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Low Stock Products -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Low Stock</dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ number_format($stats['low_stock_products']) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Out of Stock Products -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Out of Stock</dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ number_format($stats['out_of_stock_products']) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts and Analytics -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Product Categories Distribution -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Product Categories
                </h3>
                
                @if($topCategories->count() > 0)
                    <div class="space-y-4">
                        @foreach($topCategories as $category)
                            @php
                                $percentage = $stats['total_products'] > 0 ? round(($category->products_count / $stats['total_products']) * 100) : 0;
                            @endphp
                            <div class="flex items-center justify-between">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $category->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $category->products_count }} products</p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <div class="w-24 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                        <div class="bg-indigo-600 h-2 rounded-full transition-all duration-300" style="width: {{ $percentage }}%"></div>
                                    </div>
                                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400 w-8">{{ $percentage }}%</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                        </svg>
                        <p class="text-gray-500 dark:text-gray-400">No categories with products yet</p>
                        <a href="{{ route('product-management.categories') }}" class="text-indigo-600 hover:text-indigo-500 text-sm mt-2 inline-block">Add categories</a>
                    </div>
                @endif
                </div>
            </div>

            <!-- Recent Products -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                    Recent Products
                </h3>
                
                @if($recentProducts->count() > 0)
                    <div class="space-y-3 max-h-64 overflow-y-auto">
                        @foreach($recentProducts as $product)
                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $product->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        @if($product->category)
                                            {{ $product->category->name }}
                                        @endif
                                        @if($product->supplier)
                                            • {{ $product->supplier->name }}
                                        @endif
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $product->created_at->format('M d, Y') }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-semibold text-indigo-600 dark:text-indigo-400">₱{{ number_format($product->price, 2) }}</p>
                                    @if($product->sku)
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $product->sku }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        <p class="text-gray-500 dark:text-gray-400">No products added yet</p>
                        <a href="{{ route('product-management.index') }}" class="text-indigo-600 hover:text-indigo-500 text-sm mt-2 inline-block">Add your first product</a>
                    </div>
                @endif
                </div>
            </div>
        </div>

        <!-- Quick Actions and System Status -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <!-- Quick Actions -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    Quick Actions
                </h3>
                
                <div class="space-y-3">
                    <a href="{{ route('product-management.index') }}" class="flex items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                        <svg class="w-5 h-5 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">Add New Product</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Create a new product in your catalog</p>
                        </div>
                    </a>
                    
                    <a href="{{ route('product-management.categories') }}" class="flex items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                        <svg class="w-5 h-5 text-purple-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">Manage Categories</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Organize your products by categories</p>
                        </div>
                    </a>
                    
                    <a href="{{ route('product-management.images') }}" class="flex items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                        <svg class="w-5 h-5 text-pink-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">Product Images</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Manage product photos and galleries</p>
                        </div>
                    </a>
                </div>
                </div>
            </div>

            <!-- System Status -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    System Status
                </h3>
                
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Database Connection</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
                            <svg class="w-2 h-2 mr-1" fill="currentColor" viewBox="0 0 8 8">
                                <circle cx="4" cy="4" r="3"/>
                            </svg>
                            Connected
                        </span>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Product Service</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
                            <svg class="w-2 h-2 mr-1" fill="currentColor" viewBox="0 0 8 8">
                                <circle cx="4" cy="4" r="3"/>
                            </svg>
                            Active
                        </span>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Image Storage</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
                            <svg class="w-2 h-2 mr-1" fill="currentColor" viewBox="0 0 8 8">
                                <circle cx="4" cy="4" r="3"/>
                            </svg>
                            Available
                        </span>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Last Backup</span>
                        <span class="text-sm text-gray-500 dark:text-gray-400">{{ now()->format('M d, Y H:i') }}</span>
                    </div>
                </div>
            </div>

            <!-- Product Management Overview -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Product Management
                </h3>
                
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-indigo-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">Total Products</span>
                        </div>
                        <span class="text-sm font-semibold text-indigo-600 dark:text-indigo-400">{{ number_format($stats['total_products']) }}</span>
                    </div>
                    
                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-emerald-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">Categories</span>
                        </div>
                        <span class="text-sm font-semibold text-emerald-600 dark:text-emerald-400">{{ number_format($stats['total_categories']) }}</span>
                    </div>
                    
                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-violet-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">Suppliers</span>
                        </div>
                        <span class="text-sm font-semibold text-violet-600 dark:text-violet-400">{{ number_format($stats['total_suppliers']) }}</span>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
