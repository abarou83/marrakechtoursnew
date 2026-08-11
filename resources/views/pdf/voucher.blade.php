<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ __('Voucher') }} - {{ $booking->reference }}</title>
    <style>
        @page {
            margin: 20mm;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.5;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #C1440E;
            padding-bottom: 20px;
        }
        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #C1440E;
            margin-bottom: 5px;
        }
        .tagline {
            color: #666;
            font-size: 11px;
        }
        .voucher-title {
            text-align: center;
            font-size: 24px;
            color: #C1440E;
            margin: 20px 0;
            font-weight: bold;
        }
        .reference-box {
            background-color: #FFF5F0;
            border: 2px dashed #C1440E;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            margin-bottom: 25px;
        }
        .reference-label {
            font-size: 10px;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .reference-number {
            font-size: 22px;
            font-weight: bold;
            color: #C1440E;
            letter-spacing: 3px;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            background-color: #C1440E;
            color: white;
            padding: 8px 15px;
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 15px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #eee;
        }
        .info-table td:first-child {
            color: #666;
            width: 40%;
        }
        .info-table td:last-child {
            font-weight: 500;
        }
        .tour-name {
            font-size: 16px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }
        .important-box {
            background-color: #FEF3C7;
            border-left: 4px solid #F59E0B;
            padding: 15px;
            margin: 25px 0;
        }
        .important-title {
            font-weight: bold;
            color: #92400E;
            margin-bottom: 8px;
        }
        .important-box ul {
            margin: 0;
            padding-left: 20px;
        }
        .important-box li {
            margin-bottom: 5px;
            color: #78350F;
        }
        .qr-section {
            text-align: center;
            margin: 30px 0;
        }
        .qr-code {
            width: 100px;
            height: 100px;
            background-color: #f0f0f0;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #999;
            font-size: 10px;
        }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            text-align: center;
            color: #666;
            font-size: 10px;
        }
        .contact-info {
            margin-top: 10px;
        }
        .valid-stamp {
            position: absolute;
            top: 150px;
            right: 20px;
            transform: rotate(-15deg);
            border: 3px solid #22C55E;
            color: #22C55E;
            padding: 10px 20px;
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            opacity: 0.7;
        }
        .price-total {
            font-size: 18px;
            font-weight: bold;
            color: #C1440E;
        }
    </style>
</head>
<body>
    <div class="valid-stamp">{{ __('CONFIRMÉ') }}</div>

    <div class="header">
        <div class="logo">MarrakechTours</div>
        <div class="tagline">{{ __('Découvrez le Maroc authentique') }}</div>
    </div>

    <div class="voucher-title">{{ __('VOUCHER DE RÉSERVATION') }}</div>

    <div class="reference-box">
        <div class="reference-label">{{ __('Référence de réservation') }}</div>
        <div class="reference-number">{{ $booking->reference }}</div>
    </div>

    <div class="section">
        <div class="section-title">{{ __('Détails du tour') }}</div>
        <div class="tour-name">{{ $booking->tour->translate()?->title ?? $booking->tour->title }}</div>
        <table class="info-table">
            <tr>
                <td>{{ __('Date de l\'excursion') }}</td>
                <td>{{ \Carbon\Carbon::parse($booking->travel_date ?? $booking->booking_date)->translatedFormat('l d F Y') }}</td>
            </tr>
            @if($booking->tour->departure_time)
            <tr>
                <td>{{ __('Heure de départ') }}</td>
                <td>{{ $booking->tour->departure_time }}</td>
            </tr>
            @endif
            <tr>
                <td>{{ __('Durée') }}</td>
                <td>{{ $booking->tour->duration_formatted ?? __('Journée complète') }}</td>
            </tr>
            <tr>
                <td>{{ __('Point de départ') }}</td>
                <td>{{ $booking->tour->departure_point ?? __('Votre hôtel à Marrakech') }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">{{ __('Participants') }}</div>
        <table class="info-table">
            <tr>
                <td>{{ __('Adultes') }}</td>
                <td>{{ $booking->adults ?? 1 }}</td>
            </tr>
            @if(($booking->children ?? 0) > 0)
            <tr>
                <td>{{ __('Enfants (3-12 ans)') }}</td>
                <td>{{ $booking->children }}</td>
            </tr>
            @endif
            @if(($booking->infants ?? 0) > 0)
            <tr>
                <td>{{ __('Bébés (0-2 ans)') }}</td>
                <td>{{ $booking->infants }}</td>
            </tr>
            @endif
        </table>
    </div>

    <div class="section">
        <div class="section-title">{{ __('Informations client') }}</div>
        <table class="info-table">
            <tr>
                <td>{{ __('Nom') }}</td>
                <td>{{ $booking->customer_name }}</td>
            </tr>
            <tr>
                <td>{{ __('Email') }}</td>
                <td>{{ $booking->customer_email }}</td>
            </tr>
            @if($booking->customer_phone)
            <tr>
                <td>{{ __('Téléphone') }}</td>
                <td>{{ $booking->customer_phone }}</td>
            </tr>
            @endif
            @if($booking->special_requests)
            <tr>
                <td>{{ __('Demandes spéciales') }}</td>
                <td>{{ $booking->special_requests }}</td>
            </tr>
            @endif
        </table>
    </div>

    <div class="section">
        <div class="section-title">{{ __('Paiement') }}</div>
        <table class="info-table">
            <tr>
                <td>{{ __('Statut') }}</td>
                <td style="color: #22C55E; font-weight: bold;">{{ __('PAYÉ') }}</td>
            </tr>
            <tr>
                <td>{{ __('Montant total') }}</td>
                <td class="price-total">{{ number_format($booking->total_ttc ?? $booking->total_price, 2) }} €</td>
            </tr>
            <tr>
                <td>{{ __('Date de paiement') }}</td>
                <td>{{ $booking->confirmed_at ? $booking->confirmed_at->translatedFormat('d/m/Y H:i') : now()->translatedFormat('d/m/Y H:i') }}</td>
            </tr>
        </table>
    </div>

    <div class="important-box">
        <div class="important-title">{{ __('Informations importantes') }}</div>
        <ul>
            <li>{{ __('Présentez ce voucher (imprimé ou sur mobile) le jour du tour') }}</li>
            <li>{{ __('Soyez au point de rendez-vous 15 minutes avant l\'heure de départ') }}</li>
            <li>{{ __('Munissez-vous d\'une pièce d\'identité valide') }}</li>
            <li>{{ __('Prévoyez de l\'eau, des lunettes de soleil et de la crème solaire') }}</li>
        </ul>
    </div>

    <div class="footer">
        <strong>MarrakechTours</strong><br>
        <div class="contact-info">
            {{ __('Email') }}: contact@marrakechtours.net | {{ __('Téléphone') }}: +212 6 XX XX XX XX<br>
            {{ __('Site web') }}: www.marrakechtours.net
        </div>
        <br>
        <small>{{ __('Document généré le') }} {{ now()->translatedFormat('d/m/Y à H:i') }}</small>
    </div>
</body>
</html>
