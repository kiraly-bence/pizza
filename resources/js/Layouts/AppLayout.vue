<template>
    <div>
        <nav class="navbar navbar-expand-lg sticky-top" id="mainNavbar">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center gap-2" href="/">
                    <div class="logo-icon">🍕</div>
                    <span class="brand-name">PizzaRex</span>
                </a>

                <div class="ms-auto d-flex align-items-center gap-3">
                    <button class="btn cart-btn" @click="openCart">
                        🛒
                        <span class="cart-count" v-if="cartCount > 0">{{ cartCount }}</span>
                    </button>
                    <template v-if="auth && auth.user">
                        <div class="dropdown">
                            <button
                                class="btn user-btn dropdown-toggle d-flex align-items-center gap-2"
                                type="button"
                                data-bs-toggle="dropdown"
                                aria-expanded="false"
                            >
                                <div class="user-avatar">{{ initials }}</div>
                                <span>{{ auth.user.name }}</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="/rendeleseim">Rendeléseim</a></li>
                                <li><a class="dropdown-item" href="/profil">Profilom</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <button class="dropdown-item text-danger" @click="logout">
                                        Kijelentkezés
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </template>
                    <template v-else>
                        <button class="btn login-btn" data-bs-toggle="modal" data-bs-target="#authModal">
                            Bejelentkezés
                        </button>
                    </template>
                </div>
            </div>
        </nav>

        <div v-if="restaurant && !restaurant.is_open" class="closed-banner">
            <div class="container text-center">
                <span v-if="restaurant.is_paused">
                    Az étterem jelenleg szünetelteti a rendelések fogadását.
                </span>
                <span v-else>
                    Az étterem jelenleg zárva van, nem fogad rendeléseket.
                </span>
            </div>
        </div>

        <div v-if="showVerifyBanner" class="verify-banner">
            <div class="container d-flex align-items-center justify-content-center gap-3 flex-wrap">
                <span>Az e-mail címed még nincs megerősítve. Ellenőrizd a postaládádat!</span>
                <button class="verify-resend-btn" :disabled="resending" @click="resendVerification">
                    {{ resendDone ? 'Elküldve!' : (resending ? '...' : 'Újraküldés') }}
                </button>
            </div>
        </div>

        <main>
            <slot />
        </main>

        <!-- Auth modal -->
        <div class="modal fade" id="authModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content auth-modal">
                    <div class="modal-header border-0 pb-0">
                        <div class="auth-tabs d-flex gap-0 w-100" v-if="authTab !== 'forgot'">
                            <button
                                class="auth-tab-btn flex-fill"
                                :class="{ active: authTab === 'login' }"
                                @click="switchTab('login')"
                            >Bejelentkezés</button>
                            <button
                                class="auth-tab-btn flex-fill"
                                :class="{ active: authTab === 'register' }"
                                @click="switchTab('register')"
                            >Regisztráció</button>
                        </div>
                        <div v-else class="d-flex align-items-center gap-2 w-100">
                            <button class="btn btn-sm btn-link p-0 text-secondary" @click="switchTab('login')">
                                ← Vissza
                            </button>
                            <span class="fw-semibold">Elfelejtett jelszó</span>
                        </div>
                        <button type="button" class="btn-close ms-3" data-bs-dismiss="modal" aria-label="Bezárás"></button>
                    </div>

                    <div class="modal-body pt-3">

                        <!-- Login -->
                        <div v-if="authTab === 'login'">
                            <div v-if="loginForm.errors.email" class="alert alert-danger py-2 small mb-3">
                                {{ loginForm.errors.email }}
                            </div>
                            <div class="mb-3">
                                <label class="form-label">E-mail cím</label>
                                <input
                                    v-model="loginForm.email"
                                    type="email"
                                    class="form-control"
                                    :class="{ 'is-invalid': loginForm.errors.email }"
                                    placeholder="pelda@email.hu"
                                    @keyup.enter="submitLogin"
                                >
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Jelszó</label>
                                <input
                                    v-model="loginForm.password"
                                    type="password"
                                    class="form-control"
                                    :class="{ 'is-invalid': loginForm.errors.password }"
                                    placeholder="••••••••"
                                    @keyup.enter="submitLogin"
                                >
                                <div v-if="loginForm.errors.password" class="invalid-feedback">
                                    {{ loginForm.errors.password }}
                                </div>
                            </div>
                            <div class="text-end mb-4">
                                <button class="btn btn-link btn-sm p-0 forgot-link" @click="switchTab('forgot')">
                                    Elfelejtett jelszó?
                                </button>
                            </div>
                            <button
                                class="btn w-100 submit-btn"
                                :disabled="loginForm.processing"
                                @click="submitLogin"
                            >
                                {{ loginForm.processing ? 'Bejelentkezés...' : 'Bejelentkezés' }}
                            </button>
                        </div>

                        <!-- Register -->
                        <div v-else-if="authTab === 'register'">
                            <div class="mb-3">
                                <label class="form-label">Teljes név</label>
                                <input
                                    v-model="registerForm.name"
                                    type="text"
                                    class="form-control"
                                    :class="{ 'is-invalid': registerForm.errors.name }"
                                    placeholder="Kovács János"
                                >
                                <div v-if="registerForm.errors.name" class="invalid-feedback">
                                    {{ registerForm.errors.name }}
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">E-mail cím</label>
                                <input
                                    v-model="registerForm.email"
                                    type="email"
                                    class="form-control"
                                    :class="{ 'is-invalid': registerForm.errors.email }"
                                    placeholder="pelda@email.hu"
                                >
                                <div v-if="registerForm.errors.email" class="invalid-feedback">
                                    {{ registerForm.errors.email }}
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Jelszó</label>
                                <input
                                    v-model="registerForm.password"
                                    type="password"
                                    class="form-control"
                                    :class="{ 'is-invalid': registerForm.errors.password }"
                                    placeholder="••••••••"
                                >
                                <div v-if="registerForm.errors.password" class="invalid-feedback">
                                    {{ registerForm.errors.password }}
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="form-label">Jelszó megerősítése</label>
                                <input
                                    v-model="registerForm.password_confirmation"
                                    type="password"
                                    class="form-control"
                                    placeholder="••••••••"
                                    @keyup.enter="submitRegister"
                                >
                            </div>
                            <button
                                class="btn w-100 submit-btn"
                                :disabled="registerForm.processing"
                                @click="submitRegister"
                            >
                                {{ registerForm.processing ? 'Regisztráció...' : 'Regisztráció' }}
                            </button>
                        </div>

                        <!-- Forgot password -->
                        <div v-else-if="authTab === 'forgot'">
                            <div v-if="forgotSent" class="text-center py-3">
                                <div class="forgot-icon">📧</div>
                                <p class="forgot-text">
                                    Ha ez az e-mail cím regisztrált nálunk, hamarosan megérkezik a jelszó-visszaállítási hivatkozás.
                                </p>
                                <button class="btn w-100 submit-btn mt-2" @click="switchTab('login')">
                                    Vissza a bejelentkezéshez
                                </button>
                            </div>
                            <div v-else>
                                <p class="forgot-text mb-3">
                                    Add meg a fiókodhoz tartozó e-mail címet, és küldünk egy visszaállítási hivatkozást.
                                </p>
                                <div v-if="forgotForm.errors.email" class="alert alert-danger py-2 small mb-3">
                                    {{ forgotForm.errors.email }}
                                </div>
                                <div class="mb-4">
                                    <label class="form-label">E-mail cím</label>
                                    <input
                                        v-model="forgotForm.email"
                                        type="email"
                                        class="form-control"
                                        :class="{ 'is-invalid': forgotForm.errors.email }"
                                        placeholder="pelda@email.hu"
                                        @keyup.enter="submitForgot"
                                    >
                                </div>
                                <button
                                    class="btn w-100 submit-btn"
                                    :disabled="forgotForm.processing"
                                    @click="submitForgot"
                                >
                                    {{ forgotForm.processing ? 'Küldés...' : 'Hivatkozás küldése' }}
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- Cart sidebar -->
        <transition name="cart-slide">
            <div v-if="cartOpen" class="cart-sidebar">
                <div class="cart-header">
                    <span class="cart-title">Kosár</span>
                    <button class="btn-close btn-close-white" @click="closeCart"></button>
                </div>

                <div class="cart-body" v-if="cartItems.length > 0">
                    <div class="cart-item" v-for="item in cartItems" :key="item.id">
                        <img
                            :src="item.image ?? 'https://images.unsplash.com/photo-1513104890138-7c749659a591?w=200&q=60'"
                            :alt="item.name"
                            class="cart-item-img"
                        >
                        <div class="cart-item-info">
                            <div class="cart-item-name">{{ item.name }}</div>
                            <div class="cart-item-price">{{ formatPrice(item.price) }} Ft</div>
                        </div>
                        <div class="cart-item-actions">
                            <button class="qty-btn" @click="updateQuantity(item.id, item.quantity - 1)">−</button>
                            <span class="qty-val">{{ item.quantity }}</span>
                            <button class="qty-btn" @click="updateQuantity(item.id, item.quantity + 1)">+</button>
                        </div>
                    </div>
                </div>

                <div class="cart-empty" v-else>
                    <div class="cart-empty-icon">🛒</div>
                    <p>A kosár üres</p>
                </div>

                <div class="cart-footer" v-if="cartItems.length > 0">
                    <div class="cart-total">
                        <span>Összesen:</span>
                        <span class="cart-total-price">{{ formatPrice(cartTotal) }} Ft</span>
                    </div>
                    <template v-if="auth && auth.user">
                        <a href="/checkout" class="btn w-100 submit-btn mt-3" @click="closeCart">Megrendelés →</a>
                    </template>
                    <template v-else>
                        <p class="cart-login-hint">A rendelés leadásához be kell jelentkezned.</p>
                        <button
                            class="btn w-100 submit-btn"
                            @click="closeCart(); getAuthModal().show()"
                        >Bejelentkezés</button>
                    </template>
                </div>
            </div>
        </transition>
        <transition name="overlay-fade">
            <div v-if="cartOpen" class="cart-overlay" @click="closeCart"></div>
        </transition>

        <!-- Order success overlay -->
        <transition name="overlay-fade">
            <div v-if="orderSuccess" class="thankyou-overlay" @click.self="dismissOrder">
                <div class="thankyou-card">
                    <div class="thankyou-icon">🍕</div>
                    <h2 class="thankyou-title">Köszönjük a rendelésed!</h2>
                    <p class="thankyou-text">
                        Megkaptuk a rendelésed, és már dolgozunk rajta.<br>
                        Hamarosan megérkezik hozzád a friss étel!
                    </p>
                    <button class="btn submit-btn px-5" @click="dismissOrder">Rendben</button>
                </div>
            </div>
        </transition>

        <footer class="site-footer">
            <div class="container text-center">
                <p class="mb-0">© 2026 PizzaRex — Minden jog fenntartva</p>
            </div>
        </footer>
    </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { useForm, router, usePage } from '@inertiajs/vue3'
