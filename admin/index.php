<?php
session_start();
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/User.php';

// Verificar autenticación y permisos
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userModel = new User();
if (!$userModel->isAdmin($_SESSION['user_id'])) {
    header('Location: ../');
    exit;
}

// Obtener estadísticas
$productModel = new Product();
try {
    $totalProducts = count($productModel->getAll());
    $totalUsers = count($userModel->getAll());
    $adminUsers = count(array_filter($userModel->getAll(), function($u) { return $u['role'] === 'admin'; }));
    $activeUsers = count(array_filter($userModel->getAll(), function($u) { return $u['active'] == 1; }));
} catch (Exception $e) {
    $totalProducts = $totalUsers = $adminUsers = $activeUsers = 0;
    $error = $e->getMessage();
}

// Mensajes de sesión
$success = $_SESSION['success'] ?? '';
$error = $_SESSION['error'] ?? $error ?? '';
unset($_SESSION['success'], $_SESSION['error']);
$menu = 2;

?>
<?php include 'menu.php'; ?>

<div class="container">
    <!-- Mensajes -->
    <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($success); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i><?php echo htmlspecialchars($error); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Header del Dashboard -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2><i class="fas fa-tachometer-alt me-3"></i>Dashboard de Administración</h2>
                    <p class="text-muted">Panel de control para gestión de la tienda</p>
                </div>
                <div class="text-end">
                    <small class="text-muted">Última actualización: <?php echo date('d/m/Y H:i'); ?></small>
                </div>
            </div>
        </div>
    </div>

    <!-- Tarjetas de estadísticas -->
    <div class="row mb-5">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card dashboard-card bg-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3 class="fw-bold"><?php echo $totalProducts; ?></h3>
                            <p class="mb-0">Total Productos</p>
                            <small class="opacity-75">En el catálogo</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-box stat-icon"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-primary border-0">
                    <a href="products.php" class="text-white text-decoration-none">
                        <small><i class="fas fa-eye me-1"></i>Ver todos los productos</small>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card dashboard-card bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3 class="fw-bold"><?php echo $totalUsers; ?></h3>
                            <p class="mb-0">Total Usuarios</p>
                            <small class="opacity-75">Registrados</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-users stat-icon"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-success border-0">
                    <a href="users.php" class="text-white text-decoration-none">
                        <small><i class="fas fa-eye me-1"></i>Gestionar usuarios</small>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card dashboard-card bg-warning text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3 class="fw-bold"><?php echo $adminUsers; ?></h3>
                            <p class="mb-0">Administradores</p>
                            <small class="opacity-75">Activos</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-user-shield stat-icon"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-warning border-0">
                    <a href="users.php" class="text-white text-decoration-none">
                        <small><i class="fas fa-cog me-1"></i>Gestionar permisos</small>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card dashboard-card bg-info text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3 class="fw-bold"><?php echo $activeUsers; ?></h3>
                            <p class="mb-0">Usuarios Activos</p>
                            <small class="opacity-75">Habilitados</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-user-check stat-icon"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-info border-0">
                    <a href="users.php" class="text-white text-decoration-none">
                        <small><i class="fas fa-chart-line me-1"></i>Ver actividad</small>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Acciones rápidas -->
    <div class="row mb-5">
        <div class="col-12">
            <h4><i class="fas fa-bolt me-2"></i>Acciones Rápidas</h4>
            <p class="text-muted">Operaciones frecuentes de administración</p>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card quick-action h-100">
                <div class="card-body text-center">
                    <i class="fas fa-plus-circle fa-3x text-primary mb-3"></i>
                    <h5>Nuevo Producto</h5>
                    <p class="text-muted">Agregar producto al catálogo</p>
                    <a href="products.php" class="btn btn-primary">Crear Producto</a>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card quick-action h-100">
                <div class="card-body text-center">
                    <i class="fas fa-user-plus fa-3x text-success mb-3"></i>
                    <h5>Nuevo Usuario</h5>
                    <p class="text-muted">Registrar nuevo usuario</p>
                    <a href="users.php" class="btn btn-success">Crear Usuario</a>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card quick-action h-100">
                <div class="card-body text-center">
                    <i class="fas fa-store fa-3x text-info mb-3"></i>
                    <h5>Ver Tienda</h5>
                    <p class="text-muted">Ir a la vista pública</p>
                    <a href="../" class="btn btn-info text-white">Ir a Tienda</a>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card quick-action h-100">
                <div class="card-body text-center">
                    <i class="fas fa-cogs fa-3x text-warning mb-3"></i>
                    <h5>Configuración</h5>
                    <p class="text-muted">Ajustes del sistema</p>
                    <a href="#" class="btn btn-warning text-white">Configurar</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Información del sistema -->
    <div class="row">
        <div class="col-md-8 mb-4 pb-4">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-info-circle me-2"></i>Información del Sistema</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-unstyled">
                                <li><strong>Versión:</strong> 2.0 POO</li>
                                <li><strong>Base de datos:</strong> MySQL</li>
                                <li><strong>Framework:</strong> Bootstrap 5</li>
                                <li><strong>PHP:</strong> <?php echo phpversion(); ?></li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-unstyled">
                                <li><strong>Servidor:</strong> <?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'N/A'; ?></li>
                                <li><strong>Host:</strong> <?php echo $_SERVER['HTTP_HOST']; ?></li>
                                <li><strong>Última actualización:</strong> <?php echo date('d/m/Y'); ?></li>
                                <li><strong>Estado:</strong> <span class="badge bg-success">Operativo</span></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-graduation-cap me-2"></i>Acerca del Proyecto</h5>
                </div>
                <div class="card-body">
                    <p class="small">
                        <strong>MiTienda Grupo 5</strong><br>
                        Sistema desarrollado para la Universidad Abierta Para Adultos (UAPA)
                        como proyecto académico de Desarrollo de Proyectos con Software Libre.
                    </p>
                    <p class="small mb-0">
                        <i class="fas fa-users me-1"></i> Desarrollado por Grupo 5 en año <br>
                        <i class="fas fa-calendar me-1"></i> <?php echo date('Y'); ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Auto-dismiss alerts después de 5 segundos
setTimeout(function() {
    var alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        var bsAlert = new bootstrap.Alert(alert);
        bsAlert.close();
    });
}, 5000);

// Actualizar hora cada minuto
setInterval(function() {
    var now = new Date();
    var timeString = now.toLocaleString('es-ES');
    document.querySelector('.text-muted small').textContent = 'Última actualización: ' + timeString;
}, 60000);
</script>
</body>
</html>
