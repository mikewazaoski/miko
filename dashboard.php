<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Ekim's Flower Shop</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary-color: #ec4899;
            --primary-hover: #db2777;
            --primary-light: #fce7f3;
            --secondary-color: #f97316;
            --text-color: #1f2937;
            --text-light: #6b7280;
            --text-xlight: #9ca3af;
            --bg-light: #fef7ff;
            --bg-white: #ffffff;
            --bg-dark: #111827;
            --border-color: #f3e8ff;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --shadow-sm: 0 1px 2px 0 rgba(236, 72, 153, 0.1);
            --shadow: 0 4px 6px -1px rgba(236, 72, 153, 0.15), 0 2px 4px -1px rgba(236, 72, 153, 0.1);
            --shadow-lg: 0 10px 15px -3px rgba(236, 72, 153, 0.2), 0 4px 6px -2px rgba(236, 72, 153, 0.15);
            --rounded: 0.5rem;
            --rounded-lg: 1rem;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Nunito', sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #fef7ff 0%, #fce7f3 100%);
            color: var(--text-color);
            display: flex;
            min-height: 100vh;
            position: relative;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                radial-gradient(circle at 20% 20%, rgba(236, 72, 153, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(249, 115, 22, 0.1) 0%, transparent 50%);
            pointer-events: none;
            z-index: -1;
        }
        
        /* Sidebar Styles */
        .sidebar {
            width: 280px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-right: 1px solid rgba(236, 72, 153, 0.2);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            z-index: 40;
            transition: all 0.3s ease;
            box-shadow: var(--shadow);
        }
        
        .sidebar-logo {
            padding: 1.5rem;
            display: flex;
            align-items: center;
            border-bottom: 1px solid var(--border-color);
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
        }
        
        .logo-icon {
            font-size: 1.75rem;
            color: white;
            margin-right: 0.75rem;
        }
        
        .logo-text {
            font-size: 1.25rem;
            font-weight: 700;
            color: white;
        }
        
        .nav-items {
            padding: 1.5rem 0;
            flex-grow: 1;
            overflow-y: auto;
        }
        
        .nav-section {
            margin-bottom: 1.5rem;
        }
        
        .nav-section-title {
            padding: 0 1.5rem;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
            letter-spacing: 0.05em;
        }
        
        .nav-item {
            padding: 0.75rem 1.5rem;
            display: flex;
            align-items: center;
            text-decoration: none;
            color: var(--text-color);
            border-left: 3px solid transparent;
            transition: all 0.2s ease;
            position: relative;
        }
        
        .nav-item:hover {
            background: var(--primary-light);
            color: var(--primary-color);
            border-left-color: var(--primary-color);
        }
        
        .nav-item.active {
            background: var(--primary-light);
            color: var(--primary-color);
            border-left-color: var(--primary-color);
            font-weight: 600;
        }
        
        .nav-icon {
            margin-right: 0.75rem;
            font-size: 1.1rem;
            min-width: 1.5rem;
            text-align: center;
        }
        
        .sidebar-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid var(--border-color);
        }
        
        .logout-btn {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            background: var(--primary-light);
            color: var(--primary-color);
            border: none;
            border-radius: var(--rounded);
            cursor: pointer;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s ease;
            width: 100%;
            justify-content: center;
        }
        
        .logout-btn:hover {
            background: var(--primary-color);
            color: white;
        }
        
        .logout-icon {
            margin-right: 0.5rem;
        }
        
        /* Main Content Styles */
        .main-content {
            flex-grow: 1;
            margin-left: 280px;
            display: flex;
            flex-direction: column;
        }
        
        .topbar {
            padding: 1rem 2rem;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(236, 72, 153, 0.2);
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 30;
            box-shadow: var(--shadow-sm);
        }
        
        .menu-toggle {
            display: none;
            background: none;
            border: none;
            color: var(--text-color);
            font-size: 1.25rem;
            cursor: pointer;
        }
        
        .page-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
            text-shadow: 0 2px 4px rgba(236, 72, 153, 0.1);
        }
        
        .topbar-right {
            display: flex;
            align-items: center;
        }
        
        .notification-bell {
            position: relative;
            margin-right: 1.5rem;
            color: var(--primary-color);
            font-size: 1.25rem;
            cursor: pointer;
            transition: color 0.2s ease;
        }
        
        .notification-bell:hover {
            color: var(--primary-hover);
        }
        
        .notification-indicator {
            position: absolute;
            top: -5px;
            right: -5px;
            background: var(--secondary-color);
            width: 18px;
            height: 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 0.7rem;
            font-weight: 700;
        }
        
        .user-profile {
            display: flex;
            align-items: center;
            cursor: pointer;
            position: relative;
            padding: 0.5rem;
            border-radius: var(--rounded);
            transition: background 0.2s ease;
        }
        
        .user-profile:hover {
            background: var(--primary-light);
        }
        
        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.1rem;
            margin-right: 0.75rem;
        }
        
        .user-info {
            display: flex;
            flex-direction: column;
        }
        
        .user-name {
            font-weight: 600;
            color: var(--text-color);
        }
        
        .user-role {
            font-size: 0.75rem;
            color: var(--primary-color);
        }
        
        .dropdown-icon {
            margin-left: 0.5rem;
            color: var(--primary-color);
            transition: transform 0.2s ease;
        }
        
        .user-profile:hover .dropdown-icon {
            transform: rotate(180deg);
        }
        
        /* Dashboard Content Styles */
        .content {
            padding: 2rem;
            flex-grow: 1;
        }
        
        .welcome-section {
            margin-bottom: 1.5rem;
        }
        
        .welcome-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: var(--primary-color);
            text-shadow: 0 2px 4px rgba(236, 72, 153, 0.1);
        }
        
        .welcome-subtitle {
            color: var(--text-light);
            font-size: 1rem;
            margin-bottom: 1rem;
        }
        
        /* Flower Stock Section */
        .stock-overview {
            margin-top: 1.5rem;
            padding: 1.5rem;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: var(--rounded-lg);
            box-shadow: var(--shadow);
            border: 1px solid rgba(236, 72, 153, 0.2);
        }

        .stock-overview-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 1.25rem;
            color: var(--primary-color);
            display: flex;
            align-items: center;
        }

        .stock-overview-title i {
            margin-right: 0.5rem;
            color: var(--primary-color);
        }

        .stock-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1rem;
        }

        .stock-item {
            background: rgba(255, 255, 255, 0.95);
            border-radius: var(--rounded);
            box-shadow: var(--shadow-sm);
            display: flex;
            overflow: hidden;
            transition: all 0.2s ease;
            border-left: 4px solid var(--border-color);
        }

        .stock-item:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow);
        }

        .stock-item.critical {
            border-left-color: #ef4444;
        }

        .stock-item.warning {
            border-left-color: #f59e0b;
        }

        .stock-item.normal {
            border-left-color: var(--primary-color);
        }

        .stock-item.featured {
            grid-column: span 2;
            transform: scale(1.05);
            z-index: 10;
            box-shadow: var(--shadow-lg);
        }

        .stock-item.featured:hover {
            transform: translateY(-3px) scale(1.05);
        }

        .stock-item.featured .stock-level-indicator {
            padding: 1.5rem;
            font-size: 1.25rem;
        }

        .stock-item.featured .stock-level-indicator i {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .stock-item.featured .stock-name {
            font-size: 1.25rem;
        }

        .stock-level-indicator {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 0.75rem;
            color: white;
            font-weight: 700;
            min-width: 60px;
            background: var(--primary-color);
        }

        .stock-level-indicator i {
            font-size: 1.25rem;
            margin-bottom: 0.25rem;
        }

        .stock-details {
            padding: 1rem;
            flex-grow: 1;
            display: flex;
            align-items: center;
        }

        .stock-name {
            font-weight: 600;
            color: var(--text-color);
            margin: 0;
        }

        .stock-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.75rem;
            font-size: 0.875rem;
            color: var(--text-light);
        }

        .stock-actions {
            display: flex;
            justify-content: space-between;
            gap: 0.5rem;
        }

        .btn-add-stock, .btn-view-product {
            padding: 0.4rem 0.75rem;
            border-radius: var(--rounded);
            font-size: 0.75rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .btn-add-stock {
            background: var(--primary-color);
            color: white;
            flex-grow: 1;
            text-align: center;
        }

        .btn-add-stock:hover {
            background: var(--primary-hover);
        }

        .btn-view-product {
            background: var(--primary-light);
            color: var(--primary-color);
        }

        .btn-view-product:hover {
            background: var(--border-color);
        }

        .stock-overview-footer {
            margin-top: 1.5rem;
            text-align: center;
        }

        .view-all-btn {
            display: inline-block;
            padding: 0.5rem 1rem;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: var(--rounded);
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .view-all-btn:hover {
            background: var(--primary-hover);
        }

        .no-products-alert {
            margin-top: 1.5rem;
            padding: 1rem;
            background: var(--primary-light);
            border-radius: var(--rounded);
            display: flex;
            align-items: center;
            color: var(--primary-color);
            border: 1px solid rgba(236, 72, 153, 0.3);
        }

        .no-products-alert i {
            font-size: 1.5rem;
            margin-right: 1rem;
        }

        .no-products-alert a {
            color: var(--primary-color);
            font-weight: 600;
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .stock-grid {
                grid-template-columns: 1fr;
            }
            
            .stock-item.featured {
                grid-column: span 1;
            }
        }
        
        /* Dashboard Cards */
        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: var(--rounded-lg);
            box-shadow: var(--shadow);
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            border: 1px solid rgba(236, 72, 153, 0.2);
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .card-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .card-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        
        .card-icon-products {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
        }
        
        .card-icon-suppliers {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
        }
        
        .card-icon-stock {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
        }
        
        .card-icon-sales {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
        }
        
        .card-content {
            margin-top: auto;
        }
        
        .card-description {
            color: var(--text-light);
            font-size: 0.875rem;
            margin-bottom: 1rem;
        }
        
        .card-action {
            display: inline-block;
            padding: 0.5rem 1rem;
            background: var(--primary-color);
            color: white;
            text-decoration: none;
            border-radius: var(--rounded);
            font-weight: 600;
            font-size: 0.875rem;
            transition: all 0.2s ease;
        }
        
        .card-action:hover {
            background: var(--primary-hover);
            transform: translateY(-1px);
        }
        
        /* Media Queries for Responsive Design */
        @media (max-width: 1024px) {
            .dashboard-cards {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                box-shadow: var(--shadow-lg);
            }
            
            .sidebar.open {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .menu-toggle {
                display: flex;
                margin-right: 1rem;
            }
            
            .topbar {
                padding: 1rem;
            }
            
            .dashboard-cards {
                grid-template-columns: 1fr;
            }
            
            .low-stock-grid {
                grid-template-columns: 1fr;
            }
            
            .low-stock-item.critical {
                grid-column: 1;
                transform: scale(1);
            }
            
            .low-stock-item.critical:hover {
                transform: translateY(-5px);
            }
        }
        
        @media (max-width: 480px) {
            .content {
                padding: 1.5rem 1rem;
            }
            
            .user-info {
                display: none;
            }
        }
        
        .glass-circle {
            position: absolute;
            border-radius: 50%;
            background: rgba(236, 72, 153, 0.1);
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
            z-index: -1;
        }

        .glass-circle-1 {
            top: -50px;
            left: -50px;
            width: 150px;
            height: 150px;
        }

        .glass-circle-2 {
            bottom: -70px;
            right: -70px;
            width: 200px;
            height: 200px;
        }

        /* Loading overlay styles */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(236, 72, 153, 0.8);
            backdrop-filter: blur(5px);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }

        .loading-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .loading-spinner {
            width: 70px;
            height: 70px;
            border: 5px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
            margin-bottom: 15px;
        }

        .loading-text {
            color: white;
            font-size: 1.2rem;
            font-weight: 600;
            margin-top: 15px;
            text-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
        }

        .loading-progress {
            width: 200px;
            height: 6px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 3px;
            margin-top: 15px;
            overflow: hidden;
            position: relative;
        }

        .loading-progress-bar {
            height: 100%;
            width: 0%;
            background: linear-gradient(90deg, white, rgba(255, 255, 255, 0.8));
            border-radius: 3px;
            transition: width 2s ease;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 2rem 1.5rem;
            }
        }

        /* Floating flower decorations */
        .flower-decoration {
            position: fixed;
            pointer-events: none;
            z-index: -1;
            opacity: 0.1;
            animation: float 6s ease-in-out infinite;
        }

        .flower-decoration:nth-child(1) {
            top: 10%;
            right: 10%;
            color: var(--primary-color);
            font-size: 2rem;
            animation-delay: 0s;
        }

        .flower-decoration:nth-child(2) {
            top: 30%;
            left: 5%;
            color: var(--secondary-color);
            font-size: 1.5rem;
            animation-delay: 2s;
        }

        .flower-decoration:nth-child(3) {
            bottom: 20%;
            right: 20%;
            color: var(--primary-color);
            font-size: 2.5rem;
            animation-delay: 4s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
    </style>
</head>
<body>
    <!-- Floating flower decorations -->
    <div class="flower-decoration"><i class="fas fa-seedling"></i></div>
    <div class="flower-decoration"><i class="fas fa-leaf"></i></div>
    <div class="flower-decoration"><i class="fas fa-spa"></i></div>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-logo">
            <div class="logo-icon"><i class="fas fa-seedling"></i></div>
            <div class="logo-text">Ekim's Flower Shop</div>
        </div>
        
        <nav class="nav-items">
            <div class="nav-section">
                <div class="nav-section-title">Main</div>
                <a href="dashboard.php" class="nav-item active">
                    <i class="nav-icon fas fa-home"></i> Dashboard
                </a>
            </div>
            
            <div class="nav-section">
                <div class="nav-section-title">Flower Inventory</div>
                <a href="products/index.php" class="nav-item">
                    <i class="nav-icon fas fa-leaf"></i> Flowers & Plants
                </a>
                <a href="suppliers/index.php" class="nav-item">
                    <i class="nav-icon fas fa-truck"></i> Suppliers
                </a>
                <a href="stock/index.php" class="nav-item">
                    <i class="nav-icon fas fa-warehouse"></i> Stock Management
                </a>
            </div>
            
            <div class="nav-section">
                <div class="nav-section-title">Sales & Reports</div>
                <a href="sales/index.php" class="nav-item">
                    <i class="nav-icon fas fa-shopping-cart"></i> Sales
                </a>
                <a href="reports/dashboard.php" class="nav-item">
                    <i class="nav-icon fas fa-chart-line"></i> Reports & Analytics
                </a>
            </div>
            
            <div class="nav-section">
                <div class="nav-section-title">Administration</div>
                <a href="user_management.php" class="nav-item">
                    <i class="nav-icon fas fa-users-cog"></i> User Management
                </a>
                <a href="settings.php" class="nav-item">
                    <i class="nav-icon fas fa-cog"></i> Settings
                </a>
            </div>
        </nav>
        
        <div class="sidebar-footer">
            <a href="#" class="logout-btn" id="logoutButton">
                <i class="logout-icon fas fa-sign-out-alt"></i> Logout
            </a>
        </div>

        <!-- Loading overlay that will appear when logging out -->
        <div class="loading-overlay" id="loadingOverlay">
            <div class="glass-circle glass-circle-1"></div>
            <div class="glass-circle glass-circle-2"></div>
            <div class="loading-spinner"></div>
            <div class="loading-text">Logging out...</div>
            <div class="loading-progress">
                <div class="loading-progress-bar" id="progressBar"></div>
            </div>
        </div>
    </aside>
    
    <!-- Main Content -->
    <main class="main-content">
        <!-- Top Navigation Bar -->
        <div class="topbar">
            <div class="topbar-left">
                <button id="menuToggle" class="menu-toggle">
                    <i class="fas fa-bars"></i>
                </button>
                <h1 class="page-title">🌸 Ekim's Flower Shop Dashboard</h1>
            </div>
            
            <div class="topbar-right">
                <div class="notification-bell">
                    <i class="fas fa-bell"></i>
                    <span class="notification-indicator">3</span>
                </div>
                
                <div class="user-profile">
                    <div class="avatar">
                        E
                    </div>
                    <div class="user-info">
                        <span class="user-name">Ekim</span>
                        <span class="user-role">Shop Owner</span>
                    </div>
                    <i class="dropdown-icon fas fa-chevron-down"></i>
                </div>
            </div>
        </div>
        
        <div class="content">
            <section class="welcome-section">
                <h2 class="welcome-title">Welcome back, Ekim! 🌺</h2>
                <p class="welcome-subtitle">Here's what's blooming in your flower shop today.</p>
                
                <!-- Flower Stock Section -->
                <div class="stock-overview">    
                    <h3 class="stock-overview-title">
                        <i class="fas fa-seedling"></i> Current Flower & Plant Inventory
                        <span style="font-size: 0.8rem; margin-left: 10px; color: var(--text-light);">
                            (Critical: ≤3, Low: ≤5)
                        </span>
                    </h3>
                    <div class="stock-grid">
                        <!-- Sample flower inventory items -->
                        <div class="stock-item critical featured">
                            <div class="stock-level-indicator" style="background-color: #ef4444;">
                                <i class="fas fa-exclamation-circle"></i>
                                <span>2</span