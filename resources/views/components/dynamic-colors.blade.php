@php
    // Valeurs par défaut si la base de données n'est pas accessible
    try {
        $backgroundColor = site_setting('background_color', '#fdfbf7');
        $primary = primary_color();
        $secondary = secondary_color();
        $accent = site_setting('accent_color', '#15F5BA');
        $light = site_setting('light_color', '#F0F3FF');
        $borderColor = site_setting('border_color', '#e5e7eb');
    } catch (\Exception $e) {
        $backgroundColor = '#fdfbf7';
        $primary = '#211951';
        $secondary = '#836FFF';
        $accent = '#15F5BA';
        $light = '#F0F3FF';
        $borderColor = '#e5e7eb';
    }
@endphp

<style>
:root {
    --color-background: {{ $backgroundColor }};
    --color-primary: {{ $primary }};
    --color-secondary: {{ $secondary }};
    --color-accent: {{ $accent }};
    --color-light: {{ $light }};
    --color-border: {{ $borderColor }};
}

/* Couleur de fond du site */
body,
html {
    background-color: var(--color-background) !important;
}

.bg-background { background-color: var(--color-background) !important; }

/* Remplacer les fonds gris par la couleur personnalisée */
.bg-gray-50,
.bg-gray-100,
.bg-slate-50,
.bg-slate-100,
.bg-zinc-50,
.bg-zinc-100,
.bg-neutral-50,
.bg-neutral-100,
.bg-stone-50,
.bg-stone-100 {
    background-color: var(--color-background) !important;
}

/* Override Tailwind primary colors */
.bg-primary { background-color: var(--color-primary) !important; }
.text-primary { color: var(--color-primary) !important; }
.border-primary { border-color: var(--color-primary) !important; }

.bg-secondary { background-color: var(--color-secondary) !important; }
.text-secondary { color: var(--color-secondary) !important; }
.border-secondary { border-color: var(--color-secondary) !important; }

.bg-accent { background-color: var(--color-accent) !important; }
.text-accent { color: var(--color-accent) !important; }
.border-accent { border-color: var(--color-accent) !important; }

.bg-light { background-color: var(--color-light) !important; }

/* Hover states */
.hover\:bg-primary:hover { background-color: var(--color-primary) !important; }
.hover\:text-primary:hover { color: var(--color-primary) !important; }
.hover\:border-primary:hover { border-color: var(--color-primary) !important; }

.hover\:bg-opacity-90:hover { opacity: 0.9; }

