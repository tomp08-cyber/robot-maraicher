<?php
session_start();

// Redirect if already logged in
if (isset($_SESSION['user'])) {
    if ($_SESSION['role'] === 'admin') {
        header('Location: admin.php');
    } else {
        header('Location: user.php');
    }
    exit;
}

$error = '';

// Demo credentials (in real app, use DB)
$users = [
    'admin' => ['password' => 'admin123', 'role' => 'admin', 'name' => 'Administrateur'],
    'pierre' => ['password' => 'pierre123', 'role' => 'user', 'name' => 'Pierre Dupont', 'pin' => '4872', 'authorized' => true],
    'marie' => ['password' => 'marie123', 'role' => 'user', 'name' => 'Marie Lambert', 'pin' => '2951', 'authorized' => false],
    'jean' => ['password' => 'jean123', 'role' => 'user', 'name' => 'Jean Martin', 'pin' => '7364', 'authorized' => true],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';

    if (isset($users[$login]) && $users[$login]['password'] === $password) {
        $_SESSION['user'] = $login;
        $_SESSION['role'] = $users[$login]['role'];
        $_SESSION['name'] = $users[$login]['name'];
        if ($users[$login]['role'] === 'user') {
            $_SESSION['pin'] = $users[$login]['pin'];
            $_SESSION['authorized'] = $users[$login]['authorized'];
        }
        if ($_SESSION['role'] === 'admin') {
            header('Location: admin.php');
        } else {
            header('Location: user.php');
        }
        exit;
    } else {
        $error = 'Identifiant ou mot de passe incorrect.';
    }
}
?>

<!DOCTYPE html>

<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AgroBot — Connexion</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Mono:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

