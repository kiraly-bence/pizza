<template>
    <AppLayout :auth="auth">
        <section class="hero-section">
            <div class="container">
                <div class="hero-content text-center">
                    <h1 class="hero-title">Friss ételek,<br><span class="accent">azonnali szállítás</span></h1>
                    <p class="hero-sub">Házi készítésű fogásaink friss alapanyagokból, egyenesen az ajtódig.</p>
                </div>
            </div>
        </section>

        <section class="menu-section">
            <div class="container">
                <div v-for="category in categories" :key="category.id" class="category-block">
                    <h2 class="section-title">{{ category.name }}</h2>

                    <div v-if="category.products.length === 0" class="text-muted fst-italic mb-4">
                        Hamarosan...
                    </div>

                    <div v-else class="row g-4">
                        <div
                            v-for="product in category.products"
                            :key="product.id"
                            class="col-12 col-md-6 col-lg-4"
                        >
                            <div class="card pizza-card h-100" @click="openModal(product)" role="button">
                                <div class="pizza-img-wrap">
                                    <img
                                        :src="product.image ?? 'https://images.unsplash.com/photo-1513104890138-7c749659a591?w=600&q=80'"
                                        :alt="product.name"
                                        class="card-img-top pizza-img"
                                    >
                                    <div
                                        v-for="label in primaryLabels(product)"
                                        :key="label.id"
                                        class="pizza-badge"
                                    >{{ label.name }}</div>
                                </div>
                                <div class="card-body d-flex flex-column">
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <h5 class="card-title pizza-name mb-0">{{ product.name }}</h5>
                                        <span class="pizza-price">
                                            <span v-if="product.sale_price" class="pizza-price-original">{{ formatPrice(product.price) }} Ft</span>
                                            {{ formatPrice(product.sale_price ?? product.price) }} Ft
                                        </span>
                                    </div>
                                    <p class="card-text pizza-desc flex-grow-1">{{ product.description }}</p>
                                    <div class="pizza-tags mt-2">
                                        <span
                                            v-for="label in secondaryLabels(product)"
                                            :key="label.id"
                                            class="pizza-tag"
                                        >{{ label.name }}</span>
                                    </div>
                                </div>
                                <div class="card-footer pizza-card-footer">
                                    <span class="order-hint">Kattints a rendeléshez →</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Product modal -->
        <div class="modal fade" id="productModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content pizza-modal" v-if="selectedProduct">
                    <div class="modal-header border-0">
                        <h5 class="modal-title pizza-modal-title">{{ selectedProduct.name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Bezárás"></button>
                    </div>
                    <div class="modal-body pt-0">
                        <div class="row g-4">
                            <div class="col-md-5">
                                <img
                                    :src="selectedProduct.image ?? 'https://images.unsplash.com/photo-1513104890138-7c749659a591?w=600&q=80'"
                                    :alt="selectedProduct.name"
                                    class="img-fluid rounded-3 w-100 pizza-modal-img"
                                >
                            </div>
                            <div class="col-md-7 d-flex flex-column">
                                <p class="pizza-modal-desc">{{ selectedProduct.description }}</p>

                                <div v-if="selectedProduct.ingredients.length" class="mb-3">
                                    <p class="ingredients-title">Hozzávalók:</p>
                                    <div class="d-flex flex-wrap gap-2">
                                        <span
                                            v-for="ingredient in selectedProduct.ingredients"
                                            :key="ingredient.id"
                                            class="ingredient-tag"
                                        >{{ ingredient.name }}</span>
                                    </div>
                                </div>

                                <div class="mt-auto">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="modal-price">
                                            <span v-if="selectedProduct.original_price" class="modal-price-original">{{ formatPrice(selectedProduct.original_price) }} Ft</span>
                                            {{ formatPrice(selectedProduct.price) }} Ft
                                        </span>
                                    </div>
                                    <button class="btn add-to-cart-btn w-100" @click="addToCart(selectedProduct)">
                                        🛒 Hozzáadás a kosárhoz
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import { Modal } from 'bootstrap'
import { useCart } from '@/composables/useCart'

defineProps({
    auth: {
        type: Object,
        default: () => ({ user: null })
    },
    categories: {
        type: Array,
        default: () => []
    }
})

defineOptions({ layout: null })

const { addItem } = useCart()
const selectedProduct = ref(null)

const addToCart = (product) => {
    addItem(product)
    Modal.getOrCreateInstance(document.getElementById('productModal')).hide()
}

const openModal = (product) => {
    selectedProduct.value = product
    const el = document.getElementById('productModal')
    Modal.getOrCreateInstance(el).show()
}

const primaryLabels = (product) =>
    product.labels.filter(l => l.type === 'primary')

const secondaryLabels = (product) =>
    product.labels.filter(l => l.type === 'secondary')

const formatPrice = (price) =>
    Number(price).toLocaleString('hu-HU')
</script>

<style scoped>
.hero-section {
    background: #1a1a1a;
    padding: 5rem 0 4rem;
    margin-bottom: 3rem;
    position: relative;
    overflow: hidden;
}

.hero-section::before {
    content: '';
    position: absolute;
    inset: 0;
    background: radial-gradient(ellipse at 60% 50%, rgba(230, 57, 70, 0.15) 0%, transparent 70%);
    pointer-events: none;
}

.hero-title {
    font-family: 'Georgia', serif;
    font-size: clamp(2.2rem, 5vw, 3.5rem);
    font-weight: 700;
    color: #fff;
    line-height: 1.2;
    margin-bottom: 1rem;
}

.accent { color: #e63946; }

.hero-sub {
    color: rgba(255,255,255,0.6);
    font-size: 1.1rem;
    max-width: 460px;
    margin: 0 auto;
}

.menu-section { padding-bottom: 3rem; }

.category-block { margin-bottom: 3.5rem; }

.section-title {
    font-family: 'Georgia', serif;
    font-size: 1.8rem;
    font-weight: 700;
    color: #1a1a1a;
    margin-bottom: 1.75rem;
    padding-bottom: 0.6rem;
    border-bottom: 3px solid #e63946;
    display: inline-block;
}

.pizza-card {
    border: 1px solid #eee;
    border-radius: 14px;
    overflow: hidden;
    transition: transform 0.2s, box-shadow 0.2s;
    cursor: pointer;
    background: #fff;
}

.pizza-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 30px rgba(0,0,0,0.1);
}

.pizza-img-wrap {
    position: relative;
    overflow: hidden;
    height: 200px;
}

.pizza-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s;
}

.pizza-card:hover .pizza-img { transform: scale(1.04); }

.pizza-badge {
    position: absolute;
    top: 10px;
    left: 10px;
    background: #e63946;
    color: #fff;
    font-size: 0.75rem;
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 20px;
}

.pizza-name {
    font-family: 'Georgia', serif;
    font-size: 1.1rem;
    font-weight: 700;
    color: #1a1a1a;
}

.pizza-price {
    font-size: 1rem;
    font-weight: 700;
    color: #e63946;
    white-space: nowrap;
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 1px;
    margin-left: 8px;
}

.pizza-desc {
    font-size: 0.875rem;
    color: #666;
    line-height: 1.5;
    margin-top: 0.4rem;
}

.pizza-tags { display: flex; flex-wrap: wrap; gap: 5px; }

.pizza-tag {
    background: #f5f5f5;
    color: #555;
    font-size: 0.72rem;
    font-weight: 600;
    padding: 3px 9px;
    border-radius: 20px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.pizza-card-footer {
    background: #fafafa;
    border-top: 1px solid #f0f0f0;
    padding: 0.6rem 1rem;
}

.order-hint {
    font-size: 0.8rem;
    color: #e63946;
    font-weight: 600;
}

.pizza-modal {
    border-radius: 16px;
    border: none;
    overflow: hidden;
}

.pizza-modal-title {
    font-family: 'Georgia', serif;
    font-size: 1.4rem;
    font-weight: 700;
}

.pizza-modal-img {
    object-fit: cover;
    height: 260px;
}

.pizza-modal-desc {
    color: #555;
    font-size: 0.95rem;
    line-height: 1.6;
    margin-bottom: 1rem;
}

.ingredients-title {
    font-size: 0.85rem;
    font-weight: 700;
    color: #333;
    margin-bottom: 0.5rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.ingredient-tag {
    background: #fff3f3;
    color: #c1121f;
    border: 1px solid #fcc;
    font-size: 0.78rem;
    font-weight: 500;
    padding: 3px 10px;
    border-radius: 20px;
}

.pizza-price-original {
    font-size: 0.78rem;
    font-weight: 400;
    color: #aaa;
    text-decoration: line-through;
}

.modal-price {
    font-size: 1.4rem;
    font-weight: 700;
    color: #e63946;
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.modal-price-original {
    font-size: 0.95rem;
    font-weight: 400;
    color: #aaa;
    text-decoration: line-through;
}

.add-to-cart-btn {
    background: #e63946;
    color: #fff;
    border: none;
    border-radius: 10px;
    padding: 0.75rem;
    font-size: 1rem;
    font-weight: 700;
    transition: background 0.2s;
}

.add-to-cart-btn:hover {
    background: #c1121f;
    color: #fff;
}
</style>
