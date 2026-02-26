<?php
/**
 * ============================================================
 *  RSYI Dashboard — Installation Wizard
 *  معالج التنصيب — لوحة تحكم RSYI
 * ============================================================
 */

define('ROOT_DIR',  dirname(__DIR__));
define('LOCK_FILE', ROOT_DIR . '/storage/installed.lock');

// ── Already installed? redirect to dashboard ─────────────
if (file_exists(LOCK_FILE) && !isset($_GET['reinstall'])) {
    header('Location: /');
    exit;
}

session_start();

$step   = (int)($_SESSION['install_step'] ?? 1);
$errors = [];

/* ─────────────────────────────────────────────────────────
 *  Helper Functions
 * ─────────────────────────────────────────────────────────*/
function getRequirements(): array
{
    return [
        [
            'name'    => 'PHP 8.1+',
            'pass'    => PHP_VERSION_ID >= 80100,
            'current' => PHP_VERSION,
        ],
        [
            'name'    => 'PDO MySQL',
            'pass'    => extension_loaded('pdo_mysql'),
            'current' => extension_loaded('pdo_mysql') ? 'مفعّل' : 'غير مفعّل',
        ],
        [
            'name'    => 'mbstring',
            'pass'    => extension_loaded('mbstring'),
            'current' => extension_loaded('mbstring') ? 'مفعّل' : 'غير مفعّل',
        ],
        [
            'name'    => 'OpenSSL',
            'pass'    => extension_loaded('openssl'),
            'current' => extension_loaded('openssl') ? 'مفعّل' : 'غير مفعّل',
        ],
        [
            'name'    => 'Tokenizer',
            'pass'    => extension_loaded('tokenizer'),
            'current' => extension_loaded('tokenizer') ? 'مفعّل' : 'غير مفعّل',
        ],
        [
            'name'    => 'مجلد storage قابل للكتابة',
            'pass'    => is_writable(ROOT_DIR . '/storage'),
            'current' => is_writable(ROOT_DIR . '/storage')
                ? 'قابل للكتابة ✓'
                : 'غير قابل للكتابة — نفّذ: chmod -R 775 storage',
        ],
        [
            'name'    => 'مجلد bootstrap/cache',
            'pass'    => is_writable(ROOT_DIR . '/bootstrap/cache'),
            'current' => is_writable(ROOT_DIR . '/bootstrap/cache')
                ? 'قابل للكتابة ✓'
                : 'غير قابل للكتابة — نفّذ: chmod -R 775 bootstrap/cache',
        ],
        [
            'name'    => 'Composer vendor',
            'pass'    => file_exists(ROOT_DIR . '/vendor/autoload.php'),
            'current' => file_exists(ROOT_DIR . '/vendor/autoload.php')
                ? 'موجود ✓'
                : 'غير موجود — نفّذ: composer install',
        ],
    ];
}

function testDbConn(string $host, string $port, string $db, string $user, string $pass): array
{
    try {
        new PDO(
            "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4",
            $user,
            $pass,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_TIMEOUT => 5]
        );
        return ['ok' => true, 'msg' => 'تم الاتصال بنجاح ✓'];
    } catch (PDOException $e) {
        $m = $e->getMessage();
        if (str_contains($m, 'Access denied'))       $m = 'خطأ في اسم المستخدم أو كلمة المرور';
        elseif (str_contains($m, 'Unknown database')) $m = 'قاعدة البيانات غير موجودة';
        elseif (str_contains($m, 'refused'))          $m = 'تعذّر الوصول إلى السيرفر — تحقق من Host و Port';
        else                                           $m = substr($m, 0, 140);
        return ['ok' => false, 'msg' => $m];
    }
}

function buildEnvContent(array $db, array $app): string
{
    $name  = $app['name'] ?? 'RSYI Dashboard';
    $url   = rtrim($app['url'] ?? 'http://localhost', '/');
    $tz    = $app['tz']   ?? 'Africa/Cairo';
    $env   = $app['env']  ?? 'production';
    $debug = $env === 'local' ? 'true' : 'false';

    return <<<ENV
APP_NAME="{$name}"
APP_ENV={$env}
APP_KEY=
APP_DEBUG={$debug}
APP_URL={$url}

APP_LOCALE=ar
APP_FALLBACK_LOCALE=ar
APP_TIMEZONE={$tz}
APP_MAINTENANCE_DRIVER=file

LOG_CHANNEL=stack
LOG_STACK=single
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST={$db['host']}
DB_PORT={$db['port']}
DB_DATABASE={$db['name']}
DB_USERNAME={$db['user']}
DB_PASSWORD={$db['pass']}

SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

CACHE_STORE=file
QUEUE_CONNECTION=sync
FILESYSTEM_DISK=local
ENV;
}

