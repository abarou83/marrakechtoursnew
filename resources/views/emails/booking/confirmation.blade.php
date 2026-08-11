<x-mail::message>
# {{ __('Réservation confirmée !') }}

{{ __('Bonjour :name,', ['name' => $booking->customer_name]) }}

{{ __('Votre réservation a été confirmée avec succès. Nous avons hâte de vous accueillir !') }}

<x-mail::panel>
**{{ __('Référence de réservation') }}**<br>
<span style="font-size: 24px; font-weight: bold; color: #C1440E;">{{ $booking->reference }}</span>
</x-mail::panel>

## {{ __('Détails de votre excursion') }}

<x-mail::table>
| {{ __('Information') }} | {{ __('Détails') }} |
|:------------------------|:---------------------|
| **{{ __('Tour') }}** | {{ $tour->translate()?->title ?? $tour->title }} |
| **{{ __('Date') }}** | {{ \Carbon\Carbon::parse($booking->travel_date ?? $booking->booking_date)->translatedFormat('l d F Y') }} |
| **{{ __('Participants') }}** | {{ $booking->adults ?? 1 }} {{ __('adulte(s)') }}@if(($booking->children ?? 0) > 0), {{ $booking->children }} {{ __('enfant(s)') }}@endif @if(($booking->infants ?? 0) > 0), {{ $booking->infants }} {{ __('bébé(s)') }}@endif |
| **{{ __('Total payé') }}** | **{{ number_format($booking->total_ttc ?? $booking->total_price, 2) }} €** |
</x-mail::table>

<x-mail::button :url="$url" color="primary">
{{ __('Voir ma réservation') }}
</x-mail::button>

## {{ __('Informations importantes') }}

- {{ __('Présentez ce voucher (imprimé ou sur mobile) le jour du tour') }}
- {{ __('Soyez au point de rendez-vous 15 minutes avant l\'heure de départ') }}
- {{ __('Annulation gratuite jusqu\'à 24h avant le départ') }}

<x-mail::subcopy>
{{ __('Votre voucher est également joint à cet email en pièce jointe PDF.') }}
</x-mail::subcopy>

{{ __('À très bientôt !') }}<br>
**{{ __('L\'équipe MarrakechTours') }}**

<x-mail::subcopy>
{{ __('Des questions ? Contactez-nous à') }} [contact@marrakechtours.net](mailto:contact@marrakechtours.net) {{ __('ou via WhatsApp') }} +212 6 XX XX XX XX
</x-mail::subcopy>
</x-mail::message>
