<template>
    <AdminLayout :auth="auth" title="Kuponok">
        <div class="admin-card">
            <div class="table-top">
                <button class="btn-primary" @click="openCreate">+ Új kupon</button>
            </div>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Kód</th>
                        <th>Kedvezmény</th>
                        <th>Max. felh./user</th>
                        <th>Össz. felhasználás</th>
                        <th>Lejárat</th>
                        <th>Állapot</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="coupon in coupons" :key="coupon.id">
                        <td class="text-muted">{{ coupon.id }}</td>
                        <td class="fw-semibold font-monospace">{{ coupon.code }}</td>
                        <td>
                            <span v-if="coupon.discount_type === 'percentage'">{{ coupon.discount_value }}%</span>
                            <span v-else>{{ formatPrice(coupon.discount_value) }} Ft</span>
                        </td>
                        <td>{{ coupon.max_uses_per_user ?? '∞' }}</td>
                        <td>{{ coupon.total_uses }}</td>
                        <td>{{ coupon.expires_at ? formatDate(coupon.expires_at) : '–' }}</td>
                        <td>
                            <span class="badge" :class="coupon.is_active ? 'badge-active' : 'badge-inactive'">
                                {{ coupon.is_active ? 'Aktív' : 'Inaktív' }}
                            </span>
                        </td>
                        <td class="d-flex gap-2 flex-wrap">
                            <button class="btn-edit"   @click="openEdit(coupon)">Szerkesztés</button>
                            <button class="btn-toggle" @click="toggle(coupon.id)">
                                {{ coupon.is_active ? 'Deaktiválás' : 'Aktiválás' }}
                            </button>
                            <button class="btn-delete" @click="destroy(coupon.id)">Törlés</button>
                        </td>
                    </tr>
                    <tr v-if="coupons.length === 0">
                        <td colspan="8" class="text-center text-muted py-4">Nincsenek kuponok.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Modal -->
        <div class="modal-overlay" v-if="modal" @click.self="modal = false">
            <div class="modal-box modal-wide">
                <p class="modal-title">{{ editing ? 'Kupon szerkesztése' : 'Új kupon' }}</p>

                <div class="mb-3">
                    <label class="form-label">Kupon kód</label>
                    <input v-model="form.code" class="form-control font-monospace text-uppercase" placeholder="pl. PIZZA20">
                    <span class="text-danger small" v-if="errors.code">{{ errors.code }}</span>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <label class="form-label">Kedvezmény típusa</label>
                        <select v-model="form.discount_type" class="form-select">
                            <option value="percentage">Százalék (%)</option>
                            <option value="fixed">Fix összeg (Ft)</option>
                        </select>
                        <span class="text-danger small" v-if="errors.discount_type">{{ errors.discount_type }}</span>
                    </div>
                    <div class="col-6">
                        <label class="form-label">Kedvezmény értéke</label>
                        <div class="input-group">
                            <input v-model.number="form.discount_value" type="number" min="0.01" step="0.01" class="form-control">
                            <span class="input-group-text">{{ form.discount_type === 'percentage' ? '%' : 'Ft' }}</span>
                        </div>
                        <span class="text-danger small" v-if="errors.discount_value">{{ errors.discount_value }}</span>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <label class="form-label">Max. felhasználás/user <span class="text-muted">(opcionális)</span></label>
                        <input v-model.number="form.max_uses_per_user" type="number" min="1" class="form-control" placeholder="korlátlan">
                        <span class="text-danger small" v-if="errors.max_uses_per_user">{{ errors.max_uses_per_user }}</span>
                    </div>
                    <div class="col-6">
                        <label class="form-label">Lejárat dátuma <span class="text-muted">(opcionális)</span></label>
                        <input v-model="form.expires_at" type="datetime-local" class="form-control">
                        <span class="text-danger small" v-if="errors.expires_at">{{ errors.expires_at }}</span>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="form-check">
                        <input v-model="form.is_active" class="form-check-input" type="checkbox" id="isActive">
                        <label class="form-check-label" for="isActive">Aktív</label>
                    </div>
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
import { useFormatting } from '@/composables/useFormatting'

defineProps({
    auth:    { type: Object, required: true },
    coupons: { type: Array,  default: () => [] },
})

const modal   = ref(false)
const editing = ref(null)
const saving  = ref(false)
const errors  = ref({})

const blankForm = () => ({
    code:               '',
    discount_type:      'percentage',
    discount_value:     10,
    max_uses_per_user:  null,
    expires_at:         null,
    is_active:          true,
})

const form = ref(blankForm())

const openCreate = () => {
    editing.value = null
    form.value = blankForm()
    errors.value = {}
    modal.value = true
}

const openEdit = (coupon) => {
    editing.value = coupon.id
    form.value = {
        code:               coupon.code,
        discount_type:      coupon.discount_type,
        discount_value:     coupon.discount_value,
        max_uses_per_user:  coupon.max_uses_per_user,
        expires_at:         coupon.expires_at ?? null,
        is_active:          coupon.is_active,
    }
    errors.value = {}
    modal.value = true
}

const save = () => {
    saving.value = true
    const url    = editing.value ? `/admin/coupons/${editing.value}` : '/admin/coupons'
    const method = editing.value ? 'patch' : 'post'
    router[method](url, form.value, {
        preserveScroll: true,
        onSuccess: () => { modal.value = false; saving.value = false },
        onError:   (e) => { errors.value = e; saving.value = false },
    })
}

const toggle = (id) => {
    router.patch(`/admin/coupons/${id}/toggle`, {}, { preserveScroll: true })
}

const destroy = (id) => {
    if (!confirm('Biztosan törlöd ezt a kupont?')) return
    router.delete(`/admin/coupons/${id}`, { preserveScroll: true })
}

const { formatPrice } = useFormatting()
const formatDate  = (d) => new Date(d).toLocaleString('hu-HU', { year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit' })
</script>

<style scoped>
@import './admin-table.css';

.modal-wide { max-width: 560px; }

.badge {
    display: inline-block;
    padding: 0.2rem 0.55rem;
    border-radius: 999px;
    font-size: 0.75rem;
    font-weight: 600;
}

.badge-active   { background: #d1fae5; color: #065f46; }
.badge-inactive { background: #fee2e2; color: #991b1b; }

.btn-toggle {
    padding: 0.25rem 0.6rem;
    border-radius: 6px;
    border: 1px solid #6366f1;
    background: transparent;
    color: #6366f1;
    font-size: 0.78rem;
    cursor: pointer;
    transition: all 0.15s;
    white-space: nowrap;
}

.btn-toggle:hover {
    background: #6366f1;
    color: #fff;
}

.font-monospace { font-family: monospace; }
</style>