import { Modal } from 'bootstrap'
import { useCart } from '@/composables/useCart'

const props = defineProps({
    auth: {
        type: Object,
        default: () => ({ user: null })
    }
})

const page       = usePage()
const restaurant = computed(() => page.props.restaurant)

const showVerifyBanner = computed(() =>
    props.auth?.user && !props.auth.user.email_verified_at
)
const resending  = ref(false)
const resendDone = ref(false)

const resendVerification = () => {
    resending.value = true
    router.post('/email/verification-notification', {}, {
        preserveScroll: true,
        onFinish: () => { resending.value = false; resendDone.value = true },
    })
}

const authTab    = ref('login')
const forgotSent = ref(page.props.flash?.forgot_status === 'sent')

watch(() => page.props.flash?.forgot_status, (val) => {
    if (val === 'sent') forgotSent.value = true
})

const orderSuccess = ref(!!page.props.flash?.order_success)
const dismissOrder = () => { orderSuccess.value = false }

const switchTab = (tab) => {
    authTab.value = tab
    if (tab !== 'forgot') {
        forgotSent.value = false
        forgotForm.email = ''
        forgotForm.clearErrors()
    }
    loginForm.clearErrors()
    registerForm.clearErrors()
}

const initials = computed(() => {
    if (!props.auth?.user) return ''
    return props.auth.user.name
        .split(' ')
        .map(n => n[0])
        .join('')
        .toUpperCase()
        .slice(0, 2)
})

