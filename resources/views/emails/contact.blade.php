<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Nouveau message — AgriFish</title>
<style>
  body { font-family: Arial, sans-serif; background: #f8f6f0; margin: 0; padding: 20px; color: #1a2e1a; }
  .container { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
  .header { background: linear-gradient(135deg, #1B4332, #2D6A4F); padding: 32px 40px; }
  .header h1 { color: #fff; margin: 0; font-size: 22px; }
  .header span { color: #F4C542; }
  .body { padding: 32px 40px; }
  .field { margin-bottom: 20px; border-bottom: 1px solid #f0ebe0; padding-bottom: 16px; }
  .field:last-child { border-bottom: none; }
  .label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: #C9952A; margin-bottom: 4px; }
  .value { font-size: 15px; color: #1a2e1a; line-height: 1.6; }
  .message-box { background: #f8f6f0; border-left: 4px solid #2D6A4F; border-radius: 0 8px 8px 0; padding: 16px; }
  .footer { background: #1B4332; padding: 20px 40px; text-align: center; color: rgba(255,255,255,0.5); font-size: 12px; }
  .footer strong { color: #F4C542; }
</style>
</head>
<body>
<div class="container">
  <div class="header">
    <h1>Agri<span>Fish</span> — Nouveau message</h1>
  </div>
  <div class="body">
    <div class="field">
      <div class="label">Expéditeur</div>
      <div class="value"><strong>{{ $formData['first_name'] }} {{ $formData['last_name'] }}</strong></div>
    </div>
    <div class="field">
      <div class="label">Email</div>
      <div class="value"><a href="mailto:{{ $formData['email'] }}" style="color:#2D6A4F;">{{ $formData['email'] }}</a></div>
    </div>
    @if(!empty($formData['phone']))
    <div class="field">
      <div class="label">Téléphone</div>
      <div class="value">{{ $formData['phone'] }}</div>
    </div>
    @endif
    <div class="field">
      <div class="label">Sujet</div>
      <div class="value">{{ $formData['subject'] }}</div>
    </div>
    <div class="field">
      <div class="label">Message</div>
      <div class="message-box">
        <div class="value">{{ $formData['message'] }}</div>
      </div>
    </div>
  </div>
  <div class="footer">
    Message envoyé depuis le formulaire de contact de <strong>AgriFish</strong> — {{ now()->format('d/m/Y à H:i') }}
  </div>
</div>
</body>
</html>
