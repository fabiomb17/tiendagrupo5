// Configuración de la aplicación
const Config = {
    API_BASE_URL: '/tiendagrupo5/api',
    ENDPOINTS: {
        PRODUCTS: '/products',
        CART: '/cart',
        AUTH: '/auth'
    }
};

// Clase para manejar las llamadas a la API
class ApiService {
    static async request(endpoint, options = {}) {
        const url = Config.API_BASE_URL + endpoint;
        const defaultOptions = {
            headers: {
                'Content-Type': 'application/json',
            }
        };

        const finalOptions = { ...defaultOptions, ...options };
        
        // Si hay datos en el body y no es FormData, convertir a JSON
        if (finalOptions.body && !(finalOptions.body instanceof FormData)) {
            finalOptions.body = JSON.stringify(finalOptions.body);
        }

        try {
            const response = await fetch(url, finalOptions);
            const data = await response.json();
            
            if (!response.ok) {
                throw new Error(data.message || `HTTP error! status: ${response.status}`);
            }
            
            return data;
        } catch (error) {
            console.error('API Error:', error);
            throw error;
        }
    }

    static async get(endpoint) {
        return this.request(endpoint, { method: 'GET' });
    }

    static async post(endpoint, data = {}) {
        return this.request(endpoint, {
            method: 'POST',
            body: data
        });
    }
}

// Clase para manejar productos
class ProductManager {
    static async getAll() {
        try {
            const response = await ApiService.get(Config.ENDPOINTS.PRODUCTS);
            return response.data || [];
        } catch (error) {
            console.error('Error al obtener productos:', error);
            return [];
        }
    }

    static async getByCategory(category) {
        try {
            const response = await ApiService.get(`${Config.ENDPOINTS.PRODUCTS}?category=${category}`);
            return response.data || [];
        } catch (error) {
            console.error('Error al obtener productos por categoría:', error);
            return [];
        }
    }

    static async search(searchTerm) {
        try {
            const response = await ApiService.get(`${Config.ENDPOINTS.PRODUCTS}?q=${encodeURIComponent(searchTerm)}`);
            return response.data || [];
        } catch (error) {
            console.error('Error al buscar productos:', error);
            return [];
        }
    }
}

// Clase para manejar el carrito
class CartManager {
    static async getItems() {
        try {
            const response = await ApiService.get(Config.ENDPOINTS.CART);
            return response.data || { items: [], total: { item_count: 0, total_quantity: 0, total_amount: 0 } };
        } catch (error) {
            console.error('Error al obtener carrito:', error);
            return { items: [], total: { item_count: 0, total_quantity: 0, total_amount: 0 } };
        }
    }

    static async addItem(productId, quantity = 1) {
        try {
            const response = await ApiService.post('/cart/add', {
                product_id: productId,
                quantity: quantity
            });
            return response;
        } catch (error) {
            console.error('Error al agregar al carrito:', error);
            throw error;
        }
    }

    static async updateQuantity(productId, quantity) {
        try {
            const response = await ApiService.post('/cart/update', {
                product_id: productId,
                quantity: quantity
            });
            return response;
        } catch (error) {
            console.error('Error al actualizar cantidad:', error);
            throw error;
        }
    }

    static async removeItem(productId) {
        try {
            const response = await ApiService.post('/cart/remove', {
                product_id: productId
            });
            return response;
        } catch (error) {
            console.error('Error al eliminar del carrito:', error);
            throw error;
        }
    }

    static async getSummary() {
        try {
            const response = await ApiService.get('/cart/summary');
            return response.data || { item_count: 0, total_quantity: 0, total_amount: 0 };
        } catch (error) {
            console.error('Error al obtener resumen del carrito:', error);
            return { item_count: 0, total_quantity: 0, total_amount: 0 };
        }
    }
}

