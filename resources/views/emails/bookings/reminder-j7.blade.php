<x-mail::message>
# {{ __('Bonjour :name,', ['name' => $customerName]) }}

{{ __('Votre excursion approche ! Dans une semaine, vous partirez découvrir :tour.', ['tour' => $tour->name]) }}

<x-mail::panel>
## {{ __('Récapitulatif de votre réservation') }}

**{{ __('Tour') }}:** {{ $tour->name }}

**{{ __('Date') }}:** {{ $tourDate->translatedFormat('l d F Y') }}

**{{ __('Heure de départ') }}:** {{ $tour->departure_time ?? '09:00' }}

**{{ __('Point de rencontre') }}:** {{ $tour->departure_point ?? 'À confirmer' }}

**{{ __('Référence') }}:** {{ $booking->reference }}
</x-mail::panel>

## {{ __('Informations pratiques') }}

{{ __('Voici quelques conseils pour bien préparer votre excursion :') }}

- {{ __('Vérifiez la météo prévue et préparez des vêtements adaptés') }}
- {{ __('N\'oubliez pas votre appareil photo') }}
- {{ __('Apportez de l\'eau et de la crème solaire') }}
@if($tour->what_to_bring)
- {{ $tour->what_to_bring }}
@endif

<x-mail::button :url="$whatsappLink" color="success">
{{ __('Contacter notre équipe sur WhatsApp') }}
</x-mail::button>

{{ __('Une question ? Notre équipe locale est à votre disposition pour vous accompagner.') }}

{{ __('À très bientôt à Marrakech !') }}

**{{ __('L\'équipe Marrakech Tours') }}**

---

<small>{{ __('Voucher :code', ['code' => $voucherCode]) }}</small>
</x-mail::message>
