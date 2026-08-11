<x-mail::message>
# {{ __('Votre réservation vous attend !') }}

{{ __('Bonjour :name,', ['name' => $cart->customer_name ?? '']) }}

{{ __('Vous avez commencé une réservation pour **:tour** mais ne l\'avez pas finalisée.', [
    'tour' => $tour?->translate()?->title ?? $tour?->title ?? 'votre excursion',
]) }}

@if($cart->travel_date)
**{{ __('Date prévue') }}:** {{ $cart->travel_date->format('d/m/Y') }}
@endif

@if($cart->total_amount)
**{{ __('Montant') }}:** {{ number_format($cart->total_amount, 2) }} {{ $cart->currency }}
@endif

<x-mail::button :url="$bookingUrl">
{{ __('Finaliser ma réservation') }}
</x-mail::button>

{{ __('Offre spéciale : :percent% de réduction si vous finalisez dans les 24h.', ['percent' => $promoPercent]) }}

{{ __('À bientôt,') }}<br>
{{ config('app.name') }}
</x-mail::message>
