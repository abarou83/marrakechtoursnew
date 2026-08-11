<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Page;
use App\Models\PageTranslation;
use App\Models\Language;
use Illuminate\Support\Str;

class PageSeeder extends Seeder
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
            ]);
        }

        $pages = [
            [
                'order' => 1,
                'is_active' => true,
                'translations' => [
                    'fr' => [
                        'slug' => 'confidentialite',
                        'title' => 'Politique de Confidentialité',
                        'content' => '<h2>1. Collecte des informations</h2>
<p>Nous collectons les informations que vous nous fournissez directement lorsque vous utilisez notre site, notamment :</p>
<ul>
    <li>Nom et prénom</li>
    <li>Adresse e-mail</li>
    <li>Numéro de téléphone</li>
    <li>Informations de réservation</li>
</ul>

<h2>2. Utilisation des informations</h2>
<p>Nous utilisons vos informations pour :</p>
<ul>
    <li>Traiter vos réservations</li>
    <li>Vous contacter concernant vos réservations</li>
    <li>Améliorer nos services</li>
    <li>Vous envoyer des communications marketing (avec votre consentement)</li>
</ul>

<h2>3. Protection des données</h2>
<p>Nous mettons en œuvre des mesures de sécurité appropriées pour protéger vos informations personnelles contre tout accès non autorisé, altération, divulgation ou destruction.</p>

<h2>4. Vos droits</h2>
<p>Conformément au RGPD, vous avez le droit de :</p>
<ul>
    <li>Accéder à vos données personnelles</li>
    <li>Rectifier vos données</li>
    <li>Supprimer vos données</li>
    <li>Vous opposer au traitement de vos données</li>
    <li>Demander la portabilité de vos données</li>
</ul>

<h2>5. Contact</h2>
<p>Pour toute question concernant cette politique de confidentialité, veuillez nous contacter à : <a href="mailto:contact@example.com">contact@example.com</a></p>',
                        'meta_title' => 'Politique de Confidentialité - Tourify',
                        'meta_description' => 'Découvrez comment nous collectons, utilisons et protégeons vos informations personnelles. Politique de confidentialité complète et transparente.',
                        'meta_keywords' => 'confidentialité, protection des données, RGPD, vie privée, données personnelles',
                    ],
                    'en' => [
                        'slug' => 'privacy-policy',
                        'title' => 'Privacy Policy',
                        'content' => '<h2>1. Information Collection</h2>
<p>We collect information that you provide directly when using our website, including:</p>
<ul>
    <li>First and last name</li>
    <li>Email address</li>
    <li>Phone number</li>
    <li>Booking information</li>
</ul>

<h2>2. Use of Information</h2>
<p>We use your information to:</p>
<ul>
    <li>Process your bookings</li>
    <li>Contact you regarding your bookings</li>
    <li>Improve our services</li>
    <li>Send you marketing communications (with your consent)</li>
</ul>

<h2>3. Data Protection</h2>
<p>We implement appropriate security measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction.</p>

<h2>4. Your Rights</h2>
<p>In accordance with GDPR, you have the right to:</p>
<ul>
    <li>Access your personal data</li>
    <li>Rectify your data</li>
    <li>Delete your data</li>
    <li>Object to the processing of your data</li>
    <li>Request data portability</li>
</ul>

<h2>5. Contact</h2>
<p>For any questions regarding this privacy policy, please contact us at: <a href="mailto:contact@example.com">contact@example.com</a></p>',
                        'meta_title' => 'Privacy Policy - Tourify',
                        'meta_description' => 'Discover how we collect, use, and protect your personal information. Complete and transparent privacy policy.',
                        'meta_keywords' => 'privacy, data protection, GDPR, personal data, privacy policy',
                    ],
                ],
            ],
            [
                'order' => 2,
                'is_active' => true,
                'translations' => [
                    'fr' => [
                        'slug' => 'conditions-generales',
                        'title' => 'Conditions Générales d\'Utilisation',
                        'content' => '<h2>1. Acceptation des conditions</h2>
<p>En accédant et en utilisant ce site web, vous acceptez d\'être lié par les présentes conditions générales d\'utilisation.</p>

<h2>2. Utilisation du site</h2>
<p>Vous vous engagez à utiliser ce site de manière légale et conforme à ces conditions. Il est interdit de :</p>
<ul>
    <li>Utiliser le site à des fins illégales</li>
    <li>Tenter d\'accéder à des zones non autorisées</li>
    <li>Transmettre des virus ou codes malveillants</li>
    <li>Copier ou reproduire le contenu sans autorisation</li>
</ul>

<h2>3. Réservations</h2>
<p>Les réservations sont soumises aux conditions suivantes :</p>
<ul>
    <li>Les prix sont indiqués en euros (€) TTC</li>
    <li>Le paiement doit être effectué selon les modalités indiquées</li>
    <li>Les annulations sont régies par notre politique d\'annulation</li>
    <li>Nous nous réservons le droit d\'annuler une réservation en cas de force majeure</li>
</ul>

<h2>4. Propriété intellectuelle</h2>
<p>Tous les contenus présents sur ce site (textes, images, logos, etc.) sont protégés par le droit d\'auteur et appartiennent à Tourify ou à ses partenaires.</p>

<h2>5. Limitation de responsabilité</h2>
<p>Tourify ne saurait être tenu responsable des dommages directs ou indirects résultant de l\'utilisation de ce site.</p>

<h2>6. Modifications</h2>
<p>Nous nous réservons le droit de modifier ces conditions à tout moment. Les modifications entrent en vigueur dès leur publication sur le site.</p>',
                        'meta_title' => 'Conditions Générales d\'Utilisation - Tourify',
                        'meta_description' => 'Consultez nos conditions générales d\'utilisation pour connaître vos droits et obligations lors de l\'utilisation de notre site et de nos services.',
                        'meta_keywords' => 'conditions générales, CGU, termes d\'utilisation, conditions d\'utilisation, réservations',
                    ],
                    'en' => [
                        'slug' => 'terms-of-use',
                        'title' => 'Terms of Use',
                        'content' => '<h2>1. Acceptance of Terms</h2>
<p>By accessing and using this website, you agree to be bound by these terms of use.</p>

<h2>2. Use of the Site</h2>
<p>You agree to use this site in a legal manner and in accordance with these terms. It is prohibited to:</p>
<ul>
    <li>Use the site for illegal purposes</li>
    <li>Attempt to access unauthorized areas</li>
    <li>Transmit viruses or malicious code</li>
    <li>Copy or reproduce content without authorization</li>
</ul>

<h2>3. Bookings</h2>
<p>Bookings are subject to the following conditions:</p>
<ul>
    <li>Prices are indicated in euros (€) including VAT</li>
    <li>Payment must be made according to the indicated methods</li>
    <li>Cancellations are governed by our cancellation policy</li>
    <li>We reserve the right to cancel a booking in case of force majeure</li>
</ul>

<h2>4. Intellectual Property</h2>
<p>All content on this site (texts, images, logos, etc.) is protected by copyright and belongs to Tourify or its partners.</p>

<h2>5. Limitation of Liability</h2>
<p>Tourify cannot be held responsible for direct or indirect damages resulting from the use of this site.</p>

<h2>6. Modifications</h2>
<p>We reserve the right to modify these terms at any time. Modifications take effect upon publication on the site.</p>',
                        'meta_title' => 'Terms of Use - Tourify',
                        'meta_description' => 'Read our terms of use to understand your rights and obligations when using our website and services.',
                        'meta_keywords' => 'terms of use, terms and conditions, booking terms, website terms',
                    ],
                ],
            ],
            [
                'order' => 3,
                'is_active' => true,
                'translations' => [
                    'fr' => [
                        'slug' => 'a-propos',
                        'title' => 'À Propos de Nous',
                        'content' => '<h2>Notre Histoire</h2>
<p>Tourify est né de la passion pour le voyage et la découverte. Fondée en 2020, notre entreprise s\'engage à offrir des expériences de voyage authentiques et mémorables.</p>

<h2>Notre Mission</h2>
<p>Notre mission est de rendre le voyage accessible à tous en proposant des expériences uniques, soigneusement sélectionnées, qui permettent de découvrir le monde de manière responsable et enrichissante.</p>

<h2>Nos Valeurs</h2>
<ul>
    <li><strong>Authenticité</strong> : Nous privilégions les expériences authentiques qui reflètent la vraie culture des destinations.</li>
    <li><strong>Responsabilité</strong> : Nous nous engageons pour un tourisme durable et respectueux de l\'environnement.</li>
    <li><strong>Qualité</strong> : Nous sélectionnons rigoureusement nos partenaires et nos expériences.</li>
    <li><strong>Service client</strong> : Votre satisfaction est notre priorité absolue.</li>
</ul>

<h2>Notre Équipe</h2>
<p>Notre équipe est composée de passionnés de voyage qui partagent la même vision : rendre le voyage accessible et enrichissant pour tous.</p>

<h2>Contactez-nous</h2>
<p>Pour toute question ou suggestion, n\'hésitez pas à nous contacter à : <a href="mailto:contact@example.com">contact@example.com</a></p>',
                        'meta_title' => 'À Propos de Nous - Tourify',
                        'meta_description' => 'Découvrez l\'histoire, la mission et les valeurs de Tourify. Nous sommes passionnés par le voyage et nous nous engageons à offrir des expériences authentiques.',
                        'meta_keywords' => 'à propos, notre histoire, mission, valeurs, tourisme, voyage',
                    ],
                    'en' => [
                        'slug' => 'about-us',
                        'title' => 'About Us',
                        'content' => '<h2>Our Story</h2>
<p>Tourify was born from a passion for travel and discovery. Founded in 2020, our company is committed to offering authentic and memorable travel experiences.</p>

<h2>Our Mission</h2>
<p>Our mission is to make travel accessible to everyone by offering unique, carefully selected experiences that allow you to discover the world in a responsible and enriching way.</p>

<h2>Our Values</h2>
<ul>
    <li><strong>Authenticity</strong>: We favor authentic experiences that reflect the true culture of destinations.</li>
    <li><strong>Responsibility</strong>: We are committed to sustainable and environmentally friendly tourism.</li>
    <li><strong>Quality</strong>: We rigorously select our partners and experiences.</li>
    <li><strong>Customer Service</strong>: Your satisfaction is our absolute priority.</li>
</ul>

<h2>Our Team</h2>
<p>Our team is made up of travel enthusiasts who share the same vision: making travel accessible and enriching for everyone.</p>

<h2>Contact Us</h2>
<p>For any questions or suggestions, please contact us at: <a href="mailto:contact@example.com">contact@example.com</a></p>',
                        'meta_title' => 'About Us - Tourify',
                        'meta_description' => 'Discover Tourify\'s story, mission, and values. We are passionate about travel and committed to offering authentic experiences.',
                        'meta_keywords' => 'about us, our story, mission, values, tourism, travel',
                    ],
                ],
            ],
            [
                'order' => 4,
                'is_active' => true,
                'translations' => [
                    'fr' => [
                        'slug' => 'mentions-legales',
                        'title' => 'Mentions Légales',
                        'content' => '<h2>1. Informations sur l\'éditeur</h2>
<p><strong>Raison sociale</strong> : Tourify<br>
<strong>Forme juridique</strong> : SARL<br>
<strong>Capital social</strong> : 10 000 €<br>
<strong>Siège social</strong> : 123 Rue de la Tour, 75001 Paris, France<br>
<strong>SIRET</strong> : 123 456 789 00012<br>
<strong>RCS</strong> : Paris B 123 456 789</p>

<h2>2. Directeur de publication</h2>
<p>Le directeur de publication est : [Nom du directeur]</p>

<h2>3. Hébergement</h2>
<p>Ce site est hébergé par :<br>
<strong>Nom de l\'hébergeur</strong><br>
<strong>Adresse</strong> : [Adresse de l\'hébergeur]<br>
<strong>Téléphone</strong> : [Numéro de téléphone]</p>

<h2>4. Protection des données</h2>
<p>Conformément à la loi "Informatique et Libertés" du 6 janvier 1978 modifiée et au Règlement Général sur la Protection des Données (RGPD), vous disposez d\'un droit d\'accès, de rectification, de suppression et d\'opposition aux données personnelles vous concernant.</p>

<h2>5. Propriété intellectuelle</h2>
<p>L\'ensemble de ce site relève de la législation française et internationale sur le droit d\'auteur et la propriété intellectuelle. Tous les droits de reproduction sont réservés.</p>

<h2>6. Contact</h2>
<p>Pour toute question concernant ces mentions légales, vous pouvez nous contacter à : <a href="mailto:contact@example.com">contact@example.com</a></p>',
                        'meta_title' => 'Mentions Légales - Tourify',
                        'meta_description' => 'Consultez les mentions légales de Tourify : informations sur l\'éditeur, hébergement, protection des données et propriété intellectuelle.',
                        'meta_keywords' => 'mentions légales, informations légales, éditeur, hébergement, SIRET',
                    ],
                    'en' => [
                        'slug' => 'legal-notice',
                        'title' => 'Legal Notice',
                        'content' => '<h2>1. Publisher Information</h2>
<p><strong>Company name</strong>: Tourify<br>
<strong>Legal form</strong>: LLC<br>
<strong>Share capital</strong>: €10,000<br>
<strong>Registered office</strong>: 123 Tour Street, 75001 Paris, France<br>
<strong>Registration number</strong>: 123 456 789 00012<br>
<strong>RCS</strong>: Paris B 123 456 789</p>

<h2>2. Publication Director</h2>
<p>The publication director is: [Director Name]</p>

<h2>3. Hosting</h2>
<p>This site is hosted by:<br>
<strong>Host name</strong><br>
<strong>Address</strong>: [Host address]<br>
<strong>Phone</strong>: [Phone number]</p>

<h2>4. Data Protection</h2>
<p>In accordance with the "Data Protection Act" of January 6, 1978, as amended, and the General Data Protection Regulation (GDPR), you have the right to access, rectify, delete, and object to personal data concerning you.</p>

<h2>5. Intellectual Property</h2>
<p>This entire site is subject to French and international legislation on copyright and intellectual property. All reproduction rights are reserved.</p>

<h2>6. Contact</h2>
<p>For any questions regarding this legal notice, you can contact us at: <a href="mailto:contact@example.com">contact@example.com</a></p>',
                        'meta_title' => 'Legal Notice - Tourify',
                        'meta_description' => 'Read Tourify\'s legal notice: publisher information, hosting, data protection, and intellectual property.',
                        'meta_keywords' => 'legal notice, legal information, publisher, hosting, registration',
                    ],
                ],
            ],
        ];

        foreach ($pages as $pageData) {
            // Créer la page
            $page = Page::create([
                'order' => $pageData['order'],
                'is_active' => $pageData['is_active'],
            ]);

            // Créer les traductions pour chaque langue active
            foreach ($activeLanguages as $language) {
                $locale = $language->code;
                
                // Vérifier si une traduction existe pour cette langue
                if (isset($pageData['translations'][$locale])) {
                    $translationData = $pageData['translations'][$locale];
                    
                    PageTranslation::create([
                        'page_id' => $page->id,
                        'locale' => $locale,
                        'slug' => $translationData['slug'],
                        'title' => $translationData['title'],
                        'content' => $translationData['content'],
                        'meta_title' => $translationData['meta_title'] ?? null,
                        'meta_description' => $translationData['meta_description'] ?? null,
                        'meta_keywords' => $translationData['meta_keywords'] ?? null,
                    ]);
                }
            }
        }

        $this->command->info('✅ Pages de démonstration créées avec succès !');
    }
}
