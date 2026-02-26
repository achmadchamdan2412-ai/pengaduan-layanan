<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta name="description" content="Kuesioner Kepuasan Pasien - Rumah Sakit Eka Husada">
    <meta name="theme-color" content="#0284c7">
    <title><?= $pageTitle ?? 'Kuesioner Kepuasan Pasien' ?> - RS Eka Husada</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <style>
        :root {
            --primary-color: #0284c7;
            --primary-dark: #0369a1;
            --secondary-color: #14b8a6;
            --accent-color: #06b6d4;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --bg-gradient-start: #0ea5e9;
            --bg-gradient-end: #14b8a6;
            --white: #ffffff;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            color: var(--text-primary);
            line-height: 1.6;
            min-height: 100vh;
        }

        .header {
            background: linear-gradient(135deg, var(--bg-gradient-start) 0%, var(--bg-gradient-end) 100%);
            padding: 25px 20px;
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow-xl);
        }

        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            opacity: 0.3;
            pointer-events: none;
        }

        .header-logo {
            display: flex;
            align-items: center;
            gap: 24px;
            max-width: 1200px;
            margin: auto;
            position: relative;
            z-index: 1;
        }

        .logo-container {
            flex-shrink: 0;
            background: var(--white);
            border-radius: 16px;
            padding: 12px;
            box-shadow: var(--shadow-lg);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .logo-container:hover {
            transform: translateY(-2px) rotate(2deg);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
        }

        .logo-icon {
            width: 70px;
            height: 70px;
            object-fit: contain;
            display: block;
            transition: transform 0.3s ease;
        }

        .logo-container:hover .logo-icon {
            transform: scale(1.05);
        }

        .header-text {
            display: flex;
            flex-direction: column;
            justify-content: center;
            flex: 1;
            color: var(--white);
        }

        .namars {
            margin: 0;
            font-size: 26px;
            font-weight: 800;
            letter-spacing: -0.5px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            line-height: 1.2;
        }

        .page-title {
            margin: 6px 0;
            font-size: 15px;
            font-weight: 500;
            opacity: 0.95;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .page-title::before {
            content: '';
            width: 4px;
            height: 4px;
            background: var(--accent-color);
            border-radius: 50%;
            display: inline-block;
        }

        .alamatrs {
            margin: 10px 0 0;
            font-size: 13px;
            display: flex;
            align-items: flex-start;
            gap: 8px;
            opacity: 0.9;
            line-height: 1.5;
            max-width: 600px;
        }

        .alamatrs svg {
            flex-shrink: 0;
            margin-top: 2px;
            filter: drop-shadow(0 1px 2px rgba(0, 0, 0, 0.1));
        }


        .mobile-badge {
            display: none;
            position: absolute;
            top: 15px;
            right: 15px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            color: var(--white);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }


        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .header {
            animation: fadeInDown 0.6s ease-out;
        }


        @media (max-width: 768px) {
            .header {
                padding: 20px 15px;
            }

            .header-logo {
                flex-direction: column;
                text-align: center;
                gap: 16px;
            }

            .logo-container {
                padding: 10px;
                border-radius: 14px;
            }

            .logo-icon {
                width: 60px;
                height: 60px;
            }

            .namars {
                font-size: 20px;
                text-align: center;
            }

            .page-title {
                font-size: 14px;
                justify-content: center;
            }

            .alamatrs {
                font-size: 12px;
                justify-content: center;
                text-align: center;
            }

            .mobile-badge {
                display: block;
            }
        }

        @media (max-width: 480px) {
            .header {
                padding: 18px 12px;
            }

            .logo-icon {
                width: 55px;
                height: 55px;
            }

            .namars {
                font-size: 18px;
                line-height: 1.3;
            }

            .page-title {
                font-size: 13px;
            }

            .alamatrs {
                font-size: 11px;
            }
        }

        @media (min-width: 769px) and (max-width: 1024px) {
            .namars {
                font-size: 22px;
            }

            .logo-icon {
                width: 65px;
                height: 65px;
            }
        }


        @media (prefers-color-scheme: dark) {
            body {
                background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            }
        }

        @media print {
            .header {
                background: var(--white) !important;
                color: var(--text-primary) !important;
                box-shadow: none;
                padding: 15px;
            }

            .namars,
            .page-title,
            .alamatrs {
                color: var(--text-primary) !important;
            }

            .logo-container {
                box-shadow: none;
                background: transparent;
            }
        }


        .header-loading {
            position: relative;
            overflow: hidden;
        }

        .header-loading::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            animation: shimmer 2s infinite;
        }

        @keyframes shimmer {
            0% {
                left: -100%;
            }

            100% {
                left: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <header class="header">
            <div class="mobile-badge">
                <i class="fas fa-mobile-alt"></i> Mobile View
            </div>

            <div class="header-logo">
                <div class="logo-container">
                    <img src="images/logo.png" alt="Logo RS Eka Husada" class="logo-icon" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22%3E%3Ccircle cx=%2250%22 cy=%2250%22 r=%2245%22 fill=%22%230284c7%22/%3E%3Ctext x=%2250%22 y=%2260%22 font-size=%2240%22 text-anchor=%22middle%22 fill=%22white%22%3E🏥%3C/text%3E%3C/svg%3E'">
                </div>

                <div class="header-text">
                    <h1 class="namars">RUMAH SAKIT EKA HUSADA</h1>
                    <p class="page-title"><?= htmlspecialchars($pageTitle ?? 'Kuesioner Kepuasan Pasien') ?></p>
                    <div class="alamatrs">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10zm0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6z" />
                            <circle cx="8" cy="9" r="1.5" />
                        </svg>
                        <span>Jl. Raya Sido Mulyo, Sidomulyo, Hulaan, Kec. Menganti</span>
                    </div>
                </div>
            </div>
        </header>