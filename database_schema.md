# Database Schema Documentation

## Table: users
- **Description**: Stores user accounts for authentication and role-based access control.
- **Primary Key**: `id`
- **Columns**:
  - `name`: string
  - `username`: string (unique)
  - `email`: string (nullable)
  - `password`: string
  - `phone`: string (unique)
  - `address`: string (nullable)
  - `profile_picture`: string (nullable)
  - `role`: enum ('admin', 'user', 'staff', 'rep') - Default: 'user'
  - `remember_token`: string
  - `timestamps`: created_at, updated_at

## Table: suppliers
- **Description**: Stores information about product suppliers.
- **Primary Key**: `id`
- **Columns**:
  - `name`: string
  - `contactno`: string (nullable)
  - `address`: string (nullable)
  - `timestamps`: created_at, updated_at

## Table: products
- **Description**: Master catalog of all products.
- **Primary Key**: `id`
- **Columns**:
  - `material_code`: string (unique)
  - `name`: string
  - `category`: string
  - `timestamps`: created_at, updated_at

## Table: supplier_invoices
- **Description**: Records of invoices received from suppliers.
- **Primary Key**: `id`
- **Columns**:
  - `invoice_number`: string (unique)
  - `invoice_date`: date
  - `total_bill_amount`: decimal(17, 2)
  - `discount`: decimal(10, 2) (nullable)
  - `supplier_id`: foreignId (constrained to `suppliers.id`) [Delete Constraint: Cascade]
  - `timestamps`: created_at, updated_at

## Table: batch__stocks
- **Description**: Individual batches of stock received from supplier invoices. Tracks expiry and pricing per batch.
- **Primary Key**: `id`
- **Columns**:
  - `product_id`: foreignId (constrained to `products.id`)
  - `supplier_invoice_id`: foreignId (constrained to `supplier_invoices.id`)
  - `no_cases`: integer
  - `pack_size`: integer
  - `qty`: integer
  - `retail_price`: decimal(10, 2)
  - `netprice`: decimal(10, 2)
  - `expiry_date`: date (nullable)
  - `timestamps`: created_at, updated_at

## Table: routes
- **Description**: Delivery routes for distribution.
- **Primary Key**: `id`
- **Columns**:
  - `route_code`: string (unique)
  - `route_description`: string
  - `timestamps`: created_at, updated_at

## Table: trucks
- **Description**: Delivery vehicles.
- **Primary Key**: `id`
- **Columns**:
  - `licence_plate_no`: string (unique)
  - `description`: string (nullable)
  - `timestamps`: created_at, updated_at

## Table: employees
- **Description**: Staff members such as drivers and helpers.
- **Primary Key**: `id`
- **Columns**:
  - `name`: string
  - `nic`: string (unique)
  - `role`: enum ('warehouse_helper', 'cash_collecter', 'helper', 'driver')
  - `phoneno`: string
  - `timestamps`: created_at, updated_at

## Table: shops
- **Description**: Retail shops/customers on specific routes.
- **Primary Key**: `id`
- **Columns**:
  - `shop_code`: string (unique)
  - `shop_name`: string
  - `address`: string (nullable)
  - `phoneno`: string (nullable)
  - `route_code`: string (Foreign Key referencing `routes.route_code`) [Delete Constraint: Cascade]
  - `timestamps`: created_at, updated_at

## Table: sales_reps
- **Description**: Sales representatives assigned to routes and suppliers.
- **Primary Key**: `id`
- **Columns**:
  - `rep_id`: string
  - `name`: string
  - `contact`: string (nullable)
  - `join_date`: date (nullable)
  - `supplier_id`: foreignId (constrained to `suppliers.id`) [Delete Constraint: Cascade]
  - `route_id`: foreignId (constrained to `routes.id`) [Delete Constraint: Cascade]
  - `timestamps`: created_at, updated_at

## Table: loadings
- **Description**: Loading sheets/manifests for delivery trips using trucks on specific routes.
- **Primary Key**: `id`
- **Columns**:
  - `load_number`: string
  - `prepared_date`: date (nullable)
  - `loading_date`: date (nullable)
  - `status`: enum ('pending', 'delivered', 'not_delivered') - Default: 'pending'
  - `truck_id`: foreignId (constrained to `trucks.id`) [Delete Constraint: Cascade]
  - `route_id`: foreignId (constrained to `routes.id`) [Delete Constraint: Cascade]
  - `timestamps`: created_at, updated_at

## Table: load_list_items
- **Description**: Individual items (stock batches) added to a loading manifest.
- **Primary Key**: `id`
- **Columns**:
  - `qty`: integer
  - `free_qty`: integer (nullable)
  - `wh_price`: double (nullable)
  - `net_price`: double (nullable)
  - `loading_id`: foreignId (constrained to `loadings.id`) [Delete Constraint: Cascade]
  - `batch_id`: foreignId (constrained to `batch__stocks.id`) [Delete Constraint: Cascade]
  - `timestamps`: created_at, updated_at
