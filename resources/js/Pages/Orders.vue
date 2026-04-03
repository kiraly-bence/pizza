<template>
    <AppLayout :auth="auth">
        <section class="orders-section">
            <div class="container">
                <h1 class="orders-heading">Rendeléseim</h1>

                <div v-if="orders.length === 0" class="orders-empty">
                    <div class="orders-empty-icon">🍽️</div>
                    <p class="orders-empty-text">Még nem adtál le rendelést.</p>
                    <a href="/" class="btn order-btn px-4">Rendelés indítása</a>
                </div>

                <div v-else class="orders-list">
                    <div
                        v-for="order in orders"
                        :key="order.id"
                        class="order-card"
                    >
                        <!-- Header row -->
                        <div class="order-card-header" @click="toggle(order.id)">
                            <div class="order-meta">
                                <span class="order-id">#{{ order.id }}</span>
                                <span class="order-date">{{ order.created_at }}</span>
                            </div>
                            <div class="order-header-right">
                                <span class="order-status" :class="statusClass(order.status)">
                                    {{ statusLabel(order.status) }}
                                </span>
                                <span class="order-total-badge">{{ formatPrice(order.total) }} Ft</span>
                                <span class="order-chevron" :class="{ open: expanded.includes(order.id) }">›</span>
                            </div>
                        </div>

                        <!-- Expanded detail -->
                        <div v-if="expanded.includes(order.id)" class="order-card-body">

                            <div class="order-items-list">
                                <div
                                    v-for="(item, i) in order.items"
                                    :key="i"
                                    class="order-line"
                                >
                                    <span class="order-line-name">{{ item.name }}</span>
                                    <span class="order-line-qty">× {{ item.quantity }}</span>
                                    <span class="order-line-price">{{ formatPrice(item.price * item.quantity) }} Ft</span>
                                </div>
                            </div>

                            <div class="order-summary">
                                <div class="order-summary-row">
                                    <span>Részösszeg</span>
                                    <span>{{ formatPrice(order.subtotal) }} Ft</span>
                                </div>
                                <div class="order-summary-row">
                                    <span>Kiszállítás</span>
                                    <span>{{ formatPrice(order.delivery_fee) }} Ft</span>
                                </div>
                                <div class="order-summary-row">
                                    <span>Szolgáltatási díj</span>
                                    <span>{{ formatPrice(order.service_fee) }} Ft</span>
                                </div>
                                <div class="order-summary-row order-summary-total">
                                    <span>Összesen</span>
                                    <span>{{ formatPrice(order.total) }} Ft</span>
                                </div>
                            </div>

                            <div class="order-details">
                                <div class="order-detail-item">
                                    <span class="order-detail-label">Szállítási cím</span>
                                    <span>{{ order.address }}</span>
                                </div>
                                <div class="order-detail-item">
                                    <span class="order-detail-label">Fizetési mód</span>
                                    <span>{{ order.payment_method === 'card' ? 'Bankkártya' : 'Készpénz' }}</span>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </section>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'

defineOptions({ layout: null })

defineProps({
    auth:   { type: Object, default: () => ({ user: null }) },
    orders: { type: Array,  default: () => [] },
})

const expanded = ref([])

const toggle = (id) => {
    const idx = expanded.value.indexOf(id)
    if (idx === -1) expanded.value.push(id)
    else expanded.value.splice(idx, 1)
}

const formatPrice = (price) => Number(price).toLocaleString('hu-HU')

const statusLabel = (status) => ({
    pending:    'Függőben',
    confirmed:  'Visszaigazolva',
    preparing:  'Készül',
    delivering: 'Úton van',
    delivered:  'Kiszállítva',
    cancelled:  'Lemondva',
}[status] ?? status)

const statusClass = (status) => ({
    pending:    'status-pending',
    confirmed:  'status-confirmed',
    preparing:  'status-preparing',
    delivering: 'status-delivering',
    delivered:  'status-delivered',
    cancelled:  'status-cancelled',
}[status] ?? '')
</script>