// Variables globales
let currentProducts = [];
let cartData = { items: [], total: { item_count: 0, total_quantity: 0, total_amount: 0 } };
        
        // DOM Elements
        const productsGrid = document.getElementById('productsGrid');
        const categoriesList = document.getElementById('categoriesList');
        const cartButton = document.getElementById('cartButton');
        const cartSidebar = document.getElementById('cartSidebar');
        const cartOverlay = document.getElementById('cartOverlay');
        const closeCart = document.getElementById('closeCart');
        const cartItems = document.getElementById('cartItems');
        const cartTotalItems = document.getElementById('cartTotalItems');
        const cartTotalAmount = document.getElementById('cartTotalAmount');
        const checkoutButton = document.getElementById('checkoutButton');
        const cartBadge = document.getElementById('cartBadge');
        
        // Inicializar la aplicación
        document.addEventListener('DOMContentLoaded', async function() {
            await loadProducts();
            await loadCart();
            setupEventListeners();
        });

        // Cargar productos desde la API
        async function loadProducts() {
            try {
                currentProducts = await ProductManager.getAll();
                renderProductsGrid(currentProducts);
            } catch (error) {
                showNotification('Error al cargar productos', 'error');
                console.error('Error:', error);
            }
        }

        // Cargar carrito desde la API
        async function loadCart() {
            try {
                cartData = await CartManager.getItems();
                updateCartUI();
            } catch (error) {
                console.error('Error al cargar carrito:', error);
            }
        }
        
        // Configurar event listeners
        function setupEventListeners() {
            // Filtrado por categoría
            categoriesList.querySelectorAll('.nav-link').forEach(link => {
                link.addEventListener('click', async function(e) {
                    e.preventDefault();
                    const category = this.dataset.category;
                    
                    // Actualizar active class
                    categoriesList.querySelectorAll('.nav-link').forEach(item => {
                        item.classList.remove('active');
                    });
                    this.classList.add('active');
                    
                    // Filtrar productos
                    await filterProducts(category);
                });
            });
            
            // Carrito
            cartButton.addEventListener('click', toggleCart);
            closeCart.addEventListener('click', toggleCart);
            cartOverlay.addEventListener('click', toggleCart);
            
            // Completar compra
            checkoutButton.addEventListener('click', completePurchase);

            // Buscar productos
            const searchForm = document.querySelector('form.d-flex');
            if (searchForm) {
                searchForm.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    const searchInput = this.querySelector('input[type="search"]');
                    const searchTerm = searchInput.value.trim();
                    
                    if (searchTerm) {
                        await searchProducts(searchTerm);
                    } else {
                        await loadProducts();
                    }
                });
            }
        }
        
        // Renderizar grid de productos en el área principal
        function renderProductsGrid(products = currentProducts) {
            productsGrid.innerHTML = '';
            
            if (!products || products.length === 0) {
                productsGrid.innerHTML = '<div class="col-12 text-center py-5"><h5>No hay productos disponibles</h5></div>';
                return;
            }
            
            products.forEach(product => {
                const col = document.createElement('div');
                col.className = 'col-lg-3 col-md-4 col-sm-6 mb-4';
                
                col.innerHTML = `
                    <div class="card product-card h-100">
                        <img src="${product.image}" class="card-img-top product-img p-3" alt="${product.name}">
                        <div class="card-body">
                            <h5 class="card-title">${product.name}</h5>
                            <p class="card-text text-muted">ID: ${product.id}</p>
                            ${product.description ? `<p class="card-text small">${product.description}</p>` : ''}
                        </div>
                        <div class="card-footer bg-transparent">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="h5 mb-0">DOP ${parseFloat(product.price).toFixed(2)}</span>
                                <button class="btn btn-primary add-to-cart" data-id="${product.id}">
                                    <i class="fas fa-plus"></i> Agregar
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                
                productsGrid.appendChild(col);
            });
            
            // Agregar event listeners a los botones de añadir al carrito
            document.querySelectorAll('.add-to-cart').forEach(button => {
                button.addEventListener('click', async function() {
                    const productId = parseInt(this.dataset.id);
                    await addToCart(productId);
                });
            });
        }

        // Filtrar productos por categoría
        async function filterProducts(category) {
            try {
                let products;
                if (category === 'all') {
                    products = await ProductManager.getAll();
                } else {
                    products = await ProductManager.getByCategory(category);
                }
                currentProducts = products;
                renderProductsGrid(products);
            } catch (error) {
                showNotification('Error al filtrar productos', 'error');
                console.error('Error:', error);
            }
        }

        // Buscar productos
        async function searchProducts(searchTerm) {
            try {
                const products = await ProductManager.search(searchTerm);
                currentProducts = products;
                renderProductsGrid(products);
                
                // Actualizar la categoría activa a "Todos"
                categoriesList.querySelectorAll('.nav-link').forEach(item => {
                    item.classList.remove('active');
                });
                categoriesList.querySelector('[data-category="all"]').classList.add('active');
            } catch (error) {
                showNotification('Error al buscar productos', 'error');
                console.error('Error:', error);
            }
        }
        
        // Añadir producto al carrito
        async function addToCart(productId) {
            try {
                await CartManager.addItem(productId, 1);
                await loadCart(); // Recargar carrito
                showCartNotification('Producto agregado al carrito', 'success');
            } catch (error) {
                if (error.message.includes('No autorizado')) {
                    showNotification('Debes iniciar sesión para agregar productos al carrito', 'warning');
                    // Aquí podrías redirigir al login o mostrar un modal de login
                } else {
                    showNotification(error.message || 'Error al agregar producto al carrito', 'error');
                }
                console.error('Error:', error);
            }
        }

        // Actualizar la UI del carrito
        function updateCartUI() {
            // Actualizar badge
            cartBadge.textContent = cartData.total.total_quantity || 0;
            
            // Actualizar sidebar del carrito
            if (!cartData.items || cartData.items.length === 0) {
                cartItems.innerHTML = `
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-shopping-cart fa-3x mb-3"></i>
                        <p>Tu carrito está vacío</p>
                    </div>
                `;
                checkoutButton.disabled = true;
            } else {
                cartItems.innerHTML = '';
                
                cartData.items.forEach(item => {
                    const cartItem = document.createElement('div');
                    cartItem.className = 'cart-item mb-3 border-bottom pb-3';
                    cartItem.innerHTML = `
                        <div class="d-flex">
                            <img src="${item.image}" class="rounded me-3" width="60" height="60" style="object-fit: cover;">
                            <div class="flex-grow-1">
                                <h6 class="mb-1">${item.name}</h6>
                                <small class="text-muted">DOP ${parseFloat(item.price).toFixed(2)} c/u</small>
                            </div>
                            <div class="text-end">
                                <div class="d-flex align-items-center mb-1">
                                    <button class="btn btn-sm btn-outline-secondary decrease-quantity" data-id="${item.product_id}">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <span class="mx-2">${item.quantity}</span>
                                    <button class="btn btn-sm btn-outline-secondary increase-quantity" data-id="${item.product_id}">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                                <span class="fw-bold">DOP ${parseFloat(item.subtotal).toFixed(2)}</span>
                            </div>
                        </div>
                    `;
                    
                    cartItems.appendChild(cartItem);
                });
                
                // Habilitar botón de compra
                checkoutButton.disabled = false;
            }
            
            // Actualizar resumen
            cartTotalItems.textContent = cartData.total.total_quantity || 0;
            cartTotalAmount.textContent = `DOP ${parseFloat(cartData.total.total_amount || 0).toFixed(2)}`;
            
            // Agregar event listeners a los botones de cantidad
            document.querySelectorAll('.increase-quantity').forEach(button => {
                button.addEventListener('click', async function() {
                    const productId = parseInt(this.dataset.id);
                    await updateCartItemQuantity(productId, 1);
                });
            });
            
            document.querySelectorAll('.decrease-quantity').forEach(button => {
                button.addEventListener('click', async function() {
                    const productId = parseInt(this.dataset.id);
                    await updateCartItemQuantity(productId, -1);
                });
            });
        }

        // Actualizar cantidad de un item en el carrito
        async function updateCartItemQuantity(productId, change) {
            try {
                const currentItem = cartData.items.find(item => item.product_id === productId);
                if (!currentItem) return;
                
                const newQuantity = currentItem.quantity + change;
                
                if (newQuantity <= 0) {
                    await CartManager.removeItem(productId);
                } else {
                    await CartManager.updateQuantity(productId, newQuantity);
                }
                
                await loadCart(); // Recargar carrito
            } catch (error) {
                showNotification('Error al actualizar cantidad', 'error');
                console.error('Error:', error);
            }
        }
        
        // Mostrar/ocultar carrito
        function toggleCart() {
            cartSidebar.classList.toggle('show');
            cartOverlay.classList.toggle('show');
            
            // Bloquear scroll del body cuando el carrito está abierto
            if (cartSidebar.classList.contains('show')) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = '';
            }
        }

        // Mostrar notificación
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = 'position-fixed bottom-0 end-0 p-3';
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

        // Mostrar notificación al añadir al carrito
        function showCartNotification(message = 'Producto añadido al carrito', type = 'success') {
            showNotification(message, type);
        }

        // Completar compra
        async function completePurchase() {
            if (!cartData.items || cartData.items.length === 0) return;
            
            try {
                // Aquí iría la lógica para procesar la compra
                // Por ahora solo mostramos un mensaje
                showNotification(`Compra completada! Total: ${cartTotalAmount.textContent}`, 'success');
                
                // Vaciar carrito
                await CartManager.clearCart();
                await loadCart();
                toggleCart();
            } catch (error) {
                showNotification('Error al procesar la compra', 'error');
                console.error('Error:', error);
            }
        }