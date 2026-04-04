<template>
    <AdminLayout :auth="auth" title="Termékek">
        <div class="admin-card">
            <div class="table-top">
                <button class="btn-primary" @click="openCreate">+ Új termék</button>
            </div>
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Név</th>
                            <th>Kategória</th>
                            <th>Ár</th>
                            <th>Sorrend</th>
                            <th>Elérhető</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="product in products" :key="product.id">
                            <td class="thumb-cell">
                                <img
                                    v-if="product.image"
                                    :src="product.image"
                                    class="product-thumb"
                                    :alt="product.name"
                                >
                                <div v-else class="product-thumb-placeholder">📷</div>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ product.name }}</div>
                                <div class="text-muted small" v-if="product.description">
                                    {{ product.description.slice(0, 60) }}{{ product.description.length > 60 ? '…' : '' }}
                                </div>
                            </td>
                            <td>{{ product.category }}</td>
                            <td class="fw-semibold">{{ formatPrice(product.price) }} Ft</td>
                            <td>{{ product.sort_order }}</td>
                            <td>
                                <button
                                    class="toggle-btn"
                                    :class="product.is_available ? 'toggle-on' : 'toggle-off'"
                                    @click="toggleAvailable(product)"
                                >{{ product.is_available ? 'Igen' : 'Nem' }}</button>
                            </td>
                            <td class="d-flex gap-2">
                                <button class="btn-edit"   @click="openEdit(product)">Szerkesztés</button>
                                <button class="btn-delete" @click="destroy(product.id)">Törlés</button>
                            </td>
                        </tr>
                        <tr v-if="products.length === 0">
                            <td colspan="7" class="text-center text-muted py-4">Nincsenek termékek.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Create / Edit modal -->
        <div class="modal-overlay" v-if="modal" @click.self="modal = false">
            <div class="modal-box">
                <p class="modal-title">{{ editing ? 'Termék szerkesztése' : 'Új termék' }}</p>

                <div class="mb-3">
                    <label class="form-label">Név *</label>
                    <input v-model="form.name" class="form-control" placeholder="pl. Margherita">
                    <span class="error-text" v-if="errors.name">{{ errors.name }}</span>
                </div>

                <div class="mb-3">
                    <label class="form-label">Leírás</label>
                    <textarea v-model="form.description" class="form-control" rows="2" placeholder="Rövid leírás..."></textarea>
                </div>

                <!-- Image upload -->
                <div class="mb-3">
                    <label class="form-label">Kép</label>
                    <div class="image-upload-area" @click="$refs.fileInput.click()">
                        <img v-if="imagePreview" :src="imagePreview" class="image-preview" alt="Előnézet">
                        <div v-else class="image-placeholder">
                            <span class="image-placeholder-icon">📷</span>
                            <span>Kattints a kép feltöltéséhez</span>
                        </div>
                        <div v-if="imagePreview" class="image-overlay">Kép cseréje</div>
                    </div>
                    <input
                        ref="fileInput"
                        type="file"
                        accept="image/*"
                        class="hidden-input"
                        @change="onFileChange"
                    >
                    <button
                        v-if="imagePreview"
                        class="btn-remove-image"
                        @click="removeImage"
                    >Kép eltávolítása</button>
                    <span class="error-text" v-if="errors.image">{{ errors.image }}</span>
                </div>

                <div class="row-two mb-3">
                    <div>
                        <label class="form-label">Ár (Ft) *</label>
                        <input v-model.number="form.price" type="number" class="form-control" min="1" placeholder="2900">
                        <span class="error-text" v-if="errors.price">{{ errors.price }}</span>
                    </div>
                    <div>
                        <label class="form-label">Kedvezményes ár (Ft) <span class="text-muted">(opcionális)</span></label>
                        <input v-model.number="form.sale_price" type="number" class="form-control" min="0" placeholder="2450">
                        <span class="error-text" v-if="errors.sale_price">{{ errors.sale_price }}</span>
                    </div>
                </div>

                <div class="row-two mb-3">
                    <div>
                        <label class="form-label">Sorrend</label>
                        <input v-model.number="form.sort_order" type="number" class="form-control" min="0">
                    </div>
                    <div></div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Kategória *</label>
                    <select v-model="form.category_id" class="form-select">
                        <option value="" disabled>Válassz kategóriát</option>
                        <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                    </select>
                    <span class="error-text" v-if="errors.category_id">{{ errors.category_id }}</span>
                </div>

                <div class="mb-3">
                    <label class="form-label">Hozzávalók</label>
                    <div class="checkbox-grid">
                        <label class="checkbox-item" v-for="ing in ingredients" :key="ing.id">
                            <input type="checkbox" :value="ing.id" v-model="form.ingredients">
                            {{ ing.name }}
                        </label>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Labelek</label>
                    <div class="checkbox-grid">
                        <label class="checkbox-item" v-for="label in labels" :key="label.id">
                            <input type="checkbox" :value="label.id" v-model="form.labels">
                            {{ label.name }}
                            <span class="text-muted small">({{ label.type === 'primary' ? 'elsőd.' : 'másod.' }})</span>
                        </label>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="checkbox-item">
                        <input type="checkbox" v-model="form.is_available">
                        Elérhető a menüben
                    </label>
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

