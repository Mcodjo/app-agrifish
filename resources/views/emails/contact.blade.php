<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Nouvelle demande de contact</title>
</head>
<body style="margin:0;padding:24px;background:#f4f2ec;font-family:Arial,Helvetica,sans-serif;color:#17211b;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:600px;margin:0 auto;background:#ffffff;border:1px solid #e4e0d6;border-radius:12px;">
  <tr>
    <td style="padding:24px 32px;border-bottom:1px solid #e4e0d6;">
      <p style="margin:0;font-size:13px;color:#5c665f;">Formulaire de contact du site</p>
      <h1 style="margin:6px 0 0;font-size:20px;color:#17211b;">{{ $formData['subject'] }}</h1>
    </td>
  </tr>
  <tr>
    <td style="padding:24px 32px;font-size:15px;line-height:1.6;">
      <p style="margin:0 0 4px;color:#5c665f;font-size:13px;">Expéditeur</p>
      <p style="margin:0 0 16px;"><strong>{{ $formData['first_name'] }} {{ $formData['last_name'] }}</strong>@if(!empty($formData['organisation'])), {{ $formData['organisation'] }}@endif</p>

      <p style="margin:0 0 4px;color:#5c665f;font-size:13px;">E-mail</p>
      <p style="margin:0 0 16px;"><a href="mailto:{{ $formData['email'] }}" style="color:#1b4332;">{{ $formData['email'] }}</a></p>

      @if(!empty($formData['phone']))
        <p style="margin:0 0 4px;color:#5c665f;font-size:13px;">Téléphone</p>
        <p style="margin:0 0 16px;">{{ $formData['phone'] }}</p>
      @endif

      <p style="margin:0 0 4px;color:#5c665f;font-size:13px;">Message</p>
      <p style="margin:0;padding:16px;background:#f4f2ec;border-radius:8px;white-space:pre-line;">{{ $formData['message'] }}</p>
    </td>
  </tr>
  <tr>
    <td style="padding:16px 32px;border-top:1px solid #e4e0d6;font-size:12px;color:#5c665f;">
      Reçu le {{ now()->format('d/m/Y à H:i') }}. Répondre directement à cet e-mail pour écrire à l’expéditeur.
    </td>
  </tr>
</table>
</body>
</html>
