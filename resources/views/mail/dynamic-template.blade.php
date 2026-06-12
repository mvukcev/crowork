@php
    $body = $body ?? '';
    $lines = preg_split('/\r\n|\r|\n/', trim($body)) ?: [];

    $code = null;
    foreach ($lines as $line) {
        if (preg_match('/\b(\d{6})\b/', $line, $matches) === 1) {
            $code = $matches[1];
            break;
        }
    }
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'CroWork') }}</title>
</head>
<body style="margin: 0; padding: 0; background: #f1f5f9; color: #0f172a; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Helvetica, Arial, sans-serif;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background: #f1f5f9; padding: 28px 14px;">
    <tr>
        <td align="center">
            <table role="presentation" width="620" cellpadding="0" cellspacing="0" style="width: 100%; max-width: 620px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; overflow: hidden;">
                <tr>
                    <td style="padding: 22px 28px; border-bottom: 1px solid #e2e8f0; background: linear-gradient(135deg, #f8fafc 0%, #eef2ff 100%);">
                        <p style="margin: 0; font-size: 13px; line-height: 1; color: #334155; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase;">CroWork</p>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 28px;">
                        <div style="font-size: 16px; line-height: 1.72; color: #1e293b;">
                            {!! nl2br(e($body)) !!}
                        </div>

                        @if ($code)
                            <div style="margin: 24px 0 10px; padding: 18px 16px; border-radius: 12px; border: 1px dashed #cbd5e1; background: #f8fafc; text-align: center;">
                                <p style="margin: 0 0 8px; font-size: 12px; color: #64748b; letter-spacing: 0.06em; text-transform: uppercase; font-weight: 600;">Verifikacijski kod</p>
                                <p style="margin: 0; font-size: 34px; line-height: 1.1; letter-spacing: 0.22em; font-weight: 700; color: #0f172a; font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace;">{{ $code }}</p>
                            </div>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="padding: 0 28px 24px;">
                        <p style="margin: 0; font-size: 12px; line-height: 1.6; color: #64748b;">Ova poruka poslana je automatski iz sustava CroWork.</p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
