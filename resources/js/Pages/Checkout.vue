<template>
    <AppLayout :auth="auth">
        <section class="checkout-section">
            <div class="container">
                <h1 class="checkout-heading">Rendelés leadása</h1>

                <div v-if="cartItems.length === 0" class="empty-cart-notice">
                    <p>A kosarad üres. <a href="/">Vissza a főoldalra</a></p>
                </div>

                <div v-else class="row g-5">
                    <!-- Left column: items + address + payment -->
                    <div class="col-lg-7">

                        <!-- Items -->
                        <div class="checkout-card mb-4">
                            <h2 class="card-section-title">Rendelt tételek</h2>
                            <div class="order-items">
                                <div class="order-item" v-for="item in cartItems" :key="item.id">
                                    <img
                                        :src="item.image ?? 'https://images.unsplash.com/photo-1513104890138-7c749659a591?w=200&q=60'"
                                        :alt="item.name"
                                        class="order-item-img"
                                    >
                                    <div class="order-item-info">
                                        <span class="order-item-name">{{ item.name }}</span>
                                        <span class="order-item-qty">× {{ item.quantity }}</span>
                                    </div>
                                    <span class="order-item-total">{{ formatPrice(item.price * item.quantity) }} Ft</span>
                                </div>
                            </div>
                        </div>

                        <!-- Address -->
                        <div class="checkout-card mb-4">
                            <h2 class="card-section-title">Szállítási cím</h2>

                            <div class="row g-3">
                                <div class="col-4">
                                    <label class="form-label">Irányítószám</label>
                                    <input
                                        v-model="form.zip"
                                        type="text"
                                        class="form-control"
                                        :class="{ 'is-invalid': form.errors.zip }"
                                        placeholder="1234"
                                    >
                                    <div class="invalid-feedback">{{ form.errors.zip }}</div>
                                </div>
                                <div class="col-8">
                                    <label class="form-label">Város</label>
                                    <input
                                        v-model="form.city"
                                        type="text"
                                        class="form-control"
                                        :class="{ 'is-invalid': form.errors.city }"
                                        placeholder="Budapest"
                                    >
                                    <div class="invalid-feedback">{{ form.errors.city }}</div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Utca, házszám</label>
                                    <input
                                        v-model="form.street"
                                        type="text"
                                        class="form-control"
                                        :class="{ 'is-invalid': form.errors.street }"
                                        placeholder="Kossuth Lajos utca 1."
                                    >
                                    <div class="invalid-feedback">{{ form.errors.street }}</div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Emelet, ajtó <span class="text-muted">(opcionális)</span></label>
                                    <input
                                        v-model="form.note"
                                        type="text"
                                        class="form-control"
                                        placeholder="3. emelet, 12. ajtó"
                                    >
                                </div>
                                <div class="col-12">
                                    <div class="form-check">
                                        <input
                                            v-model="form.save_address"
                                            class="form-check-input"
                                            type="checkbox"
                                            id="saveAddress"
                                        >
                                        <label class="form-check-label" for="saveAddress">
                                            Mentsd el ezt a címet a profilomra
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment -->
                        <div class="checkout-card">
                            <h2 class="card-section-title">Fizetési mód</h2>
                            <div class="payment-options">
                                <label class="payment-option" :class="{ active: form.payment_method === 'cash' }">
                                    <input type="radio" v-model="form.payment_method" value="cash" hidden>
                                    <div class="payment-icon">💵</div>
                                    <div>
                                        <div class="payment-label">Készpénz</div>
                                        <div class="payment-desc">Fizetés kézbesítéskor</div>
                                    </div>
                                </label>
                                <label class="payment-option" :class="{ active: form.payment_method === 'card' }">
                                    <input type="radio" v-model="form.payment_method" value="card" hidden>
                                    <div class="payment-icon">💳</div>
                                    <div>
                                        <div class="payment-label">Bankkártya</div>
                                        <div class="payment-desc">Fizetés kézbesítéskor</div>
                                    </div>
                                </label>
                            </div>
                            <div v-if="form.errors.payment_method" class="text-danger small mt-2">
                                {{ form.errors.payment_method }}
                            </div>
                        </div>
                    </div>

                    <!-- Right column: summary -->
                    <div class="col-lg-5">
                        <div class="summary-card sticky-top">
                            <h2 class="card-section-title">Összesítő</h2>

                            <div class="summary-row" v-for="item in cartItems" :key="item.id">
                                <span class="summary-item-name">{{ item.name }} <span class="text-muted">×{{ item.quantity }}</span></span>
                                <span>{{ formatPrice(item.price * item.quantity) }} Ft</span>
                            </div>

                            <hr class="summary-divider">

                            <div class="summary-row">
                                <span>Részösszeg</span>
                                <span>{{ formatPrice(cartTotal) }} Ft</span>
                            </div>
                            <div class="summary-row">
                                <span>Kiszállítási díj</span>
                                <span>{{ formatPrice(deliveryFee) }} Ft</span>
                            </div>
                            <div class="summary-row">
                                <span>Szolgáltatási díj</span>
                                <span>{{ formatPrice(serviceFee) }} Ft</span>
                            </div>

                            <hr class="summary-divider">

                            <div class="summary-row summary-total">
                                <span>Fizetendő összeg</span>
                                <span>{{ formatPrice(grandTotal) }} Ft</span>
                            </div>

                            <button
                                class="btn w-100 order-btn mt-4"
                                :disabled="form.processing"
                                @click="submit"
                            >
                                {{ form.processing ? 'Feldolgozás...' : '🛒 Megrendelés leadása' }}
                            </button>

                            <p class="summary-note">
                                A megrendelés gombra kattintva elfogadod az általános szerződési feltételeket.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </AppLayout>