function canRunShell(): bool
{
    if (!function_exists('shell_exec')) return false;
    $disabled = array_map('trim', explode(',', ini_get('disable_functions')));
    return !in_array('shell_exec', $disabled);
}

function doInstall(): array
{
    $log = [];
    $db  = $_SESSION['wiz_db']  ?? [];
    $app = $_SESSION['wiz_app'] ?? [];

    // 1. Write .env
    $wrote = @file_put_contents(ROOT_DIR . '/.env', buildEnvContent($db, $app));
    $log[] = ['label' => 'إنشاء ملف .env', 'ok' => $wrote !== false];
    if (!$wrote) {
        return ['ok' => false, 'msg' => 'فشل كتابة ملف .env — تحقق من صلاحيات المجلد', 'log' => $log];
    }

    // 2. php artisan key:generate
    $keyOk     = false;
    $keyManual = null;
    if (canRunShell()) {
        $out   = (string)shell_exec('cd ' . escapeshellarg(ROOT_DIR) . ' && php artisan key:generate --force 2>&1');
        $keyOk = str_contains($out, 'successfully') || str_contains($out, 'generated');
        if (!$keyOk) {
            $env   = (string)@file_get_contents(ROOT_DIR . '/.env');
            $keyOk = (bool)preg_match('/APP_KEY=base64:.{43}/', $env);
        }
    } else {
        $keyManual = 'php artisan key:generate --force';
    }
    $log[] = ['label' => 'توليد مفتاح التشفير (APP_KEY)', 'ok' => $keyOk || $keyManual !== null, 'manual' => $keyManual];

    // 3. Cache optimisation
    $cacheManual = null;
    if (canRunShell()) {
        shell_exec('cd ' . escapeshellarg(ROOT_DIR) . ' && php artisan config:cache 2>&1');
        shell_exec('cd ' . escapeshellarg(ROOT_DIR) . ' && php artisan route:cache  2>&1');
        shell_exec('cd ' . escapeshellarg(ROOT_DIR) . ' && php artisan view:cache   2>&1');
    } else {
        $cacheManual = 'php artisan config:cache && php artisan route:cache';
    }
    $log[] = ['label' => 'تحسين الأداء (Cache)', 'ok' => true, 'manual' => $cacheManual];

    // 4. Storage link
    if (canRunShell()) {
        shell_exec('cd ' . escapeshellarg(ROOT_DIR) . ' && php artisan storage:link 2>&1');
    }
    $log[] = ['label' => 'ربط مجلد Storage', 'ok' => true];

    // 5. Lock file
    $locked = @file_put_contents(LOCK_FILE, date('Y-m-d H:i:s'));
    $log[]  = ['label' => 'حفظ ملف التثبيت (lock)', 'ok' => $locked !== false];

    return ['ok' => true, 'log' => $log];
}

/* ─────────────────────────────────────────────────────────
 *  AJAX: Test DB connection
 * ─────────────────────────────────────────────────────────*/
if (($_POST['_ajax'] ?? '') === 'test_db') {
    header('Content-Type: application/json');
    echo json_encode(testDbConn(
        $_POST['host'] ?? '127.0.0.1',
        $_POST['port'] ?? '3306',
        $_POST['name'] ?? '',
        $_POST['user'] ?? '',
        $_POST['pass'] ?? ''
    ));
    exit;
}

