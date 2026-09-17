<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!defined('LIBRARIES')) {
    define('LIBRARIES', './libraries/');
}

$lang = (!empty($lang)) ? $lang : ((!empty($_SESSION['lang'])) ? $_SESSION['lang'] : 'vi');
$langFile = LIBRARIES . 'lang/web/' . $lang . '.php';
if (!is_file($langFile)) {
    $lang = 'vi';
    $langFile = LIBRARIES . 'lang/web/vi.php';
}
if (!defined('trangkhongtontai')) {
    require_once $langFile;
}

$homeUrl = !empty($configBase) ? $configBase : '/';

if (empty($setting) && !empty($d) && !empty($cache)) {
    $setting = $cache->get("select * from #_setting", null, 'fetch', 7200);
}

$brandName = !empty($setting['name' . $lang])
    ? $setting['name' . $lang]
    : (!empty($setting['namevi']) ? $setting['namevi'] : trangchu);
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang, ENT_QUOTES, 'UTF-8') ?>">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex,nofollow">
    <title>404 · <?= htmlspecialchars($brandName, ENT_QUOTES, 'UTF-8') ?></title>
    <base href="<?= htmlspecialchars($homeUrl, ENT_QUOTES, 'UTF-8') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;600;700;800&family=Syne:wght@700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --nf-ink: #0f2433;
            --nf-muted: #5b7386;
            --nf-main: #0C77B1;
            --nf-main-deep: #085a87;
            --nf-accent: #F58225;
            --nf-paper: #f3f8fc;
            --nf-line: rgba(12, 119, 177, 0.16);
        }

        * { box-sizing: border-box; }

        html, body {
            margin: 0;
            min-height: 100%;
        }

        body {
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: clamp(24px, 5vw, 48px);
            font-family: "Montserrat", "Segoe UI", sans-serif;
            color: var(--nf-ink);
            background:
                radial-gradient(900px 420px at 8% 10%, rgba(12, 119, 177, 0.26), transparent 58%),
                radial-gradient(780px 380px at 92% 88%, rgba(245, 130, 37, 0.2), transparent 56%),
                linear-gradient(160deg, #eef6fb 0%, #e4eef7 42%, #f8f4ee 100%);
            overflow-x: hidden;
        }

        body::before,
        body::after {
            content: "";
            position: fixed;
            border-radius: 50%;
            pointer-events: none;
            z-index: 0;
            animation: nf-float 10s ease-in-out infinite;
        }

        body::before {
            width: 220px;
            height: 220px;
            left: 5%;
            top: 18%;
            background: rgba(12, 119, 177, 0.12);
        }

        body::after {
            width: 160px;
            height: 160px;
            right: 7%;
            bottom: 12%;
            background: rgba(245, 130, 37, 0.14);
            animation-delay: -3s;
        }

        .nf {
            position: relative;
            z-index: 1;
            width: min(100%, 720px);
            text-align: center;
            animation: nf-rise 0.6s cubic-bezier(.2, .8, .2, 1) both;
        }

        .nf-brand {
            margin: 0 0 18px;
            font-size: clamp(1.35rem, 2.4vw, 1.85rem);
            font-weight: 800;
            letter-spacing: -0.02em;
            color: var(--nf-main);
            animation: nf-fade 0.7s ease 0.08s both;
        }

        .nf-brand a {
            color: inherit;
            text-decoration: none;
        }

        .nf-brand a:hover {
            color: var(--nf-accent);
        }

        .nf-code {
            margin: 0;
            font-family: "Syne", "Montserrat", sans-serif;
            font-size: clamp(7.5rem, 22vw, 11.5rem);
            font-weight: 800;
            line-height: 0.9;
            letter-spacing: -0.06em;
            background: linear-gradient(135deg, var(--nf-main) 0%, #1490d0 48%, var(--nf-accent) 100%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            animation: nf-pop 0.65s cubic-bezier(.2, 1.3, .3, 1) 0.12s both;
        }

        .nf-title {
            margin: 18px 0 10px;
            font-size: clamp(1.35rem, 3vw, 1.85rem);
            font-weight: 800;
            letter-spacing: -0.02em;
            animation: nf-fade 0.7s ease 0.2s both;
        }

        .nf-desc {
            margin: 0 auto 28px;
            max-width: 38ch;
            font-size: 1rem;
            line-height: 1.65;
            color: var(--nf-muted);
            font-weight: 500;
            animation: nf-fade 0.7s ease 0.28s both;
        }

        .nf-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            justify-content: center;
            animation: nf-fade 0.7s ease 0.36s both;
        }

        .nf-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 48px;
            padding: 0 26px;
            border-radius: 12px;
            font-size: 0.95rem;
            font-weight: 700;
            text-decoration: none;
            transition: transform 0.2s ease, background 0.2s ease, color 0.2s ease, border-color 0.2s ease;
        }

        .nf-btn:hover {
            transform: translateY(-2px);
        }

        .nf-btn-primary {
            color: #fff;
            background: linear-gradient(135deg, var(--nf-main), var(--nf-main-deep));
            border: 1px solid transparent;
            box-shadow: 0 12px 28px rgba(12, 119, 177, 0.22);
        }

        .nf-btn-primary:hover {
            background: linear-gradient(135deg, #0e86c5, var(--nf-main));
        }

        .nf-btn-ghost {
            color: var(--nf-main);
            background: rgba(255, 255, 255, 0.55);
            border: 1px solid var(--nf-line);
        }

        .nf-btn-ghost:hover {
            color: var(--nf-accent);
            border-color: rgba(245, 130, 37, 0.35);
            background: #fff;
        }

        @keyframes nf-rise {
            from { opacity: 0; transform: translateY(22px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes nf-fade {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes nf-pop {
            from { opacity: 0; transform: scale(0.86); }
            to { opacity: 1; transform: scale(1); }
        }

        @keyframes nf-float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-18px); }
        }

        @media (max-width: 480px) {
            .nf-btn {
                width: 100%;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                animation: none !important;
                transition: none !important;
            }
        }
    </style>
</head>
<body>
    <main class="nf">
        <p class="nf-brand">
            <a href="<?= htmlspecialchars($homeUrl, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($brandName, ENT_QUOTES, 'UTF-8') ?></a>
        </p>
        <p class="nf-code" aria-hidden="true">404</p>
        <h1 class="nf-title"><?= trangkhongtontai ?></h1>
        <p class="nf-desc"><?= mota404 ?></p>
        <div class="nf-actions">
            <a class="nf-btn nf-btn-primary" href="<?= htmlspecialchars($homeUrl, ENT_QUOTES, 'UTF-8') ?>"><?= vetrangchu ?></a>
            <a class="nf-btn nf-btn-ghost" href="javascript:history.back()"><?= quaylai ?></a>
        </div>
    </main>
</body>
</html>