</template>

<script setup>
import { computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { useCart } from '@/composables/useCart'

defineOptions({ layout: null })

const props = defineProps({
    auth:         { type: Object, default: () => ({ user: null }) },
    savedAddress: { type: Object, default: () => ({}) },
    deliveryFee:  { type: Number, default: 990 },
    serviceFee:   { type: Number, default: 199 },
})

const { items: cartItems, cartTotal, clearCart } = useCart()

const grandTotal = computed(() => cartTotal.value + props.deliveryFee + props.serviceFee)

const form = useForm({
    payment_method: 'cash',
    zip:            props.savedAddress.zip    ?? '',
    city:           props.savedAddress.city   ?? '',
    street:         props.savedAddress.street ?? '',
    note:           props.savedAddress.note   ?? '',
    save_address:   false,
})

const submit = () => {
    form.transform(data => ({
        ...data,
        items: cartItems.value.map(i => ({ id: i.id, quantity: i.quantity })),
    })).post('/orders', {
        onSuccess: () => clearCart(),
    })
}

const formatPrice = (price) => Number(price).toLocaleString('hu-HU')
</script>

<style scoped>
.checkout-section {
    padding: 3rem 0 5rem;
    background: #f7f7f7;
    min-height: 100vh;
}

.checkout-heading {
    font-family: 'Georgia', serif;
    font-size: 2rem;
    font-weight: 700;
    color: #1a1a1a;
    margin-bottom: 2rem;
}

.checkout-card,
.summary-card {
    background: #fff;
    border-radius: 14px;
    padding: 1.75rem;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
}

.summary-card {
    top: 100px;
}

.card-section-title {
    font-family: 'Georgia', serif;
    font-size: 1.1rem;
    font-weight: 700;
    color: #1a1a1a;
    margin-bottom: 1.25rem;
    padding-bottom: 0.6rem;
    border-bottom: 2px solid #f0f0f0;
}

/* Order items */
.order-items { display: flex; flex-direction: column; gap: 0.85rem; }

.order-item {
    display: flex;
    align-items: center;
    gap: 0.85rem;
}

.order-item-img {
    width: 52px;
    height: 52px;
    border-radius: 8px;
    object-fit: cover;
    flex-shrink: 0;
}

.order-item-info {
    flex: 1;
    display: flex;
    align-items: baseline;
    gap: 0.4rem;
    min-width: 0;
}

.order-item-name {
    font-size: 0.9rem;
    font-weight: 600;
    color: #1a1a1a;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.order-item-qty {
    font-size: 0.8rem;
    color: #888;
    white-space: nowrap;
}

.order-item-total {
    font-size: 0.9rem;
    font-weight: 700;
    color: #e63946;
    white-space: nowrap;
}

/* Payment */
.payment-options {
    display: flex;
    gap: 1rem;
}

.payment-option {
    flex: 1;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    border: 2px solid #e5e5e5;
    border-radius: 10px;
    padding: 1rem;
    cursor: pointer;
    transition: border-color 0.2s, background 0.2s;
}

.payment-option.active {
    border-color: #e63946;
    background: #fff5f5;
}

.payment-icon { font-size: 1.6rem; line-height: 1; }

.payment-label {
    font-size: 0.9rem;
    font-weight: 700;
    color: #1a1a1a;
}

.payment-desc {
    font-size: 0.75rem;
    color: #888;
    margin-top: 2px;
}

/* Summary */
.summary-row {
    display: flex;
    justify-content: space-between;
    font-size: 0.9rem;
    color: #555;
    margin-bottom: 0.6rem;
}

.summary-item-name {
    max-width: 65%;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.summary-divider {
    border-color: #f0f0f0;
    margin: 0.75rem 0;
}

.summary-total {
    font-size: 1.05rem;
    font-weight: 700;
    color: #1a1a1a;
}

.order-btn {
    background: #e63946;
    color: #fff;
    border: none;
    border-radius: 10px;
    padding: 0.8rem;
    font-size: 1rem;
    font-weight: 700;
    transition: background 0.2s;
}

.order-btn:hover:not(:disabled) { background: #c1121f; color: #fff; }
.order-btn:disabled { opacity: 0.7; color: #fff; }

.summary-note {
    font-size: 0.72rem;
    color: #aaa;
    text-align: center;
    margin-top: 0.75rem;
    margin-bottom: 0;
    line-height: 1.4;
}

.empty-cart-notice {
    text-align: center;
    padding: 4rem 0;
    color: #888;
}

.empty-cart-notice a { color: #e63946; }
</style>
