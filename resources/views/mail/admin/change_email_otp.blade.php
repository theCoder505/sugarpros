<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Your OTP Code</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f6f9fc; margin: 0; padding: 0;">
    <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; margin-top: 40px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <tr>
                        <td style="padding: 40px 30px 20px 30px; text-align: center;">
                            <h2 style="color: #1C3B6F;">🔐 Email Change Verification (OTP)</h2>
                            <p style="font-size: 16px; color: #333;">
                                Dear SugarPros Admin,<br>
                                You have requested to change your account email address from the account settings page of {{ $brandname }}.<br>
                                Please use the following 6-digit OTP to verify your request and complete the email change process.
                            </p>
                            <div style="margin: 30px 0;">
                                <span style="display: inline-block; background-color: #1C3B6F; color: #ffffff; font-size: 28px; font-weight: bold; padding: 15px 30px; border-radius: 6px; letter-spacing: 4px;">
                                    {{ $OTP }}
                                </span>
                            </div>
                            <p style="font-size: 14px; color: #999;">
                                If you did not request to change your email address, please ignore this email.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 20px 30px 40px 30px; text-align: center; font-size: 12px; color: #bbb;">
                            &copy; {{ date('Y') }} {{ $brandname }}. All rights reserved.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