const props = defineProps({
    auth:        { type: Object, required: true },
    products:    { type: Array,  default: () => [] },
    categories:  { type: Array,  default: () => [] },
    ingredients: { type: Array,  default: () => [] },
    labels:      { type: Array,  default: () => [] },
})

const modal        = ref(false)
const editing      = ref(null)
const saving       = ref(false)
const errors       = ref({})
const imageFile    = ref(null)
const imagePreview = ref(null)
const fileInput    = ref(null)

const emptyForm = () => ({
    name:        '',
    description: '',
    price:       0,
    sale_price:  null,
    sort_order:  0,
    is_available: true,
    category_id:  '',
    ingredients:  [],
    labels:       [],
})

const form = ref(emptyForm())

const onFileChange = (e) => {
    const file = e.target.files[0]
    if (!file) return
    imageFile.value    = file
    imagePreview.value = URL.createObjectURL(file)
}

const removeImage = () => {
    imageFile.value    = null
    imagePreview.value = null
    if (fileInput.value) fileInput.value.value = ''
}

const openCreate = () => {
    editing.value      = null
    form.value         = emptyForm()
    errors.value       = {}
    imageFile.value    = null
    imagePreview.value = null
    modal.value        = true
}

const openEdit = (p) => {
    editing.value      = p.id
    form.value         = {
        name:        p.name,
        description: p.description ?? '',
        price:       p.price,
        sale_price:  p.sale_price ?? null,
        sort_order:  p.sort_order,
        is_available: p.is_available,
        category_id:  p.category_id,
        ingredients:  [...p.ingredients],
        labels:       [...p.labels],
    }
    errors.value       = {}
    imageFile.value    = null
    imagePreview.value = p.image ?? null  // show existing image
    modal.value        = true
}

const save = () => {
    saving.value = true

    const data = { ...form.value }
    if (imageFile.value) {
        data.image = imageFile.value
    }

    const url    = editing.value ? `/admin/products/${editing.value}` : '/admin/products'
    const method = editing.value ? 'patch' : 'post'

    router[method](url, data, {
        preserveScroll: true,
        onSuccess: () => { modal.value = false; saving.value = false },
        onError:   (e) => { errors.value = e; saving.value = false },
    })
}

const toggleAvailable = (product) => {
    router.patch(`/admin/products/${product.id}`, {
        ...product,
        ingredients:  [...product.ingredients],
        labels:       [...product.labels],
        is_available: !product.is_available,
    }, { preserveScroll: true })
}

const destroy = (id) => {
    if (!confirm('Biztosan törlöd ezt a terméket?')) return
    router.delete(`/admin/products/${id}`, { preserveScroll: true })
}

const { formatPrice } = useFormatting()
</script>

<style scoped>
@import './admin-table.css';

.thumb-cell { width: 56px; }

.product-thumb {
    width: 48px;
    height: 48px;
    border-radius: 8px;
    object-fit: cover;
}

.product-thumb-placeholder {
    width: 48px;
    height: 48px;
    border-radius: 8px;
    background: #f3f4f6;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
}

.toggle-btn {
    border: none;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 700;
    padding: 3px 12px;
    cursor: pointer;
    transition: all 0.15s;
}
.toggle-on  { background: #d1e7dd; color: #0a3622; }
.toggle-off { background: #f8d7da; color: #842029; }

.row-two {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.75rem;
}

.error-text {
    color: #dc2626;
    font-size: 0.78rem;
    margin-top: 2px;
    display: block;
}

/* Image upload */
.image-upload-area {
    position: relative;
    border: 2px dashed #d1d5db;
    border-radius: 10px;
    overflow: hidden;
    cursor: pointer;
    transition: border-color 0.2s;
    height: 160px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f9fafb;
}

.image-upload-area:hover {
    border-color: #e63946;
}

.image-preview {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.image-placeholder {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    color: #9ca3af;
    font-size: 0.85rem;
}

.image-placeholder-icon { font-size: 2rem; }

.image-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0,0,0,0.45);
    color: #fff;
    font-size: 0.85rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.2s;
}

.image-upload-area:hover .image-overlay { opacity: 1; }

.hidden-input { display: none; }

.btn-remove-image {
    margin-top: 0.4rem;
    background: none;
    border: none;
    color: #dc2626;
    font-size: 0.78rem;
    cursor: pointer;
    padding: 0;
    text-decoration: underline;
}
</style>
