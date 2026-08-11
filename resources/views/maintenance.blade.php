<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Site Under Maintenance - {{ config('app.name', 'Tourify') }}</title>
    
    <!-- Favicon -->
    @php
        $favicon = site_setting('favicon_path');
    @endphp
    @if($favicon && \Storage::disk('public')->exists($favicon))
        <link rel="icon" type="image/x-icon" href="{{ \Storage::url($favicon) }}">
    @endif
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    @php
        $backgroundColor = site_setting('background_color', '#fdfbf7');
        $primaryColor = site_setting('primary_color', '#211951');
        $secondaryColor = site_setting('secondary_color', '#836FFF');
        $accentColor = site_setting('accent_color', '#15F5BA');
    @endphp
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Figtree', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: {{ $backgroundColor }};
            padding: 20px;
            overflow: hidden;
        }
        
        .maintenance-container {
            text-align: center;
            max-width: 600px;
            width: 100%;
            padding: 60px 40px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            position: relative;
            z-index: 10;
            animation: fadeInUp 0.8s ease-out;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .logo {
            margin-bottom: 30px;
        }
        
        .logo img {
            max-height: 80px;
            width: auto;
        }
        
        .icon-container {
            width: 120px;
            height: 120px;
            margin: 0 auto 30px;
            background: linear-gradient(135deg, {{ $primaryColor }} 0%, {{ $secondaryColor }} 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(131, 111, 255, 0.4);
            }
            50% {
                transform: scale(1.05);
                box-shadow: 0 0 0 20px rgba(131, 111, 255, 0);
            }
        }
        
        .icon-container i {
            font-size: 48px;
            color: white;
        }
        
        h1 {
            font-size: 2.5rem;
            font-weight: 800;
            color: {{ $primaryColor }};
            margin-bottom: 16px;
            line-height: 1.2;
        }
        
        .subtitle {
            font-size: 1.25rem;
            color: #6b7280;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        
        .message {
            background: #f3f4f6;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 30px;
            color: #374151;
            font-size: 1rem;
            line-height: 1.7;
        }
        
        .progress-container {
            margin-bottom: 30px;
        }
        
        .progress-bar {
            height: 6px;
            background: #e5e7eb;
            border-radius: 3px;
            overflow: hidden;
        }
        
        .progress-bar-inner {
            height: 100%;
            background: linear-gradient(90deg, {{ $primaryColor }} 0%, {{ $accentColor }} 100%);
            border-radius: 3px;
            animation: loading 2s ease-in-out infinite;
            width: 30%;
        }
        
        @keyframes loading {
            0% {
                transform: translateX(-100%);
            }
            100% {
                transform: translateX(400%);
            }
        }
        
        .contact-info {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            margin-top: 30px;
        }
        
        .contact-item {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #6b7280;
            font-size: 0.9rem;
        }
        
        .contact-item i {
            color: {{ $secondaryColor }};
        }
        
        .social-links {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 30px;
        }
        
        .social-links a {
            width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f3f4f6;
            border-radius: 50%;
            color: {{ $primaryColor }};
            font-size: 1.2rem;
            transition: all 0.3s ease;
            text-decoration: none;
        }
        
        .social-links a:hover {
            background: {{ $primaryColor }};
            color: white;
            transform: translateY(-3px);
        }
        
        /* Éléments décoratifs */
        .decoration {
            position: fixed;
            border-radius: 50%;
            opacity: 0.1;
            z-index: 1;
        }
        
        .decoration-1 {
            width: 400px;
            height: 400px;
            background: {{ $accentColor }};
            top: -100px;
            right: -100px;
        }
        
        .decoration-2 {
            width: 300px;
            height: 300px;
            background: white;
            bottom: -50px;
            left: -50px;
        }
        
        .decoration-3 {
            width: 200px;
            height: 200px;
            background: {{ $accentColor }};
            bottom: 20%;
            right: 10%;
        }
        
        /* Responsive */
        @media (max-width: 640px) {
            .maintenance-container {
                padding: 40px 25px;
            }
            
            h1 {
                font-size: 1.75rem;
            }
            
            .subtitle {
                font-size: 1rem;
            }
            
            .icon-container {
                width: 100px;
                height: 100px;
            }
            
            .icon-container i {
                font-size: 36px;
            }
            
            .whatsapp-btn {
                bottom: 20px !important;
                right: 20px !important;
                width: 55px !important;
                height: 55px !important;
                font-size: 1.5rem !important;
            }
        }
        
        /* Bouton WhatsApp clignotant */
        .whatsapp-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 65px;
            height: 65px;
            background: #25D366;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.8rem;
            text-decoration: none;
            z-index: 100;
            box-shadow: 0 4px 15px rgba(37, 211, 102, 0.4);
            animation: whatsappPulse 1.5s infinite;
            transition: transform 0.3s ease;
        }
        
        .whatsapp-btn:hover {
            transform: scale(1.1);
            animation: none;
            box-shadow: 0 6px 20px rgba(37, 211, 102, 0.6);
        }
        
        @keyframes whatsappPulse {
            0% {
                box-shadow: 0 0 0 0 rgba(37, 211, 102, 0.7);
                transform: scale(1);
            }
            50% {
                box-shadow: 0 0 0 15px rgba(37, 211, 102, 0);
                transform: scale(1.05);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(37, 211, 102, 0);
                transform: scale(1);
            }
        }
        
        .whatsapp-tooltip {
            position: absolute;
            right: 75px;
            background: #333;
            color: white;
            padding: 8px 15px;
            border-radius: 8px;
            font-size: 0.85rem;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        
        .whatsapp-tooltip::after {
            content: '';
            position: absolute;
            right: -8px;
            top: 50%;
            transform: translateY(-50%);
            border-width: 8px;
            border-style: solid;
            border-color: transparent transparent transparent #333;
        }
        
        .whatsapp-btn:hover .whatsapp-tooltip {
            opacity: 1;
            visibility: visible;
        }
    </style>
</head>
<body>

    
    <div class="maintenance-container">
        <!-- Logo -->
        @php
            $logo = site_setting('logo_path');
        @endphp
        @if($logo && \Storage::disk('public')->exists($logo))
            <div class="logo">
                <img src="{{ \Storage::url($logo) }}" alt="{{ config('app.name') }}">
            </div>
        @endif
        
        <!-- Icône -->
        <div class="icon-container">
            <i class="fas fa-tools"></i>
        </div>
        
        <!-- Titre -->
        <h1>Site Under Maintenance</h1>
        
        <!-- Sous-titre -->
        <p class="subtitle">
            We're working hard to improve your experience!
        </p>
        
        <!-- Message personnalisé -->
        @php
            $maintenanceMessage = site_setting('maintenance_message', 'Our website is currently under maintenance for improvements. We will be back very soon. Thank you for your patience!');
        @endphp
        <div class="message">
            {{ $maintenanceMessage }}
        </div>
        
        <!-- Barre de progression -->
        <div class="progress-container">
            <div class="progress-bar">
                <div class="progress-bar-inner"></div>
            </div>
        </div>
        
        <!-- Informations de contact -->
        @php
            $companyEmail = site_setting('company_email', 'contact@example.com');
            $companyPhone = site_setting('company_phone', '+33 1 23 45 67 89');
            $companyAddress = site_setting('company_address', 'Paris, France');
            
            // Réseaux sociaux
            $socialFacebook = site_setting('social_facebook');
            $socialInstagram = site_setting('social_instagram');
            $socialTwitter = site_setting('social_twitter');
            $socialLinkedin = site_setting('social_linkedin');
            $socialYoutube = site_setting('social_youtube');
            $socialTiktok = site_setting('social_tiktok');
        @endphp
        <div class="contact-info">
            <div class="contact-item">
                <i class="fas fa-envelope"></i>
                <span>{{ $companyEmail }}</span>
            </div>
            <div class="contact-item">
                <i class="fas fa-phone"></i>
                <span>{{ $companyPhone }}</span>
            </div>
            @if($companyAddress)
            <div class="contact-item">
                <i class="fas fa-map-marker-alt"></i>
                <span>{{ $companyAddress }}</span>
            </div>
            @endif
        </div>
        
        <!-- Réseaux sociaux -->
        <div class="social-links">
            @if($socialFacebook)
                <a href="{{ $socialFacebook }}" target="_blank" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
            @endif
            @if($socialInstagram)
                <a href="{{ $socialInstagram }}" target="_blank" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
            @endif
            @if($socialTwitter)
                <a href="{{ $socialTwitter }}" target="_blank" aria-label="Twitter"><i class="fab fa-x-twitter"></i></a>
            @endif
            @if($socialLinkedin)
                <a href="{{ $socialLinkedin }}" target="_blank" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
            @endif
            @if($socialYoutube)
                <a href="{{ $socialYoutube }}" target="_blank" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
            @endif
            @if($socialTiktok)
                <a href="{{ $socialTiktok }}" target="_blank" aria-label="TikTok"><i class="fab fa-tiktok"></i></a>
            @endif
        </div>
    </div>
    
    <!-- Bouton WhatsApp clignotant -->
    @php
        $whatsappNumber = site_setting('whatsapp_number', '+33123456789');
        $whatsappMessage = urlencode('Hello, I would like to get information about your services.');
    @endphp
    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $whatsappNumber) }}?text={{ $whatsappMessage }}" 
       target="_blank" 
       class="whatsapp-btn"
       aria-label="Contact us on WhatsApp">
        <span class="whatsapp-tooltip">Contact us on WhatsApp</span>
        <i class="fab fa-whatsapp"></i>
    </a>
</body>
</html>