/* ─────────────────────────────────────────────────────────
 *  Handle POST — step navigation
 * ─────────────────────────────────────────────────────────*/
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_POST['_ajax'])) {
    $a = $_POST['_action'] ?? '';

    if ($a === 'back') {
        $_SESSION['install_step'] = $step = max(1, $step - 1);

    } elseif ($a === 'next1') {
        $_SESSION['install_step'] = $step = 2;

    } elseif ($a === 'next2') {
        $reqs    = getRequirements();
        $allPass = !in_array(false, array_column($reqs, 'pass'));
        if ($allPass) {
            $_SESSION['install_step'] = $step = 3;
        } else {
            $errors[] = 'يرجى إصلاح المتطلبات المُشار إليها باللون الأحمر أولاً';
        }

    } elseif ($a === 'next3') {
        $r = testDbConn(
            $_POST['db_host'] ?? '',
            $_POST['db_port'] ?? '3306',
            $_POST['db_name'] ?? '',
            $_POST['db_user'] ?? '',
            $_POST['db_pass'] ?? ''
        );
        if ($r['ok']) {
            $_SESSION['wiz_db']       = [
                'host' => $_POST['db_host'],
                'port' => $_POST['db_port'],
                'name' => $_POST['db_name'],
                'user' => $_POST['db_user'],
                'pass' => $_POST['db_pass'],
            ];
            $_SESSION['install_step'] = $step = 4;
        } else {
            $errors[] = $r['msg'];
        }

    } elseif ($a === 'next4') {
        if (empty(trim($_POST['app_name'] ?? ''))) $errors[] = 'اسم التطبيق مطلوب';
        if (empty(trim($_POST['app_url']  ?? ''))) $errors[] = 'رابط الموقع مطلوب';
        if (empty($errors)) {
            $_SESSION['wiz_app']      = [
                'name' => trim($_POST['app_name']),
                'url'  => rtrim(trim($_POST['app_url']), '/'),
                'tz'   => $_POST['timezone'] ?? 'Africa/Cairo',
                'env'  => $_POST['app_env']  ?? 'production',
            ];
            $_SESSION['install_step'] = $step = 5;
        }

    } elseif ($a === 'install') {
        $result                       = doInstall();
        $_SESSION['install_result']   = $result;
        $_SESSION['install_step']     = $step = 6;

    } elseif ($a === 'reset') {
        session_destroy();
        header('Location: install.php');
        exit;
    }
}

// Auto-detect current URL for the App URL field
$detectedUrl  = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http');
$detectedUrl .= '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost');
$detectedUrl  = preg_replace('#/install\.php.*$#', '', $detectedUrl);

