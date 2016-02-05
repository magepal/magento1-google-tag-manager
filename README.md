## Google Tag Manager for Magento 1.x with Data Layer
Google Tag Manager allows you to quickly and easily add or update AdWords, Google Analytics, Facebook Tags and other code snippets on your website without editing any site code.

#Features
Quick and easy setup
Add tag via XML layout
Add tag via event/observer
Data layer support

###Data layer attributes
---------
* pageType (i.e catalog_category_view)
* list (cart, category, detail, other)

####Customer
* customer.isLoggedIn
* customer.id
* customer.groupId

####Category
* category.id
* customer.category

####Product
* product.id
* product.name
* product.sku

####Cart
* cart.hasItems
* cart.items[].sku
* cart.items[].name
* cart.items[].price
* cart.items[].quantity
* cart.total
* cart.itemCount
* cart.hasCoupons
* cart.couponCode

####Transaction
* transactionId
* transactionAffiliation
* transactionTotal
* transactionShipping
* transactionProducts[].sku
* transactionProducts[].name
* transactionProducts[].price
* transactionProducts[].quantity