// Login
const loginForm = useForm({
    email: '',
    password: '',
})

const getAuthModal = () => Modal.getOrCreateInstance(document.getElementById('authModal'))

const submitLogin = () => {
    loginForm.post('/login', {
        onSuccess: () => getAuthModal().hide(),
    })
}

// Register
const registerForm = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
})

const submitRegister = () => {
    registerForm.post('/register')
}

// Cart
const { items: cartItems, cartCount, cartTotal, removeItem, updateQuantity } = useCart()
const cartOpen = ref(false)
const openCart = () => { cartOpen.value = true }
const closeCart = () => { cartOpen.value = false }

// Forgot password
const forgotForm = useForm({ email: '' })

const submitForgot = () => {
    forgotForm.post('/forgot-password')
}

const formatPrice = (price) => Number(price).toLocaleString('hu-HU')

// Logout
const logout = () => {
    router.post('/logout')
}
</script>

<style scoped>
#mainNavbar {
    background: #1a1a1a;
    border-bottom: 2px solid #e63946;
    padding: 0.75rem 0;
}

.brand-name {
    font-family: 'Georgia', serif;
    font-size: 1.5rem;
    font-weight: 700;
    color: #fff;
    letter-spacing: -0.5px;
}

.logo-icon {
    font-size: 1.6rem;
    line-height: 1;
}