$timezones = [
    'Africa/Cairo'      => '🇪🇬 مصر (القاهرة)',
    'Asia/Riyadh'       => '🇸🇦 السعودية (الرياض)',
    'Asia/Kuwait'       => '🇰🇼 الكويت',
    'Asia/Baghdad'      => '🇮🇶 العراق (بغداد)',
    'Asia/Dubai'        => '🇦🇪 الإمارات (دبي)',
    'Asia/Beirut'       => '🇱🇧 لبنان (بيروت)',
    'Asia/Amman'        => '🇯🇴 الأردن (عمّان)',
    'Asia/Qatar'        => '🇶🇦 قطر',
    'Asia/Bahrain'      => '🇧🇭 البحرين',
    'Africa/Tripoli'    => '🇱🇾 ليبيا (طرابلس)',
    'Africa/Tunis'      => '🇹🇳 تونس',
    'Africa/Algiers'    => '🇩🇿 الجزائر',
    'Africa/Casablanca' => '🇲🇦 المغرب',
    'Europe/London'     => '🇬🇧 لندن (GMT)',
    'UTC'               => '🌐 UTC',
];
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>معالج التنصيب — RSYI Dashboard</title>

    <!-- Bootstrap 5 RTL -->
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Cairo Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800&display=swap"
          rel="stylesheet">

    <style>
        :root {
            --primary:      #2563eb;
            --primary-dark: #1d4ed8;
            --success:      #16a34a;
            --danger:       #dc2626;
            --bg:           #eef2ff;
        }
        * { font-family: 'Cairo', sans-serif; box-sizing: border-box; }

        body {
            background: var(--bg);
            min-height: 100vh;
        }

        /* ── Header ─────────────────────────────── */
        .wiz-header {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            color: #fff;
            padding: 2.5rem 1rem 5rem;
            text-align: center;
        }
        .wiz-header h1 { font-weight: 800; font-size: 1.75rem; margin-bottom: .3rem; }
        .wiz-header p  { opacity: .8; margin: 0; }

        /* ── Card ───────────────────────────────── */
        .wiz-card {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 10px 50px rgba(37,99,235,.13);
            margin-top: -3rem;
            overflow: hidden;
        }

        /* ── Steps bar ──────────────────────────── */
        .steps-bar {
            display: flex;
            justify-content: center;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            padding: 1.4rem 1rem;
            overflow-x: auto;
            gap: 0;
        }
        .step-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            min-width: 72px;
            flex: 0 0 auto;
        }
        .step-item + .step-item::before {
            content: '';
            position: absolute;
            top: 17px;
            right: calc(50% + 18px);
            width: calc(100% - 36px);
            height: 2px;
            background: #e2e8f0;
            z-index: 0;
        }
        .step-item.done  + .step-item::before,
        .step-item.active+ .step-item::before { background: var(--primary); }

        .step-circle {
            width: 35px; height: 35px;
            border-radius: 50%;
            border: 2px solid #e2e8f0;
            background: #fff;
            display: flex; align-items: center; justify-content: center;
            font-size: .78rem; font-weight: 700;
            color: #94a3b8;
            position: relative; z-index: 1;
            transition: all .25s;
        }
        .step-item.done   .step-circle { background: var(--primary); border-color: var(--primary); color: #fff; }
        .step-item.active .step-circle {
            background: var(--primary); border-color: var(--primary); color: #fff;
            box-shadow: 0 0 0 5px rgba(37,99,235,.18);
        }
        .step-label {
            font-size: .68rem; color: #94a3b8; margin-top: .35rem;
            white-space: nowrap;
        }
        .step-item.done   .step-label,
        .step-item.active .step-label { color: var(--primary); font-weight: 600; }

        /* ── Body ───────────────────────────────── */
        .wiz-body { padding: 2.2rem 2.5rem; }

        .step-title    { font-size: 1.35rem; font-weight: 800; color: #1e293b; margin-bottom: .25rem; }
        .step-subtitle { color: #64748b; margin-bottom: 1.6rem; font-size: .95rem; }

        /* ── Requirement list ───────────────────── */
        .req-row {
            display: flex; align-items: center; justify-content: space-between;
            padding: .65rem 1rem;
            border-radius: 9px;
            margin-bottom: .35rem;
            background: #f8fafc;
            font-size: .9rem;
        }
        .req-row.fail    { background: #fef2f2; }
        .req-row .badge  { font-size: .75rem; font-weight: 600; }

        /* ── Form controls ──────────────────────── */
        .form-control, .form-select {
            border-radius: 9px !important;
            border: 1.5px solid #e2e8f0 !important;
            padding: .62rem 1rem !important;
            font-size: .93rem !important;
            transition: border-color .2s, box-shadow .2s;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary) !important;
            box-shadow: 0 0 0 3px rgba(37,99,235,.13) !important;
        }
        .form-label { font-weight: 600; color: #374151; font-size: .88rem; margin-bottom: .3rem; }

        /* ── DB test status ─────────────────────── */
        #db-status {
            border-radius: 9px; padding: .6rem 1rem;
            font-size: .88rem; font-weight: 600;
            display: none;
        }
        #db-status.ok   { display:block; background:#f0fdf4; color:#16a34a; border:1px solid #86efac; }
        #db-status.fail { display:block; background:#fef2f2; color:#dc2626; border:1px solid #fca5a5; }

        /* ── Install log ────────────────────────── */
        .log-row {
            display: flex; align-items: center; gap: .7rem;
            padding: .7rem 1rem;
            border-radius: 9px; margin-bottom: .4rem;
            font-weight: 500; font-size: .9rem;
        }
        .log-row.ok   { background: #f0fdf4; color: #166534; }
        .log-row.fail { background: #fef2f2; color: #991b1b; }

        /* ── Env radio cards ────────────────────── */
        .env-card {
            border: 2px solid #e2e8f0;
            border-radius: 11px;
            padding: 1rem;
            cursor: pointer;
            text-align: center;
            transition: all .2s;
            user-select: none;
        }
        .env-card:has(input:checked) { border-color: var(--primary); background: #eff6ff; }
        .env-card input { position: absolute; opacity: 0; pointer-events: none; }
        .env-card .icon { font-size: 1.8rem; margin-bottom: .25rem; }

        /* ── Buttons ────────────────────────────── */
        .btn-primary {
            background: var(--primary) !important;
            border-color: var(--primary) !important;
            border-radius: 10px !important;
            padding: .62rem 1.7rem !important;
            font-weight: 700 !important;
        }
        .btn-primary:hover { background: var(--primary-dark) !important; }
        .btn-outline-secondary { border-radius: 10px !important; }

        /* ── Alerts ─────────────────────────────── */
        .alert { border-radius: 11px !important; font-weight: 500; }

        /* ── Summary box ────────────────────────── */
        .summary-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 11px;
            padding: 1.1rem 1.3rem;
            font-size: .88rem;
        }
        .summary-box .row > div { padding: .3rem 0; }

        /* ── Success icon ───────────────────────── */
        .big-icon { font-size: 5rem; margin-bottom: .8rem; }

        /* ── Spinner ────────────────────────────── */
        @keyframes spin { to { transform: rotate(360deg); } }
        .fa-spin-custom { animation: spin .75s linear infinite; display: inline-block; }

        /* ── Password toggle ────────────────────── */
        .input-group .btn-outline-secondary {
            border-color: #e2e8f0 !important;
            background: #f8fafc;
            color: #64748b;
        }

        /* ── Welcome cards ──────────────────────── */
        .welcome-card {
            background: #eff6ff;
            border-radius: 11px;
            padding: .9rem 1rem;
            display: flex; align-items: center; gap: .7rem;
            font-size: .9rem;
        }

        @media (max-width: 576px) {
            .wiz-body { padding: 1.5rem 1.2rem; }
            .step-label { display: none; }
        }
    </style>
</head>
<body>

<!-- ═══════════════════════════════════════ Header ═══════ -->
<div class="wiz-header">
    <div class="container">
        <h1><i class="fas fa-rocket me-2"></i>معالج تنصيب RSYI</h1>
        <p>اتبع الخطوات البسيطة لإعداد النظام كاملاً</p>
    </div>
</div>

<!-- ═══════════════════════════════════════ Wizard Card ══ -->
<div class="container" style="max-width:700px; padding-bottom:4rem;">
    <div class="wiz-card">

        <!-- Steps Bar -->
        <div class="steps-bar">
            <?php
            $stepLabels = ['مرحباً', 'المتطلبات', 'قاعدة البيانات', 'الإعدادات', 'التثبيت', 'تم!'];
            foreach ($stepLabels as $i => $label):
                $n   = $i + 1;
                $cls = $n < $step ? 'done' : ($n === $step ? 'active' : '');
            ?>
            <div class="step-item <?= $cls ?>">
                <div class="step-circle">
                    <?php if ($n < $step): ?>
                        <i class="fas fa-check" style="font-size:.65rem"></i>
                    <?php else: ?>
                        <?= $n ?>
                    <?php endif ?>
                </div>
                <div class="step-label"><?= $label ?></div>
            </div>
            <?php endforeach ?>
        </div>

        <!-- Body -->
        <div class="wiz-body">

            <!-- Errors -->
            <?php if ($errors): ?>
            <div class="alert alert-danger mb-4">
                <?php foreach ($errors as $e): ?>
                <div><i class="fas fa-exclamation-circle me-1"></i><?= htmlspecialchars($e) ?></div>
                <?php endforeach ?>
            </div>
            <?php endif ?>

            <?php /* ════════════════ STEP 1 — Welcome ════════════════ */ if ($step === 1): ?>

            <div class="text-center py-2">
                <div class="big-icon">🎉</div>
                <h2 class="step-title">أهلاً بك في معالج التنصيب</h2>
                <p class="step-subtitle">
                    سيساعدك هذا المعالج على إعداد <strong>RSYI Dashboard</strong> في 5 خطوات بسيطة
                </p>

                <div class="row g-2 text-start mb-4">
                    <div class="col-6">
                        <div class="welcome-card">
                            <i class="fas fa-check-circle text-primary fs-5"></i>
                            <span>فحص المتطلبات</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="welcome-card">
                            <i class="fas fa-database text-primary fs-5"></i>
                            <span>إعداد قاعدة البيانات</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="welcome-card">
                            <i class="fas fa-cog text-primary fs-5"></i>
                            <span>ضبط الإعدادات</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="welcome-card">
                            <i class="fas fa-rocket text-primary fs-5"></i>
                            <span>تشغيل النظام تلقائياً</span>
                        </div>
                    </div>
                </div>

                <form method="POST">
                    <input type="hidden" name="_action" value="next1">
                    <button type="submit" class="btn btn-primary btn-lg px-5">
                        ابدأ التنصيب &nbsp;<i class="fas fa-arrow-left"></i>
                    </button>
                </form>
            </div>

            <?php /* ════════════════ STEP 2 — Requirements ════════════ */ elseif ($step === 2):
                $reqs    = getRequirements();
                $allPass = !in_array(false, array_column($reqs, 'pass'));
            ?>

            <h2 class="step-title"><i class="fas fa-check-circle me-2 text-primary"></i>فحص المتطلبات</h2>
            <p class="step-subtitle">يتحقق النظام من توافر جميع المكونات اللازمة للتشغيل</p>

            <?php foreach ($reqs as $r): ?>
            <div class="req-row <?= $r['pass'] ? '' : 'fail' ?>">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas <?= $r['pass'] ? 'fa-check-circle text-success' : 'fa-times-circle text-danger' ?>"></i>
                    <span><?= htmlspecialchars($r['name']) ?></span>
                </div>
                <span class="badge <?= $r['pass'] ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' ?>"
                      style="border-radius:7px; padding:.3rem .6rem;">
                    <?= htmlspecialchars($r['current']) ?>
                </span>
            </div>
            <?php endforeach ?>

            <?php if (!$allPass): ?>
            <div class="alert alert-warning mt-3">
                <i class="fas fa-tools me-1"></i>
                أصلح العناصر المُعلَّمة بالأحمر ثم اضغط <strong>"إعادة الفحص"</strong>
            </div>
            <?php else: ?>
            <div class="alert alert-success mt-3">
                <i class="fas fa-party-horn me-1"></i>
                رائع! جميع المتطلبات مكتملة — يمكنك المتابعة
            </div>
            <?php endif ?>

            <div class="d-flex justify-content-between mt-4">
                <form method="POST">
                    <input type="hidden" name="_action" value="back">
                    <button class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-right me-1"></i>السابق
                    </button>
                </form>
                <div class="d-flex gap-2">
                    <a href="install.php" class="btn btn-outline-secondary">
                        <i class="fas fa-sync-alt me-1"></i>إعادة الفحص
                    </a>
                    <form method="POST">
                        <input type="hidden" name="_action" value="next2">
                        <button type="submit" class="btn btn-primary" <?= $allPass ? '' : 'disabled' ?>>
                            التالي &nbsp;<i class="fas fa-arrow-left"></i>
                        </button>
                    </form>
                </div>
            </div>

            <?php /* ════════════════ STEP 3 — Database ════════════════ */ elseif ($step === 3):
                $saved = $_SESSION['wiz_db'] ?? [];
            ?>

            <h2 class="step-title"><i class="fas fa-database me-2 text-primary"></i>إعداد قاعدة البيانات</h2>
            <p class="step-subtitle">أدخل بيانات الاتصال بقاعدة بيانات WordPress</p>

            <form method="POST" id="dbForm">
                <input type="hidden" name="_action" value="next3">
                <div class="row g-3">
                    <div class="col-8">
                        <label class="form-label">عنوان السيرفر <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="db_host" id="db_host"
                               value="<?= htmlspecialchars($saved['host'] ?? '127.0.0.1') ?>"
                               placeholder="127.0.0.1" required>
                    </div>
                    <div class="col-4">
                        <label class="form-label">المنفذ (Port)</label>
                        <input type="number" class="form-control" name="db_port" id="db_port"
                               value="<?= htmlspecialchars($saved['port'] ?? '3306') ?>"
                               placeholder="3306" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">اسم قاعدة البيانات <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="db_name" id="db_name"
                               value="<?= htmlspecialchars($saved['name'] ?? '') ?>"
                               placeholder="مثال: wordpress_db" required>
                        <div class="form-text">نفس قاعدة بيانات WordPress</div>
                    </div>
                    <div class="col-6">
                        <label class="form-label">اسم المستخدم <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="db_user" id="db_user"
                               value="<?= htmlspecialchars($saved['user'] ?? '') ?>"
                               placeholder="root" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label">كلمة المرور</label>
                        <div class="input-group">
                            <input type="password" class="form-control" name="db_pass" id="db_pass"
                                   value="<?= htmlspecialchars($saved['pass'] ?? '') ?>">
                            <button type="button" class="btn btn-outline-secondary"
                                    onclick="togglePass('db_pass', this)">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div id="db-status" class="mt-3"></div>

                <div class="d-flex justify-content-between mt-4">
                    <button type="button" class="btn btn-outline-secondary"
                            onclick="setAction('back'); dbForm.submit()">
                        <i class="fas fa-arrow-right me-1"></i>السابق
                    </button>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-secondary" onclick="testDbNow()">
                            <i class="fas fa-plug me-1"></i>اختبار الاتصال
                        </button>
                        <button type="submit" class="btn btn-primary">
                            التالي &nbsp;<i class="fas fa-arrow-left"></i>
                        </button>
                    </div>
                </div>
            </form>

            <?php /* ════════════════ STEP 4 — App Settings ════════════ */ elseif ($step === 4):
                $saved = $_SESSION['wiz_app'] ?? [];
            ?>

            <h2 class="step-title"><i class="fas fa-cogs me-2 text-primary"></i>إعدادات التطبيق</h2>
            <p class="step-subtitle">أدخل معلومات موقعك الأساسية</p>

            <form method="POST" id="appForm">
                <input type="hidden" name="_action" value="next4">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">اسم التطبيق <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="app_name"
                               value="<?= htmlspecialchars($saved['name'] ?? 'RSYI Dashboard') ?>"
                               required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">رابط الموقع (App URL) <span class="text-danger">*</span></label>
                        <input type="url" class="form-control" name="app_url"
                               value="<?= htmlspecialchars($saved['url'] ?? $detectedUrl) ?>"
                               placeholder="https://your-domain.com" required>
                        <div class="form-text">الرابط الكامل بدون / في النهاية</div>
                    </div>
                    <div class="col-12">
                        <label class="form-label">المنطقة الزمنية</label>
                        <select class="form-select" name="timezone">
                            <?php foreach ($timezones as $val => $label): ?>
                            <option value="<?= $val ?>"
                                <?= ($saved['tz'] ?? 'Africa/Cairo') === $val ? 'selected' : '' ?>>
                                <?= $label ?>
                            </option>
                            <?php endforeach ?>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label d-block mb-2">بيئة التشغيل</label>
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="env-card d-block position-relative">
                                    <input type="radio" name="app_env" value="production"
                                           <?= ($saved['env'] ?? 'production') === 'production' ? 'checked' : '' ?>>
                                    <div class="icon">🚀</div>
                                    <div class="fw-700">إنتاج</div>
                                    <small class="text-muted">للنشر الفعلي</small>
                                </label>
                            </div>
                            <div class="col-6">
                                <label class="env-card d-block position-relative">
                                    <input type="radio" name="app_env" value="local"
                                           <?= ($saved['env'] ?? '') === 'local' ? 'checked' : '' ?>>
                                    <div class="icon">🛠️</div>
                                    <div class="fw-700">تطوير</div>
                                    <small class="text-muted">للاختبار المحلي</small>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <button type="button" class="btn btn-outline-secondary"
                            onclick="setAction('back'); appForm.submit()">
                        <i class="fas fa-arrow-right me-1"></i>السابق
                    </button>
                    <button type="submit" class="btn btn-primary">
                        التالي &nbsp;<i class="fas fa-arrow-left"></i>
                    </button>
                </div>
            </form>

            <?php /* ════════════════ STEP 5 — Confirm & Install ════════ */ elseif ($step === 5):
                $db  = $_SESSION['wiz_db']  ?? [];
                $app = $_SESSION['wiz_app'] ?? [];
            ?>

            <h2 class="step-title"><i class="fas fa-download me-2 text-primary"></i>تأكيد وتثبيت</h2>
            <p class="step-subtitle">مراجعة الإعدادات — ثم اضغط "ثبّت الآن"</p>

            <div class="summary-box mb-4">
                <div class="row">
                    <div class="col-6 text-muted small">قاعدة البيانات</div>
                    <div class="col-6 fw-700"><?= htmlspecialchars($db['name'] ?? '—') ?></div>

                    <div class="col-6 text-muted small">عنوان السيرفر</div>
                    <div class="col-6 fw-700">
                        <?= htmlspecialchars(($db['host'] ?? '—') . ':' . ($db['port'] ?? '')) ?>
                    </div>

                    <div class="col-6 text-muted small">اسم التطبيق</div>
                    <div class="col-6 fw-700"><?= htmlspecialchars($app['name'] ?? '—') ?></div>

                    <div class="col-6 text-muted small">رابط الموقع</div>
                    <div class="col-6 fw-700" style="word-break:break-all">
                        <?= htmlspecialchars($app['url'] ?? '—') ?>
                    </div>

                    <div class="col-6 text-muted small">المنطقة الزمنية</div>
                    <div class="col-6 fw-700"><?= htmlspecialchars($app['tz'] ?? '—') ?></div>

                    <div class="col-6 text-muted small">بيئة التشغيل</div>
                    <div class="col-6 fw-700"><?= htmlspecialchars($app['env'] ?? '—') ?></div>
                </div>
            </div>

            <form method="POST" id="installForm">
                <input type="hidden" name="_action" value="install">
                <div class="d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-secondary"
                            onclick="setActionOn('installForm','back'); installForm.submit()">
                        <i class="fas fa-arrow-right me-1"></i>السابق
                    </button>
                    <button type="submit" class="btn btn-primary btn-lg" id="installBtn">
                        <i class="fas fa-rocket me-2"></i>ثبّت الآن
                    </button>
                </div>
            </form>

            <?php /* ════════════════ STEP 6 — Done ═════════════════════ */ elseif ($step === 6):
                $result = $_SESSION['install_result'] ?? ['ok' => false, 'log' => []];
                $manualItems = array_filter($result['log'] ?? [], fn($l) => !empty($l['manual']));
            ?>

            <div class="text-center py-3">
                <?php if ($result['ok']): ?>
                <div class="big-icon">✅</div>
                <h2 class="step-title mb-1">تم التثبيت بنجاح!</h2>
                <p class="text-muted mb-4">نظام RSYI Dashboard جاهز الآن للاستخدام</p>
                <?php else: ?>
                <div class="big-icon">⚠️</div>
                <h2 class="step-title mb-1">اكتمل التثبيت مع ملاحظات</h2>
                <p class="text-muted mb-4">راجع الأوامر اليدوية أدناه</p>
                <?php endif ?>

                <!-- Log -->
                <?php if (!empty($result['log'])): ?>
                <div class="text-start mb-4">
                    <?php foreach ($result['log'] as $l): ?>
                    <div class="log-row <?= $l['ok'] ? 'ok' : 'fail' ?>">
                        <i class="fas <?= $l['ok'] ? 'fa-check-circle' : 'fa-times-circle' ?> fs-5"></i>
                        <span class="flex-grow-1"><?= htmlspecialchars($l['label']) ?></span>
                    </div>
                    <?php endforeach ?>
                </div>
                <?php endif ?>

                <!-- Manual commands if shell_exec disabled -->
                <?php if ($manualItems): ?>
                <div class="alert alert-warning text-start mb-4">
                    <strong><i class="fas fa-terminal me-1"></i>أوامر يجب تشغيلها يدوياً على السيرفر:</strong>
                    <?php foreach ($manualItems as $l): ?>
                    <div class="mt-2 p-2 rounded" style="background:#fff3cd; font-family:monospace; font-size:.85rem">
                        $ <?= htmlspecialchars($l['manual']) ?>
                    </div>
                    <?php endforeach ?>
                </div>
                <?php endif ?>

                <div class="d-flex gap-3 justify-content-center flex-wrap">
                    <a href="/" class="btn btn-primary btn-lg">
                        <i class="fas fa-tachometer-alt me-2"></i>الذهاب للوحة التحكم
                    </a>
                    <form method="POST" class="d-inline">
                        <input type="hidden" name="_action" value="reset">
                        <button class="btn btn-outline-secondary">
                            <i class="fas fa-redo me-1"></i>إعادة التثبيت
                        </button>
                    </form>
                </div>
            </div>

            <?php endif ?>

        </div><!-- /.wiz-body -->
    </div><!-- /.wiz-card -->

    <!-- Footer -->
    <p class="text-center text-muted mt-3" style="font-size:.8rem">
        RSYI Dashboard &copy; <?= date('Y') ?> &nbsp;—&nbsp;
        <a href="install.php?reinstall=1" class="text-muted">إعادة التشغيل</a>
    </p>
</div><!-- /.container -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
/* ── Password toggle ─────────────────────────────────── */
function togglePass(id, btn) {
    const inp = document.getElementById(id);
    inp.type  = inp.type === 'password' ? 'text' : 'password';
    btn.querySelector('i').classList.toggle('fa-eye');
    btn.querySelector('i').classList.toggle('fa-eye-slash');
}

/* ── Set hidden _action value ───────────────────────── */
function setAction(val) {
    document.querySelector('[name="_action"]').value = val;
}
function setActionOn(formId, val) {
    document.getElementById(formId).querySelector('[name="_action"]').value = val;
}

/* ── AJAX DB test ────────────────────────────────────── */
async function testDbNow() {
    const st  = document.getElementById('db-status');
    st.className = '';
    st.style.cssText = 'display:block; background:#eff6ff; color:#2563eb; border:1px solid #bfdbfe; border-radius:9px; padding:.6rem 1rem; font-size:.88rem; font-weight:600;';
    st.innerHTML = '<i class="fas fa-circle-notch fa-spin-custom me-1"></i>جاري الاتصال…';

    const fd = new FormData();
    fd.append('_ajax', 'test_db');
    fd.append('host',  document.getElementById('db_host').value);
    fd.append('port',  document.getElementById('db_port').value);
    fd.append('name',  document.getElementById('db_name').value);
    fd.append('user',  document.getElementById('db_user').value);
    fd.append('pass',  document.getElementById('db_pass').value);

    try {
        const res  = await fetch('install.php', { method: 'POST', body: fd });
        const data = await res.json();
        st.style.cssText = '';
        st.className     = data.ok ? 'ok' : 'fail';
        st.innerHTML     = '<i class="fas fa-' + (data.ok ? 'check' : 'times') + '-circle me-1"></i>' + data.msg;
    } catch (e) {
        st.style.cssText = '';
        st.className     = 'fail';
        st.innerHTML     = '<i class="fas fa-times-circle me-1"></i>خطأ في الطلب';
    }
}

/* ── Install spinner ─────────────────────────────────── */
const installForm = document.getElementById('installForm');
if (installForm) {
    installForm.addEventListener('submit', () => {
        const btn     = document.getElementById('installBtn');
        btn.disabled  = true;
        btn.innerHTML = '<i class="fas fa-circle-notch fa-spin-custom me-2"></i>جاري التثبيت…';
    });
}
</script>
</body>
</html>
