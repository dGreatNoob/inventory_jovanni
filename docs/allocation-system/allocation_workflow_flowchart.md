# Allocation System Workflow - Warehouse & Sales Integration

## Workflow Description

### Warehouse Allocation Process 
1. **Create Batch** - Warehouse manager creates a new delivery batch with auto-generated reference number
2. **Add Branches** - Select multiple branches that will receive the delivery
3. **Add Items** - For each branch, add products with quantities and pricing
4. **Edit Items** - Modify quantities, prices, or remove items as needed
5. **Dispatch** - Final step that changes batch status to 'dispatched' and automatically creates Sales Receipt records
6. **Export VDR** - Generate delivery receipts for documentation

### Sales Receipt Process 
1. **Select Batch** - Sales personnel select a date to view available dispatched batches
2. **View Receipts** - See all branch receipts for the selected batch
3. **Receipt Details** - Examine individual branch allocations and item details
4. **Edit Items** - Adjust received quantities, record damaged items before confirmation
5. **Confirm Receipt** - Finalize the received quantities and update branch allocation status
6. **Mark as Sold** - Track actual sales of received inventory

### Key Integration Points
- **Dispatch creates Sales Receipts**: When warehouse dispatches a batch, it automatically creates SalesReceipt and SalesReceiptItem records
- **Status Synchronization**: Branch allocation status updates from 'pending' to 'received' when sales confirms receipt
- **Data Flow**: Warehouse allocations become sales receipt items with allocated quantities that can be adjusted during receipt confirmation
- **Audit Trail**: Complete tracking from initial allocation to final sale

### Status Flow
```
Warehouse: Draft → Dispatched
Sales: Pending → Received → (Partial) Sold/Sold
```

This integrated system ensures complete traceability from warehouse allocation to final sales, with proper status tracking and quantity reconciliation at each stage.