<style scoped>
.orders-section {
    padding: 3rem 0 5rem;
    background: #f7f7f7;
    min-height: 100vh;
}

.orders-heading {
    font-family: 'Georgia', serif;
    font-size: 2rem;
    font-weight: 700;
    color: #1a1a1a;
    margin-bottom: 2rem;
}

/* Empty state */
.orders-empty {
    text-align: center;
    padding: 5rem 0;
}

.orders-empty-icon { font-size: 3.5rem; margin-bottom: 1rem; }

.orders-empty-text {
    color: #888;
    font-size: 1rem;
    margin-bottom: 1.5rem;
}

/* List */
.orders-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    max-width: 760px;
}

.order-card {
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    overflow: hidden;
}

/* Card header */
.order-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1.1rem 1.4rem;
    cursor: pointer;
    user-select: none;
    transition: background 0.15s;
}

.order-card-header:hover { background: #fafafa; }

.order-meta {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.order-id {
    font-size: 0.95rem;
    font-weight: 700;
    color: #1a1a1a;
}

.order-date {
    font-size: 0.78rem;
    color: #999;
}

.order-header-right {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.order-total-badge {
    font-size: 0.95rem;
    font-weight: 700;
    color: #e63946;
}

.order-chevron {
    font-size: 1.3rem;
    color: #aaa;
    line-height: 1;
    transform: rotate(0deg);
    transition: transform 0.2s;
    display: inline-block;
}

.order-chevron.open { transform: rotate(90deg); }

/* Status badges */
.order-status {
    font-size: 0.72rem;
    font-weight: 700;
    padding: 3px 10px;
    border-radius: 20px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.status-pending    { background: #fff3cd; color: #856404; }
.status-confirmed  { background: #cfe2ff; color: #084298; }
.status-preparing  { background: #fff3cd; color: #856404; }
.status-delivering { background: #d1ecf1; color: #0c5460; }
.status-delivered  { background: #d1e7dd; color: #0a3622; }
.status-cancelled  { background: #f8d7da; color: #842029; }

/* Card body */
.order-card-body {
    border-top: 1px solid #f0f0f0;
    padding: 1.25rem 1.4rem;
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}

/* Items */
.order-items-list {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.order-line {
    display: flex;
    align-items: baseline;
    gap: 0.5rem;
    font-size: 0.9rem;
}

.order-line-name {
    flex: 1;
    color: #333;
    font-weight: 500;
}

.order-line-qty { color: #999; white-space: nowrap; }

.order-line-price {
    font-weight: 600;
    color: #1a1a1a;
    white-space: nowrap;
}

/* Summary */
.order-summary {
    background: #fafafa;
    border-radius: 10px;
    padding: 0.9rem 1rem;
    display: flex;
    flex-direction: column;
    gap: 0.4rem;
}

.order-summary-row {
    display: flex;
    justify-content: space-between;
    font-size: 0.85rem;
    color: #666;
}

.order-summary-total {
    font-weight: 700;
    font-size: 0.95rem;
    color: #1a1a1a;
    padding-top: 0.4rem;
    border-top: 1px solid #eee;
    margin-top: 0.2rem;
}

/* Details */
.order-details {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.order-detail-item {
    display: flex;
    gap: 0.75rem;
    font-size: 0.85rem;
    color: #555;
}

.order-detail-label {
    font-weight: 600;
    color: #333;
    min-width: 110px;
    flex-shrink: 0;
}

/* Shared button */
.order-btn {
    background: #e63946;
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 0.6rem 1.5rem;
    font-weight: 600;
    font-size: 0.9rem;
    text-decoration: none;
    display: inline-block;
    transition: background 0.2s;
}

.order-btn:hover { background: #c1121f; color: #fff; }
</style>
