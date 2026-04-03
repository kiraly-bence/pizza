<template>
    <div>
        <nav class="navbar navbar-expand-lg sticky-top" id="mainNavbar">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center gap-2" href="/">
                    <div class="logo-icon">🍕</div>
                    <span class="brand-name">PizzaRex</span>
                </a>

                <div class="ms-auto d-flex align-items-center gap-3">
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
                                <li><a class="dropdown-item" href="#">Rendeléseim</a></li>
                                <li><a class="dropdown-item" href="#">Profilom</a></li>
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

        <footer class="site-footer">
            <div class="container text-center">
                <p class="mb-0">© 2026 PizzaRex — Minden jog fenntartva</p>
            </div>
        </footer>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useForm, router, usePage } from '@inertiajs/vue3'

const props = defineProps({
    auth: {
        type: Object,
        default: () => ({ user: null })
    }
})

const page = usePage()
const authTab = ref('login')
const forgotSent = computed(() => page.props.flash?.forgot_status === 'sent')

const switchTab = (tab) => {
    authTab.value = tab
    loginForm.clearErrors()
    registerForm.clearErrors()
    forgotForm.clearErrors()
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

const submitLogin = () => {
    loginForm.post('/login')
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

// Forgot password
const forgotForm = useForm({ email: '' })

const submitForgot = () => {
    forgotForm.post('/forgot-password')
}

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
</style>