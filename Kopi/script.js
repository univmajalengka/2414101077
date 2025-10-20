// script.js
document.addEventListener('DOMContentLoaded', () => {
    // Variabel global untuk menyimpan data keranjang
    let cart = [];

    // --- ELEMEN DOM ---
    const menuList = document.getElementById('menu-list');
    const filterButtons = document.querySelectorAll('.filter-btn');
    const cartCountBadge = document.getElementById('cart-count');
    const cartItemsContainer = document.getElementById('cart-items-container');
    const cartTotalElement = document.getElementById('cart-total');
    const cartModal = new bootstrap.Modal(document.getElementById('cartModal'));
    const checkoutModal = new bootstrap.Modal(document.getElementById('checkoutModal'));
    const checkoutButton = document.getElementById('checkout-btn');
    const checkoutForm = document.getElementById('checkout-form');

    // --- EVENT LISTENERS ---

    // 1. Filter Kategori Menu
    filterButtons.forEach(button => {
        button.addEventListener('click', () => {
            // Hapus kelas 'active' dari semua tombol dan tambahkan ke yang diklik
            filterButtons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');

            const category = button.dataset.category;
            filterMenu(category);
        });
    });

    // 2. Tombol "Tambah ke Keranjang" dan "Beli Sekarang"
    menuList.addEventListener('click', (e) => {
        if (e.target.classList.contains('add-to-cart-btn')) {
            const product = e.target.closest('.card-footer').parentElement;
            addToCart(product.querySelector('.add-to-cart-btn').dataset);
        }
        if (e.target.classList.contains('buy-now-btn')) {
            const product = e.target.closest('.card-footer').parentElement;
            addToCart(product.querySelector('.buy-now-btn').dataset);
            cartModal.hide();
            checkoutModal.show();
        }
    });

    // 3. Kontrol Kuantitas di dalam Keranjang (Tambah/Kurang)
    cartItemsContainer.addEventListener('click', (e) => {
        const target = e.target;
        if (target.classList.contains('increase-qty') || target.classList.contains('decrease-qty')) {
            const productId = target.dataset.id;
            const isIncrease = target.classList.contains('increase-qty');
            updateQuantity(productId, isIncrease);
        }
    });

    // 4. Tombol Checkout di Modal Keranjang
    checkoutButton.addEventListener('click', () => {
        if (cart.length === 0) {
            alert('Keranjang Anda masih kosong!');
            return;
        }
        cartModal.hide();
        checkoutModal.show();
    });
    
    // 5. Submit Form Pemesanan
checkoutForm.addEventListener('submit', (e) => {
    e.preventDefault();
    const customerName = document.getElementById('customerName').value;
    const purchaseDate = document.getElementById('purchaseDate').value;

    if (customerName && purchaseDate && cart.length > 0) {
        const orderData = {
            customerName: customerName,
            purchaseDate: purchaseDate,
            items: cart
        };

        // Kirim data ke server menggunakan fetch API
        fetch('place_order.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(orderData),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(`Terima kasih, ${customerName}! Pesanan Anda berhasil dibuat dan akan segera kami proses.`);
                
                // Kosongkan keranjang dan reset UI
                cart = [];
                updateCartUI();
                checkoutForm.reset();
                checkoutModal.hide();
            } else {
                alert('Maaf, terjadi kesalahan saat membuat pesanan. Silakan coba lagi.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan koneksi.');
        });
    }
});


    // --- FUNGSI-FUNGSI ---

    // Fungsi untuk memfilter menu berdasarkan kategori
    function filterMenu(category) {
        const menuItems = document.querySelectorAll('.product-item');
        menuItems.forEach(item => {
            if (category === 'all' || item.dataset.category === category) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    }

    // Fungsi untuk menambah item ke keranjang
    function addToCart(productData) {
        const existingItem = cart.find(item => item.id === productData.id);
        if (existingItem) {
            existingItem.quantity++;
        } else {
            cart.push({
                id: productData.id,
                name: productData.name,
                price: parseFloat(productData.price),
                quantity: 1
            });
        }
        updateCartUI();
    }

    // Fungsi untuk mengubah kuantitas item di keranjang
    function updateQuantity(productId, increase) {
        const itemIndex = cart.findIndex(item => item.id === productId);
        if (itemIndex > -1) {
            if (increase) {
                cart[itemIndex].quantity++;
            } else {
                cart[itemIndex].quantity--;
                if (cart[itemIndex].quantity <= 0) {
                    cart.splice(itemIndex, 1); // Hapus item jika kuantitas 0
                }
            }
            updateCartUI();
        }
    }

    // Fungsi untuk mengupdate tampilan UI keranjang (modal dan badge)
    function updateCartUI() {
        // Update badge jumlah item
        const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
        if (totalItems > 0) {
            cartCountBadge.textContent = totalItems;
            cartCountBadge.style.display = 'block';
        } else {
            cartCountBadge.style.display = 'none';
        }

        // Update isi modal keranjang
        if (cart.length === 0) {
            cartItemsContainer.innerHTML = '<p class="text-center">Keranjang Anda kosong.</p>';
            cartTotalElement.textContent = 'Rp 0';
            return;
        }

        cartItemsContainer.innerHTML = cart.map(item => `
            <div class="cart-item">
                <div>
                    <strong>${item.name}</strong>
                    <br>
                    <small>Rp ${item.price.toLocaleString('id-ID')}</small>
                </div>
                <div class="quantity-controls">
                    <button class="btn btn-sm btn-outline-secondary decrease-qty" data-id="${item.id}">-</button>
                    <span class="mx-2">${item.quantity}</span>
                    <button class="btn btn-sm btn-outline-secondary increase-qty" data-id="${item.id}">+</button>
                </div>
                <div>
                    <strong>Rp ${(item.price * item.quantity).toLocaleString('id-ID')}</strong>
                </div>
            </div>
        `).join('');

        // Update total harga
        const totalPrice = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        cartTotalElement.textContent = `Rp ${totalPrice.toLocaleString('id-ID')}`;
    }
});