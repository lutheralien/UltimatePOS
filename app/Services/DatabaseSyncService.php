<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class DatabaseSyncService
{
    private $localConnection;
    private $cloudConnection;
    private $tables;
    
    public function __construct()
    {
        $this->localConnection = 'mysql';
        $this->cloudConnection = 'cloud_mysql';
        $this->tables = [
            'account_transactions', 'account_types', 'accounts', 'activity_log', 'barcodes',
            'bookings', 'brands', 'business', 'business_locations', 'cash_denominations',
            'cash_register_transactions', 'cash_registers', 'categories', 'categorizables', 'contacts',
            'currencies', 'customer_groups', 'dashboard_configurations', 'discount_variations', 'discounts',
            'document_and_notes', 'expense_categories', 'group_sub_taxes', 'invoice_layouts', 'invoice_schemes',
            'media', 'migrations', 'model_has_permissions', 'model_has_roles', 'notification_templates',
            'notifications', 'oauth_access_tokens', 'oauth_auth_codes', 'oauth_clients', 'oauth_personal_access_clients',
            'oauth_refresh_tokens', 'password_resets', 'permissions', 'printers', 'product_locations',
            'product_racks', 'product_variations', 'products', 'purchase_lines', 'reference_counts',
            'res_product_modifier_sets', 'res_tables', 'role_has_permissions', 'roles', 'sell_line_warranties',
            'selling_price_groups', 'sessions', 'stock_adjustment_lines', 'stock_adjustments_temp', 'system',
            'tax_rates', 'transaction_payments', 'transaction_sell_lines', 'transaction_sell_lines_purchase_lines', 'transactions',
            'types_of_services', 'units', 'user_contact_access', 'users', 'variation_group_prices',
            'variation_location_details', 'variation_templates', 'variation_value_templates', 'variations', 'warranties'
        ];
    }

    public function sync()
    {
        try {
            DB::beginTransaction();
            Log::info('Starting database sync...');
            
            if (!$this->isLocalOnline()) {
                Log::info('Local database offline, syncing from cloud');
                $this->syncToLocal();
            } else {
                Log::info('Local database online, syncing to cloud');
                $this->syncToCloud();
            }
            
            DB::commit();
            Log::info('Sync completed successfully');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Sync failed: ' . $e->getMessage());
            throw $e;
        }
    }

    private function isLocalOnline()
    {
        try {
            DB::connection($this->localConnection)->getPdo();
            Log::info('Local database connection test: SUCCESS');
            return true;
        } catch (Exception $e) {
            Log::warning('Local database connection test: FAILED - ' . $e->getMessage());
            return false;
        }
    }

    private function syncToCloud()
    {
        foreach ($this->tables as $table) {
            Log::info("Starting sync to cloud for table: {$table}");
            
            try {
                // Check if updated_at column exists
                $hasUpdatedAt = Schema::connection($this->localConnection)
                    ->hasColumn($table, 'updated_at');
    
                $query = DB::connection($this->localConnection)->table($table);
                
                if ($hasUpdatedAt) {
                    $lastSyncTime = DB::connection($this->cloudConnection)
                        ->table($table)
                        ->max('updated_at');
    
                    if (!$lastSyncTime) {
                        $lastSyncTime = Carbon::createFromTimestamp(0);
                    }
                    Log::info("Last sync time for {$table}: {$lastSyncTime}");
                    $query->whereRaw('updated_at > ?', [$lastSyncTime]);
                }
    
                $localData = $query->get()->map(function ($item) {
                    return (array) $item;
                });
    
                Log::info("Found {$localData->count()} records in local {$table}");
    
                if ($localData->isNotEmpty()) {
                    $upsertCount = 0;
                    foreach ($localData->chunk(100) as $chunk) {
                        try {
                            DB::connection($this->cloudConnection)
                                ->table($table)
                                ->upsert(
                                    $chunk->toArray(),
                                    ['id'],
                                    array_keys($chunk->first())
                                );
                            $upsertCount += $chunk->count();
                            Log::info("Upserted chunk of {$chunk->count()} records to cloud {$table}");
                        } catch (Exception $e) {
                            Log::error("Error upserting chunk to {$table}: " . $e->getMessage());
                            throw $e;
                        }
                    }
                    Log::info("Completed sync to cloud for {$table}. Total upserted: {$upsertCount}");
                }
            } catch (Exception $e) {
                Log::error("Error processing table {$table}: " . $e->getMessage());
                continue; // Skip to next table on error
            }
        }
    }

   
}