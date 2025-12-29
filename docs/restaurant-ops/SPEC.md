# Restaurant Ops SPEC (Procurement + Inventory + Cost Control)

## اهداف محصول
- ایجاد ماژول جامع خرید، انبارداری و کاست‌کنترل ویژه رستوران‌های ایران (تک‌شعبه تا زنجیره‌ای).
- اتصال امن و دقیق به حسابداری (ثبت اتومات اسناد موجودی/هزینه مواد).
- کاهش ضایعات، کنترل مصرف، و شفافیت هزینه مواد غذایی.
- پشتیبانی از UI فارسی/RTL، تقویم جلالی، و مقیاس‌پذیری بین شعب.

## غیرهدف‌ها (Non-goals)
- جایگزینی کامل POS یا CRM.
- پیاده‌سازی کامل حسابداری در این ماژول (به پلاگین حسابداری واگذار می‌شود).
- ویژگی‌های نظارتی یا مانیتورینگ پرسنل.

## ماژول‌ها و نیازمندی‌های کلیدی
### خرید
- مدیریت تامین‌کنندگان و سوابق خرید.
- درخواست خرید → سفارش خرید → رسید کالا با ثبت مغایرت.
- ثبت مالیات/عوارض، هزینه‌های جانبی و برگشت خرید.

### انبارداری
- چندانباره/چندشعبه‌ای با انتقال بین انبارها.
- ثبت رسید/حواله/تعدیل/ضایعات/مصرف با سند انبار.
- موجودی لحظه‌ای، بچ/انقضا، انبارگردانی دوره‌ای (Phase 2).

### کاست‌کنترل و مهندسی منو
- تعریف Recipe و محاسبه بهای تمام‌شده هر آیتم.
- کاهش موجودی بر اساس فروش (POS یا ثبت دستی).
- گزارش انحراف مصرف، ضایعات، و تحلیل محبوبیت/سودآوری (Phase 2).

## مدل دامنه (MVP)
- Supplier (تأمین‌کننده)
- Item (کالای مواد اولیه)
- Uom (واحد سنجش)
- Warehouse (انبار) + InventoryLot (بچ/انقضا)
- PurchaseRequest + PurchaseRequestLine
- PurchaseOrder + PurchaseOrderLine
- GoodsReceipt + GoodsReceiptLine
- InventoryDoc + InventoryDocLine + StockMove + InventoryBalance
- Recipe + RecipeLine (مواد اولیه و Yield)
- MenuItem
- MenuSale + MenuSaleLine (ورودی فروش برای مصرف مواد)

## جریان‌های Posting
- رسید کالا → ایجاد سند انبار (Receipt) → StockMove + InventoryBalance + InventoryLot.
- سند انبار (Receipt/Issue/Transfer/Waste/Adjustment/Consumption) → ثبت قطعی و به‌روزرسانی موجودی.
- فروش منو → تولید سند مصرف (Consumption) براساس Recipe → کاهش موجودی.

## معماری داده و ایندکس‌ها
- همه جداول دارای `tenant_id`, `company_id`, `branch_id` (در صورت نیاز).
- ایندکس‌های کلیدی: `tenant_id`, `company_id`, `branch_id`, `warehouse_id`, `item_id`, `status`, `doc_date`, `created_at`.
- FK به `tenants` و به `accounting_ir_companies` و `accounting_ir_branches`.
- لینک اختیاری بین ماژول‌ها:
  - `restaurant_suppliers.accounting_party_id` → `accounting_ir_parties`
  - `restaurant_items.accounting_inventory_item_id` → `accounting_ir_inventory_items`
  - `restaurant_warehouses.accounting_inventory_warehouse_id` → `accounting_ir_inventory_warehouses`
  - `restaurant_inventory_docs.accounting_inventory_doc_id` → `accounting_ir_inventory_docs`
  - `restaurant_goods_receipts.accounting_journal_entry_id` → `accounting_ir_journal_entries`
  - `restaurant_menu_sales.accounting_journal_entry_id` → `accounting_ir_journal_entries`
- Soft delete برای داده‌های مرجع (supplier/item/warehouse/recipe/menu_item).

## امنیت و نقش‌ها
- مجوزهای ریز برای هر عمل: ایجاد/تأیید/ارسال/ثبت قطعی/گزارش.
- تفکیک وظایف در فرآیندها (ایجاد ≠ تأیید ≠ ثبت قطعی).
- همه عملیات حساس لاگ می‌شوند و از IAM Suite پیروی می‌کنند.

## نقشه UI (Filament v4)
- اطلاعات پایه: تامین‌کنندگان، کالاها، واحدها، انبارها
- خرید: درخواست خرید، سفارش خرید، رسید کالا
- انبار: اسناد انبار، گردش و موجودی
- کاست‌کنترل: دستور پخت، آیتم‌های منو، فروش/مصرف

## API v1
- `/api/v1/restaurant-ops/suppliers`
- `/api/v1/restaurant-ops/items`
- `/api/v1/restaurant-ops/warehouses`
- `/api/v1/restaurant-ops/purchase-requests`
- `/api/v1/restaurant-ops/purchase-orders`
- `/api/v1/restaurant-ops/goods-receipts`
- `/api/v1/restaurant-ops/inventory-docs`
- `/api/v1/restaurant-ops/recipes`
- `/api/v1/restaurant-ops/menu-items`
- `/api/v1/restaurant-ops/menu-sales`
- `/api/v1/restaurant-ops/openapi`

## Webhooks (برنامه)
- `purchase_order.sent`
- `goods_receipt.posted`
- `inventory_doc.posted`
- `menu_sale.posted`
- `recipe.cost_updated`

## فازبندی
- MVP: خرید + انبار + Recipe + مصرف بر اساس فروش
- Phase 2: مهندسی منو، داشبورد KPI، انبارگردانی، انتقال دو مرحله‌ای
- Phase 3: AI (OCR فاکتور، پیش‌بینی تقاضا، دستیار فارسی)

## فرضیات
- پلاگین حسابداری فعال است و برای اسناد مالی از آن استفاده می‌شود.
- داده‌های پایه (company/branch) از ماژول حسابداری دریافت می‌شود.
- در صورت نبود `warehouse_id` برای فروش منو، انبار پیش‌فرض از شعبه انتخاب می‌شود.
