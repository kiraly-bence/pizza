<template>
    <div class="admin-wrapper">
        <aside class="sidebar">
            <div class="sidebar-brand">
                <span class="brand-icon">🍕</span>
                <span class="brand-text">Csepel Pizza Admin</span>
            </div>

            <nav class="sidebar-nav">
                <p class="nav-section-label">Főmenü</p>
                <a href="/admin" class="nav-item" :class="{ active: isActive('/admin') }">
                    <span class="nav-icon">▪</span> Dashboard
                </a>

                <p class="nav-section-label">Rendelések</p>
                <a href="/admin/orders" class="nav-item" :class="{ active: isActive('/admin/orders') }">
                    <span class="nav-icon">▪</span> Rendelések
                </a>

                <p class="nav-section-label">Menü kezelés</p>
                <a href="/admin/categories" class="nav-item" :class="{ active: isActive('/admin/categories') }">
                    <span class="nav-icon">▪</span> Kategóriák
                </a>
                <a href="/admin/products" class="nav-item" :class="{ active: isActive('/admin/products') }">
                    <span class="nav-icon">▪</span> Termékek
                </a>
                <a href="/admin/ingredients" class="nav-item" :class="{ active: isActive('/admin/ingredients') }">
                    <span class="nav-icon">▪</span> Hozzávalók
                </a>
                <a href="/admin/labels" class="nav-item" :class="{ active: isActive('/admin/labels') }">
                    <span class="nav-icon">▪</span> Címkék
                </a>
                <a href="/admin/coupons" class="nav-item" :class="{ active: isActive('/admin/coupons') }">
                    <span class="nav-icon">▪</span> Kuponok
                </a>

                <p class="nav-section-label">Rendszer</p>
                <a href="/admin/settings" class="nav-item" :class="{ active: isActive('/admin/settings') }">
                    <span class="nav-icon">▪</span> Beállítások
                </a>

                <p class="nav-section-label">Felhasználók</p>
                <a href="/admin/users" class="nav-item" :class="{ active: isActive('/admin/users') }">
                    <span class="nav-icon">▪</span> Felhasználók
                </a>
            </nav>

            <div class="sidebar-footer">
                <div class="admin-user-info">
                    <div class="admin-avatar">{{ initials }}</div>
                    <div>
                        <p class="admin-name">{{ auth.user.name }}</p>
                        <p class="admin-role">Adminisztrátor</p>
                    </div>
                </div>
                <form method="POST" :action="route('logout')">
                    <input type="hidden" name="_token" :value="csrf">
                    <button type="submit" class="logout-btn">Kilépés</button>
                </form>
            </div>
        </aside>

        <div class="admin-main">
            <header class="admin-topbar">
                <h1 class="page-title">{{ title }}</h1>
            </header>
            <main class="admin-content">
                <slot />
            </main>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

const props = defineProps({
    auth:  { type: Object, required: true },
    title: { type: String, default: 'Dashboard' },
})

const page = usePage()

const csrf = computed(() =>
    document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? ''
)

const initials = computed(() =>
    props.auth.user.name
        .split(' ')
        .map(n => n[0])
        .join('')
        .toUpperCase()
        .slice(0, 2)
)

const isActive = (path) => window.location.pathname === path

const route = (name) => {
    const routes = { logout: '/logout' }
    return routes[name] ?? '/'
}
</script>

<style scoped>
.admin-wrapper {
    display: flex;
    min-height: 100vh;
    background: #f4f5f7;
}

.sidebar {
    width: 240px;
    min-height: 100vh;
    background: #1a1a1a;
    display: flex;
    flex-direction: column;
    flex-shrink: 0;
    position: fixed;
    top: 0;
    left: 0;
    bottom: 0;
    z-index: 100;
}

.sidebar-brand {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 1.25rem 1.25rem 1rem;
    border-bottom: 1px solid rgba(255,255,255,0.08);
}

.brand-icon { font-size: 1.4rem; }

.brand-text {
    font-family: 'Georgia', serif;
    font-size: 1rem;
    font-weight: 700;
    color: #fff;
}

.sidebar-nav {
    flex: 1;
    padding: 1rem 0.75rem;
    overflow-y: auto;
}

.nav-section-label {
    font-size: 0.68rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: rgba(255,255,255,0.3);
    margin: 1rem 0.5rem 0.4rem;
}

.nav-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 0.5rem 0.75rem;
    border-radius: 7px;
    color: rgba(255,255,255,0.65);
    font-size: 0.9rem;
    text-decoration: none;
    transition: all 0.15s;
    margin-bottom: 2px;
}

.nav-item:hover {
    background: rgba(255,255,255,0.07);
    color: #fff;
}

.nav-item.active {
    background: #e63946;
    color: #fff;
}

.nav-icon { font-size: 0.5rem; opacity: 0.6; }

.sidebar-footer {
    padding: 1rem;
    border-top: 1px solid rgba(255,255,255,0.08);
}

.admin-user-info {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 0.75rem;
}

.admin-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #e63946;
    color: #fff;
    font-size: 0.72rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.admin-name {
    font-size: 0.85rem;
    font-weight: 600;
    color: #fff;
    margin: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.admin-role {
    font-size: 0.72rem;
    color: rgba(255,255,255,0.4);
    margin: 0;
}

.logout-btn {
    width: 100%;
    background: rgba(255,255,255,0.07);
    border: 1px solid rgba(255,255,255,0.12);
    color: rgba(255,255,255,0.7);
    border-radius: 7px;
    padding: 0.4rem 0.75rem;
    font-size: 0.85rem;
    cursor: pointer;
    transition: all 0.15s;
    text-align: left;
}

.logout-btn:hover {
    background: rgba(230, 57, 70, 0.3);
    color: #fff;
    border-color: #e63946;
}

.admin-main {
    margin-left: 240px;
    flex: 1;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
}

.admin-topbar {
    background: #fff;
    border-bottom: 1px solid #e5e7eb;
    padding: 1rem 2rem;
    display: flex;
    align-items: center;
}

.page-title {
    font-family: 'Georgia', serif;
    font-size: 1.3rem;
    font-weight: 700;
    color: #1a1a1a;
    margin: 0;
}

.admin-content {
    padding: 2rem;
    flex: 1;
}
</style>
