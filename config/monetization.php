<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Platform Fee Settings
    |--------------------------------------------------------------------------
    */

    'platform_fee_percentage' => env('PLATFORM_FEE_PERCENTAGE', 5.0),
    'platform_fee_type' => env('PLATFORM_FEE_TYPE', 'percentage'), // percentage or fixed
    'platform_fee_fixed_amount' => env('PLATFORM_FEE_FIXED_AMOUNT', 0),

    /*
    |--------------------------------------------------------------------------
    | Invoice Settings
    |--------------------------------------------------------------------------
    */

    'auto_invoice' => env('AUTO_INVOICE', true),
    'invoice_cc_email' => env('INVOICE_CC_EMAIL', ''),
    'payment_deadline_days' => env('PAYMENT_DEADLINE_DAYS', 14),

    /*
    |--------------------------------------------------------------------------
    | Payout Settings
    |--------------------------------------------------------------------------
    */

    'minimum_payout_amount' => env('MINIMUM_PAYOUT_AMOUNT', 50.00),
    'payout_day' => env('PAYOUT_DAY', 15),

    /*
    |--------------------------------------------------------------------------
    | Currency Settings
    |--------------------------------------------------------------------------
    */

    'currency' => env('CURRENCY', 'EUR'),
    'currency_symbol' => env('CURRENCY_SYMBOL', '€'),

    /*
    |--------------------------------------------------------------------------
    | Platform Billing Data
    |--------------------------------------------------------------------------
    */

    'platform_company_name' => env('PLATFORM_COMPANY_NAME', ''),
    'platform_company_address' => env('PLATFORM_COMPANY_ADDRESS', ''),
    'platform_company_postal_code' => env('PLATFORM_COMPANY_POSTAL_CODE', ''),
    'platform_company_city' => env('PLATFORM_COMPANY_CITY', ''),
    'platform_company_country' => env('PLATFORM_COMPANY_COUNTRY', 'Deutschland'),
    'platform_tax_id' => env('PLATFORM_TAX_ID', ''),
    'platform_vat_id' => env('PLATFORM_VAT_ID', ''),
    'platform_company_email' => env('PLATFORM_COMPANY_EMAIL', ''),
    'platform_company_phone' => env('PLATFORM_COMPANY_PHONE', ''),
    'platform_bank_name' => env('PLATFORM_BANK_NAME', ''),
    'platform_bank_iban' => env('PLATFORM_BANK_IBAN', ''),
    'platform_bank_bic' => env('PLATFORM_BANK_BIC', ''),

];

