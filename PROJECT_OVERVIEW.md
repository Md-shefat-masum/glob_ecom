# Dashboard Project - Complete Overview

## 📋 Table of Contents
1. [Project Structure](#project-structure)
2. [Routes](#routes)
3. [Models](#models)
4. [Migrations](#migrations)
5. [Sidebar Navigation](#sidebar-navigation)
6. [Views Structure](#views-structure)
7. [Key Features](#key-features)

---

## 🏗️ Project Structure

```
dashboard/
├── app/
│   ├── Console/Commands/
│   ├── Exceptions/
│   ├── Http/
│   │   ├── Controllers/ (129 controllers)
│   │   ├── Middleware/ (11 middleware)
│   │   └── Resources/
│   ├── Mail/
│   ├── Models/ (92 models)
│   ├── Providers/
│   └── Services/Analytics/
├── database/
│   ├── migrations/ (142+ migrations)
│   └── seeders/
├── resources/
│   └── views/ (365+ blade files)
├── routes/ (18 route files)
└── public/
```

---

## 🛣️ Routes

### Main Route File (`routes/web.php`)
Central route file that includes all module-specific route files:

- `authRoutes.php` - Authentication routes
- `dashboardRoutes.php` - Dashboard routes
- `paymentRoutes.php` - Payment processing
- `ecommerceRoutes.php` - E-commerce functionality
- `inventoryRoutes.php` - Inventory management
- `accountRoutes.php` - Accounting module
- `crmRoutes.php` - CRM functionality
- `productManagementRoutes.php` - Product management
- `rolePermissionRoutes.php` - User roles & permissions
- `WebConfigRoutes.php` - Website configuration
- `cmsRoutes.php` - Content management
- `generalRoutes.php` - General utilities
- `mediaRoutes.php` - Media management
- `stockAdjustmentRoutes.php` - Stock adjustments
- `analyticsRoutes.php` - Analytics & reporting
- `pos_desktop_route.php` - POS system

### Key Route Groups

#### Dashboard Routes (`dashboardRoutes.php`)
```php
Route::get('/', [HomePageAnalytics::class, 'index'])->name('home');
Route::get('/home/analytics/data', [HomePageAnalytics::class, 'summary'])->name('home.analytics');
Route::get('/crm-home', [HomeController::class, 'crm_index'])->name('crm.home');
Route::get('/accounts-home', [HomeController::class, 'accounts_index'])->name('accounts.home');
Route::get('/inventory-home', [HomeController::class, 'inventory_dashboard'])->name('inventory.home');
```

#### Product Management Routes (`productManagementRoutes.php`)
- CRUD operations for products
- Filter and search functionality
- Variant management
- Unit pricing
- Bulk operations
- AJAX endpoints for dynamic data

#### Inventory Routes (`inventoryRoutes.php`)
- Warehouse management (warehouses, rooms, cartons)
- Supplier management
- Purchase orders & quotations
- Purchase returns
- Product orders
- Stock management

#### CRM Routes (`crmRoutes.php`)
- Customer management
- Customer categories & source types
- Contact history
- Next contact dates
- SMS management

---

## 📦 Models (92 Models)

### Core E-commerce Models
- `Product` - Main product model
- `Category`, `Subcategory`, `ChildCategory` - Category hierarchy
- `Brand`, `ProductModel` - Product attributes
- `Order`, `OrderDetails` - Order management
- `Cart`, `WishList` - Shopping features
- `ProductReview`, `ProductQuestionAnswer` - Customer engagement

### Inventory Models
- `ProductWarehouse` - Warehouse management
- `ProductWarehouseRoom` - Room management
- `ProductWarehouseRoomCartoon` - Carton management
- `ProductSupplier` - Supplier management
- `ProductStock` - Stock tracking
- `ProductStockLog` - Stock movement logs
- `ProductPurchaseOrder` - Purchase orders
- `ProductPurchaseQuotation` - Quotations
- `ProductPurchaseReturn` - Purchase returns

### Product Variant Models
- `ProductStockVariantGroup` - Variant groups
- `ProductStockVariantsGroupKey` - Variant keys
- `ProductVariantCombination` - Variant combinations
- `ProductFilterAttribute` - Filter attributes
- `ProductFilterAttributeMapping` - Attribute mappings
- `ProductUnitPricing` - Unit-based pricing

### Order & Sales Models
- `ProductOrder` - POS orders
- `ProductOrderProduct` - Order items
- `ProductOrderReturn` - Order returns
- `ProductOrderReturnProduct` - Return items
- `ManualProductReturn` - Manual returns
- `ProductOrderHold` - Order holds

### CRM Models
- `Customer` - Customer management
- `CustomerCategory` - Customer categorization
- `CustomerContactHistory` - Contact tracking
- `CustomerNextContactDate` - Scheduled contacts

### Accounting Models
- `AcAccount` - Chart of accounts
- `AcTransaction` - Financial transactions
- `AcMoneyDeposit` - Deposits
- `AcMoneyTransfer` - Money transfers
- `DbExpense` - Expenses
- `DbExpenseCategory` - Expense categories
- `DbCustomerPayment` - Customer payments
- `DbSupplierPayment` - Supplier payments

### Media & Content Models
- `Media` - Media files
- `MediaFile` - File management
- `MediaFolder` - Folder structure
- `Blog`, `BlogCategory` - Blog management
- `Banner`, `PromotionalBanner` - Banner management

### User & Permission Models
- `User` - User accounts
- `UserRole` - User roles
- `UserRolePermission` - Role permissions
- `RolePermission` - Permission definitions
- `PermissionRoutes` - Route permissions

### Analytics Models
- `ProductDemandPrediction` - Demand forecasting
- `ProductView` - Product views tracking
- `UserActivity` - User activity logs

### Package Products
- `PackageProduct` - Package products
- `PackageProductItem` - Package items

---

## 🗄️ Migrations (142+ Migrations)

### Core Tables
- `users` - User accounts
- `products` - Product catalog
- `categories`, `subcategories`, `child_categories` - Category structure
- `orders`, `order_details` - E-commerce orders
- `product_orders` - POS orders

### Inventory Tables
- `product_warehouses` - Warehouse locations
- `product_warehouse_rooms` - Warehouse rooms
- `product_warehouse_room_cartoons` - Room cartons
- `product_suppliers` - Supplier information
- `product_stocks` - Stock levels
- `product_stock_logs` - Stock movement history
- `product_purchase_orders` - Purchase orders
- `product_purchase_quotations` - Purchase quotations
- `product_purchase_returns` - Purchase returns

### Variant System Tables
- `product_stock_variant_groups` - Variant groups
- `product_stock_variants_group_keys` - Variant keys
- `product_variant_combinations` - Variant combinations
- `product_filter_attributes` - Filter attributes
- `product_filter_attribute_mappings` - Attribute mappings
- `product_unit_pricing` - Unit pricing

### CRM Tables
- `customers` - Customer database
- `customer_categories` - Customer categories
- `customer_source_types` - Customer sources
- `customer_contact_histories` - Contact history
- `customer_next_contact_dates` - Scheduled contacts

### Accounting Tables
- `ac_accounts` - Chart of accounts
- `ac_transactions` - Financial transactions
- `ac_money_deposits` - Deposits
- `ac_money_transfers` - Transfers
- `db_expenses` - Expenses
- `db_expense_categories` - Expense categories
- `db_customer_payments` - Customer payments
- `db_supplier_payments` - Supplier payments

### Analytics Tables
- `product_demand_predictions` - Demand forecasts
- `product_views` - Product view tracking
- `user_activities` - User activity logs

### Recent Migrations (2025)
- Variant system enhancements
- Package product support
- Demand prediction system
- API field additions
- Order hold functionality
- Media management system

---

## 🎨 Sidebar Navigation

The sidebar (`resources/views/backend/sidebar.blade.php`) is organized into sections:

### 📊 DASHBOARDS
- E-commerce Dashboard (`/home`)
- Analytics Dashboard (`/analytics/dashboard`)
- CRM Dashboard (`/crm-home`)
- Accounts Dashboard (`/accounts-home`)
- Inventory Dashboard (`/inventory-home`)

### 📦 PRODUCT MANAGEMENT
- **Products**
  - All Products
  - Create New Product
  - Package Products
  - Barcode Generator
- **Categories**
  - Categories
  - Subcategories
  - Child Categories
- **Attributes**
  - Measurement Units
  - Brands
  - Models
  - Flags
  - Warranties
  - Variant Management
- **Reviews & Q/A**
  - Product Reviews
  - Questions/Answers

### 🛒 SALES & ORDERS
- All Orders
- Create Order
- POS System
- Returns (Order Returns, Manual Returns)
- Customer Payments
- Promo Codes
- Customer Wishlist
- Delivery Charges
- Upazila & Thana

### 📦 INVENTORY & STOCK
- **Warehouse**
  - Warehouses
  - Warehouse Rooms
  - Room Cartons
- **Stock Adjustment**
  - Adjustment Logs
  - New Adjustment
- **Suppliers**
  - Supplier Types
  - All Suppliers
- **Purchase**
  - Charge Types
  - Quotations
  - Purchase Orders
  - Purchase Returns

### 💰 ACCOUNTS & FINANCE
- Accounts
- Expenses (Categories, All Expenses)
- Deposits
- Payment Types
- Financial Reports (Journal, Ledger, Balance Sheet, Income Statement, Purchase Report)

### 👥 CRM & CUSTOMERS
- **Customers**
  - All Customers
  - E-commerce Customers
  - Customer Categories
  - Customer Source Types
- **Contact Management**
  - Contact History
  - Scheduled Contacts
  - Contact Requests
- Newsletter Subscribers
- SMS Management

### 🔐 USER MANAGEMENT
- System Users
- Roles & Permissions (User Roles, Assign Permissions, Permission Routes)

### 🌐 WEBSITE & CONTENT
- **Blog Management**
  - Write New Blog
  - All Blogs
  - Blog Categories
- **Pages & Policies**
  - Custom Pages
  - About Us
  - Terms & Conditions
  - Privacy Policy
  - Shipping Policy
  - Return Policy

### ⚙️ SETTINGS
- General Information
- Social Media Links
- SEO Settings
- Custom CSS & JS
- Chat & Social Scripts

---

## 👁️ Views Structure

### Main Views Directory
`resources/views/backend/` contains 365+ blade files organized by module:

### Dashboard Views
- `dashboard.blade.php` - Main analytics dashboard
- `crm-dashboard.blade.php` - CRM dashboard
- `accounts-dashboard.blade.php` - Accounts dashboard
- `inventory-dashboard.blade.php` - Inventory dashboard

### Product Management Views
- `product/` - Product CRUD views
- `product/barcode_gen.blade.php` - Barcode generator
- Category, Subcategory, Child Category views
- Brand, Model, Unit views

### Inventory Views
- `inventory/` - Warehouse, supplier, purchase order views
- Stock management views
- Purchase quotation and order views

### CRM Views
- `customer/` - Customer management views
- Contact history and scheduling views
- Customer category and source type views

### Accounting Views
- `account/` - Account management
- Transaction views
- Financial report views (Journal, Ledger, Balance Sheet, Income Statement)

### E-commerce Views
- `order/` - Order management
- `order/product-order/` - POS order views
- Return management views

### System Views
- `user/` - User management
- `role/` - Role and permission management
- `settings/` - System settings

### Layout Files
- `master.blade.php` - Main layout template
- `sidebar.blade.php` - Sidebar navigation
- Header and footer components

---

## ✨ Key Features

### 1. Multi-Dashboard System
- 5 specialized dashboards (E-commerce, Analytics, CRM, Accounts, Inventory)
- Real-time analytics with date range filtering
- AJAX-powered data loading

### 2. Advanced Product Management
- Full CRUD operations
- Variant system with groups and combinations
- Unit-based pricing
- Package products
- Barcode generation
- Stock tracking with logs

### 3. Inventory Management
- Multi-level warehouse structure (Warehouse → Room → Carton)
- Supplier management
- Purchase order workflow (Quotation → Order → Return)
- Stock adjustments
- Low stock alerts

### 4. CRM System
- Customer database management
- Contact history tracking
- Scheduled contact dates
- Customer categorization
- Source type tracking

### 5. Accounting Module
- Double-entry bookkeeping
- Chart of accounts
- Financial transactions
- Expense management
- Financial reports (Journal, Ledger, Balance Sheet, Income Statement)

### 6. Order Management
- E-commerce orders
- POS orders
- Order returns (automatic and manual)
- Customer payments
- Payment tracking

### 7. Analytics & Reporting
- Demand prediction system
- Product view tracking
- User activity logs
- Sales analytics
- Financial reports

### 8. User & Permission System
- Role-based access control
- Permission management
- Route-level permissions
- User activity tracking

### 9. Media Management
- File upload system
- Folder structure
- Media library

### 10. Content Management
- Blog system
- Custom pages
- Banner management
- SEO settings

---

## 🔧 Technical Stack

- **Framework:** Laravel (PHP)
- **Frontend:** Blade Templates, Bootstrap, Chart.js
- **Database:** MySQL
- **AJAX:** Axios
- **Charts:** Chart.js 2.9.4
- **Icons:** Feather Icons

---

## 📝 Notes

- All routes are protected by authentication middleware
- Most routes also use `CheckUserType` and `DemoMode` middleware
- The system supports both e-commerce and POS order types
- Variant system allows complex product configurations
- Demand prediction system is optional (configurable)
- Media management supports folder-based organization
- Accounting follows double-entry bookkeeping principles

---

## 🚀 Getting Started

1. Configure `.env` file with database credentials
2. Run migrations: `php artisan migrate`
3. Seed database (optional): `php artisan db:seed`
4. Access dashboard at `/home`
5. Default user credentials depend on seeder configuration

---

*Last Updated: Based on current codebase structure*

