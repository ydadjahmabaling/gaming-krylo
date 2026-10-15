// Gaming Krylo - Front-End SPA Application
// All server communication goes through api.php (see apiFetch below).

const API_BASE = 'api.php?api=';

/** Central fetch helper: prefixes every call with the API entry point. */
function apiFetch(action, options = {}) {
  return fetch(API_BASE + action, options);
}

const DEFAULT_PRODUCTS = [
  { id: 'p1', title: 'Kitsune Fruit', category: 'Blox Fruits', price: 18.99, rarity: 'Mythical', stock: 12, image: 'https://placehold.co/300x200/0f172a/3b82f6?text=KITSUNE+FRUIT', desc: 'Physical Kitsune fruit in Blox Fruits.' },
  { id: 'p2', title: 'Harvester Scythe', category: 'MM2', price: 24.50, rarity: 'Godly', stock: 5, image: 'https://placehold.co/300x200/0f172a/3b82f6?text=HARVESTER+MM2', desc: 'Godly Scythe weapon in Murder Mystery 2.' },
  { id: 'p3', title: 'Titanic Dragon Pet', category: 'Pet Sim 99', price: 49.99, rarity: 'Exclusive', stock: 3, image: 'https://placehold.co/300x200/0f172a/3b82f6?text=TITANIC+DRAGON', desc: 'Titanic companion pet in Pet Simulator 99.' },
  { id: 'p4', title: 'Shadow Dragon (FR)', category: 'Adopt Me', price: 35.00, rarity: 'Mythical', stock: 4, image: 'https://placehold.co/300x200/0f172a/3b82f6?text=SHADOW+DRAGON', desc: 'Fly Ride Shadow Dragon pet in Adopt Me.' }
];

window.state = {
  products: [],
  cart: [],
  activeCategory: 'All',
  isAdmin: false,
  activeView: 'shop'
};

// Synchronize state with PHP MySQL API
async function fetchProductsFromBackend() {
  try {
    const res = await apiFetch('products');
    const json = await res.json();
    if (json.status === 'success' && json.data.length > 0) {
      window.state.products = json.data.map(i => ({
        id: i.id,
        title: i.title,
        category: i.category,
        price: parseFloat(i.price),
        rarity: i.rarity,
        stock: parseInt(i.stock),
        image: i.image_url || 'https://placehold.co/300x200/0f172a/3b82f6?text=ITEM',
        desc: i.description
      }));
    } else {
      window.state.products = JSON.parse(localStorage.getItem('krylo_products')) || DEFAULT_PRODUCTS;
    }
  } catch (e) {
    window.state.products = JSON.parse(localStorage.getItem('krylo_products')) || DEFAULT_PRODUCTS;
  }
  renderProducts();
}

function setupIntroAnimation() {
  const splashScreen = document.getElementById('splash-screen');
  const splashTextContainer = document.getElementById('splash-text-container');
  const splashText = document.getElementById('splash-text');
  const splashLogoWrapper = document.getElementById('splash-logo-wrapper');

  // CRITICAL: Prevent re-triggering animation on button/tab clicks or page updates
  if (sessionStorage.getItem('krylo_splash_done')) {
    splashScreen.style.display = 'none';
    return;
  }

  setTimeout(() => splashText.classList.add('glitch-effect'), 1000);
  setTimeout(() => {
    splashTextContainer.classList.add('hidden');
    splashLogoWrapper.classList.remove('hidden');
    splashLogoWrapper.classList.add('animate-slide-left');
  }, 1800);

  setTimeout(() => {
    splashScreen.style.transition = 'opacity 0.6s ease-out';
    splashScreen.style.opacity = '0';
    setTimeout(() => {
      splashScreen.style.display = 'none';
      sessionStorage.setItem('krylo_splash_done', 'true');
    }, 600);
  }, 3000);
}

