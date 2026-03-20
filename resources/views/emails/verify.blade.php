<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject }}</title>
</head>
<body style="margin: 0; padding: 0; background: #0a0a0f; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background: #0a0a0f;">
        <tr>
            <td align="center" style="padding: 40px 20px;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width: 560px;">

                    <!-- Header -->
                    <tr>
                        <td style="padding: 0 0 32px 0; text-align: center;">
                            <div style="display: inline-block; background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); border: 1px solid rgba(43, 220, 108, 0.2); border-radius: 16px; padding: 12px 28px;">
                                <span style="color: #2bdc6c; font-size: 24px; font-weight: 700; letter-spacing: 0.15em; text-transform: uppercase;">Atelier</span>
                            </div>
                        </td>
                    </tr>

                    <!-- Main Card -->
                    <tr>
                        <td style="background: linear-gradient(180deg, #14141f 0%, #0f0f18 100%); border: 1px solid rgba(255,255,255,0.08); border-radius: 24px; padding: 48px 40px;">

                            <!-- Icon -->
                            <div style="text-align: center; margin: 0 0 24px 0;">
                                <div style="display: inline-block; width: 64px; height: 64px; background: rgba(43, 220, 108, 0.1); border-radius: 50%; line-height: 64px; text-align: center;">
                                    <span style="font-size: 28px;">✉️</span>
                                </div>
                            </div>

                            <!-- Greeting -->
                            <h1 style="margin: 0 0 8px 0; font-size: 28px; font-weight: 600; color: #ffffff; line-height: 1.3; text-align: center;">
                                Verify your email.
                            </h1>

                            <p style="margin: 0 0 32px 0; font-size: 16px; color: rgba(255,255,255,0.6); line-height: 1.6; text-align: center;">
                                Hey {{ $userName }}, click the button below to verify your email address and unlock all features.
                            </p>

                            <!-- CTA Button -->
                            <table role="presentation" cellspacing="0" cellpadding="0" style="margin: 0 auto 32px auto;">
                                <tr>
                                    <td style="border-radius: 12px; background: #2bdc6c; overflow: hidden;">
                                        <a href="{{ $verifyUrl }}" style="display: inline-block; padding: 16px 32px; color: #0a0a0f; font-size: 15px; font-weight: 600; text-decoration: none; border-radius: 12px;">
                                            Verify Email →
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Expiry Notice -->
                            <div style="text-align: center; margin: 0 0 32px 0;">
                                <p style="margin: 0; font-size: 13px; color: rgba(255,255,255,0.4);">
                                    This link expires in 60 minutes.
                                </p>
                            </div>

                            <!-- Divider -->
                            <div style="height: 1px; background: rgba(255,255,255,0.06); margin: 0 0 32px 0;"></div>

                            <!-- Footer Text -->
                            <p style="margin: 0; font-size: 13px; color: rgba(255,255,255,0.4); line-height: 1.6; text-align: center;">
                                If you did not create an account, no further action is required.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding: 32px 0 0 0; text-align: center;">
                            <p style="margin: 0; font-size: 12px; color: rgba(255,255,255,0.3); line-height: 1.6;">
                                Atelier — A commission platform built by artists, for artists
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
