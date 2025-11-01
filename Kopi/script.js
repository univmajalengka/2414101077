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
    // Hapus referensi ke checkoutModal dan checkoutForm
    const checkoutButton = document.getElementById('checkout-btn');


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
            
            // Modifikasi "Beli Sekarang": Simpan ke session dan redirect
            saveCartAndRedirect();
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

    // 4. Tombol Checkout di Modal Keranjang (VERSI BARU)
    checkoutButton.addEventListener('click', () => {
        if (cart.length === 0) {
            alert('Keranjang Anda masih kosong!');
            return;
        }
        
        // Tutup modal keranjang
        cartModal.hide();
        
        // Panggil fungsi untuk simpan dan redirect
        saveCartAndRedirect();
    });
    
    // 5. Submit Form Pemesanan (DIHAPUS KARENA PINDAH KE checkout.php)


    // --- FUNGSI-FUNGSI ---

    // Fungsi baru untuk menyimpan keranjang ke session dan redirect
    function saveCartAndRedirect() {
        // Tampilkan loading (opsional, bisa ditambahkan di tombol)
        console.log("Menyimpan keranjang ke session...");

        fetch('save_cart_to_session.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ cart: cart }), // Kirim data keranjang
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Jika berhasil disimpan, redirect ke halaman checkout
                window.location.href = 'checkout.php';
            } else {
                alert('Gagal memproses keranjang. Coba lagi.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan koneksi.');
        });
    }

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