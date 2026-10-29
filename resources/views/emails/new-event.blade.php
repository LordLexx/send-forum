<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- No <title> tag — some email clients render it as an ugly heading at the top --}}
    <style>
        body { font-family: sans-serif; background-color: #f9fafb; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 40px auto; padding: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div style="background: white; border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #f3f4f6; overflow: hidden;">

            {{-- Event Image Banner (uploaded image OR default Unsplash image) --}}
            @php
                $imageUrl = $event->image
                    ? asset('storage/' . $event->image)
                    : 'https://images.unsplash.com/photo-1521017432531-fbd92d768814?q=80&w=1200&auto=format&fit=crop';
            @endphp
            <img src="{{ $imageUrl }}" alt="{{ $event->title }}" style="width: 100%; height: 240px; object-fit: cover; display: block;">

            <div style="padding: 40px; text-align: center;">

                {{-- Icon (Calendar) --}}
                <div style="width: 80px; height: 80px; margin: 0 auto 24px; background: #f2f3f8; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                    <svg style="height: 40px; width: 40px; color: #2b3a7a;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>

                {{-- Heading --}}
                <h1 style="font-size: 24px; font-weight: bold; color: #111827; margin-bottom: 12px;">New Event Posted!</h1>

                {{-- Event Title --}}
                <h2 style="font-size: 20px; font-weight: 600; color: #2b3a7a; margin-bottom: 16px;">{{ $event->title }}</h2>

                {{-- Body Copy --}}
                <p style="color: #4b5563; font-size: 16px; line-height: 1.5; margin-bottom: 24px;">
                    A new event has been posted on the Slough SEND Parent Carer Forum. We'd love to see you there!
                </p>

                {{-- Info Box --}}
                <div style="background: #f2f3f8; border: 1px solid #e6e8f1; border-radius: 12px; padding: 20px; margin-bottom: 24px; text-align: left;">

                    {{-- Date & Time --}}
                    <div style="display: flex; align-items: flex-start; margin-bottom: 16px;">
                        <svg style="height: 20px; width: 20px; color: #2b3a7a; margin-right: 12px; margin-top: 2px; flex-shrink: 0;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <p style="font-size: 13px; font-weight: 600; color: #111827; margin: 0 0 4px 0;">Date &amp; Time</p>
                            <p style="font-size: 14px; color: #4b5563; margin: 0;">{{ $event->start_date->format('F d, Y') }}</p>
                            <p style="font-size: 14px; color: #4b5563; margin: 0;">{{ $event->start_date->format('g:i A') }} {{ $event->end_date ? '- ' . $event->end_date->format('g:i A') : '' }}</p>
                        </div>
                    </div>

                    {{-- Location --}}
                    <div style="display: flex; align-items: flex-start;">
                        <svg style="height: 20px; width: 20px; color: #2b3a7a; margin-right: 12px; margin-top: 2px; flex-shrink: 0;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <div>
                            <p style="font-size: 13px; font-weight: 600; color: #111827; margin: 0 0 4px 0;">Location</p>
                            @if($event->location_type === 'physical')
                                <p style="font-size: 14px; color: #4b5563; margin: 0;">{{ $event->address }}</p>
                            @else
                                <p style="font-size: 14px; color: #4b5563; margin: 0;">Online Event</p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- About this Event --}}
                <div style="text-align: left; margin-bottom: 32px;">
                    <p style="font-size: 13px; font-weight: 600; color: #111827; margin: 0 0 8px 0;">About this Event</p>
                    <p style="color: #4b5563; font-size: 14px; line-height: 1.5; margin: 0;">
                        {{ \Illuminate\Support\Str::limit($event->description, 200) }}
                    </p>
                </div>

                {{-- Actions --}}
                <a href="{{ route('events.show', $event) }}" style="display: inline-block; text-decoration: none; padding: 12px 24px; border: 1px solid transparent; color: white; background: #2b3a7a; border-radius: 9999px; font-weight: 500;">
                    View Event Details
                </a>
            </div>
        </div>

        <p style="text-align: center; font-size: 12px; color: #9ca3af; margin-top: 24px;">
            Slough SEND Parent Carer Forum &copy; {{ date('Y') }}
        </p>
    </div>
</body>
</html>s
