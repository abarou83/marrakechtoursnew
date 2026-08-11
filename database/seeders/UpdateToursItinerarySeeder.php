<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tour;
use App\Models\TourTranslation;

class UpdateToursItinerarySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Exemples d'itinéraires au format titre|texte
        $itineraryExamples = [
            'fr' => "Accueil|Rencontre avec votre guide à l'entrée principale du site. Présentation du programme de la journée et remise des billets d'entrée.\n\nVisite guidée|Découverte des points d'intérêt majeurs avec un guide expert qui vous expliquera l'histoire et les anecdotes des lieux. Temps pour prendre des photos.\n\nPause déjeuner|Temps libre pour déjeuner (repas non inclus). Votre guide vous recommandera des restaurants locaux authentiques.\n\nSuite de la visite|Continuation de la découverte avec des explications approfondies sur les aspects culturels et historiques.\n\nConclusion|Retour au point de départ. Temps libre pour les dernières photos ou achats de souvenirs.",
            
            'en' => "Welcome|Meet your guide at the main entrance of the site. Presentation of the day's program and ticket distribution.\n\nGuided tour|Discovery of major points of interest with an expert guide who will explain the history and anecdotes of the places. Time to take photos.\n\nLunch break|Free time for lunch (meal not included). Your guide will recommend authentic local restaurants.\n\nContinuation|Continuation of the discovery with in-depth explanations on cultural and historical aspects.\n\nConclusion|Return to the starting point. Free time for last photos or souvenir shopping.",
            
            'ar' => "الترحيب|قابل دليلك عند المدخل الرئيسي للموقع. عرض برنامج اليوم وتوزيع التذاكر.\n\nجولة إرشادية|اكتشاف نقاط الاهتمام الرئيسية مع دليل خبير سيشرح تاريخ وطرائف الأماكن. وقت لأخذ الصور.\n\nاستراحة الغداء|وقت حر لتناول الغداء (الوجبة غير مشمولة). سينصحك دليلك بمطاعم محلية أصيلة.\n\nالمتابعة|متابعة الاكتشاف مع شروحات متعمقة حول الجوانب الثقافية والتاريخية.\n\nالخلاصة|العودة إلى نقطة البداية. وقت حر لأخذ الصور الأخيرة أو شراء الهدايا التذكارية."
        ];

        // Mettre à jour tous les tours
        $tours = Tour::with('translations')->get();
        
        foreach ($tours as $tour) {
            foreach ($tour->translations as $translation) {
                $locale = $translation->locale;
                
                // Si l'itinéraire est vide ou très court, le remplacer par un exemple
                if (empty($translation->itinerary) || strlen($translation->itinerary) < 50) {
                    if (isset($itineraryExamples[$locale])) {
                        $translation->update([
                            'itinerary' => $itineraryExamples[$locale]
                        ]);
                        
                        $this->command->info("✅ Itinéraire mis à jour pour le tour #{$tour->id} ({$tour->title}) en {$locale}");
                    }
                } else {
                    // Si l'itinéraire existe déjà mais n'est pas au bon format, le convertir
                    $lines = explode("\n", $translation->itinerary);
                    $formattedItinerary = [];
                    
                    foreach ($lines as $line) {
                        $line = trim($line);
                        if (empty($line)) continue;
                        
                        // Si la ligne ne contient pas de séparateur, la formater
                        if (strpos($line, '|') === false && strpos($line, ' - ') === false) {
                            // Ajouter un titre simple et utiliser la ligne comme description
                            $formattedItinerary[] = "Étape|{$line}";
                        } else {
                            // Garder tel quel si déjà formaté
                            $formattedItinerary[] = $line;
                        }
                    }
                    
                    if (!empty($formattedItinerary)) {
                        $translation->update([
                            'itinerary' => implode("\n", $formattedItinerary)
                        ]);
                        
                        $this->command->info("✅ Itinéraire formaté pour le tour #{$tour->id} ({$tour->title}) en {$locale}");
                    }
                }
            }
        }
        
        $this->command->info("\n🎉 Mise à jour des itinéraires terminée !");
    }
}
