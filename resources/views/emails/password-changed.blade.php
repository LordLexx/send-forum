<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Changed</title>
    <style>
        body { font-family: sans-serif; background-color: #f9fafb; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 40px auto; padding: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div style="background: white; border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #f3f4f6; padding: 40px; text-align: center;">

            <!-- Icon (Shield Check) -->
            <div style="width: 80px; height: 80px; margin: 0 auto 24px; background: #f2f3f8; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <svg style="height: 40px; width: 40px; color: #2b3a7a;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </div>

            <!-- Heading -->
            <h1 style="font-size: 24px; font-weight: bold; color: #111827; margin-bottom: 12px;">Password Successfully Reset</h1>

            <!-- Body Copy -->
            <p style="color: #4b5563; font-size: 16px; line-height: 1.5; margin-bottom: 24px;">
                Your password has been successfully reset. You can now log in to your account using your new password.
            </p>

            <!-- Info Box -->
            <div style="background: #fef2f2; border: 1px solid #fee2e2; border-radius: 12px; padding: 20px; margin-bottom: 32px; text-align: left;">
                <div style="display: flex; align-items: flex-start;">
                    <svg style="height: 20px; width: 20px; color: #ef4444; margin-right: 12px; margin-top: 2px; flex-shrink: 0;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <div>
                        <p style="font-size: 14px; color: #4b5563; margin: 0;">If you did not make this change, please contact our administrative team immediately.</p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <a href="{{ url('/login') }}" style="display: inline-block; text-decoration: none; padding: 12px 24px; border: 1px solid transparent; color: white; background: #f97316; border-radius: 9999px; font-weight: 500;">
                Log In to Forum
            </a>
        </div>

        <p style="text-align: center; font-size: 12px; color: #9ca3af; margin-top: 24px;">
            Slough SEND Parent Carer Forum &copy; {{ date('Y') }}
        </p>
    </div>
</body>
</html>