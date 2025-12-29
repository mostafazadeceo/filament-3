<?php

namespace Vendor\FilamentAccountingIr\Support;

class AccountingOpenApi
{
    public static function toArray(): array
    {
        return [
            'openapi' => '3.0.0',
            'info' => [
                'title' => 'Accounting IR API',
                'version' => '1.0.0',
            ],
            'paths' => [
                '/api/v1/accounting-ir/companies' => [
                    'get' => ['summary' => 'List companies'],
                    'post' => ['summary' => 'Create company'],
                ],
                '/api/v1/accounting-ir/companies/{company}' => [
                    'get' => ['summary' => 'Show company'],
                    'put' => ['summary' => 'Update company'],
                    'delete' => ['summary' => 'Delete company'],
                ],
                '/api/v1/accounting-ir/company-settings' => [
                    'get' => ['summary' => 'List company settings'],
                    'post' => ['summary' => 'Create company settings'],
                ],
                '/api/v1/accounting-ir/company-settings/{company_setting}' => [
                    'get' => ['summary' => 'Show company settings'],
                    'put' => ['summary' => 'Update company settings'],
                    'delete' => ['summary' => 'Delete company settings'],
                ],
                '/api/v1/accounting-ir/branches' => [
                    'get' => ['summary' => 'List branches'],
                    'post' => ['summary' => 'Create branch'],
                ],
                '/api/v1/accounting-ir/fiscal-years' => [
                    'get' => ['summary' => 'List fiscal years'],
                    'post' => ['summary' => 'Create fiscal year'],
                ],
                '/api/v1/accounting-ir/fiscal-periods' => [
                    'get' => ['summary' => 'List fiscal periods'],
                    'post' => ['summary' => 'Create fiscal period'],
                ],
                '/api/v1/accounting-ir/account-plans' => [
                    'get' => ['summary' => 'List account plans'],
                    'post' => ['summary' => 'Create account plan'],
                ],
                '/api/v1/accounting-ir/chart-accounts' => [
                    'get' => ['summary' => 'List chart accounts'],
                    'post' => ['summary' => 'Create chart account'],
                ],
                '/api/v1/accounting-ir/dimensions' => [
                    'get' => ['summary' => 'List dimensions'],
                    'post' => ['summary' => 'Create dimension'],
                ],
                '/api/v1/accounting-ir/journal-entries' => [
                    'get' => ['summary' => 'List journal entries'],
                    'post' => ['summary' => 'Create journal entry'],
                ],
                '/api/v1/accounting-ir/parties' => [
                    'get' => ['summary' => 'List parties'],
                    'post' => ['summary' => 'Create party'],
                ],
                '/api/v1/accounting-ir/products' => [
                    'get' => ['summary' => 'List products/services'],
                    'post' => ['summary' => 'Create product/service'],
                ],
                '/api/v1/accounting-ir/tax-categories' => [
                    'get' => ['summary' => 'List tax categories'],
                    'post' => ['summary' => 'Create tax category'],
                ],
                '/api/v1/accounting-ir/uoms' => [
                    'get' => ['summary' => 'List units of measure'],
                    'post' => ['summary' => 'Create unit of measure'],
                ],
                '/api/v1/accounting-ir/tax-rates' => [
                    'get' => ['summary' => 'List tax rates'],
                    'post' => ['summary' => 'Create tax rate'],
                ],
                '/api/v1/accounting-ir/vat-periods' => [
                    'get' => ['summary' => 'List VAT periods'],
                    'post' => ['summary' => 'Create VAT period'],
                ],
                '/api/v1/accounting-ir/vat-periods/{vat_period}/generate' => [
                    'post' => ['summary' => 'Generate VAT report'],
                ],
                '/api/v1/accounting-ir/vat-reports' => [
                    'get' => ['summary' => 'List VAT reports'],
                    'post' => ['summary' => 'Create VAT report'],
                ],
                '/api/v1/accounting-ir/vat-reports/{vat_report}/submit' => [
                    'post' => ['summary' => 'Submit VAT report'],
                ],
                '/api/v1/accounting-ir/withholding-rates' => [
                    'get' => ['summary' => 'List withholding rates'],
                    'post' => ['summary' => 'Create withholding rate'],
                ],
                '/api/v1/accounting-ir/seasonal-reports' => [
                    'get' => ['summary' => 'List seasonal reports'],
                    'post' => ['summary' => 'Create seasonal report'],
                ],
                '/api/v1/accounting-ir/sales-invoices' => [
                    'get' => ['summary' => 'List sales invoices'],
                    'post' => ['summary' => 'Create sales invoice'],
                ],
                '/api/v1/accounting-ir/sales-invoices/{sales_invoice}/issue' => [
                    'post' => ['summary' => 'Issue sales invoice'],
                ],
                '/api/v1/accounting-ir/purchase-invoices' => [
                    'get' => ['summary' => 'List purchase invoices'],
                    'post' => ['summary' => 'Create purchase invoice'],
                ],
                '/api/v1/accounting-ir/purchase-invoices/{purchase_invoice}/receive' => [
                    'post' => ['summary' => 'Receive purchase invoice'],
                ],
                '/api/v1/accounting-ir/treasury-accounts' => [
                    'get' => ['summary' => 'List treasury accounts'],
                    'post' => ['summary' => 'Create treasury account'],
                ],
                '/api/v1/accounting-ir/treasury-transactions' => [
                    'get' => ['summary' => 'List treasury transactions'],
                    'post' => ['summary' => 'Create treasury transaction'],
                ],
                '/api/v1/accounting-ir/cheques' => [
                    'get' => ['summary' => 'List cheques'],
                    'post' => ['summary' => 'Create cheque'],
                ],
                '/api/v1/accounting-ir/warehouses' => [
                    'get' => ['summary' => 'List warehouses'],
                    'post' => ['summary' => 'Create warehouse'],
                ],
                '/api/v1/accounting-ir/inventory-items' => [
                    'get' => ['summary' => 'List inventory items'],
                    'post' => ['summary' => 'Create inventory item'],
                ],
                '/api/v1/accounting-ir/inventory-docs' => [
                    'get' => ['summary' => 'List inventory docs'],
                    'post' => ['summary' => 'Create inventory doc'],
                ],
                '/api/v1/accounting-ir/inventory-docs/{inventory_doc}/post' => [
                    'post' => ['summary' => 'Post inventory doc'],
                ],
                '/api/v1/accounting-ir/fixed-assets' => [
                    'get' => ['summary' => 'List fixed assets'],
                    'post' => ['summary' => 'Create fixed asset'],
                ],
                '/api/v1/accounting-ir/employees' => [
                    'get' => ['summary' => 'List employees'],
                    'post' => ['summary' => 'Create employee'],
                ],
                '/api/v1/accounting-ir/payroll-runs' => [
                    'get' => ['summary' => 'List payroll runs'],
                    'post' => ['summary' => 'Create payroll run'],
                ],
                '/api/v1/accounting-ir/payroll-tables' => [
                    'get' => ['summary' => 'List payroll tables'],
                    'post' => ['summary' => 'Create payroll table'],
                ],
                '/api/v1/accounting-ir/projects' => [
                    'get' => ['summary' => 'List projects'],
                    'post' => ['summary' => 'Create project'],
                ],
                '/api/v1/accounting-ir/contracts' => [
                    'get' => ['summary' => 'List contracts'],
                    'post' => ['summary' => 'Create contract'],
                ],
                '/api/v1/accounting-ir/e-invoice-providers' => [
                    'get' => ['summary' => 'List e-invoice providers'],
                    'post' => ['summary' => 'Create e-invoice provider'],
                ],
                '/api/v1/accounting-ir/e-invoices' => [
                    'get' => ['summary' => 'List e-invoices'],
                    'post' => ['summary' => 'Create e-invoice'],
                ],
                '/api/v1/accounting-ir/e-invoices/{e_invoice}/send' => [
                    'post' => ['summary' => 'Send e-invoice'],
                ],
                '/api/v1/accounting-ir/key-materials' => [
                    'get' => ['summary' => 'List key materials'],
                    'post' => ['summary' => 'Create key material'],
                ],
                '/api/v1/accounting-ir/integrations' => [
                    'get' => ['summary' => 'List integrations'],
                    'post' => ['summary' => 'Create integration connector'],
                ],
                '/api/v1/accounting-ir/integrations/{integration_connector}/run' => [
                    'post' => ['summary' => 'Run integration connector'],
                ],
                '/api/v1/accounting-ir/reports/trial-balance' => [
                    'get' => ['summary' => 'Trial balance report'],
                ],
                '/api/v1/accounting-ir/reports/general-ledger' => [
                    'get' => ['summary' => 'General ledger report'],
                ],
            ],
        ];
    }
}
