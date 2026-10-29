<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reply to Your Enquiry</title>
</head>
<body style="font-family: 'Open Sans', Arial, sans-serif; background-color: #f9fafb; margin: 0; padding: 0;">
    <div style="max-width: 600px; margin: 40px auto; padding: 20px;">

        <!-- Brand header -->
        <div style="text-align: center; margin-bottom: 20px;">
            <p style="font-size: 13px; font-weight: 700; color: #6b7280; letter-spacing: 0.08em; text-transform: uppercase; margin: 0;">
                Slough SEND Parent Carer Forum
            </p>
        </div>

        <div style="background: white; border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #f3f4f6; padding: 40px; text-align: center;">

            <!-- Icon (Chat Bubble) -->
            <div style="width: 80px; height: 80px; margin: 0 auto 24px; background: #fff7ed; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <svg style="height: 40px; width: 40px; color: #f97316;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
            </div>

            <!-- Heading -->
            <h1 style="font-size: 24px; font-weight: bold; color: #111827; margin-bottom: 12px;">Reply to Your Enquiry</h1>

            <!-- Body Copy -->
            <p style="color: #4b5563; font-size: 16px; line-height: 1.5; margin-bottom: 24px;">
                Hi {{ $submission->name }}, thank you for getting in touch with Slough SEND Parent Carer Forum. Here's our response to your enquiry.
            </p>

            <!-- Original Subject Reference -->
            <div style="background: #f9fafb; border: 1px solid #f3f4f6; border-radius: 12px; padding: 20px; margin-bottom: 20px; text-align: left;">
                <p style="font-size: 12px; font-weight: 600; color: #1f2937; margin: 0 0 6px 0; text-transform: uppercase; letter-spacing: 0.05em;">Your Enquiry</p>
                <p style="font-size: 14px; color: #4b5563; margin: 0;">{{ $submission->subject }}</p>
            </div>

            <!-- Reply Message -->
            <div style="background: #fff7ed; border: 1px solid #fed7aa; border-radius: 12px; padding: 24px; margin-bottom: 32px; text-align: left;">
                <p style="font-size: 12px; font-weight: 600; color: #9a3412; margin: 0 0 12px 0; text-transform: uppercase; letter-spacing: 0.05em;">Our Reply</p>
                <p style="font-size: 15px; color: #1f2937; margin: 0; line-height: 1.6; white-space: pre-wrap;">{{ $replyMessage }}</p>
            </div>

            <!-- Info Box -->
            <div style="background: #f9fafb; border: 1px solid #f3f4f6; border-radius: 12px; padding: 20px; margin-bottom: 32px; text-align: left;">
                <p style="font-size: 14px; color: #4b5563; margin: 0 0 8px 0;">If you have further questions, simply reply to this email and our team will get back to you.</p>
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