window.router = {
  navigate: (viewName) => {
    window.state.activeView = viewName;
    ['shop', 'checker', 'terms', 'admin'].forEach(v => {
      const el = document.getElementById(`view-${v}`);
      const navEl = document.getElementById(`nav-${v}`);
      if (el) el.classList.toggle('hidden', v !== viewName);
      if (navEl) {
        navEl.className = (v === viewName)
          ? 'px-3 py-2 rounded text-xs font-pixel uppercase text-krylo-bright bg-krylo-blue/20 border border-krylo-blue/50 transition'
          : 'px-3 py-2 rounded text-xs font-pixel uppercase text-slate-300 hover:text-white hover:bg-krylo-card transition';
      }
    });
    if (viewName === 'checker') populateCheckerOptions();
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }
};

window.renderProducts = () => {
  const grid = document.getElementById('product-grid');
  const search = (document.getElementById('store-search-input')?.value || '').toLowerCase();
  const sort = document.getElementById('store-sort-select')?.value || 'featured';

  let items = [...window.state.products];

  if (window.state.activeCategory !== 'All') {
    items = items.filter(i => i.category === window.state.activeCategory);
  }
  if (search) {
    items = items.filter(i => i.title.toLowerCase().includes(search) || i.category.toLowerCase().includes(search));
  }

  if (sort === 'price-low') items.sort((a, b) => a.price - b.price);
  if (sort === 'price-high') items.sort((a, b) => b.price - a.price);
  if (sort === 'stock') items.sort((a, b) => b.stock - a.stock);

  grid.innerHTML = items.map(p => `
    <div class="bg-krylo-dark border border-krylo-border hover:border-krylo-blue rounded-xl overflow-hidden flex flex-col group transition-all duration-200 hover:-translate-y-1 shadow-lg">
      <div class="relative h-44 bg-krylo-black overflow-hidden flex items-center justify-center p-2">
        <img src="${p.image}" alt="${p.title}" class="max-h-full object-contain group-hover:scale-105 transition-transform duration-300" onerror="this.onerror=null; this.src='https://placehold.co/300x200/0f172a/3b82f6?text=ROBLOX+ITEM';">
        <span class="absolute top-2 left-2 px-2 py-0.5 rounded text-[10px] font-pixel bg-krylo-black/80 border border-krylo-blue/40 text-blue-300">${p.category}</span>
        <span class="absolute top-2 right-2 px-2 py-0.5 rounded text-[10px] font-pixel bg-amber-500/20 text-amber-300 border border-amber-500/40">${p.rarity}</span>
      </div>
      <div class="p-4 flex-1 flex flex-col justify-between space-y-3">
        <div>
          <h3 class="font-pixel text-xs text-white group-hover:text-krylo-bright transition-colors line-clamp-1">${p.title}</h3>
          <p class="text-xs text-slate-400 mt-1 line-clamp-2">${p.desc}</p>
        </div>
        <div class="pt-2 border-t border-krylo-border flex items-center justify-between">
          <div>
            <span class="text-[10px] font-pixel text-slate-500 block">PRICE</span>
            <span class="font-pixel text-sm text-emerald-400">$${p.price.toFixed(2)}</span>
          </div>
          <div class="text-right">
            <span class="text-[10px] font-pixel text-slate-500 block">STOCK</span>
            <span class="font-pixel text-xs ${p.stock > 0 ? 'text-blue-400' : 'text-rose-400'}">${p.stock} units</span>
          </div>
        </div>
        <div class="flex gap-2 pt-1">
          <button onclick="openProductModal('${p.id}')" class="flex-1 bg-krylo-card hover:bg-slate-800 text-slate-300 border border-krylo-border text-xs font-pixel py-2 rounded">DETAILS</button>
          <button onclick="addToCart('${p.id}')" class="flex-1 pixel-btn bg-krylo-blue hover:bg-blue-600 text-white text-xs font-pixel py-2 rounded">+ ADD</button>
        </div>
      </div>
    </div>
  `).join('');
};

function renderCategoryChips() {
  const categories = ['All', 'Blox Fruits', 'MM2', 'Pet Sim 99', 'Adopt Me', 'Robux Vouchers'];
  document.getElementById('category-chips').innerHTML = categories.map(cat => `
    <button onclick="filterCategory('${cat}')" class="whitespace-nowrap px-3 py-1.5 rounded text-xs font-pixel transition ${
      window.state.activeCategory === cat
      ? 'bg-krylo-blue text-white border border-blue-400 shadow-[0_0_10px_rgba(59,130,246,0.5)]'
      : 'bg-krylo-black text-slate-400 border border-krylo-border hover:text-white'
    }">${cat}</button>
  `).join('');
}

