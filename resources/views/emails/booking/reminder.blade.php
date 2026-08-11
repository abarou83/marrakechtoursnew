<x-mail::message>
@if($reminderType === '24h')
# {{ __('Votre aventure commence demain !') }} 🌴
@else
# {{ __('C\'est bientôt l\'heure !') }} ⏰
@endif

{{ __('Bonjour :name,', ['name' => $booking->customer_name]) }}

@if($reminderType === '24h')
{{ __('Nous avons hâte de vous accueillir demain pour votre excursion !') }}
@else
{{ __('Votre excursion commence dans quelques heures. Êtes-vous prêt ?') }}
@endif

<x-mail::panel>
**{{ $tour->translate()?->title ?? $tour->title }}**<br>
📅 {{ \Carbon\Carbon::parse($booking->travel_date ?? $booking->booking_date)->translatedFormat('l d F Y') }}<br>
🎫 {{ __('Référence') }} : **{{ $booking->reference }}**
</x-mail::panel>

## {{ __('Checklist avant le départ') }}

✅ {{ __('Votre voucher (imprimé ou sur mobile)') }}<br>
✅ {{ __('Une pièce d\'identité valide') }}<br>
✅ {{ __('De l\'eau et des encas') }}<br>
✅ {{ __('Crème solaire et lunettes de soleil') }}<br>
✅ {{ __('Appareil photo pour capturer les souvenirs !') }}

@if($tour->departure_point)
## {{ __('Point de rendez-vous') }}

📍 **{{ $tour->departure_point }}**

{{ __('Soyez présent 15 minutes avant l\'heure de départ.') }}
@endif

<x-mail::button :url="$voucherUrl" color="primary">
{{ __('Télécharger mon voucher') }}
</x-mail::button>

{{ __('Nous vous souhaitons une excellente excursion !') }}

**{{ __('L\'équipe MarrakechTours') }}**

<x-mail::subcopy>
{{ __('Besoin de modifier ou annuler votre réservation ? Contactez-nous à') }} [contact@marrakechtours.net](mailto:contact@marrakechtours.net) {{ __('ou via WhatsApp') }} +212 6 XX XX XX XX
</x-mail::subcopy>
</x-mail::message>
