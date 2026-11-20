<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:24px;background:#f5f5f5;font-family:Arial,Helvetica,sans-serif;color:#17181b;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:640px;margin:0 auto;background:#ffffff;border:1px solid #dedfe0;border-radius:4px;">
    <tr>
        <td style="padding:24px 28px;border-bottom:1px solid #dedfe0;">
            <h1 style="margin:0;color:#C32922;font-size:22px;">{{ $asunto }}</h1>
            <p style="margin:6px 0 0;font-size:13px;color:#777;">Enviado desde la web el {{ now()->format('d/m/Y H:i') }}</p>
        </td>
    </tr>
    <tr>
        <td style="padding:12px 28px 24px;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size:14px;line-height:1.5;">
                @foreach ($campos as $campo => $valor)
                    @continue($valor === null || $valor === '')
                    <tr>
                        <td style="padding:8px 12px 8px 0;border-bottom:1px solid #eee;color:#555;vertical-align:top;width:40%;">{{ $campo }}</td>
                        <td style="padding:8px 0;border-bottom:1px solid #eee;white-space:pre-line;">{{ $valor }}</td>
                    </tr>
                @endforeach
            </table>
        </td>
    </tr>
</table>
</body>
</html>
