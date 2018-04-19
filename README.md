# Delete orders
Magento 2 extension which enables the store admin to delete orders

## Installation

Using [composer](https://getcomposer.org/):

```bash
composer config repositories.kuzman_deleteorders git https://github.com/vladankuzmanovic/Kuzman_DeleteOrders.git
composer require kuzman/module-delete-orders:dev-master
```

## Enable

```bash
php bin/magento module:enable Kuzman_DeleteOrders --clear-static-content
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento cache:flush
```

## Disable

Enter following commands to disable module:

```bash
php bin/magento module:disable Kuzman_DeleteOrders --clear-static-content    
```

## Technical details

   **Dependencies**
   - Extension depends on Magento_Sales extension
   
   **Database modifications**
   - None
   


