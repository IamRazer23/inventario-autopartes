/**
 * Carrito de Compras - JavaScript
 * Funciones para manejo del carrito vía AJAX
 */

const CarritoJS = {
    // Configuración
    config: {
        baseUrl: '', // Se establece dinámicamente
        itbmsRate: 0.07
    },

    /**
     * Inicializa el módulo del carrito
     */
    init: function(baseUrl) {
        this.config.baseUrl = baseUrl;
        this.bindEvents();
    },

    /**
     * Bindea los eventos
     */
    bindEvents: function() {
        // Botones de agregar al carrito
        document.querySelectorAll('.btn-agregar-carrito').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const autoparteId = btn.dataset.autoparteId;
                const cantidad = btn.dataset.cantidad || 1;
                this.agregar(autoparteId, cantidad);
            });
        });
    },

    /**
     * Agrega un producto al carrito
     */
    agregar: function(autoparteId, cantidad = 1) {
        this.showLoading();
        
        fetch(`${this.config.baseUrl}/index.php?module=carrito&action=agregar`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: `autoparte_id=${autoparteId}&cantidad=${cantidad}`
        })
        .then(response => response.json())
        .then(data => {
            this.hideLoading();
            if (data.success) {
                this.updateCartCount(data.cart_count);
                this.showNotification(data.message, 'success');
                this.animateCartIcon();
            } else {
                this.showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            this.hideLoading();
            this.showNotification('Error al agregar al carrito', 'error');
            console.error('Error:', error);
        });
    },

    /**
     * Actualiza la cantidad de un item
     */
    actualizar: function(autoparteId, cantidad, callback) {
        fetch(`${this.config.baseUrl}/index.php?module=carrito&action=actualizar`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: `autoparte_id=${autoparteId}&cantidad=${cantidad}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.updateCartCount(data.cart_count);
                if (callback) callback(data);
            } else {
                this.showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            this.showNotification('Error al actualizar', 'error');
            console.error('Error:', error);
        });
    },

    /**
     * Elimina un item del carrito
     */
    eliminar: function(autoparteId, callback) {
        fetch(`${this.config.baseUrl}/index.php?module=carrito&action=eliminar`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: `autoparte_id=${autoparteId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.updateCartCount(data.cart_count);
                this.showNotification(data.message, 'success');
                if (callback) callback(data);
            } else {
                this.showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            this.showNotification('Error al eliminar', 'error');
            console.error('Error:', error);
        });
    },

    /**
     * Vacía el carrito completo
     */
    vaciar: function(callback) {
        fetch(`${this.config.baseUrl}/index.php?module=carrito&action=vaciar`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.updateCartCount(0);
                this.showNotification(data.message, 'success');
                if (callback) callback(data);
            } else {
                this.showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            this.showNotification('Error al vaciar carrito', 'error');
            console.error('Error:', error);
        });
    },

    /**
     * Obtiene el contador del carrito
     */
    getCount: function(callback) {
        fetch(`${this.config.baseUrl}/index.php?module=carrito&action=contador`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.updateCartCount(data.count);
                if (callback) callback(data.count);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    },

    /**
     * Obtiene el mini-carrito
     */
    getMini: function(callback) {
        fetch(`${this.config.baseUrl}/index.php?module=carrito&action=mini`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && callback) {
                callback(data);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    },

    /**
     * Actualiza el contador del carrito en la UI
     */
    updateCartCount: function(count) {
        const cartCountElements = document.querySelectorAll('#cart-count, .cart-count');
        cartCountElements.forEach(el => {
            el.textContent = count;
            el.classList.toggle('hidden', count === 0);
        });
    },

    /**
     * Anima el icono del carrito
     */
    animateCartIcon: function() {
        const cartIcon = document.querySelector('.fa-shopping-cart');
        if (cartIcon) {
            cartIcon.classList.add('animate-bounce');
            setTimeout(() => {
                cartIcon.classList.remove('animate-bounce');
            }, 500);
        }
    },

    /**
     * Muestra una notificación
     */
    showNotification: function(message, type = 'success') {
        // Remover notificaciones anteriores
        document.querySelectorAll('.cart-notification').forEach(n => n.remove());
        
        const notification = document.createElement('div');
        notification.className = `cart-notification fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg transform transition-all duration-300 ${
            type === 'success' ? 'bg-green-500' : 'bg-red-500'
        } text-white font-medium`;
        notification.style.transform = 'translateX(100%)';
        
        const icon = type === 'success' ? 'check-circle' : 'exclamation-circle';
        notification.innerHTML = `
            <div class="flex items-center">
                <i class="fas fa-${icon} mr-2"></i>
                <span>${message}</span>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Animar entrada
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
        }, 10);
        
        // Remover después de 3 segundos
        setTimeout(() => {
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    },

    /**
     * Muestra el loading
     */
    showLoading: function() {
        let loader = document.getElementById('cart-loader');
        if (!loader) {
            loader = document.createElement('div');
            loader.id = 'cart-loader';
            loader.className = 'fixed inset-0 bg-black bg-opacity-30 z-50 flex items-center justify-center';
            loader.innerHTML = `
                <div class="bg-white rounded-lg p-6 shadow-xl">
                    <div class="flex items-center">
                        <svg class="animate-spin h-8 w-8 text-indigo-600 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="text-gray-700 font-medium">Procesando...</span>
                    </div>
                </div>
            `;
            document.body.appendChild(loader);
        }
        loader.classList.remove('hidden');
    },

    /**
     * Oculta el loading
     */
    hideLoading: function() {
        const loader = document.getElementById('cart-loader');
        if (loader) {
            loader.classList.add('hidden');
        }
    },

    /**
     * Formatea un número como moneda
     */
    formatCurrency: function(amount) {
        return '$' + parseFloat(amount).toFixed(2);
    },

    /**
     * Calcula los totales
     */
    calculateTotals: function(subtotal) {
        const itbms = subtotal * this.config.itbmsRate;
        const total = subtotal + itbms;
        return {
            subtotal: subtotal,
            itbms: itbms,
            total: total
        };
    }
};

// Exportar para uso global
window.CarritoJS = CarritoJS;