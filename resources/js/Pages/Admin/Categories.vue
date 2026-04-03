<template>
    <AdminLayout :auth="auth" title="Kategóriák">
        <div class="admin-card">
            <div class="table-top">
                <button class="btn-primary" @click="openCreate">+ Új kategória</button>
            </div>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Név</th>
                        <th>Sorrend</th>
                        <th>Termékek</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="cat in categories" :key="cat.id">
                        <td class="text-muted">{{ cat.id }}</td>
                        <td class="fw-semibold">{{ cat.name }}</td>
                        <td>{{ cat.sort_order }}</td>
                        <td>{{ cat.products_count }}</td>
                        <td class="d-flex gap-2">
                            <button class="btn-edit"   @click="openEdit(cat)">Szerkesztés</button>
                            <button class="btn-delete" @click="destroy(cat.id)">Törlés</button>
                        </td>
                    </tr>
                    <tr v-if="categories.length === 0">
                        <td colspan="5" class="text-center text-muted py-4">Nincsenek kategóriák.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Modal -->
        <div class="modal-overlay" v-if="modal" @click.self="modal = false">
            <div class="modal-box">
                <p class="modal-title">{{ editing ? 'Kategória szerkesztése' : 'Új kategória' }}</p>
                <div class="mb-3">
                    <label class="form-label">Név</label>
                    <input v-model="form.name" class="form-control" placeholder="pl. Pizzák">
                    <span class="text-muted small" v-if="errors.name">{{ errors.name }}</span>
                </div>
                <div class="mb-4">
                    <label class="form-label">Sorrend</label>
                    <input v-model.number="form.sort_order" type="number" class="form-control" min="0">
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
    auth:       { type: Object, required: true },
    categories: { type: Array,  default: () => [] },
})

const modal   = ref(false)
const editing = ref(null)
const saving  = ref(false)
const errors  = ref({})
const form    = ref({ name: '', sort_order: 0 })

const openCreate = () => {
    editing.value = null
    form.value = { name: '', sort_order: 0 }
    errors.value = {}
    modal.value = true
}

const openEdit = (cat) => {
    editing.value = cat.id
    form.value = { name: cat.name, sort_order: cat.sort_order }
    errors.value = {}
    modal.value = true
}

const save = () => {
    saving.value = true
    const url = editing.value ? `/admin/categories/${editing.value}` : '/admin/categories'
    const method = editing.value ? 'patch' : 'post'
    router[method](url, form.value, {
        preserveScroll: true,
        onSuccess: () => { modal.value = false; saving.value = false },
        onError: (e) => { errors.value = e; saving.value = false },
    })
}

const destroy = (id) => {
    if (!confirm('Biztosan törlöd ezt a kategóriát?')) return
    router.delete(`/admin/categories/${id}`, { preserveScroll: true })
}
</script>

<style scoped>
@import './admin-table.css';
</style>
