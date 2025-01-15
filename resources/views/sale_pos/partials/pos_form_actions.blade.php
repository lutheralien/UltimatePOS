@php
    $is_mobile = isMobile();
@endphp
<div class="row">
    <div class="pos-form-actions tw-rounded-tr-xl tw-rounded-tl-xl tw-shadow-[rgba(17,_17,_26,_0.1)_0px_0px_16px] tw-bg-white tw-cursor-pointer">
        <div class="tw-flex tw-items-center tw-justify-between tw-flex-col sm:tw-flex-row md:tw-flex-row lg:tw-flex-row xl:tw-flex-row tw-gap-2 tw-px-4 tw-py-0 tw-overflow-x-auto tw-w-full">
            <!-- Mobile Total Display -->
            <div class="md:!tw-w-none !tw-flex md:!tw-hidden !tw-flex-row !tw-items-center !tw-gap-3">
                <div class="tw-pos-total tw-flex tw-items-center tw-gap-3">
                    <div class="tw-text-black tw-font-bold tw-text-sm tw-flex tw-items-center tw-flex-col tw-leading-1">
                        <div>@lang('sale.total_payable'):</div>
                    </div>
                    <input type="hidden" name="final_total" id="final_total_input" value="0.00">
                    <span id="total_payable" class="tw-text-green-900 tw-font-bold tw-text-sm number">0.00</span>
                </div>
            </div>

            <!-- Mobile Action Buttons -->
            <div class="!tw-w-full md:!tw-w-none !tw-flex md:!tw-hidden !tw-flex-row !tw-items-center !tw-gap-3">
                @if (!Gate::check('disable_pay_checkout') || auth()->user()->can('superadmin') || auth()->user()->can('admin'))
                    <button type="button" class="tw-flex tw-flex-row tw-items-center tw-justify-center tw-gap-1 tw-font-bold tw-text-white tw-cursor-pointer tw-text-xs md:tw-text-sm tw-bg-[#001F3E] tw-rounded-md tw-p-2 tw-w-[8.5rem] @if (!$is_mobile)  @endif no-print @if ($pos_settings['disable_pay_checkout'] != 0) hide @endif" id="pos-finalize" title="@lang('lang_v1.tooltip_checkout_multi_pay')">
                        <i class="fas fa-money-check-alt" aria-hidden="true"></i> @lang('lang_v1.checkout_multi_pay')
                    </button>
                @endif

                @if (!Gate::check('disable_express_checkout') || auth()->user()->can('superadmin') || auth()->user()->can('admin'))
                    <button type="button" class="tw-font-bold tw-text-white tw-cursor-pointer tw-text-xs md:tw-text-sm tw-bg-[rgb(40,183,123)] tw-p-2 tw-rounded-md tw-w-[5.5rem] tw-flex tw-flex-row tw-items-center tw-justify-center tw-gap-1 @if (!$is_mobile)  @endif no-print @if ($pos_settings['disable_express_checkout'] != 0 || !array_key_exists('cash', $payment_types)) hide @endif pos-express-finalize @if ($is_mobile) col-xs-6 @endif" data-pay_method="cash" title="@lang('tooltip.express_checkout')">
                        <i class="fas fa-money-bill-alt" aria-hidden="true"></i> @lang('lang_v1.express_checkout_cash')
                    </button>
                @endif

                @if (empty($edit))
                    <button type="button" class="tw-font-bold tw-text-white tw-cursor-pointer tw-text-xs md:tw-text-sm tw-bg-red-600 tw-p-2 tw-rounded-md tw-w-[5.5rem] tw-flex tw-flex-row tw-items-center tw-justify-center tw-gap-1" id="pos-cancel">
                        <i class="fas fa-window-close"></i> @lang('sale.cancel')
                    </button>
                @else
                    <button type="button" class="btn-danger tw-dw-btn hide tw-dw-btn-xs" id="pos-delete" @if (!empty($only_payment)) disabled @endif>
                        <i class="fas fa-trash-alt"></i> @lang('messages.delete')
                    </button>
                @endif
            </div>

            <!-- Main Action Buttons -->
            <div class="tw-flex tw-items-center tw-gap-4 tw-flex-row tw-overflow-x-auto">
                @if (!Gate::check('disable_draft') || auth()->user()->can('superadmin') || auth()->user()->can('admin'))
                    <button type="button" class="tw-font-bold tw-text-gray-700 tw-text-xs md:tw-text-sm tw-flex tw-flex-col tw-items-center tw-justify-center tw-gap-1 @if ($pos_settings['disable_draft'] != 0) hide @endif" id="pos-draft" @if (!empty($only_payment)) disabled @endif>
                        <i class="fas fa-edit tw-text-[#009ce4]"></i> @lang('sale.draft')
                    </button>
                @endif

                @if (!Gate::check('disable_quotation') || auth()->user()->can('superadmin') || auth()->user()->can('admin'))
                    <button type="button" class="tw-font-bold tw-text-gray-700 tw-cursor-pointer tw-text-xs md:tw-text-sm tw-flex tw-flex-col tw-items-center tw-justify-center tw-gap-1 @if ($is_mobile) col-xs-6 @endif" id="pos-quotation" @if (!empty($only_payment)) disabled @endif>
                        <i class="fas fa-edit tw-text-[#E7A500]"></i> @lang('lang_v1.quotation')
                    </button>
                @endif

                @if (!Gate::check('disable_suspend_sale') || auth()->user()->can('superadmin') || auth()->user()->can('admin'))
                    @if (empty($pos_settings['disable_suspend']))
                        <button type="button" class="tw-font-bold tw-text-gray-700 tw-cursor-pointer tw-text-xs md:tw-text-sm tw-flex tw-flex-col tw-items-center tw-justify-center tw-gap-1 no-print pos-express-finalize" data-pay_method="suspend" title="@lang('lang_v1.tooltip_suspend')" @if (!empty($only_payment)) disabled @endif>
                            <i class="fas fa-pause tw-text-[#EF4B51]" aria-hidden="true"></i> @lang('lang_v1.suspend')
                        </button>
                    @endif
                @endif

                @if (!Gate::check('disable_credit_sale') || auth()->user()->can('superadmin') || auth()->user()->can('admin'))
                    @if (empty($pos_settings['disable_credit_sale_button']))
                        <input type="hidden" name="is_credit_sale" value="0" id="is_credit_sale">
                        <button type="button" class="tw-font-bold tw-text-gray-700 tw-cursor-pointer tw-text-xs md:tw-text-sm tw-flex tw-flex-col tw-items-center tw-justify-center tw-gap-1 no-print pos-express-finalize @if ($is_mobile) col-xs-6 @endif" data-pay_method="credit_sale" title="@lang('lang_v1.tooltip_credit_sale')" @if (!empty($only_payment)) disabled @endif>
                            <i class="fas fa-check tw-text-[#5E5CA8]" aria-hidden="true"></i> @lang('lang_v1.credit_sale')
                        </button>
                    @endif
                @endif

                @if (!Gate::check('disable_card') || auth()->user()->can('superadmin') || auth()->user()->can('admin'))
                    <button type="button" class="tw-font-bold tw-text-gray-700 tw-cursor-pointer tw-text-xs md:tw-text-sm tw-flex tw-flex-col tw-items-center tw-justify-center tw-gap-1 no-print @if (!empty($pos_settings['disable_suspend'])) @endif pos-express-finalize @if (!array_key_exists('card', $payment_types)) hide @endif @if ($is_mobile) col-xs-6 @endif" data-pay_method="card" title="@lang('lang_v1.tooltip_express_checkout_card')">
                        <i class="fas fa-credit-card tw-text-[#D61B60]" aria-hidden="true"></i> @lang('lang_v1.express_checkout_card')
                    </button>
                @endif

                @if(auth()->user()->can('superadmin') || auth()->user()->can('admin'))
                    <button type="button" 
                        id="sync-database" 
                        class="tw-font-bold tw-text-gray-700 tw-cursor-pointer tw-text-xs md:tw-text-sm tw-flex tw-flex-col tw-items-center tw-justify-center tw-gap-1">
                        <i class="fas fa-sync tw-text-[#009ce4]"></i> Sync DB
                    </button>
                @endif

                <!-- Connection Status Indicator -->
                <div id="connection-status" class="tw-text-center tw-font-bold tw-px-4 tw-py-2 tw-rounded-md tw-flex tw-items-center tw-gap-2">
                    <i class="status-icon fas fa-wifi tw-text-lg"></i>
                    <span class="status-text">Checking...</span>
                </div>

                <!-- Offline Save Button -->
                <button type="button" id="offline-save" class="tw-hidden tw-font-bold tw-text-dark tw-bg-amber-600 hover:tw-bg-amber-700 tw-p-2 tw-rounded-md tw-flex tw-items-center tw-gap-2"></button>

                @if (!Gate::check('disable_pay_checkout') || auth()->user()->can('superadmin') || auth()->user()->can('admin'))
                    <button type="button" class="tw-hidden md:tw-flex md:tw-flex-row md:tw-items-center md:tw-justify-center md:tw-gap-1 tw-font-bold tw-text-white tw-cursor-pointer tw-text-xs md:tw-text-sm tw-bg-[#001F3E] tw-rounded-md tw-p-2 tw-w-[8.5rem] @if (!$is_mobile) @endif no-print @if ($pos_settings['disable_pay_checkout'] != 0) hide @endif" id="pos-finalize" title="@lang('lang_v1.tooltip_checkout_multi_pay')">
                        <i class="fas fa-money-check-alt" aria-hidden="true"></i> @lang('lang_v1.checkout_multi_pay')
                    </button>
                @endif

                @if (!Gate::check('disable_express_checkout') || auth()->user()->can('superadmin') || auth()->user()->can('admin'))
                    <button type="button" class="tw-font-bold tw-text-white tw-cursor-pointer tw-text-xs md:tw-text-sm tw-bg-[rgb(40,183,123)] tw-p-2 tw-rounded-md tw-w-[8.5rem] tw-hidden md:tw-flex lg:tw-flex lg:tw-flex-row lg:tw-items-center lg:tw-justify-center lg:tw-gap-1 @if (!$is_mobile) @endif no-print @if ($pos_settings['disable_express_checkout'] != 0 || !array_key_exists('cash', $payment_types)) hide @endif pos-express-finalize" data-pay_method="cash" title="@lang('tooltip.express_checkout')">
                        <i class="fas fa-money-bill-alt" aria-hidden="true"></i> @lang('lang_v1.express_checkout_cash')
                    </button>
                @endif

                @if (empty($edit))
                    <button type="button" class="tw-font-bold tw-text-white tw-cursor-pointer tw-text-xs md:tw-text-sm tw-bg-red-600 tw-p-2 tw-rounded-md tw-w-[8.5rem] tw-hidden md:tw-flex lg:tw-flex lg:tw-flex-row lg:tw-items-center lg:tw-justify-center lg:tw-gap-1" id="pos-cancel">
                        <i class="fas fa-window-close"></i> @lang('sale.cancel')
                    </button>
                @else
                    <button type="button" class="tw-font-bold tw-text-white tw-cursor-pointer tw-text-xs md:tw-text-sm tw-bg-red-600 tw-p-2 tw-rounded-md tw-w-[8.5rem] tw-hidden md:tw-flex lg:tw-flex lg:tw-flex-row lg:tw-items-center lg:tw-justify-center lg:tw-gap-1 hide" id="pos-delete" @if (!empty($only_payment)) disabled @endif>
                        <i class="fas fa-trash-alt"></i> @lang('messages.delete')
                    </button>
                @endif
            </div>

            <!-- Desktop Total Display -->
            @if (!$is_mobile)
                <div class="pos-total md:tw-flex md:tw-items-center md:tw-gap-3 tw-hidden">
                    <div class="tw-text-black tw-font-bold tw-text-base md:tw-text-2xl tw-flex tw-items-center tw-flex-col">
                        <div>Total</div>
                        <div>Payable:</div>
                    </div>
                    <input type="hidden" name="final_total" id="final_total_input" value="0.00">
                    <span id="total_payable" class="tw-text-green-900 tw-font-bold tw-text-base md:tw-text-2xl number">0.00</span>
                </div>
            @endif

            <!-- Recent Transactions Button -->
            <div class="tw-w-full md:tw-w-fit tw-flex tw-flex-col tw-items-end tw-gap-3 tw-hidden md:tw-block">
                @if (!isset($pos_settings['hide_recent_trans']) || $pos_settings['hide_recent_trans'] == 0)
                    <button type="button" class="tw-font-bold tw-bg-[#646EE4] hover:tw-bg-[#414aac] tw-rounded-full tw-text-white tw-w-full md:tw-w-fit tw-px-5 tw-h-11 tw-cursor-pointer tw-text-xs md:tw-text-sm" data-toggle="modal" data-target="#recent_transactions_modal" id="recent-transactions">
                        <i class="fas fa-clock"></i> @lang('lang_v1.recent_transactions')
                    </button>
                @endif
            </div>
        </div>
    </div>
    <form data-business-name="{{ $business_details->name }}"
      data-business-address="{{ $business_details->address }}"
      data-business-phone="{{ $business_details->phone }}">
    </form>
    </div>

