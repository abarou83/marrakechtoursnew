<x-app-layout>
    @push('styles')
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- intl-tel-input CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@19.5.6/build/css/intlTelInput.css">
    <style>
        .text-primary { color: {{ primary_color() }}; }
        .bg-primary { background-color: {{ primary_color() }}; }
        .border-primary { border-color: {{ primary_color() }}; }
        .font-poppins { font-family: 'Poppins', sans-serif; }
        
        /* intl-tel-input custom styles */
        .iti {
            width: 100%;
        }
        .iti__flag-container {
            z-index: 10;
        }
        .iti__selected-flag {
            padding: 0 12px 0 16px;
            border-right: 1px solid #d1d5db;
        }
        .iti__selected-flag:hover {
            background-color: #f9fafb;
        }
        .iti__country-list {
            z-index: 50;
            max-height: 200px;
            overflow-y: auto;
        }
        #phone {
            padding-left: 60px;
        }
    </style>
    @endpush

    <div class="bg-[#f8fbfd] min-h-screen py-8 md:py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="font-poppins text-3xl md:text-4xl font-bold text-gray-900 mb-2">
                    Mon Profil
                </h1>
                <p class="text-gray-600">Gérez vos informations personnelles et vos paramètres de compte</p>
            </div>

            <div class="space-y-6">
                <!-- Profile Information -->
                <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 md:p-8">
                    <div class="mb-6">
                        <h2 class="font-poppins text-2xl font-bold text-gray-900 mb-2 flex items-center">
                            <i class="fas fa-user-circle mr-3 text-primary"></i>
                            Informations du profil
                        </h2>
                        <p class="text-sm text-gray-600">Mettez à jour les informations de votre compte et votre adresse email.</p>
                    </div>

                    @include('profile.partials.update-profile-information-form')
                </div>

                <!-- Update Password -->
                <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 md:p-8">
                    <div class="mb-6">
                        <h2 class="font-poppins text-2xl font-bold text-gray-900 mb-2 flex items-center">
                            <i class="fas fa-lock mr-3 text-primary"></i>
                            Modifier le mot de passe
                        </h2>
                        <p class="text-sm text-gray-600">Assurez-vous que votre compte utilise un mot de passe long et aléatoire pour rester sécurisé.</p>
                    </div>

                    @include('profile.partials.update-password-form')
                </div>

                <!-- Delete Account -->
                <div class="bg-white rounded-xl shadow-lg border border-red-200 p-6 md:p-8">
                    <div class="mb-6">
                        <h2 class="font-poppins text-2xl font-bold text-red-900 mb-2 flex items-center">
                            <i class="fas fa-exclamation-triangle mr-3 text-red-600"></i>
                            Supprimer le compte
                        </h2>
                        <p class="text-sm text-gray-600">
                            Une fois votre compte supprimé, toutes ses ressources et données seront définitivement supprimées. Avant de supprimer votre compte, veuillez télécharger toutes les données ou informations que vous souhaitez conserver.
                        </p>
                    </div>

                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <!-- intl-tel-input JS -->
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@19.5.6/build/js/intlTelInput.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const phoneInput = document.getElementById('phone');
            if (phoneInput) {
                const iti = window.intlTelInput(phoneInput, {
                    initialCountry: "fr", // Pays par défaut (France)
                    preferredCountries: ["fr", "ma", "dz", "tn", "be", "ch", "ca", "us", "gb"],
                    separateDialCode: true,
                    utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@19.5.6/build/js/utils.js",
                    nationalMode: false,
                    formatOnDisplay: true,
                });

                // Mettre à jour le champ hidden avec le code pays
                phoneInput.addEventListener('countrychange', function() {
                    const countryData = iti.getSelectedCountryData();
                    const countryCodeInput = document.getElementById('phone_country_code');
                    if (countryCodeInput) {
                        countryCodeInput.value = '+' + countryData.dialCode;
                    }
                });

                // Initialiser le code pays au chargement
                const countryData = iti.getSelectedCountryData();
                const countryCodeInput = document.getElementById('phone_country_code');
                if (countryCodeInput) {
                    countryCodeInput.value = '+' + countryData.dialCode;
                }

                // Si le numéro existe déjà, formater avec le code pays
                if (phoneInput.value) {
                    const currentValue = phoneInput.value;
                    // Si le numéro commence déjà par +, le formater correctement
                    if (currentValue.startsWith('+')) {
                        iti.setNumber(currentValue);
                    } else {
                        // Sinon, utiliser le code pays par défaut
                        const countryData = iti.getSelectedCountryData();
                        phoneInput.value = '+' + countryData.dialCode + ' ' + currentValue;
                    }
                }
            }
        });
    </script>
    @endpush
</x-app-layout>
