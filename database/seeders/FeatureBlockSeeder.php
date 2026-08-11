<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FeatureBlock;
use App\Models\FeatureBlockTranslation;
use App\Models\FeatureBlocksSectionTranslation;
use App\Models\Language;

class FeatureBlockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer les langues actives
        $activeLanguages = Language::active()->get();
        
        // Si aucune langue n'est active, utiliser les langues par défaut
        if ($activeLanguages->isEmpty()) {
            $activeLanguages = collect([
                (object)['code' => 'fr', 'name' => 'Français'],
                (object)['code' => 'en', 'name' => 'English'],
                (object)['code' => 'ar', 'name' => 'العربية'],
            ]);
        }

        // Données des feature blocks avec traductions
        $featureBlocksData = [
            [
                'icon' => 'fas fa-shield-alt',
                'order' => 1,
                'is_active' => true,
                'translations' => [
                    'fr' => [
                        'title' => 'Réservation Sécurisée',
                        'description' => 'Paiement 100% sécurisé avec protection des données. Vos informations sont en sécurité.',
                    ],
                    'en' => [
                        'title' => 'Secure Booking',
                        'description' => '100% secure payment with data protection. Your information is safe.',
                    ],
                    'ar' => [
                        'title' => 'حجز آمن',
                        'description' => 'دفع آمن 100% مع حماية البيانات. معلوماتك آمنة.',
                    ],
                ],
            ],
            [
                'icon' => 'fas fa-undo',
                'order' => 2,
                'is_active' => true,
                'translations' => [
                    'fr' => [
                        'title' => 'Annulation Gratuite',
                        'description' => 'Annulez sans frais jusqu\'à 24h avant votre visite. Flexibilité totale.',
                    ],
                    'en' => [
                        'title' => 'Free Cancellation',
                        'description' => 'Cancel free of charge up to 24 hours before your visit. Total flexibility.',
                    ],
                    'ar' => [
                        'title' => 'إلغاء مجاني',
                        'description' => 'إلغاء مجانًا حتى 24 ساعة قبل زيارتك. مرونة كاملة.',
                    ],
                ],
            ],
            [
                'icon' => 'fas fa-headset',
                'order' => 3,
                'is_active' => true,
                'translations' => [
                    'fr' => [
                        'title' => 'Support 24/7',
                        'description' => 'Notre équipe est disponible 24h/24 et 7j/7 pour vous assister. Assistance immédiate.',
                    ],
                    'en' => [
                        'title' => '24/7 Support',
                        'description' => 'Our team is available 24/7 to assist you. Immediate assistance.',
                    ],
                    'ar' => [
                        'title' => 'دعم على مدار الساعة',
                        'description' => 'فريقنا متاح على مدار الساعة لمساعدتك. مساعدة فورية.',
                    ],
                ],
            ],
            [
                'icon' => 'fas fa-star',
                'order' => 4,
                'is_active' => true,
                'translations' => [
                    'fr' => [
                        'title' => 'Meilleur Prix Garanti',
                        'description' => 'Nous garantissons les meilleurs prix du marché. Si vous trouvez moins cher, on vous rembourse la différence.',
                    ],
                    'en' => [
                        'title' => 'Best Price Guaranteed',
                        'description' => 'We guarantee the best prices on the market. If you find cheaper, we refund the difference.',
                    ],
                    'ar' => [
                        'title' => 'أفضل سعر مضمون',
                        'description' => 'نضمن أفضل الأسعار في السوق. إذا وجدت أرخص، نعيد لك الفرق.',
                    ],
                ],
            ],
        ];

        foreach ($featureBlocksData as $blockData) {
            // Utiliser la première traduction disponible comme titre/description par défaut
            $defaultTranslation = $blockData['translations']['fr'] ?? reset($blockData['translations']);
            
            // Créer le feature block
            $featureBlock = FeatureBlock::create([
                'icon' => $blockData['icon'],
                'title' => $defaultTranslation['title'] ?? 'Feature Block',
                'description' => $defaultTranslation['description'] ?? 'Description',
                'order' => $blockData['order'],
                'is_active' => $blockData['is_active'],
            ]);

            // Créer les traductions pour chaque langue active
            foreach ($activeLanguages as $language) {
                $locale = $language->code;
                
                // Utiliser les traductions fournies ou un fallback
                $translationData = $blockData['translations'][$locale] ?? $blockData['translations']['fr'] ?? [
                    'title' => 'Feature Block ' . $featureBlock->order,
                    'description' => 'Description du bloc de fonctionnalité ' . $featureBlock->order,
                ];

                FeatureBlockTranslation::create([
                    'feature_block_id' => $featureBlock->id,
                    'locale' => $locale,
                    'title' => $translationData['title'],
                    'description' => $translationData['description'],
                ]);
            }
        }

        // Créer les traductions de section si elles n'existent pas
        $sectionTranslations = [
            'fr' => [
                'title' => 'Pourquoi réserver avec nous ?',
                'description' => 'Découvrez les avantages qui font de nous votre meilleur choix pour vos visites et expériences.',
            ],
            'en' => [
                'title' => 'Why book with us?',
                'description' => 'Discover the benefits that make us your best choice for your tours and experiences.',
            ],
            'ar' => [
                'title' => 'لماذا تحجز معنا؟',
                'description' => 'اكتشف الفوائد التي تجعلنا خيارك الأفضل لجولاتك وتجاربك.',
            ],
        ];

        foreach ($activeLanguages as $language) {
            $locale = $language->code;
            $translationData = $sectionTranslations[$locale] ?? $sectionTranslations['fr'];

            FeatureBlocksSectionTranslation::updateOrCreate(
                ['locale' => $locale],
                [
                    'title' => $translationData['title'],
                    'description' => $translationData['description'],
                ]
            );
        }

        $this->command->info('✅ Feature blocks créés avec succès !');
        $this->command->info('   - ' . count($featureBlocksData) . ' blocs créés');
        $this->command->info('   - ' . (count($featureBlocksData) * $activeLanguages->count()) . ' traductions de blocs créées');
        $this->command->info('   - ' . $activeLanguages->count() . ' traductions de section créées/mises à jour');
    }
}

