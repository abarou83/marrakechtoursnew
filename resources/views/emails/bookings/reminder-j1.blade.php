<x-mail::message>
# {{ __('C\'est demain, :name !', ['name' => $customerName]) }}

{{ __('Nous avons hâte de vous accueillir pour votre excursion :tour.', ['tour' => $tour->name]) }}

<x-mail::panel>
## {{ __('Informations importantes') }}

**{{ __('Date') }}:** {{ $tourDate->translatedFormat('l d F Y') }}

**{{ __('Heure de départ') }}:** {{ $tour->departure_time ?? '09:00' }}

**{{ __('Point de rencontre') }}:** {{ $tour->departure_point ?? 'Votre hôtel' }}

**{{ __('Durée') }}:** {{ $tour->formatted_duration }}
</x-mail::panel>

## {{ __('Checklist avant le départ') }}

- ✅ {{ __('Votre voucher (ci-dessous) ou cette email') }}
- ✅ {{ __('Pièce d\'identité') }}
- ✅ {{ __('Vêtements confortables') }}
- ✅ {{ __('Crème solaire et chapeau') }}
- ✅ {{ __('Appareil photo') }}
- ✅ {{ __('Petite bouteille d\'eau') }}

<x-mail::panel>
## {{ __('Votre voucher') }}

**Code:** `{{ $voucherCode }}`

{{ __('Présentez ce code ou cet email à votre guide.') }}
</x-mail::panel>

<x-mail::button :url="$whatsappLink" color="success">
{{ __('WhatsApp : Contacter votre guide') }}
</x-mail::button>

{{ __('En cas de retard ou d\'imprévu, contactez-nous immédiatement sur WhatsApp.') }}

{{ __('Excellente excursion !') }}

**{{ __('L\'équipe Marrakech Tours') }}**
</x-mail::message>
