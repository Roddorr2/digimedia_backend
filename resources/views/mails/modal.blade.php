<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DigiMedia Marketing</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border: 1px solid #eaeaea;
            border-radius: 12px;
            overflow: hidden;
        }
        .header {
            background-color: #8a2be2;
            padding: 24px 0;
            text-align: center;
        }
        .header-title {
            color: #ffffff;
            font-size: 28px;
            font-weight: 700;
            margin: 0;
            letter-spacing: 0.5px;
        }
        .content {
            padding: 40px 30px;
        }
        .featured-image {
            width: 100%;
            height: auto;
            border-radius: 8px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        .message-title {
            font-size: 22px;
            font-weight: 600;
            color: #8a2be2;
            margin-bottom: 20px;
        }
        .message-content {
            margin-bottom: 30px;
            color: #444;
            font-size: 16px;
            line-height: 1.7;
        }
        .user-info {
            background-color: #f9f9f9;
            padding: 25px;
            border-radius: 8px;
            margin-bottom: 30px;
            border-left: 3px solid #8a2be2;
        }
        .user-info-title {
            font-weight: 600;
            margin-top: 0;
            margin-bottom: 15px;
            color: #333;
            font-size: 18px;
        }
        .user-detail {
            margin-bottom: 10px;
            font-size: 15px;
        }
        .user-label {
            font-weight: 600;
            color: #555;
            display: inline-block;
            width: 80px;
        }
        .cta-container {
            text-align: center;
            margin: 35px 0;
        }
        .cta-button {
            display: inline-block;
            background-color: #8a2be2;
            color: #fefefe;
            text-decoration: none;
            padding: 22px 50px;
            border-radius: 30px;
            font-weight: 600;
            font-size: 20px;
            transition: background-color 0.3s;
        }
        .cta-button:hover {
            background-color: #7825c1;
        }
        .footer {
            background-color: #f9f9f9;
            padding: 25px 20px;
            text-align: center;
            font-size: 14px;
            color: #777;
            border-top: 1px solid #eaeaea;
        }
        .social-links {
            margin-bottom: 20px;
        }
        .social-link {
            display: inline-block;
            margin: 0 10px;
        }
        .social-link img {
            width: 24px;
            height: 24px;
            opacity: 0.8;
        }
        @media only screen and (max-width: 600px) {
            .container {
                width: 100%;
                border-radius: 0;
                border: none;
            }
            .content {
                padding: 25px 20px;
            }
            .user-info {
                padding: 20px 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            @if(isset($title))
            <h1 class="header-title">{!! $title !!}</h1>
            @endif
        </div>

        <div class="content">
            @if(isset($image))
            <img src="{{ $image }}" alt="DigiMedia Marketing" class="featured-image">
            @endif

            @if(isset($send_message))
            <div class="message-content">
                <p>¡Hola {{ $data["nombre"] }}! 👋🏼</p>
                {!! $send_message !!}
            </div>
            @endif

            <div class="cta-container">
                <a href="{{ $url_boton ?? 'https://wa.me/51983027828?text=Hola%2C%20me%20gustar%C3%ADa%20obtener%20m%C3%A1s%20informaci%C3%B3n%20sobre%20sus%20servicios.' }}"
                    class="cta-button" style="color:white;">{{ $mensaje_boton ?? '¡CONTÁCTANOS!' }}</a>
            </div>

            <div class="extra-message">
                <p>Quedamos atentos a tu mensaje.<br><strong>Saludos,</strong><br>Equipo Digimedia</p>
            </div>
        </div>

        <div class="footer">
            <div class="social-links">
                @if(isset($redes['facebook']))
                <a href="{{ $redes['facebook'] }}" class="social-link">
                    <img src="https://cdn-icons-png.flaticon.com/512/733/733547.png" alt="Facebook">
                </a>
                @endif
                @if(isset($redes['tiktok']))
                <a href="{{ $redes['tiktok'] }}" class="social-link">
                    <img src="https://cdn-icons-png.flaticon.com/512/3046/3046121.png" alt="TikTok">
                </a>
                @endif
                @if(isset($redes['instagram']))
                <a href="{{ $redes['instagram'] }}" class="social-link">
                    <img src="https://cdn-icons-png.flaticon.com/512/2111/2111463.png" alt="Instagram">
                </a>
                @endif
                @if(isset($redes['linkedin']))
                <a href="{{ $redes['linkedin'] }}" class="social-link">
                    <img src="https://cdn-icons-png.flaticon.com/512/3536/3536505.png" alt="LinkedIn">
                </a>
                @endif
            </div>
            <p>{{ $footer ?? '&copy; ' . date('Y') . ' DigiMedia Marketing. Todos los derechos reservados.' }}</p>
        </div>
    </div>
</body>
</html>
