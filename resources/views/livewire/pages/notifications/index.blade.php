<div class="p-6 space-y-4">
    <div class="flex justify-between items-center">
        <h2 class="text-xl font-bold">Notifications</h2>
        <button wire:click="markAllAsRead" class="text-sm text-blue-600 hover:underline">
            Mark all as read
        </button>
    </div>

    @forelse ($notifications as $notification)
        <div class="bg-white border rounded-lg p-4 shadow-sm">
            <p class="text-gray-800">{{ $notification->data['message'] ?? 'No message' }}</p>
            <p class="text-xs text-gray-500">{{ $notification->created_at->diffForHumans() }}</p>
        </div>
    @empty
        <div class="text-gray-500">No notifications.</div>
    @endforelse
</div>
