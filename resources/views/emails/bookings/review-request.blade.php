<x-mail::message>
@if($isReminder)
# {{ __('N\'oubliez pas de partager votre expérience !') }}

{{ __('Bonjour :name, nous espérons que vous gardez un excellent souvenir de votre excursion.', ['name' => $customerName]) }}
@else
# {{ __('Comment s\'est passée votre excursion ?') }}

{{ __('Bonjour :name, nous espérons que vous avez passé un moment inoubliable lors de votre :tour.', ['name' => $customerName, 'tour' => $tour->name]) }}
@endif

{{ __('Votre avis compte énormément pour nous ! Il aide les futurs voyageurs à découvrir nos excursions et nous permet de nous améliorer.') }}

<x-mail::button :url="$reviewUrl">
{{ __('Donner mon avis') }}
</x-mail::button>

## {{ __('En remerciement') }}

{{ __('Pour vous remercier de prendre le temps de partager votre expérience, nous vous offrons un code promo de -10% sur votre prochaine réservation :') }}

<x-mail::panel>
**{{ $promoCode }}**

{{ __('Valable 6 mois sur toutes nos excursions.') }}
</x-mail::panel>

{{ __('Merci d\'avoir choisi Marrakech Tours. Nous espérons vous revoir bientôt pour de nouvelles aventures !') }}

**{{ __('L\'équipe Marrakech Tours') }}**

---

<small>
{{ __('Cet email concerne votre réservation :reference du :date.', [
    'reference' => $booking->reference,
    'date' => $booking->tour_date->translatedFormat('d/m/Y'),
]) }}
</small>
</x-mail::message>
