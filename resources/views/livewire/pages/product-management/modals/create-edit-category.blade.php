<!-- Create/Edit Category Modal -->
<flux:modal name="create-edit-category" class="max-w-lg" :closable="false">
    <div class="pr-2">
        <div class="mb-6">
            <flux:heading size="lg" class="text-gray-900 dark:text-white">
                @if($editingCategory)
                    Edit Category
                @elseif($creationMode === 'root')
                    Create New Root Category
                @else
                    Create New Subcategory
                @endif
            </flux:heading>
            <flux:subheading class="text-gray-600 dark:text-gray-400">
                @if($editingCategory)
                    Update the category information below.
                @elseif($creationMode === 'root')
                    Fill in the root category information to organize your products.
                @else
                    Fill in the subcategory information to organize your products.
                @endif
            </flux:subheading>
        </div>

        <div class="space-y-6">
            <flux:field>
                <flux:label>Category Name</flux:label>
                <flux:input 
                    wire:model="form.name" 
                    placeholder="Enter category name"
                    required
                    class="dark:bg-gray-700 dark:text-white dark:border-gray-600"
                />
                @error('form.name') 
                    <flux:error>{{ $message }}</flux:error>
                @enderror
            </flux:field>

            <flux:field>
                <flux:label>Description</flux:label>
                <flux:textarea 
                    wire:model="form.description" 
                    rows="3"
                    placeholder="Enter category description"
                    class="dark:bg-gray-700 dark:text-white dark:border-gray-600"
                />
                @error('form.description') 
                    <flux:error>{{ $message }}</flux:error>
                @enderror
            </flux:field>

            @if($creationMode === 'subcategory')
            <flux:field>
                <flux:label>Parent Category</flux:label>
                <flux:select 
                    wire:model="form.parent_id" 
                    placeholder="Select parent category"
                    class="dark:bg-gray-700 dark:text-white dark:border-gray-600"
                >
                    <option value="">No Parent (Root Category)</option>
                    @foreach($parentCategories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </flux:select>
                @error('form.parent_id') 
                    <flux:error>{{ $message }}</flux:error>
                @enderror
            </flux:field>
            @endif

            <flux:field>
                <flux:label>Sort Order</flux:label>
                <flux:input 
                    wire:model="form.sort_order" 
                    type="number"
                    placeholder="Enter sort order (0 = first, 1 = second, etc.)"
                    class="dark:bg-gray-700 dark:text-white dark:border-gray-600"
                />
                <flux:description>Controls the display order of categories. Lower numbers appear first.</flux:description>
                @error('form.sort_order') 
                    <flux:error>{{ $message }}</flux:error>
                @enderror
            </flux:field>

            <flux:field>
                <flux:label>Slug</flux:label>
                <flux:input 
                    wire:model="form.slug" 
                    placeholder="Enter URL slug (auto-generated if empty)"
                    class="dark:bg-gray-700 dark:text-white dark:border-gray-600"
                />
                @error('form.slug') 
                    <flux:error>{{ $message }}</flux:error>
                @enderror
            </flux:field>


            <!-- Status -->
            <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                <flux:checkbox 
                    wire:model="form.is_active" 
                    label="Active"
                    description="Check this to make the category active and visible"
                />
            </div>

            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>
                
                <flux:button 
                    wire:click="saveCategory" 
                    variant="primary"
                >
                    @if($editingCategory)
                        Update Category
                    @elseif($creationMode === 'root')
                        Create Root Category
                    @else
                        Create Subcategory
                    @endif
                </flux:button>
            </div>
        </div>
    </div>
</flux:modal>