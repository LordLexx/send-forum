<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Contact Submission</title>
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

            <!-- Icon (Envelope) -->
            <div style="width: 80px; height: 80px; margin: 0 auto 24px; background: #fff7ed; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <svg style="height: 40px; width: 40px; color: #f97316;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </div>

            <!-- Heading -->
            <h1 style="font-size: 24px; font-weight: bold; color: #111827; margin-bottom: 12px;">New Contact Submission</h1>

            <!-- Body Copy -->
            <p style="color: #4b5563; font-size: 16px; line-height: 1.5; margin-bottom: 24px;">
                You've received a new enquiry through the website contact form.
            </p>

            <!-- Submission Details Box -->
            <div style="background: #f9fafb; border: 1px solid #f3f4f6; border-radius: 12px; padding: 24px; margin-bottom: 32px; text-align: left;">
                <table style="width: 100%; font-size: 14px; color: #4b5563; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 6px 0; font-weight: 600; color: #1f2937; width: 90px; vertical-align: top;">Name</td>
                        <td style="padding: 6px 0; vertical-align: top;">{{ $submission->name }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 6px 0; font-weight: 600; color: #1f2937; vertical-align: top;">Email</td>
                        <td style="padding: 6px 0; vertical-align: top;"><a href="mailto:{{ $submission->email }}" style="color: #f97316; text-decoration: none;">{{ $submission->email }}</a></td>
                    </tr>
                    <tr>
                        <td style="padding: 6px 0; font-weight: 600; color: #1f2937; vertical-align: top;">Topic</td>
                        <td style="padding: 6px 0; vertical-align: top;">{{ $submission->topic_label }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 6px 0; font-weight: 600; color: #1f2937; vertical-align: top;">Subject</td>
                        <td style="padding: 6px 0; vertical-align: top;">{{ $submission->subject }}</td>
                    </tr>
                </table>

                <div style="border-top: 1px solid #e5e7eb; margin: 16px 0;"></div>

                <p style="font-size: 12px; font-weight: 600; color: #1f2937; margin: 0 0 8px 0; text-transform: uppercase; letter-spacing: 0.05em;">Message</p>
                <p style="font-size: 14px; color: #4b5563; margin: 0; line-height: 1.6; white-space: pre-wrap;">{{ $submission->message }}</p>
            </div>

            <!-- Action Button -->
            <a href="{{ url('/admin/contact-submissions/' . $submission->id) }}" style="display: inline-block; text-decoration: none; padding: 12px 28px; background: #f97316; color: white; border-radius: 9999px; font-weight: 600; font-size: 14px;">
                View in Admin Panel
            </a>
        </div>

        <p style="text-align: center; font-size: 12px; color: #9ca3af; margin-top: 24px;">
            Slough SEND Parent Carer Forum &copy; {{ date('Y') }}
        </p>
    </div>
</body>
</html>
