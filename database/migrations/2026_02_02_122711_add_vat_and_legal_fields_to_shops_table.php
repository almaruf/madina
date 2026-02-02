<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('shops', function (Blueprint $table) {
            // Legal Information
            $table->string('legal_company_name')->nullable()->after('name');
            $table->string('company_registration_number')->nullable()->after('legal_company_name');
            
            // VAT Information
            $table->boolean('vat_registered')->default(false)->after('currency_symbol');
            $table->string('vat_number')->nullable()->after('vat_registered');
            $table->decimal('vat_rate', 5, 2)->default(config('services.vat.default_rate', 20.00))->after('vat_number'); // Default VAT rate
            $table->boolean('prices_include_vat')->default(true)->after('vat_rate'); // Prices shown inclusive or exclusive
            
            // Bank Details
            $table->string('bank_name')->nullable()->after('whatsapp_number');
            $table->string('bank_account_name')->nullable()->after('bank_name');
            $table->string('bank_account_number')->nullable()->after('bank_account_name');
            $table->string('bank_sort_code')->nullable()->after('bank_account_number');
            $table->string('bank_iban')->nullable()->after('bank_sort_code');
            $table->string('bank_swift_code')->nullable()->after('bank_iban');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->dropColumn([
                'legal_company_name',
                'company_registration_number',
                'vat_registered',
                'vat_number',
                'vat_rate',
                'prices_include_vat',
                'bank_name',
                'bank_account_name',
                'bank_account_number',
                'bank_sort_code',
                'bank_iban',
                'bank_swift_code',
            ]);
        });
    }
};
