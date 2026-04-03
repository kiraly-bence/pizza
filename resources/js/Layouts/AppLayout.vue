<template>
    <div>
        <nav class="navbar navbar-expand-lg sticky-top" id="mainNavbar">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center gap-2" href="/">
                    <div class="logo-icon">🍕</div>
                    <span class="brand-name">PizzaRex</span>
                </a>

                <div class="ms-auto d-flex align-items-center gap-3">
                    <template v-if="auth.user">
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
                                <li><a class="dropdown-item text-danger" href="#">Kijelentkezés</a></li>
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

        <div class="modal fade" id="authModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content auth-modal">
                    <div class="modal-header border-0 pb-0">
                        <div class="auth-tabs d-flex gap-0 w-100">
                            <button
                                class="auth-tab-btn flex-fill"
                                :class="{ active: authTab === 'login' }"
                                @click="authTab = 'login'"
                            >Bejelentkezés</button>
                            <button
                                class="auth-tab-btn flex-fill"
                                :class="{ active: authTab === 'register' }"
                                @click="authTab = 'register'"
                            >Regisztráció</button>
                        </div>
                        <button type="button" class="btn-close ms-3" data-bs-dismiss="modal" aria-label="Bezárás"></button>
                    </div>
                    <div class="modal-body pt-3">
                        <div v-if="authTab === 'login'">
                            <div class="mb-3">
                                <label class="form-label">E-mail cím</label>
                                <input type="email" class="form-control" placeholder="pelda@email.hu">
                            </div>
                            <div class="mb-4">
                                <label class="form-label">Jelszó</label>
                                <input type="password" class="form-control" placeholder="••••••••">
                            </div>
                            <button class="btn btn-primary w-100 submit-btn">Bejelentkezés</button>
                        </div>
                        <div v-else>
                            <div class="mb-3">
                                <label class="form-label">Teljes név</label>
                                <input type="text" class="form-control" placeholder="Kovács János">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">E-mail cím</label>
                                <input type="email" class="form-control" placeholder="pelda@email.hu">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Jelszó</label>
                                <input type="password" class="form-control" placeholder="••••••••">
                            </div>
                            <div class="mb-4">
                                <label class="form-label">Jelszó megerősítése</label>
                                <input type="password" class="form-control" placeholder="••••••••">
                            </div>
                            <button class="btn btn-primary w-100 submit-btn">Regisztráció</button>
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

const props = defineProps({
    auth: {
        type: Object,
        default: () => ({ user: null })
    }
})

const authTab = ref('login')

const initials = computed(() => {
    if (!props.auth.user) return ''
    return props.auth.user.name
        .split(' ')
        .map(n => n[0])
        .join('')
        .toUpperCase()
        .slice(0, 2)
})
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

.submit-btn {
    background: #e63946;
    border: none;
    border-radius: 8px;
    padding: 0.65rem;
    font-weight: 600;
    font-size: 0.95rem;
    transition: background 0.2s;
}

.submit-btn:hover {
    background: #c1121f;
}

.site-footer {
    background: #1a1a1a;
    color: rgba(255,255,255,0.5);
    font-size: 0.85rem;
    padding: 1.5rem 0;
    margin-top: 4rem;
}
</style>