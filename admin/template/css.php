<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
<!-- Font Poppins -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<!-- Custom Admin CSS -->
<style>
:root {
    --primary-color: #4361ee;
    --secondary-color: #3f37c9;
    --sidebar-width: 280px;
    --topbar-height: 70px;
}

body {
    font-family: 'Poppins', sans-serif;
    background-color: #f8fafc;
    overflow-x: hidden;
}

/* Sidebar Styles */
.sidebar {
    width: var(--sidebar-width);
    height: 100vh;
    position: fixed;
    left: 0;
    top: 0;
    background: linear-gradient(180deg, var(--primary-color), var(--secondary-color));
    color: white;
    transition: all 0.3s ease;
    z-index: 1000;
}

.sidebar-brand {
    height: var(--topbar-height);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0 1.5rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.sidebar-nav {
    padding: 1rem 0;
}

.sidebar-link {
    display: flex;
    align-items: center;
    padding: 0.75rem 1.5rem;
    color: rgba(255, 255, 255, 0.8);
    text-decoration: none;
    transition: all 0.3s ease;
    border-left: 3px solid transparent;
}

.sidebar-link:hover, .sidebar-link.active {
    background-color: rgba(255, 255, 255, 0.1);
    color: white;
    border-left: 3px solid white;
}

.sidebar-link i {
    margin-right: 0.75rem;
    font-size: 1.1rem;
}

/* Main Content Styles */
.main-content {
    margin-left: var(--sidebar-width);
    min-height: 100vh;
    transition: all 0.3s ease;
}

.topbar {
    height: var(--topbar-height);
    background-color: white;
    box-shadow: 0 1px 15px rgba(0, 0, 0, 0.1);
    position: sticky;
    top: 0;
    z-index: 100;
}

.content-wrapper {
    padding: 2rem;
}

/* Card Styles */
.dashboard-card {
    border: none;
    border-radius: 10px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.dashboard-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.card-icon {
    width: 50px;
    height: 50px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

/* Responsive Styles */
@media (max-width: 992px) {
    .sidebar {
        transform: translateX(-100%);
    }
    
    .sidebar.active {
        transform: translateX(0);
    }
    
    .main-content {
        margin-left: 0;
    }
    
    .topbar .navbar-toggler {
        display: block;
    }
}

/* Modal Custom Styles */
.modal-header {
    border-bottom: none;
    padding-bottom: 0;
}

.modal-footer {
    border-top: none;
    padding-top: 0;
}

/* Table Styles */
.table-responsive {
    border-radius: 10px;
    overflow: hidden;
}

.table thead th {
    background-color: #f8fafc;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.5px;
    color: #64748b;
}

/* Badge Styles */
.badge {
    font-weight: 500;
    padding: 5px 10px;
    border-radius: 8px;
}

/* Button Styles */
.btn-primary {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
}

.btn-primary:hover {
    background-color: var(--secondary-color);
    border-color: var(--secondary-color);
}

.btn-outline-primary {
    color: var(--primary-color);
    border-color: var(--primary-color);
}

.btn-outline-primary:hover {
    background-color: var(--primary-color);
    color: white;
}
/* Layout Fix */


.main-content {
    flex: 1;
    display: flex;
    flex-direction: column;
}

.content-wrapper {
    flex: 1;
    padding-bottom: 60px; 
}

.footer {
    position: sticky;
    bottom: 0;
    width: 100%;
    z-index: 100;
    margin-top: auto;
}

/* Navbar Custom */
.topbar {
    height: 60px;
    z-index: 1030;
}

/* Notification Dropdown */
.dropdown-notification {
    max-height: 400px;
    overflow-y: auto;
}

/* Online Status Indicator */
.online-status {
    width: 10px;
    height: 10px;
    background-color: #28a745;
    border-radius: 50%;
    position: absolute;
    bottom: 0;
    right: 0;
    border: 2px solid white;
}
</style>