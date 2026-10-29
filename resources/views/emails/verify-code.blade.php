<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Email Verification Code</title>
</head>
<body style="font-family: sans-serif; background-color: #f9fafb; margin: 0; padding: 0;">
    <div style="max-width: 600px; margin: 40px auto; padding: 20px;">
        <div style="background: white; border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #f3f4f6; padding: 40px; text-align: center;">

            <!-- Icon -->
            <div style="width: 80px; height: 80px; margin: 0 auto 24px; background: #f2f3f8; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <svg style="height: 40px; width: 40px; color: #2b3a7a;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </div>

            <!-- Heading -->
            <h1 style="font-size: 24px; font-weight: bold; color: #111827; margin-bottom: 12px;">Verify Your Email Address</h1>

            <!-- Body Copy -->
            <p style="color: #4b5563; font-size: 16px; line-height: 1.5; margin-bottom: 24px;">
                Hi {{ $user->name }},<br><br>
                Thank you for starting your application to join the Slough SEND Parent Carer Forum. To confirm your email address and submit your application for review, please use the verification code below.
            </p>

            <!-- Code Box -->
            <div style="display: inline-block; padding: 20px 48px; background: #f2f3f8; border-radius: 12px; margin-bottom: 24px;">
                <span style="font-size: 36px; font-weight: bold; color: #2b3a7a; letter-spacing: 10px;">{{ $code }}</span>
            </div>

            <!-- Info Box -->
            <div style="background: #f9fafb; border: 1px solid #f3f4f6; border-radius: 12px; padding: 20px; margin-bottom: 32px; text-align: left;">
                <div style="display: flex; align-items: flex-start;">
                    <svg style="height: 20px; width: 20px; color: #2b3a7a; margin-right: 12px; margin-top: 2px; flex-shrink: 0;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <p style="font-size: 14px; color: #4b5563; margin: 0 0 8px 0;">This code will expire in 30 minutes.</p>
                        <p style="font-size: 14px; color: #4b5563; margin: 0;">If you didn't create an account, you can safely ignore this email.</p>
                    </div>
                </div>
            </div>
        </div>

        <p style="text-align: center; font-size: 12px; color: #9ca3af; margin-top: 24px;">
            Slough SEND Parent Carer Forum &copy; {{ date('Y') }}
        </p>
    </div>
</body>
</html>
