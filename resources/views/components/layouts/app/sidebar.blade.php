<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
    @livewireStyles
    {{-- Dynamic favicon - will use black logo for light mode --}}
    <link rel="icon" type="image/png" href="{{ asset('images/jovanni_logo_black.png') }}" alt="Jovanni Logo" />
</head>
@php
    use App\Enums\Enum\PermissionEnum;
@endphp

<body class="min-h-screen bg-white dark:bg-zinc-800">
    <flux:sidebar sticky stashable class="border-r border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

        <a href="{{ route('dashboard') }}" class="me-5 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
            <x-app-logo />
        </a>

        <flux:navlist variant="outline">
            <flux:navlist.group :heading="__('')" class="grid">
                {{-- Dashboard --}}
                <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')"
                    wire:navigate>{{ __('Dashboard') }}
                </flux:navlist.item>

                {{-- Request Management --}}
                {{-- @if (Route::has('requisition.requestslip'))
                    <flux:navlist.group expandable :expanded="request()->routeIs('requisition.*')"
                        :heading="__('Request Management')" class="lg:grid">
                        <flux:navlist.item icon="inbox-stack" href="{{ route('requisition.requestslip') }}"
                            :current="request()->routeIs('requisition.requestslip')" wire:navigate>{{ __('Request Slip') }}
                        </flux:navlist.item>
                    </flux:navlist.group>
                @endif --}}

                {{-- Supplier Management --}}
                @if(Auth::user()->hasAnyPermission([
                    'supplier view', 
                    'supplier create', 
                    'supplier edit', 
                    'supplier delete', 
                    'supplier report view'
                ]))
                    <flux:navlist.group expandable :expanded="request()->routeIs('supplier.*')"
                        :heading="__('Supplier Management')" class="lg:grid">
                        <flux:navlist.item icon="users" href="{{ route('supplier.profile') }}"
                            :current="request()->routeIs('supplier.profile')" wire:navigate>
                            {{ __('Profile') }}
                        </flux:navlist.item>
                    </flux:navlist.group>
                @endif

                {{-- Purchase Order Management --}}
                 @if(Auth::user()->hasAnyPermission([
                    'po view',          
                    'po create',        
                    'po edit',          
                    'po delete',        
                    'po approve',      
                    'po receive',       
                    'po report view'    
                ]))
                <flux:navlist.group expandable :expanded="request()->routeIs('pomanagement.*')" :heading="__('PO Management')" class="lg:grid">
                    <flux:navlist.item icon="inbox-stack" href="{{ route('pomanagement.purchaseorder') }}"
                        :current="request()->routeIs('pomanagement.purchaseorder')" wire:navigate>
                        {{ __('Purchase Order') }}
                    </flux:navlist.item>
                    <flux:navlist.item icon="truck" href="{{ route('pomanagement.deliveries') }}"
                        :current="request()->routeIs('pomanagement.deliveries')" wire:navigate>
                        {{ __('Deliveries') }}
                    </flux:navlist.item>
                </flux:navlist.group>
                @endif

                {{-- Product Management --}}
                @if(Auth::user()->hasAnyPermission([
                    'product view', 
                    'product create', 
                    'product edit', 
                    'product delete', 
                    'product export'
                ]))
                    <flux:navlist.group 
                        expandable 
                        :expanded="request()->routeIs('product-management.*')" 
                        :heading="__('Product Management')" 
                        class="lg:grid text-left"
                    >
                        <flux:navlist.item 
                            icon="cube" 
                            href="{{ route('product-management.index') }}" 
                            :current="request()->routeIs('product-management.index')" 
                            wire:navigate
                        >
                            {{ __('Product Masterlist') }}
                        </flux:navlist.item>

                        <flux:navlist.item 
                            icon="tag" 
                            href="{{ route('product-management.categories') }}" 
                            :current="request()->routeIs('product-management.categories')" 
                            wire:navigate
                        >
                            {{ __('Categories') }}
                        </flux:navlist.item>

                        <flux:navlist.item 
                            icon="photo" 
                            href="{{ route('product-management.images') }}" 
                            :current="request()->routeIs('product-management.images')" 
                            wire:navigate
                        >
                            {{ __('Images') }}
                        </flux:navlist.item>

                        <flux:navlist.item 
                        icon="gift" 
                        href="{{ route('sales.promo') }}"
                        :current="request()->routeIs('sales.promo')" 
                        wire:navigate>{{ __('Promo Creation') }}
                        </flux:navlist.item>

                        <flux:navlist.item 
                            icon="chart-bar" 
                            href="{{ route('product-management.dashboard') }}" 
                            :current="request()->routeIs('product-management.dashboard')" 
                            wire:navigate
                        >
                            {{ __('Analytics') }}
                        </flux:navlist.item>
                    </flux:navlist.group>
                @endif
                
                {{-- User Management --}}
                @if(Auth::user()->hasAnyPermission([
                    'user view',
                    'user create',
                    'user edit',
                    'user delete',
                    'role view',
                    'role create',
                    'role edit',
                    'role delete',
                    'permission manage'
                ]))
                    <flux:navlist.group expandable
                        :expanded="request()->routeIs('user.index') || request()->routeIs('roles.index')"
                        :heading="__('User Management')" class="lg:grid">
                        <flux:navlist.item icon="users" href="{{ route('user.index') }}"
                            :current="request()->routeIs('user.index')" wire:navigate>
                            {{ __('Manage Users') }}
                        </flux:navlist.item>
                        <flux:navlist.item icon="shield-check" href="{{ route('roles.index') }}"
                            :current="request()->routeIs('roles.index')" wire:navigate>
                            {{ __('Roles & Permissions') }}
                        </flux:navlist.item>
                    </flux:navlist.group>
                @endif

                {{-- Allocation --}}
                <flux:navlist.group
                    expandable
                    :expanded="request()->routeIs('allocation.*')"
                    :heading="__('Allocation')"
                    class="lg:grid text-left"
                >
                    <flux:navlist.item
                            icon="building-storefront"
                            href="{{ route('allocation.warehouse') }}"
                            :current="request()->routeIs('allocation.warehouse')"
                            wire:navigate
                        >
                            {{ __('Warehouse') }}
                        </flux:navlist.item>
                        <flux:navlist.item
                            icon="chart-bar"
                            href="{{ route('allocation.sales') }}"
                            :current="request()->routeIs('allocation.sales')"
                            wire:navigate
                        >
                            {{ __('Sales') }}
                        </flux:navlist.item>
                </flux:navlist.group>

                {{-- Agent & Branches Management --}}
                @if(Auth::user()->hasAnyPermission([
                    'agent view',
                    'agent create',
                    'agent edit',
                    'agent delete',
                    'branch view',
                    'branch create',
                    'branch edit',
                    'branch delete',
                    'agent assign branch',
                    'agent transfer branch'
                ]))
                <flux:navlist.group expandable :expanded="request()->routeIs('customer.*', 'branch.*', 'agent.*')"
                    :heading="__('Operational Management')" class="lg:grid">
                    <flux:navlist.item icon="users" href="{{ route('agent.profile') }}"
                        :current="request()->routeIs('agent.profile')" wire:navigate>{{ __('Agent management') }}
                    </flux:navlist.item>
                    <flux:navlist.item icon="building-storefront" href="{{ route('branch.profile') }}"
                        :current="request()->routeIs('branch.profile')" wire:navigate>{{ __('Branch management') }}
                    </flux:navlist.item>
                    <!-- <flux:navlist.item icon="banknotes" href="javascript:void(0);"
                        :current="request()->routeIs('customer.rebate')" wire:navigate>{{ __('Rebate Criteria') }}
                    </flux:navlist.item> -->
                </flux:navlist.group>
                @endif

                {{-- Finance --}}
                @php
                    $financeRoutes = [
                        'finance.receivables',
                        'finance.payables',
                        'finance.expenses',
                        'finance.payments',
                        // 'finance.currency-conversion',
                    ];
                    $hasFinanceRoutes = collect($financeRoutes)->some(fn ($name) => Route::has($name));
                @endphp
                @if ($hasFinanceRoutes)
                    <flux:navlist.group expandable :expanded="request()->routeIs('finance.*')" :heading="__('Finance')"
                        class="lg:grid">
                        @if (Route::has('finance.receivables'))
                            <flux:navlist.item icon="banknotes" href="{{ route('finance.receivables') }}"
                                :current="request()->routeIs('finance.receivables')" wire:navigate>{{ __('Receivables') }}
                            </flux:navlist.item>
                        @endif
                        @if (Route::has('finance.payables'))
                            <flux:navlist.item icon="banknotes" href="{{ route('finance.payables') }}"
                                :current="request()->routeIs('finance.payables')" wire:navigate>{{ __('Payables') }}
                            </flux:navlist.item>
                        @endif
                        @if (Route::has('finance.expenses'))
                            <flux:navlist.item icon="banknotes" href="{{ route('finance.expenses') }}"
                                :current="request()->routeIs('finance.expenses')" wire:navigate>{{ __('Expenses') }}
                            </flux:navlist.item>
                        @endif
                        @if (Route::has('finance.payments'))
                            <flux:navlist.item icon="credit-card" href="{{ route('finance.payments') }}"
                                :current="request()->routeIs('finance.payments')" wire:navigate>{{ __('Payments') }}
                            </flux:navlist.item>
                        @endif
                        <!-- @if (Route::has('finance.currency-conversion'))
                            <flux:navlist.item icon="banknotes" href="{{ route('finance.currency-conversion') }}"  wire:navigate>{{ __('Currency Conversion') }}
                            </flux:navlist.item>
                        @endif -->
                    </flux:navlist.group>
                @endif

                {{-- Shipment Management --}}
                @php
                    $shipmentRoutes = [
                        'shipment.index',
                        'shipment.qrscanner',
                    ];
                    $hasShipmentRoutes = collect($shipmentRoutes)->some(fn ($name) => Route::has($name));
                @endphp
                @if ($hasShipmentRoutes)
                    <flux:navlist.group expandable :expanded="request()->routeIs('shipment.*')"
                        :heading="__('Shipment Management')" class="lg:grid">
                        @if (Route::has('shipment.index'))
                            <flux:navlist.item icon="banknotes" href="{{ route('shipment.index') }}"
                                :current="request()->routeIs('shipment.index')" wire:navigate>{{ __('Shipments') }}
                            </flux:navlist.item>
                        @endif
                        @if (Route::has('shipment.qrscanner'))
                            <flux:navlist.item icon="banknotes" href="{{ route('shipment.qrscanner') }}"
                                :current="request()->routeIs('shipment.qrscanner')" wire:navigate>{{ __('QR Scanner') }}
                            </flux:navlist.item>
                        @endif
                    </flux:navlist.group>
                @endif

                {{-- Warehouse Staff --}}
                @php
                    $warehouseRoutes = [
                        'warehousestaff.stockin',
                        'warehousestaff.stockout',
                    ];
                    $hasWarehouseRoutes = collect($warehouseRoutes)->some(fn ($name) => Route::has($name));
                @endphp
                @if ($hasWarehouseRoutes)
                    <flux:navlist.group expandable :expanded="request()->routeIs('warehousestaff.*')"
                        :heading="__('Warehouse Staff')" class="lg:grid">
                        @if (Route::has('warehousestaff.stockin'))
                            <flux:navlist.item icon="qr-code" href="{{ route('warehousestaff.stockin') }}"
                                :current="request()->routeIs('warehousestaff.stockin')" wire:navigate>{{ __('Stock In') }}
                            </flux:navlist.item>
                        @endif
                        @if (Route::has('warehousestaff.stockout'))
                            <flux:navlist.item icon="qr-code" href="{{ route('warehousestaff.stockout') }}"
                                :current="request()->routeIs('warehousestaff.stockout')" wire:navigate>{{ __('Stock Out') }}
                            </flux:navlist.item>
                        @endif
                        <!-- <flux:navlist.item icon="banknotes" href="javascript:void(0);"  wire:navigate>{{ __('Returns') }}
                        </flux:navlist.item> -->
                    </flux:navlist.group>
                @endif

                {{-- Activity Logs --}}
                <flux:navlist.item icon="clipboard-document-list" href="{{ route('activity.logs') }}"
                    :current="request()->routeIs('activity.logs')" wire:navigate>
                    {{ __('Activity Logs') }}
                </flux:navlist.item>

                {{-- ==========================================
                     HIDDEN/COMMENTED OUT MENU ITEMS
                     ========================================== --}}

                {{-- @if (class_exists('App\\Livewire\\NotificationBadge'))
                    <flux:navlist.item icon="bell" href="javascript:void(0);" wire:navigate>
                        {{ __('Notifications') }}
                        <livewire:notification-badge />
                    </flux:navlist.item>
                @endif --}}

                {{-- @php
                    $hasSuppliesRoutes = Route::has('fs.inventory') || Route::has('fs.purchaserequest');
                @endphp
                @if ($hasSuppliesRoutes)
                    <flux:navlist.group expandable :expanded="false" :heading="__('Supplies')"
                        class="lg:grid">
                        @if (Route::has('fs.inventory'))
                            <flux:navlist.item icon="inbox-stack" href="{{ route('fs.inventory') }}"
                                :current="request()->routeIs('fs.inventory')" wire:navigate>{{ __('Inventory') }}
                            </flux:navlist.item>
                        @endif
                        @if (Route::has('fs.purchaserequest'))
                            <flux:navlist.item icon="ticket" href="{{ route('fs.purchaserequest') }}"
                                :current="request()->routeIs('fs.purchaserequest')" wire:navigate>{{ __('Purchase List') }}
                            </flux:navlist.item>
                        @endif
                    </flux:navlist.group>
                @endif --}}

                {{-- Purchase Order Management --}}
                {{-- @if(Auth::user()->hasAnyPermission([
                    'po view',          
                    'po create',        
                    'po edit',          
                    'po delete',        
                    'po approve',      
                    'po receive',       
                    'po report view'    
                ]))
                <flux:navlist.group expandable :expanded="request()->routeIs('pomanagement.*')" :heading="__('PO Management')" class="lg:grid">
                    <flux:navlist.item icon="inbox-stack" href="{{ route('pomanagement.purchaseorder') }}"
                        :current="request()->routeIs('pomanagement.purchaseorder')" wire:navigate>
                        {{ __('Purchase Order') }}
                    </flux:navlist.item>
                    <flux:navlist.item icon="truck" href="{{ route('pomanagement.deliveries') }}"
                        :current="request()->routeIs('pomanagement.deliveries')" wire:navigate>
                        {{ __('Deliveries') }}
                    </flux:navlist.item>
                </flux:navlist.group>
                @endif --}}

                {{-- Sales Management --}}
                {{-- <flux:navlist.group expandable
                    :expanded="request()->routeIs('salesorder.*') || request()->routeIs('salesreturn.*') || request()->routeIs('sales-price.*')"
                    :heading="__('Sales Management')" class="lg:grid text-left">
                    <flux:navlist.item icon="inbox-stack" href="{{ route('salesorder.index') }}"
                        :current="request()->routeIs('salesorder.index')" wire:navigate>{{ __('Sales Order') }}
                    </flux:navlist.item>
                    @if (Route::has('salesorder.return'))
                        <flux:navlist.item icon="inbox-stack" href="{{ route('salesorder.return') }}"
                            :current="request()->routeIs('salesorder.return')" wire:navigate>{{ __('Sales Return') }}
                        </flux:navlist.item>
                    @endif
                    <flux:navlist.item icon="tag" href="{{ route('sales-price.index') }}"
                        :current="request()->routeIs('sales-price.*')" wire:navigate>{{ __('Sales Price') }}
                    </flux:navlist.item>
                </flux:navlist.group> --}}

                {{-- Allocation --}}
                {{-- <flux:navlist.group
                    expandable
                    :expanded="request()->routeIs('allocation.*')"
                    :heading="__('Allocation')"
                    class="lg:grid text-left"
                >
                    <flux:navlist.item
                            icon="building-storefront"
                            href="{{ route('allocation.warehouse') }}"
                            :current="request()->routeIs('allocation.warehouse')"
                            wire:navigate
                        >
                            {{ __('Warehouse') }}
                        </flux:navlist.item>
                        <flux:navlist.item
                            icon="chart-bar"
                            href="{{ route('allocation.sales') }}"
                            :current="request()->routeIs('allocation.sales')"
                            wire:navigate
                        >
                            {{ __('Sales') }}
                        </flux:navlist.item>
                </flux:navlist.group> --}}

                {{-- Finance --}}
                {{-- @php
                    $financeRoutes = [
                        'finance.receivables',
                        'finance.payables',
                        'finance.expenses',
                        'finance.currency-conversion',
                    ];
                    $hasFinanceRoutes = collect($financeRoutes)->some(fn ($name) => Route::has($name));
                @endphp
                @if ($hasFinanceRoutes)
                    <flux:navlist.group expandable :expanded="request()->routeIs('finance.*')" :heading="__('Finance')"
                        class="lg:grid">
                        @if (Route::has('finance.receivables'))
                            <flux:navlist.item icon="banknotes" href="{{ route('finance.receivables') }}"
                                :current="request()->routeIs('finance.receivables')" wire:navigate>{{ __('Receivables') }}
                            </flux:navlist.item>
                        @endif
                        @if (Route::has('finance.payables'))
                            <flux:navlist.item icon="banknotes" href="{{ route('finance.payables') }}"
                                :current="request()->routeIs('finance.payables')" wire:navigate>{{ __('Payables') }}
                            </flux:navlist.item>
                        @endif
                        @if (Route::has('finance.expenses'))
                            <flux:navlist.item icon="banknotes" href="{{ route('finance.expenses') }}"
                                :current="request()->routeIs('finance.expenses')" wire:navigate>{{ __('Expenses') }}
                            </flux:navlist.item>
                        @endif
                        @if (Route::has('finance.currency-conversion'))
                            <flux:navlist.item icon="banknotes" href="{{ route('finance.currency-conversion') }}"  wire:navigate>{{ __('Currency Conversion') }}
                            </flux:navlist.item>
                        @endif
                    </flux:navlist.group>
                @endif --}}

                {{-- Shipment Management --}}
                {{-- @php
                    $shipmentRoutes = [
                        'shipment.index',
                        'shipment.qrscanner',
                    ];
                    $hasShipmentRoutes = collect($shipmentRoutes)->some(fn ($name) => Route::has($name));
                @endphp
                @if ($hasShipmentRoutes)
                    <flux:navlist.group expandable :expanded="request()->routeIs('shipment.*')"
                        :heading="__('Shipment Management')" class="lg:grid">
                        @if (Route::has('shipment.index'))
                            <flux:navlist.item icon="banknotes" href="{{ route('shipment.index') }}"
                                :current="request()->routeIs('shipment.index')" wire:navigate>{{ __('Shipments') }}
                            </flux:navlist.item>
                        @endif
                        @if (Route::has('shipment.qrscanner'))
                            <flux:navlist.item icon="banknotes" href="{{ route('shipment.qrscanner') }}"
                                :current="request()->routeIs('shipment.qrscanner')" wire:navigate>{{ __('QR Scanner') }}
                            </flux:navlist.item>
                        @endif
                    </flux:navlist.group>
                @endif --}}

                {{-- Shipping Management --}}
                {{-- @php
                    $shippingRoutes = [
                        'shipping.plan',
                        'shipping.deliveries',
                    ];
                    $hasShippingRoutes = collect($shippingRoutes)->some(fn ($name) => Route::has($name));
                @endphp
                @if ($hasShippingRoutes)
                    <flux:navlist.group expandable :expanded="request()->routeIs('shipping.*')" :heading="__('Shipping Management')"
                        class="lg:grid">
                        @if (Route::has('shipping.plan'))
                            <flux:navlist.item icon="inbox-stack" href="{{ route('shipping.plan') }}"  wire:navigate>{{ __('Shipping Plan') }}
                            </flux:navlist.item>
                        @endif
                        @if (Route::has('shipping.deliveries'))
                            <flux:navlist.item icon="inbox-stack" href="{{ route('shipping.deliveries') }}"  wire:navigate>{{ __('Deliveries') }}
                            </flux:navlist.item>
                        @endif
                    </flux:navlist.group>
                @endif --}}

                {{-- Agent & Branches Management --}}
                {{-- @if(Auth::user()->hasAnyPermission([
                    'agent view',
                    'agent create',
                    'agent edit',
                    'agent delete',
                    'branch view',
                    'branch create',
                    'branch edit',
                    'branch delete',
                    'agent assign branch',
                    'agent transfer branch'
                ]))
                <flux:navlist.group expandable :expanded="request()->routeIs('customer.*', 'branch.*', 'agent.*')"
                    :heading="__('Operational Management')" class="lg:grid">
                    <flux:navlist.item icon="users" href="{{ route('customer.profile') }}"
                        :current="request()->routeIs('customer.profile')" wire:navigate>{{ __('Agent Management') }}
                    </flux:navlist.item>
                    <flux:navlist.item icon="users" href="{{ route('agent.profile') }}"
                        :current="request()->routeIs('agent.profile')" wire:navigate>{{ __('Agent management') }}
                    </flux:navlist.item>
                    <flux:navlist.item icon="building-storefront" href="{{ route('branch.profile') }}"
                        :current="request()->routeIs('branch.profile')" wire:navigate>{{ __('Branch management') }}
                    </flux:navlist.item>
                    <flux:navlist.item icon="banknotes" href="javascript:void(0);"
                        :current="request()->routeIs('customer.rebate')" wire:navigate>{{ __('Rebate Criteria') }}
                    </flux:navlist.item>
                </flux:navlist.group>
                @endif --}}

                {{-- Setup Section --}}
                {{-- @role(['Super Admin', 'Admin'])
                    @php
                        $setupRoutes = [
                            'setup.department',
                            'setup.itemType',
                            'setup.allocation',
                        ];
                        $hasSetupRoutes = collect($setupRoutes)->some(fn ($name) => Route::has($name));
                    @endphp
                    @if ($hasSetupRoutes)
                        <flux:navlist.group expandable :expanded="request()->routeIs('setup.*')"
                            :heading="__('Setup Section')" class="lg:grid">
                            @if (Route::has('setup.department'))
                                <flux:navlist.item icon="inbox-stack" href="{{ route('setup.department') }}"
                                    :current="request()->routeIs('setup.department')" wire:navigate>{{ __('Department') }}
                                </flux:navlist.item>
                            @endif
                            @if (Route::has('setup.itemType'))
                                <flux:navlist.item icon="inbox-stack" href="{{ route('setup.itemType') }}"
                                    :current="request()->routeIs('setup.itemType')" wire:navigate>{{ __('Item Type') }}
                                </flux:navlist.item>
                            @endif
                            @if (Route::has('setup.allocation'))
                                <flux:navlist.item icon="inbox-stack" href="{{ route('setup.allocation') }}"
                                    :current="request()->routeIs('setup.allocation')" wire:navigate>{{ __('Allocation') }}
                                </flux:navlist.item>
                            @endif
                        </flux:navlist.group>
                    @endif
                @endrole --}}

                {{-- Warehouse Staff --}}
                <!-- @php
                    $warehouseRoutes = [
                        'warehousestaff.stockin',
                        'warehousestaff.stockout',
                    ];
                    $hasWarehouseRoutes = collect($warehouseRoutes)->some(fn ($name) => Route::has($name));
                @endphp
                @if ($hasWarehouseRoutes)
                    <flux:navlist.group expandable :expanded="request()->routeIs('warehousestaff.*')"
                        :heading="__('Warehouse Staff')" class="lg:grid">
                        @if (Route::has('warehousestaff.stockin'))
                            <flux:navlist.item icon="qr-code" href="{{ route('warehousestaff.stockin') }}"
                                :current="request()->routeIs('warehousestaff.stockin')" wire:navigate>{{ __('Stock In') }}
                            </flux:navlist.item>
                        @endif
                        @if (Route::has('warehousestaff.stockout'))
                            <flux:navlist.item icon="qr-code" href="{{ route('warehousestaff.stockout') }}"
                                :current="request()->routeIs('warehousestaff.stockout')" wire:navigate>{{ __('Stock Out') }}
                            </flux:navlist.item>
                        @endif
                        <flux:navlist.item icon="banknotes" href="javascript:void(0);"  wire:navigate>{{ __('Returns') }}
                        </flux:navlist.item>
                    </flux:navlist.group>
                @endif -->

                {{-- Reports --}}
                {{-- <flux:navlist.group expandable :expanded="request()->routeIs('reports.*')" :heading="__('Reports')"
                    class="lg:grid">
                    <flux:navlist.item icon="chart-bar" href="{{ route('dashboard') }}" 
                        :current="request()->routeIs('dashboard')" wire:navigate>
                        {{ __('Overview Dashboard') }}
                    </flux:navlist.item>
                    <flux:navlist.item icon="clipboard-document-list" href="{{ route('reports.stock-available') }}"
                        :current="request()->routeIs('reports.stock-available')" wire:navigate>{{ __('Stock Available') }}
                    </flux:navlist.item>
                    <flux:navlist.item icon="clipboard-document-list" href="{{ route('reports.purchase-orders') }}"
                        :current="request()->routeIs('reports.purchase-orders')" wire:navigate>{{ __('Purchase Orders') }}
                    </flux:navlist.item>
                    <flux:navlist.item icon="clipboard-document-list" href="{{ route('reports.sales-orders') }}"
                        :current="request()->routeIs('reports.sales-orders')" wire:navigate>{{ __('Sales Orders') }}
                    </flux:navlist.item>
                    <flux:navlist.item icon="clipboard-document-list" href="{{ route('reports.stock-movement') }}"
                        :current="request()->routeIs('reports.stock-movement')" wire:navigate>{{ __('Stock Movement') }}
                    </flux:navlist.item>
                    <flux:navlist.item icon="clipboard-document-list" href="{{ route('reports.inventory-valuation') }}"
                        :current="request()->routeIs('reports.inventory-valuation')" wire:navigate>
                        {{ __('Inventory Valuation') }}
                    </flux:navlist.item>
                    <flux:navlist.item icon="clipboard-document-list" href="{{ route('reports.top-products') }}"
                        :current="request()->routeIs('reports.top-products')" wire:navigate>{{ __('Top Products') }}
                    </flux:navlist.item>
                    <flux:navlist.item icon="clipboard-document-list" href="{{ route('reports.supplier-performance') }}"
                        :current="request()->routeIs('reports.supplier-performance')" wire:navigate>
                        {{ __('Supplier') }}
                    </flux:navlist.item>
                    <flux:navlist.item icon="clipboard-document-list" href="{{ route('reports.customer-analysis') }}"
                        :current="request()->routeIs('reports.customer-analysis')" wire:navigate>
                        {{ __('Customer') }}
                    </flux:navlist.item>
                    <flux:navlist.item icon="clipboard-document-list" href="{{ route('reports.sales-returns') }}"
                        :current="request()->routeIs('reports.sales-returns')" wire:navigate>
                        {{ __('Sales Returns') }}
                    </flux:navlist.item>
                    <flux:navlist.item icon="clipboard-document-list" href="{{ route('reports.purchase-returns') }}"
                        :current="request()->routeIs('reports.purchase-returns')" wire:navigate>
                        {{ __('Purchase Returns') }}
                    </flux:navlist.item>
                    <flux:navlist.item icon="chart-bar" href="{{ route('reports.financial-summary') }}"
                        :current="request()->routeIs('reports.financial-summary')" wire:navigate>
                        {{ __('Financial Summary') }}
                    </flux:navlist.item>
                    <flux:navlist.item icon="banknotes" href="{{ route('finance.receivables') }}"
                        :current="request()->routeIs('finance.receivables')" wire:navigate>
                        {{ __('Receivables') }}
                    </flux:navlist.item>
                    <flux:navlist.item icon="banknotes" href="{{ route('finance.payables') }}"
                        :current="request()->routeIs('finance.payables')" wire:navigate>
                        {{ __('Payables') }}
                    </flux:navlist.item>
                    <flux:navlist.item icon="banknotes" href="{{ route('finance.expenses') }}"
                        :current="request()->routeIs('finance.expenses')" wire:navigate>
                        {{ __('Expenses') }}
                    </flux:navlist.item>
                    <flux:navlist.item icon="clipboard-document-list" href="{{ route('activity.logs') }}"
                        :current="request()->routeIs('activity.logs')" wire:navigate>
                        {{ __('Activity Logs') }}
                    </flux:navlist.item>
                </flux:navlist.group> --}}
            </flux:navlist.group>
        </flux:navlist>



        <flux:spacer />

        {{-- <flux:navlist variant="outline">
                <flux:navlist.item icon="folder-git-2" href="https://github.com/laravel/livewire-starter-kit" target="_blank">
                {{ __('Repository') }}
                </flux:navlist.item>

                <flux:navlist.item icon="book-open-text" href="https://laravel.com/docs/starter-kits" target="_blank">
                {{ __('Documentation') }}
                </flux:navlist.item>
            </flux:navlist> --}}

        <!-- Desktop User Menu -->
        <flux:dropdown position="bottom" align="start">
            <flux:profile :name="auth()->user()-> name" :initials="auth()->user()->initials()"
                icon-trailing="chevrons-up-down" />

            <flux:menu class="w-[220px]">
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                <span
                                    class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                    {{ auth()->user()->initials() }}
                                </span>
                            </span>

                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>
                        {{ __('Settings') }}</flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle"
                        class="w-full">
                        {{ __('Log Out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:sidebar>

    <!-- Mobile User Menu -->
    <flux:header class="lg:hidden">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <flux:spacer />

        <flux:dropdown position="top" align="end">
            <flux:profile :initials="auth()->user()->initials()" icon-trailing="chevron-down" />

            <flux:menu>
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                <span
                                    class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                    {{ auth()->user()->initials() }}
                                </span>
                            </span>

                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>
                        {{ __('Settings') }}</flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle"
                        class="w-full">
                        {{ __('Log Out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:header>

    {{ $slot }}


    @fluxScripts
    @livewireScripts
</body>

</html>