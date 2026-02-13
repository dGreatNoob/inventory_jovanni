{{-- Reusable optional-reason modal for Close for fulfillment and Reopen PO --}}
<flux:modal name="po-reason-modal" class="max-w-md" focusable>
    <div class="space-y-4">
        <div>
            <flux:heading size="lg">
                @if($reasonModalAction === 'close')
                    Close for fulfillment
                @elseif($reasonModalAction === 'reopen')
                    Reopen PO
                @else
                    Reason (optional)
                @endif
            </flux:heading>
            <flux:subheading>
                @if($reasonModalAction === 'close')
                    Are you sure you want to close this purchase order for fulfillment? This will mark the PO as received. You may add an optional reason below (e.g. short or complete shipment).
                @elseif($reasonModalAction === 'reopen')
                    Add an optional reason for reopening this PO.
                @else
                    Provide a reason if you wish.
                @endif
            </flux:subheading>
        </div>

        <form wire:submit="submitReasonModal" class="space-y-4">
            <flux:textarea
                wire:model="reasonModalReason"
                label="Reason (optional)"
                placeholder="e.g. Short shipment received; PO closed as complete."
                rows="3"
            />

            <div class="flex justify-end gap-2">
                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">
                    @if($reasonModalAction === 'close')
                        Close for fulfillment
                    @elseif($reasonModalAction === 'reopen')
                        Reopen PO
                    @else
                        Submit
                    @endif
                </flux:button>
            </div>
        </form>
    </div>
</flux:modal>
