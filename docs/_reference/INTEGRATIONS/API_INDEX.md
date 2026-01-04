# API_INDEX

این سند فهرست APIهای نسخه v1 را با scopeها، permissionها، نرخ‌محدودیت و نمونه Request/Response ارائه می‌کند.
نمونه‌ها tenant-safe هستند و نحوه احراز هویت و tenant resolution در هر endpoint مشخص شده است.

## blog
### GET /api/v1/blog/categories
- Scope: `blog.category.manage`
- Permission: `blog.category.manage`
- Rate limit: `60,1 (config: blog.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/blog/categories?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/blog/categories
- Scope: `blog.category.manage`
- Permission: `blog.category.manage`
- Rate limit: `60,1 (config: blog.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/blog/categories
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "site_id": 1,
  "name": "دسته‌بندی نمونه",
  "slug": "sample-category",
  "description": "توضیح کوتاه"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/blog/categories/{category}
- Scope: `blog.category.manage`
- Permission: `blog.category.manage`
- Rate limit: `60,1 (config: blog.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/blog/categories/{category}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/blog/categories/{category}
- Scope: `blog.category.manage`
- Permission: `blog.category.manage`
- Rate limit: `60,1 (config: blog.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/blog/categories/{category}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/blog/categories/{category}
- Scope: `blog.category.manage`
- Permission: `blog.category.manage`
- Rate limit: `60,1 (config: blog.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/blog/categories/{category}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "دسته‌بندی به‌روزشده",
  "slug": "sample-category-updated",
  "description": "توضیح به‌روزشده"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/blog/categories/{category}
- Scope: `blog.category.manage`
- Permission: `blog.category.manage`
- Rate limit: `60,1 (config: blog.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/blog/categories/{category}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "دسته‌بندی به‌روزشده",
  "slug": "sample-category-updated",
  "description": "توضیح به‌روزشده"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/blog/openapi
- Scope: `blog.post.view`
- Permission: `blog.post.view`
- Rate limit: `60,1 (config: blog.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/blog/openapi?page=1
```

Response:
```json
{
  "openapi": "3.0.0",
  "info": {
    "title": "Module API",
    "version": "1.0.0"
  }
}
```

### POST /api/v1/blog/posts
- Scope: `blog.post.manage`
- Permission: `blog.post.manage`
- Rate limit: `60,1 (config: blog.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/blog/posts
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "site_id": 1,
  "category_id": 2,
  "title": "عنوان پست",
  "slug": "sample-post",
  "excerpt": "خلاصه کوتاه",
  "seo": {
    "title": "عنوان سئو",
    "description": "توضیح سئو"
  },
  "draft_content": "متن پیش‌نویس",
  "status": "draft",
  "tags": [3, 4]
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/blog/posts/{post}
- Scope: `blog.post.manage`
- Permission: `blog.post.manage`
- Rate limit: `60,1 (config: blog.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/blog/posts/{post}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### PATCH /api/v1/blog/posts/{post}
- Scope: `blog.post.manage`
- Permission: `blog.post.manage`
- Rate limit: `60,1 (config: blog.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/blog/posts/{post}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "title": "عنوان به‌روزشده",
  "slug": "sample-post-updated",
  "excerpt": "خلاصه جدید",
  "draft_content": "متن جدید",
  "status": "published",
  "category_id": 2,
  "tags": [3]
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/blog/posts/{post}
- Scope: `blog.post.manage`
- Permission: `blog.post.manage`
- Rate limit: `60,1 (config: blog.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/blog/posts/{post}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "title": "عنوان به‌روزشده",
  "slug": "sample-post-updated",
  "excerpt": "خلاصه جدید",
  "draft_content": "متن جدید",
  "status": "published",
  "category_id": 2,
  "tags": [3]
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/blog/tags
- Scope: `blog.post.view`
- Permission: `blog.post.view`
- Rate limit: `60,1 (config: blog.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/blog/tags?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/blog/tags
- Scope: `blog.post.view`
- Permission: `blog.post.view`
- Rate limit: `60,1 (config: blog.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/blog/tags
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "site_id": 1,
  "name": "برچسب نمونه",
  "slug": "sample-tag"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/blog/tags/{tag}
- Scope: `blog.post.view`
- Permission: `blog.post.view`
- Rate limit: `60,1 (config: blog.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/blog/tags/{tag}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/blog/tags/{tag}
- Scope: `blog.post.view`
- Permission: `blog.post.view`
- Rate limit: `60,1 (config: blog.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/blog/tags/{tag}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/blog/tags/{tag}
- Scope: `blog.post.view`
- Permission: `blog.post.view`
- Rate limit: `60,1 (config: blog.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/blog/tags/{tag}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "برچسب به‌روزشده",
  "slug": "sample-tag-updated"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/blog/tags/{tag}
- Scope: `blog.post.view`
- Permission: `blog.post.view`
- Rate limit: `60,1 (config: blog.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/blog/tags/{tag}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "برچسب به‌روزشده",
  "slug": "sample-tag-updated"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

## commerce-catalog
### GET /api/v1/commerce-catalog/collections
- Scope: `catalog.collection.manage`
- Permission: `catalog.collection.manage`
- Rate limit: `60,1 (config: commerce-catalog.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/commerce-catalog/collections?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/commerce-catalog/collections
- Scope: `catalog.collection.manage`
- Permission: `catalog.collection.manage`
- Rate limit: `60,1 (config: commerce-catalog.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/commerce-catalog/collections
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "site_id": 1,
  "name": "کالکشن تابستان ۱۴۰۳",
  "slug": "summer-1403",
  "status": "published",
  "description": "محصولات فصل تابستان",
  "products": [10, 11]
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/commerce-catalog/collections/{collection}
- Scope: `catalog.collection.manage`
- Permission: `catalog.collection.manage`
- Rate limit: `60,1 (config: commerce-catalog.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/commerce-catalog/collections/{collection}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/commerce-catalog/collections/{collection}
- Scope: `catalog.collection.manage`
- Permission: `catalog.collection.manage`
- Rate limit: `60,1 (config: commerce-catalog.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/commerce-catalog/collections/{collection}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/commerce-catalog/collections/{collection}
- Scope: `catalog.collection.manage`
- Permission: `catalog.collection.manage`
- Rate limit: `60,1 (config: commerce-catalog.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/commerce-catalog/collections/{collection}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "کالکشن به‌روزشده",
  "slug": "summer-1403-updated",
  "status": "draft",
  "description": "بازنگری در کالکشن",
  "products": [10]
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/commerce-catalog/collections/{collection}
- Scope: `catalog.collection.manage`
- Permission: `catalog.collection.manage`
- Rate limit: `60,1 (config: commerce-catalog.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/commerce-catalog/collections/{collection}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "کالکشن به‌روزشده",
  "slug": "summer-1403-updated",
  "status": "draft",
  "description": "بازنگری در کالکشن",
  "products": [10]
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/commerce-catalog/openapi
- Scope: `catalog.product.view`
- Permission: `catalog.product.view`
- Rate limit: `60,1 (config: commerce-catalog.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/commerce-catalog/openapi?page=1
```

Response:
```json
{
  "openapi": "3.0.0",
  "info": {
    "title": "Module API",
    "version": "1.0.0"
  }
}
```

### POST /api/v1/commerce-catalog/products
- Scope: `catalog.product.manage`
- Permission: `catalog.product.manage`
- Rate limit: `60,1 (config: commerce-catalog.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/commerce-catalog/products
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "site_id": 1,
  "name": "کالای نمونه",
  "slug": "sample-product",
  "type": "physical",
  "status": "published",
  "sku": "SKU-1001",
  "summary": "خلاصه محصول",
  "description": "توضیح کامل محصول",
  "currency": "IRR",
  "price": 150000,
  "compare_at_price": 180000,
  "track_inventory": true,
  "accounting_product_id": 5,
  "inventory_item_id": 12,
  "metadata": {
    "origin": "IR"
  },
  "collections": [2, 3]
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/commerce-catalog/products/{product}
- Scope: `catalog.product.manage`
- Permission: `catalog.product.manage`
- Rate limit: `60,1 (config: commerce-catalog.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/commerce-catalog/products/{product}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### PATCH /api/v1/commerce-catalog/products/{product}
- Scope: `catalog.product.manage`
- Permission: `catalog.product.manage`
- Rate limit: `60,1 (config: commerce-catalog.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/commerce-catalog/products/{product}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "کالای به‌روزشده",
  "slug": "sample-product-updated",
  "status": "draft",
  "price": 175000,
  "compare_at_price": 190000,
  "track_inventory": false,
  "collections": [2]
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/commerce-catalog/products/{product}
- Scope: `catalog.product.manage`
- Permission: `catalog.product.manage`
- Rate limit: `60,1 (config: commerce-catalog.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/commerce-catalog/products/{product}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "کالای به‌روزشده",
  "slug": "sample-product-updated",
  "status": "draft",
  "price": 175000,
  "compare_at_price": 190000,
  "track_inventory": false,
  "collections": [2]
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

## commerce-checkout
### DELETE /api/v1/commerce-checkout/cart-items/{item}
- Scope: `commerce.cart.manage`
- Permission: `commerce.cart.manage`
- Rate limit: `60,1 (config: commerce-checkout.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/commerce-checkout/cart-items/{item}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### PATCH /api/v1/commerce-checkout/cart-items/{item}
- Scope: `commerce.cart.manage`
- Permission: `commerce.cart.manage`
- Rate limit: `60,1 (config: commerce-checkout.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/commerce-checkout/cart-items/{item}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "quantity": 1
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/commerce-checkout/carts
- Scope: `commerce.cart.manage`
- Permission: `commerce.cart.manage`
- Rate limit: `60,1 (config: commerce-checkout.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/commerce-checkout/carts
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "site_id": 1
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/commerce-checkout/carts/{cart}/items
- Scope: `commerce.cart.manage`
- Permission: `commerce.cart.manage`
- Rate limit: `60,1 (config: commerce-checkout.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/commerce-checkout/carts/{cart}/items
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "product_id": 1,
  "variant_id": 10,
  "quantity": 1,
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/commerce-checkout/checkout
- Scope: `commerce.checkout.create`
- Permission: `commerce.checkout.create`
- Rate limit: `60,1 (config: commerce-checkout.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/commerce-checkout/checkout
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "cart_id": 1,
  "payment_method": "wallet",
  "idempotency_key": "demo-key",
  "payment_idempotency_key": "payment-key",
  "customer_name": "Demo Customer",
  "customer_email": "demo@example.com",
  "customer_phone": "+989121234567",
  "billing_address": {},
  "shipping_address": {},
  "customer_note": "note",
  "internal_note": "internal",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/commerce-checkout/openapi
- Scope: `commerce.cart.view`
- Permission: `commerce.cart.view`
- Rate limit: `60,1 (config: commerce-checkout.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/commerce-checkout/openapi?page=1
```

Response:
```json
{
  "openapi": "3.0.0",
  "info": {
    "title": "Module API",
    "version": "1.0.0"
  }
}
```

## commerce-orders
### GET /api/v1/commerce-orders/openapi
- Scope: `commerce.order.view`
- Permission: `commerce.order.view`
- Rate limit: `60,1 (config: commerce-orders.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/commerce-orders/openapi?page=1
```

Response:
```json
{
  "openapi": "3.0.0",
  "info": {
    "title": "Module API",
    "version": "1.0.0"
  }
}
```

### PATCH /api/v1/commerce-orders/orders/{order}
- Scope: `commerce.order.manage`
- Permission: `commerce.order.manage`
- Rate limit: `60,1 (config: commerce-orders.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/commerce-orders/orders/{order}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "status": "processing",
  "payment_status": "paid",
  "internal_note": "به‌روزرسانی وضعیت سفارش"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/commerce-orders/orders/{order}
- Scope: `commerce.order.manage`
- Permission: `commerce.order.manage`
- Rate limit: `60,1 (config: commerce-orders.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/commerce-orders/orders/{order}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "status": "processing",
  "payment_status": "paid",
  "internal_note": "به‌روزرسانی وضعیت سفارش"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

## content-cms
### GET /
- Scope: `public`
- Permission: `public`
- Rate limit: `60,1`
- Tenant: `false`
- Auth: `public/none`

Request:
```http
GET /?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### GET /api/v1/content-cms/openapi
- Scope: `cms.page.view`
- Permission: `cms.page.view`
- Rate limit: `60,1 (config: content-cms.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/content-cms/openapi?page=1
```

Response:
```json
{
  "openapi": "3.0.0",
  "info": {
    "title": "Module API",
    "version": "1.0.0"
  }
}
```

### POST /api/v1/content-cms/pages
- Scope: `cms.page.manage`
- Permission: `cms.page.manage`
- Rate limit: `60,1 (config: content-cms.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/content-cms/pages
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "site_id": 1,
  "slug": "about-us",
  "title": "درباره ما",
  "seo": {
    "title": "درباره ما",
    "description": "معرفی کوتاه"
  },
  "draft_content": {
    "blocks": []
  },
  "status": "draft"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/content-cms/pages/{page}
- Scope: `cms.page.manage`
- Permission: `cms.page.manage`
- Rate limit: `60,1 (config: content-cms.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/content-cms/pages/{page}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### PATCH /api/v1/content-cms/pages/{page}
- Scope: `cms.page.manage`
- Permission: `cms.page.manage`
- Rate limit: `60,1 (config: content-cms.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/content-cms/pages/{page}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "title": "درباره ما (به‌روزشده)",
  "slug": "about-us-updated",
  "draft_content": {
    "blocks": []
  },
  "status": "published"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/content-cms/pages/{page}
- Scope: `cms.page.manage`
- Permission: `cms.page.manage`
- Rate limit: `60,1 (config: content-cms.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/content-cms/pages/{page}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "title": "درباره ما (به‌روزشده)",
  "slug": "about-us-updated",
  "draft_content": {
    "blocks": []
  },
  "status": "published"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /{slug}
- Scope: `public`
- Permission: `public`
- Rate limit: `60,1`
- Tenant: `false`
- Auth: `public/none`

Request:
```http
GET /{slug}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

## filamat-iam-suite
### GET /api/v1/groups
- Scope: `iam`
- Permission: `iam`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/groups?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/groups
- Scope: `iam`
- Permission: `iam`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/groups
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "گروه پشتیبانی",
  "description": "گروه تیم پشتیبانی"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/groups/{group}
- Scope: `iam`
- Permission: `iam`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/groups/{group}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/groups/{group}
- Scope: `iam`
- Permission: `iam`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/groups/{group}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/groups/{group}
- Scope: `iam`
- Permission: `iam`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/groups/{group}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "گروه پشتیبانی (به‌روزشده)",
  "description": "ویرایش توضیحات گروه"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/groups/{group}
- Scope: `iam`
- Permission: `iam`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/groups/{group}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "گروه پشتیبانی (به‌روزشده)",
  "description": "ویرایش توضیحات گروه"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/iam/impersonations/start
- Scope: `iam.impersonate`
- Permission: `iam.impersonate`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/iam/impersonations/start
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "user_id": 15,
  "tenant_id": 1,
  "reason": "بررسی درخواست مشتری",
  "ticket_id": "TCK-1024",
  "ttl_minutes": 60,
  "restricted": true,
  "totp": "123456"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/iam/impersonations/stop
- Scope: `iam.impersonate`
- Permission: `iam.impersonate`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/iam/impersonations/stop
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "reason": "اتمام بررسی"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/iam/invitations
- Scope: `user.invite`
- Permission: `user.invite`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/iam/invitations
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "email": "user@example.com",
  "name": "کاربر جدید",
  "roles": ["member"],
  "permissions": ["workhub.project.view"],
  "reason": "دعوت به تیم",
  "expires_at": "2026-02-01"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/iam/invitations/{invitation}
- Scope: `user.view`
- Permission: `user.view`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/iam/invitations/{invitation}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### POST /api/v1/iam/invitations/{invitation}/accept
- Scope: `user.invite`
- Permission: `user.invite`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/iam/invitations/{invitation}/accept
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "token": "invitation-token"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/iam/invitations/{invitation}/revoke
- Scope: `user.invite`
- Permission: `user.invite`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/iam/invitations/{invitation}/revoke
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "reason": "لغو دعوت"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/iam/mfa/totp/confirm
- Scope: `mfa.manage`
- Permission: `mfa.manage`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/iam/mfa/totp/confirm
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "code": "123456"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/iam/mfa/totp/reset
- Scope: `mfa.reset`
- Permission: `mfa.reset`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/iam/mfa/totp/reset
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "reason": "تعویض دستگاه",
  "totp": "123456"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/iam/mfa/totp/start
- Scope: `mfa.manage`
- Permission: `mfa.manage`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/iam/mfa/totp/start
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/iam/n8n/callback
- Scope: `automation.manage`
- Permission: `automation.manage`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/iam/n8n/callback
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/iam/privilege-activations
- Scope: `pam`
- Permission: `pam`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/iam/privilege-activations?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/iam/privilege-activations
- Scope: `pam.activate`
- Permission: `pam.activate`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/iam/privilege-activations
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/iam/privilege-activations/{activation}/revoke
- Scope: `pam.revoke`
- Permission: `pam.revoke`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/iam/privilege-activations/{activation}/revoke
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/iam/privilege-activations/{privilege-activation}
- Scope: `pam`
- Permission: `pam`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/iam/privilege-activations/{privilege-activation}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### GET /api/v1/iam/privilege-eligibilities
- Scope: `pam`
- Permission: `pam`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/iam/privilege-eligibilities?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/iam/privilege-eligibilities
- Scope: `pam`
- Permission: `pam`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/iam/privilege-eligibilities
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/iam/privilege-eligibilities/{privilege-eligibility}
- Scope: `pam`
- Permission: `pam`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/iam/privilege-eligibilities/{privilege-eligibility}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/iam/privilege-eligibilities/{privilege-eligibility}
- Scope: `pam`
- Permission: `pam`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/iam/privilege-eligibilities/{privilege-eligibility}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/iam/privilege-eligibilities/{privilege-eligibility}
- Scope: `pam`
- Permission: `pam`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/iam/privilege-eligibilities/{privilege-eligibility}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/iam/privilege-eligibilities/{privilege-eligibility}
- Scope: `pam`
- Permission: `pam`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/iam/privilege-eligibilities/{privilege-eligibility}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/iam/privilege-requests
- Scope: `pam`
- Permission: `pam`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/iam/privilege-requests?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/iam/privilege-requests
- Scope: `pam.request`
- Permission: `pam.request`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/iam/privilege-requests
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/iam/privilege-requests/{privilege-request}
- Scope: `pam`
- Permission: `pam`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/iam/privilege-requests/{privilege-request}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### POST /api/v1/iam/privilege-requests/{requestModel}/approve
- Scope: `pam.approve`
- Permission: `pam.approve`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/iam/privilege-requests/{requestModel}/approve
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "note": "operation note",
  "reason": "optional"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/iam/privilege-requests/{requestModel}/deny
- Scope: `pam.approve`
- Permission: `pam.approve`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/iam/privilege-requests/{requestModel}/deny
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/iam/protected-actions/verify
- Scope: `iam`
- Permission: `iam`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/iam/protected-actions/verify
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/iam/scim/Groups
- Scope: `scim.view`
- Permission: `scim.view`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/iam/scim/Groups?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### GET /api/v1/iam/scim/Users
- Scope: `scim.view`
- Permission: `scim.view`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/iam/scim/Users?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/iam/scim/Users
- Scope: `scim.manage`
- Permission: `scim.manage`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/iam/scim/Users
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/iam/scim/Users/{id}
- Scope: `scim.manage`
- Permission: `scim.manage`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/iam/scim/Users/{id}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### PATCH /api/v1/iam/scim/Users/{id}
- Scope: `scim.manage`
- Permission: `scim.manage`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/iam/scim/Users/{id}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/iam/sessions
- Scope: `session`
- Permission: `session`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/iam/sessions?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/iam/sessions/{session}/revoke
- Scope: `session.revoke`
- Permission: `session.revoke`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/iam/sessions/{session}/revoke
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/iam/sso/oidc/callback
- Scope: `sso.manage`
- Permission: `sso.manage`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/iam/sso/oidc/callback
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/iam/sso/providers
- Scope: `sso.view`
- Permission: `sso.view`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/iam/sso/providers?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/notifications/send
- Scope: `iam`
- Permission: `iam`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/notifications/send
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/permissions
- Scope: `iam`
- Permission: `iam`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/permissions?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/permissions
- Scope: `iam`
- Permission: `iam`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/permissions
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/permissions/{permission}
- Scope: `iam`
- Permission: `iam`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/permissions/{permission}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/permissions/{permission}
- Scope: `iam`
- Permission: `iam`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/permissions/{permission}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/permissions/{permission}
- Scope: `iam`
- Permission: `iam`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/permissions/{permission}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/permissions/{permission}
- Scope: `iam`
- Permission: `iam`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/permissions/{permission}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/plans
- Scope: `iam`
- Permission: `iam`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/plans?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/plans
- Scope: `iam`
- Permission: `iam`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/plans
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/plans/{plan}
- Scope: `iam`
- Permission: `iam`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/plans/{plan}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/plans/{plan}
- Scope: `iam`
- Permission: `iam`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/plans/{plan}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/plans/{plan}
- Scope: `iam`
- Permission: `iam`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/plans/{plan}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/plans/{plan}
- Scope: `iam`
- Permission: `iam`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/plans/{plan}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/roles
- Scope: `iam`
- Permission: `iam`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/roles?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/roles
- Scope: `iam`
- Permission: `iam`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/roles
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/roles/{role}
- Scope: `iam`
- Permission: `iam`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/roles/{role}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/roles/{role}
- Scope: `iam`
- Permission: `iam`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/roles/{role}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/roles/{role}
- Scope: `iam`
- Permission: `iam`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/roles/{role}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/roles/{role}
- Scope: `iam`
- Permission: `iam`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/roles/{role}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/subscriptions
- Scope: `iam`
- Permission: `iam`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/subscriptions?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/subscriptions
- Scope: `iam`
- Permission: `iam`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/subscriptions
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/subscriptions/{subscription}
- Scope: `iam`
- Permission: `iam`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/subscriptions/{subscription}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/subscriptions/{subscription}
- Scope: `iam`
- Permission: `iam`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/subscriptions/{subscription}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/subscriptions/{subscription}
- Scope: `iam`
- Permission: `iam`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/subscriptions/{subscription}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/subscriptions/{subscription}
- Scope: `iam`
- Permission: `iam`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/subscriptions/{subscription}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/transactions
- Scope: `iam`
- Permission: `iam`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/transactions?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### GET /api/v1/transactions/{transaction}
- Scope: `iam`
- Permission: `iam`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/transactions/{transaction}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### GET /api/v1/users
- Scope: `iam`
- Permission: `iam`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/users?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/users
- Scope: `iam`
- Permission: `iam`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/users
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/users/{user}
- Scope: `iam`
- Permission: `iam`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/users/{user}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/users/{user}
- Scope: `iam`
- Permission: `iam`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/users/{user}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/users/{user}
- Scope: `iam`
- Permission: `iam`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/users/{user}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/users/{user}
- Scope: `iam`
- Permission: `iam`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/users/{user}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/wallet-holds
- Scope: `iam`
- Permission: `iam`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/wallet-holds?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/wallet-holds/{hold}/capture
- Scope: `iam`
- Permission: `iam`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/wallet-holds/{hold}/capture
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/wallet-holds/{hold}/release
- Scope: `iam`
- Permission: `iam`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/wallet-holds/{hold}/release
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/wallet-holds/{wallet-hold}
- Scope: `iam`
- Permission: `iam`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/wallet-holds/{wallet-hold}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### GET /api/v1/wallets
- Scope: `iam`
- Permission: `iam`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/wallets?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/wallets
- Scope: `iam`
- Permission: `iam`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/wallets
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/wallets/transfer
- Scope: `iam`
- Permission: `iam`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/wallets/transfer
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/wallets/{wallet}
- Scope: `iam`
- Permission: `iam`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/wallets/{wallet}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/wallets/{wallet}
- Scope: `iam`
- Permission: `iam`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/wallets/{wallet}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/wallets/{wallet}
- Scope: `iam`
- Permission: `iam`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/wallets/{wallet}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/wallets/{wallet}
- Scope: `iam`
- Permission: `iam`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/wallets/{wallet}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/wallets/{wallet}/credit
- Scope: `iam`
- Permission: `iam`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/wallets/{wallet}/credit
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/wallets/{wallet}/debit
- Scope: `iam`
- Permission: `iam`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/wallets/{wallet}/debit
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/wallets/{wallet}/holds
- Scope: `iam`
- Permission: `iam`
- Rate limit: `60,1 (config: filamat-iam.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/wallets/{wallet}/holds
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/webhooks/notification-plugin
- Scope: `public`
- Permission: `public`
- Rate limit: `60,1`
- Tenant: `false`
- Auth: `public/none`

Request:
```http
POST /api/v1/webhooks/notification-plugin
Content-Type: application/json
```
```json
{
  "event": "sample.event",
  "payload": {
    "id": "evt_123",
    "status": "ok"
  },
  "signature": "sha256=..."
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/webhooks/payment-provider
- Scope: `public`
- Permission: `public`
- Rate limit: `60,1`
- Tenant: `false`
- Auth: `public/none`

Request:
```http
POST /api/v1/webhooks/payment-provider
Content-Type: application/json
```
```json
{
  "event": "sample.event",
  "payload": {
    "id": "evt_123",
    "status": "ok"
  },
  "signature": "sha256=..."
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /filamat-iam/impersonation/stop
- Scope: `public`
- Permission: `public`
- Rate limit: `60,1`
- Tenant: `false`
- Auth: `public/none`

Request:
```http
GET /filamat-iam/impersonation/stop?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

## filament-accounting-ir
### GET /api/v1/accounting-ir/account-plans
- Scope: `accounting.account_plan.view`
- Permission: `accounting.account_plan.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/accounting-ir/account-plans?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/accounting-ir/account-plans
- Scope: `accounting.account_plan.manage`
- Permission: `accounting.account_plan.manage`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/accounting-ir/account-plans
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/accounting-ir/account-plans/{account-plan}
- Scope: `accounting.account_plan.manage`
- Permission: `accounting.account_plan.manage`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/accounting-ir/account-plans/{account-plan}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/accounting-ir/account-plans/{account-plan}
- Scope: `accounting.account_plan.view`
- Permission: `accounting.account_plan.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/accounting-ir/account-plans/{account-plan}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/accounting-ir/account-plans/{account-plan}
- Scope: `accounting.account_plan.manage`
- Permission: `accounting.account_plan.manage`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/accounting-ir/account-plans/{account-plan}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/accounting-ir/account-plans/{account-plan}
- Scope: `accounting.account_plan.manage`
- Permission: `accounting.account_plan.manage`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/accounting-ir/account-plans/{account-plan}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/accounting-ir/branches
- Scope: `accounting.branch.view`
- Permission: `accounting.branch.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/accounting-ir/branches?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/accounting-ir/branches
- Scope: `accounting.branch.manage`
- Permission: `accounting.branch.manage`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/accounting-ir/branches
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/accounting-ir/branches/{branche}
- Scope: `accounting.branch.manage`
- Permission: `accounting.branch.manage`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/accounting-ir/branches/{branche}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/accounting-ir/branches/{branche}
- Scope: `accounting.branch.view`
- Permission: `accounting.branch.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/accounting-ir/branches/{branche}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/accounting-ir/branches/{branche}
- Scope: `accounting.branch.manage`
- Permission: `accounting.branch.manage`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/accounting-ir/branches/{branche}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/accounting-ir/branches/{branche}
- Scope: `accounting.branch.manage`
- Permission: `accounting.branch.manage`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/accounting-ir/branches/{branche}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/accounting-ir/chart-accounts
- Scope: `accounting.chart_account.view`
- Permission: `accounting.chart_account.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/accounting-ir/chart-accounts?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/accounting-ir/chart-accounts
- Scope: `accounting.chart_account.manage`
- Permission: `accounting.chart_account.manage`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/accounting-ir/chart-accounts
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/accounting-ir/chart-accounts/{chart-account}
- Scope: `accounting.chart_account.manage`
- Permission: `accounting.chart_account.manage`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/accounting-ir/chart-accounts/{chart-account}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/accounting-ir/chart-accounts/{chart-account}
- Scope: `accounting.chart_account.view`
- Permission: `accounting.chart_account.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/accounting-ir/chart-accounts/{chart-account}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/accounting-ir/chart-accounts/{chart-account}
- Scope: `accounting.chart_account.manage`
- Permission: `accounting.chart_account.manage`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/accounting-ir/chart-accounts/{chart-account}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/accounting-ir/chart-accounts/{chart-account}
- Scope: `accounting.chart_account.manage`
- Permission: `accounting.chart_account.manage`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/accounting-ir/chart-accounts/{chart-account}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/accounting-ir/cheques
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/accounting-ir/cheques?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/accounting-ir/cheques
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/accounting-ir/cheques
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/accounting-ir/cheques/{cheque}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/accounting-ir/cheques/{cheque}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/accounting-ir/cheques/{cheque}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/accounting-ir/cheques/{cheque}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/accounting-ir/cheques/{cheque}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/accounting-ir/cheques/{cheque}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/accounting-ir/cheques/{cheque}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/accounting-ir/cheques/{cheque}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/accounting-ir/companies
- Scope: `accounting.company.manage`
- Permission: `accounting.company.manage`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/accounting-ir/companies
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/accounting-ir/companies/{company}
- Scope: `accounting.company.manage`
- Permission: `accounting.company.manage`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/accounting-ir/companies/{company}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### PATCH /api/v1/accounting-ir/companies/{company}
- Scope: `accounting.company.manage`
- Permission: `accounting.company.manage`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/accounting-ir/companies/{company}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/accounting-ir/companies/{company}
- Scope: `accounting.company.manage`
- Permission: `accounting.company.manage`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/accounting-ir/companies/{company}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/accounting-ir/company-settings
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/accounting-ir/company-settings?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/accounting-ir/company-settings
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/accounting-ir/company-settings
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/accounting-ir/company-settings/{company-setting}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/accounting-ir/company-settings/{company-setting}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/accounting-ir/company-settings/{company-setting}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/accounting-ir/company-settings/{company-setting}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/accounting-ir/company-settings/{company-setting}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/accounting-ir/company-settings/{company-setting}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/accounting-ir/company-settings/{company-setting}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/accounting-ir/company-settings/{company-setting}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/accounting-ir/contracts
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/accounting-ir/contracts?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/accounting-ir/contracts
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/accounting-ir/contracts
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/accounting-ir/contracts/{contract}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/accounting-ir/contracts/{contract}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/accounting-ir/contracts/{contract}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/accounting-ir/contracts/{contract}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/accounting-ir/contracts/{contract}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/accounting-ir/contracts/{contract}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/accounting-ir/contracts/{contract}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/accounting-ir/contracts/{contract}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/accounting-ir/dimensions
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/accounting-ir/dimensions?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/accounting-ir/dimensions
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/accounting-ir/dimensions
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/accounting-ir/dimensions/{dimension}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/accounting-ir/dimensions/{dimension}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/accounting-ir/dimensions/{dimension}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/accounting-ir/dimensions/{dimension}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/accounting-ir/dimensions/{dimension}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/accounting-ir/dimensions/{dimension}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/accounting-ir/dimensions/{dimension}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/accounting-ir/dimensions/{dimension}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/accounting-ir/e-invoice-providers
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/accounting-ir/e-invoice-providers?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/accounting-ir/e-invoice-providers
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/accounting-ir/e-invoice-providers
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/accounting-ir/e-invoice-providers/{e-invoice-provider}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/accounting-ir/e-invoice-providers/{e-invoice-provider}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/accounting-ir/e-invoice-providers/{e-invoice-provider}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/accounting-ir/e-invoice-providers/{e-invoice-provider}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/accounting-ir/e-invoice-providers/{e-invoice-provider}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/accounting-ir/e-invoice-providers/{e-invoice-provider}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/accounting-ir/e-invoice-providers/{e-invoice-provider}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/accounting-ir/e-invoice-providers/{e-invoice-provider}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/accounting-ir/e-invoices
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/accounting-ir/e-invoices?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/accounting-ir/e-invoices
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/accounting-ir/e-invoices
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "amount": 100,
  "currency": "USDT",
  "order_id": "order-123",
  "idempotency_key": "invoice-demo-1"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/accounting-ir/e-invoices/{e-invoice}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/accounting-ir/e-invoices/{e-invoice}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/accounting-ir/e-invoices/{e-invoice}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/accounting-ir/e-invoices/{e-invoice}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/accounting-ir/e-invoices/{e-invoice}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/accounting-ir/e-invoices/{e-invoice}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "amount": 100,
  "currency": "USDT",
  "order_id": "order-123",
  "idempotency_key": "invoice-demo-1"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/accounting-ir/e-invoices/{e-invoice}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/accounting-ir/e-invoices/{e-invoice}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "amount": 100,
  "currency": "USDT",
  "order_id": "order-123",
  "idempotency_key": "invoice-demo-1"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/accounting-ir/e-invoices/{e_invoice}/send
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/accounting-ir/e-invoices/{e_invoice}/send
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "amount": 100,
  "currency": "USDT",
  "order_id": "order-123",
  "idempotency_key": "invoice-demo-1"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/accounting-ir/employees
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/accounting-ir/employees?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/accounting-ir/employees
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/accounting-ir/employees
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/accounting-ir/employees/{employee}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/accounting-ir/employees/{employee}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/accounting-ir/employees/{employee}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/accounting-ir/employees/{employee}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/accounting-ir/employees/{employee}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/accounting-ir/employees/{employee}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/accounting-ir/employees/{employee}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/accounting-ir/employees/{employee}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/accounting-ir/fiscal-periods
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/accounting-ir/fiscal-periods?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/accounting-ir/fiscal-periods
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/accounting-ir/fiscal-periods
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/accounting-ir/fiscal-periods/{fiscal-period}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/accounting-ir/fiscal-periods/{fiscal-period}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/accounting-ir/fiscal-periods/{fiscal-period}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/accounting-ir/fiscal-periods/{fiscal-period}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/accounting-ir/fiscal-periods/{fiscal-period}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/accounting-ir/fiscal-periods/{fiscal-period}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/accounting-ir/fiscal-periods/{fiscal-period}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/accounting-ir/fiscal-periods/{fiscal-period}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/accounting-ir/fiscal-years
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/accounting-ir/fiscal-years?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/accounting-ir/fiscal-years
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/accounting-ir/fiscal-years
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/accounting-ir/fiscal-years/{fiscal-year}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/accounting-ir/fiscal-years/{fiscal-year}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/accounting-ir/fiscal-years/{fiscal-year}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/accounting-ir/fiscal-years/{fiscal-year}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/accounting-ir/fiscal-years/{fiscal-year}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/accounting-ir/fiscal-years/{fiscal-year}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/accounting-ir/fiscal-years/{fiscal-year}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/accounting-ir/fiscal-years/{fiscal-year}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/accounting-ir/fixed-assets
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/accounting-ir/fixed-assets?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/accounting-ir/fixed-assets
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/accounting-ir/fixed-assets
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/accounting-ir/fixed-assets/{fixed-asset}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/accounting-ir/fixed-assets/{fixed-asset}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/accounting-ir/fixed-assets/{fixed-asset}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/accounting-ir/fixed-assets/{fixed-asset}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/accounting-ir/fixed-assets/{fixed-asset}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/accounting-ir/fixed-assets/{fixed-asset}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/accounting-ir/fixed-assets/{fixed-asset}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/accounting-ir/fixed-assets/{fixed-asset}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/accounting-ir/integrations
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/accounting-ir/integrations?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/accounting-ir/integrations
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/accounting-ir/integrations
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/accounting-ir/integrations/{integration_connector}/run
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/accounting-ir/integrations/{integration_connector}/run
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "note": "operation note",
  "reason": "optional"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/accounting-ir/integrations/{integration}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/accounting-ir/integrations/{integration}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/accounting-ir/integrations/{integration}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/accounting-ir/integrations/{integration}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/accounting-ir/integrations/{integration}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/accounting-ir/integrations/{integration}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/accounting-ir/integrations/{integration}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/accounting-ir/integrations/{integration}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/accounting-ir/inventory-docs
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/accounting-ir/inventory-docs?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/accounting-ir/inventory-docs
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/accounting-ir/inventory-docs
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/accounting-ir/inventory-docs/{inventory-doc}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/accounting-ir/inventory-docs/{inventory-doc}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/accounting-ir/inventory-docs/{inventory-doc}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/accounting-ir/inventory-docs/{inventory-doc}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/accounting-ir/inventory-docs/{inventory-doc}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/accounting-ir/inventory-docs/{inventory-doc}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/accounting-ir/inventory-docs/{inventory-doc}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/accounting-ir/inventory-docs/{inventory-doc}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/accounting-ir/inventory-docs/{inventory_doc}/post
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/accounting-ir/inventory-docs/{inventory_doc}/post
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "note": "operation note",
  "reason": "optional"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/accounting-ir/inventory-items
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/accounting-ir/inventory-items?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/accounting-ir/inventory-items
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/accounting-ir/inventory-items
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/accounting-ir/inventory-items/{inventory-item}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/accounting-ir/inventory-items/{inventory-item}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/accounting-ir/inventory-items/{inventory-item}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/accounting-ir/inventory-items/{inventory-item}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/accounting-ir/inventory-items/{inventory-item}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/accounting-ir/inventory-items/{inventory-item}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/accounting-ir/inventory-items/{inventory-item}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/accounting-ir/inventory-items/{inventory-item}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/accounting-ir/journal-entries
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/accounting-ir/journal-entries?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/accounting-ir/journal-entries
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/accounting-ir/journal-entries
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/accounting-ir/journal-entries/{journal-entry}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/accounting-ir/journal-entries/{journal-entry}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/accounting-ir/journal-entries/{journal-entry}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/accounting-ir/journal-entries/{journal-entry}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/accounting-ir/journal-entries/{journal-entry}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/accounting-ir/journal-entries/{journal-entry}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/accounting-ir/journal-entries/{journalEntry}/approve
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/accounting-ir/journal-entries/{journalEntry}/approve
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "note": "operation note",
  "reason": "optional"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/accounting-ir/journal-entries/{journalEntry}/post
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/accounting-ir/journal-entries/{journalEntry}/post
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "note": "operation note",
  "reason": "optional"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/accounting-ir/journal-entries/{journalEntry}/reverse
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/accounting-ir/journal-entries/{journalEntry}/reverse
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/accounting-ir/journal-entries/{journalEntry}/submit
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/accounting-ir/journal-entries/{journalEntry}/submit
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "note": "operation note",
  "reason": "optional"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/accounting-ir/key-materials
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/accounting-ir/key-materials?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/accounting-ir/key-materials
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/accounting-ir/key-materials
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/accounting-ir/key-materials/{key-material}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/accounting-ir/key-materials/{key-material}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/accounting-ir/key-materials/{key-material}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/accounting-ir/key-materials/{key-material}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/accounting-ir/key-materials/{key-material}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/accounting-ir/key-materials/{key-material}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/accounting-ir/key-materials/{key-material}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/accounting-ir/key-materials/{key-material}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/accounting-ir/openapi
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/accounting-ir/openapi?page=1
```

Response:
```json
{
  "openapi": "3.0.0",
  "info": {
    "title": "Module API",
    "version": "1.0.0"
  }
}
```

### GET /api/v1/accounting-ir/parties
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/accounting-ir/parties?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/accounting-ir/parties
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/accounting-ir/parties
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/accounting-ir/parties/{party}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/accounting-ir/parties/{party}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/accounting-ir/parties/{party}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/accounting-ir/parties/{party}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/accounting-ir/parties/{party}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/accounting-ir/parties/{party}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/accounting-ir/parties/{party}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/accounting-ir/parties/{party}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/accounting-ir/payroll-runs
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/accounting-ir/payroll-runs?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/accounting-ir/payroll-runs
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/accounting-ir/payroll-runs
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "note": "operation note",
  "reason": "optional"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/accounting-ir/payroll-runs/{payroll-run}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/accounting-ir/payroll-runs/{payroll-run}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/accounting-ir/payroll-runs/{payroll-run}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/accounting-ir/payroll-runs/{payroll-run}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/accounting-ir/payroll-runs/{payroll-run}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/accounting-ir/payroll-runs/{payroll-run}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "note": "operation note",
  "reason": "optional"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/accounting-ir/payroll-runs/{payroll-run}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/accounting-ir/payroll-runs/{payroll-run}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "note": "operation note",
  "reason": "optional"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/accounting-ir/payroll-tables
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/accounting-ir/payroll-tables?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/accounting-ir/payroll-tables
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/accounting-ir/payroll-tables
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/accounting-ir/payroll-tables/{payroll-table}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/accounting-ir/payroll-tables/{payroll-table}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/accounting-ir/payroll-tables/{payroll-table}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/accounting-ir/payroll-tables/{payroll-table}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/accounting-ir/payroll-tables/{payroll-table}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/accounting-ir/payroll-tables/{payroll-table}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/accounting-ir/payroll-tables/{payroll-table}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/accounting-ir/payroll-tables/{payroll-table}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/accounting-ir/products
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/accounting-ir/products?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/accounting-ir/products
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/accounting-ir/products
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/accounting-ir/products/{product}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/accounting-ir/products/{product}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/accounting-ir/products/{product}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/accounting-ir/products/{product}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/accounting-ir/products/{product}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/accounting-ir/products/{product}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/accounting-ir/products/{product}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/accounting-ir/products/{product}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/accounting-ir/projects
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/accounting-ir/projects?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/accounting-ir/projects
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/accounting-ir/projects
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/accounting-ir/projects/{project}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/accounting-ir/projects/{project}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/accounting-ir/projects/{project}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/accounting-ir/projects/{project}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/accounting-ir/projects/{project}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/accounting-ir/projects/{project}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/accounting-ir/projects/{project}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/accounting-ir/projects/{project}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/accounting-ir/purchase-invoices
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/accounting-ir/purchase-invoices?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/accounting-ir/purchase-invoices
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/accounting-ir/purchase-invoices
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "amount": 100,
  "currency": "USDT",
  "order_id": "order-123",
  "idempotency_key": "invoice-demo-1"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/accounting-ir/purchase-invoices/{purchase-invoice}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/accounting-ir/purchase-invoices/{purchase-invoice}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/accounting-ir/purchase-invoices/{purchase-invoice}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/accounting-ir/purchase-invoices/{purchase-invoice}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/accounting-ir/purchase-invoices/{purchase-invoice}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/accounting-ir/purchase-invoices/{purchase-invoice}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "amount": 100,
  "currency": "USDT",
  "order_id": "order-123",
  "idempotency_key": "invoice-demo-1"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/accounting-ir/purchase-invoices/{purchase-invoice}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/accounting-ir/purchase-invoices/{purchase-invoice}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "amount": 100,
  "currency": "USDT",
  "order_id": "order-123",
  "idempotency_key": "invoice-demo-1"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/accounting-ir/purchase-invoices/{purchase_invoice}/receive
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/accounting-ir/purchase-invoices/{purchase_invoice}/receive
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "amount": 100,
  "currency": "USDT",
  "order_id": "order-123",
  "idempotency_key": "invoice-demo-1"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/accounting-ir/reports/general-ledger
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/accounting-ir/reports/general-ledger?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### GET /api/v1/accounting-ir/reports/trial-balance
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/accounting-ir/reports/trial-balance?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### GET /api/v1/accounting-ir/sales-invoices
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/accounting-ir/sales-invoices?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/accounting-ir/sales-invoices
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/accounting-ir/sales-invoices
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "amount": 100,
  "currency": "USDT",
  "order_id": "order-123",
  "idempotency_key": "invoice-demo-1"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/accounting-ir/sales-invoices/{sales-invoice}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/accounting-ir/sales-invoices/{sales-invoice}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/accounting-ir/sales-invoices/{sales-invoice}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/accounting-ir/sales-invoices/{sales-invoice}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/accounting-ir/sales-invoices/{sales-invoice}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/accounting-ir/sales-invoices/{sales-invoice}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "amount": 100,
  "currency": "USDT",
  "order_id": "order-123",
  "idempotency_key": "invoice-demo-1"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/accounting-ir/sales-invoices/{sales-invoice}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/accounting-ir/sales-invoices/{sales-invoice}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "amount": 100,
  "currency": "USDT",
  "order_id": "order-123",
  "idempotency_key": "invoice-demo-1"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/accounting-ir/sales-invoices/{sales_invoice}/issue
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/accounting-ir/sales-invoices/{sales_invoice}/issue
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "amount": 100,
  "currency": "USDT",
  "order_id": "order-123",
  "idempotency_key": "invoice-demo-1"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/accounting-ir/seasonal-reports
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/accounting-ir/seasonal-reports?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/accounting-ir/seasonal-reports
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/accounting-ir/seasonal-reports
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/accounting-ir/seasonal-reports/{seasonal-report}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/accounting-ir/seasonal-reports/{seasonal-report}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/accounting-ir/seasonal-reports/{seasonal-report}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/accounting-ir/seasonal-reports/{seasonal-report}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/accounting-ir/seasonal-reports/{seasonal-report}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/accounting-ir/seasonal-reports/{seasonal-report}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/accounting-ir/seasonal-reports/{seasonal-report}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/accounting-ir/seasonal-reports/{seasonal-report}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/accounting-ir/tax-categories
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/accounting-ir/tax-categories?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/accounting-ir/tax-categories
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/accounting-ir/tax-categories
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/accounting-ir/tax-categories/{tax-category}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/accounting-ir/tax-categories/{tax-category}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/accounting-ir/tax-categories/{tax-category}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/accounting-ir/tax-categories/{tax-category}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/accounting-ir/tax-categories/{tax-category}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/accounting-ir/tax-categories/{tax-category}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/accounting-ir/tax-categories/{tax-category}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/accounting-ir/tax-categories/{tax-category}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/accounting-ir/tax-rates
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/accounting-ir/tax-rates?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/accounting-ir/tax-rates
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/accounting-ir/tax-rates
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/accounting-ir/tax-rates/{tax-rate}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/accounting-ir/tax-rates/{tax-rate}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/accounting-ir/tax-rates/{tax-rate}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/accounting-ir/tax-rates/{tax-rate}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/accounting-ir/tax-rates/{tax-rate}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/accounting-ir/tax-rates/{tax-rate}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/accounting-ir/tax-rates/{tax-rate}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/accounting-ir/tax-rates/{tax-rate}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/accounting-ir/treasury-accounts
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/accounting-ir/treasury-accounts?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/accounting-ir/treasury-accounts
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/accounting-ir/treasury-accounts
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/accounting-ir/treasury-accounts/{treasury-account}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/accounting-ir/treasury-accounts/{treasury-account}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/accounting-ir/treasury-accounts/{treasury-account}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/accounting-ir/treasury-accounts/{treasury-account}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/accounting-ir/treasury-accounts/{treasury-account}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/accounting-ir/treasury-accounts/{treasury-account}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/accounting-ir/treasury-accounts/{treasury-account}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/accounting-ir/treasury-accounts/{treasury-account}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/accounting-ir/treasury-transactions
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/accounting-ir/treasury-transactions?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/accounting-ir/treasury-transactions
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/accounting-ir/treasury-transactions
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/accounting-ir/treasury-transactions/{treasury-transaction}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/accounting-ir/treasury-transactions/{treasury-transaction}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/accounting-ir/treasury-transactions/{treasury-transaction}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/accounting-ir/treasury-transactions/{treasury-transaction}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/accounting-ir/treasury-transactions/{treasury-transaction}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/accounting-ir/treasury-transactions/{treasury-transaction}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/accounting-ir/treasury-transactions/{treasury-transaction}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/accounting-ir/treasury-transactions/{treasury-transaction}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/accounting-ir/uoms
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/accounting-ir/uoms?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/accounting-ir/uoms
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/accounting-ir/uoms
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/accounting-ir/uoms/{uom}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/accounting-ir/uoms/{uom}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/accounting-ir/uoms/{uom}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/accounting-ir/uoms/{uom}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/accounting-ir/uoms/{uom}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/accounting-ir/uoms/{uom}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/accounting-ir/uoms/{uom}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/accounting-ir/uoms/{uom}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/accounting-ir/vat-periods
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/accounting-ir/vat-periods?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/accounting-ir/vat-periods
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/accounting-ir/vat-periods
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/accounting-ir/vat-periods/{vat-period}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/accounting-ir/vat-periods/{vat-period}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/accounting-ir/vat-periods/{vat-period}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/accounting-ir/vat-periods/{vat-period}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/accounting-ir/vat-periods/{vat-period}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/accounting-ir/vat-periods/{vat-period}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/accounting-ir/vat-periods/{vat-period}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/accounting-ir/vat-periods/{vat-period}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/accounting-ir/vat-periods/{vat_period}/generate
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/accounting-ir/vat-periods/{vat_period}/generate
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "note": "operation note",
  "reason": "optional"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/accounting-ir/vat-reports
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/accounting-ir/vat-reports?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/accounting-ir/vat-reports
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/accounting-ir/vat-reports
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/accounting-ir/vat-reports/{vat-report}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/accounting-ir/vat-reports/{vat-report}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/accounting-ir/vat-reports/{vat-report}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/accounting-ir/vat-reports/{vat-report}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/accounting-ir/vat-reports/{vat-report}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/accounting-ir/vat-reports/{vat-report}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/accounting-ir/vat-reports/{vat-report}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/accounting-ir/vat-reports/{vat-report}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/accounting-ir/vat-reports/{vat_report}/submit
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/accounting-ir/vat-reports/{vat_report}/submit
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "note": "operation note",
  "reason": "optional"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/accounting-ir/warehouses
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/accounting-ir/warehouses?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/accounting-ir/warehouses
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/accounting-ir/warehouses
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/accounting-ir/warehouses/{warehous}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/accounting-ir/warehouses/{warehous}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/accounting-ir/warehouses/{warehous}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/accounting-ir/warehouses/{warehous}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/accounting-ir/warehouses/{warehous}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/accounting-ir/warehouses/{warehous}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/accounting-ir/warehouses/{warehous}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/accounting-ir/warehouses/{warehous}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/accounting-ir/withholding-rates
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/accounting-ir/withholding-rates?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/accounting-ir/withholding-rates
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/accounting-ir/withholding-rates
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/accounting-ir/withholding-rates/{withholding-rate}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/accounting-ir/withholding-rates/{withholding-rate}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/accounting-ir/withholding-rates/{withholding-rate}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/accounting-ir/withholding-rates/{withholding-rate}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/accounting-ir/withholding-rates/{withholding-rate}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/accounting-ir/withholding-rates/{withholding-rate}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/accounting-ir/withholding-rates/{withholding-rate}
- Scope: `accounting.company.view`
- Permission: `accounting.company.view`
- Rate limit: `60,1 (config: filament-accounting-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/accounting-ir/withholding-rates/{withholding-rate}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

## filament-app-api
### POST /api/v1/app/auth/logout
- Scope: `app.view`
- Permission: `app.view`
- Rate limit: `60,1 (config: filament-app-api.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/app/auth/logout
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "device_id": "demo-device"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/app/auth/me
- Scope: `app.view`
- Permission: `app.view`
- Rate limit: `60,1 (config: filament-app-api.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/app/auth/me?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/app/auth/refresh
- Scope: `public`
- Permission: `public`
- Rate limit: `60,1 (config: filament-app-api.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/app/auth/refresh
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "refresh_token": "demo-refresh-token"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/app/capabilities
- Scope: `app.view`
- Permission: `app.view`
- Rate limit: `60,1 (config: filament-app-api.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/app/capabilities?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### GET /api/v1/app/config
- Scope: `app.config.view`
- Permission: `app.config.view`
- Rate limit: `60,1 (config: filament-app-api.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/app/config?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/app/devices
- Scope: `app.device.manage`
- Permission: `app.device.manage`
- Rate limit: `60,1 (config: filament-app-api.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/app/devices
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "device_id": "demo-device",
  "platform": "android"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/app/devices/{device}
- Scope: `app.device.manage`
- Permission: `app.device.manage`
- Rate limit: `60,1 (config: filament-app-api.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/app/devices/{device}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### POST /api/v1/app/devices/{device}/tokens
- Scope: `app.device.manage`
- Permission: `app.device.manage`
- Rate limit: `60,1 (config: filament-app-api.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/app/devices/{device}/tokens
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "token": "push-token"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/app/notifications
- Scope: `app.notification.view`
- Permission: `app.notification.view`
- Rate limit: `60,1 (config: filament-app-api.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/app/notifications?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/app/notifications/{notification}/read
- Scope: `app.notification.manage`
- Permission: `app.notification.manage`
- Rate limit: `60,1 (config: filament-app-api.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/app/notifications/{notification}/read
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/app/openapi
- Scope: `app.view`
- Permission: `app.view`
- Rate limit: `60,1 (config: filament-app-api.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/app/openapi?page=1
```

Response:
```json
{
  "openapi": "3.0.0",
  "info": {
    "title": "Module API",
    "version": "1.0.0"
  }
}
```

### GET /api/v1/app/realtime/signals
- Scope: `app.realtime.signal`
- Permission: `app.realtime.signal`
- Rate limit: `60,1 (config: filament-app-api.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/app/realtime/signals?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/app/realtime/signals
- Scope: `app.realtime.signal`
- Permission: `app.realtime.signal`
- Rate limit: `60,1 (config: filament-app-api.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/app/realtime/signals
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/app/sync/conflicts
- Scope: `app.sync`
- Permission: `app.sync`
- Rate limit: `60,1 (config: filament-app-api.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/app/sync/conflicts
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "since": "2026-01-01T00:00:00Z",
  "items": []
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/app/sync/pull
- Scope: `app.sync`
- Permission: `app.sync`
- Rate limit: `60,1 (config: filament-app-api.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/app/sync/pull?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/app/sync/push
- Scope: `app.sync`
- Permission: `app.sync`
- Rate limit: `60,1 (config: filament-app-api.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/app/sync/push
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "since": "2026-01-01T00:00:00Z",
  "items": []
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/app/tenant/current
- Scope: `app.tenant.view`
- Permission: `app.tenant.view`
- Rate limit: `60,1 (config: filament-app-api.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/app/tenant/current?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/app/tenant/switch
- Scope: `app.tenant.switch`
- Permission: `app.tenant.switch`
- Rate limit: `60,1 (config: filament-app-api.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/app/tenant/switch
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/app/tickets
- Scope: `support.ticket.view`
- Permission: `support.ticket.view`
- Rate limit: `60,1 (config: filament-app-api.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/app/tickets?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/app/tickets
- Scope: `support.ticket.manage`
- Permission: `support.ticket.manage`
- Rate limit: `60,1 (config: filament-app-api.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/app/tickets
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "subject": "Demo ticket",
  "body": "Issue details"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/app/tickets/{ticket}/attachments
- Scope: `support.attachment.manage`
- Permission: `support.attachment.manage`
- Rate limit: `60,1 (config: filament-app-api.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/app/tickets/{ticket}/attachments
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "subject": "Demo ticket",
  "body": "Issue details"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/app/tickets/{ticket}/messages
- Scope: `support.message.view`
- Permission: `support.message.view`
- Rate limit: `60,1 (config: filament-app-api.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/app/tickets/{ticket}/messages?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### POST /api/v1/app/tickets/{ticket}/messages
- Scope: `support.message.manage`
- Permission: `support.message.manage`
- Rate limit: `60,1 (config: filament-app-api.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/app/tickets/{ticket}/messages
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "body": "Message body"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

## filament-commerce-core
### GET /api/v1/filament-commerce-core/openapi
- Scope: `commerce.catalog.view`
- Permission: `commerce.catalog.view`
- Rate limit: `60,1 (config: filament-commerce-core.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/filament-commerce-core/openapi?page=1
```

Response:
```json
{
  "openapi": "3.0.0",
  "info": {
    "title": "Module API",
    "version": "1.0.0"
  }
}
```

### GET /api/v1/filament-commerce-core/snapshots/inventory
- Scope: `commerce.catalog.view`
- Permission: `commerce.catalog.view`
- Rate limit: `60,1 (config: filament-commerce-core.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/filament-commerce-core/snapshots/inventory?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### GET /api/v1/filament-commerce-core/snapshots/pricing
- Scope: `commerce.catalog.view`
- Permission: `commerce.catalog.view`
- Rate limit: `60,1 (config: filament-commerce-core.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/filament-commerce-core/snapshots/pricing?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

## filament-commerce-experience
### POST /api/v1/filament-commerce-experience/buy-now
- Scope: `experience.buy_now.manage`
- Permission: `experience.buy_now.manage`
- Rate limit: `60,1 (config: filament-commerce-experience.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/filament-commerce-experience/buy-now
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/filament-commerce-experience/csat
- Scope: `experience.csat.manage`
- Permission: `experience.csat.manage`
- Rate limit: `60,1 (config: filament-commerce-experience.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/filament-commerce-experience/csat
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/filament-commerce-experience/openapi
- Scope: `experience.reviews.view`
- Permission: `experience.reviews.view`
- Rate limit: `60,1 (config: filament-commerce-experience.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/filament-commerce-experience/openapi?page=1
```

Response:
```json
{
  "openapi": "3.0.0",
  "info": {
    "title": "Module API",
    "version": "1.0.0"
  }
}
```

### GET /api/v1/filament-commerce-experience/questions
- Scope: `experience.reviews.view`
- Permission: `experience.reviews.view`
- Rate limit: `60,1 (config: filament-commerce-experience.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/filament-commerce-experience/questions?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

## filament-crypto-gateway
### GET /api/v1/crypto/health/nodes
- Scope: `crypto.invoices.manage`
- Permission: `crypto.invoices.manage`
- Rate limit: `60,1 (config: filament-crypto-gateway.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/crypto/health/nodes?page=1
```

Response:
```json
{
  "data": {
    "status": "ok",
    "items": []
  }
}
```

### GET /api/v1/crypto/health/providers
- Scope: `crypto.invoices.manage`
- Permission: `crypto.invoices.manage`
- Rate limit: `60,1 (config: filament-crypto-gateway.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/crypto/health/providers?page=1
```

Response:
```json
{
  "data": {
    "status": "ok",
    "items": []
  }
}
```

### GET /api/v1/crypto/invoices/{invoice}
- Scope: `crypto.invoices.manage`
- Permission: `crypto.invoices.manage`
- Rate limit: `60,1 (config: filament-crypto-gateway.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/crypto/invoices/{invoice}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### POST /api/v1/crypto/invoices/{invoice}/refresh
- Scope: `crypto.invoices.manage`
- Permission: `crypto.invoices.manage`
- Rate limit: `60,1 (config: filament-crypto-gateway.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/crypto/invoices/{invoice}/refresh
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/crypto/invoices/{invoice}/status
- Scope: `crypto.invoices.manage`
- Permission: `crypto.invoices.manage`
- Rate limit: `60,1 (config: filament-crypto-gateway.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/crypto/invoices/{invoice}/status?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### GET /api/v1/crypto/openapi
- Scope: `crypto.invoices.manage`
- Permission: `crypto.invoices.manage`
- Rate limit: `60,1 (config: filament-crypto-gateway.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/crypto/openapi?page=1
```

Response:
```json
{
  "openapi": "3.0.0",
  "info": {
    "title": "Module API",
    "version": "1.0.0"
  }
}
```

### GET /api/v1/crypto/payout-destinations
- Scope: `crypto.invoices.manage`
- Permission: `crypto.invoices.manage`
- Rate limit: `60,1 (config: filament-crypto-gateway.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/crypto/payout-destinations?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/crypto/payout-destinations
- Scope: `crypto.invoices.manage`
- Permission: `crypto.invoices.manage`
- Rate limit: `60,1 (config: filament-crypto-gateway.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/crypto/payout-destinations
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "label": "Main",
  "address": "addr-123",
  "currency": "USDT",
  "network": "TRC20"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/crypto/payout-destinations/{destination}
- Scope: `crypto.invoices.manage`
- Permission: `crypto.invoices.manage`
- Rate limit: `60,1 (config: filament-crypto-gateway.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/crypto/payout-destinations/{destination}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/crypto/payout-destinations/{destination}
- Scope: `crypto.invoices.manage`
- Permission: `crypto.invoices.manage`
- Rate limit: `60,1 (config: filament-crypto-gateway.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/crypto/payout-destinations/{destination}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PUT /api/v1/crypto/payout-destinations/{destination}
- Scope: `crypto.invoices.manage`
- Permission: `crypto.invoices.manage`
- Rate limit: `60,1 (config: filament-crypto-gateway.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/crypto/payout-destinations/{destination}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "label": "Main",
  "address": "addr-123",
  "currency": "USDT",
  "network": "TRC20"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/crypto/payouts
- Scope: `crypto.invoices.manage`
- Permission: `crypto.invoices.manage`
- Rate limit: `60,1 (config: filament-crypto-gateway.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/crypto/payouts
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "amount": 100,
  "currency": "USDT",
  "destination_id": 1,
  "idempotency_key": "payout-demo-1"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/crypto/payouts/{payout}
- Scope: `crypto.invoices.manage`
- Permission: `crypto.invoices.manage`
- Rate limit: `60,1 (config: filament-crypto-gateway.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/crypto/payouts/{payout}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### POST /api/v1/crypto/payouts/{payout}/approve
- Scope: `crypto.invoices.manage`
- Permission: `crypto.invoices.manage`
- Rate limit: `60,1 (config: filament-crypto-gateway.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/crypto/payouts/{payout}/approve
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "note": "operation note",
  "reason": "optional"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/crypto/payouts/{payout}/reject
- Scope: `crypto.invoices.manage`
- Permission: `crypto.invoices.manage`
- Rate limit: `60,1 (config: filament-crypto-gateway.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/crypto/payouts/{payout}/reject
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "note": "operation note",
  "reason": "optional"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/crypto/policy
- Scope: `crypto.fee_policies.view`
- Permission: `crypto.fee_policies.view`
- Rate limit: `60,1 (config: filament-crypto-gateway.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/crypto/policy?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### GET /api/v1/crypto/rates
- Scope: `crypto.invoices.manage`
- Permission: `crypto.invoices.manage`
- Rate limit: `60,1 (config: filament-crypto-gateway.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/crypto/rates?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/crypto/reconcile/run
- Scope: `crypto.invoices.manage`
- Permission: `crypto.invoices.manage`
- Rate limit: `60,1 (config: filament-crypto-gateway.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/crypto/reconcile/run
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "note": "operation note",
  "reason": "optional"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/crypto/webhooks/{provider}
- Scope: `crypto.invoices.manage`
- Permission: `crypto.invoices.manage`
- Rate limit: `60,1 (config: filament-crypto-gateway.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/crypto/webhooks/{provider}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "event": "sample.event",
  "payload": {
    "id": "evt_123",
    "status": "ok"
  },
  "signature": "sha256=..."
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

## filament-currency-rates
### GET /currency-rates/{code}
- Scope: `public`
- Permission: `public`
- Rate limit: `60,1`
- Tenant: `false`
- Auth: `public/none`

Request:
```http
GET /currency-rates/{code}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

## filament-loyalty-club
### GET /api/v1/loyalty/campaigns/offers
- Scope: `loyalty.campaign.view`
- Permission: `loyalty.campaign.view`
- Rate limit: `60,1 (config: filament-loyalty-club.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/loyalty/campaigns/offers?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/loyalty/coupons/redeem
- Scope: `loyalty.coupon.redeem`
- Permission: `loyalty.coupon.redeem`
- Rate limit: `60,1 (config: filament-loyalty-club.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/loyalty/coupons/redeem
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/loyalty/coupons/validate
- Scope: `loyalty.coupon.view`
- Permission: `loyalty.coupon.view`
- Rate limit: `60,1 (config: filament-loyalty-club.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/loyalty/coupons/validate
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/loyalty/customers
- Scope: `loyalty.customer.manage`
- Permission: `loyalty.customer.manage`
- Rate limit: `60,1 (config: filament-loyalty-club.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/loyalty/customers
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/loyalty/customers/{customer}
- Scope: `loyalty.customer.view`
- Permission: `loyalty.customer.view`
- Rate limit: `60,1 (config: filament-loyalty-club.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/loyalty/customers/{customer}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PUT /api/v1/loyalty/customers/{customer}
- Scope: `loyalty.customer.manage`
- Permission: `loyalty.customer.manage`
- Rate limit: `60,1 (config: filament-loyalty-club.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/loyalty/customers/{customer}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/loyalty/customers/{customer}/balances
- Scope: `loyalty.customer.view`
- Permission: `loyalty.customer.view`
- Rate limit: `60,1 (config: filament-loyalty-club.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/loyalty/customers/{customer}/balances?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### POST /api/v1/loyalty/events
- Scope: `loyalty.customer.view`
- Permission: `loyalty.customer.view`
- Rate limit: `60,1 (config: filament-loyalty-club.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/loyalty/events
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/loyalty/missions
- Scope: `loyalty.customer.view`
- Permission: `loyalty.customer.view`
- Rate limit: `60,1 (config: filament-loyalty-club.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/loyalty/missions?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### GET /api/v1/loyalty/missions/{mission}/progress
- Scope: `loyalty.customer.view`
- Permission: `loyalty.customer.view`
- Rate limit: `60,1 (config: filament-loyalty-club.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/loyalty/missions/{mission}/progress?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### GET /api/v1/loyalty/openapi
- Scope: `loyalty.customer.view`
- Permission: `loyalty.customer.view`
- Rate limit: `60,1 (config: filament-loyalty-club.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/loyalty/openapi?page=1
```

Response:
```json
{
  "openapi": "3.0.0",
  "info": {
    "title": "Module API",
    "version": "1.0.0"
  }
}
```

### POST /api/v1/loyalty/referrals
- Scope: `loyalty.customer.view`
- Permission: `loyalty.customer.view`
- Rate limit: `60,1 (config: filament-loyalty-club.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/loyalty/referrals
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/loyalty/referrals/{referral}
- Scope: `loyalty.customer.view`
- Permission: `loyalty.customer.view`
- Rate limit: `60,1 (config: filament-loyalty-club.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/loyalty/referrals/{referral}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### GET /api/v1/loyalty/rewards
- Scope: `loyalty.customer.view`
- Permission: `loyalty.customer.view`
- Rate limit: `60,1 (config: filament-loyalty-club.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/loyalty/rewards?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/loyalty/rewards/{reward}/redeem
- Scope: `loyalty.customer.view`
- Permission: `loyalty.customer.view`
- Rate limit: `60,1 (config: filament-loyalty-club.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/loyalty/rewards/{reward}/redeem
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

## filament-marketplace-connectors
### POST /api/v1/filament-marketplace-connectors/connectors/{connector}/sync
- Scope: `marketplace.connectors.manage`
- Permission: `marketplace.connectors.manage`
- Rate limit: `60,1 (config: filament-marketplace-connectors.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/filament-marketplace-connectors/connectors/{connector}/sync
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "since": "2026-01-01T00:00:00Z",
  "items": []
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/filament-marketplace-connectors/openapi
- Scope: `marketplace.connectors.manage`
- Permission: `marketplace.connectors.manage`
- Rate limit: `60,1 (config: filament-marketplace-connectors.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/filament-marketplace-connectors/openapi?page=1
```

Response:
```json
{
  "openapi": "3.0.0",
  "info": {
    "title": "Module API",
    "version": "1.0.0"
  }
}
```

## filament-meetings
### POST /
- Scope: `meetings.manage`
- Permission: `meetings.manage`
- Rate limit: `60,1 (config: filament-meetings.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/meetings/agenda-items/{agendaItem}
- Scope: `meetings.manage`
- Permission: `meetings.manage`
- Rate limit: `60,1 (config: filament-meetings.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/meetings/agenda-items/{agendaItem}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### PUT /api/v1/meetings/agenda-items/{agendaItem}
- Scope: `meetings.manage`
- Permission: `meetings.manage`
- Rate limit: `60,1 (config: filament-meetings.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/meetings/agenda-items/{agendaItem}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/meetings/attendees/{attendee}
- Scope: `meetings.manage`
- Permission: `meetings.manage`
- Rate limit: `60,1 (config: filament-meetings.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/meetings/attendees/{attendee}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### PUT /api/v1/meetings/attendees/{attendee}
- Scope: `meetings.manage`
- Permission: `meetings.manage`
- Rate limit: `60,1 (config: filament-meetings.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/meetings/attendees/{attendee}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/meetings/openapi
- Scope: `meetings.view`
- Permission: `meetings.view`
- Rate limit: `60,1 (config: filament-meetings.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/meetings/openapi?page=1
```

Response:
```json
{
  "openapi": "3.0.0",
  "info": {
    "title": "Module API",
    "version": "1.0.0"
  }
}
```

### GET /api/v1/meetings/templates
- Scope: `meetings.view`
- Permission: `meetings.view`
- Rate limit: `60,1 (config: filament-meetings.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/meetings/templates?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/meetings/templates
- Scope: `meetings.templates.manage`
- Permission: `meetings.templates.manage`
- Rate limit: `60,1 (config: filament-meetings.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/meetings/templates
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/meetings/templates/{template}
- Scope: `meetings.templates.manage`
- Permission: `meetings.templates.manage`
- Rate limit: `60,1 (config: filament-meetings.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/meetings/templates/{template}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/meetings/templates/{template}
- Scope: `meetings.view`
- Permission: `meetings.view`
- Rate limit: `60,1 (config: filament-meetings.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/meetings/templates/{template}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/meetings/templates/{template}
- Scope: `meetings.templates.manage`
- Permission: `meetings.templates.manage`
- Rate limit: `60,1 (config: filament-meetings.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/meetings/templates/{template}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/meetings/templates/{template}
- Scope: `meetings.templates.manage`
- Permission: `meetings.templates.manage`
- Rate limit: `60,1 (config: filament-meetings.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/meetings/templates/{template}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/meetings/{meeting}
- Scope: `meetings.manage`
- Permission: `meetings.manage`
- Rate limit: `60,1 (config: filament-meetings.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/meetings/{meeting}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/meetings/{meeting}
- Scope: `meetings.view`
- Permission: `meetings.view`
- Rate limit: `60,1 (config: filament-meetings.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/meetings/{meeting}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PUT /api/v1/meetings/{meeting}
- Scope: `meetings.manage`
- Permission: `meetings.manage`
- Rate limit: `60,1 (config: filament-meetings.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/meetings/{meeting}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/meetings/{meeting}/action-items/link-to-workhub
- Scope: `meetings.action_items.manage`
- Permission: `meetings.action_items.manage`
- Rate limit: `60,1 (config: filament-meetings.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/meetings/{meeting}/action-items/link-to-workhub
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/meetings/{meeting}/agenda-items
- Scope: `meetings.view`
- Permission: `meetings.view`
- Rate limit: `60,1 (config: filament-meetings.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/meetings/{meeting}/agenda-items?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### POST /api/v1/meetings/{meeting}/agenda-items
- Scope: `meetings.manage`
- Permission: `meetings.manage`
- Rate limit: `60,1 (config: filament-meetings.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/meetings/{meeting}/agenda-items
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/meetings/{meeting}/ai/generate-agenda
- Scope: `meetings.ai.use`
- Permission: `meetings.ai.use`
- Rate limit: `60,1 (config: filament-meetings.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/meetings/{meeting}/ai/generate-agenda
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "note": "operation note",
  "reason": "optional"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/meetings/{meeting}/ai/generate-minutes
- Scope: `meetings.ai.use`
- Permission: `meetings.ai.use`
- Rate limit: `60,1 (config: filament-meetings.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/meetings/{meeting}/ai/generate-minutes
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "note": "operation note",
  "reason": "optional"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/meetings/{meeting}/ai/recap
- Scope: `meetings.ai.use`
- Permission: `meetings.ai.use`
- Rate limit: `60,1 (config: filament-meetings.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/meetings/{meeting}/ai/recap
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/meetings/{meeting}/attendees
- Scope: `meetings.view`
- Permission: `meetings.view`
- Rate limit: `60,1 (config: filament-meetings.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/meetings/{meeting}/attendees?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### POST /api/v1/meetings/{meeting}/attendees
- Scope: `meetings.manage`
- Permission: `meetings.manage`
- Rate limit: `60,1 (config: filament-meetings.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/meetings/{meeting}/attendees
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/meetings/{meeting}/consent/confirm
- Scope: `meetings.ai.use`
- Permission: `meetings.ai.use`
- Rate limit: `60,1 (config: filament-meetings.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/meetings/{meeting}/consent/confirm
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "consented": true
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/meetings/{meeting}/minutes/export
- Scope: `meetings.minutes.manage`
- Permission: `meetings.minutes.manage`
- Rate limit: `60,1 (config: filament-meetings.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/meetings/{meeting}/minutes/export?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### POST /api/v1/meetings/{meeting}/transcript/manual
- Scope: `meetings.transcript.manage`
- Permission: `meetings.transcript.manage`
- Rate limit: `60,1 (config: filament-meetings.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/meetings/{meeting}/transcript/manual
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/meetings/{meeting}/transcript/upload
- Scope: `meetings.transcript.manage`
- Permission: `meetings.transcript.manage`
- Rate limit: `60,1 (config: filament-meetings.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/meetings/{meeting}/transcript/upload
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

## filament-payments
### GET /api/v1/filament-payments/intents/{intent}
- Scope: `payments.manage`
- Permission: `payments.manage`
- Rate limit: `60,1 (config: filament-payments.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/filament-payments/intents/{intent}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### GET /api/v1/filament-payments/openapi
- Scope: `payments.manage`
- Permission: `payments.manage`
- Rate limit: `60,1 (config: filament-payments.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/filament-payments/openapi?page=1
```

Response:
```json
{
  "openapi": "3.0.0",
  "info": {
    "title": "Module API",
    "version": "1.0.0"
  }
}
```

### POST /api/v1/filament-payments/webhooks/{provider}
- Scope: `payments.manage`
- Permission: `payments.manage`
- Rate limit: `60,1 (config: filament-payments.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/filament-payments/webhooks/{provider}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "event": "sample.event",
  "payload": {
    "id": "evt_123",
    "status": "ok"
  },
  "signature": "sha256=..."
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

## filament-payroll-attendance-ir
### GET /api/v1/payroll-attendance/advances
- Scope: `payroll.advance.manage`
- Permission: `payroll.advance.manage`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/payroll-attendance/advances?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/payroll-attendance/advances
- Scope: `payroll.advance.manage`
- Permission: `payroll.advance.manage`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/payroll-attendance/advances
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/payroll-attendance/advances/{advance}
- Scope: `payroll.advance.manage`
- Permission: `payroll.advance.manage`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/payroll-attendance/advances/{advance}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/payroll-attendance/advances/{advance}
- Scope: `payroll.advance.manage`
- Permission: `payroll.advance.manage`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/payroll-attendance/advances/{advance}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/payroll-attendance/advances/{advance}
- Scope: `payroll.advance.manage`
- Permission: `payroll.advance.manage`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/payroll-attendance/advances/{advance}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/payroll-attendance/advances/{advance}
- Scope: `payroll.advance.manage`
- Permission: `payroll.advance.manage`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/payroll-attendance/advances/{advance}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/payroll-attendance/ai-logs
- Scope: `payroll.ai.view`
- Permission: `payroll.ai.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/payroll-attendance/ai-logs?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### GET /api/v1/payroll-attendance/ai-logs/{ai-log}
- Scope: `payroll.ai.view`
- Permission: `payroll.ai.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/payroll-attendance/ai-logs/{ai-log}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### GET /api/v1/payroll-attendance/attendance-exceptions
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/payroll-attendance/attendance-exceptions?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### GET /api/v1/payroll-attendance/attendance-exceptions/{attendance-exception}
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/payroll-attendance/attendance-exceptions/{attendance-exception}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### POST /api/v1/payroll-attendance/attendance-exceptions/{attendance_exception}/resolve
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/payroll-attendance/attendance-exceptions/{attendance_exception}/resolve
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/payroll-attendance/attendance-policies
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/payroll-attendance/attendance-policies?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/payroll-attendance/attendance-policies
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/payroll-attendance/attendance-policies
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/payroll-attendance/attendance-policies/{attendance-policy}
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/payroll-attendance/attendance-policies/{attendance-policy}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/payroll-attendance/attendance-policies/{attendance-policy}
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/payroll-attendance/attendance-policies/{attendance-policy}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/payroll-attendance/attendance-policies/{attendance-policy}
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/payroll-attendance/attendance-policies/{attendance-policy}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/payroll-attendance/attendance-policies/{attendance-policy}
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/payroll-attendance/attendance-policies/{attendance-policy}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/payroll-attendance/attendance-records
- Scope: `payroll.attendance.view`
- Permission: `payroll.attendance.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/payroll-attendance/attendance-records?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/payroll-attendance/attendance-records
- Scope: `payroll.attendance.manage`
- Permission: `payroll.attendance.manage`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/payroll-attendance/attendance-records
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/payroll-attendance/attendance-records/{attendance-record}
- Scope: `payroll.attendance.manage`
- Permission: `payroll.attendance.manage`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/payroll-attendance/attendance-records/{attendance-record}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/payroll-attendance/attendance-records/{attendance-record}
- Scope: `payroll.attendance.view`
- Permission: `payroll.attendance.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/payroll-attendance/attendance-records/{attendance-record}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/payroll-attendance/attendance-records/{attendance-record}
- Scope: `payroll.attendance.manage`
- Permission: `payroll.attendance.manage`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/payroll-attendance/attendance-records/{attendance-record}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/payroll-attendance/attendance-records/{attendance-record}
- Scope: `payroll.attendance.manage`
- Permission: `payroll.attendance.manage`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/payroll-attendance/attendance-records/{attendance-record}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/payroll-attendance/attendance-records/{attendance_record}/approve
- Scope: `payroll.attendance.approve`
- Permission: `payroll.attendance.approve`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/payroll-attendance/attendance-records/{attendance_record}/approve
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "note": "operation note",
  "reason": "optional"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/payroll-attendance/contracts
- Scope: `payroll.contract.view`
- Permission: `payroll.contract.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/payroll-attendance/contracts?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/payroll-attendance/contracts
- Scope: `payroll.contract.manage`
- Permission: `payroll.contract.manage`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/payroll-attendance/contracts
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/payroll-attendance/contracts/{contract}
- Scope: `payroll.contract.manage`
- Permission: `payroll.contract.manage`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/payroll-attendance/contracts/{contract}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/payroll-attendance/contracts/{contract}
- Scope: `payroll.contract.view`
- Permission: `payroll.contract.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/payroll-attendance/contracts/{contract}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/payroll-attendance/contracts/{contract}
- Scope: `payroll.contract.manage`
- Permission: `payroll.contract.manage`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/payroll-attendance/contracts/{contract}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/payroll-attendance/contracts/{contract}
- Scope: `payroll.contract.manage`
- Permission: `payroll.contract.manage`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/payroll-attendance/contracts/{contract}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/payroll-attendance/employee-consents
- Scope: `payroll.consent.view`
- Permission: `payroll.consent.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/payroll-attendance/employee-consents?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/payroll-attendance/employee-consents
- Scope: `payroll.consent.manage`
- Permission: `payroll.consent.manage`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/payroll-attendance/employee-consents
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "consented": true
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/payroll-attendance/employee-consents/{employee-consent}
- Scope: `payroll.consent.manage`
- Permission: `payroll.consent.manage`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/payroll-attendance/employee-consents/{employee-consent}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/payroll-attendance/employee-consents/{employee-consent}
- Scope: `payroll.consent.view`
- Permission: `payroll.consent.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/payroll-attendance/employee-consents/{employee-consent}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/payroll-attendance/employee-consents/{employee-consent}
- Scope: `payroll.consent.manage`
- Permission: `payroll.consent.manage`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/payroll-attendance/employee-consents/{employee-consent}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "consented": true
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/payroll-attendance/employee-consents/{employee-consent}
- Scope: `payroll.consent.manage`
- Permission: `payroll.consent.manage`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/payroll-attendance/employee-consents/{employee-consent}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "consented": true
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/payroll-attendance/employees
- Scope: `payroll.employee.manage`
- Permission: `payroll.employee.manage`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/payroll-attendance/employees
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/payroll-attendance/employees/{employee}
- Scope: `payroll.employee.manage`
- Permission: `payroll.employee.manage`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/payroll-attendance/employees/{employee}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### PATCH /api/v1/payroll-attendance/employees/{employee}
- Scope: `payroll.employee.manage`
- Permission: `payroll.employee.manage`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/payroll-attendance/employees/{employee}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/payroll-attendance/employees/{employee}
- Scope: `payroll.employee.manage`
- Permission: `payroll.employee.manage`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/payroll-attendance/employees/{employee}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/payroll-attendance/holiday-rules
- Scope: `payroll.calendar.view`
- Permission: `payroll.calendar.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/payroll-attendance/holiday-rules?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/payroll-attendance/holiday-rules
- Scope: `payroll.calendar.manage`
- Permission: `payroll.calendar.manage`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/payroll-attendance/holiday-rules
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/payroll-attendance/holiday-rules/{holiday-rule}
- Scope: `payroll.calendar.manage`
- Permission: `payroll.calendar.manage`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/payroll-attendance/holiday-rules/{holiday-rule}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/payroll-attendance/holiday-rules/{holiday-rule}
- Scope: `payroll.calendar.view`
- Permission: `payroll.calendar.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/payroll-attendance/holiday-rules/{holiday-rule}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/payroll-attendance/holiday-rules/{holiday-rule}
- Scope: `payroll.calendar.manage`
- Permission: `payroll.calendar.manage`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/payroll-attendance/holiday-rules/{holiday-rule}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/payroll-attendance/holiday-rules/{holiday-rule}
- Scope: `payroll.calendar.manage`
- Permission: `payroll.calendar.manage`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/payroll-attendance/holiday-rules/{holiday-rule}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/payroll-attendance/leave-requests
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/payroll-attendance/leave-requests?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/payroll-attendance/leave-requests
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/payroll-attendance/leave-requests
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/payroll-attendance/leave-requests/{leave-request}
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/payroll-attendance/leave-requests/{leave-request}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/payroll-attendance/leave-requests/{leave-request}
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/payroll-attendance/leave-requests/{leave-request}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/payroll-attendance/leave-requests/{leave-request}
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/payroll-attendance/leave-requests/{leave-request}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/payroll-attendance/leave-requests/{leave-request}
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/payroll-attendance/leave-requests/{leave-request}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/payroll-attendance/leave-requests/{leave_request}/approve
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/payroll-attendance/leave-requests/{leave_request}/approve
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "note": "operation note",
  "reason": "optional"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/payroll-attendance/leave-types
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/payroll-attendance/leave-types?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/payroll-attendance/leave-types
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/payroll-attendance/leave-types
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/payroll-attendance/leave-types/{leave-type}
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/payroll-attendance/leave-types/{leave-type}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/payroll-attendance/leave-types/{leave-type}
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/payroll-attendance/leave-types/{leave-type}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/payroll-attendance/leave-types/{leave-type}
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/payroll-attendance/leave-types/{leave-type}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/payroll-attendance/leave-types/{leave-type}
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/payroll-attendance/leave-types/{leave-type}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/payroll-attendance/loans
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/payroll-attendance/loans?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/payroll-attendance/loans
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/payroll-attendance/loans
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/payroll-attendance/loans/{loan}
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/payroll-attendance/loans/{loan}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/payroll-attendance/loans/{loan}
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/payroll-attendance/loans/{loan}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/payroll-attendance/loans/{loan}
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/payroll-attendance/loans/{loan}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/payroll-attendance/loans/{loan}
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/payroll-attendance/loans/{loan}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/payroll-attendance/mission-requests
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/payroll-attendance/mission-requests?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/payroll-attendance/mission-requests
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/payroll-attendance/mission-requests
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/payroll-attendance/mission-requests/{mission-request}
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/payroll-attendance/mission-requests/{mission-request}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/payroll-attendance/mission-requests/{mission-request}
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/payroll-attendance/mission-requests/{mission-request}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/payroll-attendance/mission-requests/{mission-request}
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/payroll-attendance/mission-requests/{mission-request}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/payroll-attendance/mission-requests/{mission-request}
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/payroll-attendance/mission-requests/{mission-request}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/payroll-attendance/mission-requests/{mission_request}/approve
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/payroll-attendance/mission-requests/{mission_request}/approve
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "note": "operation note",
  "reason": "optional"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/payroll-attendance/mission-requests/{mission_request}/reject
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/payroll-attendance/mission-requests/{mission_request}/reject
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "note": "operation note",
  "reason": "optional"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/payroll-attendance/openapi
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/payroll-attendance/openapi?page=1
```

Response:
```json
{
  "openapi": "3.0.0",
  "info": {
    "title": "Module API",
    "version": "1.0.0"
  }
}
```

### GET /api/v1/payroll-attendance/overtime-requests
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/payroll-attendance/overtime-requests?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/payroll-attendance/overtime-requests
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/payroll-attendance/overtime-requests
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/payroll-attendance/overtime-requests/{overtime-request}
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/payroll-attendance/overtime-requests/{overtime-request}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/payroll-attendance/overtime-requests/{overtime-request}
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/payroll-attendance/overtime-requests/{overtime-request}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/payroll-attendance/overtime-requests/{overtime-request}
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/payroll-attendance/overtime-requests/{overtime-request}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/payroll-attendance/overtime-requests/{overtime-request}
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/payroll-attendance/overtime-requests/{overtime-request}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/payroll-attendance/overtime-requests/{overtime_request}/approve
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/payroll-attendance/overtime-requests/{overtime_request}/approve
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "note": "operation note",
  "reason": "optional"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/payroll-attendance/overtime-requests/{overtime_request}/reject
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/payroll-attendance/overtime-requests/{overtime_request}/reject
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "note": "operation note",
  "reason": "optional"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/payroll-attendance/payroll-runs
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/payroll-attendance/payroll-runs?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/payroll-attendance/payroll-runs
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/payroll-attendance/payroll-runs
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "note": "operation note",
  "reason": "optional"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/payroll-attendance/payroll-runs/{payroll-run}
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/payroll-attendance/payroll-runs/{payroll-run}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/payroll-attendance/payroll-runs/{payroll-run}
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/payroll-attendance/payroll-runs/{payroll-run}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/payroll-attendance/payroll-runs/{payroll-run}
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/payroll-attendance/payroll-runs/{payroll-run}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "note": "operation note",
  "reason": "optional"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/payroll-attendance/payroll-runs/{payroll-run}
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/payroll-attendance/payroll-runs/{payroll-run}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "note": "operation note",
  "reason": "optional"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/payroll-attendance/payroll-runs/{payroll_run}/approve
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/payroll-attendance/payroll-runs/{payroll_run}/approve
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "note": "operation note",
  "reason": "optional"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/payroll-attendance/payroll-runs/{payroll_run}/generate
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/payroll-attendance/payroll-runs/{payroll_run}/generate
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "note": "operation note",
  "reason": "optional"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/payroll-attendance/payroll-runs/{payroll_run}/lock
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/payroll-attendance/payroll-runs/{payroll_run}/lock
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "note": "operation note",
  "reason": "optional"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/payroll-attendance/payroll-runs/{payroll_run}/post
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/payroll-attendance/payroll-runs/{payroll_run}/post
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "note": "operation note",
  "reason": "optional"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/payroll-attendance/payroll-slips
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/payroll-attendance/payroll-slips?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### GET /api/v1/payroll-attendance/payroll-slips/{payroll-slip}
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/payroll-attendance/payroll-slips/{payroll-slip}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### GET /api/v1/payroll-attendance/punches
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/payroll-attendance/punches?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/payroll-attendance/punches
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/payroll-attendance/punches
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/payroll-attendance/punches/{punche}
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/payroll-attendance/punches/{punche}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/payroll-attendance/punches/{punche}
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/payroll-attendance/punches/{punche}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/payroll-attendance/punches/{punche}
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/payroll-attendance/punches/{punche}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/payroll-attendance/punches/{punche}
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/payroll-attendance/punches/{punche}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/payroll-attendance/reports/ai/manager
- Scope: `payroll.ai.use`
- Permission: `payroll.ai.use`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/payroll-attendance/reports/ai/manager
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "site_id": 1,
  "verification_method": "cname",
  "is_primary": true
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/payroll-attendance/reports/attendance-summary
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/payroll-attendance/reports/attendance-summary?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### GET /api/v1/payroll-attendance/reports/coverage-gaps
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/payroll-attendance/reports/coverage-gaps?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/payroll-attendance/reports/export
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/payroll-attendance/reports/export
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/payroll-attendance/reports/leave-balance
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/payroll-attendance/reports/leave-balance?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### GET /api/v1/payroll-attendance/reports/overtime
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/payroll-attendance/reports/overtime?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### GET /api/v1/payroll-attendance/reports/tardiness
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/payroll-attendance/reports/tardiness?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### GET /api/v1/payroll-attendance/schedules
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/payroll-attendance/schedules?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/payroll-attendance/schedules
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/payroll-attendance/schedules
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/payroll-attendance/schedules/{schedule}
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/payroll-attendance/schedules/{schedule}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/payroll-attendance/schedules/{schedule}
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/payroll-attendance/schedules/{schedule}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/payroll-attendance/schedules/{schedule}
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/payroll-attendance/schedules/{schedule}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/payroll-attendance/schedules/{schedule}
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/payroll-attendance/schedules/{schedule}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/payroll-attendance/sensitive-access-logs
- Scope: `payroll.audit.view`
- Permission: `payroll.audit.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/payroll-attendance/sensitive-access-logs?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### GET /api/v1/payroll-attendance/sensitive-access-logs/{sensitive-access-log}
- Scope: `payroll.audit.view`
- Permission: `payroll.audit.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/payroll-attendance/sensitive-access-logs/{sensitive-access-log}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### GET /api/v1/payroll-attendance/shifts
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/payroll-attendance/shifts?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/payroll-attendance/shifts
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/payroll-attendance/shifts
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/payroll-attendance/shifts/{shift}
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/payroll-attendance/shifts/{shift}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/payroll-attendance/shifts/{shift}
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/payroll-attendance/shifts/{shift}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/payroll-attendance/shifts/{shift}
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/payroll-attendance/shifts/{shift}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/payroll-attendance/shifts/{shift}
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/payroll-attendance/shifts/{shift}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/payroll-attendance/time-events
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/payroll-attendance/time-events?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/payroll-attendance/time-events
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/payroll-attendance/time-events
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/payroll-attendance/time-events/{time-event}
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/payroll-attendance/time-events/{time-event}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/payroll-attendance/time-events/{time-event}
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/payroll-attendance/time-events/{time-event}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/payroll-attendance/time-events/{time-event}
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/payroll-attendance/time-events/{time-event}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/payroll-attendance/time-events/{time-event}
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/payroll-attendance/time-events/{time-event}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/payroll-attendance/timesheets
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/payroll-attendance/timesheets?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/payroll-attendance/timesheets/generate
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/payroll-attendance/timesheets/generate
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "note": "operation note",
  "reason": "optional"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/payroll-attendance/timesheets/{timesheet}
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/payroll-attendance/timesheets/{timesheet}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### POST /api/v1/payroll-attendance/timesheets/{timesheet}/approve
- Scope: `payroll.employee.view`
- Permission: `payroll.employee.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/payroll-attendance/timesheets/{timesheet}/approve
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "note": "operation note",
  "reason": "optional"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/payroll-attendance/work-calendars
- Scope: `payroll.calendar.view`
- Permission: `payroll.calendar.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/payroll-attendance/work-calendars?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/payroll-attendance/work-calendars
- Scope: `payroll.calendar.manage`
- Permission: `payroll.calendar.manage`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/payroll-attendance/work-calendars
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/payroll-attendance/work-calendars/{work-calendar}
- Scope: `payroll.calendar.manage`
- Permission: `payroll.calendar.manage`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/payroll-attendance/work-calendars/{work-calendar}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/payroll-attendance/work-calendars/{work-calendar}
- Scope: `payroll.calendar.view`
- Permission: `payroll.calendar.view`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/payroll-attendance/work-calendars/{work-calendar}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/payroll-attendance/work-calendars/{work-calendar}
- Scope: `payroll.calendar.manage`
- Permission: `payroll.calendar.manage`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/payroll-attendance/work-calendars/{work-calendar}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/payroll-attendance/work-calendars/{work-calendar}
- Scope: `payroll.calendar.manage`
- Permission: `payroll.calendar.manage`
- Rate limit: `60,1 (config: filament-payroll-attendance-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/payroll-attendance/work-calendars/{work-calendar}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

## filament-petty-cash-ir
### POST /api/v1/petty-cash/ai/audit
- Scope: `petty_cash.ai.use`
- Permission: `petty_cash.ai.use`
- Rate limit: `60,1 (config: filament-petty-cash-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/petty-cash/ai/audit
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "fund_id": 1,
  "limit": 200
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/petty-cash/ai/report
- Scope: `petty_cash.ai.view_reports`
- Permission: `petty_cash.ai.view_reports`
- Rate limit: `60,1 (config: filament-petty-cash-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/petty-cash/ai/report?fund_id=1&from=2026-01-01&to=2026-01-31
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### GET /api/v1/petty-cash/categories
- Scope: `petty_cash.category.view`
- Permission: `petty_cash.category.view`
- Rate limit: `60,1 (config: filament-petty-cash-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/petty-cash/categories?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/petty-cash/categories
- Scope: `petty_cash.category.manage`
- Permission: `petty_cash.category.manage`
- Rate limit: `60,1 (config: filament-petty-cash-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/petty-cash/categories
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/petty-cash/categories/{category}
- Scope: `petty_cash.category.manage`
- Permission: `petty_cash.category.manage`
- Rate limit: `60,1 (config: filament-petty-cash-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/petty-cash/categories/{category}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/petty-cash/categories/{category}
- Scope: `petty_cash.category.view`
- Permission: `petty_cash.category.view`
- Rate limit: `60,1 (config: filament-petty-cash-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/petty-cash/categories/{category}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/petty-cash/categories/{category}
- Scope: `petty_cash.category.manage`
- Permission: `petty_cash.category.manage`
- Rate limit: `60,1 (config: filament-petty-cash-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/petty-cash/categories/{category}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/petty-cash/categories/{category}
- Scope: `petty_cash.category.manage`
- Permission: `petty_cash.category.manage`
- Rate limit: `60,1 (config: filament-petty-cash-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/petty-cash/categories/{category}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/petty-cash/expenses
- Scope: `petty_cash.expense.view`
- Permission: `petty_cash.expense.view`
- Rate limit: `60,1 (config: filament-petty-cash-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/petty-cash/expenses?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/petty-cash/expenses
- Scope: `petty_cash.expense.manage`
- Permission: `petty_cash.expense.manage`
- Rate limit: `60,1 (config: filament-petty-cash-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/petty-cash/expenses
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/petty-cash/expenses/{expense}/ai-apply
- Scope: `petty_cash.ai.use`
- Permission: `petty_cash.ai.use`
- Rate limit: `60,1 (config: filament-petty-cash-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/petty-cash/expenses/{expense}/ai-apply
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/petty-cash/expenses/{expense}/ai-reject
- Scope: `petty_cash.ai.use`
- Permission: `petty_cash.ai.use`
- Rate limit: `60,1 (config: filament-petty-cash-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/petty-cash/expenses/{expense}/ai-reject
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/petty-cash/expenses/{expense}/ai-suggest
- Scope: `petty_cash.ai.use`
- Permission: `petty_cash.ai.use`
- Rate limit: `60,1 (config: filament-petty-cash-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/petty-cash/expenses/{expense}/ai-suggest
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/petty-cash/expenses/{expense}/approve
- Scope: `petty_cash.expense.approve`
- Permission: `petty_cash.expense.approve`
- Rate limit: `60,1 (config: filament-petty-cash-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/petty-cash/expenses/{expense}/approve
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "note": "operation note",
  "reason": "optional"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/petty-cash/expenses/{expense}/post
- Scope: `petty_cash.expense.post`
- Permission: `petty_cash.expense.post`
- Rate limit: `60,1 (config: filament-petty-cash-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/petty-cash/expenses/{expense}/post
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "note": "operation note",
  "reason": "optional"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/petty-cash/expenses/{expense}/reject
- Scope: `petty_cash.expense.reject`
- Permission: `petty_cash.expense.reject`
- Rate limit: `60,1 (config: filament-petty-cash-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/petty-cash/expenses/{expense}/reject
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "note": "operation note",
  "reason": "optional"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/petty-cash/expenses/{expense}/submit
- Scope: `petty_cash.expense.manage`
- Permission: `petty_cash.expense.manage`
- Rate limit: `60,1 (config: filament-petty-cash-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/petty-cash/expenses/{expense}/submit
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "note": "operation note",
  "reason": "optional"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/petty-cash/expenses/{expens}
- Scope: `petty_cash.expense.manage`
- Permission: `petty_cash.expense.manage`
- Rate limit: `60,1 (config: filament-petty-cash-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/petty-cash/expenses/{expens}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/petty-cash/expenses/{expens}
- Scope: `petty_cash.expense.view`
- Permission: `petty_cash.expense.view`
- Rate limit: `60,1 (config: filament-petty-cash-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/petty-cash/expenses/{expens}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/petty-cash/expenses/{expens}
- Scope: `petty_cash.expense.manage`
- Permission: `petty_cash.expense.manage`
- Rate limit: `60,1 (config: filament-petty-cash-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/petty-cash/expenses/{expens}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/petty-cash/expenses/{expens}
- Scope: `petty_cash.expense.manage`
- Permission: `petty_cash.expense.manage`
- Rate limit: `60,1 (config: filament-petty-cash-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/petty-cash/expenses/{expens}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/petty-cash/funds
- Scope: `petty_cash.fund.manage`
- Permission: `petty_cash.fund.manage`
- Rate limit: `60,1 (config: filament-petty-cash-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/petty-cash/funds
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/petty-cash/funds/{fund}
- Scope: `petty_cash.fund.manage`
- Permission: `petty_cash.fund.manage`
- Rate limit: `60,1 (config: filament-petty-cash-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/petty-cash/funds/{fund}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### PATCH /api/v1/petty-cash/funds/{fund}
- Scope: `petty_cash.fund.manage`
- Permission: `petty_cash.fund.manage`
- Rate limit: `60,1 (config: filament-petty-cash-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/petty-cash/funds/{fund}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/petty-cash/funds/{fund}
- Scope: `petty_cash.fund.manage`
- Permission: `petty_cash.fund.manage`
- Rate limit: `60,1 (config: filament-petty-cash-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/petty-cash/funds/{fund}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/petty-cash/openapi
- Scope: `petty_cash.fund.view`
- Permission: `petty_cash.fund.view`
- Rate limit: `60,1 (config: filament-petty-cash-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/petty-cash/openapi?page=1
```

Response:
```json
{
  "openapi": "3.0.0",
  "info": {
    "title": "Module API",
    "version": "1.0.0"
  }
}
```

### GET /api/v1/petty-cash/replenishments
- Scope: `petty_cash.fund.view`
- Permission: `petty_cash.fund.view`
- Rate limit: `60,1 (config: filament-petty-cash-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/petty-cash/replenishments?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/petty-cash/replenishments
- Scope: `petty_cash.fund.view`
- Permission: `petty_cash.fund.view`
- Rate limit: `60,1 (config: filament-petty-cash-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/petty-cash/replenishments
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/petty-cash/replenishments/{replenishment}
- Scope: `petty_cash.fund.view`
- Permission: `petty_cash.fund.view`
- Rate limit: `60,1 (config: filament-petty-cash-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/petty-cash/replenishments/{replenishment}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/petty-cash/replenishments/{replenishment}
- Scope: `petty_cash.fund.view`
- Permission: `petty_cash.fund.view`
- Rate limit: `60,1 (config: filament-petty-cash-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/petty-cash/replenishments/{replenishment}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/petty-cash/replenishments/{replenishment}
- Scope: `petty_cash.fund.view`
- Permission: `petty_cash.fund.view`
- Rate limit: `60,1 (config: filament-petty-cash-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/petty-cash/replenishments/{replenishment}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/petty-cash/replenishments/{replenishment}
- Scope: `petty_cash.fund.view`
- Permission: `petty_cash.fund.view`
- Rate limit: `60,1 (config: filament-petty-cash-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/petty-cash/replenishments/{replenishment}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/petty-cash/replenishments/{replenishment}/approve
- Scope: `petty_cash.fund.view`
- Permission: `petty_cash.fund.view`
- Rate limit: `60,1 (config: filament-petty-cash-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/petty-cash/replenishments/{replenishment}/approve
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "note": "operation note",
  "reason": "optional"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/petty-cash/replenishments/{replenishment}/post
- Scope: `petty_cash.fund.view`
- Permission: `petty_cash.fund.view`
- Rate limit: `60,1 (config: filament-petty-cash-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/petty-cash/replenishments/{replenishment}/post
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "note": "operation note",
  "reason": "optional"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/petty-cash/replenishments/{replenishment}/reject
- Scope: `petty_cash.fund.view`
- Permission: `petty_cash.fund.view`
- Rate limit: `60,1 (config: filament-petty-cash-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/petty-cash/replenishments/{replenishment}/reject
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "note": "operation note",
  "reason": "optional"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/petty-cash/replenishments/{replenishment}/submit
- Scope: `petty_cash.fund.view`
- Permission: `petty_cash.fund.view`
- Rate limit: `60,1 (config: filament-petty-cash-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/petty-cash/replenishments/{replenishment}/submit
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "note": "operation note",
  "reason": "optional"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/petty-cash/settlements
- Scope: `petty_cash.fund.view`
- Permission: `petty_cash.fund.view`
- Rate limit: `60,1 (config: filament-petty-cash-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/petty-cash/settlements?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/petty-cash/settlements
- Scope: `petty_cash.fund.view`
- Permission: `petty_cash.fund.view`
- Rate limit: `60,1 (config: filament-petty-cash-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/petty-cash/settlements
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/petty-cash/settlements/{settlement}
- Scope: `petty_cash.fund.view`
- Permission: `petty_cash.fund.view`
- Rate limit: `60,1 (config: filament-petty-cash-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/petty-cash/settlements/{settlement}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/petty-cash/settlements/{settlement}
- Scope: `petty_cash.fund.view`
- Permission: `petty_cash.fund.view`
- Rate limit: `60,1 (config: filament-petty-cash-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/petty-cash/settlements/{settlement}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/petty-cash/settlements/{settlement}
- Scope: `petty_cash.fund.view`
- Permission: `petty_cash.fund.view`
- Rate limit: `60,1 (config: filament-petty-cash-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/petty-cash/settlements/{settlement}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/petty-cash/settlements/{settlement}
- Scope: `petty_cash.fund.view`
- Permission: `petty_cash.fund.view`
- Rate limit: `60,1 (config: filament-petty-cash-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/petty-cash/settlements/{settlement}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/petty-cash/settlements/{settlement}/approve
- Scope: `petty_cash.fund.view`
- Permission: `petty_cash.fund.view`
- Rate limit: `60,1 (config: filament-petty-cash-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/petty-cash/settlements/{settlement}/approve
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "note": "operation note",
  "reason": "optional"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/petty-cash/settlements/{settlement}/post
- Scope: `petty_cash.fund.view`
- Permission: `petty_cash.fund.view`
- Rate limit: `60,1 (config: filament-petty-cash-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/petty-cash/settlements/{settlement}/post
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "note": "operation note",
  "reason": "optional"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/petty-cash/settlements/{settlement}/submit
- Scope: `petty_cash.fund.view`
- Permission: `petty_cash.fund.view`
- Rate limit: `60,1 (config: filament-petty-cash-ir.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/petty-cash/settlements/{settlement}/submit
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "note": "operation note",
  "reason": "optional"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

## filament-pos
### GET /api/v1/filament-pos/openapi
- Scope: `pos.use`
- Permission: `pos.use`
- Rate limit: `60,1 (config: filament-pos.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/filament-pos/openapi?page=1
```

Response:
```json
{
  "openapi": "3.0.0",
  "info": {
    "title": "Module API",
    "version": "1.0.0"
  }
}
```

### POST /api/v1/filament-pos/sales
- Scope: `pos.use`
- Permission: `pos.use`
- Rate limit: `60,1 (config: filament-pos.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/filament-pos/sales
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/filament-pos/sync/delta
- Scope: `pos.use`
- Permission: `pos.use`
- Rate limit: `60,1 (config: filament-pos.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/filament-pos/sync/delta?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/filament-pos/sync/outbox
- Scope: `pos.use`
- Permission: `pos.use`
- Rate limit: `60,1 (config: filament-pos.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/filament-pos/sync/outbox
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "since": "2026-01-01T00:00:00Z",
  "items": []
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

## filament-relograde
### POST /relograde/webhook/order-finished
- Scope: `public`
- Permission: `public`
- Rate limit: `60,1`
- Tenant: `false`
- Auth: `public/none`

Request:
```http
POST /relograde/webhook/order-finished
Content-Type: application/json
```
```json
{
  "event": "sample.event",
  "payload": {
    "id": "evt_123",
    "status": "ok"
  },
  "signature": "sha256=..."
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

## filament-restaurant-ops
### GET /api/v1/restaurant-ops/goods-receipts
- Scope: `restaurant.goods_receipt.view`
- Permission: `restaurant.goods_receipt.view`
- Rate limit: `60,1 (config: filament-restaurant-ops.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/restaurant-ops/goods-receipts?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/restaurant-ops/goods-receipts
- Scope: `restaurant.goods_receipt.manage`
- Permission: `restaurant.goods_receipt.manage`
- Rate limit: `60,1 (config: filament-restaurant-ops.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/restaurant-ops/goods-receipts
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/restaurant-ops/goods-receipts/{goods-receipt}
- Scope: `restaurant.goods_receipt.manage`
- Permission: `restaurant.goods_receipt.manage`
- Rate limit: `60,1 (config: filament-restaurant-ops.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/restaurant-ops/goods-receipts/{goods-receipt}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/restaurant-ops/goods-receipts/{goods-receipt}
- Scope: `restaurant.goods_receipt.view`
- Permission: `restaurant.goods_receipt.view`
- Rate limit: `60,1 (config: filament-restaurant-ops.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/restaurant-ops/goods-receipts/{goods-receipt}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/restaurant-ops/goods-receipts/{goods-receipt}
- Scope: `restaurant.goods_receipt.manage`
- Permission: `restaurant.goods_receipt.manage`
- Rate limit: `60,1 (config: filament-restaurant-ops.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/restaurant-ops/goods-receipts/{goods-receipt}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/restaurant-ops/goods-receipts/{goods-receipt}
- Scope: `restaurant.goods_receipt.manage`
- Permission: `restaurant.goods_receipt.manage`
- Rate limit: `60,1 (config: filament-restaurant-ops.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/restaurant-ops/goods-receipts/{goods-receipt}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/restaurant-ops/goods-receipts/{goods_receipt}/post
- Scope: `restaurant.goods_receipt.post`
- Permission: `restaurant.goods_receipt.post`
- Rate limit: `60,1 (config: filament-restaurant-ops.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/restaurant-ops/goods-receipts/{goods_receipt}/post
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "note": "operation note",
  "reason": "optional"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/restaurant-ops/inventory-docs
- Scope: `restaurant.inventory_doc.view`
- Permission: `restaurant.inventory_doc.view`
- Rate limit: `60,1 (config: filament-restaurant-ops.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/restaurant-ops/inventory-docs?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/restaurant-ops/inventory-docs
- Scope: `restaurant.inventory_doc.manage`
- Permission: `restaurant.inventory_doc.manage`
- Rate limit: `60,1 (config: filament-restaurant-ops.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/restaurant-ops/inventory-docs
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/restaurant-ops/inventory-docs/{inventory-doc}
- Scope: `restaurant.inventory_doc.manage`
- Permission: `restaurant.inventory_doc.manage`
- Rate limit: `60,1 (config: filament-restaurant-ops.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/restaurant-ops/inventory-docs/{inventory-doc}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/restaurant-ops/inventory-docs/{inventory-doc}
- Scope: `restaurant.inventory_doc.view`
- Permission: `restaurant.inventory_doc.view`
- Rate limit: `60,1 (config: filament-restaurant-ops.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/restaurant-ops/inventory-docs/{inventory-doc}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/restaurant-ops/inventory-docs/{inventory-doc}
- Scope: `restaurant.inventory_doc.manage`
- Permission: `restaurant.inventory_doc.manage`
- Rate limit: `60,1 (config: filament-restaurant-ops.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/restaurant-ops/inventory-docs/{inventory-doc}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/restaurant-ops/inventory-docs/{inventory-doc}
- Scope: `restaurant.inventory_doc.manage`
- Permission: `restaurant.inventory_doc.manage`
- Rate limit: `60,1 (config: filament-restaurant-ops.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/restaurant-ops/inventory-docs/{inventory-doc}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/restaurant-ops/inventory-docs/{inventory_doc}/post
- Scope: `restaurant.inventory_doc.post`
- Permission: `restaurant.inventory_doc.post`
- Rate limit: `60,1 (config: filament-restaurant-ops.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/restaurant-ops/inventory-docs/{inventory_doc}/post
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "note": "operation note",
  "reason": "optional"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/restaurant-ops/items
- Scope: `restaurant.item.view`
- Permission: `restaurant.item.view`
- Rate limit: `60,1 (config: filament-restaurant-ops.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/restaurant-ops/items?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/restaurant-ops/items
- Scope: `restaurant.item.manage`
- Permission: `restaurant.item.manage`
- Rate limit: `60,1 (config: filament-restaurant-ops.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/restaurant-ops/items
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/restaurant-ops/items/{item}
- Scope: `restaurant.item.manage`
- Permission: `restaurant.item.manage`
- Rate limit: `60,1 (config: filament-restaurant-ops.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/restaurant-ops/items/{item}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/restaurant-ops/items/{item}
- Scope: `restaurant.item.view`
- Permission: `restaurant.item.view`
- Rate limit: `60,1 (config: filament-restaurant-ops.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/restaurant-ops/items/{item}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/restaurant-ops/items/{item}
- Scope: `restaurant.item.manage`
- Permission: `restaurant.item.manage`
- Rate limit: `60,1 (config: filament-restaurant-ops.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/restaurant-ops/items/{item}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/restaurant-ops/items/{item}
- Scope: `restaurant.item.manage`
- Permission: `restaurant.item.manage`
- Rate limit: `60,1 (config: filament-restaurant-ops.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/restaurant-ops/items/{item}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/restaurant-ops/menu-items
- Scope: `restaurant.menu_item.view`
- Permission: `restaurant.menu_item.view`
- Rate limit: `60,1 (config: filament-restaurant-ops.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/restaurant-ops/menu-items?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/restaurant-ops/menu-items
- Scope: `restaurant.menu_item.manage`
- Permission: `restaurant.menu_item.manage`
- Rate limit: `60,1 (config: filament-restaurant-ops.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/restaurant-ops/menu-items
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/restaurant-ops/menu-items/{menu-item}
- Scope: `restaurant.menu_item.manage`
- Permission: `restaurant.menu_item.manage`
- Rate limit: `60,1 (config: filament-restaurant-ops.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/restaurant-ops/menu-items/{menu-item}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/restaurant-ops/menu-items/{menu-item}
- Scope: `restaurant.menu_item.view`
- Permission: `restaurant.menu_item.view`
- Rate limit: `60,1 (config: filament-restaurant-ops.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/restaurant-ops/menu-items/{menu-item}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/restaurant-ops/menu-items/{menu-item}
- Scope: `restaurant.menu_item.manage`
- Permission: `restaurant.menu_item.manage`
- Rate limit: `60,1 (config: filament-restaurant-ops.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/restaurant-ops/menu-items/{menu-item}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/restaurant-ops/menu-items/{menu-item}
- Scope: `restaurant.menu_item.manage`
- Permission: `restaurant.menu_item.manage`
- Rate limit: `60,1 (config: filament-restaurant-ops.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/restaurant-ops/menu-items/{menu-item}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/restaurant-ops/menu-sales
- Scope: `restaurant.menu_sale.view`
- Permission: `restaurant.menu_sale.view`
- Rate limit: `60,1 (config: filament-restaurant-ops.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/restaurant-ops/menu-sales?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/restaurant-ops/menu-sales
- Scope: `restaurant.menu_sale.manage`
- Permission: `restaurant.menu_sale.manage`
- Rate limit: `60,1 (config: filament-restaurant-ops.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/restaurant-ops/menu-sales
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/restaurant-ops/menu-sales/{menu-sale}
- Scope: `restaurant.menu_sale.manage`
- Permission: `restaurant.menu_sale.manage`
- Rate limit: `60,1 (config: filament-restaurant-ops.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/restaurant-ops/menu-sales/{menu-sale}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/restaurant-ops/menu-sales/{menu-sale}
- Scope: `restaurant.menu_sale.view`
- Permission: `restaurant.menu_sale.view`
- Rate limit: `60,1 (config: filament-restaurant-ops.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/restaurant-ops/menu-sales/{menu-sale}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/restaurant-ops/menu-sales/{menu-sale}
- Scope: `restaurant.menu_sale.manage`
- Permission: `restaurant.menu_sale.manage`
- Rate limit: `60,1 (config: filament-restaurant-ops.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/restaurant-ops/menu-sales/{menu-sale}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/restaurant-ops/menu-sales/{menu-sale}
- Scope: `restaurant.menu_sale.manage`
- Permission: `restaurant.menu_sale.manage`
- Rate limit: `60,1 (config: filament-restaurant-ops.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/restaurant-ops/menu-sales/{menu-sale}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/restaurant-ops/menu-sales/{menu_sale}/post
- Scope: `restaurant.menu_sale.post`
- Permission: `restaurant.menu_sale.post`
- Rate limit: `60,1 (config: filament-restaurant-ops.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/restaurant-ops/menu-sales/{menu_sale}/post
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "note": "operation note",
  "reason": "optional"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/restaurant-ops/openapi
- Scope: `restaurant.supplier.view`
- Permission: `restaurant.supplier.view`
- Rate limit: `60,1 (config: filament-restaurant-ops.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/restaurant-ops/openapi?page=1
```

Response:
```json
{
  "openapi": "3.0.0",
  "info": {
    "title": "Module API",
    "version": "1.0.0"
  }
}
```

### GET /api/v1/restaurant-ops/purchase-orders
- Scope: `restaurant.purchase_order.view`
- Permission: `restaurant.purchase_order.view`
- Rate limit: `60,1 (config: filament-restaurant-ops.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/restaurant-ops/purchase-orders?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/restaurant-ops/purchase-orders
- Scope: `restaurant.purchase_order.manage`
- Permission: `restaurant.purchase_order.manage`
- Rate limit: `60,1 (config: filament-restaurant-ops.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/restaurant-ops/purchase-orders
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/restaurant-ops/purchase-orders/{purchase-order}
- Scope: `restaurant.purchase_order.manage`
- Permission: `restaurant.purchase_order.manage`
- Rate limit: `60,1 (config: filament-restaurant-ops.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/restaurant-ops/purchase-orders/{purchase-order}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/restaurant-ops/purchase-orders/{purchase-order}
- Scope: `restaurant.purchase_order.view`
- Permission: `restaurant.purchase_order.view`
- Rate limit: `60,1 (config: filament-restaurant-ops.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/restaurant-ops/purchase-orders/{purchase-order}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/restaurant-ops/purchase-orders/{purchase-order}
- Scope: `restaurant.purchase_order.manage`
- Permission: `restaurant.purchase_order.manage`
- Rate limit: `60,1 (config: filament-restaurant-ops.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/restaurant-ops/purchase-orders/{purchase-order}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/restaurant-ops/purchase-orders/{purchase-order}
- Scope: `restaurant.purchase_order.manage`
- Permission: `restaurant.purchase_order.manage`
- Rate limit: `60,1 (config: filament-restaurant-ops.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/restaurant-ops/purchase-orders/{purchase-order}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/restaurant-ops/purchase-requests
- Scope: `restaurant.purchase_request.view`
- Permission: `restaurant.purchase_request.view`
- Rate limit: `60,1 (config: filament-restaurant-ops.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/restaurant-ops/purchase-requests?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/restaurant-ops/purchase-requests
- Scope: `restaurant.purchase_request.manage`
- Permission: `restaurant.purchase_request.manage`
- Rate limit: `60,1 (config: filament-restaurant-ops.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/restaurant-ops/purchase-requests
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/restaurant-ops/purchase-requests/{purchase-request}
- Scope: `restaurant.purchase_request.manage`
- Permission: `restaurant.purchase_request.manage`
- Rate limit: `60,1 (config: filament-restaurant-ops.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/restaurant-ops/purchase-requests/{purchase-request}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/restaurant-ops/purchase-requests/{purchase-request}
- Scope: `restaurant.purchase_request.view`
- Permission: `restaurant.purchase_request.view`
- Rate limit: `60,1 (config: filament-restaurant-ops.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/restaurant-ops/purchase-requests/{purchase-request}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/restaurant-ops/purchase-requests/{purchase-request}
- Scope: `restaurant.purchase_request.manage`
- Permission: `restaurant.purchase_request.manage`
- Rate limit: `60,1 (config: filament-restaurant-ops.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/restaurant-ops/purchase-requests/{purchase-request}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/restaurant-ops/purchase-requests/{purchase-request}
- Scope: `restaurant.purchase_request.manage`
- Permission: `restaurant.purchase_request.manage`
- Rate limit: `60,1 (config: filament-restaurant-ops.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/restaurant-ops/purchase-requests/{purchase-request}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/restaurant-ops/recipes
- Scope: `restaurant.recipe.view`
- Permission: `restaurant.recipe.view`
- Rate limit: `60,1 (config: filament-restaurant-ops.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/restaurant-ops/recipes?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/restaurant-ops/recipes
- Scope: `restaurant.recipe.manage`
- Permission: `restaurant.recipe.manage`
- Rate limit: `60,1 (config: filament-restaurant-ops.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/restaurant-ops/recipes
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/restaurant-ops/recipes/{recipe}
- Scope: `restaurant.recipe.manage`
- Permission: `restaurant.recipe.manage`
- Rate limit: `60,1 (config: filament-restaurant-ops.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/restaurant-ops/recipes/{recipe}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/restaurant-ops/recipes/{recipe}
- Scope: `restaurant.recipe.view`
- Permission: `restaurant.recipe.view`
- Rate limit: `60,1 (config: filament-restaurant-ops.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/restaurant-ops/recipes/{recipe}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/restaurant-ops/recipes/{recipe}
- Scope: `restaurant.recipe.manage`
- Permission: `restaurant.recipe.manage`
- Rate limit: `60,1 (config: filament-restaurant-ops.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/restaurant-ops/recipes/{recipe}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/restaurant-ops/recipes/{recipe}
- Scope: `restaurant.recipe.manage`
- Permission: `restaurant.recipe.manage`
- Rate limit: `60,1 (config: filament-restaurant-ops.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/restaurant-ops/recipes/{recipe}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/restaurant-ops/suppliers
- Scope: `restaurant.supplier.manage`
- Permission: `restaurant.supplier.manage`
- Rate limit: `60,1 (config: filament-restaurant-ops.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/restaurant-ops/suppliers
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/restaurant-ops/suppliers/{supplier}
- Scope: `restaurant.supplier.manage`
- Permission: `restaurant.supplier.manage`
- Rate limit: `60,1 (config: filament-restaurant-ops.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/restaurant-ops/suppliers/{supplier}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### PATCH /api/v1/restaurant-ops/suppliers/{supplier}
- Scope: `restaurant.supplier.manage`
- Permission: `restaurant.supplier.manage`
- Rate limit: `60,1 (config: filament-restaurant-ops.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/restaurant-ops/suppliers/{supplier}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/restaurant-ops/suppliers/{supplier}
- Scope: `restaurant.supplier.manage`
- Permission: `restaurant.supplier.manage`
- Rate limit: `60,1 (config: filament-restaurant-ops.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/restaurant-ops/suppliers/{supplier}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/restaurant-ops/uoms
- Scope: `restaurant.supplier.view`
- Permission: `restaurant.supplier.view`
- Rate limit: `60,1 (config: filament-restaurant-ops.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/restaurant-ops/uoms?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/restaurant-ops/uoms
- Scope: `restaurant.supplier.view`
- Permission: `restaurant.supplier.view`
- Rate limit: `60,1 (config: filament-restaurant-ops.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/restaurant-ops/uoms
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/restaurant-ops/uoms/{uom}
- Scope: `restaurant.supplier.view`
- Permission: `restaurant.supplier.view`
- Rate limit: `60,1 (config: filament-restaurant-ops.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/restaurant-ops/uoms/{uom}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/restaurant-ops/uoms/{uom}
- Scope: `restaurant.supplier.view`
- Permission: `restaurant.supplier.view`
- Rate limit: `60,1 (config: filament-restaurant-ops.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/restaurant-ops/uoms/{uom}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/restaurant-ops/uoms/{uom}
- Scope: `restaurant.supplier.view`
- Permission: `restaurant.supplier.view`
- Rate limit: `60,1 (config: filament-restaurant-ops.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/restaurant-ops/uoms/{uom}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/restaurant-ops/uoms/{uom}
- Scope: `restaurant.supplier.view`
- Permission: `restaurant.supplier.view`
- Rate limit: `60,1 (config: filament-restaurant-ops.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/restaurant-ops/uoms/{uom}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/restaurant-ops/warehouses
- Scope: `restaurant.supplier.view`
- Permission: `restaurant.supplier.view`
- Rate limit: `60,1 (config: filament-restaurant-ops.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/restaurant-ops/warehouses?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/restaurant-ops/warehouses
- Scope: `restaurant.supplier.view`
- Permission: `restaurant.supplier.view`
- Rate limit: `60,1 (config: filament-restaurant-ops.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/restaurant-ops/warehouses
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/restaurant-ops/warehouses/{warehous}
- Scope: `restaurant.supplier.view`
- Permission: `restaurant.supplier.view`
- Rate limit: `60,1 (config: filament-restaurant-ops.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/restaurant-ops/warehouses/{warehous}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/restaurant-ops/warehouses/{warehous}
- Scope: `restaurant.supplier.view`
- Permission: `restaurant.supplier.view`
- Rate limit: `60,1 (config: filament-restaurant-ops.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/restaurant-ops/warehouses/{warehous}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/restaurant-ops/warehouses/{warehous}
- Scope: `restaurant.supplier.view`
- Permission: `restaurant.supplier.view`
- Rate limit: `60,1 (config: filament-restaurant-ops.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/restaurant-ops/warehouses/{warehous}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/restaurant-ops/warehouses/{warehous}
- Scope: `restaurant.supplier.view`
- Permission: `restaurant.supplier.view`
- Rate limit: `60,1 (config: filament-restaurant-ops.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/restaurant-ops/warehouses/{warehous}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

## filament-storefront-builder
### GET /blocks/{key}
- Scope: `public`
- Permission: `public`
- Rate limit: `60,1`
- Tenant: `false`
- Auth: `public/none`

Request:
```http
GET /blocks/{key}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### GET /menus/{key}
- Scope: `public`
- Permission: `public`
- Rate limit: `60,1`
- Tenant: `false`
- Auth: `public/none`

Request:
```http
GET /menus/{key}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### GET /pages/{slug}
- Scope: `public`
- Permission: `public`
- Rate limit: `60,1`
- Tenant: `false`
- Auth: `public/none`

Request:
```http
GET /pages/{slug}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### GET /theme
- Scope: `public`
- Permission: `public`
- Rate limit: `60,1`
- Tenant: `false`
- Auth: `public/none`

Request:
```http
GET /theme?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

## filament-threecx
### POST /api/v1/threecx/crm/contacts
- Scope: `threecx.crm_connector`
- Permission: `threecx.crm_connector`
- Rate limit: `30,1 (config: filament-threecx.crm_connector.rate_limit)`
- Tenant: `false`
- Auth: `public/none`

Request:
```http
POST /api/v1/threecx/crm/contacts
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/threecx/crm/journal/call
- Scope: `threecx.crm_connector`
- Permission: `threecx.crm_connector`
- Rate limit: `30,1 (config: filament-threecx.crm_connector.rate_limit)`
- Tenant: `false`
- Auth: `public/none`

Request:
```http
POST /api/v1/threecx/crm/journal/call
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/threecx/crm/journal/chat
- Scope: `threecx.crm_connector`
- Permission: `threecx.crm_connector`
- Rate limit: `30,1 (config: filament-threecx.crm_connector.rate_limit)`
- Tenant: `false`
- Auth: `public/none`

Request:
```http
POST /api/v1/threecx/crm/journal/chat
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/threecx/crm/search
- Scope: `threecx.crm_connector`
- Permission: `threecx.crm_connector`
- Rate limit: `30,1 (config: filament-threecx.crm_connector.rate_limit)`
- Tenant: `false`
- Auth: `public/none`

Request:
```http
GET /api/v1/threecx/crm/search?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

## filament-workhub
### DELETE /api/v1/workhub/attachments/{attachment}
- Scope: `workhub.attachment.manage`
- Permission: `workhub.attachment.manage`
- Rate limit: `60,1 (config: filament-workhub.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/workhub/attachments/{attachment}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/workhub/automation-rules
- Scope: `workhub.automation.view`
- Permission: `workhub.automation.view`
- Rate limit: `60,1 (config: filament-workhub.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/workhub/automation-rules?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/workhub/automation-rules
- Scope: `workhub.automation.manage`
- Permission: `workhub.automation.manage`
- Rate limit: `60,1 (config: filament-workhub.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/workhub/automation-rules
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/workhub/automation-rules/{automation-rule}
- Scope: `workhub.automation.manage`
- Permission: `workhub.automation.manage`
- Rate limit: `60,1 (config: filament-workhub.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/workhub/automation-rules/{automation-rule}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/workhub/automation-rules/{automation-rule}
- Scope: `workhub.automation.view`
- Permission: `workhub.automation.view`
- Rate limit: `60,1 (config: filament-workhub.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/workhub/automation-rules/{automation-rule}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/workhub/automation-rules/{automation-rule}
- Scope: `workhub.automation.manage`
- Permission: `workhub.automation.manage`
- Rate limit: `60,1 (config: filament-workhub.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/workhub/automation-rules/{automation-rule}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/workhub/automation-rules/{automation-rule}
- Scope: `workhub.automation.manage`
- Permission: `workhub.automation.manage`
- Rate limit: `60,1 (config: filament-workhub.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/workhub/automation-rules/{automation-rule}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/workhub/comments/{comment}
- Scope: `workhub.comment.manage`
- Permission: `workhub.comment.manage`
- Rate limit: `60,1 (config: filament-workhub.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/workhub/comments/{comment}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/workhub/custom-fields
- Scope: `workhub.custom_field.view`
- Permission: `workhub.custom_field.view`
- Rate limit: `60,1 (config: filament-workhub.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/workhub/custom-fields?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/workhub/custom-fields
- Scope: `workhub.custom_field.manage`
- Permission: `workhub.custom_field.manage`
- Rate limit: `60,1 (config: filament-workhub.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/workhub/custom-fields
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/workhub/custom-fields/{custom-field}
- Scope: `workhub.custom_field.manage`
- Permission: `workhub.custom_field.manage`
- Rate limit: `60,1 (config: filament-workhub.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/workhub/custom-fields/{custom-field}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/workhub/custom-fields/{custom-field}
- Scope: `workhub.custom_field.view`
- Permission: `workhub.custom_field.view`
- Rate limit: `60,1 (config: filament-workhub.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/workhub/custom-fields/{custom-field}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/workhub/custom-fields/{custom-field}
- Scope: `workhub.custom_field.manage`
- Permission: `workhub.custom_field.manage`
- Rate limit: `60,1 (config: filament-workhub.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/workhub/custom-fields/{custom-field}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/workhub/custom-fields/{custom-field}
- Scope: `workhub.custom_field.manage`
- Permission: `workhub.custom_field.manage`
- Rate limit: `60,1 (config: filament-workhub.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/workhub/custom-fields/{custom-field}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/workhub/decisions/{decision}
- Scope: `workhub.decision.manage`
- Permission: `workhub.decision.manage`
- Rate limit: `60,1 (config: filament-workhub.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/workhub/decisions/{decision}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/workhub/labels
- Scope: `workhub.label.view`
- Permission: `workhub.label.view`
- Rate limit: `60,1 (config: filament-workhub.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/workhub/labels?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/workhub/labels
- Scope: `workhub.label.manage`
- Permission: `workhub.label.manage`
- Rate limit: `60,1 (config: filament-workhub.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/workhub/labels
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/workhub/labels/{label}
- Scope: `workhub.label.manage`
- Permission: `workhub.label.manage`
- Rate limit: `60,1 (config: filament-workhub.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/workhub/labels/{label}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### PATCH /api/v1/workhub/labels/{label}
- Scope: `workhub.label.manage`
- Permission: `workhub.label.manage`
- Rate limit: `60,1 (config: filament-workhub.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/workhub/labels/{label}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/workhub/labels/{label}
- Scope: `workhub.label.manage`
- Permission: `workhub.label.manage`
- Rate limit: `60,1 (config: filament-workhub.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/workhub/labels/{label}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/workhub/links/{link}
- Scope: `workhub.link.manage`
- Permission: `workhub.link.manage`
- Rate limit: `60,1 (config: filament-workhub.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/workhub/links/{link}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/workhub/openapi
- Scope: `workhub.project.view`
- Permission: `workhub.project.view`
- Rate limit: `60,1 (config: filament-workhub.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/workhub/openapi?page=1
```

Response:
```json
{
  "openapi": "3.0.0",
  "info": {
    "title": "Module API",
    "version": "1.0.0"
  }
}
```

### POST /api/v1/workhub/projects
- Scope: `workhub.project.manage`
- Permission: `workhub.project.manage`
- Rate limit: `60,1 (config: filament-workhub.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/workhub/projects
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/workhub/projects/{project}
- Scope: `workhub.project.manage`
- Permission: `workhub.project.manage`
- Rate limit: `60,1 (config: filament-workhub.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/workhub/projects/{project}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### PATCH /api/v1/workhub/projects/{project}
- Scope: `workhub.project.manage`
- Permission: `workhub.project.manage`
- Rate limit: `60,1 (config: filament-workhub.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/workhub/projects/{project}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/workhub/projects/{project}
- Scope: `workhub.project.manage`
- Permission: `workhub.project.manage`
- Rate limit: `60,1 (config: filament-workhub.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/workhub/projects/{project}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/workhub/projects/{project}/ai/executive-summary
- Scope: `workhub.ai.project_reports.manage`
- Permission: `workhub.ai.project_reports.manage`
- Rate limit: `60,1 (config: filament-workhub.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/workhub/projects/{project}/ai/executive-summary
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "status_id": 1,
  "updated_since_days": 30,
  "limit": 50
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/workhub/projects/{project}/ai/stuck-tasks
- Scope: `workhub.ai.project_reports.manage`
- Permission: `workhub.ai.project_reports.manage`
- Rate limit: `60,1 (config: filament-workhub.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/workhub/projects/{project}/ai/stuck-tasks?days=7
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### GET /api/v1/workhub/statuses
- Scope: `workhub.project.view`
- Permission: `workhub.project.view`
- Rate limit: `60,1 (config: filament-workhub.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/workhub/statuses?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/workhub/statuses
- Scope: `workhub.project.view`
- Permission: `workhub.project.view`
- Rate limit: `60,1 (config: filament-workhub.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/workhub/statuses
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/workhub/statuses/{status}
- Scope: `workhub.project.view`
- Permission: `workhub.project.view`
- Rate limit: `60,1 (config: filament-workhub.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/workhub/statuses/{status}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/workhub/statuses/{status}
- Scope: `workhub.project.view`
- Permission: `workhub.project.view`
- Rate limit: `60,1 (config: filament-workhub.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/workhub/statuses/{status}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/workhub/statuses/{status}
- Scope: `workhub.project.view`
- Permission: `workhub.project.view`
- Rate limit: `60,1 (config: filament-workhub.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/workhub/statuses/{status}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/workhub/statuses/{status}
- Scope: `workhub.project.view`
- Permission: `workhub.project.view`
- Rate limit: `60,1 (config: filament-workhub.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/workhub/statuses/{status}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/workhub/time-entries/{timeEntry}
- Scope: `workhub.project.view`
- Permission: `workhub.project.view`
- Rate limit: `60,1 (config: filament-workhub.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/workhub/time-entries/{timeEntry}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/workhub/transitions
- Scope: `workhub.project.view`
- Permission: `workhub.project.view`
- Rate limit: `60,1 (config: filament-workhub.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/workhub/transitions?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/workhub/transitions
- Scope: `workhub.project.view`
- Permission: `workhub.project.view`
- Rate limit: `60,1 (config: filament-workhub.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/workhub/transitions
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/workhub/transitions/{transition}
- Scope: `workhub.project.view`
- Permission: `workhub.project.view`
- Rate limit: `60,1 (config: filament-workhub.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/workhub/transitions/{transition}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/workhub/transitions/{transition}
- Scope: `workhub.project.view`
- Permission: `workhub.project.view`
- Rate limit: `60,1 (config: filament-workhub.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/workhub/transitions/{transition}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/workhub/transitions/{transition}
- Scope: `workhub.project.view`
- Permission: `workhub.project.view`
- Rate limit: `60,1 (config: filament-workhub.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/workhub/transitions/{transition}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/workhub/transitions/{transition}
- Scope: `workhub.project.view`
- Permission: `workhub.project.view`
- Rate limit: `60,1 (config: filament-workhub.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/workhub/transitions/{transition}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/workhub/watchers/{watcher}
- Scope: `workhub.project.view`
- Permission: `workhub.project.view`
- Rate limit: `60,1 (config: filament-workhub.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/workhub/watchers/{watcher}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/workhub/work-items
- Scope: `workhub.project.view`
- Permission: `workhub.project.view`
- Rate limit: `60,1 (config: filament-workhub.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/workhub/work-items?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/workhub/work-items
- Scope: `workhub.project.view`
- Permission: `workhub.project.view`
- Rate limit: `60,1 (config: filament-workhub.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/workhub/work-items
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/workhub/work-items/{work-item}
- Scope: `workhub.project.view`
- Permission: `workhub.project.view`
- Rate limit: `60,1 (config: filament-workhub.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/workhub/work-items/{work-item}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/workhub/work-items/{work-item}
- Scope: `workhub.project.view`
- Permission: `workhub.project.view`
- Rate limit: `60,1 (config: filament-workhub.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/workhub/work-items/{work-item}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/workhub/work-items/{work-item}
- Scope: `workhub.project.view`
- Permission: `workhub.project.view`
- Rate limit: `60,1 (config: filament-workhub.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/workhub/work-items/{work-item}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/workhub/work-items/{work-item}
- Scope: `workhub.project.view`
- Permission: `workhub.project.view`
- Rate limit: `60,1 (config: filament-workhub.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/workhub/work-items/{work-item}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/workhub/work-items/{workItem}/ai/find-similar
- Scope: `workhub.ai.use`
- Permission: `workhub.ai.use`
- Rate limit: `60,1 (config: filament-workhub.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/workhub/work-items/{workItem}/ai/find-similar
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "limit": 5
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/workhub/work-items/{workItem}/ai/generate-subtasks
- Scope: `workhub.ai.use`
- Permission: `workhub.ai.use`
- Rate limit: `60,1 (config: filament-workhub.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/workhub/work-items/{workItem}/ai/generate-subtasks
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "max_items": 8
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/workhub/work-items/{workItem}/ai/personal-summary
- Scope: `workhub.ai.use`
- Permission: `workhub.ai.use`
- Rate limit: `60,1 (config: filament-workhub.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/workhub/work-items/{workItem}/ai/personal-summary
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "include_comments": true
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/workhub/work-items/{workItem}/ai/progress-update
- Scope: `workhub.ai.use`
- Permission: `workhub.ai.use`
- Rate limit: `60,1 (config: filament-workhub.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/workhub/work-items/{workItem}/ai/progress-update
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "window_days": 7
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/workhub/work-items/{workItem}/ai/shared-summary
- Scope: `workhub.ai.share`
- Permission: `workhub.ai.share`
- Rate limit: `60,1 (config: filament-workhub.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/workhub/work-items/{workItem}/ai/shared-summary
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "include_comments": true,
  "notify_watchers": false
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/workhub/work-items/{workItem}/ai/thread-summary
- Scope: `workhub.ai.use`
- Permission: `workhub.ai.use`
- Rate limit: `60,1 (config: filament-workhub.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/workhub/work-items/{workItem}/ai/thread-summary
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "ttl_minutes": 60
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/workhub/work-items/{workItem}/attachments
- Scope: `workhub.attachment.view`
- Permission: `workhub.attachment.view`
- Rate limit: `60,1 (config: filament-workhub.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/workhub/work-items/{workItem}/attachments?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### POST /api/v1/workhub/work-items/{workItem}/attachments
- Scope: `workhub.attachment.manage`
- Permission: `workhub.attachment.manage`
- Rate limit: `60,1 (config: filament-workhub.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/workhub/work-items/{workItem}/attachments
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "file_name": "demo.txt",
  "content_base64": "ZGVtbw=="
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/workhub/work-items/{workItem}/comments
- Scope: `workhub.comment.view`
- Permission: `workhub.comment.view`
- Rate limit: `60,1 (config: filament-workhub.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/workhub/work-items/{workItem}/comments?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### POST /api/v1/workhub/work-items/{workItem}/comments
- Scope: `workhub.comment.manage`
- Permission: `workhub.comment.manage`
- Rate limit: `60,1 (config: filament-workhub.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/workhub/work-items/{workItem}/comments
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/workhub/work-items/{workItem}/decisions
- Scope: `workhub.decision.view`
- Permission: `workhub.decision.view`
- Rate limit: `60,1 (config: filament-workhub.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/workhub/work-items/{workItem}/decisions?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### POST /api/v1/workhub/work-items/{workItem}/decisions
- Scope: `workhub.decision.manage`
- Permission: `workhub.decision.manage`
- Rate limit: `60,1 (config: filament-workhub.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/workhub/work-items/{workItem}/decisions
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/workhub/work-items/{workItem}/links
- Scope: `workhub.link.view`
- Permission: `workhub.link.view`
- Rate limit: `60,1 (config: filament-workhub.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/workhub/work-items/{workItem}/links?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### POST /api/v1/workhub/work-items/{workItem}/links
- Scope: `workhub.link.manage`
- Permission: `workhub.link.manage`
- Rate limit: `60,1 (config: filament-workhub.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/workhub/work-items/{workItem}/links
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/workhub/work-items/{workItem}/time-entries
- Scope: `workhub.project.view`
- Permission: `workhub.project.view`
- Rate limit: `60,1 (config: filament-workhub.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/workhub/work-items/{workItem}/time-entries?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### POST /api/v1/workhub/work-items/{workItem}/time-entries
- Scope: `workhub.project.view`
- Permission: `workhub.project.view`
- Rate limit: `60,1 (config: filament-workhub.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/workhub/work-items/{workItem}/time-entries
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/workhub/work-items/{workItem}/transition
- Scope: `workhub.project.view`
- Permission: `workhub.project.view`
- Rate limit: `60,1 (config: filament-workhub.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/workhub/work-items/{workItem}/transition
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/workhub/work-items/{workItem}/watchers
- Scope: `workhub.project.view`
- Permission: `workhub.project.view`
- Rate limit: `60,1 (config: filament-workhub.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/workhub/work-items/{workItem}/watchers?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### POST /api/v1/workhub/work-items/{workItem}/watchers
- Scope: `workhub.project.view`
- Permission: `workhub.project.view`
- Rate limit: `60,1 (config: filament-workhub.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/workhub/work-items/{workItem}/watchers
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/workhub/work-types
- Scope: `workhub.project.view`
- Permission: `workhub.project.view`
- Rate limit: `60,1 (config: filament-workhub.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/workhub/work-types?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/workhub/work-types
- Scope: `workhub.project.view`
- Permission: `workhub.project.view`
- Rate limit: `60,1 (config: filament-workhub.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/workhub/work-types
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/workhub/work-types/{work-type}
- Scope: `workhub.project.view`
- Permission: `workhub.project.view`
- Rate limit: `60,1 (config: filament-workhub.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/workhub/work-types/{work-type}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/workhub/work-types/{work-type}
- Scope: `workhub.project.view`
- Permission: `workhub.project.view`
- Rate limit: `60,1 (config: filament-workhub.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/workhub/work-types/{work-type}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/workhub/work-types/{work-type}
- Scope: `workhub.project.view`
- Permission: `workhub.project.view`
- Rate limit: `60,1 (config: filament-workhub.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/workhub/work-types/{work-type}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/workhub/work-types/{work-type}
- Scope: `workhub.project.view`
- Permission: `workhub.project.view`
- Rate limit: `60,1 (config: filament-workhub.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/workhub/work-types/{work-type}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/workhub/workflows
- Scope: `workhub.project.view`
- Permission: `workhub.project.view`
- Rate limit: `60,1 (config: filament-workhub.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/workhub/workflows?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/workhub/workflows
- Scope: `workhub.project.view`
- Permission: `workhub.project.view`
- Rate limit: `60,1 (config: filament-workhub.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/workhub/workflows
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/workhub/workflows/{workflow}
- Scope: `workhub.project.view`
- Permission: `workhub.project.view`
- Rate limit: `60,1 (config: filament-workhub.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/workhub/workflows/{workflow}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/workhub/workflows/{workflow}
- Scope: `workhub.project.view`
- Permission: `workhub.project.view`
- Rate limit: `60,1 (config: filament-workhub.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/workhub/workflows/{workflow}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/workhub/workflows/{workflow}
- Scope: `workhub.project.view`
- Permission: `workhub.project.view`
- Rate limit: `60,1 (config: filament-workhub.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/workhub/workflows/{workflow}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/workhub/workflows/{workflow}
- Scope: `workhub.project.view`
- Permission: `workhub.project.view`
- Rate limit: `60,1 (config: filament-workhub.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/workhub/workflows/{workflow}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

## mailtrap-core
### GET /api/v1/mailtrap/audiences
- Scope: `mailtrap.audience.view`
- Permission: `mailtrap.audience.view`
- Rate limit: `60,1`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/mailtrap/audiences?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/mailtrap/audiences
- Scope: `mailtrap.audience.manage`
- Permission: `mailtrap.audience.manage`
- Rate limit: `60,1`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/mailtrap/audiences
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "Main Audience",
  "status": "active",
  "description": "Primary list"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/mailtrap/audiences/{audience}
- Scope: `mailtrap.audience.manage`
- Permission: `mailtrap.audience.manage`
- Rate limit: `60,1`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/mailtrap/audiences/{audience}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/mailtrap/audiences/{audience}
- Scope: `mailtrap.audience.view`
- Permission: `mailtrap.audience.view`
- Rate limit: `60,1`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/mailtrap/audiences/{audience}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/mailtrap/audiences/{audience}
- Scope: `mailtrap.audience.manage`
- Permission: `mailtrap.audience.manage`
- Rate limit: `60,1`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/mailtrap/audiences/{audience}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "Updated Audience",
  "status": "inactive",
  "description": "Updated description"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/mailtrap/audiences/{audience}
- Scope: `mailtrap.audience.manage`
- Permission: `mailtrap.audience.manage`
- Rate limit: `60,1`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/mailtrap/audiences/{audience}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "Updated Audience",
  "status": "inactive",
  "description": "Updated description"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/mailtrap/audiences/{audience}/contacts
- Scope: `mailtrap.audience.view`
- Permission: `mailtrap.audience.view`
- Rate limit: `60,1`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/mailtrap/audiences/{audience}/contacts?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### POST /api/v1/mailtrap/audiences/{audience}/contacts
- Scope: `mailtrap.audience.manage`
- Permission: `mailtrap.audience.manage`
- Rate limit: `60,1`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/mailtrap/audiences/{audience}/contacts
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "email": "user@example.com",
  "name": "Demo User",
  "status": "subscribed",
  "metadata": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/mailtrap/audiences/{audience}/contacts/{contact}
- Scope: `mailtrap.audience.manage`
- Permission: `mailtrap.audience.manage`
- Rate limit: `60,1`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/mailtrap/audiences/{audience}/contacts/{contact}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### PUT /api/v1/mailtrap/audiences/{audience}/contacts/{contact}
- Scope: `mailtrap.audience.manage`
- Permission: `mailtrap.audience.manage`
- Rate limit: `60,1`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/mailtrap/audiences/{audience}/contacts/{contact}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "email": "user@example.com",
  "name": "Demo User",
  "status": "unsubscribed",
  "metadata": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/mailtrap/campaigns
- Scope: `mailtrap.campaign.view`
- Permission: `mailtrap.campaign.view`
- Rate limit: `60,1`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/mailtrap/campaigns?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/mailtrap/campaigns
- Scope: `mailtrap.campaign.manage`
- Permission: `mailtrap.campaign.manage`
- Rate limit: `60,1`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/mailtrap/campaigns
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "connection_id": 1,
  "audience_id": 2,
  "name": "Spring Campaign",
  "subject": "Welcome",
  "from_email": "noreply@example.com",
  "from_name": "Example",
  "html_body": "<p>Hello</p>",
  "text_body": "Hello",
  "status": "draft",
  "scheduled_at": "2026-01-03 10:00:00",
  "settings": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/mailtrap/campaigns/{campaign}
- Scope: `mailtrap.campaign.manage`
- Permission: `mailtrap.campaign.manage`
- Rate limit: `60,1`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/mailtrap/campaigns/{campaign}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/mailtrap/campaigns/{campaign}
- Scope: `mailtrap.campaign.view`
- Permission: `mailtrap.campaign.view`
- Rate limit: `60,1`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/mailtrap/campaigns/{campaign}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/mailtrap/campaigns/{campaign}
- Scope: `mailtrap.campaign.manage`
- Permission: `mailtrap.campaign.manage`
- Rate limit: `60,1`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/mailtrap/campaigns/{campaign}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "Updated Campaign",
  "subject": "Updated subject",
  "status": "scheduled",
  "scheduled_at": "2026-01-04 10:00:00"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/mailtrap/campaigns/{campaign}
- Scope: `mailtrap.campaign.manage`
- Permission: `mailtrap.campaign.manage`
- Rate limit: `60,1`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/mailtrap/campaigns/{campaign}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "Updated Campaign",
  "subject": "Updated subject",
  "status": "scheduled",
  "scheduled_at": "2026-01-04 10:00:00"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/mailtrap/campaigns/{campaign}/send
- Scope: `mailtrap.campaign.send`
- Permission: `mailtrap.campaign.send`
- Rate limit: `60,1`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/mailtrap/campaigns/{campaign}/send
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/mailtrap/connections
- Scope: `mailtrap.connection.manage`
- Permission: `mailtrap.connection.manage`
- Rate limit: `60,1`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/mailtrap/connections
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "Primary",
  "api_token": "token",
  "send_api_token": "send-token",
  "account_id": 123,
  "default_inbox_id": 456,
  "status": "active",
  "metadata": {},
  "test_connection": true
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/mailtrap/connections/{connection}
- Scope: `mailtrap.connection.manage`
- Permission: `mailtrap.connection.manage`
- Rate limit: `60,1`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/mailtrap/connections/{connection}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### PATCH /api/v1/mailtrap/connections/{connection}
- Scope: `mailtrap.connection.manage`
- Permission: `mailtrap.connection.manage`
- Rate limit: `60,1`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/mailtrap/connections/{connection}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "Primary",
  "status": "inactive",
  "test_connection": true
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/mailtrap/connections/{connection}
- Scope: `mailtrap.connection.manage`
- Permission: `mailtrap.connection.manage`
- Rate limit: `60,1`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/mailtrap/connections/{connection}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "Primary",
  "status": "inactive",
  "test_connection": true
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/mailtrap/domains
- Scope: `mailtrap.domain.view`
- Permission: `mailtrap.domain.view`
- Rate limit: `60,1`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/mailtrap/domains?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/mailtrap/domains
- Scope: `mailtrap.domain.manage`
- Permission: `mailtrap.domain.manage`
- Rate limit: `60,1`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/mailtrap/domains
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "connection_id": 1,
  "domain_name": "example.com"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/mailtrap/domains/sync
- Scope: `mailtrap.domain.sync`
- Permission: `mailtrap.domain.sync`
- Rate limit: `60,1`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/mailtrap/domains/sync
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "connection_id": 1,
  "force": false
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/mailtrap/domains/{domain}
- Scope: `mailtrap.domain.manage`
- Permission: `mailtrap.domain.manage`
- Rate limit: `60,1`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/mailtrap/domains/{domain}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### PATCH /api/v1/mailtrap/domains/{domain}
- Scope: `mailtrap.domain.manage`
- Permission: `mailtrap.domain.manage`
- Rate limit: `60,1`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/mailtrap/domains/{domain}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "domain_name": "example.com"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/mailtrap/domains/{domain}
- Scope: `mailtrap.domain.manage`
- Permission: `mailtrap.domain.manage`
- Rate limit: `60,1`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/mailtrap/domains/{domain}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "domain_name": "example.com"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/mailtrap/inboxes
- Scope: `mailtrap.inbox.view`
- Permission: `mailtrap.inbox.view`
- Rate limit: `60,1`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/mailtrap/inboxes?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/mailtrap/inboxes
- Scope: `mailtrap.inbox.manage`
- Permission: `mailtrap.inbox.manage`
- Rate limit: `60,1`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/mailtrap/inboxes
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "connection_id": 1,
  "name": "Main Inbox",
  "status": "active"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/mailtrap/inboxes/sync
- Scope: `mailtrap.inbox.sync`
- Permission: `mailtrap.inbox.sync`
- Rate limit: `60,1`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/mailtrap/inboxes/sync
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "connection_id": 1,
  "force": false
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/mailtrap/inboxes/{inbox}
- Scope: `mailtrap.inbox.manage`
- Permission: `mailtrap.inbox.manage`
- Rate limit: `60,1`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/mailtrap/inboxes/{inbox}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### PATCH /api/v1/mailtrap/inboxes/{inbox}
- Scope: `mailtrap.inbox.manage`
- Permission: `mailtrap.inbox.manage`
- Rate limit: `60,1`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/mailtrap/inboxes/{inbox}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "Updated Inbox",
  "status": "inactive"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/mailtrap/inboxes/{inbox}
- Scope: `mailtrap.inbox.manage`
- Permission: `mailtrap.inbox.manage`
- Rate limit: `60,1`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/mailtrap/inboxes/{inbox}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "Updated Inbox",
  "status": "inactive"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/mailtrap/messages
- Scope: `mailtrap.message.view`
- Permission: `mailtrap.message.view`
- Rate limit: `60,1`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/mailtrap/messages?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### GET /api/v1/mailtrap/messages/{message}
- Scope: `mailtrap.message.view`
- Permission: `mailtrap.message.view`
- Rate limit: `60,1`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/mailtrap/messages/{message}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### GET /api/v1/mailtrap/messages/{message}/attachments
- Scope: `mailtrap.message.view`
- Permission: `mailtrap.message.view`
- Rate limit: `60,1`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/mailtrap/messages/{message}/attachments?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### GET /api/v1/mailtrap/messages/{message}/attachments/{attachment}
- Scope: `mailtrap.message.view`
- Permission: `mailtrap.message.view`
- Rate limit: `60,1`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/mailtrap/messages/{message}/attachments/{attachment}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### GET /api/v1/mailtrap/messages/{message}/body
- Scope: `mailtrap.message.view`
- Permission: `mailtrap.message.view`
- Rate limit: `60,1`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/mailtrap/messages/{message}/body?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### GET /api/v1/mailtrap/offers
- Scope: `mailtrap.offer.view`
- Permission: `mailtrap.offer.view`
- Rate limit: `60,1`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/mailtrap/offers?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/mailtrap/offers
- Scope: `mailtrap.offer.manage`
- Permission: `mailtrap.offer.manage`
- Rate limit: `60,1`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/mailtrap/offers
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "Starter",
  "slug": "starter",
  "status": "active",
  "description": "Starter plan",
  "duration_days": 30,
  "feature_keys": [
    "feature_a",
    "feature_b"
  ],
  "limits": {},
  "price": 100000,
  "currency": "IRR",
  "metadata": {},
  "publish_to_catalog": true
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/mailtrap/offers/{offer}
- Scope: `mailtrap.offer.manage`
- Permission: `mailtrap.offer.manage`
- Rate limit: `60,1`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/mailtrap/offers/{offer}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/mailtrap/offers/{offer}
- Scope: `mailtrap.offer.view`
- Permission: `mailtrap.offer.view`
- Rate limit: `60,1`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/mailtrap/offers/{offer}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/mailtrap/offers/{offer}
- Scope: `mailtrap.offer.manage`
- Permission: `mailtrap.offer.manage`
- Rate limit: `60,1`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/mailtrap/offers/{offer}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "Starter",
  "status": "inactive",
  "price": 120000
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### PUT /api/v1/mailtrap/offers/{offer}
- Scope: `mailtrap.offer.manage`
- Permission: `mailtrap.offer.manage`
- Rate limit: `60,1`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PUT /api/v1/mailtrap/offers/{offer}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "name": "Starter",
  "status": "inactive",
  "price": 120000
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/mailtrap/openapi
- Scope: `mailtrap.connection.view`
- Permission: `mailtrap.connection.view`
- Rate limit: `60,1`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/mailtrap/openapi?page=1
```

Response:
```json
{
  "openapi": "3.0.0",
  "info": {
    "title": "Module API",
    "version": "1.0.0"
  }
}
```

### GET /api/v1/mailtrap/single-sends
- Scope: `mailtrap.connection.view`
- Permission: `mailtrap.connection.view`
- Rate limit: `60,1`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/mailtrap/single-sends?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /api/v1/mailtrap/single-sends
- Scope: `mailtrap.connection.view`
- Permission: `mailtrap.connection.view`
- Rate limit: `60,1`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/mailtrap/single-sends
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "connection_id": 1,
  "to_email": "user@example.com",
  "to_name": "Demo User",
  "subject": "Hello",
  "text_body": "Hello",
  "html_body": "<p>Hello</p>",
  "from_email": "noreply@example.com",
  "from_name": "Example"
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### GET /api/v1/mailtrap/single-sends/{single-send}
- Scope: `mailtrap.connection.view`
- Permission: `mailtrap.connection.view`
- Rate limit: `60,1`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/mailtrap/single-sends/{single-send}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

## payments-orchestrator
### GET /api/v1/commerce-payments/intents/{intent}
- Scope: `commerce.payment.manage`
- Permission: `commerce.payment.manage`
- Rate limit: `60,1 (config: payments-orchestrator.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/commerce-payments/intents/{intent}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### GET /api/v1/commerce-payments/openapi
- Scope: `commerce.payment.manage`
- Permission: `commerce.payment.manage`
- Rate limit: `60,1 (config: payments-orchestrator.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/commerce-payments/openapi?page=1
```

Response:
```json
{
  "openapi": "3.0.0",
  "info": {
    "title": "Module API",
    "version": "1.0.0"
  }
}
```

### POST /api/v1/commerce-payments/webhooks/{provider}
- Scope: `public`
- Permission: `public`
- Rate limit: `60,1`
- Tenant: `true`
- Auth: `public/none`

Request:
```http
POST /api/v1/commerce-payments/webhooks/{provider}
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "event": "sample.event",
  "payload": {
    "id": "evt_123",
    "status": "ok"
  },
  "signature": "sha256=..."
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

## providers-esim-go-core
### GET /api/v1/providers/esim-go/openapi
- Scope: `esim_go.connection.view`
- Permission: `esim_go.connection.view`
- Rate limit: `60,1 (config: providers-esim-go-core.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/providers/esim-go/openapi?page=1
```

Response:
```json
{
  "openapi": "3.0.0",
  "info": {
    "title": "Module API",
    "version": "1.0.0"
  }
}
```

### GET /api/v1/providers/esim-go/orders
- Scope: `esim_go.connection.view`
- Permission: `esim_go.connection.view`
- Rate limit: `60,1 (config: providers-esim-go-core.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/providers/esim-go/orders?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### GET /api/v1/providers/esim-go/orders/{order}
- Scope: `esim_go.connection.view`
- Permission: `esim_go.connection.view`
- Rate limit: `60,1 (config: providers-esim-go-core.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/providers/esim-go/orders/{order}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### GET /api/v1/providers/esim-go/products
- Scope: `esim_go.connection.view`
- Permission: `esim_go.connection.view`
- Rate limit: `60,1 (config: providers-esim-go-core.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/providers/esim-go/products?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### GET /api/v1/providers/esim-go/products/{product}
- Scope: `esim_go.connection.view`
- Permission: `esim_go.connection.view`
- Rate limit: `60,1 (config: providers-esim-go-core.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/providers/esim-go/products/{product}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### POST /api/v1/providers/esim-go/sync
- Scope: `esim_go.catalogue.sync`
- Permission: `esim_go.catalogue.sync`
- Rate limit: `60,1 (config: providers-esim-go-core.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/providers/esim-go/sync
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "since": "2026-01-01T00:00:00Z",
  "items": []
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

## root
### GET /
- Scope: `public`
- Permission: `public`
- Rate limit: `60,1`
- Tenant: `false`
- Auth: `public/none`

Request:
```http
GET /?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### GET /login
- Scope: `public`
- Permission: `public`
- Rate limit: `60,1`
- Tenant: `false`
- Auth: `public/none`

Request:
```http
GET /login?page=1
```

Response:
```json
{
  "data": [],
  "meta": {
    "page": 1,
    "per_page": 15
  }
}
```

### POST /ttt/analyze
- Scope: `public`
- Permission: `public`
- Rate limit: `60,1`
- Tenant: `false`
- Auth: `public/none`

Request:
```http
POST /ttt/analyze
Content-Type: application/json
```
```json
{
  "name": "نمونه",
  "status": "active",
  "meta": {}
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

## tenancy-domains
### POST /api/v1/tenancy-domains/domains
- Scope: `site.domain.manage`
- Permission: `site.domain.manage`
- Rate limit: `60,1 (config: tenancy-domains.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/tenancy-domains/domains
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "site_id": 1,
  "host": "example.com",
  "type": "custom",
  "verification_method": "txt",
  "is_primary": true
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### DELETE /api/v1/tenancy-domains/domains/{domain}
- Scope: `site.domain.manage`
- Permission: `site.domain.manage`
- Rate limit: `60,1 (config: tenancy-domains.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
DELETE /api/v1/tenancy-domains/domains/{domain}?page=1
```

Response:
```json
{
  "status": "deleted"
}
```

### GET /api/v1/tenancy-domains/domains/{domain}
- Scope: `site.domain.view`
- Permission: `site.domain.view`
- Rate limit: `60,1 (config: tenancy-domains.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
GET /api/v1/tenancy-domains/domains/{domain}?page=1
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "active"
  }
}
```

### PATCH /api/v1/tenancy-domains/domains/{domain}
- Scope: `site.domain.manage`
- Permission: `site.domain.manage`
- Rate limit: `60,1 (config: tenancy-domains.api.rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
PATCH /api/v1/tenancy-domains/domains/{domain}
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{
  "site_id": 1,
  "verification_method": "cname",
  "is_primary": true
}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/tenancy-domains/domains/{domain}/request-tls
- Scope: `site.domain.manage`
- Permission: `site.domain.manage`
- Rate limit: `20,1 (config: tenancy-domains.api.verify_rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/tenancy-domains/domains/{domain}/request-tls
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/tenancy-domains/domains/{domain}/request-verification
- Scope: `site.domain.manage`
- Permission: `site.domain.manage`
- Rate limit: `20,1 (config: tenancy-domains.api.verify_rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/tenancy-domains/domains/{domain}/request-verification
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```

### POST /api/v1/tenancy-domains/domains/{domain}/verify
- Scope: `site.domain.manage`
- Permission: `site.domain.manage`
- Rate limit: `20,1 (config: tenancy-domains.api.verify_rate_limit)`
- Tenant: `true`
- Auth: `ApiKeyAuth, ApiAuth`

Request:
```http
POST /api/v1/tenancy-domains/domains/{domain}/verify
X-Api-Key: demo-api-key
Authorization: Bearer demo-token
X-Tenant-ID: 1
Content-Type: application/json
```
```json
{}
```

Response:
```json
{
  "data": {
    "id": 1,
    "status": "ok"
  }
}
```
