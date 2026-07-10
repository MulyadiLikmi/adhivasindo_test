/* ==========================================================================
   Pasarin — app.js
   Menghubungkan tampilan (landing, cart, admin) ke REST API backend.
   Token JWT disimpan di localStorage setelah login/register.
   ========================================================================== */

const API_BASE = '/api';

function authHeaders() {
    const token = localStorage.getItem('pasarin_token');
    return token
        ? { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json', 'Accept': 'application/json' }
        : { 'Content-Type': 'application/json', 'Accept': 'application/json' };
}

function formatRupiah(num) {
    return 'Rp ' + Number(num).toLocaleString('id-ID');
}

function cartKey() {
    const email = localStorage.getItem('pasarin_email') || 'guest';
    return `pasarin_cart_${email}`;
}
function getCart() {
    return JSON.parse(localStorage.getItem(cartKey()) || '[]');
}
function saveCart(cart) {
    localStorage.setItem(cartKey(), JSON.stringify(cart));
    updateCartCount();
}
function updateCartCount() {
    const el = document.getElementById('cart-count');
    if (el) el.textContent = getCart().reduce((sum, i) => sum + i.qty, 0);
}

/* ---------------- Landing page: load products from API ---------------- */
let currentCategoryId = '';

async function loadCategories() {
    const chipRow = document.getElementById('category-chips');
    if (!chipRow) return;
    try {
        const res = await fetch(`${API_BASE}/categories`);
        const categories = await res.json();
        categories.forEach(cat => {
            const chip = document.createElement('span');
            chip.className = 'chip';
            chip.textContent = cat.name;
            chip.dataset.categoryId = cat.id;
            chip.addEventListener('click', () => selectCategory(cat.id, chip));
            chipRow.appendChild(chip);
        });
        // "Semua" chip (sudah ada di HTML) juga perlu handler
        const allChip = chipRow.querySelector('.chip[data-category-id=""]');
        if (allChip) allChip.addEventListener('click', () => selectCategory('', allChip));
    } catch (e) {
        console.error('Gagal memuat kategori', e);
    }
}

function selectCategory(categoryId, chipEl) {
    currentCategoryId = categoryId;
    document.querySelectorAll('#category-chips .chip').forEach(c => c.classList.remove('active'));
    chipEl.classList.add('active');
    loadProducts();
}

const IMAGE_KEYWORD_MAP = {
    'beras': 'rice', 'nasi': 'rice',
    'minyak': 'oil',
    'gula': 'sugar',
    'teh': 'tea',
    'kopi': 'coffee',
    'susu': 'milk',
    'telur': 'egg',
    'air mineral': 'water', 'air': 'water',
    'keripik': 'chips', 'kripik': 'chips',
    'wafer': 'wafer', 'coklat': 'chocolate', 'cokelat': 'chocolate',
    'roti': 'bread',
    'mie': 'noodles', 'mi ': 'noodles',
    'buah': 'fruit',
    'sayur': 'vegetable',
};
const CATEGORY_KEYWORD_MAP = {
    'sembako': 'groceries',
    'minuman': 'drink',
    'snack': 'snack',
};

function productImageUrl(p) {
    const nameLower = (p.name || '').toLowerCase();
    let keyword = Object.keys(IMAGE_KEYWORD_MAP).find(key => nameLower.includes(key));
    keyword = keyword ? IMAGE_KEYWORD_MAP[keyword] : null;

    if (!keyword && p.category) {
        const catLower = (p.category.name || '').toLowerCase();
        keyword = CATEGORY_KEYWORD_MAP[catLower] || null;
    }
    if (!keyword) keyword = 'grocery';

    // lock=id supaya foto yang sama konsisten muncul tiap kali produk yang sama di-render
    return `https://loremflickr.com/400/300/${keyword}?lock=${p.id}`;
}

async function loadProducts(keyword = '') {
    const grid = document.getElementById('product-grid');
    if (!grid) return;
    const searchInput = document.querySelector('.market-search input[name="q"]');
    const q = keyword || (searchInput ? searchInput.value : '');
    const role = localStorage.getItem('pasarin_role');
    const isAdmin = role === 'admin';

    try {
        const params = new URLSearchParams();
        if (q) params.set('q', q);
        if (currentCategoryId) params.set('category_id', currentCategoryId);
        const url = `${API_BASE}/products${params.toString() ? '?' + params.toString() : ''}`;
        const res = await fetch(url);
        const data = await res.json();
        const items = data.data || [];

        grid.innerHTML = '';

        if (isAdmin) {
            const notice = document.createElement('div');
            notice.style.cssText = 'grid-column: 1/-1; background:#FBEAD0; border:1px solid var(--turmeric); border-radius:8px; padding:14px; font-size:13.5px; color:#5B4315;';
            notice.innerHTML = 'Kamu login sebagai <b>admin</b>. Belanja/checkout hanya untuk akun customer. Kelola barang & laporan ada di <a href="/admin/dashboard" style="color:var(--clay); font-weight:700;">Dashboard Admin</a>.';
            grid.appendChild(notice);
        }

        if (!items.length) {
            const empty = document.createElement('p');
            empty.style.cssText = 'grid-column: 1/-1; color: var(--ink-soft);';
            empty.textContent = 'Barang tidak ditemukan.';
            grid.appendChild(empty);
        }

        items.forEach(p => {
            const card = document.createElement('div');
            card.className = 'stall-card';
            const cartControls = isAdmin
                ? `<div class="qty-row"><span style="font-size:12px; color:var(--ink-soft);">Khusus customer</span></div>`
                : `<div class="qty-row">
                        <button type="button" onclick="changeQty(this, -1)">-</button>
                        <span class="qty-val">1</span>
                        <button type="button" onclick="changeQty(this, 1)">+</button>
                        <button type="button" class="btn-add" onclick="addToCart(${p.id}, '${p.name.replace(/'/g, "")}', ${p.price}, this)">+ Keranjang</button>
                   </div>`;
            card.innerHTML = `
                <div class="price-ribbon">${formatRupiah(p.price)}</div>
                <div class="thumb"><img src="${productImageUrl(p)}" alt="${p.name}" loading="lazy"></div>
                <div class="body">
                    <div class="name">${p.name}</div>
                    <div class="meta">Stok: ${p.stock}</div>
                    ${cartControls}
                </div>`;
            grid.appendChild(card);
        });
    } catch (e) {
        grid.innerHTML = '<p>Gagal memuat produk. Pastikan backend API berjalan.</p>';
    }
    updateCartCount();
}

function changeQty(btn, delta) {
    const row = btn.closest('.qty-row');
    const span = row.querySelector('.qty-val');
    let val = parseInt(span.textContent) + delta;
    if (val < 1) val = 1;
    span.textContent = val;
}

function addToCart(id, name, price, btn) {
    const row = btn.closest('.qty-row');
    const qty = parseInt(row.querySelector('.qty-val').textContent);
    const cart = getCart();
    const existing = cart.find(i => i.product_id === id);
    if (existing) existing.qty += qty; else cart.push({ product_id: id, name, price, qty });
    saveCart(cart);
}

/* ---------------- Cart page ---------------- */
function renderCart() {
    const body = document.getElementById('cart-body');
    if (!body) return;
    const cart = getCart();
    body.innerHTML = '';
    let total = 0;
    cart.forEach((item, idx) => {
        const subtotal = item.price * item.qty;
        total += subtotal;
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${item.name}</td>
            <td>${formatRupiah(item.price)}</td>
            <td>${item.qty}</td>
            <td>${formatRupiah(subtotal)}</td>
            <td><a href="#" onclick="removeFromCart(${idx}); return false;">Hapus</a></td>`;
        body.appendChild(tr);
    });
    document.getElementById('cart-total-item').textContent = cart.reduce((s, i) => s + i.qty, 0);
    document.getElementById('cart-total-amount').textContent = formatRupiah(total);
}

function removeFromCart(idx) {
    const cart = getCart();
    cart.splice(idx, 1);
    saveCart(cart);
    renderCart();
}

async function doCheckout() {
    const msg = document.getElementById('checkout-msg');
    const token = localStorage.getItem('pasarin_token');
    if (!token) {
        msg.style.color = 'var(--clay)';
        msg.textContent = 'Silakan masuk terlebih dahulu untuk checkout.';
        window.location.href = '/login';
        return;
    }
    const cart = getCart();
    if (!cart.length) {
        msg.textContent = 'Keranjang masih kosong.';
        return;
    }
    try {
        const res = await fetch(`${API_BASE}/checkout`, {
            method: 'POST',
            headers: authHeaders(),
            body: JSON.stringify({ items: cart.map(i => ({ product_id: i.product_id, qty: i.qty })) })
        });
        const data = await res.json();
        if (!res.ok) throw new Error(data.message || 'Checkout gagal');
        localStorage.removeItem(cartKey());
        msg.style.color = 'var(--forest)';
        msg.textContent = `Checkout berhasil! Kode order: ${data.data.order_code}. Status: Pending.`;
        renderCart();
    } catch (e) {
        msg.style.color = 'var(--clay)';
        msg.textContent = e.message;
    }
}

/* ---------------- Admin: dashboard report ---------------- */
async function loadSalesReport() {
    try {
        const res = await fetch(`${API_BASE}/report/sales`, { headers: authHeaders() });
        const data = await res.json();
        if (!res.ok) throw new Error(data.message);

        document.getElementById('stat-revenue').textContent = formatRupiah(data.summary.total_pendapatan);
        document.getElementById('stat-sold').textContent = data.summary.total_terjual;
        document.getElementById('stat-stock').textContent = data.summary.total_stok;
        document.getElementById('stat-pending').textContent = data.summary.order_pending;

        // Chart produk terlaris (bar chart CSS sederhana, tanpa library)
        const chartBox = document.getElementById('top-products-chart');
        const topProducts = data.top_products || [];
        if (!topProducts.length) {
            chartBox.innerHTML = '<p style="color:var(--ink-soft); font-size:13px;">Belum ada order yang dikonfirmasi.</p>';
        } else {
            const maxQty = Math.max(...topProducts.map(p => p.total_qty));
            chartBox.innerHTML = topProducts.map(p => `
                <div class="bar-chart-row">
                    <div class="bar-label">${p.product_name}</div>
                    <div class="bar-track"><div class="bar-fill" style="width:${(p.total_qty / maxQty * 100).toFixed(0)}%"></div></div>
                    <div class="bar-value">${p.total_qty} pcs</div>
                </div>`).join('');
        }

        const body = document.getElementById('orders-body');
        body.innerHTML = '';
        (data.orders.data || []).forEach(o => {
            const tr = document.createElement('tr');
            const actionCell = o.status === 'pending'
                ? `<button type="button" class="btn-confirm" onclick="confirmOrder(${o.id})">Konfirmasi</button>`
                : '—';
            tr.innerHTML = `
                <td>${o.order_code}</td>
                <td>${o.user ? o.user.name : '-'}</td>
                <td>${formatRupiah(o.total_amount)}</td>
                <td><span class="status-pill status-${o.status}">${o.status}</span></td>
                <td>${new Date(o.created_at).toLocaleDateString('id-ID')}</td>
                <td>${actionCell}</td>`;
            body.appendChild(tr);
        });
    } catch (e) {
        console.error(e);
    }
}

async function confirmOrder(orderId) {
    if (!confirm('Konfirmasi order ini? Pendapatan & stok terjual akan ikut terhitung setelah ini.')) return;
    try {
        const res = await fetch(`${API_BASE}/orders/${orderId}/status`, {
            method: 'PATCH',
            headers: authHeaders(),
            body: JSON.stringify({ status: 'completed' })
        });
        if (!res.ok) {
            const data = await res.json();
            throw new Error(data.message || 'Gagal konfirmasi order');
        }
        loadSalesReport();
    } catch (e) {
        alert(e.message);
    }
}

/* ---------------- Admin: product CRUD ---------------- */
async function loadCategoryOptions() {
    const select = document.getElementById('product-category-select');
    if (!select) return;
    try {
        const res = await fetch(`${API_BASE}/categories`);
        const categories = await res.json();
        categories.forEach(cat => {
            const opt = document.createElement('option');
            opt.value = cat.id;
            opt.textContent = cat.name;
            select.appendChild(opt);
        });
    } catch (e) {
        console.error('Gagal memuat kategori', e);
    }
}

async function loadAdminProducts() {
    const body = document.getElementById('products-body');
    if (!body) return;
    const res = await fetch(`${API_BASE}/products`);
    const data = await res.json();
    body.innerHTML = '';
    (data.data || []).forEach(p => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${p.name}</td>
            <td>${p.category ? p.category.name : '-'}</td>
            <td>${formatRupiah(p.price)}</td>
            <td>${p.stock}</td>
            <td><span class="status-pill ${p.is_active ? 'status-completed' : 'status-cancelled'}">${p.is_active ? 'Aktif' : 'Nonaktif'}</span></td>
            <td><a href="#" onclick='editProduct(${p.id}, ${JSON.stringify(p.name)}, ${p.price}, ${p.stock}, ${p.category_id ?? 'null'}); return false;'>Edit</a> ·
                <a href="#" onclick="deleteProduct(${p.id}); return false;">Hapus</a></td>`;
        body.appendChild(tr);
    });
}

function openProductModal() {
    document.getElementById('product-modal').style.display = 'flex';
}
function closeProductModal() {
    document.getElementById('product-modal').style.display = 'none';
    document.getElementById('product-form').reset();
}
function editProduct(id, name, price, stock, categoryId) {
    const form = document.getElementById('product-form');
    form.id.value = id;
    form.name.value = name;
    form.price.value = price;
    form.stock.value = stock;
    form.category_id.value = categoryId ?? '';
    document.getElementById('modal-title').textContent = 'Edit Barang';
    openProductModal();
}
async function deleteProduct(id) {
    if (!confirm('Hapus barang ini?')) return;
    await fetch(`${API_BASE}/products/${id}`, { method: 'DELETE', headers: authHeaders() });
    loadAdminProducts();
}

function doLogout() {
    localStorage.removeItem('pasarin_token');
    localStorage.removeItem('pasarin_role');
    localStorage.removeItem('pasarin_email');
    localStorage.removeItem('pasarin_name');
    window.location.href = '/login';
}

function renderAuthArea() {
    const area = document.getElementById('auth-area');
    if (!area) return;
    const token = localStorage.getItem('pasarin_token');
    const name = localStorage.getItem('pasarin_name');
    const role = localStorage.getItem('pasarin_role');

    if (token && name) {
        const roleLabel = role === 'admin' ? 'Admin' : 'Customer';
        const dashboardLink = role === 'admin'
            ? `<a href="/admin/dashboard" style="margin-right:14px;">⚙️ Dashboard Admin</a>`
            : '';
        area.innerHTML = `
            <span style="margin-right:12px; font-size:13.5px;">👤 ${name} <span style="opacity:.75;">(${roleLabel})</span></span>
            ${dashboardLink}
            <a href="#" id="navbar-logout">Keluar</a>`;
        document.getElementById('navbar-logout').addEventListener('click', function (e) {
            e.preventDefault();
            doLogout();
        });
    } else {
        area.innerHTML = `<a href="/login">Masuk</a>`;
    }
}

// Browser sering menampilkan halaman dari cache (bfcache) saat tombol Back diklik,
// tanpa menjalankan ulang JS. Ini memaksa data disegarkan dari localStorage tiap kali itu terjadi.
window.addEventListener('pageshow', function (event) {
    updateCartCount();
    if (document.getElementById('cart-body')) renderCart();
    if (document.getElementById('auth-area')) renderAuthArea();
});

document.addEventListener('DOMContentLoaded', function () {
    updateCartCount();
    renderAuthArea();
    if (typeof loadCategories === 'function') loadCategories();
    if (typeof loadCategoryOptions === 'function') loadCategoryOptions();

    const addBtn = document.getElementById('add-product-btn');
    if (addBtn) addBtn.addEventListener('click', () => {
        document.getElementById('product-form').reset();
        document.getElementById('modal-title').textContent = 'Tambah Barang';
        openProductModal();
    });

    const cancelBtn = document.getElementById('cancel-modal');
    if (cancelBtn) cancelBtn.addEventListener('click', closeProductModal);

    const productForm = document.getElementById('product-form');
    if (productForm) productForm.addEventListener('submit', async function (e) {
        e.preventDefault();
        const form = new FormData(this);
        const id = form.get('id');
        const payload = {
            name: form.get('name'),
            price: Number(form.get('price')),
            stock: Number(form.get('stock')),
            category_id: form.get('category_id') || null,
        };
        const url = id ? `${API_BASE}/products/${id}` : `${API_BASE}/products`;
        const method = id ? 'PUT' : 'POST';
        await fetch(url, { method, headers: authHeaders(), body: JSON.stringify(payload) });
        closeProductModal();
        loadAdminProducts();
    });

    const logoutLink = document.getElementById('admin-logout');
    if (logoutLink) logoutLink.addEventListener('click', function (e) {
        e.preventDefault();
        doLogout();
    });

    // Search box di landing page
    const searchForm = document.querySelector('.market-search');
    if (searchForm) searchForm.addEventListener('submit', function (e) {
        e.preventDefault();
        loadProducts(this.q.value);
    });
});
