<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Subscription</title>
    <style>
        body { font-family: sans-serif; background-color: #f9fafb; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 40px auto; padding: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div style="background: white; border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #f3f4f6; padding: 40px; text-align: center;">

            <!-- Icon (Mail Check) -->
            <div style="width: 80px; height: 80px; margin: 0 auto 24px; background: #fff7ed; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <svg style="height: 40px; width: 40px; color: #f97316;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>

            <!-- Heading -->
            <h1 style="font-size: 24px; font-weight: bold; color: #111827; margin-bottom: 12px;">Please Verify Your Email</h1>

            <!-- Body Copy -->
            <p style="color: #4b5563; font-size: 16px; line-height: 1.5; margin-bottom: 24px;">
                Thank you for subscribing to the Slough SEND Parent Carer Forum newsletter. To prevent spam and ensure you really want to receive our emails, we need you to verify your email address.
            </p>

            <!-- Actions -->
            <a href="{{ url('/newsletter/verify/' . $token) }}" style="display: inline-block; text-decoration: none; padding: 12px 24px; border: none; color: white; background: #f97316; border-radius: 9999px; font-weight: 600; font-size: 16px;">
                Verify My Email Address
            </a>

            <!-- Info Box -->
            <div style="background: #f9fafb; border: 1px solid #f3f4f6; border-radius: 12px; padding: 20px; margin-top: 32px; text-align: left;">
                <p style="font-size: 14px; color: #4b5563; margin: 0;">If you did not request this subscription, you can safely ignore this email and you will not be added to our mailing list.</p>
            </div>
        </div>

        <p style="text-align: center; font-size: 12px; color: #9ca3af; margin-top: 24px;">
            Slough SEND Parent Carer Forum &copy; {{ date('Y') }}
        </p>
    </div>
</body>
</html>