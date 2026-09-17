<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!defined('LIBRARIES')) {
    define('LIBRARIES', './libraries/');
}
$lang = (!empty($_SESSION['lang'])) ? $_SESSION['lang'] : 'vi';
require_once LIBRARIES . "lang/web/" . $lang . ".php";
$isSuccess = !empty($numb);
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang, ENT_QUOTES, 'UTF-8') ?>">
<head>
    <title>:: <?= thongbao ?> ::</title>
    <base href="<?= $basehref ?>" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="REFRESH" content="4.5; url=<?= $page_transfer ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta name="robots" content="noodp,noindex,nofollow" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --tf-ink: #0f172a;
            --tf-muted: #64748b;
            --tf-card: rgba(255, 255, 255, 0.94);
            --tf-line: rgba(12, 119, 177, 0.12);
            --tf-main: #0C77B1;
            --tf-accent: #F58225;
            --tf-ok: #0f8a4b;
            --tf-ok-soft: #e8f8ef;
            --tf-bad: #d64545;
            --tf-bad-soft: #fdecec;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 24px;
            font-family: "Montserrat", "Segoe UI", sans-serif;
            color: var(--tf-ink);
            background:
                radial-gradient(820px 380px at 12% 12%, rgba(12, 119, 177, 0.22), transparent 58%),
                radial-gradient(720px 340px at 88% 88%, rgba(245, 130, 37, 0.18), transparent 55%),
                linear-gradient(165deg, #f4f8fc 0%, #e8f1f8 45%, #f7f3ec 100%);
            overflow: hidden;
        }

        body::before,
        body::after {
            content: "";
            position: fixed;
            border-radius: 50%;
            pointer-events: none;
            animation: float 9s ease-in-out infinite;
        }

        body::before {
            width: 200px;
            height: 200px;
            left: 6%;
            top: 16%;
            background: rgba(12, 119, 177, 0.12);
        }

        body::after {
            width: 150px;
            height: 150px;
            right: 8%;
            bottom: 14%;
            background: rgba(245, 130, 37, 0.14);
            animation-delay: -2.5s;
        }

        .tf-card {
            position: relative;
            width: min(100%, 460px);
            padding: 40px 30px 30px;
            border-radius: 26px;
            background: var(--tf-card);
            border: 1px solid var(--tf-line);
            box-shadow: 0 28px 70px rgba(12, 70, 110, 0.14);
            backdrop-filter: blur(12px);
            text-align: center;
            animation: rise 0.55s cubic-bezier(.2, .8, .2, 1) both;
        }

        .tf-icon {
            width: 84px;
            height: 84px;
            margin: 0 auto 18px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            animation: pop 0.55s cubic-bezier(.2, 1.4, .3, 1) 0.1s both;
        }

        .tf-icon svg {
            width: 40px;
            height: 40px;
            stroke: currentColor;
            fill: none;
            stroke-width: 2.4;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .tf-card.is-ok .tf-icon {
            color: var(--tf-ok);
            background: var(--tf-ok-soft);
            box-shadow: 0 0 0 10px rgba(15, 138, 75, 0.08);
        }

        .tf-card.is-bad .tf-icon {
            color: var(--tf-bad);
            background: var(--tf-bad-soft);
            box-shadow: 0 0 0 10px rgba(214, 69, 69, 0.08);
        }

        .tf-label {
            margin: 0 0 8px;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: var(--tf-main);
        }

        .tf-message {
            margin: 0 auto 18px;
            max-width: 34ch;
            font-size: 19px;
            font-weight: 700;
            line-height: 1.45;
        }

        .tf-card.is-ok .tf-message { color: #0b5c34; }
        .tf-card.is-bad .tf-message { color: #9f2d2d; }

        .tf-hint {
            margin: 0 0 24px;
            font-size: 13.5px;
            color: var(--tf-muted);
        }

        .tf-hint a {
            color: var(--tf-accent);
            font-weight: 700;
            text-decoration: none;
            border-bottom: 1px solid rgba(245, 130, 37, 0.4);
        }

        .tf-hint a:hover { border-bottom-color: var(--tf-accent); }

        .tf-progress {
            height: 8px;
            border-radius: 999px;
            background: #e2e8f0;
            overflow: hidden;
        }

        .tf-progress > span {
            display: block;
            height: 100%;
            width: 0;
            border-radius: inherit;
            transition: width 0.05s linear;
        }

        .tf-card.is-ok .tf-progress > span {
            background: linear-gradient(90deg, #0ca9fe, #0C77B1);
        }

        .tf-card.is-bad .tf-progress > span {
            background: linear-gradient(90deg, #f59a74, #d64545);
        }

        @keyframes rise {
            from { opacity: 0; transform: translateY(18px) scale(0.98); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        @keyframes pop {
            from { opacity: 0; transform: scale(0.6); }
            to { opacity: 1; transform: scale(1); }
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-16px); }
        }

        @media (max-width: 480px) {
            .tf-card { padding: 32px 20px 24px; border-radius: 22px; }
            .tf-message { font-size: 16.5px; }
        }
    </style>
</head>
<body>
    <div class="tf-card <?= $isSuccess ? 'is-ok' : 'is-bad' ?>">
        <div class="tf-icon" aria-hidden="true">
            <?php if ($isSuccess) { ?>
                <svg viewBox="0 0 24 24"><path d="M20 6L9 17l-5-5"/></svg>
            <?php } else { ?>
                <svg viewBox="0 0 24 24"><path d="M12 9v4"/><path d="M12 17h.01"/><path d="M10.3 3.9L1.8 18a2 2 0 001.7 3h16.9a2 2 0 001.7-3L13.7 3.9a2 2 0 00-3.4 0z"/></svg>
            <?php } ?>
        </div>
        <p class="tf-label"><?= thongbao ?></p>
        <p class="tf-message"><?= $showtext ?></p>
        <p class="tf-hint">
            <a href="<?= htmlspecialchars($page_transfer, ENT_QUOTES, 'UTF-8') ?>"><?= clickvaodayneukhongmuondoilau ?></a>
        </p>
        <div class="tf-progress" aria-hidden="true"><span id="process-bar"></span></div>
    </div>
    <script>
        (function () {
            var bar = document.getElementById('process-bar');
            var pos = 0;
            var timer = setInterval(function () {
                pos += 1;
                if (pos >= 100) {
                    pos = 100;
                    clearInterval(timer);
                }
                bar.style.width = pos + '%';
            }, 42);
        })();
    </script>
</body>
</html>