<!-- Include Modals -->
@if (isset($transaction))
    @include('sale_pos.partials.edit_discount_modal', [
        'sales_discount' => $transaction->discount_amount,
        'discount_type' => $transaction->discount_type,
        'rp_redeemed' => $transaction->rp_redeemed,
        'rp_redeemed_amount' => $transaction->rp_redeemed_amount,
        'max_available' => !empty($redeem_details['points']) ? $redeem_details['points'] : 0,
    ])
@else
    @include('sale_pos.partials.edit_discount_modal', [
        'sales_discount' => $business_details->default_sales_discount,
        'discount_type' => 'percentage',
        'rp_redeemed' => 0,
        'rp_redeemed_amount' => 0,
        'max_available' => 0,
    ])
@endif

@if (isset($transaction))
    @include('sale_pos.partials.edit_order_tax_modal', ['selected_tax' => $transaction->tax_id])
@else
    @include('sale_pos.partials.edit_order_tax_modal', [
        'selected_tax' => $business_details->default_sales_tax,
    ])
@endif

@include('sale_pos.partials.edit_shipping_modal')

<style>
/* Connection Status Animations and Styles */
@keyframes pulse {
    0% { transform: scale(0.95); opacity: 0.5; }
    50% { transform: scale(1.05); opacity: 0.8; }
    100% { transform: scale(0.95); opacity: 0.5; }
}

