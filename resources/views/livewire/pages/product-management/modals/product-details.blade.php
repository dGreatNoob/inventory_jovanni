<flux:modal name="product-details" class="w-[1100px] max-w-full">
    @if($editingProduct)
        @php
            $isSale = str($editingProduct->price_note ?? '')->startsWith('SAL');
            $margin = $editingProduct->price - $editingProduct->cost;
            $marginPercent = $editingProduct->price > 0 ? ($margin / $editingProduct->price) * 100 : 0;
            $priceHistoryCount = count($priceHistories ?? []);
            $lastPriceChange = $priceHistoryCount ? \Illuminate\Support\Arr::first($priceHistories) : null;
            $lastPriceChangeHuman = $lastPriceChange && ($lastPriceChange['changed_at'] ?? null)
                ? \Carbon\Carbon::parse($lastPriceChange['changed_at'])->diffForHumans()
                : null;
        @endphp
        <div class="space-y-6 pr-2">
            <div class="flex flex-col lg:flex-row gap-6">
                <!-- Gallery & Overview -->
                <div class="w-full lg:w-2/5 space-y-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0 h-12 w-12 rounded-xl overflow-hidden bg-gray-100 dark:bg-gray-700">
                                @if($editingProduct->primary_image)
                                    <img src="{{ asset('storage/photos/' . $editingProduct->primary_image) }}" alt="{{ $editingProduct->name }}" class="h-12 w-12 object-cover">
                                @else
                                    <div class="h-full w-full flex items-center justify-center text-gray-400 dark:text-gray-500">
                                        <flux:icon name="photo" class="h-6 w-6" />
                                    </div>
                                @endif
                            </div>
                            <div>
                                <flux:heading size="lg" class="text-gray-900 dark:text-white leading-snug">
                                    {{ $editingProduct->remarks ?: $editingProduct->name }}
                                </flux:heading>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                    SKU: {{ $editingProduct->sku }}
                                </p>
                                <div class="mt-2 flex flex-wrap gap-2">
                                    <span class="inline-flex items-center rounded-md bg-indigo-50 px-2.5 py-1 text-xs font-medium text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-200">
                                        Product ID: {{ $editingProduct->product_number ?? '—' }}
                                    </span>
                                    <span class="inline-flex items-center rounded-md bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-700 dark:bg-slate-700 dark:text-slate-200">
                                        Barcode: {{ $editingProduct->barcode ?: '—' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $isSale ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900 dark:text-emerald-300' }}">
                            <span class="w-2 h-2 rounded-full mr-2 {{ $isSale ? 'bg-red-500' : 'bg-emerald-500' }}"></span>
                            {{ $isSale ? 'Red Tag' : 'Regular' }}
                        </span>
                    </div>

                    <div
                        class="relative h-64 rounded-2xl border border-gray-200 bg-white dark:bg-gray-900 dark:border-gray-700 shadow-sm flex items-center justify-center"
                    >
                        @if($viewingImage)
                            <button type="button" wire:click="viewerPrev" aria-label="Previous image" class="absolute left-3 top-1/2 -translate-y-1/2 bg-white/85 dark:bg-gray-900/70 hover:bg-white dark:hover:bg-gray-800 rounded-full p-2 shadow transition">
                                <flux:icon name="chevron-left" class="h-5 w-5" />
                            </button>

                            <div class="h-full w-full flex items-center justify-center">
                                <img src="{{ $this->viewingImageUrl ?? ($viewingImage->url ?? asset('storage/photos/' . $viewingImage->filename)) }}" 
                                     alt="{{ $editingProduct->name }}" 
                                     class="h-full w-full object-contain"
                                     onerror="this.src='{{ asset('images/placeholder.png') }}'; this.onerror=null;">
                            </div>

                            <button type="button" wire:click="viewerNext" aria-label="Next image" class="absolute right-3 top-1/2 -translate-y-1/2 bg-white/85 dark:bg-gray-900/70 hover:bg-white dark:hover:bg-gray-800 rounded-full p-2 shadow transition">
                                <flux:icon name="chevron-right" class="h-5 w-5" />
                            </button>
                        @else
                            <div class="flex flex-col items-center justify-center text-gray-400 dark:text-gray-500">
                                <flux:icon name="photo" class="h-10 w-10 mb-3" />
                                <p class="text-sm">No product media uploaded</p>
                            </div>
                        @endif
                    </div>

                    @if($viewingImage)
                        <div class="flex justify-end">
                            <flux:modal.trigger name="product-image-zoom">
                                <flux:button variant="ghost" size="sm" icon="magnifying-glass">
                                    Zoom
                                </flux:button>
                            </flux:modal.trigger>
                        </div>
                    @endif

                    @if(!empty($viewerImages))
                        <div class="px-3 pt-1">
                            <div class="flex items-center justify-center gap-2 overflow-x-auto scrollbar-thin">
                                @foreach($viewerImages as $imageId)
                                    @php $thumb = \App\Models\ProductImage::find($imageId); @endphp
                                    <button
                                        type="button"
                                        wire:click="setViewerImage({{ $imageId }})"
                                        aria-label="View image {{ $loop->iteration }}"
                                        class="relative h-14 w-14 overflow-hidden rounded-lg border {{ ($viewingImage && $viewingImage->id === $imageId) ? 'border-indigo-500 ring-2 ring-indigo-200' : 'border-gray-200 dark:border-gray-700' }}"
                                    >
                                        @if($thumb && $thumb->filename)
                                            <img src="{{ asset('storage/photos/' . $thumb->filename) }}" 
                                                 alt="Thumbnail {{ $loop->iteration }}" 
                                                 class="h-full w-full object-cover"
                                                 onerror="this.src='{{ asset('images/placeholder.png') }}'; this.onerror=null;">
                                        @else
                                            <div class="flex h-full w-full items-center justify-center text-xs text-gray-400">IMG</div>
                                        @endif
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($editingProduct->remarks)
                        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-900">
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2 flex items-center gap-2">
                                <flux:icon name="document-text" class="h-4 w-4 text-indigo-500" />
                                Description
                            </h4>
                            <p class="text-sm leading-relaxed text-gray-700 dark:text-gray-300">{{ $editingProduct->remarks }}</p>
                        </div>
                    @endif
                </div>

                <!-- Details -->
                <div class="w-full lg:flex-1 space-y-6">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Updated {{ optional($editingProduct->updated_at)->diffForHumans() ?? '—' }}
                        </p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-900">
                            <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-1">Selling Price</p>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-white">₱{{ number_format($editingProduct->price, 2) }}</p>
                        </div>
                        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-900">
                            <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-1">Cost</p>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-white">₱{{ number_format($editingProduct->cost, 2) }}</p>
                        </div>
                        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-900">
                            <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-1">Margin</p>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-white">₱{{ number_format($margin, 2) }}</p>
                            <p class="text-xs text-green-600 dark:text-green-400 mt-1">({{ number_format($marginPercent, 1) }}%)</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-900">
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                                <flux:icon name="archive-box" class="h-4 w-4 text-indigo-500" />
                                Classification
                            </h4>
                            <dl class="space-y-2 text-sm">
                                <div class="flex justify-between gap-4"><dt class="text-gray-500 dark:text-gray-400">Category</dt><dd class="text-gray-900 dark:text-white text-right">{{ $editingProduct->category->name ?? 'N/A' }}</dd></div>
                                <div class="flex justify-between gap-4"><dt class="text-gray-500 dark:text-gray-400">Supplier</dt><dd class="text-gray-900 dark:text-white text-right">{{ $editingProduct->supplier->name ?? 'N/A' }}</dd></div>
                                <div class="flex justify-between gap-4"><dt class="text-gray-500 dark:text-gray-400">Supplier SKU</dt><dd class="text-gray-900 dark:text-white text-right">{{ $editingProduct->supplier_code ?: '—' }}</dd></div>
                                <div class="flex justify-between gap-4"><dt class="text-gray-500 dark:text-gray-400">Color</dt><dd class="text-gray-900 dark:text-white text-right">{{ optional($editingProduct->color)->shortcut ?: optional($editingProduct->color)->name ?: '—' }}</dd></div>
                            </dl>
                        </div>
                        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-900">
                            <div class="flex items-center justify-between mb-3">
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                                <flux:icon name="chart-bar" class="h-4 w-4 text-indigo-500" />
                                    Pricing History
                                </h4>
                                <flux:modal.trigger name="product-price-history">
                                    <flux:button variant="ghost" size="sm">
                                        View History @if($priceHistoryCount) ({{ $priceHistoryCount }}) @endif
                                    </flux:button>
                                </flux:modal.trigger>
                            </div>
                            <dl class="space-y-2 text-sm">
                                <div class="flex justify-between gap-4"><dt class="text-gray-500 dark:text-gray-400">Type</dt><dd class="text-gray-900 dark:text-white text-right">{{ $isSale ? 'Sale' : 'Regular' }}</dd></div>
                                <div class="flex justify-between gap-4"><dt class="text-gray-500 dark:text-gray-400">Pricing Note</dt><dd class="text-gray-900 dark:text-white text-right">{{ $editingProduct->price_note ?: '—' }}</dd></div>
                                <div class="flex justify-between gap-4"><dt class="text-gray-500 dark:text-gray-400">Last Change</dt><dd class="text-gray-900 dark:text-white text-right">
                                    {{ $lastPriceChangeHuman ?? '—' }}
                                </dd></div>
                            </dl>
                        </div>
                        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-900">
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                                <flux:icon name="cube" class="h-4 w-4 text-indigo-500" />
                                Inventory Snapshot
                            </h4>
                            <dl class="space-y-2 text-sm">
                                <div class="flex justify-between gap-4"><dt class="text-gray-500 dark:text-gray-400">On Hand</dt><dd class="text-gray-900 dark:text-white text-right">{{ number_format($editingProduct->total_quantity) }} {{ $editingProduct->uom }}</dd></div>
                                <div class="flex justify-between gap-4"><dt class="text-gray-500 dark:text-gray-400">Shelf Life</dt><dd class="text-gray-900 dark:text-white text-right">{{ $editingProduct->shelf_life_days ?: 'N/A' }}</dd></div>
                                <div class="flex justify-between gap-4"><dt class="text-gray-500 dark:text-gray-400">Status</dt><dd>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $editingProduct->disabled ? 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-200' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900 dark:text-emerald-200' }}">
                                        {{ $editingProduct->disabled ? 'Disabled' : 'Available' }}
                                    </span>
                                </dd></div>
                            </dl>
                        </div>
                        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-900">
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                                <flux:icon name="finger-print" class="h-4 w-4 text-indigo-500" />
                                Identifiers
                            </h4>
                            <dl class="space-y-2 text-sm">
                                <div class="flex justify-between gap-4"><dt class="text-gray-500 dark:text-gray-400">Product ID</dt><dd class="text-gray-900 dark:text-white text-right">{{ $editingProduct->product_number ?? '—' }}</dd></div>
                                <div class="flex justify-between gap-4"><dt class="text-gray-500 dark:text-gray-400">Barcode</dt><dd class="text-gray-900 dark:text-white text-right">{{ $editingProduct->barcode ?: '—' }}</dd></div>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</flux:modal>

