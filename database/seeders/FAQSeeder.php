<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FAQ;
use App\Models\FAQTranslation;

class FAQSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faqs = [
            [
                'order' => 1,
                'is_active' => true,
                'translations' => [
                    'fr' => [
                        'question' => 'Comment puis-je réserver un tour ?',
                        'answer' => "Réserver un tour est très simple ! Parcourez notre sélection de tours, choisissez celui qui vous intéresse, sélectionnez votre formule, la date et le nombre de participants. Vous pouvez réserver en quelques clics directement sur notre site.",
                    ],
                    'en' => [
                        'question' => 'How can I book a tour?',
                        'answer' => 'Booking a tour is very simple! Browse our selection of tours, choose the one that interests you, select your package, date and number of participants. You can book in just a few clicks directly on our website.',
                    ],
                ],
            ],
            [
                'order' => 2,
                'is_active' => true,
                'translations' => [
                    'fr' => [
                        'question' => 'Puis-je annuler ma réservation ?',
                        'answer' => "Oui, vous pouvez annuler votre réservation gratuitement jusqu'à 24 heures avant le début du tour. Les annulations après ce délai peuvent être soumises à des frais selon les conditions de la réservation.",
                    ],
                    'en' => [
                        'question' => 'Can I cancel my booking?',
                        'answer' => 'Yes, you can cancel your booking for free up to 24 hours before the start of the tour. Cancellations after this deadline may be subject to fees according to booking conditions.',
                    ],
                ],
            ],
            [
                'order' => 3,
                'is_active' => true,
                'translations' => [
                    'fr' => [
                        'question' => 'Quels moyens de paiement acceptez-vous ?',
                        'answer' => 'Nous acceptons les cartes bancaires (Visa, Mastercard), PayPal et les virements bancaires. Le paiement est sécurisé via notre système de paiement crypté.',
                    ],
                    'en' => [
                        'question' => 'What payment methods do you accept?',
                        'answer' => 'We accept credit cards (Visa, Mastercard), PayPal and bank transfers. Payment is secure through our encrypted payment system.',
                    ],
                ],
            ],
            [
                'order' => 4,
                'is_active' => true,
                'translations' => [
                    'fr' => [
                        'question' => 'Les tours sont-ils adaptés aux enfants ?',
                        'answer' => "Oui, la plupart de nos tours sont adaptés aux enfants. Chaque description de tour indique l'âge minimum recommandé. N'hésitez pas à nous contacter si vous avez des questions spécifiques.",
                    ],
                    'en' => [
                        'question' => 'Are tours suitable for children?',
                        'answer' => 'Yes, most of our tours are suitable for children. Each tour description indicates the recommended minimum age. Do not hesitate to contact us if you have specific questions.',
                    ],
                ],
            ],
            [
                'order' => 5,
                'is_active' => true,
                'translations' => [
                    'fr' => [
                        'question' => 'Que se passe-t-il si le tour est annulé ?',
                        'answer' => "En cas d'annulation du tour par notre part, nous vous rembourserons intégralement ou vous proposerons une alternative. Vous serez contacté dans les plus brefs délais pour organiser une solution.",
                    ],
                    'en' => [
                        'question' => 'What happens if the tour is cancelled?',
                        'answer' => 'If the tour is cancelled by us, we will fully refund you or offer an alternative. You will be contacted as soon as possible to arrange a solution.',
                    ],
                ],
            ],
            [
                'order' => 6,
                'is_active' => true,
                'translations' => [
                    'fr' => [
                        'question' => 'Offrez-vous des réductions pour les groupes ?',
                        'answer' => "Oui ! Nous proposons des tarifs spéciaux pour les groupes de 6 personnes ou plus. Contactez-nous pour obtenir un devis personnalisé et découvrir nos offres groupe.",
                    ],
                    'en' => [
                        'question' => 'Do you offer group discounts?',
                        'answer' => 'Yes! We offer special rates for groups of 6 or more. Contact us to get a personalized quote and discover our group offers.',
                    ],
                ],
            ],
        ];

        foreach ($faqs as $faqData) {
            $faq = FAQ::create([
                'order' => $faqData['order'],
                'is_active' => $faqData['is_active'],
            ]);

            foreach ($faqData['translations'] as $locale => $translation) {
                FAQTranslation::create([
                    'faq_id' => $faq->id,
                    'locale' => $locale,
                    'question' => $translation['question'],
                    'answer' => $translation['answer'],
                ]);
            }
        }

        echo "✅ " . count($faqs) . " FAQs créées\n";
    }
}
