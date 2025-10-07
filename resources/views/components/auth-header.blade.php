@props([
    'title',
    'description',
])

<div class="flex w-full flex-col text-center space-y-1">
    <flux:heading size="xl">{{ $title }}</flux:heading>
    <flux:subheading>{{ $description }}</flux:subheading>
</div>
