<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
    :root {
        --primary-color: #3498db;
        --primary-light: #e8f4fd;
        --secondary-color: #2c3e50;
        --success-color: #2ecc71;
        --success-light: #e8f8f0;
        --info-color: #1abc9c;
        --warning-color: #f39c12;
        --danger-color: #e74c3c;
        --danger-light: #fde8e8;
        --light-color: #f8f9fa;
        --dark-color: #343a40;
    }
    
    body {
        font-family: 'Poppins', sans-serif;
        background-color: #f5f7fa;
        color: #333;
    }
    
    .hero-section {
        background-color: white;
        border-radius: 12px;
        padding: 2rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }
    
    .card {
        border-radius: 12px;
        border: none;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        overflow: hidden;
    }
    
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
    }
    
    .stat-card {
        border-left: 4px solid var(--primary-color);
    }
    
    .badge {
        font-weight: 500;
        padding: 5px 10px;
        border-radius: 8px;
    }
    
    .bg-primary-light {
        background-color: var(--primary-light);
    }
    
    .bg-success-light {
        background-color: var(--success-light);
    }
    
    .bg-danger-light {
        background-color: var(--danger-light);
    }
    
    .btn {
        border-radius: 8px;
        font-weight: 500;
        padding: 8px 16px;
        transition: all 0.3s ease;
    }
    
    .btn-primary {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }
    
    .btn-outline-primary {
        color: var(--primary-color);
        border-color: var(--primary-color);
    }
    
    .btn-outline-primary:hover {
        background-color: var(--primary-color);
        color: white;
    }
    
    .form-control, .input-group-text {
        border-radius: 8px !important;
    }
    
    .border-primary-light {
        border-color: var(--primary-light) !important;
    }
    
    .table th {
        font-weight: 600;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #6c757d;
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(52, 152, 219, 0.05);
    }
    
    @media (max-width: 768px) {
        .hero-section h1 {
            font-size: 1.8rem;
        }
        
        .stat-card {
            margin-bottom: 1rem;
        }
    }

    footer {
    border-top: 1px solid rgba(0, 0, 0, 0.05);
    margin-top: 3rem;
}

footer a {
    transition: color 0.2s ease;
}

footer a:hover {
    color: var(--primary-color) !important;
    text-decoration: underline !important;
}
</style>