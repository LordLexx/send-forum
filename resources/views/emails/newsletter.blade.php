<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? $title }}</title>
</head>
<body style="margin:0;padding:0;background-color:#f1f5f9;font-family: 'Open Sans', Helvetica, Arial, sans-serif;">

    @php
        $s = $style ?? [];
        $primaryColor = $s['primaryColor'] ?? '#141f52';
        $accentColor = $s['accentColor'] ?? '#f97316';
        $linkColor = $s['linkColor'] ?? '#141f52';
        $textOnPrimary = $s['textOnPrimary'] ?? '#ffffff';
        $bodyTextColor = $s['bodyTextColor'] ?? '#334155';
        $headingColor = $s['headingColor'] ?? '#0f172a';
        $mutedColor = $s['mutedColor'] ?? '#64748b';
        $background = $s['background'] ?? '#ffffff';
        $borderColor = $s['borderColor'] ?? '#e2e8f0';
        $blockquoteBg = $s['blockquoteBg'] ?? '#e6e8f1';

        $headingFont = "'" . ($s['headingFont'] ?? 'Poppins') . "', sans-serif";
        $bodyFont = "'" . ($s['bodyFont'] ?? 'Open Sans') . "', sans-serif";

        $baseFontSize = ($s['baseFontSize'] ?? 15) . 'px';
        $headingScale = $s['headingScale'] ?? 1.0;
        $lineHeight = $s['lineHeight'] ?? 1.6;

        $contentWidth = ($s['contentWidth'] ?? 600) . 'px';
        $contentPadding = ($s['contentPadding'] ?? 32) . 'px';
        $blockSpacing = ($s['blockSpacing'] ?? 16) . 'px';
        $borderRadius = ($s['borderRadius'] ?? 10) . 'px';
        $buttonRadius = ($s['buttonRadius'] ?? 999) . 'px';

        $headerStyle = $s['headerStyle'] ?? 'gradient';
        $headerBgColor = $s['headerBgColor'] ?? '#141f52';
        $headerGradientEnd = $s['headerGradientEnd'] ?? '#2b3a7a';
        $headerText = $s['headerText'] ?? 'Slough SEND Parent Carer Forum';
        $headerLogoText = $s['headerLogoText'] ?? 'S';
        $headerLogoUrl = $s['headerLogoUrl'] ?? '';
        $headerTextColor = $s['headerTextColor'] ?? '#ffffff';
        $headerPadding = ($s['headerPadding'] ?? 20) . 'px';

        $footerBgColor = $s['footerBgColor'] ?? '#0f172a';
        $footerTextColor = $s['footerTextColor'] ?? '#94a3b8';
        $footerLinkColor = $s['footerLinkColor'] ?? '#6a78b5';
        $footerOrgName = $s['footerOrgName'] ?? 'Slough SEND Parent Carer Forum';
        $footerText = $s['footerText'] ?? "Together, we are creating a stronger future for SEND families across Slough. 💙";
        $showUnsubscribe = $s['showUnsubscribe'] ?? true;
        
        $footerTagline = $s['footerTagline'] ?? 'Every Voice Matters';
        $footerSubTagline = $s['footerSubTagline'] ?? 'Listening • Supporting • Representing • Influencing • Empowering';
        $footerWebsiteText = $s['footerWebsiteText'] ?? 'Website';
        $footerWebsiteUrl = $s['footerWebsiteUrl'] ?? '';
        $footerEmail = $s['footerEmail'] ?? '';
        $footerFacebookText = $s['footerFacebookText'] ?? 'Facebook';
        $footerFacebookUrl = $s['footerFacebookUrl'] ?? '';
        $footerInstagramText = $s['footerInstagramText'] ?? 'Instagram';
        $footerInstagramUrl = $s['footerInstagramUrl'] ?? '';
        $footerLocation = $s['footerLocation'] ?? 'Slough, UK';
        
        // Ensure the logo URL is absolute so email clients can load it
        if (!empty($headerLogoUrl) && !str_starts_with($headerLogoUrl, 'http')) {
            $headerLogoUrl = asset($headerLogoUrl);
        }
    @endphp

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f1f5f9;">
        <tr>
            <td align="center" style="padding: 24px 12px;">

                <table role="presentation" cellpadding="0" cellspacing="0" width="600" style="width: {{ $contentWidth }}; max-width: 100%; background-color: {{ $background }}; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">

                    <!-- Header -->
                    <tr>
                        <td style="padding: {{ $headerPadding }}; text-align: center;
                            @if($headerStyle == 'gradient') background: linear-gradient(135deg, {{ $headerBgColor }} 0%, {{ $headerGradientEnd }} 100%); color: {{ $headerTextColor }};
                            @elseif($headerStyle == 'solid') background: {{ $headerBgColor }}; color: {{ $headerTextColor }};
                            @else background: {{ $background }}; border-bottom: 2px solid {{ $primaryColor }}; color: {{ $primaryColor }};
                            @endif
                            font-family: {{ $headingFont }};">
                            
                            @if(!empty($headerLogoUrl))
                                <img src="{{ $headerLogoUrl }}" alt="Logo" style="height: 50px; width: auto; display: block; margin: 0 auto 8px; border: 0;">
                            @else
                                <div style="width:40px;height:40px;border-radius:9999px;background:rgba(255,255,255,0.2);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:18px;margin:0 auto 8px;color:{{ $headerTextColor }};">{{ $headerLogoText }}</div>
                            @endif
                            
                            <p style="margin:0;font-size:11px;text-transform:uppercase;letter-spacing:1px;opacity:0.9;">{{ $headerText }}</p>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td style="padding: {{ $contentPadding }}; font-family: {{ $bodyFont }}; color: {{ $bodyTextColor }}; line-height: {{ $lineHeight }}; font-size: {{ $baseFontSize }};">

                            <h1 style="font-family: {{ $headingFont }}; color: {{ $headingColor }}; font-size: 28px; line-height: 1.3; margin: 0 0 8px;">{{ $title }}</h1>
                            <p style="font-size: 12px; color: {{ $mutedColor }}; margin: 0 0 24px;">{{ now()->format('j F Y') }}</p>

                            @foreach($blocks as $block)
                                @php
                                    $bs = $block['meta']['styles'] ?? [];
                                    $wrapperStyle = "margin-bottom: {$blockSpacing}; ";
                                    if (!empty($bs['bgColor']) && $block['type'] !== 'quote') $wrapperStyle .= "background-color: {$bs['bgColor']}; ";
                                    if (!empty($bs['padding'])) $wrapperStyle .= "padding: {$bs['padding']}px; ";
                                    if (!empty($bs['align'])) $wrapperStyle .= "text-align: {$bs['align']}; ";

                                    $innerStyle = "";
                                    if (!empty($bs['textColor'])) $innerStyle .= "color: {$bs['textColor']}; ";
                                    if (!empty($bs['font']) && $bs['font'] !== 'inherit') $innerStyle .= "font-family: '{$bs['font']}', sans-serif; ";
                                @endphp

                                <div style="{{ $wrapperStyle }}">
                                    @if($block['type'] == 'h1')
                                        <h1 style="font-family: {{ $headingFont }}; color: {{ $headingColor }}; font-size: 28px; line-height: 1.3; margin: 0; {{ $innerStyle }}">{!! $block['content'] !!}</h1>
                                    @elseif($block['type'] == 'h2')
                                        <h2 style="font-family: {{ $headingFont }}; color: {{ $headingColor }}; font-size: 24px; line-height: 1.3; margin: 0; {{ $innerStyle }}">{!! $block['content'] !!}</h2>
                                    @elseif($block['type'] == 'h3')
                                        <h3 style="font-family: {{ $headingFont }}; color: {{ $headingColor }}; font-size: 20px; line-height: 1.3; margin: 0; {{ $innerStyle }}">{!! $block['content'] !!}</h3>
                                    @elseif($block['type'] == 'paragraph')
                                        <p style="margin: 0; {{ $innerStyle }}">{!! $block['content'] !!}</p>
                                    @elseif($block['type'] == 'bulletList')
                                        <ul style="margin: 0; padding-left: 24px; {{ $innerStyle }}">{!! $block['content'] !!}</ul>
                                    @elseif($block['type'] == 'numberList')
                                        <ol style="margin: 0; padding-left: 24px; {{ $innerStyle }}">{!! $block['content'] !!}</ol>
                                    @elseif($block['type'] == 'quote')
                                        @php
                                            $quoteBg = !empty($bs['bgColor']) ? $bs['bgColor'] : $blockquoteBg;
                                        @endphp
                                        <blockquote style="margin: 0; padding: 12px 20px; border-left: 4px solid {{ $primaryColor }}; background-color: {{ $quoteBg }}; border-radius: 0 8px 8px 0; font-style: italic; {{ $innerStyle }}">{!! $block['content'] !!}</blockquote>
                                    @elseif($block['type'] == 'divider')
                                        <hr style="border: 0; border-top: 1px solid {{ $borderColor }}; margin: 0;">
                                    @elseif($block['type'] == 'spacer')
                                        <div style="height: 32px;">&nbsp;</div>
                                    @elseif($block['type'] == 'image' && !empty($block['meta']['src']))
                                        @php
                                            $imgSrc = $block['meta']['src'];
                                            $imgLink = $block['meta']['link'] ?? '';
                                            $imgAlt = $block['meta']['alt'] ?? '';

                                            // Extract the path from the URL so we can find it locally
                                            $path = parse_url($imgSrc, PHP_URL_PATH) ?: '';

                                            // If the path contains "/storage/", extract the relative path and embed it!
                                            if (str_contains($path, '/storage/')) {
                                                $relativePath = \Illuminate\Support\Str::after($path, '/storage/');

                                                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($relativePath)) {
                                                    // This embeds the raw image bytes into the email (CID)
                                                    $imgSrc = $message->embed(\Illuminate\Support\Facades\Storage::disk('public')->path($relativePath));
                                                } else {
                                                    $imgSrc = asset($imgSrc); // Fallback
                                                }
                                            }
                                        @endphp

                                        @if(!empty($imgLink))
                                            <a href="{{ $imgLink }}" target="_blank" style="display: block; cursor: pointer;">
                                        @endif
                                        <img src="{{ $imgSrc }}" alt="{{ $imgAlt }}" style="width: 100%; max-width: 100%; border-radius: {{ $borderRadius }}; display: block;">
                                        @if(!empty($imgLink))
                                            </a>
                                        @endif

                                        @if(!empty($block['meta']['caption']))
                                            <p style="text-align: center; font-size: 12px; color: {{ $mutedColor }}; margin-top: 8px; margin-bottom: 0;">{!! $block['meta']['caption'] !!}</p>
                                        @endif
                                    @elseif($block['type'] == 'cta' && !empty($block['meta']['text']) && !empty($block['meta']['url']))
                                        @php
                                            $btnSize = $bs['buttonSize'] ?? 'medium';
                                            $sizeStyles = [
                                                'small' => 'padding: 6px 16px; font-size: 13px;',
                                                'medium' => 'padding: 10px 22px; font-size: 15px;',
                                                'large' => 'padding: 14px 28px; font-size: 17px;'
                                            ];
                                            $sizeStyle = $sizeStyles[$btnSize] ?? $sizeStyles['medium'];

                                            // Determine button color: per-block override > style-based > global accent
                                            $btnColor = $bs['buttonColor'] ?? null;
                                            if (empty($btnColor)) {
                                                $btnColor = ($block['meta']['style'] == 'teal' || $block['meta']['style'] == 'outline') ? $primaryColor : $accentColor;
                                            }

                                            $btnBg = $block['meta']['style'] == 'outline' ? 'transparent' : $btnColor;
                                            $btnTextColor = $block['meta']['style'] == 'outline' ? $btnColor : '#ffffff';
                                            $btnBorder = "2px solid {$btnColor}";
                                        @endphp
                                        <a href="{{ $block['meta']['url'] }}" target="_blank" style="display: inline-block; {{ $sizeStyle }} background-color: {{ $btnBg }}; color: {{ $btnTextColor }}; border: {{ $btnBorder }}; border-radius: {{ $buttonRadius }}; text-decoration: none; font-weight: 600;">
                                            {{ $block['meta']['text'] }}
                                        </a>
                                    @elseif($block['type'] == 'embed' && !empty($block['meta']['url']))
                                        @php
                                            $platform   = $block['meta']['platform']   ?? 'custom';
                                            $originalUrl = $block['meta']['originalUrl'] ?? $block['meta']['url'];
                                            $embedUrl   = $block['meta']['url'];
                                            $caption    = $block['meta']['caption']    ?? '';

                                            // Try to extract a YouTube video ID from either the embed or original URL
                                            $youtubeId = null;
                                            if ($platform === 'youtube') {
                                                if (preg_match('#/embed/([\w-]{11})#', $embedUrl, $m)) {
                                                    $youtubeId = $m[1];
                                                } elseif (preg_match('#(?:youtube\.com/watch\?v=|youtu\.be/|youtube\.com/shorts/)([\w-]{11})#', $originalUrl, $m)) {
                                                    $youtubeId = $m[1];
                                                }
                                            }

                                            // Branded fallback styling for non-YouTube platforms
                                            $platformStyles = [
                                                'tiktok'     => ['bg' => '#010101', 'icon' => '🎵', 'name' => 'TikTok'],
                                                'instagram'  => ['bg' => '#E1306C', 'icon' => '📸', 'name' => 'Instagram'],
                                                'twitter'    => ['bg' => '#000000', 'icon' => '🐦', 'name' => 'X / Twitter'],
                                                'facebook'   => ['bg' => '#1877F2', 'icon' => '👍', 'name' => 'Facebook'],
                                                'custom'     => ['bg' => $primaryColor, 'icon' => '🔗', 'name' => 'External Link'],
                                            ];
                                            $ps = $platformStyles[$platform] ?? $platformStyles['custom'];
                                        @endphp

                                        @if($platform === 'youtube' && $youtubeId)
                                            {{-- YouTube: show thumbnail with play button overlay, all wrapped in a link --}}
                                            <a href="https://www.youtube.com/watch?v={{ $youtubeId }}" target="_blank"
                                            style="display: block; position: relative; text-decoration: none; border-radius: {{ $borderRadius }}; overflow: hidden;">

                                                <img src="https://img.youtube.com/vi/{{ $youtubeId }}/maxresdefault.jpg"
                                                    alt="YouTube video thumbnail"
                                                    width="536"
                                                    style="width: 100%; max-width: 100%; height: auto; display: block; border-radius: {{ $borderRadius }}; border: 0;"
                                                    onerror="this.onerror=null;this.src='https://img.youtube.com/vi/{{ $youtubeId }}/hqdefault.jpg';">

                                                {{-- Play button overlay --}}
                                                <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;">
                                                    <tr>
                                                        <td align="center" valign="middle" style="vertical-align: middle; padding: 0;">
                                                            <table role="presentation" cellpadding="0" cellspacing="0" style="margin: 0 auto;">
                                                                <tr>
                                                                    <td align="center"
                                                                        style="width: 72px; height: 50px; background-color: rgba(255, 0, 0, 0.85); border-radius: 14px; filter: drop-shadow(0 4px 12px rgba(0,0,0,0.4));">
                                                                        <table role="presentation" cellpadding="0" cellspacing="0" width="72">
                                                                            <tr>
                                                                                <td align="center" valign="middle" style="vertical-align: middle; line-height: 0; padding: 0;">
                                                                                    <div style="display: inline-block; width: 0; height: 0; border-left: 18px solid #ffffff; border-top: 11px solid transparent; border-bottom: 11px solid transparent; margin-left: 4px;"></div>
                                                                                </td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </a>
                                        @else
                                            {{-- Non-YouTube platforms: show a branded card --}}
                                            <a href="{{ $originalUrl }}" target="_blank"
                                            style="display: block; text-decoration: none; border: 1px solid {{ $borderColor }}; border-radius: {{ $borderRadius }}; overflow: hidden;">

                                                <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f8fafc;">
                                                    <tr>
                                                        <td style="padding: 28px 24px; text-align: center;">

                                                            {{-- Platform icon --}}
                                                            <table role="presentation" cellpadding="0" cellspacing="0" align="center" style="margin: 0 auto 14px;">
                                                                <tr>
                                                                    <td align="center" valign="middle"
                                                                        style="width: 56px; height: 56px; background-color: {{ $ps['bg'] }}; border-radius: 9999px; font-size: 28px; line-height: 56px; text-align: center;">
                                                                        {{ $ps['icon'] }}
                                                                    </td>
                                                                </tr>
                                                            </table>

                                                            <p style="margin: 0 0 4px; font-size: 15px; font-weight: 600; color: {{ $headingColor }}; font-family: {{ $headingFont }};">
                                                                {{ $ps['name'] }} Content
                                                            </p>

                                                            <p style="margin: 0 0 18px; font-size: 12px; color: {{ $mutedColor }}; word-break: break-all; word-wrap: break-all; overflow-wrap: break-word;">
                                                                {{ $originalUrl }}
                                                            </p>

                                                            <span style="display: inline-block; padding: 10px 22px; background-color: {{ $ps['bg'] }}; color: #ffffff; border-radius: {{ $buttonRadius }}; font-size: 13px; font-weight: 600; text-decoration: none;">
                                                                View on {{ $ps['name'] }} ↗
                                                            </span>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </a>
                                        @endif

                                        @if(!empty($caption))
                                            <p style="text-align: center; font-size: 12px; color: {{ $mutedColor }}; margin-top: 8px; margin-bottom: 0; font-style: italic;">
                                                {!! $caption !!}
                                            </p>
                                        @endif
                                    @endif
                                </div>
                            @endforeach

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding: 32px 24px; background-color: {{ $footerBgColor }}; color: {{ $footerTextColor }}; text-align: center; font-size: 13px; font-family: {{ $bodyFont }};">
                            
                            <p style="margin: 0 0 12px; font-size: 16px; font-weight: 700; color: {{ $footerLinkColor }};">Contact Us</p>
                            <p style="margin: 0 0 12px; font-weight: 600;">{{ $footerOrgName }}</p>
                            
                            @if(!empty($footerWebsiteUrl))
                                <p style="margin: 0 0 4px;">🌐 Website: <a href="{{ $footerWebsiteUrl }}" target="_blank" style="color: {{ $footerLinkColor }}; text-decoration: none;">{{ $footerWebsiteText }}</a></p>
                            @endif
                            @if(!empty($footerEmail))
                                <p style="margin: 0 0 4px;">📧 Email: <a href="mailto:{{ $footerEmail }}" style="color: {{ $footerLinkColor }}; text-decoration: none;">{{ $footerEmail }}</a></p>
                            @endif
                            @if(!empty($footerFacebookUrl))
                                <p style="margin: 0 0 4px;">📘 Facebook: <a href="{{ $footerFacebookUrl }}" target="_blank" style="color: {{ $footerLinkColor }}; text-decoration: none;">{{ $footerFacebookText }}</a></p>
                            @endif
                            @if(!empty($footerInstagramUrl))
                                <p style="margin: 0 0 16px;">📸 Instagram: <a href="{{ $footerInstagramUrl }}" target="_blank" style="color: {{ $footerLinkColor }}; text-decoration: none;">{{ $footerInstagramText }}</a></p>
                            @endif

                            @if(!empty($footerTagline))
                                <p style="margin: 0 0 4px; font-weight: 600; font-size: 15px;">{{ $footerTagline }}</p>
                            @endif
                            @if(!empty($footerSubTagline))
                                <p style="margin: 0 0 12px; font-size: 12px; opacity: 0.8;">{{ $footerSubTagline }}</p>
                            @endif
                            @if(!empty($footerText))
                                <p style="margin: 0 0 20px; font-size: 13px;">{{ $footerText }}</p>
                            @endif

                            @if($showUnsubscribe)
                                <p style="margin: 0 0 8px; font-size: 12px;">
                                    <a href="#" target="_blank" style="color: {{ $footerLinkColor }}; text-decoration: none;">Unsubscribe</a> 
                                    | 
                                    <a href="#" target="_blank" style="color: {{ $footerLinkColor }}; text-decoration: none;">Manage subscription</a>
                                </p>
                            @endif
                            
                            @if(!empty($footerLocation))
                                <p style="margin: 0; font-size: 11px; opacity: 0.6;">{{ $footerLocation }}</p>
                            @endif
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>