<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>You are now an Admin</title>
    <style>
        body { font-family: sans-serif; background-color: #f9fafb; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 40px auto; padding: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div style="background: white; border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #f3f4f6; padding: 40px; text-align: center;">

            <!-- Icon (Key/Crown) -->
            <div style="width: 80px; height: 80px; margin: 0 auto 24px; background: #f2f3f8; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <svg style="height: 40px; width: 40px; color: #2b3a7a;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <!-- Using a Key icon to represent master access -->
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                </svg>
            </div>

            <!-- Heading -->
            <h1 style="font-size: 24px; font-weight: bold; color: #111827; margin-bottom: 12px;">You're now an Admin!</h1>

            <!-- Body Copy -->
            <p style="color: #4b5563; font-size: 16px; line-height: 1.5; margin-bottom: 24px;">
                Congratulations! You have been granted Administrator access to the Slough SEND Parent Carer Forum. You now have full control over the platform.
            </p>

            <!-- Info Box -->
            <div style="background: #f2f3f8; border: 1px solid #e6e8f1; border-radius: 12px; padding: 20px; margin-bottom: 32px; text-align: left;">
                <div style="display: flex; align-items: flex-start;">
                    <svg style="height: 20px; width: 20px; color: #2b3a7a; margin-right: 12px; margin-top: 2px; flex-shrink: 0;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <p style="font-size: 14px; color: #4b5563; margin: 0;">As an Administrator, you can manage user roles, approve applications, oversee all community content, and configure platform settings.</p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <a href="{{ url('/admin/dashboard') }}" style="display: inline-block; text-decoration: none; padding: 12px 24px; border: 1px solid transparent; color: white; background: #2b3a7a; border-radius: 9999px; font-weight: 500;">
                Go to Dashboard
            </a>
        </div>

        <p style="text-align: center; font-size: 12px; color: #9ca3af; margin-top: 24px;">
            Slough SEND Parent Carer Forum &copy; {{ date('Y') }}
        </p>
    </div>
</body>
</html>