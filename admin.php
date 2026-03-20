<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

// Demo data store (in real app: MySQL)
if (!isset($_SESSION['users_db'])) {
    $_SESSION['users_db'] = [
        ['id' => 1, 'login' => 'pierre', 'name' => 'Pierre Dupont', 'pin' => '4872', 'authorized' => true, 'last_access' => '2025-06-10 08:34'],
        ['id' => 2, 'login' => 'marie', 'name' => 'Marie Lambert', 'pin' => '2951', 'authorized' => false, 'last_access' => '2025-06-09 14:12'],
        ['id' => 3, 'login' => 'jean', 'name' => 'Jean Martin', 'pin' => '7364', 'authorized' => true, 'last_access' => '2025-06-10 07:50'],
        ['id' => 4, 'login' => 'sophie', 'name' => 'Sophie Renard', 'pin' => '1539', 'authorized' => true, 'last_access' => '2025-06-08 16:20'],
    ];
}

$message = '';
$messageType = '';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add_user') {
        $newUser = [
            'id' => max(array_column($_SESSION['users_db'], 'id')) + 1,
            'login' => trim($_POST['login']),
            'name' => trim($_POST['name']),
            'pin' => str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT),
            'authorized' => isset($_POST['authorized']),
            'last_access' => 'Jamais',
        ];
        $_SESSION['users_db'][] = $newUser;
        $message = "Utilisateur «{$newUser['name']}» créé avec le PIN {$newUser['pin']}.";
        $messageType = 'success';

    } elseif ($action === 'toggle_auth') {
        $id = (int)$_POST['user_id'];
        foreach ($_SESSION['users_db'] as &$u) {
            if ($u['id'] === $id) {
                $u['authorized'] = !$u['authorized'];
                $status = $u['authorized'] ? 'autorisé' : 'désautorisé';
                $message = "{$u['name']} est maintenant $status.";
                $messageType = $u['authorized'] ? 'success' : 'warning';
                break;
            }
        }

    } elseif ($action === 'delete_user') {
        $id = (int)$_POST['user_id'];
        foreach ($_SESSION['users_db'] as $k => $u) {
            if ($u['id'] === $id) {
                $name = $u['name'];
                unset($_SESSION['users_db'][$k]);
                $_SESSION['users_db'] = array_values($_SESSION['users_db']);
                $message = "Utilisateur «$name» supprimé.";
                $messageType = 'danger';
                break;
            }
        }

    } elseif ($action === 'regen_pin') {
        $id = (int)$_POST['user_id'];
        foreach ($_SESSION['users_db'] as &$u) {
            if ($u['id'] === $id) {
                $u['pin'] = str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);
                $message = "Nouveau PIN pour {$u['name']} : {$u['pin']}";
                $messageType = 'success';
                break;
            }
        }
    }
}

$users = $_SESSION['users_db'];
$total = count($users);
$authorized = count(array_filter($users, fn($u) => $u['authorized']));
$blocked = $total - $authorized;
?>

<!DOCTYPE html>

<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AgroBot — Administration</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Mono:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