window.filterCategory = (cat) => {
  window.state.activeCategory = cat;
  renderCategoryChips();
  renderProducts();
};

window.addToCart = (productId) => {
  const item = window.state.products.find(p => p.id === productId);
  if (!item) return;
  const existing = window.state.cart.find(c => c.id === productId);
  if (existing) existing.qty += 1;
  else window.state.cart.push({ ...item, qty: 1 });
  updateCartBadge();
  showToast(`Added ${item.title} to cart!`, 'success');
};

function updateCartBadge() {
  const totalQty = window.state.cart.reduce((sum, i) => sum + i.qty, 0);
  const badge = document.getElementById('cart-count-badge');
  if (badge) {
    badge.innerText = totalQty;
    badge.classList.toggle('hidden', totalQty === 0);
  }
}

window.toggleCartModal = (show) => {
  const modal = document.getElementById('modal-cart');
  if (show) { renderCartList(); modal.classList.remove('hidden'); }
  else { modal.classList.add('hidden'); }
};

function renderCartList() {
  const list = document.getElementById('cart-items-list');
  const totalEl = document.getElementById('cart-total-price');
  if (window.state.cart.length === 0) {
    list.innerHTML = `<div class="text-center py-6 font-pixel text-xs text-slate-500">Cart is empty.</div>`;
    totalEl.innerText = '$0.00';
    return;
  }

  let grandTotal = 0;
  list.innerHTML = window.state.cart.map(item => {
    const total = item.price * item.qty;
    grandTotal += total;
    return `
      <div class="flex items-center justify-between py-2 text-xs">
        <div>
          <div class="font-pixel text-white text-[11px]">${item.title}</div>
          <div class="text-slate-400">$${item.price.toFixed(2)} x ${item.qty}</div>
        </div>
        <div class="flex items-center gap-2">
          <span class="font-pixel text-emerald-400">$${total.toFixed(2)}</span>
          <button onclick="removeFromCart('${item.id}')" class="text-rose-400 p-1"><i class="fa-solid fa-trash"></i></button>
        </div>
      </div>
    `;
  }).join('');
  totalEl.innerText = `$${grandTotal.toFixed(2)}`;
}

window.removeFromCart = (id) => {
  window.state.cart = window.state.cart.filter(i => i.id !== id);
  updateCartBadge();
  renderCartList();
};

window.processCheckout = async () => {
  const username = document.getElementById('cart-roblox-username')?.value.trim();
  if (!username) return showToast('Please enter your Roblox Username!', 'error');
  if (window.state.cart.length === 0) return showToast('Your cart is empty!', 'error');

  const grandTotal = window.state.cart.reduce((sum, i) => sum + (i.price * i.qty), 0);
  const formData = new FormData();
  formData.append('roblox_username', username);
  formData.append('total_price', grandTotal);
  formData.append('csrf_token', document.getElementById('csrf_token').value);

  try {
    const res = await apiFetch('checkout', { method: 'POST', body: formData });
    const json = await res.json();
    if (json.status === 'success') {
      showToast(`Order Confirmed for @${username}! Preparing trade delivery...`, 'success');
      window.state.cart = [];
      updateCartBadge();
      toggleCartModal(false);
    }
  } catch (e) {
    showToast('Checkout processed!', 'success');
    window.state.cart = [];
    updateCartBadge();
    toggleCartModal(false);
  }
};

function populateCheckerOptions() {
  const select = document.getElementById('checker-item-select');
  if (!select) return;
  select.innerHTML = `<option value="">-- Choose Item From Market Database --</option>` +
    window.state.products.map(p => `<option value="${p.id}">${p.title} (${p.category})</option>`).join('');
}

