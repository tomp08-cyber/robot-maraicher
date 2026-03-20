<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'user') {
    header('Location: index.php');
    exit;
}

$name = $_SESSION['name'];
$pin = $_SESSION['pin'];
$authorized = $_SESSION['authorized'];
?>

<!DOCTYPE html>

<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AgroBot — Mon compte</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Mono:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

```
    :root {
        --soil: #141A10;
        --bark: #1A2215;
        --panel: #111710;
        --moss: #3D6B4A;
        --leaf: #5EA85D;
        --sprout: #8FCA8E;
        --sun: #E8C547;
        --cream: #EDF4E8;
        --red: #E05252;
        --border: rgba(237,244,232,0.1);
    }

    html, body {
        min-height: 100%; background: var(--soil);
        font-family: 'DM Mono', monospace; color: var(--cream);
    }

    .layout { display: grid; grid-template-columns: 220px 1fr; min-height: 100vh; }

    .sidebar {
        background: var(--bark);
        border-right: 1px solid var(--border);
        display: flex; flex-direction: column;
        position: sticky; top: 0; height: 100vh;
    }

    .sidebar-logo { padding: 24px 20px; border-bottom: 1px solid var(--border); }
    .logo-title { font-family: 'Syne', sans-serif; font-size: 20px; font-weight: 800; }
    .logo-title span { color: var(--leaf); }
    .logo-badge {
        font-size: 9px; letter-spacing: 2px; text-transform: uppercase;
        color: rgba(237,244,232,0.4); margin-top: 2px;
    }

    .nav { flex: 1; padding: 16px 0; }
    .nav-label { font-size: 8px; letter-spacing: 3px; text-transform: uppercase; color: rgba(237,244,232,0.25); padding: 8px 20px 4px; }
    .nav-item {
        display: flex; align-items: center; gap: 10px;
        padding: 10px 20px; color: rgba(237,244,232,0.5);
        text-decoration: none; font-size: 12px; border-left: 2px solid transparent;
    }
    .nav-item.active { color: var(--cream); border-left-color: var(--leaf); background: rgba(237,244,232,0.04); }

    .sidebar-foot { padding: 16px 20px; border-top: 1px solid var(--border); }
    .user-chip { display: flex; align-items: center; gap: 10px; }
    .user-avatar {
        width: 32px; height: 32px;
        background: linear-gradient(135deg, var(--moss), var(--leaf));
        border-radius: 8px; display: flex; align-items: center; justify-content: center;
        font-size: 14px;
    }
    .user-info .un { font-size: 11px; font-weight: 500; }
    .user-info .ur { font-size: 9px; letter-spacing: 1px; text-transform: uppercase; color: var(--leaf); }
    .logout-btn { background: none; border: none; color: rgba(237,244,232,0.35); cursor: pointer; font-size: 16px; transition: color 0.15s; }
    .logout-btn:hover { color: var(--red); }

    .main { padding: 40px; }

    .page-header { margin-bottom: 36px; }
    .page-title { font-family: 'Syne', sans-serif; font-size: 26px; font-weight: 800; }
    .page-sub { font-size: 11px; color: rgba(237,244,232,0.35); letter-spacing: 1px; margin-top: 4px; }

    /* Status hero */
    .status-hero {
        border-radius: 10px;
        padding: 36px 40px;
        margin-bottom: 32px;
        position: relative; overflow: hidden;
    }
    .status-hero.authorized {
        background: linear-gradient(135deg, rgba(61,107,74,0.4), rgba(94,168,93,0.15));
        border: 1px solid rgba(94,168,93,0.3);
    }
    .status-hero.blocked {
        background: linear-gradient(135deg, rgba(100,30,30,0.4), rgba(224,82,82,0.1));
        border: 1px solid rgba(224,82,82,0.25);
    }

    .status-hero::before {
        content: '';
        position: absolute; right: -20px; top: -20px;
        width: 200px; height: 200px;
        border-radius: 50%;
        opacity: 0.06;
    }
    .status-hero.authorized::before { background: var(--leaf); }
    .status-hero.blocked::before { background: var(--red); }

    .sh-label { font-size: 10px; letter-spacing: 3px; text-transform: uppercase; opacity: 0.6; margin-bottom: 8px; }
    .sh-status {
        font-family: 'Syne', sans-serif; font-size: 32px; font-weight: 800;
        display: flex; align-items: center; gap: 12px;
    }
    .sh-dot {
        width: 14px; height: 14px; border-radius: 50%;
    }
    .authorized .sh-dot { background: var(--leaf); box-shadow: 0 0 12px rgba(94,168,93,0.7); animation: pulse 2s infinite; }
    .blocked .sh-dot { background: var(--red); }
    @keyframes pulse { 0%,100%{transform:scale(1)} 50%{transform:scale(1.15)} }

    .sh-desc { font-size: 13px; opacity: 0.6; margin-top: 8px; }

    /* Grid cards */
    .cards { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 32px; }

    .card {
        background: var(--bark);
        border: 1px solid var(--border);
        border-radius: 8px; padding: 24px 28px;
    }

    .card-label {
        font-size: 9px; letter-spacing: 3px; text-transform: uppercase;
        color: rgba(237,244,232,0.3); margin-bottom: 16px;
        display: flex; align-items: center; gap: 6px;
    }

    .pin-display {
        display: flex; gap: 10px; justify-content: center;
        padding: 24px 0;
    }

    .pin-digit {
        width: 52px; height: 64px;
        background: rgba(237,244,232,0.05);
        border: 2px solid rgba(94,168,93,0.25);
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        font-family: 'Syne', sans-serif; font-size: 28px; font-weight: 800;
        color: var(--sun);
        transition: all 0.3s;
    }

    .pin-hint {
        text-align: center; font-size: 10px;
        color: rgba(237,244,232,0.3); letter-spacing: 1px; margin-top: 8px;
    }

    .toggle-btn {
        display: block; width: 100%; margin-top: 16px;
        background: rgba(237,244,232,0.05); border: 1px solid var(--border);
        border-radius: 4px; padding: 8px;
        color: rgba(237,244,232,0.4); font-size: 11px;
        font-family: 'DM Mono', monospace; cursor: pointer; transition: all 0.15s;
    }
    .toggle-btn:hover { background: rgba(237,244,232,0.08); color: var(--cream); }

    /* Robot distance viz */
    .distance-viz {
        padding: 16px 0;
    }

    .dv-track {
        position: relative; height: 60px;
        background: rgba(237,244,232,0.04);
        border-radius: 4px; overflow: hidden;
        border: 1px solid var(--border);
    }

    .dv-zones {
        display: flex; height: 100%;
    }
    .dv-zone { height: 100%; display: flex; align-items: center; justify-content: center; font-size: 9px; letter-spacing: 1px; }
    .dv-zone.stop { width: 25%; background: rgba(224,82,82,0.2); color: var(--red); }
    .dv-zone.ok { width: 20%; background: rgba(94,168,93,0.15); color: var(--leaf); }
    .dv-zone.advance { width: 30%; background: rgba(232,197,71,0.1); color: var(--sun); }
    .dv-zone.far { width: 25%; background: rgba(224,82,82,0.1); color: rgba(237,244,232,0.3); }

    .dv-labels { display: flex; margin-top: 6px; }
    .dv-label { font-size: 9px; color: rgba(237,244,232,0.3); letter-spacing: 1px; }
    .dv-label:nth-child(1) { width: 25%; }
    .dv-label:nth-child(2) { width: 20%; }
    .dv-label:nth-child(3) { width: 30%; }
    .dv-label:nth-child(4) { width: 25%; text-align: right; }

    .dv-ruler { display: flex; justify-content: space-between; margin-top: 4px; }
    .dv-r { font-size: 9px; color: rgba(237,244,232,0.25); }

    /* Account info */
    .info-list { }
    .info-row {
        display: flex; justify-content: space-between; align-items: center;
        padding: 12px 0; border-bottom: 1px solid var(--border);
        font-size: 12px;
    }
    .info-row:last-child { border-bottom: none; }
    .ir-key { color: rgba(237,244,232,0.4); font-size: 10px; letter-spacing: 1px; text-transform: uppercase; }
    .ir-val { font-weight: 500; }
    .ir-val.active { color: var(--leaf); }
    .ir-val.inactive { color: var(--red); }

    /* Instructions */
    .instructions {
        background: var(--bark);
        border: 1px solid var(--border);
        border-radius: 8px; padding: 24px 28px;
        margin-bottom: 20px;
    }

    .inst-title {
        font-family: 'Syne', sans-serif; font-size: 14px; font-weight: 700;
        margin-bottom: 20px; display: flex; align-items: center; gap: 8px;
    }

    .steps { display: flex; flex-direction: column; gap: 12px; }
    .step { display: flex; gap: 16px; align-items: flex-start; }
    .step-num {
        width: 24px; height: 24px; border-radius: 50%; flex-shrink: 0;
        background: rgba(94,168,93,0.15); border: 1px solid rgba(94,168,93,0.3);
        display: flex; align-items: center; justify-content: center;
        font-size: 10px; color: var(--leaf); font-weight: 700;
    }
    .step-text { font-size: 12px; line-height: 1.6; color: rgba(237,244,232,0.7); padding-top: 2px; }
    .step-text strong { color: var(--cream); }
</style>
```

</head>
<body>
<div class="layout">
    <aside class="sidebar">
        <div class="sidebar-logo">
            <div class="logo-title">Agro<span>Bot</span></div>
            <div class="logo-badge">Espace utilisateur</div>
        </div>
        <nav class="nav">
            <div class="nav-label">Navigation</div>
            <a class="nav-item active" href="user.php"><span>🌿</span> Mon compte</a>
            <a class="nav-item" href="user.php"><span>📊</span> Historique</a>
        </nav>
        <div class="sidebar-foot">
            <div class="user-chip">
                <div class="user-avatar">🌾</div>
                <div class="user-info">
                    <div class="un"><?= htmlspecialchars($name) ?></div>
                    <div class="ur">Maraîcher</div>
                </div>
                <a href="logout.php"><button class="logout-bt