/* Buttons */
.btn-primary {
    background-color: var(--color-primary);
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 0.5rem;
    font-weight: 600;
    transition: all 0.3s ease;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

.btn-primary:hover {
    opacity: 0.9;
    transform: translateY(-2px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

/* Focus states */
.focus\:ring-primary:focus { 
    --tw-ring-color: var(--color-primary) !important; 
}

/* Design Improvements - Enhanced Shadows */
.card-elevated {
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.card-elevated:hover {
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    transform: translateY(-4px);
}

/* Smooth Transitions */
.smooth-transition {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Enhanced Button Styles */
.btn-modern {
    background-color: transparent;
    color: var(--color-primary);
    padding: 0.875rem 1.75rem;
    border-radius: 0.75rem;
    font-weight: 600;
    font-size: 1rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 2px solid var(--color-primary);
    cursor: pointer;
    box-shadow: none;
}

.btn-modern:hover {
    background-color: var(--color-secondary);
    color: white;
    border-color: var(--color-secondary);
    transform: translateY(-2px);
}

.btn-modern:active {
    transform: translateY(0);
}

/* Badge Styles */
.badge-primary {
    background-color: var(--color-primary);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 600;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    display: inline-block;
}

.badge-accent {
    background-color: var(--color-accent);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 600;
}

/* Section Title Improvements */
.section-title {
    font-size: 2.5rem;
    font-weight: 600;
    line-height: 1.2;
    letter-spacing: -0.025em;
    color: #111827;
}

@media (min-width: 640px) {
    .section-title {
        font-size: 2.5rem;
    }
}

/* Card Hover Effects */
.card-hover {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid #e5e7eb;
}

.card-hover:hover {
    border-color: var(--color-primary);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    transform: translateY(-4px);
}

/* Image Overlay Improvements */
.image-overlay {
    background: linear-gradient(to top, rgba(0, 0, 0, 0.7) 0%, rgba(0, 0, 0, 0.3) 50%, transparent 100%);
    transition: opacity 0.3s ease;
}

/* Text Gradient Effect */
.text-gradient {
    background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-secondary) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* Icon Container with Background */
.icon-container {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 3rem;
    height: 3rem;
    border-radius: 0.75rem;
    background-color: var(--color-primary);
    color: white;
    transition: all 0.3s ease;
}

.icon-container:hover {
    background-color: var(--color-secondary);
    transform: scale(1.1) rotate(5deg);
}

/* Improved Spacing Utilities */
.section-padding {
    padding-top: 1rem;
    padding-bottom: 1rem;
}

@media (min-width: 768px) {
    .section-padding {
        padding-top: 1.5rem;
        padding-bottom: 1.5rem;
    }
}

/* Glassmorphism Effect */
.glass-effect {
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.18);
}

/* Animation Classes */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in-up {
    animation: fadeInUp 0.6s ease-out;
}

/* Utility: force icon color to white on dark backgrounds */
.on-dark i,
.on-dark .fa,
.on-dark [class^="fa-"],
.on-dark [class*=" fa-"] {
    color: white !important;
}

/* ========================== */
/* Global Border Color Styles */
/* ========================== */

/* Classe utilitaire pour la couleur de bordure personnalisée */
.border-custom { border-color: var(--color-border) !important; }

/* Appliquer la couleur de bordure aux éléments courants avec bordures */
.border,
[class*="border-gray-"],
[class*="border-slate-"],
[class*="border-zinc-"],
[class*="border-neutral-"],
[class*="border-stone-"] {
    border-color: var(--color-border) !important;
}

/* Bordures spécifiques aux composants */
.card-hover,
.rounded-lg[class*="border"],
.rounded-xl[class*="border"],
.rounded-2xl[class*="border"],
input[class*="border"],
select[class*="border"],
textarea[class*="border"],
.divide-gray-200 > *,
.divide-gray-300 > * {
    border-color: var(--color-border) !important;
}

/* Bordures de division */
[class*="divide-gray-"] > * + * {
    border-color: var(--color-border) !important;
}

/* Hover: garder la couleur primary pour hover */
.hover\:border-primary:hover,
.group:hover .group-hover\:border-primary {
    border-color: var(--color-primary) !important;
}

/* Card hover - bordure devient primary au survol */
.card-hover:hover {
    border-color: var(--color-primary) !important;
}

/* ========================== */
/* Icons with Border Color    */
/* ========================== */

/* Appliquer la couleur de bordure aux icônes */
.icon-border,
.fa-border {
    color: var(--color-border) !important;
}

/* Icônes dans les conteneurs gris/neutres */
.text-gray-400 i,
.text-gray-500 i,
.text-gray-600 i,
.text-slate-400 i,
.text-slate-500 i,
.text-slate-600 i,
i.text-gray-400,
i.text-gray-500,
i.text-gray-600,
i.text-slate-400,
i.text-slate-500,
i.text-slate-600,
.fa.text-gray-400,
.fa.text-gray-500,
.fa.text-gray-600,
[class*="fa-"].text-gray-400,
[class*="fa-"].text-gray-500,
[class*="fa-"].text-gray-600 {
    color: var(--color-border) !important;
}

/* Classe utilitaire pour forcer la couleur de bordure sur les icônes */
.icon-custom-color,
.icon-custom-color i,
.icon-custom-color .fa,
.icon-custom-color [class*="fa-"] {
    color: var(--color-border) !important;
}
</style>