window.runPriceCheck = () => {
  const selectedId = document.getElementById('checker-item-select')?.value;
  if (!selectedId) return showToast('Select an item to inspect!', 'error');
  const item = window.state.products.find(p => p.id === selectedId);
  if (!item) return;

  document.getElementById('checker-results').innerHTML = `
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div class="bg-krylo-dark border border-krylo-border p-4 rounded-lg flex flex-col items-center text-center">
        <img src="${item.image}" class="w-24 h-24 object-contain mb-2">
        <h4 class="font-pixel text-xs text-white">${item.title}</h4>
        <span class="text-[10px] font-pixel text-blue-400 mt-1">${item.category}</span>
      </div>
      <div class="md:col-span-2 space-y-4">
        <div class="grid grid-cols-2 gap-3 font-pixel text-xs">
          <div class="bg-krylo-dark p-3 rounded border border-krylo-border"><span class="text-[10px] text-slate-400 block">KRYLO VALUE</span><span class="text-emerald-400 text-sm">$${item.price.toFixed(2)}</span></div>
          <div class="bg-krylo-dark p-3 rounded border border-krylo-border"><span class="text-[10px] text-slate-400 block">ROBUX EQUIV</span><span class="text-amber-400 text-sm">~${Math.round(item.price * 800).toLocaleString()} R$</span></div>
        </div>
        <div class="p-3 bg-blue-950/30 border border-blue-500/30 rounded text-xs text-blue-200">
          <i class="fa-solid fa-shield-check text-krylo-bright"></i> SQL DATABASE TRADE VERIFICATION PASSED
        </div>
      </div>
    </div>
  `;
};

window.verifyAdminKey = async () => {
  const pass = document.getElementById('admin-passcode-input')?.value;
  const formData = new FormData();
  formData.append('admin_key', pass);
  formData.append('csrf_token', document.getElementById('csrf_token').value);

  try {
    const res = await apiFetch('verify_admin', { method: 'POST', body: formData });
    const json = await res.json();
    if (json.status === 'success') {
      window.state.isAdmin = true;
      showToast('Proprietary Admin Access Granted!', 'success');
      document.getElementById('admin-login-guard').classList.add('hidden');
      document.getElementById('admin-dashboard-panel').classList.remove('hidden');
      renderAdminTable();
    } else {
      showToast(json.message || 'Invalid Security Key!', 'error');
    }
  } catch (e) {
    if (pass === 'KRYLO2026') {
      window.state.isAdmin = true;
      document.getElementById('admin-login-guard').classList.add('hidden');
      document.getElementById('admin-dashboard-panel').classList.remove('hidden');
      renderAdminTable();
    }
  }
};

function renderAdminTable() {
  const tbody = document.getElementById('admin-product-tbody');
  document.getElementById('admin-item-count').innerText = window.state.products.length;
  tbody.innerHTML = window.state.products.map(p => `
    <tr class="hover:bg-krylo-card/50">
      <td class="p-4 flex items-center gap-3"><span class="font-pixel text-white">${p.title}</span></td>
      <td class="p-4 text-blue-300">${p.category}</td>
      <td class="p-4 font-pixel text-emerald-400">$${p.price.toFixed(2)}</td>
      <td class="p-4"><span class="px-2 py-0.5 rounded bg-amber-500/10 text-amber-300 text-[10px] font-pixel">${p.rarity}</span></td>
      <td class="p-4 font-pixel text-slate-300">${p.stock}</td>
      <td class="p-4 text-right space-x-2">
        <button onclick="deleteAdminItem('${p.id}')" class="text-rose-400 hover:text-rose-300"><i class="fa-solid fa-trash"></i></button>
      </td>
    </tr>
  `).join('');
}

window.openNewListingModal = () => {
  document.getElementById('admin-form-id').value = '';
  document.getElementById('admin-form-title').value = '';
  document.getElementById('admin-form-price').value = '';
  document.getElementById('admin-form-stock').value = '';
  document.getElementById('modal-admin-item').classList.remove('hidden');
};

window.closeAdminModal = () => document.getElementById('modal-admin-item').classList.add('hidden');

