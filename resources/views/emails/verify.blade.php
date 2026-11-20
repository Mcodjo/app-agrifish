<!doctype html>
<html lang="fr">
<body style="margin:0;background:#f5f7f4;font-family:Arial,Helvetica,sans-serif;color:#14231b">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f5f7f4;padding:24px 12px">
<tr><td align="center">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;background:#ffffff;border:1px solid #dbe3dd;border-radius:12px">
<tr><td style="height:6px;background:linear-gradient(90deg,#0b6b43 33%,#e9b21a 33% 66%,#c4362c 66%);border-radius:12px 12px 0 0"></td></tr>
<tr><td style="padding:28px">
    <p style="margin:0 0 6px;font-size:13px;font-weight:bold;letter-spacing:1px;text-transform:uppercase;color:#0b6b43">Pétition citoyenne · Porto-Novo</p>
    <h1 style="margin:0 0 16px;font-size:22px;line-height:1.25">Bonjour {{ $signature->first_name }}, confirmez votre signature</h1>
    <p style="margin:0 0 20px;line-height:1.6">Merci de soutenir la pétition « {{ config('petition.title') }} ». Cliquez sur le bouton ci-dessous pour que votre signature soit comptée.</p>
    <p style="margin:0 0 24px"><a href="{{ $url }}" style="display:inline-block;background:#0b6b43;color:#ffffff;text-decoration:none;font-weight:bold;padding:13px 20px;border-radius:8px">Confirmer ma signature</a></p>
    <p style="margin:0 0 8px;font-size:13px;color:#55655b;line-height:1.5">Ce lien est valable {{ config('petition.verification_days') }} jours. Si vous n’avez pas signé cette pétition, ignorez simplement cet e-mail.</p>
    <p style="margin:0;font-size:12px;color:#55655b;word-break:break-all">{{ $url }}</p>
</td></tr>
</table>
</td></tr>
</table>
</body>
</html>
