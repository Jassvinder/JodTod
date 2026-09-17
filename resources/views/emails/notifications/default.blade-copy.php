<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="x-apple-disable-message-reformatting">
    <title>{{ $appName }}</title>
</head>

<body style="margin:0; padding:0; background-color:#f5f7fb; font-family:Arial, Helvetica, sans-serif; color:#1e293b;">
    <div style="display:none; max-height:0; overflow:hidden; opacity:0; mso-hide:all;">{{ $greeting }}</div>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%; margin:0; padding:0; background-color:#f5f7fb;">
        <tr>
            <td align="center" style="padding:32px 16px;">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%; max-width:600px; margin:0 auto;">
                    <tr>
                        <td align="center" style="padding:0 0 20px;">
                            <img src="{{ asset('images/Logo/logo.webp') }}" alt="{{ $appName }}" width="116" style="display:block; width:116px; max-width:116px; height:auto; border:0; outline:none; text-decoration:none;">
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color:#ffffff; border:1px solid #e2e8f0; border-radius:16px; overflow:hidden;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td style="padding:32px 32px 8px;">
                                        <h1 style="margin:0; color:#172554; font-size:26px; line-height:34px; font-weight:700;">{{ $greeting }}</h1>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:16px 32px 8px;">
                                        @foreach ($contentLines as $line)
                                        <p style="margin:0 0 16px; color:#475569; font-size:16px; line-height:25px;">{{ $line }}</p>
                                        @endforeach
                                    </td>
                                </tr>
                                @if (!empty($actionText) && !empty($actionUrl))
                                <tr>
                                    <td style="padding:8px 32px 32px;">
                                        @if (!empty($actionInstruction))
                                        <p style="margin:0 0 18px; color:#334155; font-size:16px; line-height:24px;">{{ $actionInstruction }}</p>
                                        @endif
                                        <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                                            <tr>
                                                <td align="center" bgcolor="#4f46e5" style="border-radius:8px; background-color:#4f46e5;">
                                                    <a href="{{ $actionUrl }}" target="_blank" style="display:inline-block; padding:13px 22px; color:#ffffff; font-size:15px; line-height:20px; font-weight:700; text-decoration:none; border-radius:8px;">{{ $actionText }}</a>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                @endif
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:20px 24px 0; text-align:center;">
                            <p style="margin:0; color:#64748b; font-size:13px; line-height:20px;">{{ $footerText }}</p>
                            <p style="margin:8px 0 0; color:#94a3b8; font-size:12px; line-height:18px;">&copy; {{ now()->year }} {{ $appName }}. All rights reserved.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>