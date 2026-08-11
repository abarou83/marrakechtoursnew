<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Nouveau message de contact</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <h2 style="color: #111;">Nouveau message de contact</h2>

    <p><strong>Nom :</strong> {{ $data['name'] }}</p>
    <p><strong>Email :</strong> {{ $data['email'] }}</p>
    @if(!empty($data['phone']))
        <p><strong>Téléphone :</strong> {{ $data['phone'] }}</p>
    @endif
    <p><strong>Sujet :</strong> {{ $data['subject'] }}</p>

    <hr style="border: none; border-top: 1px solid #eee; margin: 20px 0;">

    <p><strong>Message :</strong></p>
    <p style="white-space: pre-wrap;">{{ $data['message'] }}</p>
</body>
</html>