@if($editingProduct)
    <flux:modal name="product-image-zoom" class="w-[720px] max-w-full">
        <div class="space-y-4">
            <div class="flex justify-between items-start">
                <div>
                    <flux:heading size="md" class="text-gray-900 dark:text-white">Zoom View</flux:heading>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Scroll to zoom further, drag to pan.</p>
                </div>
                <flux:modal.close aria-label="Close" />
            </div>

            <div
                x-data="{
                    scale: 1,
                    translate: { x: 0, y: 0 },
                    dragging: false,
                    last: { x: 0, y: 0 },
                    startDrag(event) {
                        this.dragging = true;
                        this.last = { x: event.clientX, y: event.clientY };
                    },
                    onDrag(event) {
                        if (!this.dragging) return;
                        this.translate.x += event.clientX - this.last.x;
                        this.translate.y += event.clientY - this.last.y;
                        this.last = { x: event.clientX, y: event.clientY };
                    },
                    endDrag() {
                        this.dragging = false;
                    },
                    zoom(event) {
                        event.preventDefault();
                        const next = Math.min(5, Math.max(1, this.scale + (event.deltaY < 0 ? 0.25 : -0.25)));
                        if (next === 1) {
                            this.translate = { x: 0, y: 0 };
                        }
                        this.scale = next;
                    },
                    reset() {
                        this.scale = 1;
                        this.translate = { x: 0, y: 0 };
                    }
                }"
                x-init="reset()"
                class="relative rounded-2xl border border-gray-200 bg-white dark:bg-gray-900 overflow-hidden"
            >
                <div class="flex items-center justify-between px-4 py-2 border-b border-gray-200 dark:border-gray-700">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Drag to pan • Scroll to zoom • Double-click reset</p>
                    <button type="button" class="text-xs text-indigo-500 hover:text-indigo-600" @click.prevent="reset">Reset</button>
                </div>

                <div class="relative h-[460px] cursor-grab active:cursor-grabbing"
                     @wheel="zoom($event)"
                     @mousedown="startDrag($event)"
                     @mousemove="onDrag($event)"
                     @mouseup="endDrag()"
                     @mouseleave="endDrag()"
                     @dblclick.prevent="reset()"
                >
                    <img src="{{ $viewingImage?->url }}" alt="{{ $editingProduct->name }} zoomed"
                         class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2"
                         :style="`transform: translate(calc(-50% + ${translate.x}px), calc(-50% + ${translate.y}px)) scale(${scale}); transition: ${dragging ? 'none' : 'transform 120ms ease'};`"
                    >
                </div>
            </div>
        </div>
    </flux:modal>
@endif
