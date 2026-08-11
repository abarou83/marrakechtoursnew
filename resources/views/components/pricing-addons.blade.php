@props(['pricingMode', 'tourId', 'season' => null, 'date' => null])

<div id="pricing-addons-section" class="mb-6" style="display: none;">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center">
                <div class="bg-gray-100 rounded-lg p-3 mr-4">
                    <i class="fas fa-plus-circle text-gray-600 text-lg"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Available Add-ons</h3>
                    <p class="text-sm text-gray-500" id="addons-subtitle">Select add-ons for your booking</p>
                </div>
            </div>
        </div>

        <div id="addons-loading" class="text-center py-8" style="display: none;">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-gray-600"></div>
            <p class="mt-2 text-sm text-gray-500">Loading add-ons...</p>
        </div>

        <div id="addons-container" class="space-y-3">
            <!-- Addons will be loaded dynamically here -->
        </div>

        <div id="addons-empty" class="text-center py-12 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300" style="display: none;">
            <i class="fas fa-inbox text-4xl text-gray-400 mb-3"></i>
            <p class="text-gray-500">No add-ons available for this pricing mode</p>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function() {
    'use strict';

    const tourId = {{ $tourId }};
    let currentPricingMode = '{{ $pricingMode }}';
    let selectedAddons = {};
    let availableAddons = [];

    /**
     * Load addons for the selected pricing mode
     */
    async function loadAddons(pricingMode, season = null, date = null) {
        if (!pricingMode || !['group', 'private'].includes(pricingMode)) {
            hideAddonsSection();
            return;
        }

        currentPricingMode = pricingMode;
        
        // Show loading state
        showLoading();
        hideAddonsContainer();
        hideEmptyState();

        try {
            // Build API URL
            let url = `/api/v1/tours/${tourId}/pricing/${pricingMode}/addons`;
            const params = new URLSearchParams();
            if (season) params.append('season', season);
            if (date) params.append('date', date);
            if (params.toString()) {
                url += '?' + params.toString();
            }

            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();

            if (data.success && data.addons && data.addons.length > 0) {
                availableAddons = data.addons;
                renderAddons(data.addons);
                showAddonsSection();
            } else {
                availableAddons = [];
                showEmptyState();
                showAddonsSection();
            }
        } catch (error) {
            console.error('Error loading addons:', error);
            availableAddons = [];
            showEmptyState();
            showAddonsSection();
        } finally {
            hideLoading();
        }
    }

    /**
     * Render addons in the container
     */
    function renderAddons(addons) {
        const container = document.getElementById('addons-container');
        if (!container) return;

        container.innerHTML = '';

        // Reset selected addons when switching pricing modes
        selectedAddons = {};

        addons.forEach(addon => {
            const addonDiv = document.createElement('div');
            const isRequired = addon.is_required === true;
            const isIncluded = addon.is_included === true;
            const price = addon.price || 0;
            const pricingLabel = isIncluded ? 'Included' : getPricingLabel(addon.pricing_type, price, currentPricingMode);
            
            // Different styling for included addons
            if (isIncluded) {
                addonDiv.className = 'flex items-start justify-between p-4 border-2 border-green-200 rounded-lg bg-green-50';
            } else {
                addonDiv.className = 'flex items-start justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors';
            }
            addonDiv.dataset.addonId = addon.id;

            // Build badge HTML
            let badgeHtml = '';
            if (isIncluded) {
                badgeHtml = '<span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-green-100 text-green-800"><i class="fas fa-gift mr-1"></i>Included</span>';
            } else if (isRequired) {
                badgeHtml = '<span class="ml-2 text-xs text-red-600 font-semibold">(Required)</span>';
            }

            addonDiv.innerHTML = `
                <div class="flex items-start flex-1">
                    <input 
                        type="checkbox" 
                        id="addon_${addon.id}" 
                        name="selected_addons[${addon.id}]" 
                        value="1"
                        class="mt-1 h-4 w-4 ${isIncluded ? 'text-green-600' : 'text-gray-800'} border-gray-300 rounded focus:ring-gray-500"
                        ${(isRequired || isIncluded) ? 'checked disabled' : ''}
                        data-addon-id="${addon.id}"
                        data-pricing-type="${addon.pricing_type}"
                        data-price="${price}"
                        data-is-required="${isRequired ? '1' : '0'}"
                        data-is-included="${isIncluded ? '1' : '0'}"
                    >
                    <label for="addon_${addon.id}" class="ml-3 flex-1 cursor-pointer">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="font-medium text-gray-900">${escapeHtml(addon.name)}</span>
                                ${badgeHtml}
                                <div class="text-xs text-gray-500 mt-1">
                                    ${escapeHtml(addon.pricing_type.replace('_', ' '))}
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="font-semibold ${isIncluded ? 'text-green-600' : 'text-gray-900'}">${pricingLabel}</div>
                            </div>
                        </div>
                    </label>
                </div>
            `;

            container.appendChild(addonDiv);

            // If required or included, add to selected addons (included addons are always selected but free)
            if (isRequired || isIncluded) {
                selectedAddons[addon.id] = 1;
            }

            // Add event listener for checkbox change (only for non-required and non-included)
            const checkbox = addonDiv.querySelector('input[type="checkbox"]');
            if (checkbox && !isRequired && !isIncluded) {
                checkbox.addEventListener('change', function(e) {
                    if (e.target.checked) {
                        selectedAddons[addon.id] = 1;
                    } else {
                        delete selectedAddons[addon.id];
                    }
                    triggerPriceRecalculation();
                });
            }
        });

        showAddonsContainer();
    }

    /**
     * Get pricing label based on pricing type and mode
     */
    function getPricingLabel(pricingType, price, mode) {
        if (price === 0) {
            return 'Free';
        }

        const locale = @js(app()->getLocale() === 'fr' ? 'fr-FR' : (app()->getLocale() === 'es' ? 'es-ES' : 'en-US'));
        const formattedPrice = new Intl.NumberFormat(locale, {
            style: 'currency',
            currency: 'EUR',
            minimumFractionDigits: 2
        }).format(price);

        if (pricingType === 'per_person') {
            return `+${formattedPrice} / person`;
        } else if (pricingType === 'per_group') {
            return `+${formattedPrice} / group`;
        } else {
            return formattedPrice;
        }
    }

    /**
     * Show/hide functions
     */
    function showAddonsSection() {
        const section = document.getElementById('pricing-addons-section');
        if (section) section.style.display = 'block';
    }

    function hideAddonsSection() {
        const section = document.getElementById('pricing-addons-section');
        if (section) section.style.display = 'none';
    }

    function showLoading() {
        const loading = document.getElementById('addons-loading');
        if (loading) loading.style.display = 'block';
    }

    function hideLoading() {
        const loading = document.getElementById('addons-loading');
        if (loading) loading.style.display = 'none';
    }

    function showAddonsContainer() {
        const container = document.getElementById('addons-container');
        if (container) container.style.display = 'block';
    }

    function hideAddonsContainer() {
        const container = document.getElementById('addons-container');
        if (container) container.style.display = 'none';
    }

    function showEmptyState() {
        const empty = document.getElementById('addons-empty');
        if (empty) empty.style.display = 'block';
    }

    function hideEmptyState() {
        const empty = document.getElementById('addons-empty');
        if (empty) empty.style.display = 'none';
    }

    /**
     * Escape HTML to prevent XSS
     */
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    /**
     * Trigger price recalculation event
     */
    function triggerPriceRecalculation() {
        const event = new CustomEvent('addons-changed', {
            detail: {
                selectedAddons: selectedAddons,
                availableAddons: availableAddons
            }
        });
        window.dispatchEvent(event);
    }

    /**
     * Get selected addons for form submission
     */
    function getSelectedAddons() {
        return selectedAddons;
    }

    /**
     * Initialize on DOM ready
     */
    document.addEventListener('DOMContentLoaded', function() {
        // Listen for pricing mode changes (radio buttons)
        const pricingModeRadios = document.querySelectorAll('input[name="pricing_mode"]');
        pricingModeRadios.forEach(radio => {
            radio.addEventListener('change', function(e) {
                const mode = e.target.value;
                loadAddons(mode);
            });
        });

        // Listen for pricing mode changes (select dropdown)
        const pricingModeSelect = document.getElementById('pricing_mode');
        if (pricingModeSelect) {
            pricingModeSelect.addEventListener('change', function(e) {
                const mode = e.target.value;
                loadAddons(mode);
            });

            // Load addons for initial mode if set
            if (pricingModeSelect.value) {
                loadAddons(pricingModeSelect.value);
            }
        }

        // Check initial radio selection
        const checkedRadio = document.querySelector('input[name="pricing_mode"]:checked');
        if (checkedRadio) {
            loadAddons(checkedRadio.value);
        }

        // Listen for date changes (to determine season)
        const dateInput = document.getElementById('booking_date') || document.getElementById('date');
        if (dateInput) {
            dateInput.addEventListener('change', function(e) {
                if (currentPricingMode) {
                    loadAddons(currentPricingMode, null, e.target.value);
                }
            });
        }

        // Expose getSelectedAddons globally for form submission
        window.getSelectedPricingAddons = getSelectedAddons;
    });

    // Expose loadAddons for external calls
    window.loadPricingAddons = loadAddons;
})();
</script>
@endpush

