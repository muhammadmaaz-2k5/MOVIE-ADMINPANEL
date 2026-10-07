<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ !empty($streamData['title']) ? $streamData['title'] : 'Stream Player' }}</title>

    @if(!empty($streamData) && !empty($streamData['streaming_url']))
        {{-- Direct JWPlayer for Fiuosba / Vidara (Zero iframes, 100% Mobile WebView sizing & controls) --}}
        <script src="https://ssl.p.jwpcdn.com/player/v/8.36.2/jwplayer.js"></script>
        <style>
            * {
                box-sizing: border-box;
                margin: 0;
                padding: 0;
            }
            html, body {
                width: 100%;
                height: 100%;
                overflow: hidden;
                background-color: #000000;
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
                touch-action: manipulation;
                -webkit-tap-highlight-color: transparent;
            }
            #jfkp {
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                width: 100%;
                height: 100%;
                overflow: hidden;
                background: #000000;
            }
            #jwplayer {
                width: 100% !important;
                height: 100% !important;
            }

            /* Responsive lift so controls are above Android navigation gesture bar & app bottom HUD */
            @if(isset($bottom) && $bottom !== null)
                .jwplayer .jw-controlbar, #jwplayer .jw-controlbar {
                    bottom: {{ $bottom }}px !important;
                    padding-bottom: 8px !important;
                }
            @else
                /* Mobile portrait preview (height <= 320px) */
                .jwplayer .jw-controlbar, #jwplayer .jw-controlbar {
                    bottom: 40px !important;
                    padding-bottom: 8px !important;
                    max-width: 96% !important;
                    margin: 0 auto !important;
                    left: 2% !important;
                    right: 2% !important;
                }

                /* Fullscreen / Landscape on mobile (height > 320px) */
                @media (min-height: 321px) {
                    .jwplayer .jw-controlbar, #jwplayer .jw-controlbar {
                        bottom: 65px !important;
                        padding-bottom: 12px !important;
                    }
                }

                /* Hardware safe area insets */
                @supports (padding-bottom: env(safe-area-inset-bottom)) {
                    .jwplayer .jw-controlbar, #jwplayer .jw-controlbar {
                        bottom: calc(40px + env(safe-area-inset-bottom, 0px)) !important;
                    }
                    @media (min-height: 321px) {
                        .jwplayer .jw-controlbar, #jwplayer .jw-controlbar {
                            bottom: calc(65px + env(safe-area-inset-bottom, 0px)) !important;
                        }
                    }
                }
            @endif

            .jwplayer.jw-flag-user-inactive.jw-state-playing .jw-controlbar {
                opacity: 0;
                transition: opacity .4s ease;
            }
            .jwplayer .jw-controls {
                visibility: visible !important;
            }
        </style>
    @else
        {{-- Generic Fallback Iframe Player --}}
        <style>
            * {
                box-sizing: border-box;
                margin: 0;
                padding: 0;
            }
            html, body {
                width: 100%;
                height: 100%;
                background-color: #000000;
                overflow: hidden;
                touch-action: manipulation;
                -webkit-tap-highlight-color: transparent;
            }
            #player-container {
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                width: 100%;
                overflow: hidden;
                background: #000000;
                display: flex;
                align-items: center;
                justify-content: center;
                @if(isset($bottom) && $bottom !== null)
                    bottom: {{ $bottom }}px !important;
                    height: calc(100% - {{ $bottom }}px) !important;
                @else
                    bottom: 28px;
                    height: calc(100% - 28px);
                @endif
            }

            @if(!isset($bottom) || $bottom === null)
                @media (min-height: 321px) {
                    #player-container {
                        bottom: 64px;
                        height: calc(100% - 64px);
                    }
                }
                @supports (padding-bottom: env(safe-area-inset-bottom)) {
                    @media (max-height: 320px) {
                        #player-container {
                            bottom: calc(28px + env(safe-area-inset-bottom, 0px));
                            height: calc(100% - 28px - env(safe-area-inset-bottom, 0px));
                        }
                    }
                    @media (min-height: 321px) {
                        #player-container {
                            bottom: calc(64px + env(safe-area-inset-bottom, 0px));
                            height: calc(100% - 64px - env(safe-area-inset-bottom, 0px));
                        }
                    }
                }
            @endif

            #stream-frame {
                width: 100%;
                height: 100%;
                border: none;
                outline: none;
                display: block;
                background: #000000;
            }
        </style>
    @endif
