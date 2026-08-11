<x-mail::message>
# 🎉 Nouvelle réservation !

Une nouvelle réservation vient d'être effectuée sur le site.

<x-mail::panel>
**Référence** : {{ $booking->reference }}<br>
**Montant** : **{{ number_format($booking->total_ttc ?? $booking->total_price, 2) }} €**
</x-mail::panel>

## Détails du tour

| Information | Valeur |
|:------------|:-------|
| **Tour** | {{ $tour->translate()?->title ?? $tour->title }} |
| **Date** | {{ \Carbon\Carbon::parse($booking->travel_date ?? $booking->booking_date)->format('d/m/Y') }} |
| **Mode** | {{ $booking->pricing_mode === 'private' ? 'Privé' : 'Groupe' }} |
| **Participants** | {{ $booking->adults ?? 1 }} adulte(s)@if(($booking->children ?? 0) > 0), {{ $booking->children }} enfant(s)@endif @if(($booking->infants ?? 0) > 0), {{ $booking->infants }} bébé(s)@endif |

## Client

| Information | Valeur |
|:------------|:-------|
| **Nom** | {{ $booking->customer_name }} |
| **Email** | {{ $booking->customer_email }} |
| **Téléphone** | {{ $booking->customer_phone ?? 'Non renseigné' }} |
| **Pays** | {{ $booking->country_code ?? 'N/A' }} |

@if($booking->special_requests)
## Demandes spéciales
{{ $booking->special_requests }}
@endif

<x-mail::button :url="$adminUrl" color="primary">
Voir dans l'admin
</x-mail::button>

---

*Cet email a été envoyé automatiquement par le système MarrakechTours.*
</x-mail::message>
