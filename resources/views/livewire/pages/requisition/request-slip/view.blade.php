{{-- <div>
    <h1>Request Slip #{{ $request_slip->id }}</h1>
    <p><strong>Requested By:</strong> {{ $request_slip->requested_by }}</p>
    <p><strong>Date:</strong> {{ $request_slip->created_at->format('F d, Y') }}</p>
    <p><strong>Status:</strong> {{ ucfirst($request_slip->status) }}</p>
    <p><strong>Status:</strong> {{ $request_slip->description }}</p>
    <!-- Add more fields as needed -->
</div> --}}

<div class="mb-14">
    <x-collapsible-card title="View Request Slip" open="true" size="full">
        <form wire:submit.prevent="create" x-show="open" x-transition>
            <x-dropdown value="{{ $request_slip->purpose }}" name="purpose" label="Purpose" :options="[
                'Raw Materials' => 'Request a Raw Materials',
                'Supply' => 'Request a Supply',
                'Consumables/Borrow' => 'Request Consumables/Borrow Items',
            ]"
                placeholder="Select a Purpose" readonly disabled />
            <div class="grid gap-6 mb-2 md:grid-cols-2">



                <x-dropdown name="sent_from" wire:model="sent_from" label="Sender" :options="[
                    'Raw Materials Dept.' => 'Raw Materials Dept.',
                    'Supply Dept.' => 'Supply Dept.',
                    'Engineering Dept.' => 'Engineering Dept.',
                ]"
                    value="Engineering Dept." disabled />

                <x-dropdown name="sent_to" wire:model="sent_to" label="Receiver" :options="[
                    'Purchasing Dept.' => 'Purchasing Dept.',
                ]"
                    value="Purchasing Dept." disabled />

            </div>

            <x-input type="textarea" disabled wire:model="description" rows="20"
                value="{{ $request_slip->description }}" name="description" label="Description" />


        </form>

    </x-collapsible-card>

    <div
        class="ml-15 fixed bottom-0 right-0 p-4 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 w-full">

        <div class="flex justify-end space-x-4">
            <a href="{{ route('requisition.requestslip') }}"
                class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">
                Back to List
            </a>

            @php
                use App\Enums\Enum\PermissionEnum;
            @endphp

            @can(PermissionEnum::APPROVE_REQUEST_SLIP->value)

             
                <x-button type="button" wire:click="ApproveRequestSlip" :disabled="$request_slip->status === 'approved'" class="flex justify-end space-x-4">
                    Approve Request Slip
                </x-button>
                <x-button type="button" variant="danger" :disabled="$request_slip->status === 'rejected'" wire:click="RejectRequestSlip" class="flex justify-end space-x-4">
                    Reject Request Slip
                </x-button>
            @endcan

        </div>
    </div>
</div>
