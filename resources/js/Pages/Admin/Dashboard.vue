<template>
    <AdminLayout :auth="auth" title="Dashboard">
        <div class="row g-4 mb-4">
            <div class="col-6 col-lg-3">
                <div class="stat-card">
                    <p class="stat-label">Összes rendelés</p>
                    <p class="stat-value">{{ stats.orders }}</p>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="stat-card accent">
                    <p class="stat-label">Függőben</p>
                    <p class="stat-value">{{ stats.pending }}</p>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="stat-card">
                    <p class="stat-label">Felhasználók</p>
                    <p class="stat-value">{{ stats.users }}</p>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="stat-card green">
                    <p class="stat-label">Bevétel</p>
                    <p class="stat-value">{{ formatPrice(stats.revenue) }} Ft</p>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-6 col-lg-3">
                <div class="stat-card small">
                    <p class="stat-label">Termékek</p>
                    <p class="stat-value">{{ stats.products }}</p>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="stat-card small">
                    <p class="stat-label">Kategóriák</p>
                    <p class="stat-value">{{ stats.categories }}</p>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="stat-card small">
                    <p class="stat-label">Hozzávalók</p>
                    <p class="stat-value">{{ stats.ingredients }}</p>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="stat-card small">
                    <p class="stat-label">Labelek</p>
                    <p class="stat-value">{{ stats.labels }}</p>
                </div>
            </div>
        </div>

        <div class="quick-links">
            <p class="ql-title">Gyors műveletek</p>
            <div class="row g-3">
                <div class="col-12 col-md-4">
                    <a href="/admin/orders" class="quick-card">
                        <span class="quick-icon">📋</span>
                        <span>Rendelések kezelése</span>
                    </a>
                </div>
                <div class="col-12 col-md-4">
                    <a href="/admin/products" class="quick-card">
                        <span class="quick-icon">🍕</span>
                        <span>Termékek kezelése</span>
                    </a>
                </div>
                <div class="col-12 col-md-4">
                    <a href="/admin/users" class="quick-card">
                        <span class="quick-icon">👥</span>
                        <span>Felhasználók kezelése</span>
                    </a>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineProps({
    auth:  { type: Object, required: true },
    stats: { type: Object, required: true },
})

const formatPrice = (v) => Number(v).toLocaleString('hu-HU')
</script>

<style scoped>
.stat-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 1.25rem 1.5rem;
}
.stat-card.accent { border-left: 4px solid #f59e0b; }
.stat-card.green  { border-left: 4px solid #10b981; }
.stat-card.small .stat-value { font-size: 1.6rem; }

.stat-label {
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #888;
    margin: 0 0 0.25rem;
}

.stat-value {
    font-size: 2rem;
    font-weight: 700;
    color: #1a1a1a;
    margin: 0;
}

.quick-links {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 1.5rem;
}

.ql-title {
    font-size: 0.9rem;
    font-weight: 700;
    color: #333;
    margin-bottom: 1rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.quick-card {
    display: flex;
    align-items: center;
    gap: 12px;
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    padding: 1rem 1.25rem;
    text-decoration: none;
    color: #1a1a1a;
    font-size: 0.9rem;
    font-weight: 500;
    transition: all 0.15s;
}

.quick-card:hover { border-color: #e63946; color: #e63946; background: #fff5f5; }
.quick-icon { font-size: 1.2rem; }
</style>
