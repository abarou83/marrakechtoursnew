<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Guide;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class GuideSeeder extends Seeder
{
    protected array $guides = [
        [
            'category' => 'marrakech',
            'position' => 1,
            'fr' => ['title' => 'Que faire à Marrakech en 3 jours', 'slug' => 'que-faire-marrakech-3-jours'],
            'en' => ['title' => 'What to do in Marrakech in 3 days', 'slug' => 'what-to-do-marrakech-3-days'],
            'es' => ['title' => 'Qué hacer en Marrakech en 3 días', 'slug' => 'que-hacer-marrakech-3-dias'],
        ],
        [
            'category' => 'desert',
            'position' => 2,
            'fr' => ['title' => 'Préparer un séjour dans le désert du Sahara', 'slug' => 'preparer-sejour-desert-sahara'],
            'en' => ['title' => 'How to prepare for a Sahara desert trip', 'slug' => 'prepare-sahara-desert-trip'],
            'es' => ['title' => 'Cómo preparar un viaje al desierto del Sahara', 'slug' => 'preparar-viaje-desierto-sahara'],
        ],
        [
            'category' => 'culture',
            'position' => 3,
            'fr' => ['title' => 'Étiquette et coutumes au Maroc', 'slug' => 'etiquette-coutumes-maroc'],
            'en' => ['title' => 'Etiquette and customs in Morocco', 'slug' => 'etiquette-customs-morocco'],
            'es' => ['title' => 'Etiqueta y costumbres en Marruecos', 'slug' => 'etiqueta-costumbres-marruecos'],
        ],
        [
            'category' => 'food',
            'position' => 4,
            'fr' => ['title' => 'Guide gastronomique de Marrakech', 'slug' => 'guide-gastronomique-marrakech'],
            'en' => ['title' => 'Marrakech food guide', 'slug' => 'marrakech-food-guide'],
            'es' => ['title' => 'Guía gastronómica de Marrakech', 'slug' => 'guia-gastronomica-marrakech'],
        ],
        [
            'category' => 'transport',
            'position' => 5,
            'fr' => ['title' => 'Se déplacer à Marrakech : taxi, bus, guide', 'slug' => 'se-deplacer-marrakech'],
            'en' => ['title' => 'Getting around Marrakech', 'slug' => 'getting-around-marrakech'],
            'es' => ['title' => 'Moverse por Marrakech', 'slug' => 'moverse-marrakech'],
        ],
        [
            'category' => 'tips',
            'position' => 6,
            'fr' => ['title' => 'Meilleure période pour visiter le Maroc', 'slug' => 'meilleure-periode-visiter-maroc'],
            'en' => ['title' => 'Best time to visit Morocco', 'slug' => 'best-time-visit-morocco'],
            'es' => ['title' => 'Mejor época para visitar Marruecos', 'slug' => 'mejor-epoca-visitar-marruecos'],
        ],
        [
            'category' => 'marrakech',
            'position' => 7,
            'fr' => ['title' => 'Les souks de Marrakech : guide du visiteur', 'slug' => 'souks-marrakech-guide'],
            'en' => ['title' => 'Marrakech souks: visitor guide', 'slug' => 'marrakech-souks-guide'],
            'es' => ['title' => 'Los zocos de Marrakech: guía', 'slug' => 'zocos-marrakech-guia'],
        ],
        [
            'category' => 'desert',
            'position' => 8,
            'fr' => ['title' => 'Merzouga vs Zagora : lequel choisir ?', 'slug' => 'merzouga-vs-zagora'],
            'en' => ['title' => 'Merzouga vs Zagora: which to choose?', 'slug' => 'merzouga-vs-zagora-en'],
            'es' => ['title' => 'Merzouga vs Zagora: ¿cuál elegir?', 'slug' => 'merzouga-vs-zagora-es'],
        ],
        [
            'category' => 'culture',
            'position' => 9,
            'fr' => ['title' => 'Palais et jardins incontournables', 'slug' => 'palais-jardins-maroc'],
            'en' => ['title' => 'Must-see palaces and gardens', 'slug' => 'palaces-gardens-morocco'],
            'es' => ['title' => 'Palacios y jardines imprescindibles', 'slug' => 'palacios-jardines-marruecos'],
        ],
        [
            'category' => 'tips',
            'position' => 10,
            'fr' => ['title' => 'Budget voyage au Maroc : combien prévoir ?', 'slug' => 'budget-voyage-maroc'],
            'en' => ['title' => 'Morocco travel budget guide', 'slug' => 'morocco-travel-budget'],
            'es' => ['title' => 'Presupuesto de viaje a Marruecos', 'slug' => 'presupuesto-viaje-marruecos'],
        ],
        [
            'category' => 'transport',
            'position' => 11,
            'fr' => ['title' => 'Excursions depuis Marrakech : Atlas, Essaouira, Ouzoud', 'slug' => 'excursions-depuis-marrakech'],
            'en' => ['title' => 'Day trips from Marrakech', 'slug' => 'day-trips-marrakech'],
            'es' => ['title' => 'Excursiones desde Marrakech', 'slug' => 'excursiones-marrakech'],
        ],
        [
            'category' => 'general',
            'position' => 12,
            'fr' => ['title' => 'Checklist avant de réserver votre tour', 'slug' => 'checklist-reserver-tour-maroc'],
            'en' => ['title' => 'Checklist before booking your tour', 'slug' => 'checklist-booking-tour-morocco'],
            'es' => ['title' => 'Checklist antes de reservar tu tour', 'slug' => 'checklist-reservar-tour-marruecos'],
        ],
    ];

    public function run(): void
    {
        if (Guide::exists()) {
            return;
        }

        foreach ($this->guides as $data) {
            $guide = Guide::create([
                'category' => $data['category'],
                'is_published' => true,
                'published_at' => now()->subDays(rand(1, 30)),
                'reading_time' => rand(4, 12),
                'position' => $data['position'],
            ]);

            foreach (['fr', 'en', 'es'] as $locale) {
                $meta = $data[$locale];
                $guide->translations()->create([
                    'locale' => $locale,
                    'slug' => $meta['slug'],
                    'title' => $meta['title'],
                    'excerpt' => $this->excerpt($locale),
                    'content' => $this->content($meta['title'], $locale),
                    'meta_title' => $meta['title'] . ' | MarrakechTours',
                    'meta_description' => $this->excerpt($locale),
                ]);
            }
        }
    }

    protected function excerpt(string $locale): string
    {
        return match ($locale) {
            'en' => 'Practical tips and local insights to plan your Morocco adventure with confidence.',
            'es' => 'Consejos prácticos e información local para planificar tu aventura en Marruecos.',
            default => 'Conseils pratiques et infos locales pour préparer votre voyage au Maroc sereinement.',
        };
    }

    protected function content(string $title, string $locale): string
    {
        $intro = match ($locale) {
            'en' => "<p>This guide covers everything you need to know about <strong>{$title}</strong>. Written by MarrakechTours local experts.</p>",
            'es' => "<p>Esta guía cubre todo lo que necesitas saber sobre <strong>{$title}</strong>. Escrita por expertos locales de MarrakechTours.</p>",
            default => "<p>Ce guide couvre tout ce qu'il faut savoir sur <strong>{$title}</strong>. Rédigé par les experts locaux MarrakechTours.</p>",
        };

        $sections = match ($locale) {
            'en' => '<h2>Key tips</h2><ul><li>Book in advance during peak season</li><li>Respect local customs</li><li>Carry cash for small purchases</li></ul><h2>Recommended tours</h2><p>Browse our curated experiences to complete your itinerary.</p>',
            'es' => '<h2>Consejos clave</h2><ul><li>Reserva con antelación en temporada alta</li><li>Respeta las costumbres locales</li><li>Lleva efectivo para compras pequeñas</li></ul><h2>Tours recomendados</h2><p>Explora nuestras experiencias seleccionadas.</p>',
            default => '<h2>Conseils clés</h2><ul><li>Réservez à l\'avance en haute saison</li><li>Respectez les coutumes locales</li><li>Prévoyez du cash pour les petits achats</li></ul><h2>Tours recommandés</h2><p>Parcourez nos expériences sélectionnées pour compléter votre itinéraire.</p>',
        };

        return $intro . $sections;
    }
}