</head>
<body>
    @if(!empty($streamData) && !empty($streamData['streaming_url']))
        {{-- Direct JWPlayer Container --}}
        <div id="jfkp">
            <div id="jwplayer"></div>
        </div>

        <script>
            (function() {
                jwplayer.key = "ITWMv7t88JGzI0xPwW8I0+LveiXX9SWbfdmt0ArUSyc=";
                var streamData = {!! json_encode($streamData) !!};

                var tracks = [];
                if (streamData.subtitles && Array.isArray(streamData.subtitles)) {
                    tracks = streamData.subtitles.map(function(s) {
                        return {
                            file: s.file_path || s.file,
                            label: s.language || s.label || "Subtitle",
                            kind: "captions"
                        };
                    });
                }

                var player = jwplayer("jwplayer");
                player.setup({
                    playlist: [{
                        sources: [{
                            file: streamData.streaming_url,
                            type: "hls"
                        }],
                        title: streamData.title || "",
                        image: streamData.thumbnail || "",
                        tracks: tracks
                    }],
                    autostart: true,
                    controls: true,
                    displaytitle: true,
                    displaydescription: false,
                    skin: {
                        name: "five",
                        buttons: "over"
                    },
                    fullscreenOrientationLock: "none",
                    playbackRateControls: true,
                    playbackRates: [0.5, 1, 1.25, 1.5, 2]
                });

                player.on("ready", function() {
                    // Forward 10 sec button
                    player.addButton(
                        '<svg xmlns="http://www.w3.org/2000/svg" class="jw-svg-icon" viewBox="0 0 240 240"><path d="m 25.993957,57.778 v 125.3 c 0.03604,2.63589 2.164107,4.76396 4.8,4.8 h 62.7 v -19.3 h -48.2 v -96.4 H 160.99396 v 19.3 c 0,5.3 3.6,7.2 8,4.3 l 41.8,-27.9 c 2.93574,-1.480087 4.13843,-5.04363 2.7,-8 -0.57502,-1.174985 -1.52502,-2.124979 -2.7,-2.7 l -41.8,-27.9 c -4.4,-2.9 -8,-1 -8,4.3 v 19.3 H 30.893957 c -2.689569,0.03972 -4.860275,2.210431 -4.9,4.9 z m 163.422413,73.04577 c -3.72072,-6.30626 -10.38421,-10.29683 -17.7,-10.6 -7.31579,0.30317 -13.97928,4.29374 -17.7,10.6 -8.60009,14.23525 -8.60009,32.06475 0,46.3 3.72072,6.30626 10.38421,10.29683 17.7,10.6 7.31579,-0.30317 13.97928,-4.29374 17.7,-10.6 8.60009,-14.23525 8.60009,-32.06475 0,-46.3 z m -17.7,47.2 c -7.8,0 -14.4,-11 -14.4,-24.1 0,-13.1 6.6,-24.1 14.4,-24.1 7.8,0 14.4,11 14.4,24.1 0,13.1 -6.5,24.1 -14.4,24.1 z m -47.77056,9.72863 v -51 l -4.8,4.8 -6.8,-6.8 13,-12.99999 c 3.02543,-3.03598 8.21053,-0.88605 8.2,3.4 v 62.69999 z" fill="#fff"></path></svg>',
                        "Forward 10 sec",
                        function() { player.seek(player.getPosition() + 10); },
                        "ff11"
                    );

                    // Rewind 10 sec button
                    player.addButton(
                        '<svg xmlns="http://www.w3.org/2000/svg" class="jw-svg-icon" viewBox="0 0 240 240"><path d="M113.2,131.078a21.589,21.589,0,0,0-17.7-10.6,21.589,21.589,0,0,0-17.7,10.6,44.769,44.769,0,0,0,0,46.3,21.589,21.589,0,0,0,17.7,10.6,21.589,21.589,0,0,0,17.7-10.6,44.769,44.769,0,0,0,0-46.3Zm-17.7,47.2c-7.8,0-14.4-11-14.4-24.1s6.6-24.1,14.4-24.1,14.4,11,14.4,24.1S103.4,178.278,95.5,178.278Zm-43.4,9.7v-51l-4.8,4.8-6.8-6.8,13-13a4.8,4.8,0,0,1,8.2,3.4v62.7l-9.6-.1Zm162-130.2v125.3a4.867,4.867,0,0,1-4.8,4.8H146.6v-19.3h48.2v-96.4H79.1v19.3c0,5.3-3.6,7.2-8,4.3l-41.8-27.9a6.013,6.013,0,0,1-2.7-8,5.887,5.887,0,0,1,2.7-2.7l41.8-27.9c4.4-2.9,8-1,8,4.3v19.3H209.2A4.974,4.974,0,0,1,214.1,57.778Z" fill="#fff"></path></svg>',
                        "Rewind 10 sec",
                        function() { var p = player.getPosition() - 10; player.seek(p < 0 ? 0 : p); },
                        "ff00"
                    );
                });
            })();
        </script>
    @else
        {{-- Generic Fallback Iframe --}}
        <div id="player-container">
            <iframe id="stream-frame"
                    src="{{ $url }}"
                    allow="autoplay; fullscreen; picture-in-picture; encrypted-media; gyroscope; accelerometer"
                    allowfullscreen="true"
                    webkitallowfullscreen="true"
                    mozallowfullscreen="true"
                    referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>
    @endif
</body>
</html>
