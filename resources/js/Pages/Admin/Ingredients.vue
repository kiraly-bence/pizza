<template>
    <AdminLayout :auth="auth" title="Hozzávalók">
        <div class="admin-card">
            <div class="table-top">
                <button class="btn-primary" @click="openCreate">+ Új hozzávaló</button>
            </div>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Név</th>
                        <th>Termékek</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="ing in ingredients" :key="ing.id">
                        <td class="text-muted">{{ ing.id }}</td>
                        <td class="fw-semibold">{{ ing.name }}</td>
                        <td>{{ ing.products_count }}</td>
                        <td class="d-flex gap-2">
                            <button class="btn-edit"   @click="openEdit(ing)">Szerkesztés</button>
                            <button class="btn-delete" @click="destroy(ing.id)">Törlés</button>
                        </td>
                    </tr>
                    <tr v-if="ingredients.length === 0">
                        <td colspan="4" class="text-center text-muted py-4">Nincsenek hozzávalók.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="modal-overlay" v-if="modal" @click.self="modal = false">
            <div class="modal-box">
                <p class="modal-title">{{ editing ? 'Hozzávaló szerkesztése' : 'Új hozzávaló' }}</p>
                <div class="mb-4">
                    <label class="form-label">Név</label>
                    <input v-model="form.name" class="form-control" placeholder="pl. Mozzarella">
                    <span class="text-muted small" v-if="errors.name">{{ errors.name }}</span>
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
    auth:        { type: Object, required: true },
    ingredients: { type: Array,  default: () => [] },
})

const modal   = ref(false)
const editing = ref(null)
const saving  = ref(false)
const errors  = ref({})
const form    = ref({ name: '' })

const openCreate = () => { editing.value = null; form.value = { name: '' }; errors.value = {}; modal.value = true }
const openEdit   = (i) => { editing.value = i.id; form.value = { name: i.name }; errors.value = {}; modal.value = true }

const save = () => {
    saving.value = true
    const url    = editing.value ? `/admin/ingredients/${editing.value}` : '/admin/ingredients'
    const method = editing.value ? 'patch' : 'post'
    router[method](url, form.value, {
        preserveScroll: true,
        onSuccess: () => { modal.value = false; saving.value = false },
        onError:   (e) => { errors.value = e; saving.value = false },
    })
}

const destroy = (id) => {
    if (!confirm('Biztosan törlöd ezt a hozzávalót?')) return
    router.delete(`/admin/ingredients/${id}`, { preserveScroll: true })
}
</script>

<style scoped>
@import './admin-table.css';
</style>