.cart-btn {
    position: relative;
    background: transparent;
    color: #fff;
    border: 1px solid rgba(255,255,255,0.2);
    border-radius: 8px;
    padding: 0.45rem 0.85rem;
    font-size: 1.1rem;
    line-height: 1;
    transition: border-color 0.2s;
}

.cart-btn:hover {
    border-color: rgba(255,255,255,0.5);
    color: #fff;
}

.cart-count {
    position: absolute;
    top: -6px;
    right: -6px;
    background: #e63946;
    color: #fff;
    font-size: 0.65rem;
    font-weight: 700;
    min-width: 18px;
    height: 18px;
    border-radius: 9px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0 4px;
}

.login-btn {
    background: #e63946;
    color: #fff;
    border: none;
    border-radius: 6px;
    padding: 0.45rem 1.25rem;
    font-weight: 600;
    font-size: 0.9rem;
    transition: background 0.2s;
}

.login-btn:hover {
    background: #c1121f;
    color: #fff;
}

.user-btn {
    background: transparent;
    color: #fff;
    border: 1px solid rgba(255,255,255,0.2);
    border-radius: 8px;
    padding: 0.4rem 0.9rem;
    font-size: 0.9rem;
    transition: border-color 0.2s;
}

.user-btn:hover {
    border-color: rgba(255,255,255,0.5);
    color: #fff;
}

.user-avatar {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: #e63946;
    color: #fff;
    font-size: 0.7rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
}

.auth-modal {
    border-radius: 14px;
    border: none;
    overflow: hidden;
}

.auth-tabs {
    border-radius: 8px;
    overflow: hidden;
    border: 1px solid #dee2e6;
}

.auth-tab-btn {
    background: transparent;
    border: none;
    padding: 0.6rem 1rem;
    font-size: 0.9rem;
    font-weight: 500;
    color: #666;
    transition: all 0.2s;
}

.auth-tab-btn.active {
    background: #e63946;
    color: #fff;
}

.forgot-link {
    font-size: 0.82rem;
    color: #e63946;
    text-decoration: none;
}

.forgot-link:hover {
    color: #c1121f;
    text-decoration: underline;
}

.forgot-info {
    text-align: center;
    padding: 1rem 0.5rem;
}

.forgot-icon {
    font-size: 2.5rem;
    margin-bottom: 1rem;
}

.forgot-text {
    color: #555;
    font-size: 0.9rem;
    line-height: 1.6;
    margin-bottom: 0.75rem;
}

.forgot-email {
    display: inline-block;
    font-size: 1rem;
    font-weight: 700;
    color: #e63946;
    text-decoration: none;
    margin-bottom: 0.75rem;
}

.forgot-email:hover {
    text-decoration: underline;
}

.forgot-subtext {
    color: #888;
    font-size: 0.82rem;
    margin: 0;
}

.submit-btn {
    background: #e63946;
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 0.65rem;
    font-weight: 600;
    font-size: 0.95rem;
    transition: background 0.2s;
}

.submit-btn:hover:not(:disabled) {
    background: #c1121f;
}

.submit-btn:disabled {
    opacity: 0.7;
    color: #fff;
}

.site-footer {
    background: #1a1a1a;
    color: rgba(255,255,255,0.5);
    font-size: 0.85rem;
    padding: 1.5rem 0;
    margin-top: 4rem;
}

/* Cart sidebar */
.cart-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.45);
    z-index: 1040;
}

