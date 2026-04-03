<template>
    <AdminLayout :auth="auth" title="Címkék">
        <div class="admin-card">
            <div class="table-top">
                <button class="btn-primary" @click="openCreate">+ Új label</button>
            </div>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Név</th>
                        <th>Típus</th>
                        <th>Termékek</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="label in labels" :key="label.id">
                        <td class="text-muted">{{ label.id }}</td>
                        <td class="fw-semibold">{{ label.name }}</td>
                        <td>
                            <span class="badge" :class="label.type === 'primary' ? 'badge-admin' : 'badge-user'">
                                {{ label.type === 'primary' ? 'Elsődleges' : 'Másodlagos' }}
                            </span>
                        </td>
                        <td>{{ label.products_count }}</td>
                        <td class="d-flex gap-2">
                            <button class="btn-edit"   @click="openEdit(label)">Szerkesztés</button>
                            <button class="btn-delete" @click="destroy(label.id)">Törlés</button>
                        </td>
                    </tr>
                    <tr v-if="labels.length === 0">
                        <td colspan="5" class="text-center text-muted py-4">Nincsenek labelek.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="modal-overlay" v-if="modal" @click.self="modal = false">
            <div class="modal-box">
                <p class="modal-title">{{ editing ? 'Label szerkesztése' : 'Új label' }}</p>
                <div class="mb-3">
                    <label class="form-label">Név</label>
                    <input v-model="form.name" class="form-control" placeholder="pl. Vegetáriánus">
                    <span class="text-muted small" v-if="errors.name">{{ errors.name }}</span>
                </div>
                <div class="mb-4">
                    <label class="form-label">Típus</label>
                    <select v-model="form.type" class="form-select">
                        <option value="primary">Elsődleges (badge a képen)</option>
                        <option value="secondary">Másodlagos (tag a kártyán)</option>
                    </select>
                </div>
                <div class="d-flex gap-2 justify-end">
                    <button class="btn-secondary" @click="modal = false">Mégse</button>
                    <button class="btn-primary" :disabled="saving" @click="save">
                        {{ saving ? 'Mentés...' : 'Mentés' }}
                    </button>
                </div>
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
    labels: { type: Array,  default: () => [] },
})

const modal   = ref(false)
const editing = ref(null)
const saving  = ref(false)
const errors  = ref({})
const form    = ref({ name: '', type: 'secondary' })

const openCreate = () => { editing.value = null; form.value = { name: '', type: 'secondary' }; errors.value = {}; modal.value = true }
const openEdit   = (l) => { editing.value = l.id; form.value = { name: l.name, type: l.type }; errors.value = {}; modal.value = true }

const save = () => {
    saving.value = true
    const url    = editing.value ? `/admin/labels/${editing.value}` : '/admin/labels'
    const method = editing.value ? 'patch' : 'post'
    router[method](url, form.value, {
        preserveScroll: true,
        onSuccess: () => { modal.value = false; saving.value = false },
        onError:   (e) => { errors.value = e; saving.value = false },
    })
}

const destroy = (id) => {
    if (!confirm('Biztosan törlöd ezt a labelt?')) return
    router.delete(`/admin/labels/${id}`, { preserveScroll: true })
}
</script>

<style scoped>
@import './admin-table.css';
</style>
