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
                            <th>Rendelések</th>
                            <th>Regisztráció</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in users" :key="user.id">
                            <td class="text-muted">{{ user.id }}</td>
                            <td class="fw-semibold">{{ user.name }}</td>
                            <td class="text-muted">{{ user.email }}</td>
                            <td>
                                <span class="badge" :class="user.role === 'admin' ? 'badge-admin' : 'badge-user'">
                                    {{ user.role === 'admin' ? 'Admin' : 'Felhasználó' }}
                                </span>
                            </td>
                            <td>{{ user.orders_count }}</td>
                            <td class="text-muted small">{{ user.created_at }}</td>
                            <td>
                                <button
                                    v-if="user.role !== 'admin'"
                                    class="btn-edit"
                                    @click="setRole(user.id, 'admin')"
                                >Admin legyen</button>
                                <button
                                    v-else-if="user.id !== auth.user.id"
                                    class="btn-delete"
                                    @click="setRole(user.id, 'user')"
                                >Admin elvétel</button>
                                <span v-else class="text-muted small">Te vagy</span>
                            </td>
                        </tr>
                        <tr v-if="users.length === 0">
                            <td colspan="7" class="text-center text-muted py-4">Nincsenek felhasználók.</td>
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
</script>

<style scoped>
@import './admin-table.css';
</style>
