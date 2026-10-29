<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>We've Received Your Enquiry</title>
</head>
<body style="font-family: 'Open Sans', Arial, sans-serif; background-color: #f9fafb; margin: 0; padding: 0;">
    <div style="max-width: 600px; margin: 40px auto; padding: 20px;">

        <div style="text-align: center; margin-bottom: 20px;">
            <p style="font-size: 13px; font-weight: 700; color: #6b7280; letter-spacing: 0.08em; text-transform: uppercase; margin: 0;">
                Slough SEND Parent Carer Forum
            </p>
        </div>

        <div style="background: white; border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #f3f4f6; padding: 40px; text-align: center;">

            <!-- Icon (Check) -->
            <div style="width: 80px; height: 80px; margin: 0 auto 24px; background: #ecfdf5; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <svg style="height: 40px; width: 40px; color: #10b981;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>

            <!-- Heading -->
            <h1 style="font-size: 24px; font-weight: bold; color: #111827; margin-bottom: 12px;">Thanks for Verifying!</h1>

            <!-- Body Copy -->
            <p style="color: #4b5563; font-size: 16px; line-height: 1.5; margin-bottom: 24px;">
                Hi {{ $submission->name }}, your email has been verified and your enquiry has been sent to our team. We'll get back to you within 1–2 working days.
            </p>

            <!-- Info Box -->
            <div style="background: #f9fafb; border: 1px solid #f3f4f6; border-radius: 12px; padding: 20px; margin-bottom: 20px; text-align: left;">
                <p style="font-size: 12px; font-weight: 600; color: #1f2937; margin: 0 0 6px 0; text-transform: uppercase; letter-spacing: 0.05em;">Your Enquiry</p>
                <p style="font-size: 14px; color: #4b5563; margin: 0;">{{ $submission->subject }}</p>
            </div>

            <!-- Info Box 2 -->
            <div style="background: #f9fafb; border: 1px solid #f3f4f6; border-radius: 12px; padding: 20px; margin-bottom: 32px; text-align: left;">
                <p style="font-size: 14px; color: #4b5563; margin: 0 0 8px 0;">If you have any additional information to add, simply reply to this email.</p>
                <p style="font-size: 14px; font-weight: 600; color: #1f2937; margin: 0;">We typically respond within 1–2 working days.</p>
            </div>

            <!-- Action Button -->
            <a href="{{ url('/') }}" style="display: inline-block; text-decoration: none; padding: 10px 20px; border: 1px solid #e5e7eb; color: #374151; background: white; border-radius: 9999px; font-weight: 500;">
                Visit Website
            </a>
        </div>

        <p style="text-align: center; font-size: 12px; color: #9ca3af; margin-top: 24px;">
            Slough SEND Parent Carer Forum &copy; {{ date('Y') }}
        </p>
    </div>
</body>
</html>
