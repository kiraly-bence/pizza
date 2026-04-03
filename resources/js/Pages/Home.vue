<template>
    <AppLayout :auth="auth">
        <section class="hero-section">
            <div class="container">
                <div class="hero-content text-center">
                    <h1 class="hero-title">Friss pizzák,<br><span class="accent">azonnali szállítás</span></h1>
                    <p class="hero-sub">Házi készítésű pizzáink friss alapanyagokból, egyenesen az ajtódig.</p>
                </div>
            </div>
        </section>

        <section class="pizzas-section">
            <div class="container">
                <h2 class="section-title">Pizzáink</h2>
                <div class="row g-4">
                    <div
                        v-for="pizza in pizzas"
                        :key="pizza.id"
                        class="col-12 col-md-6 col-lg-4"
                    >
                        <div class="card pizza-card h-100" @click="openPizzaModal(pizza)" role="button">
                            <div class="pizza-img-wrap">
                                <img :src="pizza.image" :alt="pizza.name" class="card-img-top pizza-img">
                                <div class="pizza-badge" v-if="pizza.badge">{{ pizza.badge }}</div>
                            </div>
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <h5 class="card-title pizza-name mb-0">{{ pizza.name }}</h5>
                                    <span class="pizza-price">{{ pizza.price }} Ft</span>
                                </div>
                                <p class="card-text pizza-desc flex-grow-1">{{ pizza.description }}</p>
                                <div class="pizza-tags mt-2">
                                    <span v-for="tag in pizza.tags" :key="tag" class="pizza-tag">{{ tag }}</span>
                                </div>
                            </div>
                            <div class="card-footer pizza-card-footer">
                                <span class="order-hint">Kattints a rendeléshez →</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="modal fade" id="pizzaModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content pizza-modal" v-if="selectedPizza">
                    <div class="modal-header border-0">
                        <h5 class="modal-title pizza-modal-title">{{ selectedPizza.name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Bezárás"></button>
                    </div>
                    <div class="modal-body pt-0">
                        <div class="row g-4">
                            <div class="col-md-5">
                                <img :src="selectedPizza.image" :alt="selectedPizza.name" class="img-fluid rounded-3 w-100 pizza-modal-img">
                            </div>
                            <div class="col-md-7 d-flex flex-column justify-content-between">
                                <div>
                                    <p class="pizza-modal-desc">{{ selectedPizza.description }}</p>
                                    <div class="pizza-tags mb-3">
                                        <span v-for="tag in selectedPizza.tags" :key="tag" class="pizza-tag">{{ tag }}</span>
                                    </div>
                                </div>
                                <div class="add-to-cart-section mt-3">
                                    <button class="btn add-to-cart-btn w-100">
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
import { ref, onMounted } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import { Modal } from 'bootstrap'

defineProps({
    auth: {
        type: Object,
        default: () => ({ user: null })
    }
})

defineOptions({ layout: null })

const selectedPizza = ref(null)
let pizzaModalInstance = null

onMounted(() => {
    pizzaModalInstance = new Modal(document.getElementById('pizzaModal'))
})

const openPizzaModal = (pizza) => {
    selectedPizza.value = pizza
    pizzaModalInstance.show()
}

const pizzas = [
    {
        id: 1,
        name: 'Margherita',
        description: 'Klasszikus olasz pizza paradicsomszósszal, friss mozzarellával és bazsalikommal.',
        price: '2 490',
        image: 'https://images.unsplash.com/photo-1574071318508-1cdbab80d002?w=600&q=80',
        tags: ['Vegetáriánus', 'Klasszikus'],
        badge: null,
    },
    {
        id: 2,
        name: 'Diavola',
        description: 'Csípős szalámi, jalapeño paprika és füstölt mozzarella az igazi pikáns élményért.',
        price: '2 890',
        image: 'https://images.unsplash.com/photo-1628840042765-356cda07504e?w=600&q=80',
        tags: ['Csípős', 'Bestseller'],
        badge: '🔥 Legjobb',
    },
    {
        id: 3,
        name: 'Quattro Formaggi',
        description: 'Négy sajt harmonikus keveréke: mozzarella, gorgonzola, parmezán és pecorino.',
        price: '3 190',
        image: 'https://images.unsplash.com/photo-1513104890138-7c749659a591?w=600&q=80',
        tags: ['Vegetáriánus', 'Sajtos'],
        badge: null,
    },
    {
        id: 4,
        name: 'Prosciutto e Funghi',
        description: 'Pármai sonka és friss erdei gombák, tejszínes alapon tálalva.',
        price: '3 090',
        image: 'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?w=600&q=80',
        tags: ['Sonkás', 'Gombás'],
        badge: null,
    },
    {
        id: 5,
        name: 'BBQ Csirke',
        description: 'Grillezett csirkemell, füstölt BBQ szósz, lilahagyma és mozzarella.',
        price: '3 290',
        image: 'https://images.unsplash.com/photo-1571407970349-bc81e7e96d47?w=600&q=80',
        tags: ['Csirkés', 'BBQ'],
        badge: '⭐ Új',
    },
    {
        id: 6,
        name: 'Tonno e Cipolla',
        description: 'Tonhal, vöröshagyma, kapribogyó és olívaolaj — a mediterrán ízek kedvelőinek.',
        price: '2 990',
        image: 'https://images.unsplash.com/photo-1593560708920-61dd98c46a4e?w=600&q=80',
        tags: ['Halas', 'Mediterrán'],
        badge: null,
    },
]
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

.accent {
    color: #e63946;
}

.hero-sub {
    color: rgba(255,255,255,0.6);
    font-size: 1.1rem;
    max-width: 460px;
    margin: 0 auto;
}

.pizzas-section {
    padding-bottom: 3rem;
}

.section-title {
    font-family: 'Georgia', serif;
    font-size: 1.8rem;
    font-weight: 700;
    color: #1a1a1a;
    margin-bottom: 1.75rem;
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

.pizza-card:hover .pizza-img {
    transform: scale(1.04);
}

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
    margin-left: 8px;
}

.pizza-desc {
    font-size: 0.875rem;
    color: #666;
    line-height: 1.5;
    margin-top: 0.4rem;
}

.pizza-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
}

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