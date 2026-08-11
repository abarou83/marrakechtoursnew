@props(['tour'])

@php
    $imageUrl = null;
    if ($tour->primaryImage) {
        $imageUrl = Storage::url($tour->primaryImage->path);
    } elseif ($tour->images->first()) {
        $imageUrl = Storage::url($tour->images->first()->path);
    }
@endphp

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "TouristAttraction",
  "name": "{{ $tour->title }}",
  "description": "{{ strip_tags($tour->description) }}",
  "url": "{{ route('tours.show', $tour->url_key) }}",
  @if($imageUrl)
  "image": "{{ $imageUrl }}",
  @endif
  "address": {
    "@type": "Place",
    "name": "{{ $tour->location }}"
  },
  "offers": {
    "@type": "Offer",
    "price": "{{ $tour->price }}",
    "priceCurrency": "EUR",
    "availability": "https://schema.org/InStock",
    "url": "{{ route('tours.show', $tour->url_key) }}"
  },
  "provider": {
    "@type": "Organization",
    "name": "{{ config('app.name', 'Tourify') }}",
    "url": "{{ url('/') }}"
  }
}
</script>