#connection-status {
    transition: all 0.3s ease;
    position: relative;
    z-index: 1000;
}

#connection-status .status-dot {
    animation: pulse 2s infinite;
    display: inline-block;
    vertical-align: middle;
}

#connection-status.online {
    background-color: rgb(22 163 74);
    color: white;
    box-shadow: 0 2px 4px rgba(22, 163, 74, 0.2);
}

#connection-status.offline {
    background-color: rgb(220 38 38);
    color: white;
    box-shadow: 0 2px 4px rgba(220, 38, 38, 0.2);
}

#connection-status.online .status-dot {
    background-color: rgb(187 247 208);
    box-shadow: 0 0 4px rgb(187 247 208);
}

#connection-status.offline .status-dot {
    background-color: rgb(254 202 202);
    box-shadow: 0 0 4px rgb(254 202 202);
}

/* Responsive adjustments */
@media (max-width: 768px) {
    #connection-status {
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
    }
    
    #connection-status .status-dot {
        width: 0.375rem;
        height: 0.375rem;
    }
}

/* Print styles */
@media print {
    #connection-status {
        display: none !important;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusElement = document.getElementById('connection-status');
    const statusText = statusElement.querySelector('.status-text');
    const statusIcon = statusElement.querySelector('.status-icon');
    const offlineSaveButton = document.getElementById('offline-save');
    let lastOnlineStatus = navigator.onLine;
    let checkCount = 0;
    let consecutiveFailures = 0;

    function updateOnlineStatus(isOnline) {
        if (isOnline !== lastOnlineStatus || checkCount === 0) {
            statusElement.classList.remove(lastOnlineStatus ? 'online' : 'offline');
            statusElement.classList.add(isOnline ? 'online' : 'offline');
            
            if (isOnline) {
                statusText.textContent = 'Online';
                statusIcon.classList.remove('fa-wifi');
                statusIcon.classList.add('fa-check');
                offlineSaveButton.classList.add('tw-hidden');
                consecutiveFailures = 0;
            } else {
                statusText.textContent = 'Offline';
                statusIcon.classList.remove('fa-check');
                statusIcon.classList.add('fa-wifi');
                offlineSaveButton.classList.remove('tw-hidden');
                if (++consecutiveFailures >= 3) {
                    console.warn('Connection appears to be persistently offline');
                }
            }
            
            lastOnlineStatus = isOnline;
            checkCount++;
            
            window.dispatchEvent(new CustomEvent('connectionStatusChanged', {
                detail: { isOnline: isOnline }
            }));
        }
    }

    async function checkConnection() {
        try {
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 5000);

            const startTime = performance.now();
            const response = await fetch('https://www.google.com/favicon.ico', {
                method: 'HEAD',
                mode: 'no-cors',
                cache: 'no-store',
                signal: controller.signal
            });
            const endTime = performance.now();
            
            clearTimeout(timeoutId);
            
            const responseTime = endTime - startTime;
            if (responseTime > 2000) {
                console.warn('Slow connection detected:', responseTime + 'ms');
            }
            
            updateOnlineStatus(true);
        } catch (error) {
            console.error('Connection check failed:', error.name);
            updateOnlineStatus(false);
        }
    }

    // Database sync functionality
    const syncButton = document.getElementById('sync-database');
    if (syncButton) {
        syncButton.addEventListener('click', async function() {
            if (!navigator.onLine) {
                alert('Cannot sync database while offline. Please check your connection.');
                return;
            }

            syncButton.disabled = true;
            const originalContent = syncButton.innerHTML;
            syncButton.innerHTML = '<i class="fas fa-spinner fa-spin tw-text-[#009ce4]"></i> Syncing...';

            try {
                const response = await fetch('/sync-database', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });

                const data = await response.json();

                if (response.ok) {
                    toastr.success("Database sync completed successfully");
                } else {
                    throw new Error(data.error || 'Sync failed');
                }
            } catch (error) {
                toastr.error('Failed to sync database: ' + error.message);
            } finally {
                syncButton.disabled = false;
                syncButton.innerHTML = originalContent;
            }
        });

        // Integrate with connection status monitoring
        window.addEventListener('connectionStatusChanged', function(event) {
            syncButton.disabled = !event.detail.isOnline;
            syncButton.title = event.detail.isOnline ? 'Sync database' : 'Cannot sync while offline';
        });
    }

    document.addEventListener('visibilitychange', function() {
        if (document.visibilityState === 'visible') {
            checkConnection();
        }
    });

    window.addEventListener('online', () => {
        console.log('Browser reports online status');
        updateOnlineStatus(true);
    });
    
    window.addEventListener('offline', () => {
        console.log('Browser reports offline status');
        updateOnlineStatus(false);
    });

    offlineSaveButton.addEventListener('click', () => {
        alert('Offline data saved!');
    });

    checkConnection();

    const intervalId = setInterval(checkConnection, 3000);

    window.addEventListener('beforeunload', () => {
        clearInterval(intervalId);
    });

    window.addEventListener('error', function(event) {
        if (event.message.includes('connection') || event.message.includes('network')) {
            console.error('Connection monitoring error:', event);
        }
    });
});
</script>