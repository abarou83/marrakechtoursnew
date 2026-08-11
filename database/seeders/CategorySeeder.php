<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'icon' => 'sun',
                'color' => '#D4A843',
                'sort_order' => 1,
                'is_active' => true,
                'is_featured' => true,
                'translations' => [
                    'fr' => [
                        'name' => 'Désert & Sahara',
                        'slug' => 'desert-sahara',
                        'description' => 'Explorez les dunes dorées du Sahara et vivez une expérience inoubliable sous les étoiles.',
                        'meta_title' => 'Excursions Désert Sahara depuis Marrakech | Tours Merzouga & Agafay',
                        'meta_description' => 'Découvrez le désert marocain avec nos excursions vers Merzouga, Zagora et le désert d\'Agafay. Nuit en bivouac, dîner spectacle et balade en chameau.',
                    ],
                    'en' => [
                        'name' => 'Desert & Sahara',
                        'slug' => 'desert-sahara',
                        'description' => 'Explore the golden dunes of the Sahara and live an unforgettable experience under the stars.',
                        'meta_title' => 'Sahara Desert Tours from Marrakech | Merzouga & Agafay Trips',
                        'meta_description' => 'Discover the Moroccan desert with our tours to Merzouga, Zagora and Agafay desert. Overnight camps, dinner shows and camel rides.',
                    ],
                    'es' => [
                        'name' => 'Desierto y Sahara',
                        'slug' => 'desierto-sahara',
                        'description' => 'Explora las dunas doradas del Sahara y vive una experiencia inolvidable bajo las estrellas.',
                        'meta_title' => 'Excursiones al Desierto del Sahara desde Marrakech',
                        'meta_description' => 'Descubre el desierto marroquí con nuestras excursiones a Merzouga, Zagora y el desierto de Agafay.',
                    ],
                    'ar' => [
                        'name' => 'الصحراء والسهارا',
                        'slug' => 'الصحراء',
                        'description' => 'اكتشف الكثبان الذهبية للصحراء وعش تجربة لا تُنسى تحت النجوم.',
                        'meta_title' => 'رحلات الصحراء من مراكش',
                        'meta_description' => 'اكتشف الصحراء المغربية مع رحلاتنا إلى مرزوقة وزاكورة وصحراء أكافاي.',
                    ],
                ],
            ],
            [
                'icon' => 'water',
                'color' => '#4355BE',
                'sort_order' => 2,
                'is_active' => true,
                'is_featured' => true,
                'translations' => [
                    'fr' => [
                        'name' => 'Mer & Côte',
                        'slug' => 'mer-cote',
                        'description' => 'Découvrez les plages atlantiques d\'Essaouira et les côtes sauvages du Maroc.',
                        'meta_title' => 'Excursion Essaouira depuis Marrakech | Journée à la mer',
                        'meta_description' => 'Escapade à Essaouira, la cité des alizés. Médina UNESCO, port de pêche, plage et fruits de mer frais.',
                    ],
                    'en' => [
                        'name' => 'Sea & Coast',
                        'slug' => 'sea-coast',
                        'description' => 'Discover the Atlantic beaches of Essaouira and the wild coasts of Morocco.',
                        'meta_title' => 'Essaouira Day Trip from Marrakech | Beach Excursion',
                        'meta_description' => 'Day trip to Essaouira, the wind city. UNESCO medina, fishing port, beach and fresh seafood.',
                    ],
                    'es' => [
                        'name' => 'Mar y Costa',
                        'slug' => 'mar-costa',
                        'description' => 'Descubre las playas atlánticas de Essaouira y las costas salvajes de Marruecos.',
                        'meta_title' => 'Excursión a Essaouira desde Marrakech',
                        'meta_description' => 'Escapada a Essaouira, la ciudad del viento. Medina UNESCO, puerto pesquero y mariscos frescos.',
                    ],
                    'ar' => [
                        'name' => 'البحر والساحل',
                        'slug' => 'البحر-والساحل',
                        'description' => 'اكتشف شواطئ الصويرة الأطلسية والسواحل البرية للمغرب.',
                        'meta_title' => 'رحلة إلى الصويرة من مراكش',
                        'meta_description' => 'رحلة يوم كامل إلى الصويرة، مدينة الرياح. المدينة القديمة، ميناء الصيد والمأكولات البحرية.',
                    ],
                ],
            ],
            [
                'icon' => 'mountain',
                'color' => '#2D5016',
                'sort_order' => 3,
                'is_active' => true,
                'is_featured' => true,
                'translations' => [
                    'fr' => [
                        'name' => 'Montagnes & Vallées',
                        'slug' => 'montagnes-vallees',
                        'description' => 'Randonnées dans l\'Atlas, cascades d\'Ouzoud et vallées berbères authentiques.',
                        'meta_title' => 'Excursions Atlas & Cascades Ouzoud depuis Marrakech',
                        'meta_description' => 'Découvrez les montagnes de l\'Atlas, la vallée de l\'Ourika, les cascades d\'Ouzoud et les villages berbères traditionnels.',
                    ],
                    'en' => [
                        'name' => 'Mountains & Valleys',
                        'slug' => 'mountains-valleys',
                        'description' => 'Hikes in the Atlas, Ouzoud waterfalls and authentic Berber valleys.',
                        'meta_title' => 'Atlas Mountains & Ouzoud Falls Tours from Marrakech',
                        'meta_description' => 'Discover the Atlas mountains, Ourika valley, Ouzoud waterfalls and traditional Berber villages.',
                    ],
                    'es' => [
                        'name' => 'Montañas y Valles',
                        'slug' => 'montanas-valles',
                        'description' => 'Senderismo en el Atlas, cascadas de Ouzoud y valles bereberes auténticos.',
                        'meta_title' => 'Excursiones al Atlas y Cascadas de Ouzoud desde Marrakech',
                        'meta_description' => 'Descubre las montañas del Atlas, el valle de Ourika, las cascadas de Ouzoud y los pueblos bereberes tradicionales.',
                    ],
                    'ar' => [
                        'name' => 'الجبال والوديان',
                        'slug' => 'الجبال-والوديان',
                        'description' => 'رحلات المشي في الأطلس، شلالات أوزود والوديان الأمازيغية الأصيلة.',
                        'meta_title' => 'رحلات جبال الأطلس وشلالات أوزود من مراكش',
                        'meta_description' => 'اكتشف جبال الأطلس، وادي أوريكا، شلالات أوزود والقرى الأمازيغية التقليدية.',
                    ],
                ],
            ],
            [
                'icon' => 'building',
                'color' => '#C1440E',
                'sort_order' => 4,
                'is_active' => true,
                'is_featured' => false,
                'translations' => [
                    'fr' => [
                        'name' => 'Villes Impériales',
                        'slug' => 'villes-imperiales',
                        'description' => 'Visitez Fès, Meknès, Rabat et Casablanca, les grandes cités historiques du Maroc.',
                        'meta_title' => 'Circuit Villes Impériales Maroc depuis Marrakech',
                        'meta_description' => 'Excursions vers Fès, Meknès, Rabat et Casablanca. Découvrez le patrimoine historique et culturel du Maroc.',
                    ],
                    'en' => [
                        'name' => 'Imperial Cities',
                        'slug' => 'imperial-cities',
                        'description' => 'Visit Fes, Meknes, Rabat and Casablanca, the great historic cities of Morocco.',
                        'meta_title' => 'Imperial Cities Tour from Marrakech',
                        'meta_description' => 'Day trips to Fes, Meknes, Rabat and Casablanca. Discover Morocco\'s historical and cultural heritage.',
                    ],
                    'es' => [
                        'name' => 'Ciudades Imperiales',
                        'slug' => 'ciudades-imperiales',
                        'description' => 'Visita Fez, Meknes, Rabat y Casablanca, las grandes ciudades históricas de Marruecos.',
                        'meta_title' => 'Circuito Ciudades Imperiales desde Marrakech',
                        'meta_description' => 'Excursiones a Fez, Meknes, Rabat y Casablanca. Descubre el patrimonio histórico y cultural de Marruecos.',
                    ],
                    'ar' => [
                        'name' => 'المدن الإمبراطورية',
                        'slug' => 'المدن-الإمبراطورية',
                        'description' => 'قم بزيارة فاس ومكناس والرباط والدار البيضاء، المدن التاريخية الكبرى في المغرب.',
                        'meta_title' => 'جولة المدن الإمبراطورية من مراكش',
                        'meta_description' => 'رحلات يومية إلى فاس ومكناس والرباط والدار البيضاء. اكتشف التراث التاريخي والثقافي للمغرب.',
                    ],
                ],
            ],
            [
                'icon' => 'sparkles',
                'color' => '#9333EA',
                'sort_order' => 5,
                'is_active' => true,
                'is_featured' => false,
                'translations' => [
                    'fr' => [
                        'name' => 'Expériences Uniques',
                        'slug' => 'experiences-uniques',
                        'description' => 'Quad, montgolfière, cours de cuisine, spa traditionnel et autres activités exclusives.',
                        'meta_title' => 'Activités & Expériences Uniques à Marrakech',
                        'meta_description' => 'Quad dans le désert, vol en montgolfière, cours de cuisine marocaine, hammam et spa traditionnel à Marrakech.',
                    ],
                    'en' => [
                        'name' => 'Unique Experiences',
                        'slug' => 'unique-experiences',
                        'description' => 'Quad biking, hot air balloon, cooking classes, traditional spa and other exclusive activities.',
                        'meta_title' => 'Unique Activities & Experiences in Marrakech',
                        'meta_description' => 'Desert quad biking, hot air balloon ride, Moroccan cooking class, hammam and traditional spa in Marrakech.',
                    ],
                    'es' => [
                        'name' => 'Experiencias Únicas',
                        'slug' => 'experiencias-unicas',
                        'description' => 'Quad, globo aerostático, clases de cocina, spa tradicional y otras actividades exclusivas.',
                        'meta_title' => 'Actividades y Experiencias Únicas en Marrakech',
                        'meta_description' => 'Quad en el desierto, vuelo en globo, clase de cocina marroquí, hammam y spa tradicional en Marrakech.',
                    ],
                    'ar' => [
                        'name' => 'تجارب فريدة',
                        'slug' => 'تجارب-فريدة',
                        'description' => 'الدراجات الرباعية، المنطاد، دروس الطبخ، السبا التقليدي وأنشطة حصرية أخرى.',
                        'meta_title' => 'أنشطة وتجارب فريدة في مراكش',
                        'meta_description' => 'ركوب الدراجات الرباعية في الصحراء، رحلة بالمنطاد، دروس الطبخ المغربي، الحمام والسبا التقليدي في مراكش.',
                    ],
                ],
            ],
        ];

        foreach ($categories as $data) {
            $translations = $data['translations'];
            unset($data['translations']);

            $category = Category::create($data);

            foreach ($translations as $locale => $trans) {
                foreach ($trans as $key => $value) {
                    $category->setTranslation($key, $locale, $value);
                }
            }

            $category->save();
        }
    }
}