```
    :root {
        --soil: #2C1A0E;
        --bark: #3D2B1F;
        --moss: #4A7C59;
        --leaf: #6DB56C;
        --sprout: #A8D5A2;
        --sun: #E8C547;
        --cream: #F5EDD8;
        --rust: #C4612D;
        --fog: rgba(245,237,216,0.06);
    }

    html, body {
        height: 100%;
        background-color: var(--soil);
        font-family: 'DM Mono', monospace;
        color: var(--cream);
        overflow: hidden;
    }

    /* Animated background */
    .bg {
        position: fixed; inset: 0;
        background:
            radial-gradient(ellipse 80% 60% at 20% 80%, rgba(74,124,89,0.25) 0%, transparent 60%),
            radial-gradient(ellipse 60% 40% at 80% 20%, rgba(168,213,162,0.12) 0%, transparent 50%),
            radial-gradient(ellipse 100% 80% at 50% 50%, rgba(44,26,14,1) 40%, #1a0e06 100%);
        z-index: 0;
    }

    .grid-overlay {
        position: fixed; inset: 0;
        background-image:
            linear-gradient(rgba(168,213,162,0.04) 1px, transparent 1px),
            linear-gradient(90deg, rgba(168,213,162,0.04) 1px, transparent 1px);
        background-size: 40px 40px;
        z-index: 0;
        animation: gridShift 20s linear infinite;
    }

    @keyframes gridShift {
        0% { transform: translate(0,0); }
        100% { transform: translate(40px, 40px); }
    }

    /* Floating particles */
    .particles { position: fixed; inset: 0; z-index: 0; pointer-events: none; }
    .particle {
        position: absolute;
        width: 3px; height: 3px;
        background: var(--leaf);
        border-radius: 50%;
        animation: float linear infinite;
        opacity: 0;
    }
    @keyframes float {
        0% { transform: translateY(100vh) rotate(0deg); opacity: 0; }
        10% { opacity: 0.6; }
        90% { opacity: 0.3; }
        100% { transform: translateY(-10vh) rotate(720deg); opacity: 0; }
    }

    .container {
        position: relative; z-index: 1;
        display: flex;
        height: 100vh;
        align-items: center;
        justify-content: center;
    }

    .login-box {
        width: 420px;
        background: rgba(30,18,10,0.85);
        border: 1px solid rgba(168,213,162,0.2);
        border-radius: 4px;
        padding: 48px 40px;
        backdrop-filter: blur(20px);
        box-shadow:
            0 0 0 1px rgba(168,213,162,0.05),
            0 40px 80px rgba(0,0,0,0.6),
            inset 0 1px 0 rgba(168,213,162,0.1);
        animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) both;
    }

    @keyframes slideUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .logo-area {
        text-align: center;
        margin-bottom: 36px;
    }

    .logo-icon {
        width: 64px; height: 64px;
        margin: 0 auto 16px;
        background: linear-gradient(135deg, var(--moss), var(--leaf));
        border-radius: 16px;
        display: flex; align-items: center; justify-content: center;
        font-size: 28px;
        box-shadow: 0 8px 24px rgba(74,124,89,0.4);
        animation: pulse 3s ease-in-out infinite;
    }

    @keyframes pulse {
        0%, 100% { box-shadow: 0 8px 24px rgba(74,124,89,0.4); }
        50% { box-shadow: 0 8px 32px rgba(109,181,108,0.6); }
    }

    .logo-title {
        font-family: 'Syne', sans-serif;
        font-size: 28px;
        font-weight: 800;
        letter-spacing: -0.5px;
        color: var(--cream);
    }

    .logo-title span { color: var(--leaf); }

    .logo-sub {
        font-size: 10px;
        letter-spacing: 3px;
        text-transform: uppercase;
        color: rgba(245,237,216,0.4);
        margin-top: 4px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    label {
        display: block;
        font-size: 10px;
        letter-spacing: 2px;
        text-transform: uppercase;
        color: rgba(245,237,216,0.5);
        margin-bottom: 8px;
    }

    input {
        width: 100%;
        background: rgba(245,237,216,0.05);
        border: 1px solid rgba(245,237,216,0.12);
        border-radius: 4px;
        padding: 13px 16px;
        color: var(--cream);
        font-family: 'DM Mono', monospace;
        font-size: 14px;
        outline: none;
        transition: all 0.2s;
    }

    input:focus {
        border-color: var(--leaf);
        background: rgba(109,181,108,0.08);
        box-shadow: 0 0 0 3px rgba(109,181,108,0.12);
    }

    input::placeholder { color: rgba(245,237,216,0.2); }

    .btn {
        width: 100%;
        padding: 14px;
        background: linear-gradient(135deg, var(--moss), var(--leaf));
        border: none;
        border-radius: 4px;
        color: #fff;
        font-family: 'Syne', sans-serif;
        font-size: 14px;
        font-weight: 700;
        letter-spacing: 1px;
        text-transform: uppercase;
        cursor: pointer;
        margin-top: 8px;
        transition: all 0.2s;
        position: relative;
        overflow: hidden;
    }

    .btn::before {
        content: '';
        position: absolute; inset: 0;
        background: linear-gradient(135deg, transparent, rgba(255,255,255,0.1));
        opacity: 0;
        transition: opacity 0.2s;
    }

    .btn:hover { transform: translateY(-1px); box-shadow: 0 8px 20px rgba(74,124,89,0.4); }
    .btn:hover::before { opacity: 1; }
    .btn:active { transform: translateY(0); }

    .error {
        background: rgba(196,97,45,0.15);
        border: 1px solid rgba(196,97,45,0.4);
        border-radius: 4px;
        padding: 12px 16px;
        font-size: 12px;
        color: #f4a06b;
        margin-bottom: 20px;
        display: flex; align-items: center; gap: 8px;
    }

    .demo-hints {
        margin-top: 28px;
        padding-top: 20px;
        border-top: 1px solid rgba(245,237,216,0.08);
    }

    .demo-label {
        font-size: 9px;
        letter-spacing: 2px;
        text-transform: uppercase;
        color: rgba(245,237,216,0.3);
        text-align: center;
        margin-bottom: 12px;
    }

    .demo-grid {
        display: grid; grid-template-columns: 1fr 1fr; gap: 8px;
    }

    .demo-item {
        background: rgba(245,237,216,0.04);
        border: 1px solid rgba(245,237,216,0.08);
        border-radius: 4px;
        padding: 10px 12px;
        cursor: pointer;
        transition: all 0.15s;
    }

    .demo-item:hover {
        background: rgba(109,181,108,0.1);
        border-color: rgba(109,181,108,0.3);
    }

    .demo-item .di-role {
        font-size: 8px;
        letter-spacing: 2px;
        text-transform: uppercase;
        color: var(--leaf);
        margin-bottom: 3px;
    }

    .demo-item .di-creds {
        font-size: 11px;
        color: rgba(245,237,216,0.6);
    }

    .status-bar {
        position: fixed;
        bottom: 0; left: 0; right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--moss), var(--leaf), var(--sun));
        z-index: 10;
        animation: shimmer 3s ease-in-out infinite;
    }

    @keyframes shimmer {
        0%, 100% { opacity: 0.6; }
        50% { opacity: 1; }
    }
</style>
```

</head>
<body>
    <div class="bg"></div>
    <div class="grid-overlay"></div>
    <div class="particles" id="particles"></div>

```
<div class="container">
    <div class="login-box">
        <div class="logo-area">
            <div class="logo-icon">🤖</div>
            <div class="logo-title">Agro<span>Bot</span></div>
            <div class="logo-sub">Robot maraîcher connecté</div>
        </div>

        <?php if ($error): ?>
        <div class="error">⚠ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="login">Identifiant</label>
                <input type="text" id="login" name="login" placeholder="Entrez votre identifiant" autocomplete="username" value="<?= htmlspecialchars($_POST['login'] ?? '') ?>">
            </div>
       