.cart-sidebar {
    position: fixed;
    top: 0;
    right: 0;
    width: 360px;
    max-width: 100vw;
    height: 100dvh;
    background: #fff;
    z-index: 1050;
    display: flex;
    flex-direction: column;
    box-shadow: -4px 0 24px rgba(0,0,0,0.15);
}

.cart-header {
    background: #1a1a1a;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1.1rem 1.25rem;
    flex-shrink: 0;
}

.cart-title {
    font-family: 'Georgia', serif;
    font-size: 1.15rem;
    font-weight: 700;
}

.cart-body {
    flex: 1;
    overflow-y: auto;
    padding: 1rem 1.25rem;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.cart-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.cart-item-img {
    width: 56px;
    height: 56px;
    border-radius: 8px;
    object-fit: cover;
    flex-shrink: 0;
}

.cart-item-info {
    flex: 1;
    min-width: 0;
}

.cart-item-name {
    font-size: 0.9rem;
    font-weight: 600;
    color: #1a1a1a;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.cart-item-price {
    font-size: 0.82rem;
    color: #e63946;
    font-weight: 600;
    margin-top: 2px;
}

.cart-item-actions {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    flex-shrink: 0;
}

.qty-btn {
    width: 26px;
    height: 26px;
    border-radius: 6px;
    border: 1px solid #ddd;
    background: #f5f5f5;
    font-size: 1rem;
    line-height: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: background 0.15s;
}

.qty-btn:hover { background: #e63946; color: #fff; border-color: #e63946; }

.qty-val {
    font-size: 0.9rem;
    font-weight: 600;
    min-width: 20px;
    text-align: center;
}

.cart-empty {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #aaa;
    font-size: 0.95rem;
}

.cart-empty-icon { font-size: 2.5rem; margin-bottom: 0.5rem; }

.cart-footer {
    padding: 1rem 1.25rem 1.5rem;
    border-top: 1px solid #eee;
    flex-shrink: 0;
}

.cart-total {
    display: flex;
    justify-content: space-between;
    font-size: 0.95rem;
    font-weight: 600;
    color: #333;
}

.cart-total-price {
    color: #e63946;
    font-size: 1.1rem;
}

.cart-login-hint {
    font-size: 0.8rem;
    color: #888;
    text-align: center;
    margin: 0.75rem 0 0.5rem;
}

/* Thank you overlay */
.thankyou-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.6);
    z-index: 2000;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1rem;
}

.thankyou-card {
    background: #fff;
    border-radius: 20px;
    padding: 3rem 2.5rem;
    max-width: 420px;
    width: 100%;
    text-align: center;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
}

.thankyou-icon {
    font-size: 4rem;
    line-height: 1;
    margin-bottom: 1.25rem;
    animation: bounce 0.6s ease;
}

@keyframes bounce {
    0%   { transform: scale(0.5); opacity: 0; }
    70%  { transform: scale(1.15); }
    100% { transform: scale(1); opacity: 1; }
}

.thankyou-title {
    font-family: 'Georgia', serif;
    font-size: 1.6rem;
    font-weight: 700;
    color: #1a1a1a;
    margin-bottom: 0.75rem;
}

.thankyou-text {
    color: #666;
    font-size: 0.95rem;
    line-height: 1.7;
    margin-bottom: 2rem;
}

/* Transitions */
.cart-slide-enter-active,
.cart-slide-leave-active {
    transition: transform 0.3s ease;
}
.cart-slide-enter-from,
.cart-slide-leave-to {
    transform: translateX(100%);
}

.overlay-fade-enter-active,
.overlay-fade-leave-active {
    transition: opacity 0.3s ease;
}
.overlay-fade-enter-from,
.overlay-fade-leave-to {
    opacity: 0;
}

.closed-banner {
    background: #e63946;
    color: #fff;
    font-size: 0.9rem;
    font-weight: 600;
    padding: 0.65rem 0;
}

.verify-banner {
    background: #fef3c7;
    color: #92400e;
    font-size: 0.875rem;
    font-weight: 500;
    padding: 0.6rem 0;
    border-bottom: 1px solid #fde68a;
}

.verify-resend-btn {
    background: #92400e;
    color: #fff;
    border: none;
    border-radius: 6px;
    font-size: 0.78rem;
    font-weight: 700;
    padding: 0.3rem 0.85rem;
    cursor: pointer;
    white-space: nowrap;
}

.verify-resend-btn:disabled { opacity: 0.6; cursor: default; }
</style>