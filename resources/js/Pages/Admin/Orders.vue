<template>
    <AdminLayout :auth="auth" title="Rendelések">
        <div class="admin-card">
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Vásárló</th>
                            <th>Tételek</th>
                            <th>Összeg</th>
                            <th>Státusz</th>
                            <th>Dátum</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template v-for="order in orders" :key="order.id">
                            <tr :class="{ 'row-expanded': expanded === order.id }">
                                <td class="text-muted">#{{ order.id }}</td>
                                <td>
                                    <div class="fw-semibold">{{ order.user.name }}</div>
                                    <div class="text-muted small">{{ order.user.email }}</div>
                                </td>
                                <td>{{ order.items_count }} db</td>
                                <td class="fw-semibold">{{ formatPrice(order.total) }} Ft</td>
                                <td>
                                    <select
                                        class="status-select"
                                        :class="statusClass(order.status)"
                                        :value="order.status"
                                        @change="updateStatus(order.id, $event.target.value)"
                                    >
                                        <option value="pending">Függőben</option>
                                        <option value="confirmed">Visszaigazolva</option>
                                        <option value="preparing">Készül</option>
                                        <option value="delivering">Úton van</option>
                                        <option value="delivered">Kiszállítva</option>
                                        <option value="cancelled">Lemondva</option>
                                    </select>
                                </td>
                                <td class="text-muted small">{{ order.created_at }}</td>
                                <td>
                                    <button class="btn-icon" @click="expanded = expanded === order.id ? null : order.id">
                                        {{ expanded === order.id ? '▲' : '▼' }}
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="expanded === order.id" class="detail-row">
                                <td colspan="7">
                                    <div class="order-detail">
                                        <div class="order-detail-items">
                                            <div v-for="(item, i) in order.items" :key="i" class="detail-line">
                                                <span>{{ item.name }} × {{ item.quantity }}</span>
                                                <span>{{ formatPrice(item.price * item.quantity) }} Ft</span>
                                            </div>
                                            <div class="detail-line detail-fees">
                                                <span>Kiszállítás</span>
                                                <span>{{ formatPrice(order.delivery_fee) }} Ft</span>
                                            </div>
                                            <div class="detail-line detail-fees">
                                                <span>Szolgáltatási díj</span>
                                                <span>{{ formatPrice(order.service_fee) }} Ft</span>
                                            </div>
                                            <div class="detail-line detail-total">
                                                <span>Összesen</span>
                                                <span>{{ formatPrice(order.total) }} Ft</span>
                                            </div>
                                        </div>
                                        <div class="order-detail-meta">
                                            <div><strong>Cím:</strong> {{ order.address }}</div>
                                            <div><strong>Fizetés:</strong> {{ order.payment_method === 'card' ? 'Bankkártya' : 'Készpénz' }}</div>
                                            <div v-if="order.delivery_message"><strong>Üzenet:</strong> {{ order.delivery_message }}</div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <tr v-if="orders.length === 0">
                            <td colspan="7" class="text-center text-muted py-4">Nincsenek rendelések.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineProps({
    auth:   { type: Object, required: true },
    orders: { type: Array,  default: () => [] },
})

const expanded = ref(null)

const formatPrice = (v) => Number(v).toLocaleString('hu-HU')

const updateStatus = (id, status) => {
    router.patch(`/admin/orders/${id}/status`, { status }, { preserveScroll: true })
}

const statusClass = (s) => ({
    pending:    'status-pending',
    confirmed:  'status-confirmed',
    preparing:  'status-preparing',
    delivering: 'status-delivering',
    delivered:  'status-delivered',
    cancelled:  'status-cancelled',
}[s] ?? '')
</script>

<style scoped>
@import './admin-table.css';

.status-select {
    border: none;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 700;
    padding: 3px 10px;
    cursor: pointer;
    appearance: none;
    text-align: center;
}

.status-pending    { background: #fff3cd; color: #856404; }
.status-confirmed  { background: #cfe2ff; color: #084298; }
.status-preparing  { background: #fff3cd; color: #856404; }
.status-delivering { background: #d1ecf1; color: #0c5460; }
.status-delivered  { background: #d1e7dd; color: #0a3622; }
.status-cancelled  { background: #f8d7da; color: #842029; }

.row-expanded td { background: #fafafa; }

.detail-row td { padding: 0; }

.order-detail {
    display: flex;
    gap: 2rem;
    padding: 1rem 1.25rem;
    background: #f9fafb;
    border-top: 1px solid #e5e7eb;
}

.order-detail-items {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 0.35rem;
}

.detail-line {
    display: flex;
    justify-content: space-between;
    font-size: 0.85rem;
    color: #555;
}

.detail-fees { color: #888; }

.detail-total {
    font-weight: 700;
    color: #1a1a1a;
    padding-top: 0.35rem;
    border-top: 1px solid #ddd;
    margin-top: 0.2rem;
}

.order-detail-meta {
    min-width: 240px;
    display: flex;
    flex-direction: column;
    gap: 0.4rem;
    font-size: 0.85rem;
    color: #555;
}
</style>