```
    :root {
        --soil: #1C110A;
        --bark: #2A1B10;
        --panel: #221508;
        --moss: #3D6B4A;
        --leaf: #5EA85D;
        --sprout: #8FCA8E;
        --sun: #E8C547;
        --cream: #F0E6CE;
        --rust: #C4612D;
        --red: #E05252;
        --border: rgba(240,230,206,0.1);
    }

    html, body { height: 100%; background: var(--soil); font-family: 'DM Mono', monospace; color: var(--cream); }

    /* Layout */
    .layout { display: grid; grid-template-columns: 220px 1fr; min-height: 100vh; }

    /* Sidebar */
    .sidebar {
        background: var(--bark);
        border-right: 1px solid var(--border);
        display: flex; flex-direction: column;
        padding: 0;
        position: sticky; top: 0; height: 100vh;
    }

    .sidebar-logo {
        padding: 24px 20px;
        border-bottom: 1px solid var(--border);
    }

    .sidebar-logo .logo-title {
        font-family: 'Syne', sans-serif;
        font-size: 20px; font-weight: 800;
        color: var(--cream);
    }
    .sidebar-logo .logo-title span { color: var(--leaf); }
    .sidebar-logo .logo-badge {
        font-size: 9px; letter-spacing: 2px; text-transform: uppercase;
        color: var(--leaf); margin-top: 2px;
        background: rgba(94,168,93,0.12);
        display: inline-block; padding: 2px 6px; border-radius: 2px;
    }

    .nav { flex: 1; padding: 16px 0; }
    .nav-label {
        font-size: 8px; letter-spacing: 3px; text-transform: uppercase;
        color: rgba(240,230,206,0.3); padding: 8px 20px 4px;
    }
    .nav-item {
        display: flex; align-items: center; gap: 10px;
        padding: 10px 20px;
        color: rgba(240,230,206,0.5);
        text-decoration: none; font-size: 12px;
        cursor: pointer; border: none; background: none; width: 100%;
        text-align: left; transition: all 0.15s;
    }
    .nav-item:hover, .nav-item.active {
        color: var(--cream);
        background: rgba(240,230,206,0.06);
    }
    .nav-item.active { border-left: 2px solid var(--leaf); }
    .nav-icon { font-size: 14px; width: 18px; text-align: center; }

    .sidebar-foot {
        padding: 16px 20px;
        border-top: 1px solid var(--border);
    }
    .user-chip {
        display: flex; align-items: center; gap: 10px;
    }
    .user-avatar {
        width: 32px; height: 32px;
        background: linear-gradient(135deg, var(--moss), var(--leaf));
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        font-size: 14px;
    }
    .user-info { flex: 1; }
    .user-info .un { font-size: 11px; font-weight: 500; }
    .user-info .ur { font-size: 9px; letter-spacing: 1px; text-transform: uppercase; color: var(--leaf); }
    .logout-btn {
        font-size: 16px; background: none; border: none;
        color: rgba(240,230,206,0.4); cursor: pointer; transition: color 0.15s;
    }
    .logout-btn:hover { color: var(--red); }

    /* Main */
    .main { padding: 32px 40px; overflow-y: auto; }

    .page-header {
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 32px;
    }

    .page-title {
        font-family: 'Syne', sans-serif;
        font-size: 24px; font-weight: 800;
    }

    .page-title .sub {
        font-family: 'DM Mono', monospace;
        font-size: 11px; font-weight: 400;
        color: rgba(240,230,206,0.4);
        margin-top: 2px; letter-spacing: 1px;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--moss), var(--leaf));
        border: none; border-radius: 4px;
        padding: 10px 20px;
        color: #fff; font-family: 'Syne', sans-serif;
        font-size: 12px; font-weight: 700; letter-spacing: 1px;
        text-transform: uppercase; cursor: pointer;
        transition: all 0.2s;
    }
    .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(74,124,89,0.4); }

    /* Stats */
    .stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 32px; }

    .stat-card {
        background: var(--bark);
        border: 1px solid var(--border);
        border-radius: 6px; padding: 20px 24px;
        position: relative; overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute; top: 0; left: 0; right: 0; height: 2px;
    }
    .stat-card.green::before { background: linear-gradient(90deg, var(--moss), var(--leaf)); }
    .stat-card.yellow::before { background: linear-gradient(90deg, #b8902a, var(--sun)); }
    .stat-card.red::before { background: linear-gradient(90deg, #a03535, var(--red)); }

    .stat-num {
        font-family: 'Syne', sans-serif;
        font-size: 36px; font-weight: 800; line-height: 1;
    }
    .stat-card.green .stat-num { color: var(--leaf); }
    .stat-card.yellow .stat-num { color: var(--sun); }
    .stat-card.red .stat-num { color: var(--red); }

    .stat-label {
        font-size: 10px; letter-spacing: 2px;
        text-transform: uppercase; color: rgba(240,230,206,0.4);
        margin-top: 6px;
    }

    .stat-icon {
        position: absolute; right: 20px; top: 50%;
        transform: translateY(-50%);
        font-size: 32px; opacity: 0.15;
    }

    /* Message */
    .flash {
        padding: 12px 16px; border-radius: 4px;
        font-size: 12px; margin-bottom: 24px;
        display: flex; align-items: center; gap: 8px;
        animation: fadeIn 0.3s ease;
    }
    @keyframes fadeIn { from { opacity:0; transform: translateY(-8px); } to { opacity:1; transform:none; } }
    .flash.success { background: rgba(94,168,93,0.15); border: 1px solid rgba(94,168,93,0.3); color: var(--sprout); }
    .flash.warning { background: rgba(232,197,71,0.12); border: 1px solid rgba(232,197,71,0.3); color: var(--sun); }
    .flash.danger { background: rgba(224,82,82,0.12); border: 1px solid rgba(224,82,82,0.3); color: #f08080; }

    /* Table */
    .section-header {
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 16px;
    }
    .section-title {
        font-family: 'Syne', sans-serif;
        font-size: 14px; font-weight: 700;
        letter-spacing: 0.5px;
    }
    .section-count {
        background: rgba(240,230,206,0.08);
        padding: 3px 10px; border-radius: 20px;
        font-size: 11px; color: rgba(240,230,206,0.5);
    }

    .table-wrap {
        background: var(--bark);
        border: 1px solid var