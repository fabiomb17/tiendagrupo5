<?php
session_start();
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/User.php';

// Verificar si el usuario está autenticado y es admin
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userModel = new User();
if (!$userModel->isAdmin($_SESSION['user_id'])) {
    header('Location: ../index.html');
    exit;
}

// Obtener productos usando el modelo
$productModel = new Product();
try {
    $products = $productModel->getAll();
} catch (Exception $e) {
    $products = [];
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
    
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-plus-circle me-2"></i>Registrar Producto</h4>
                </div>
                <div class="card-body">
                    <form method="post" action="register_product.php">
                        <div class="mb-3">
                            <label class="form-label">Nombre *</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Precio *</label>
                            <div class="input-group">
                                <span class="input-group-text">DOP</span>
                                <input type="number" step="0.01" class="form-control" name="price" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Categoría *</label>
                            <select class="form-select" name="category" required>
                                <option value="">Seleccionar categoría</option>
                                <option value="electronics">Electrónicos</option>
                                <option value="clothing">Ropa</option>
                                <option value="home">Hogar</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Imagen (URL o ruta) *</label>
                            <input type="text" class="form-control" name="image" placeholder="img/producto.png" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea class="form-control" name="description" rows="3" placeholder="Descripción del producto (opcional)"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Registrar Producto
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-list me-2"></i>Productos Registrados</h4>
                </div>
                <div class="card-body">
                    <?php if (empty($products)): ?>
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-box-open fa-3x mb-3"></i>
                            <p>No hay productos registrados</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="productsTable">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>Precio</th>
                                        <th>Categoría</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($products as $product): ?>
                                    <tr>
                                        <td><?php echo $product['id']; ?></td>
                                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                                        <td>DOP <?php echo number_format($product['price'], 2); ?></td>
                                        <td>
                                            <span class="badge bg-secondary">
                                                <?php echo ucfirst($product['category']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-warning me-1" 
                                                    onclick="editProduct(<?php echo $product['id']; ?>, '<?php echo addslashes($product['name']); ?>', <?php echo $product['price']; ?>, '<?php echo $product['category']; ?>', '<?php echo addslashes($product['image']); ?>', '<?php echo addslashes($product['description'] ?? ''); ?>')">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger" onclick="deleteProduct(<?php echo $product['id']; ?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para editar producto -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Editar Producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editForm" method="post" action="edit_product.php">
                <div class="modal-body">
                    <input type="hidden" id="editId" name="id">
                    <div class="mb-3">
                        <label class="form-label">Nombre *</label>
                        <input type="text" class="form-control" id="editName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Precio *</label>
                        <div class="input-group">
                            <span class="input-group-text">DOP</span>
                            <input type="number" step="0.01" class="form-control" id="editPrice" name="price" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Categoría *</label>
                        <select class="form-select" id="editCategory" name="category" required>
                            <option value="electronics">Electrónicos</option>
                            <option value="clothing">Ropa</option>
                            <option value="home">Hogar</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Imagen (URL o ruta) *</label>
                        <input type="text" class="form-control" id="editImage" name="image" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea class="form-control" id="editDescription" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Guardar cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function editProduct(id, name, price, category, image, description = '') {
    document.getElementById('editId').value = id;
    document.getElementById('editName').value = name;
    document.getElementById('editPrice').value = price;
    document.getElementById('editCategory').value = category;
    document.getElementById('editImage').value = image;
    document.getElementById('editDescription').value = description;
    
    var modal = new bootstrap.Modal(document.getElementById('editModal'));
    modal.show();
}

// Manejar el envío del formulario de edición
document.getElementById('editForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('edit_product.php', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        // Cerrar el modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('editModal'));
        modal.hide();
        
        if (data.success) {
            // Actualizar la fila en la tabla
            const productId = formData.get('id');
            const productName = formData.get('name');
            const productPrice = parseFloat(formData.get('price'));
            const productCategory = formData.get('category');
            const productImage = formData.get('image');
            const productDescription = formData.get('description') || '';
            
            updateProductRow(productId, productName, productPrice, productCategory, productImage, productDescription);
            
            // Mostrar mensaje de éxito
            showNotification(data.message, 'success');
        } else {
            // Mostrar mensaje de error
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error al actualizar el producto', 'error');
    });
});

function updateProductRow(id, name, price, category, image, description) {
    // Encontrar la fila del producto
    const rows = document.querySelectorAll('#productsTable tbody tr');
    rows.forEach(row => {
        const firstCell = row.querySelector('td:first-child');
        if (firstCell && firstCell.textContent.trim() == id) {
            // Mapear categorías a español
            const categoryMap = {
                'electronics': 'Electrónicos',
                'clothing': 'Ropa',
                'home': 'Hogar'
            };
            
            // Actualizar las celdas de la fila
            row.querySelector('td:nth-child(2)').textContent = name;
            row.querySelector('td:nth-child(3)').textContent = 'DOP ' + price.toFixed(2);
            row.querySelector('td:nth-child(4) .badge').textContent = categoryMap[category] || category;
            
            // Actualizar el botón de editar con los nuevos datos
            const editButton = row.querySelector('.btn-warning');
            if (editButton) {
                // Escapar comillas simples para JavaScript
                const escapedName = name.replace(/'/g, "\\'");
                const escapedImage = image.replace(/'/g, "\\'");
                const escapedDescription = description.replace(/'/g, "\\'");
                
                editButton.setAttribute('onclick', 
                    `editProduct(${id}, '${escapedName}', ${price}, '${category}', '${escapedImage}', '${escapedDescription}')`
                );
            }
        }
    });
}

function deleteProduct(id) {
    if (confirm('¿Estás seguro de que quieres eliminar este producto?\n\nEsta acción no se puede deshacer.')) {
        // Crear un formulario oculto para enviar la solicitud de eliminación
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = 'delete_product.php';
        
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'id';
        input.value = id;
        
        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
    }
}

// Mostrar notificaciones
<?php if (!empty($success)): ?>
    showNotification('<?php echo $success; ?>', 'success');
<?php endif; ?>

<?php if (!empty($error)): ?>
    showNotification('<?php echo $error; ?>', 'error');
<?php endif; ?>

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = 'position-fixed top-0 end-0 p-3';
    notification.style.zIndex = '1100';
    
    const bgColor = type === 'success' ? 'bg-success' : 
                   type === 'error' ? 'bg-danger' : 
                   type === 'warning' ? 'bg-warning' : 'bg-info';
    
    notification.innerHTML = `
        <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header ${bgColor} text-white">
                <strong class="me-auto">${type === 'error' ? 'Error' : 'Notificación'}</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                ${message}
            </div>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Eliminar la notificación después de 4 segundos
    setTimeout(() => {
        notification.querySelector('.toast').classList.remove('show');
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 4000);
    
    // Permitir cerrar la notificación manualmente
    notification.querySelector('.btn-close').addEventListener('click', function() {
        notification.querySelector('.toast').classList.remove('show');
        setTimeout(() => {
            notification.remove();
        }, 300);
    });
}
</script>
</body>
</html>
