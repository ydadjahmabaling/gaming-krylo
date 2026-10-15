<?php
// Gaming Krylo - Storefront frontend (single-page app shell).
// All data operations are handled by api.php; this file only renders HTML.

declare(strict_types=1);

require_once __DIR__ . '/src/bootstrap.php';

Security::startSession();
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gaming Krylo - Roblox Virtual Store | Krylo Digital LLC</title>

  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@400;600;700&family=Press+Start+2P&family=Silkscreen:wght@400;700&display=swap" rel="stylesheet">
  <!-- FontAwesome Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- Gaming Krylo custom styles -->
  <link rel="stylesheet" href="public/assets/css/style.css">

  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          colors: {
            krylo: {
              black: '#05070d',
              dark: '#0a0e1a',
              card: '#10172a',
              border: '#1e293b',
              blue: '#3b82f6',
              neon: '#2563eb',
              cyan: '#06b6d4',
              bright: '#60a5fa',
              light: '#f8fafc'
            }
          },
          fontFamily: {
            pixel: ['"Press Start 2P"', 'monospace'],
            pixelAlt: ['"Silkscreen"', 'cursive'],
            tech: ['"Chakra Petch"', 'sans-serif']
          }
        }
      }
    }
  </script>
</head>

<body class="bg-krylo-black text-slate-100 font-tech min-h-screen flex flex-col selection:bg-krylo-blue selection:text-white">

  <input type="hidden" id="csrf_token" value="<?php echo htmlspecialchars(Security::csrfToken(), ENT_QUOTES, 'UTF-8'); ?>">

  <!-- SCANLINE OVERLAY -->
  <div class="fixed inset-0 scanlines z-40 opacity-40 pointer-events-none"></div>

  <!-- INTRO ANIMATION SPLASH SCREEN (Executes ONCE per session) -->
  <div id="splash-screen" class="fixed inset-0 z-50 bg-krylo-black flex flex-col items-center justify-center p-4">
    <div id="splash-text-container" class="animate-slide-left text-center max-w-2xl">
      <h1 id="splash-text" class="font-pixel text-xl sm:text-2xl md:text-3xl text-krylo-bright tracking-wider leading-relaxed neon-text-glow">
        Go with gaming at krylo
      </h1>
      <p class="font-pixelAlt text-xs text-blue-300 mt-4 tracking-widest uppercase opacity-80">
        By Krylo Digital LLC
      </p>
    </div>

    <div id="splash-logo-wrapper" class="hidden flex-col items-center justify-center">
      <img id="splash-logo" src="logo.png" alt="Gaming Krylo Logo" class="w-36 h-36 md:w-48 md:h-48 object-contain drop-shadow-[0_0_25px_rgba(59,130,246,0.9)] animate-pulse" onerror="this.onerror=null; this.src='https://placehold.co/200x200/0a0e1a/3b82f6?text=KRYLO+LOGO';">
      <h2 class="font-pixel text-lg md:text-xl text-white mt-4 tracking-widest text-center">
        GAMING KRYLO
      </h2>
    </div>
  </div>

  <!-- MAIN APP WRAPPER -->
  <div id="app-container" class="flex-1 flex flex-col min-h-screen">

    <!-- HEADER NAVBAR -->
    <header class="sticky top-0 z-30 bg-krylo-dark/95 border-b-2 border-krylo-blue/40 backdrop-blur-md">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">

        <!-- Brand Logo & Title -->
        <button onclick="router.navigate('shop')" class="flex items-center gap-3 group text-left">
          <div class="relative">
            <img src="logo.png" alt="Gaming Krylo Logo" class="w-10 h-10 md:w-12 md:h-12 object-contain group-hover:scale-105 transition-transform" onerror="this.onerror=null; this.src='https://placehold.co/100x100/0a0e1a/3b82f6?text=KRYLO';">
            <span class="absolute -top-1 -right-1 flex h-3 w-3">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
              <span class="relative inline-flex rounded-full h-3 w-3 bg-krylo-blue"></span>
            </span>
          </div>
          <div>
            <span class="font-pixel text-sm md:text-base text-white tracking-wider block group-hover:text-krylo-bright transition-colors">
              GAMING KRYLO
            </span>
            <span class="font-pixelAlt text-[10px] text-blue-400 block tracking-widest">
              KRYLO DIGITAL LLC
            </span>
          </div>
        </button>

        <!-- Desktop Navigation Links -->
        <nav class="hidden md:flex items-center gap-1 lg:gap-2">
          <button onclick="router.navigate('shop')" id="nav-shop" class="px-3 py-2 rounded text-xs font-pixel uppercase tracking-wider text-krylo-bright bg-krylo-blue/20 border border-krylo-blue/50 transition">
            <i class="fa-solid fa-gamepad mr-1.5"></i> Roblox Store
          </button>
          <button onclick="router.navigate('checker')" id="nav-checker" class="px-3 py-2 rounded text-xs font-pixel uppercase tracking-wider text-slate-300 hover:text-white hover:bg-krylo-card transition">
            <i class="fa-solid fa-square-check mr-1.5 text-krylo-blue"></i> Price Verifier
          </button>
          <button onclick="router.navigate('terms')" id="nav-terms" class="px-3 py-2 rounded text-xs font-pixel uppercase tracking-wider text-slate-300 hover:text-white hover:bg-krylo-card transition">
            <i class="fa-solid fa-shield-halved mr-1.5 text-blue-400"></i> Terms & ToS
          </button>
          <button onclick="router.navigate('admin')" id="nav-admin" class="px-3 py-2 rounded text-xs font-pixel uppercase tracking-wider text-amber-400 hover:bg-amber-400/10 transition border border-amber-500/30">
            <i class="fa-solid fa-user-gear mr-1.5"></i> Admin Hub
          </button>
        </nav>

        <!-- Right Side Actions -->
        <div class="flex items-center gap-3">
          <button onclick="toggleCartModal(true)" class="relative p-2.5 bg-krylo-card border border-krylo-blue/40 rounded text-slate-200 hover:text-krylo-bright hover:border-krylo-blue transition">
            <i class="fa-solid fa-cart-shopping text-base"></i>
            <span id="cart-count-badge" class="absolute -top-2 -right-2 bg-krylo-blue text-white font-pixel text-[10px] w-5 h-5 rounded-full flex items-center justify-center border border-black hidden">0</span>
          </button>

          <div id="user-nav-block">
            <button onclick="openAuthModal()" class="pixel-btn bg-krylo-blue hover:bg-blue-600 text-white font-pixel text-xs px-3 py-2 rounded flex items-center gap-2 shadow-lg">
              <i class="fa-solid fa-user"></i>
              <span id="nav-user-label" class="hidden sm:inline"><?php echo isset($_SESSION['username']) ? '@' . strtoupper(htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8')) : 'LOGIN'; ?></span>
            </button>
          </div>

          <button onclick="toggleMobileMenu()" class="md:hidden p-2 text-slate-300 hover:text-white">
            <i class="fa-solid fa-bars text-xl"></i>
          </button>
        </div>
      </div>

      <!-- Mobile Dropdown Menu -->
      <div id="mobile-menu" class="hidden md:hidden border-t border-krylo-border bg-krylo-dark px-4 py-3 flex-col gap-2">
        <button onclick="router.navigate('shop'); toggleMobileMenu()" class="text-left w-full px-3 py-2 text-xs font-pixel text-krylo-bright hover:bg-krylo-card rounded">
          <i class="fa-solid fa-gamepad mr-2"></i> Roblox Store
        </button>
        <button onclick="router.navigate('checker'); toggleMobileMenu()" class="text-left w-full px-3 py-2 text-xs font-pixel text-slate-300 hover:bg-krylo-card rounded">
          <i class="fa-solid fa-square-check mr-2 text-krylo-blue"></i> Price & Listing Verifier
        </button>
        <button onclick="router.navigate('terms'); toggleMobileMenu()" class="text-left w-full px-3 py-2 text-xs font-pixel text-slate-300 hover:bg-krylo-card rounded">
          <i class="fa-solid fa-shield-halved mr-2 text-blue-400"></i> Terms & Conditions
        </button>
        <button onclick="router.navigate('admin'); toggleMobileMenu()" class="text-left w-full px-3 py-2 text-xs font-pixel text-amber-400 hover:bg-krylo-card rounded">
          <i class="fa-solid fa-user-gear mr-2"></i> Admin Panel
        </button>
      </div>
    </header>

    <!-- MAIN CONTENT VIEWS (Single Page Routing) -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">

      <!-- VIEW 1: STORE FRONT -->
      <section id="view-shop" class="space-y-8">

        <div class="relative rounded-xl overflow-hidden bg-gradient-to-r from-blue-950 via-krylo-card to-slate-950 border-2 border-krylo-blue/50 p-6 md:p-10 shadow-2xl">
          <div class="absolute -right-10 -bottom-10 w-72 h-72 bg-krylo-blue/10 rounded-full blur-3xl pointer-events-none"></div>
          <div class="max-w-2xl relative z-10 space-y-4">
            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-pixel bg-blue-500/10 text-krylo-bright border border-blue-500/30">
              <i class="fa-solid fa-bolt text-amber-400"></i> PHP PDO & SQL Backend Active
            </span>
            <h1 class="font-pixel text-xl sm:text-2xl md:text-3xl text-white tracking-wide leading-tight">
              PRO-TIER ROBLOX ITEMS & CURRENCY
            </h1>
            <p class="text-slate-300 text-sm md:text-base">
              Verified Blox Fruits, MM2 Godlies, Pet Simulator 99 Titanics, Adopt Me Pets & Robux Vouchers. Direct delivery powered by Krylo Digital LLC.
            </p>
            <div class="flex flex-wrap gap-3 pt-2">
              <button onclick="filterCategory('All')" class="pixel-btn bg-krylo-blue text-white font-pixel text-xs px-4 py-2.5 rounded shadow">
                BROWSE ALL GOODS
              </button>
              <button onclick="router.navigate('checker')" class="bg-krylo-dark hover:bg-slate-800 text-slate-200 border border-krylo-blue/40 font-pixel text-xs px-4 py-2.5 rounded">
                CHECK PRICE HISTORY
              </button>
            </div>
          </div>
        </div>

        <!-- Filter & Search Control Bar -->
        <div class="flex flex-col lg:flex-row gap-4 items-stretch lg:items-center justify-between bg-krylo-dark/80 p-4 rounded-lg border border-krylo-border">
          <div class="flex items-center gap-2 overflow-x-auto pb-2 lg:pb-0 scrollbar-none" id="category-chips"></div>

          <div class="flex items-center gap-3">
            <div class="relative flex-1 sm:w-64">
              <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
              <input type="text" id="store-search-input" oninput="renderProducts()" placeholder="Search Blox Fruits, MM2..." class="w-full pl-9 pr-3 py-2 bg-krylo-black border border-krylo-border rounded text-sm text-slate-100 focus:outline-none focus:border-krylo-blue">
            </div>

            <select id="store-sort-select" onchange="renderProducts()" class="bg-krylo-black border border-krylo-border text-xs text-slate-200 rounded px-3 py-2 focus:outline-none focus:border-krylo-blue">
              <option value="featured">Featured First</option>
              <option value="price-low">Price: Low to High</option>
              <option value="price-high">Price: High to Low</option>
              <option value="stock">In Stock First</option>
            </select>
          </div>
        </div>

        <!-- Product Listing Grid -->
        <div id="product-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6"></div>
      </section>

      <!-- VIEW 2: PRICE & TRADE VERIFIER SYSTEM -->
      <section id="view-checker" class="hidden space-y-6">
        <div class="bg-krylo-dark border border-krylo-blue/40 rounded-xl p-6 md:p-8 space-y-6">
          <div class="border-b border-krylo-border pb-4 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
              <h2 class="font-pixel text-lg md:text-xl text-white flex items-center gap-2">
                <i class="fa-solid fa-chart-line text-krylo-blue"></i> KRYLO PRICE & TRADE VERIFIER
              </h2>
              <p class="text-xs text-slate-400 mt-1">
                Proprietary market tracking tool to verify fair trade values & SQL database listings.
              </p>
            </div>
            <span class="px-3 py-1 rounded bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 text-xs font-pixel">
              <i class="fa-solid fa-signal mr-1"></i> MySQL Engine Connected
            </span>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="md:col-span-2">
              <label class="block font-pixel text-xs text-slate-300 mb-2">SELECT ITEM TO VERIFY:</label>
              <select id="checker-item-select" onchange="runPriceCheck()" class="w-full bg-krylo-black border border-krylo-border text-slate-200 font-pixel text-xs rounded p-3 focus:border-krylo-blue focus:outline-none">
                <option value="">-- Choose Item From Market Database --</option>
              </select>
            </div>
            <div class="flex items-end">
              <button onclick="runPriceCheck()" class="w-full pixel-btn bg-krylo-blue hover:bg-blue-600 text-white font-pixel text-xs p-3 rounded">
                <i class="fa-solid fa-magnifying-glass-chart mr-2"></i> INSPECT VALUE
              </button>
            </div>
          </div>

          <div id="checker-results" class="bg-krylo-black border border-krylo-border rounded-lg p-6 space-y-6">
            <div class="text-center py-10 text-slate-500 font-pixel text-xs">
              Select an item above to display real-time valuation and SQL database verification metrics.
            </div>
          </div>
        </div>
      </section>

      <!-- VIEW 3: TERMS AND CONDITIONS -->
      <section id="view-terms" class="hidden space-y-6">
        <div class="bg-krylo-dark border border-krylo-blue/40 rounded-xl p-6 md:p-10 space-y-6 max-w-4xl mx-auto">
          <div class="border-b border-krylo-border pb-4">
            <span class="font-pixel text-xs text-krylo-blue uppercase tracking-widest block mb-1">LEGAL POLICIES</span>
            <h2 class="font-pixel text-xl text-white">TERMS AND CONDITIONS</h2>
            <p class="text-xs text-slate-400 mt-1">Operated by Krylo Digital LLC • Effective October 2026</p>
          </div>

          <div class="space-y-6 text-sm text-slate-300 leading-relaxed font-sans">
            <div>
              <h3 class="font-pixel text-xs text-krylo-bright mb-2">1. OVERVIEW & ACCEPTANCE</h3>
              <p>Welcome to <strong>Gaming Krylo</strong> (owned and operated by <strong>Krylo Digital LLC</strong>). By accessing our store, placing an order, or utilizing our PHP/SQL database verification tools, you agree to be bound by these Terms and Conditions.</p>
            </div>
            <div>
              <h3 class="font-pixel text-xs text-krylo-bright mb-2">2. ROBLOX VIRTUAL GOODS & DELIVERY</h3>
              <p>All virtual items (Blox Fruits, MM2 weapons, Pet Simulator 99 pets, Adopt Me pets, Robux Vouchers) are delivered via verified in-game trading methods. Users must provide an accurate Roblox Username at checkout.</p>
            </div>
            <div>
              <h3 class="font-pixel text-xs text-krylo-bright mb-2">3. ANTI-DUPLICATION & VERIFICATION GUARANTEE</h3>
              <p>Krylo Digital LLC inspects all database inventory listings prior to store cataloging. Duplicated items are strictly barred.</p>
            </div>
            <div>
              <h3 class="font-pixel text-xs text-krylo-bright mb-2">4. REFUND POLICY</h3>
              <p>Due to the irreversible nature of Roblox virtual trades, all sales are final once virtual items are delivered to your Roblox account.</p>
            </div>
          </div>
        </div>
      </section>

      <!-- VIEW 4: PROPRIETARY ADMIN DASHBOARD -->
      <section id="view-admin" class="hidden space-y-6">

        <div id="admin-login-guard" class="bg-krylo-dark border-2 border-amber-500/50 rounded-xl p-8 max-w-md mx-auto text-center space-y-4">
          <div class="w-14 h-14 bg-amber-500/10 border border-amber-500/40 rounded-full flex items-center justify-center mx-auto text-amber-400 text-2xl">
            <i class="fa-solid fa-lock"></i>
          </div>
          <h2 class="font-pixel text-base text-white">PROPRIETARY ADMIN ACCESS</h2>
          <p class="text-xs text-slate-400">
            Authorized Krylo Digital LLC Administrators only. Enter proprietary security key to manage SQL listings.
          </p>
          <div class="space-y-3 pt-2">
            <input type="password" id="admin-passcode-input" placeholder="Enter Proprietary Admin Key..." class="w-full bg-krylo-black border border-krylo-border rounded p-3 text-center text-xs font-pixel text-slate-100 focus:border-amber-400 focus:outline-none">
            <button onclick="verifyAdminKey()" class="w-full pixel-btn bg-amber-600 hover:bg-amber-500 text-white font-pixel text-xs p-3 rounded">
              VERIFY CREDENTIAL
            </button>
          </div>
          <p class="text-[10px] text-slate-500 font-pixelAlt">
            Demo Security Key: <span class="text-amber-400 font-pixel">KRYLO2026</span>
          </p>
        </div>

        <div id="admin-dashboard-panel" class="hidden space-y-8">
          <div class="bg-krylo-card border border-amber-500/40 rounded-xl p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
              <span class="bg-amber-500/10 text-amber-400 text-[10px] font-pixel px-2.5 py-1 rounded border border-amber-500/30">
                PROPRIETARY SESSION ACTIVE
              </span>
              <h2 class="font-pixel text-lg text-white mt-2">LISTING MANAGEMENT HUB</h2>
              <p class="text-xs text-slate-400">Krylo Digital LLC SQL Database Control</p>
            </div>
            <div class="flex items-center gap-3">
              <button onclick="openNewListingModal()" class="pixel-btn bg-emerald-600 hover:bg-emerald-500 text-white font-pixel text-xs px-4 py-2.5 rounded">
                <i class="fa-solid fa-plus mr-1.5"></i> LIST NEW ITEM
              </button>
            </div>
          </div>

          <div class="bg-krylo-dark border border-krylo-border rounded-xl overflow-hidden">
            <div class="p-4 border-b border-krylo-border flex items-center justify-between">
              <h3 class="font-pixel text-xs text-slate-200">ACTIVE STORE INVENTORY (<span id="admin-item-count">0</span>)</h3>
            </div>
            <div class="overflow-x-auto">
              <table class="w-full text-left text-xs">
                <thead class="bg-krylo-black text-slate-400 font-pixel text-[10px] uppercase border-b border-krylo-border">
                  <tr>
                    <th class="p-4">Item</th>
                    <th class="p-4">Category</th>
                    <th class="p-4">Price ($)</th>
                    <th class="p-4">Rarity</th>
                    <th class="p-4">Stock</th>
                    <th class="p-4 text-right">Actions</th>
                  </tr>
                </thead>
                <tbody id="admin-product-tbody" class="divide-y divide-krylo-border"></tbody>
              </table>
            </div>
          </div>
        </div>
      </section>

    </main>

    <!-- FOOTER -->
    <footer class="mt-auto bg-krylo-dark border-t border-krylo-border py-8 px-4 sm:px-6 lg:px-8">
      <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-6 text-center md:text-left">
        <div class="space-y-2">
          <div class="flex items-center justify-center md:justify-start gap-3">
            <img src="logo.png" alt="Logo" class="w-7 h-7 object-contain" onerror="this.onerror=null; this.src='https://placehold.co/50x50/0a0e1a/3b82f6?text=K';">
            <span class="font-pixel text-sm text-white">GAMING KRYLO</span>
          </div>
          <p class="text-xs text-slate-400 max-w-md">
            The premier marketplace for Roblox goods. Owned and operated by <strong>Krylo Digital LLC</strong>.
          </p>
        </div>

        <div class="flex flex-col sm:flex-row items-center gap-4 text-xs font-pixel text-slate-400">
          <button onclick="router.navigate('shop')" class="hover:text-krylo-bright">Store</button>
          <button onclick="router.navigate('checker')" class="hover:text-krylo-bright">Price Checker</button>
          <button onclick="router.navigate('terms')" class="hover:text-krylo-bright">Terms & ToS</button>
          <button onclick="router.navigate('admin')" class="hover:text-amber-400">Admin</button>
        </div>

        <div class="text-xs text-slate-500 font-pixelAlt">
          © 2026 Krylo Digital LLC. All rights reserved.
        </div>
      </div>
    </footer>
  </div>

  <!-- MODALS -->
  <div id="modal-product-detail" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-krylo-dark border-2 border-krylo-blue rounded-xl max-w-lg w-full p-6 space-y-5 relative text-slate-100">
      <button onclick="closeProductModal()" class="absolute top-4 right-4 text-slate-400 hover:text-white text-lg"><i class="fa-solid fa-xmark"></i></button>
      <div id="modal-product-content"></div>
    </div>
  </div>

  <div id="modal-cart" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-krylo-dark border-2 border-krylo-blue rounded-xl max-w-md w-full p-6 space-y-5 relative">
      <button onclick="toggleCartModal(false)" class="absolute top-4 right-4 text-slate-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
      <h3 class="font-pixel text-sm text-white flex items-center gap-2 border-b border-krylo-border pb-3">
        <i class="fa-solid fa-cart-shopping text-krylo-blue"></i> YOUR SHOPPING CART
      </h3>
      <div id="cart-items-list" class="max-h-60 overflow-y-auto space-y-3 pr-1 divide-y divide-krylo-border"></div>
      <div class="border-t border-krylo-border pt-3 space-y-3">
        <div class="flex justify-between font-pixel text-xs">
          <span class="text-slate-400">TOTAL AMOUNT:</span>
          <span id="cart-total-price" class="text-emerald-400">$0.00</span>
        </div>
        <div>
          <label class="block font-pixel text-[10px] text-slate-300 mb-1">ROBLOX USERNAME (Required):</label>
          <input type="text" id="cart-roblox-username" placeholder="e.g. KryloGamer_99" class="w-full bg-krylo-black border border-krylo-border rounded text-xs p-2.5 text-white focus:border-krylo-blue focus:outline-none">
        </div>
        <button onclick="processCheckout()" class="w-full pixel-btn bg-emerald-600 hover:bg-emerald-500 text-white font-pixel text-xs p-3 rounded">
          <i class="fa-solid fa-lock mr-1.5"></i> CONFIRM & RECEIVE ITEM
        </button>
      </div>
    </div>
  </div>

  <div id="modal-admin-item" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-krylo-dark border-2 border-amber-500/70 rounded-xl max-w-lg w-full p-6 space-y-4 relative">
      <button onclick="closeAdminModal()" class="absolute top-4 right-4 text-slate-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
      <h3 id="admin-modal-title" class="font-pixel text-sm text-amber-400 border-b border-krylo-border pb-3">CREATE NEW STORE LISTING</h3>
      <form id="admin-item-form" onsubmit="handleAdminItemSubmit(event)" class="space-y-3 text-xs">
        <input type="hidden" id="admin-form-id">
        <div>
          <label class="block font-pixel text-[10px] text-slate-300 mb-1">ITEM TITLE</label>
          <input type="text" id="admin-form-title" required placeholder="e.g. Kitsune Fruit" class="w-full bg-krylo-black border border-krylo-border rounded p-2 text-white focus:border-amber-400 focus:outline-none">
        </div>
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block font-pixel text-[10px] text-slate-300 mb-1">CATEGORY</label>
            <select id="admin-form-category" class="w-full bg-krylo-black border border-krylo-border rounded p-2 text-white focus:border-amber-400 focus:outline-none">
              <option value="Blox Fruits">Blox Fruits</option>
              <option value="MM2">Murder Mystery 2</option>
              <option value="Pet Sim 99">Pet Sim 99</option>
              <option value="Adopt Me">Adopt Me</option>
              <option value="Robux Vouchers">Robux Vouchers</option>
            </select>
          </div>
          <div>
            <label class="block font-pixel text-[10px] text-slate-300 mb-1">PRICE ($ USD)</label>
            <input type="number" step="0.01" id="admin-form-price" required placeholder="14.99" class="w-full bg-krylo-black border border-krylo-border rounded p-2 text-white focus:border-amber-400 focus:outline-none">
          </div>
        </div>
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block font-pixel text-[10px] text-slate-300 mb-1">RARITY</label>
            <select id="admin-form-rarity" class="w-full bg-krylo-black border border-krylo-border rounded p-2 text-white focus:border-amber-400 focus:outline-none">
              <option value="Mythical">Mythical</option>
              <option value="Godly">Godly</option>
              <option value="Legendary">Legendary</option>
              <option value="Exclusive">Exclusive</option>
            </select>
          </div>
          <div>
            <label class="block font-pixel text-[10px] text-slate-300 mb-1">STOCK</label>
            <input type="number" id="admin-form-stock" required placeholder="10" class="w-full bg-krylo-black border border-krylo-border rounded p-2 text-white focus:border-amber-400 focus:outline-none">
          </div>
        </div>
        <div>
          <label class="block font-pixel text-[10px] text-slate-300 mb-1">IMAGE URL</label>
          <input type="text" id="admin-form-image" placeholder="https://..." class="w-full bg-krylo-black border border-krylo-border rounded p-2 text-white focus:border-amber-400 focus:outline-none">
        </div>
        <div>
          <label class="block font-pixel text-[10px] text-slate-300 mb-1">DESCRIPTION</label>
          <textarea id="admin-form-desc" rows="3" placeholder="Item trade parameters..." class="w-full bg-krylo-black border border-krylo-border rounded p-2 text-white focus:border-amber-400 focus:outline-none"></textarea>
        </div>
        <button type="submit" class="w-full pixel-btn bg-amber-600 hover:bg-amber-500 text-white font-pixel text-xs p-3 rounded mt-2">SAVE LISTING TO SQL DATABASE</button>
      </form>
    </div>
  </div>

  <div id="modal-auth" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-krylo-dark border-2 border-krylo-blue rounded-xl max-w-sm w-full p-6 space-y-4 relative">
      <button onclick="closeAuthModal()" class="absolute top-4 right-4 text-slate-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
      <h3 class="font-pixel text-sm text-white flex items-center gap-2 border-b border-krylo-border pb-3">
        <i class="fa-solid fa-user-gear text-krylo-blue"></i> USER LOGIN
      </h3>
      <div class="space-y-3">
        <div>
          <label class="block font-pixel text-[10px] text-slate-300 mb-1">USERNAME OR EMAIL</label>
          <input type="text" id="auth-username-input" placeholder="GamerTag123" class="w-full bg-krylo-black border border-krylo-border rounded text-xs p-2.5 text-white focus:border-krylo-blue focus:outline-none">
        </div>
        <div>
          <label class="block font-pixel text-[10px] text-slate-300 mb-1">PASSWORD</label>
          <input type="password" id="auth-password-input" placeholder="••••••••" class="w-full bg-krylo-black border border-krylo-border rounded text-xs p-2.5 text-white focus:border-krylo-blue focus:outline-none">
        </div>
        <button onclick="loginUser()" class="w-full pixel-btn bg-krylo-blue hover:bg-blue-600 text-white font-pixel text-xs p-2.5 rounded">SIGN IN / REGISTER</button>
      </div>
    </div>
  </div>

  <div id="toast-container" class="fixed bottom-5 right-5 z-50 flex flex-col gap-2 pointer-events-none"></div>

  <!-- Gaming Krylo SPA application -->
  <script src="public/assets/js/app.js"></script>
</body>
</html>
