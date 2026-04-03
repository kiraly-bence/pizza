<template>
    <AdminLayout :auth="auth" title="Beállítások">
        <div class="admin-card settings-card" style="max-width: 480px;">
            <p class="section-title">Díjak</p>

            <div v-if="saved" class="alert-success mb-4">Beállítások sikeresen mentve.</div>

            <div class="mb-3">
                <label class="form-label">Kiszállítási díj (Ft)</label>
                <input
                    v-model.number="form.delivery_fee"
                    type="number"
                    min="0"
                    class="form-control"
                    :class="{ 'is-invalid': errors.delivery_fee }"
                >
                <div class="invalid-feedback">{{ errors.delivery_fee }}</div>
            </div>

            <div class="mb-4">
                <label class="form-label">Szolgáltatási díj (Ft)</label>
                <input
                    v-model.number="form.service_fee"
                    type="number"
                    min="0"
                    class="form-control"
                    :class="{ 'is-invalid': errors.service_fee }"
                >
                <div class="invalid-feedback">{{ errors.service_fee }}</div>
            </div>

            <button class="btn-primary" :disabled="saving" @click="save">
                {{ saving ? 'Mentés...' : 'Mentés' }}
            </button>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    auth: { type: Object, required: true },
    fees: { type: Object, required: true },
})

const page   = usePage()
const saving = ref(false)
const errors = ref({})
const saved  = ref(false)

const form = ref({
    delivery_fee: props.fees.delivery_fee,
    service_fee:  props.fees.service_fee,
})

const save = () => {
    saving.value = true
    saved.value  = false
    router.post('/admin/settings', form.value, {
        preserveScroll: true,
        onSuccess: () => { saving.value = false; saved.value = true; errors.value = {} },
        onError:   (e) => { errors.value = e; saving.value = false },
    })
}
</script>

<style scoped>
@import './admin-table.css';

.settings-card { padding: 1.75rem; }

.section-title {
    font-size: 0.8rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #888;
    margin-bottom: 1.25rem;
}

.alert-success {
    background: #d1fae5;
    color: #065f46;
    border-radius: 8px;
    padding: 0.6rem 1rem;
    font-size: 0.875rem;
    font-weight: 500;
}
</style>
