<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal de Productos</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
    
</head>
<body>
    <!-- Barra de navegación principal -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container-fluid">
            <!-- Logo -->
            <a class="navbar-brand" href="#">
                <i class="fas fa-store me-2"></i>MiTienda Grupo 5 UAPA
            </a>
            
            <!-- Botón para móviles -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" title="Abrir menú de navegación" aria-label="Abrir menú de navegación">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <!-- Contenido de la barra de navegación -->
            <div class="collapse navbar-collapse" id="navbarMain">
                <!-- Buscador -->
                <form class="d-flex ms-auto me-3">
                    <div class="input-group">
                        <input class="form-control" type="search" placeholder="Buscar productos..." aria-label="Search">
                        <button class="btn btn-outline-secondary" type="submit" title="Buscar">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
                
                <!-- Carrito y administración -->
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link" href="/tiendagrupo5/admin" role="button">
                            <i class="fas fa-user me-1"></i>Administrar
                        </a>
                    </li>
                    <li class="nav-item">
                        <button class="btn btn-link nav-link position-relative" id="cartButton">
                            <i class="fas fa-shopping-cart"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="cartBadge">0</span>
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <div class="container-fluid">
        <div class="row">
            <!-- Barra lateral izquierda - Categorías y productos -->
            <div class="col-lg-3 col-md-3 p-0">
                <div class="sidebar mt-3 p-4 bg-light border-end">
                    <h5 class="mb-4"><i class="fas fa-list me-2"></i>Categorías</h5>
                    
                    <ul class="nav nav-pills flex-column mb-4" id="categoriesList">
                        <li class="nav-item">
                            <a class="nav-link active" href="#" data-category="all">Todos</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" data-category="electronics">Electrónicos</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" data-category="clothing">Ropa</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" data-category="home">Hogar</a>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Contenido principal -->
            <div class="col-lg-9 col-md-3 py-3">
                <div class="row" id="productsGrid">
                    <!-- Los productos se mostrarán aquí en modo grid -->
                </div>
            </div>
        </div>
    </div>
    
    <!-- Carrito de compras (sidebar derecho) -->
    <div class="cart-overlay" id="cartOverlay"></div>
    
    <div class="cart-sidebar p-3" id="cartSidebar">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4><i class="fas fa-shopping-cart me-2"></i>Carrito de compras</h4>
            <button class="btn btn-sm btn-outline-secondary" id="closeCart" title="Cerrar carrito">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="cart-items mb-3" id="cartItems">
            <!-- Los items del carrito se mostrarán aquí -->
            <div class="text-center text-muted py-4">
                <i class="fas fa-shopping-cart fa-3x mb-3"></i>
                <p>Tu carrito está vacío</p>
            </div>
        </div>
        
        <div class="cart-summary border-top pt-3">
            <div class="d-flex justify-content-between mb-2">
                <span>Artículos:</span>
                <span id="cartTotalItems">0</span>
            </div>
            <div class="d-flex justify-content-between mb-3 fw-bold">
                <span>Total:</span>
                <span id="cartTotalAmount">DOP 0.00</span>
            </div>
            
            <button class="btn btn-primary w-100" id="checkoutButton" disabled>
                Completar compra
            </button>
        </div>
    </div>
    
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script src="script.js"></script>
</body>
</html>