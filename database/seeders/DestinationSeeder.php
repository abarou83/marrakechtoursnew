<?php

namespace Database\Seeders;

use App\Models\Destination;
use Illuminate\Database\Seeder;

class DestinationSeeder extends Seeder
{
    public function run(): void
    {
        $destinations = [
            [
                'lat' => 31.4979,
                'lng' => -8.7963,
                'region' => 'marrakech',
                'sort_order' => 1,
                'is_active' => true,
                'is_featured' => true,
                'translations' => [
                    'fr' => [
                        'name' => 'Désert d\'Agafay',
                        'slug' => 'desert-agafay',
                        'description' => 'Le désert d\'Agafay, à seulement 30 minutes de Marrakech, offre un paysage lunaire spectaculaire avec vue sur l\'Atlas.',
                        'intro_text' => 'Découvrez le désert de pierres d\'Agafay, une alternative proche et accessible au Sahara. Dîner-spectacle, balade en chameau, quad et bivouac sous les étoiles.',
                        'meta_title' => 'Excursions Désert d\'Agafay depuis Marrakech | {year}',
                        'meta_description' => 'Découvrez le désert d\'Agafay à 30min de Marrakech. Dîner-spectacle, balade en chameau, quad et bivouac. Réservez en direct !',
                    ],
                    'en' => [
                        'name' => 'Agafay Desert',
                        'slug' => 'agafay-desert',
                        'description' => 'Agafay Desert, just 30 minutes from Marrakech, offers a spectacular lunar landscape with views of the Atlas.',
                        'intro_text' => 'Discover the stone desert of Agafay, a close and accessible alternative to the Sahara.',
                        'meta_title' => 'Agafay Desert Tours from Marrakech | {year}',
                        'meta_description' => 'Discover Agafay Desert, 30min from Marrakech. Dinner shows, camel rides, quad biking and camping.',
                    ],
                    'es' => [
                        'name' => 'Desierto de Agafay',
                        'slug' => 'desierto-agafay',
                        'description' => 'El desierto de Agafay, a solo 30 minutos de Marrakech, ofrece un paisaje lunar espectacular.',
                        'meta_title' => 'Excursiones al Desierto de Agafay desde Marrakech',
                        'meta_description' => 'Descubre el desierto de Agafay a 30min de Marrakech. Cenas, paseos en camello y quad.',
                    ],
                    'ar' => [
                        'name' => 'صحراء أكافاي',
                        'slug' => 'صحراء-أكافاي',
                        'description' => 'صحراء أكافاي، على بعد 30 دقيقة فقط من مراكش، توفر منظرًا قمريًا رائعًا.',
                        'meta_title' => 'رحلات صحراء أكافاي من مراكش',
                        'meta_description' => 'اكتشف صحراء أكافاي على بعد 30 دقيقة من مراكش.',
                    ],
                ],
            ],
            [
                'lat' => 31.5085,
                'lng' => -7.6059,
                'region' => 'atlas',
                'sort_order' => 2,
                'is_active' => true,
                'is_featured' => true,
                'translations' => [
                    'fr' => [
                        'name' => 'Vallée de l\'Ourika',
                        'slug' => 'vallee-ourika',
                        'description' => 'La vallée de l\'Ourika, au cœur de l\'Atlas, offre cascades, villages berbères et paysages verdoyants.',
                        'intro_text' => 'Explorez la vallée de l\'Ourika, ses cascades rafraîchissantes et ses villages berbères authentiques à 1h de Marrakech.',
                        'meta_title' => 'Excursion Vallée de l\'Ourika depuis Marrakech | {year}',
                        'meta_description' => 'Journée dans la vallée de l\'Ourika : cascades, villages berbères, déjeuner traditionnel. À 1h de Marrakech.',
                    ],
                    'en' => [
                        'name' => 'Ourika Valley',
                        'slug' => 'ourika-valley',
                        'description' => 'Ourika Valley, in the heart of the Atlas, offers waterfalls, Berber villages and green landscapes.',
                        'meta_title' => 'Ourika Valley Day Trip from Marrakech | {year}',
                        'meta_description' => 'Day trip to Ourika Valley: waterfalls, Berber villages, traditional lunch. 1h from Marrakech.',
                    ],
                    'es' => [
                        'name' => 'Valle de Ourika',
                        'slug' => 'valle-ourika',
                        'description' => 'El valle de Ourika, en el corazón del Atlas, ofrece cascadas y pueblos bereberes.',
                        'meta_title' => 'Excursión al Valle de Ourika desde Marrakech',
                        'meta_description' => 'Día en el valle de Ourika: cascadas, pueblos bereberes, almuerzo tradicional.',
                    ],
                    'ar' => [
                        'name' => 'وادي أوريكا',
                        'slug' => 'وادي-أوريكا',
                        'description' => 'وادي أوريكا في قلب الأطلس يوفر شلالات وقرى أمازيغية ومناظر خضراء.',
                        'meta_title' => 'رحلة وادي أوريكا من مراكش',
                        'meta_description' => 'يوم في وادي أوريكا: شلالات، قرى أمازيغية، غداء تقليدي.',
                    ],
                ],
            ],
            [
                'lat' => 31.5125,
                'lng' => -9.7749,
                'region' => 'coast',
                'sort_order' => 3,
                'is_active' => true,
                'is_featured' => true,
                'translations' => [
                    'fr' => [
                        'name' => 'Essaouira',
                        'slug' => 'essaouira',
                        'description' => 'Essaouira, la cité des alizés, est une ville côtière UNESCO avec médina historique et plages atlantiques.',
                        'intro_text' => 'Escapade à Essaouira, l\'ancienne Mogador : médina UNESCO, port de pêche, artisanat, fruits de mer et ambiance bohème.',
                        'meta_title' => 'Excursion Essaouira depuis Marrakech | Journée complète {year}',
                        'meta_description' => 'Journée à Essaouira depuis Marrakech. Médina UNESCO, port, plage et fruits de mer. Transport inclus.',
                    ],
                    'en' => [
                        'name' => 'Essaouira',
                        'slug' => 'essaouira',
                        'description' => 'Essaouira, the wind city, is a UNESCO coastal town with historic medina and Atlantic beaches.',
                        'meta_title' => 'Essaouira Day Trip from Marrakech | {year}',
                        'meta_description' => 'Day trip to Essaouira from Marrakech. UNESCO medina, port, beach and seafood. Transport included.',
                    ],
                    'es' => [
                        'name' => 'Essaouira',
                        'slug' => 'essaouira',
                        'description' => 'Essaouira, la ciudad del viento, es una ciudad costera UNESCO con medina histórica.',
                        'meta_title' => 'Excursión a Essaouira desde Marrakech | {year}',
                        'meta_description' => 'Día en Essaouira desde Marrakech. Medina UNESCO, puerto, playa y mariscos.',
                    ],
                    'ar' => [
                        'name' => 'الصويرة',
                        'slug' => 'الصويرة',
                        'description' => 'الصويرة، مدينة الرياح، هي مدينة ساحلية تراثية مع مدينة قديمة وشواطئ أطلسية.',
                        'meta_title' => 'رحلة الصويرة من مراكش',
                        'meta_description' => 'يوم في الصويرة من مراكش. المدينة القديمة، الميناء، الشاطئ والمأكولات البحرية.',
                    ],
                ],
            ],
            [
                'lat' => 32.0853,
                'lng' => -6.5455,
                'region' => 'atlas',
                'sort_order' => 4,
                'is_active' => true,
                'is_featured' => true,
                'translations' => [
                    'fr' => [
                        'name' => 'Cascades d\'Ouzoud',
                        'slug' => 'cascades-ouzoud',
                        'description' => 'Les cascades d\'Ouzoud, hautes de 110 mètres, sont les plus belles chutes d\'eau du Maroc.',
                        'intro_text' => 'Visitez les majestueuses cascades d\'Ouzoud, paradis naturel avec singes magots, baignade et déjeuner berbère.',
                        'meta_title' => 'Excursion Cascades d\'Ouzoud depuis Marrakech | {year}',
                        'meta_description' => 'Journée aux cascades d\'Ouzoud depuis Marrakech. 110m de haut, singes, baignade. Transport + déjeuner inclus.',
                    ],
                    'en' => [
                        'name' => 'Ouzoud Waterfalls',
                        'slug' => 'ouzoud-waterfalls',
                        'description' => 'Ouzoud Falls, 110 meters high, are Morocco\'s most beautiful waterfalls.',
                        'meta_title' => 'Ouzoud Falls Day Trip from Marrakech | {year}',
                        'meta_description' => 'Day trip to Ouzoud Falls from Marrakech. 110m high, monkeys, swimming. Transport + lunch included.',
                    ],
                    'es' => [
                        'name' => 'Cascadas de Ouzoud',
                        'slug' => 'cascadas-ouzoud',
                        'description' => 'Las cascadas de Ouzoud, de 110 metros de altura, son las más bellas de Marruecos.',
                        'meta_title' => 'Excursión Cascadas de Ouzoud desde Marrakech | {year}',
                        'meta_description' => 'Día en las cascadas de Ouzoud desde Marrakech. 110m de altura, monos, baño.',
                    ],
                    'ar' => [
                        'name' => 'شلالات أوزود',
                        'slug' => 'شلالات-أوزود',
                        'description' => 'شلالات أوزود، بارتفاع 110 أمتار، هي أجمل شلالات المغرب.',
                        'meta_title' => 'رحلة شلالات أوزود من مراكش',
                        'meta_description' => 'يوم في شلالات أوزود من مراكش. ارتفاع 110 متر، قردة، سباحة.',
                    ],
                ],
            ],
            [
                'lat' => 31.1428,
                'lng' => -4.0083,
                'region' => 'sahara',
                'sort_order' => 5,
                'is_active' => true,
                'is_featured' => true,
                'translations' => [
                    'fr' => [
                        'name' => 'Merzouga & Erg Chebbi',
                        'slug' => 'merzouga-erg-chebbi',
                        'description' => 'Merzouga et l\'Erg Chebbi offrent les plus hautes dunes du Sahara marocain, jusqu\'à 150 mètres.',
                        'intro_text' => 'Vivez l\'aventure saharienne ultime : dunes de l\'Erg Chebbi, nuit en bivouac, lever de soleil et balade en dromadaire.',
                        'meta_title' => 'Circuit Merzouga & Désert du Sahara depuis Marrakech | {year}',
                        'meta_description' => 'Circuit 2-3 jours vers Merzouga et l\'Erg Chebbi. Dunes du Sahara, bivouac, dromadaires. Depuis Marrakech.',
                    ],
                    'en' => [
                        'name' => 'Merzouga & Erg Chebbi',
                        'slug' => 'merzouga-erg-chebbi',
                        'description' => 'Merzouga and Erg Chebbi offer the highest dunes of the Moroccan Sahara, up to 150 meters.',
                        'meta_title' => 'Merzouga & Sahara Desert Tour from Marrakech | {year}',
                        'meta_description' => '2-3 day tour to Merzouga and Erg Chebbi. Sahara dunes, desert camp, camels. From Marrakech.',
                    ],
                    'es' => [
                        'name' => 'Merzouga y Erg Chebbi',
                        'slug' => 'merzouga-erg-chebbi',
                        'description' => 'Merzouga y el Erg Chebbi ofrecen las dunas más altas del Sáhara marroquí.',
                        'meta_title' => 'Circuito Merzouga y Desierto del Sahara desde Marrakech',
                        'meta_description' => 'Circuito 2-3 días a Merzouga y Erg Chebbi. Dunas del Sáhara, campamento, dromedarios.',
                    ],
                    'ar' => [
                        'name' => 'مرزوقة وعرق الشبي',
                        'slug' => 'مرزوقة-عرق-الشبي',
                        'description' => 'مرزوقة وعرق الشبي توفر أعلى كثبان الصحراء المغربية، حتى 150 متراً.',
                        'meta_title' => 'جولة مرزوقة والصحراء من مراكش',
                        'meta_description' => 'جولة 2-3 أيام إلى مرزوقة وعرق الشبي. كثبان الصحراء، مخيم، جمال.',
                    ],
                ],
            ],
        ];

        foreach ($destinations as $data) {
            $translations = $data['translations'];
            unset($data['translations']);

            $destination = Destination::create($data);

            foreach ($translations as $locale => $trans) {
                foreach ($trans as $key => $value) {
                    $destination->setTranslation($key, $locale, $value);
                }
            }

            $destination->save();
        }
    }
}
