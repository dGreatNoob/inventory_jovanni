# Sales Price Module - User Guide

## Introduction

The Sales Price module allows you to create and manage pricing configurations for your sales operations. This guide will walk you through all the features and functionality available in the module.

## Accessing the Module

1. Log in to your account
2. Navigate to the sidebar menu
3. Expand the **"Sales Management"** section
4. Click on **"Sales Price"**

## Main Interface Overview

### Header Section
- **Title**: "Sales Price Setup"
- **Description**: Brief explanation of the module's purpose
- **Add Button**: "Add Sales Price" - opens the create form

### Search Functionality
- **Search Bar**: Located at the top of the data table
- **Real-time Search**: Results update as you type
- **Search Scope**: Searches through description field
- **Clear Search**: Delete text to show all records

### Data Table
The table displays all sales price configurations with the following columns:

1. **Description**: Name of the pricing configuration
2. **Less (%)**: Discount percentage (formatted to 2 decimal places)
3. **Pricing Note**: Additional notes (truncated with tooltip)
4. **Created At**: Date when the configuration was created
5. **Actions**: Edit and Delete buttons

### Pagination
- **Per Page Options**: 5, 10, 20, 50, or 100 records per page
- **Navigation**: Previous/Next buttons and page numbers
- **Info**: Shows current page and total records

## Creating a Sales Price Configuration

### Step-by-Step Process

1. **Open Create Form**
   - Click the "Add Sales Price" button
   - A modal dialog will open

2. **Fill Required Fields**
   - **Description**: Enter a clear, descriptive name (required, max 255 characters)
   - **Less (%)**: Enter discount percentage (required, 0.00 - 100.00)

3. **Add Optional Information**
   - **Pricing Note**: Add detailed notes about this pricing configuration (optional, max 1000 characters)

4. **Save Configuration**
   - Click "Create" to save
   - Click "Cancel" to discard changes

### Field Guidelines

#### Description Field
- **Purpose**: Identifies the pricing configuration
- **Examples**:
  - "Bulk Order Discount"
  - "Loyalty Program 10%"
  - "Holiday Sale 2024"
  - "VIP Customer Rate"

#### Less (%) Field
- **Purpose**: Defines the discount percentage
- **Format**: Decimal number with up to 2 decimal places
- **Range**: 0.00 to 100.00
- **Examples**:
  - `10.00` for 10% discount
  - `15.50` for 15.5% discount
  - `0.00` for no discount

#### Pricing Note Field
- **Purpose**: Provides additional context or instructions
- **Examples**:
  - "Applied to orders over 100 units"
  - "Valid for premium customers only"
  - "Seasonal promotion - expires Dec 31"
  - "Requires manager approval for orders over $5000"

## Editing a Sales Price Configuration

1. **Locate Record**
   - Find the configuration in the data table
   - Click the **Edit** button (pencil icon)

2. **Modify Fields**
   - Update any of the fields as needed
   - All validation rules apply

3. **Save Changes**
   - Click "Update" to save changes
   - Click "Cancel" to discard changes

## Deleting a Sales Price Configuration

1. **Locate Record**
   - Find the configuration to delete

2. **Initiate Delete**
   - Click the **Delete** button (trash icon)

3. **Confirm Deletion**
   - A browser confirmation dialog will appear
   - Click "OK" to confirm deletion
   - Click "Cancel" to abort

**⚠️ Warning**: Deletion is permanent and cannot be undone. Make sure you want to delete the configuration before confirming.

## Best Practices

### Naming Conventions

- Use clear, descriptive names that indicate the purpose
- Include percentage in the name when helpful
- Use consistent naming patterns across similar configurations

**Good Examples:**
- "Bulk Discount 15%"
- "Loyalty Program 10%"
- "Seasonal Sale 25%"

**Avoid:**
- "Discount 1", "Discount 2"
- "New Price", "Old Price"
- Unclear abbreviations

