<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="x-apple-disable-message-reformatting">
    <title>{{ $appName ?? 'JodTod' }}</title>
</head>
<body style="margin:0; padding:0; background-color:#f4f7f6; font-family:-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color:#333333;">
    
    <!-- Preheader Text (Hidden but visible in email client previews) -->
    <div style="display:none; max-height:0; overflow:hidden; opacity:0; mso-hide:all;">
        Welcome to JodTod! Please verify your email to start tracking and managing your daily tasks.
    </div>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%; margin:0; padding:0; background-color:#f4f7f6;">
        <tr>
            <td align="center" style="padding:40px 16px;">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%; max-width:600px; margin:0 auto; background-color:#ffffff; border-radius:12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); overflow:hidden;">
                    
                    <!-- Header with Logo -->
                    <tr>
                        <td align="center" style="padding:30px 32px; background-color:#ffffff; border-bottom: 2px solid #f0f0f0;">
                            <img src="{{ asset('images/Logo/logo.png') }}" alt="{{ $appName ?? 'JodTod' }}" width="140" style="display:block; width:140px; max-width:140px; height:auto; border:0; outline:none; text-decoration:none;">
                        </td>
                    </tr>

                    <!-- Main Content -->
                    <tr>
                        <td style="padding:40px 32px 20px;">
                            <h1 style="margin:0 0 16px; color:#1e293b; font-size:24px; line-height:32px; font-weight:700; text-align:left;">
                                {{ $greeting ?? 'Welcome to JodTod!' }}
                            </h1>
                            <p style="margin:0 0 24px; color:#475569; font-size:16px; line-height:26px;">
                                Thank you for joining <strong>{{ $appName ?? 'JodTod' }}</strong>. We are thrilled to have you on board! To get started and keep your account secure, please verify your email address.
                            </p>

                            <!-- Call to Action Button -->
                            @if (!empty($actionText) && !empty($actionUrl))
                                <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin: 0 auto 32px auto;">
                                    <tr>
                                        <td align="center" style="border-radius:8px; background-color:#4f46e5;">
                                            <a href="{{ $actionUrl }}" target="_blank" style="display:inline-block; padding:14px 28px; color:#ffffff; font-size:16px; font-weight:600; text-decoration:none; border-radius:8px;">{{ $actionText }}</a>
                                        </td>
                                    </tr>
                                </table>
                            @endif

                            <p style="margin:0 0 24px; color:#475569; font-size:14px; line-height:22px; text-align:center;">
                                If you did not create a JodTod account, no further action is required.
                            </p>
                        </td>
                    </tr>

                    <!-- Features Section -->
                    <tr>
                        <td style="padding:0 32px 30px; background-color:#ffffff;">
                            <div style="border-top: 1px solid #e2e8f0; padding-top: 30px;">
                                <h2 style="margin:0 0 20px; color:#1e293b; font-size:18px; font-weight:600;">What can you do with JodTod?</h2>
                                
                                <!-- Feature 1 -->
                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:16px;">
                                    <tr>
                                        <td width="24" valign="top" style="padding-top:2px;">
                                            <span style="color:#4f46e5; font-size:20px;">✓</span>
                                        </td>
                                        <td style="padding-left:12px; color:#475569; font-size:15px; line-height:24px;">
                                            <strong>Track & Manage Tasks:</strong> Stay organized and boost your productivity effortlessly.
                                        </td>
                                    </tr>
                                </table>

                                <!-- Feature 2 -->
                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:16px;">
                                    <tr>
                                        <td width="24" valign="top" style="padding-top:2px;">
                                            <span style="color:#4f46e5; font-size:20px;">✓</span>
                                        </td>
                                        <td style="padding-left:12px; color:#475569; font-size:15px; line-height:24px;">
                                            <strong>Split Expenses:</strong> Easily divide bills and keep track of shared finances.
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </td>
                    </tr>

                    <!-- Future Modules / Coming Soon Section -->
                    <tr>
                        <td style="padding:24px 32px 32px; background-color:#f8fafc; border-top: 1px solid #e2e8f0;">
                            <h3 style="margin:0 0 12px; color:#334155; font-size:16px; font-weight:600; text-transform:uppercase; letter-spacing:1px;">🚀 Coming Soon</h3>
                            <p style="margin:0 0 12px; color:#64748b; font-size:15px; line-height:24px;">
                                We are constantly upgrading! In our upcoming updates, you will get access to:
                            </p>
                            <ul style="margin:0; padding-left:20px; color:#64748b; font-size:14px; line-height:24px;">
                                <li><strong>Medicine Reminders:</strong> Never miss a dose again.</li>
                                <li><strong>Prospect Follow-up Tracker:</strong> Seamlessly manage and follow up on your network marketing leads.</li>
                            </ul>
                        </td>
                    </tr>
                </table>

                <!-- Footer -->
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%; max-width:600px; margin:0 auto;">
                    <tr>
                        <td align="center" style="padding:24px 24px 0;">
                            <p style="margin:0 0 8px; color:#94a3b8; font-size:13px; line-height:20px;">
                                {{ $footerText ?? 'Need help? Contact our support team.' }}
                            </p>
                            <p style="margin:0; color:#94a3b8; font-size:12px; line-height:18px;">
                                &copy; {{ now()->year }} {{ $appName ?? 'JodTod' }}. All rights reserved.
                            </p>
                        </td>
                    </tr>
                </table>

            </td>
        </tr>
    </table>
</body>
</html>