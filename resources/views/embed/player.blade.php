<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="robots" content="noindex, nofollow">
    <title>Stream Player</title>
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
            @if($bottom !== null)
                bottom: {{ $bottom }}px !important;
                height: calc(100% - {{ $bottom }}px) !important;
            @else
                /* Default Portrait 16:9 preview player: bottom 28px clearance */
                bottom: 28px;
                height: calc(100% - 28px);
            @endif
        }

        @if($bottom === null)
            /* Landscape / Fullscreen on mobile phones (height > 320px): lift 64px to clear app HUD & gesture nav */
            @media (min-height: 321px) {
                #player-container {
                    bottom: 64px;
                    height: calc(100% - 64px);
                }
            }

            /* Extra safe-area clearance on notched or gesture-nav devices */
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
</head>
<body>
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
</body>
</html>