### Percentage Guidelines

- Use consistent decimal places (e.g., always 2 decimals)
- Consider business logic for percentage ranges
- Document any special percentage calculations in notes

### Note Management

- Use notes to explain when/how the discount applies
- Include expiration dates for temporary promotions
- Note any special conditions or requirements
- Keep notes concise but informative

## Common Use Cases

### 1. Bulk Order Discounts
```
Description: Bulk Order Discount
Less (%): 15.00
Pricing Note: Applied automatically to orders over 100 units
```

### 2. Customer Loyalty Programs
```
Description: VIP Customer Discount
Less (%): 10.00
Pricing Note: Requires active VIP membership - verified at checkout
```

### 3. Seasonal Promotions
```
Description: Holiday Sale 2024
Less (%): 25.00
Pricing Note: Valid Dec 1-31, 2024. Cannot combine with other offers.
```

### 4. Volume-Based Pricing
```
Description: Wholesale Rate
Less (%): 20.00
Pricing Note: Minimum order quantity: 500 units. Net 30 payment terms required.
```

### 5. Clearance Sales
```
Description: Clearance Discount
Less (%): 50.00
Pricing Note: Final clearance - limited stock available. No returns or exchanges.
```

## Troubleshooting

### Common Issues and Solutions

#### Modal Won't Open
- **Problem**: Create/Edit modal doesn't appear
- **Solution**: Refresh the page and try again. Check browser console for JavaScript errors.

#### Form Won't Submit
- **Problem**: Create/Update button doesn't work
- **Solution**: Check that all required fields are filled. Look for validation error messages in red.

#### Search Not Working
- **Problem**: Search bar doesn't filter results
- **Solution**: Ensure you're typing in the search field. Wait a moment for real-time search to process.

#### Data Not Saving
- **Problem**: Changes aren't saved to database
- **Solution**: Check internet connection. Verify all fields meet validation requirements.

#### Page Not Loading
- **Problem**: Sales Price page won't load
- **Solution**: Check user permissions. Clear browser cache. Try a different browser.

### Error Messages

#### Validation Errors
- **"The description field is required"**: You must enter a description
- **"The less percentage must be between 0 and 100"**: Percentage must be within valid range
- **"The description may not be greater than 255 characters"**: Description is too long

#### System Errors
- **"Connection failed"**: Check internet connection
- **"Unauthorized"**: Contact administrator for access permissions
- **"Server error"**: Contact technical support

## Keyboard Shortcuts

- **Ctrl+F**: Focus search bar (in most browsers)
- **Tab**: Navigate between form fields
- **Enter**: Submit forms (when focused on submit button)
- **Escape**: Close modals (when modal is open)

## Mobile Usage

The Sales Price module is fully responsive and works on mobile devices:

- **Touch Interface**: All buttons and links are touch-friendly
- **Responsive Tables**: Data table scrolls horizontally on small screens
- **Modal Adaptation**: Forms adapt to mobile screen sizes
- **Touch Gestures**: Standard mobile gestures work as expected

## Data Export

Currently, the module doesn't have built-in export functionality. To export data:

1. Use browser's print function (Ctrl+P) to save as PDF
2. Take screenshots for visual records
3. Copy data manually for spreadsheet use

## Security Notes

- All actions are logged and auditable
- Changes require user authentication
- Sensitive pricing information is protected
- Data is encrypted in transit and at rest

## Support

If you encounter issues or need assistance:

1. Check this user guide first
2. Review the troubleshooting section
3. Contact your system administrator
4. Submit a support ticket with:
   - Steps to reproduce the issue
   - Browser and device information
   - Screenshot of any error messages

## Version History

- **v1.0**: Initial release with basic CRUD functionality
- **v1.1**: Added pricing notes field
- **v1.2**: Enhanced search and pagination features

---

**Last Updated**: October 22, 2024
**Module Version**: 1.2