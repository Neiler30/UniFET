<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titulo ?? 'UniFET') ?></title>
    <!-- FontAwesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/design-tokens.css">
    <link rel="stylesheet" href="assets/css/base.css">
    <link rel="icon" type="image/png" href="assets/img/isotipo-blanco.png">
    <style>
        .layout-app {
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            width: 250px;
            background-color: var(--color-primario);
            color: var(--color-blanco);
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
        }
        .sidebar-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .sidebar-header img {
            width: 50px;
            margin-bottom: 10px;
        }
        .sidebar-menu {
            list-style: none;
            padding: 10px 0;
            margin: 0;
            flex-grow: 1;
            overflow-y: auto;
        }
        /* Estilos de scrollbar para sidebar */
        .sidebar-menu::-webkit-scrollbar { width: 5px; }
        .sidebar-menu::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 5px; }

        .nav-item {
            margin-bottom: 5px;
        }
        
        .nav-group-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 20px;
            color: rgba(255, 255, 255, 0.7);
            cursor: pointer;
            transition: all 0.3s;
            font-size: 14px;
            font-weight: 500;
        }
        
        .nav-group-header:hover {
            color: var(--color-blanco);
            background-color: rgba(255,255,255,0.05);
        }
        
        .nav-group-header i.icon-left {
            width: 25px;
            text-align: center;
            margin-right: 10px;
            font-size: 16px;
        }
        
        .nav-group-header i.icon-right {
            font-size: 12px;
            transition: transform 0.3s;
        }
        
        .nav-item.active .nav-group-header i.icon-right {
            transform: rotate(90deg);
        }

        .submenu {
            display: none;
            background-color: rgba(0,0,0,0.15);
            padding-left: 0;
            list-style: none;
        }
        
        .nav-item.active .submenu {
            display: block;
            animation: fadeIn 0.3s ease-in-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-5px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .submenu li a {
            display: flex;
            align-items: center;
            padding: 10px 20px 10px 55px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            font-size: 13.5px;
            transition: all 0.2s;
            border-left: 3px solid transparent;
        }
        
        .submenu li a i {
            margin-right: 10px;
            font-size: 12px;
            opacity: 0.7;
        }

        .submenu li a:hover {
            color: var(--color-blanco);
            background-color: rgba(255,255,255,0.1);
        }

        .submenu li a.active {
            color: var(--color-primario);
            background-color: var(--color-blanco);
            border-left-color: var(--color-primario);
            font-weight: 600;
        }
        
        /* Enlace simple (como el Dashboard) */
        .nav-link-simple {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }
        .nav-link-simple i {
            width: 25px;
            text-align: center;
            margin-right: 10px;
            font-size: 16px;
        }
        .nav-link-simple:hover {
            color: var(--color-blanco);
            background-color: rgba(255,255,255,0.05);
        }

        .nav-link-simple.active {
            color: var(--color-primario);
            background-color: var(--color-blanco);
            border-left-color: var(--color-primario);
            font-weight: 600;
        }
        .content {
            flex-grow: 1;
            background-color: var(--color-superficie);
            display: flex;
            flex-direction: column;
        }
        .topbar {
            background-color: var(--color-blanco);
            height: 70px;
            padding: 0 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .topbar-periodo {
            background-color: rgba(15, 157, 88, 0.1);
            color: var(--color-secundario);
            padding: 6px 12px;
            border-radius: 16px;
            font-size: 13px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .topbar-user {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--color-primario);
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: 600;
            font-size: 14px;
        }
        .user-info {
            display: flex;
            flex-direction: column;
            text-align: right;
        }
        .user-info .name { font-weight: 600; font-size: 14px; color: var(--color-oscuro); }
        .user-info .role { font-size: 12px; color: var(--color-texto-secundario); }
        .btn-logout { color: #dc3545; font-size: 13px; text-decoration: none; margin-left: 10px; }
        
        .nav-link.proximamente {
            color: rgba(255,255,255,0.3) !important;
            cursor: not-allowed;
            pointer-events: none;
        }
        .nav-link.proximamente i {
            opacity: 0.3;
        }
        .main-container {
            padding: 30px;
            flex-grow: 1;
        }
        /* Utilidades generales */
        .panel {
            background: var(--color-blanco);
            border-radius: var(--radio-borde);
            padding: 20px;
            box-shadow: var(--sombra-tarjeta);
            margin-bottom: 20px;
        }
        .header-acciones {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        .table th, .table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        .badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        .badge.activo { background-color: var(--color-secundario); color: white; }
        .badge.inactivo { background-color: #dc3545; color: white; }
    </style>
</head>
<body>
    <div class="layout-app">
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const currentParams = new URLSearchParams(window.location.search);
                const currentRuta = currentParams.get('ruta') || 'admin/dashboard';
                
                const links = document.querySelectorAll('.sidebar-menu .nav-link-simple');
                links.forEach(link => {
                    const prefix = link.getAttribute('data-prefix');
                    if (prefix && currentRuta.startsWith(prefix)) {
                        link.classList.add('active');
                    }
                });
            });
        </script>
