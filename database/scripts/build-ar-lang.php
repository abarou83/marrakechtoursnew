<?php

$en = json_decode(file_get_contents(__DIR__ . '/../../lang/en.json'), true);

$arOverrides = [
    'Home' => 'الرئيسية',
    'All Tours' => 'جميع الجولات',
    'My Bookings' => 'حجوزاتي',
    'Profile' => 'الملف الشخصي',
    'Log Out' => 'تسجيل الخروج',
    'Login / Register' => 'تسجيل الدخول / إنشاء حساب',
    'Menu' => 'القائمة',
    'Currency' => 'العملة',
    'Language' => 'اللغة',
    'Contact' => 'اتصل بنا',
    'Book' => 'احجز',
    'Book now' => 'احجز الآن',
    'From' => 'ابتداءً من',
    'per person' => 'للشخص',
    'Filter Tours' => 'تصفية الجولات',
    'Search' => 'بحث',
    'Reset filters' => 'إعادة تعيين الفلاتر',
    'View details' => 'عرض التفاصيل',
    'View all tours' => 'عرض جميع الجولات',
    'No images available' => 'لا توجد صور',
    'Free cancellation' => 'إلغاء مجاني',
    'Adults' => 'بالغون',
    'Children' => 'أطفال',
    'Babies' => 'رضّع',
    'Select a date' => 'اختر تاريخاً',
    'Recherche' => 'بحث',
    'Category' => 'الفئة',
    'Location' => 'الموقع',
    'Min Price' => 'الحد الأدنى للسعر',
    'Max Price' => 'الحد الأقصى للسعر',
    'All categories' => 'جميع الفئات',
    'tours found' => 'جولات',
    'tour found' => 'جولة واحدة',
    'avis' => 'تقييم',
    'Best-seller' => 'الأكثر مبيعاً',
    'Guides' => 'الأدلة',
    'Blog' => 'المدونة',
    'Explore the world' => 'استكشف العالم',
    'Navigation' => 'التنقل',
    'All rights reserved.' => 'جميع الحقوق محفوظة.',
    'Connectez-vous pour ajouter aux favoris' => 'سجّل الدخول لإضافة إلى المفضلة',
    'Retiré des favoris' => 'تمت الإزالة من المفضلة',
    'Ajouter aux favoris' => 'أضف إلى المفضلة',
    'Retirer des favoris' => 'إزالة من المفضلة',
];

$ar = array_merge($en, $arOverrides);

file_put_contents(
    __DIR__ . '/../../lang/ar.json',
    json_encode($ar, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
);

echo 'Created lang/ar.json with ' . count($ar) . " keys\n";
