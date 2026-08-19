# 🌐 AK-Mart 2.0 — RESTful API Documentation (v1)

**Base URL**: `http://127.0.0.1:8000/api/v1`  
**Authentication**: Laravel Sanctum Bearer Token (where required)

---

## Endpoints

### 1. Get Product Catalog
- **Method**: `GET /products`
- **Query Parameters**:
  - `q` (string): Keyword search (Name, SKU, Barcode).
  - `category_id` (integer): Filter by Category ID.
  - `per_page` (integer, default 15, max 50): Pagination limit.
- **Response**:
```json
{
  "status": "success",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "name": "Royal Heritage Aged Basmati Rice 5kg",
        "sku": "POS-SKU-99",
        "barcode": "8901234567890",
        "price": "24.99",
        "qty": 26,
        "is_active": true
      }
    ]
  }
}
```

---

### 2. Get Single Product Details & Available Stock
- **Method**: `GET /products/{id}`
- **Response**:
```json
{
  "status": "success",
  "data": {
    "product": {
      "id": 1,
      "name": "Royal Heritage Aged Basmati Rice 5kg",
      "price": "24.99",
      "qty": 26,
      "variants": [],
      "attribute_values": []
    },
    "available_stock": 26
  }
}
```

---

### 3. Get Category List
- **Method**: `GET /categories`
- **Response**:
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "name": "Groceries & Staples",
      "products_count": 8
    }
  ]
}
```

---

### 4. Real-Time Available Stock Status
- **Method**: `GET /inventory/status`
- **Query Parameters**:
  - `product_id` (integer, required): Product ID.
- **Response**:
```json
{
  "status": "success",
  "product_id": 1,
  "available_stock": 26
}
```