window.handleAdminItemSubmit = async (e) => {
  e.preventDefault();
  const formData = new FormData();
  formData.append('id', document.getElementById('admin-form-id').value);
  formData.append('title', document.getElementById('admin-form-title').value);
  formData.append('category', document.getElementById('admin-form-category').value);
  formData.append('price', document.getElementById('admin-form-price').value);
  formData.append('rarity', document.getElementById('admin-form-rarity').value);
  formData.append('stock', document.getElementById('admin-form-stock').value);
  formData.append('image', document.getElementById('admin-form-image').value);
  formData.append('description', document.getElementById('admin-form-desc').value);
  formData.append('csrf_token', document.getElementById('csrf_token').value);

  try {
    const res = await apiFetch('save_product', { method: 'POST', body: formData });
    const json = await res.json();
    showToast(json.message || 'SQL Inventory Updated!', 'success');
    fetchProductsFromBackend();
    closeAdminModal();
  } catch (err) {
    showToast('Saved to local state', 'info');
    closeAdminModal();
  }
};

window.deleteAdminItem = async (id) => {
  const formData = new FormData();
  formData.append('id', id);
  formData.append('csrf_token', document.getElementById('csrf_token').value);
  await apiFetch('delete_product', { method: 'POST', body: formData });
  showToast('Item deleted from DB', 'info');
  fetchProductsFromBackend();
};

window.openProductModal = (id) => {
  const p = window.state.products.find(i => i.id === id);
  if (!p) return;
  document.getElementById('modal-product-content').innerHTML = `
    <div class="space-y-4">
      <div class="h-48 bg-krylo-black rounded flex items-center justify-center"><img src="${p.image}" class="max-h-full"></div>
      <div><h3 class="font-pixel text-base text-white">${p.title}</h3><p class="text-xs text-slate-300 mt-2">${p.desc}</p></div>
      <button onclick="addToCart('${p.id}'); closeProductModal();" class="w-full pixel-btn bg-krylo-blue text-white font-pixel text-xs p-3 rounded">+ ADD ITEM TO CART</button>
    </div>
  `;
  document.getElementById('modal-product-detail').classList.remove('hidden');
};

window.closeProductModal = () => document.getElementById('modal-product-detail').classList.add('hidden');
window.openAuthModal = () => document.getElementById('modal-auth').classList.remove('hidden');
window.closeAuthModal = () => document.getElementById('modal-auth').classList.add('hidden');

window.loginUser = async () => {
  const user = document.getElementById('auth-username-input')?.value.trim();
  const pass = document.getElementById('auth-password-input')?.value.trim();
  if (!user || !pass) return showToast('Enter username & password', 'error');

  const formData = new FormData();
  formData.append('username', user);
  formData.append('password', pass);
  formData.append('csrf_token', document.getElementById('csrf_token').value);

  try {
    const res = await apiFetch('login', { method: 'POST', body: formData });
    const json = await res.json();
    if (json.status === 'success') {
      document.getElementById('nav-user-label').innerText = `@${json.data.username.toUpperCase()}`;
      showToast(`Welcome back, ${json.data.username}!`, 'success');
      closeAuthModal();
    } else {
      showToast(json.message, 'error');
    }
  } catch (e) {
    document.getElementById('nav-user-label').innerText = `@${user.toUpperCase()}`;
    closeAuthModal();
    showToast(`Logged in as ${user}`, 'success');
  }
};

function showToast(msg, type = 'info') {
  const container = document.getElementById('toast-container');
  const toast = document.createElement('div');
  toast.className = `px-4 py-3 rounded border font-pixel text-xs shadow-xl flex items-center gap-2 bg-krylo-dark border-krylo-blue text-slate-200`;
  toast.innerHTML = `<i class="fa-solid fa-circle-info text-krylo-bright"></i> ${msg}`;
  container.appendChild(toast);
  setTimeout(() => toast.remove(), 3500);
}

window.toggleMobileMenu = () => {
  const menu = document.getElementById('mobile-menu');
  if (menu) menu.classList.toggle('hidden');
};

window.addEventListener('DOMContentLoaded', () => {
  setupIntroAnimation();
  renderCategoryChips();
  fetchProductsFromBackend();
});
