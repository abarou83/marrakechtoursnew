@if(config('marketing.ga4_measurement_id') || config('marketing.meta_pixel_id'))
<script @isset($cspNonce) nonce="{{ $cspNonce }}" @endisset>
    window.dataLayer = window.dataLayer || [];
    window.marrakechtoursAnalytics = {
        push: function(event, data) {
            window.dataLayer.push(Object.assign({ event: event }, data || {}));
        }
    };

    function loadMarketingScripts() {
        @if(config('marketing.ga4_measurement_id'))
        if (!window.ga4Loaded) {
            var gaScript = document.createElement('script');
            gaScript.async = true;
            gaScript.src = 'https://www.googletagmanager.com/gtag/js?id={{ config('marketing.ga4_measurement_id') }}';
            document.head.appendChild(gaScript);
            window.dataLayer.push(['js', new Date()]);
            window.dataLayer.push(['config', '{{ config('marketing.ga4_measurement_id') }}', { anonymize_ip: true }]);
            window.ga4Loaded = true;
        }
        @endif

        @if(config('marketing.meta_pixel_id'))
        if (!window.fbqLoaded) {
            !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
            n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(
            window,document,'script','https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', '{{ config('marketing.meta_pixel_id') }}');
            fbq('track', 'PageView');
            window.fbqLoaded = true;
        }
        @endif
    }

    function hasMarketingConsent() {
        try {
            var consent = JSON.parse(getCookie('cookie_consent') || '{}');
            return consent.analytics === true || consent.marketing === true;
        } catch (e) {
            return false;
        }
    }

    function getCookie(name) {
        var match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
        return match ? decodeURIComponent(match[2]) : null;
    }

        document.addEventListener('DOMContentLoaded', function() {
        if (hasMarketingConsent()) {
            loadMarketingScripts();
        }
        document.addEventListener('livewire:init', function() {
            if (typeof Livewire !== 'undefined') {
                Livewire.on('analytics-purchase', function(data) {
                    var booking = data.booking || data[0]?.booking || data;
                    if (window.marrakechtoursAnalytics) {
                        window.marrakechtoursAnalytics.push('purchase', {
                            transaction_id: booking.reference,
                            value: booking.value,
                            currency: booking.currency,
                            items: [{ item_id: booking.tour_id, item_name: booking.tour_name }]
                        });
                    }
                    if (typeof fbq !== 'undefined') {
                        fbq('track', 'Purchase', { value: booking.value, currency: booking.currency });
                    }
                });
            }
        });
        window.addEventListener('cookie-consent-saved', function(e) {
            var consent = e.detail && e.detail.consent ? e.detail.consent : e.detail;
            if (consent && (consent.analytics || consent.marketing)) {
                loadMarketingScripts();
            }
        });
    });
</script>
@endif
