# DarshanDomadiya_CustomOrderProcessing Module for Magento 2

## Overview
The **DarshanDomadiya_CustomOrderProcessing** module enhances the Magento 2 order processing workflow by implementing:
- A **custom API endpoint** to update order statuses.
- An **event observer** to track order status changes.
- A **database logging system** to store order status updates.
- An **email notification system** for shipped orders.

## Features
✅ **Order Status Update via API** - Allows external systems to update order status.
✅ **Event Observer** - Captures order status changes and logs them.
✅ **Database Logging** - Stores order status history in a custom table.
✅ **Email Notifications** - Sends an email when an order is marked as shipped.
✅ **Magento 2 Best Practices** - Uses Dependency Injection, PSR-4, and repository patterns.

## Installation
### 1. Upload the Module
Copy the module files to the Magento 2 directory:
```
app/code/DarshanDomadiya/CustomOrderProcessing/
```

### 2. Enable the Module
Run the following commands in your Magento 2 root directory:
```
php bin/magento module:enable DarshanDomadiya_CustomOrderProcessing
php bin/magento setup:upgrade
php bin/magento cache:flush
```

### 3. Verify Installation
Check if the module is enabled:
```
php bin/magento module:status | grep DarshanDomadiya_CustomOrderProcessing
```

## API Usage: Update Order Status
### Endpoint:
```
POST /rest/V1/customorderprocessing/updatestatus
```

### Request Payload (JSON):
```json
{
  "order_increment_id": "100000001",
  "new_status": "complete"
}
```

### Authentication
Use a **Bearer Token** for API authentication. Generate an access token via:
```
curl -X POST "https://your-magento-site.com/rest/V1/integration/admin/token" \
  -H "Content-Type: application/json" \
  -d '{"username":"admin", "password":"admin_password"}'
```

Use the token in API requests:
```
curl -X POST "https://your-magento-site.com/rest/V1/customorderprocessing/updatestatus" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer your_access_token" \
  -d '{"order_increment_id": "100000001", "new_status": "complete"}'
```

## Event Observer: Order Status Change
### Trigger:
- Listens to `sales_order_save_after` event.
- Logs order status changes in the `order_status_log` table.
- Sends email notifications if an order is marked as "shipped".

## Database Table: `order_status_log`
| Column       | Type        | Description             |
|-------------|------------|-------------------------|
| id          | INT (PK)    | Auto-increment ID       |
| order_id    | INT        | Magento Order ID        |
| old_status  | VARCHAR    | Previous order status   |
| new_status  | VARCHAR    | Updated order status    |
| timestamp   | DATETIME   | Status update time      |

## Customization
You can modify email templates by editing:
```
app/design/frontend/DarshanDomadiya/theme/Magento_Sales/email/order_shipped.html
```

## Uninstallation
To remove the module, run:
```
php bin/magento module:disable DarshanDomadiya_CustomOrderProcessing
rm -rf app/code/DarshanDomadiya/CustomOrderProcessing
php bin/magento setup:upgrade
php bin/magento cache:flush
```

## Contributing
Feel free to fork this module and improve it. Contributions are welcome!

## License
This module is open-source and follows the MIT license.

## Support
For issues or improvements, please create an issue or reach out to the developer team.

