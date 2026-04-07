<template>
    <AdminLayout :auth="auth" title="Felhasználók">
        <div class="admin-card">
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Név</th>
                            <th>E-mail</th>
                            <th>Szerepkör</th>
                            <th>Állapot</th>
                            <th>Rendelések</th>
                            <th>Regisztráció</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in users" :key="user.id" :class="{ 'row-banned': user.banned_at }">
                            <td class="text-muted">{{ user.id }}</td>
                            <td class="fw-semibold">{{ user.name }}</td>
                            <td class="text-muted">{{ user.email }}</td>
                            <td>
                                <select
                                    class="role-select"
                                    :class="user.role === 'admin' ? 'role-admin' : 'role-user'"
                                    :value="user.role"
                                    :disabled="user.id === auth.user.id"
                                    @change="setRole(user.id, $event.target.value)"
                                >
                                    <option value="user">Felhasználó</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </td>
                            <td>
                                <span v-if="user.banned_at" class="badge badge-banned" :title="'Letiltva: ' + user.banned_at">
                                    Letiltva
                                </span>
                                <span v-else class="badge badge-active">Aktív</span>
                            </td>
                            <td>{{ user.orders_count }}</td>
                            <td class="text-muted small">{{ user.created_at }}</td>
                            <td class="d-flex gap-2 flex-wrap">
                                <!-- Self -->
                                <span v-if="user.id === auth.user.id" class="text-muted small">Te vagy</span>

                                <template v-else>
                                    <!-- Ban / Unban (only for non-admins) -->
                                    <button
                                        v-if="user.role !== 'admin' && !user.banned_at"
                                        class="btn-ban"
                                        @click="ban(user.id)"
                                    >Letiltás</button>
                                    <button
                                        v-else-if="user.role !== 'admin' && user.banned_at"
                                        class="btn-unban"
                                        @click="unban(user.id)"
                                    >Tiltás feloldása</button>
                                </template>
                            </td>
                        </tr>
                        <tr v-if="users.length === 0">
                            <td colspan="8" class="text-center text-muted py-4">Nincsenek felhasználók.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineProps({
    auth:  { type: Object, required: true },
    users: { type: Array,  default: () => [] },
})

const setRole = (id, role) => {
    router.patch(`/admin/users/${id}/role`, { role }, { preserveScroll: true })
}

const ban = (id) => {
    if (!confirm('Biztosan letiltod ezt a felhasználót?')) return
    router.patch(`/admin/users/${id}/ban`, {}, { preserveScroll: true })
}

const unban = (id) => {
    router.patch(`/admin/users/${id}/unban`, {}, { preserveScroll: true })
}
</script>

<style scoped>
@import './admin-table.css';

.row-banned td { opacity: 0.6; }

.badge {
    display: inline-block;
    padding: 0.2rem 0.55rem;
    border-radius: 999px;
    font-size: 0.75rem;
    font-weight: 600;
}

.badge-admin  { background: #fef3c7; color: #92400e; }
.badge-user   { background: #e0e7ff; color: #3730a3; }

.role-select {
    border: none;
    border-radius: 999px;
    font-size: 0.75rem;
    font-weight: 600;
    padding: 0.2rem 0.55rem;
    cursor: pointer;
    appearance: none;
    text-align: center;
}

.role-admin { background: #fef3c7; color: #92400e; }
.role-user  { background: #e0e7ff; color: #3730a3; }
.badge-active { background: #d1fae5; color: #065f46; }
.badge-banned { background: #fee2e2; color: #991b1b; }

.btn-ban {
    padding: 0.25rem 0.6rem;
    border-radius: 6px;
    border: 1px solid #e63946;
    background: transparent;
    color: #e63946;
    font-size: 0.78rem;
    cursor: pointer;
    transition: all 0.15s;
    white-space: nowrap;
}

.btn-ban:hover { background: #e63946; color: #fff; }

.btn-unban {
    padding: 0.25rem 0.6rem;
    border-radius: 6px;
    border: 1px solid #16a34a;
    background: transparent;
    color: #16a34a;
    font-size: 0.78rem;
    cursor: pointer;
    transition: all 0.15s;
    white-space: nowrap;
}

.btn-unban:hover { background: #16a34a; color: #fff; }
</style>
