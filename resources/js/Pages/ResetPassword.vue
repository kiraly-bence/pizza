<template>
    <div class="reset-page d-flex align-items-center justify-content-center min-vh-100">
        <div class="reset-card">
            <div class="text-center mb-4">
                <div class="brand-icon">🍕</div>
                <h1 class="brand-name">PizzaRex</h1>
                <h2 class="reset-title">Új jelszó beállítása</h2>
            </div>

            <div v-if="form.errors.email" class="alert alert-danger py-2 small mb-3">
                {{ form.errors.email }}
            </div>

            <div class="mb-3">
                <label class="form-label">E-mail cím</label>
                <input
                    v-model="form.email"
                    type="email"
                    class="form-control"
                    :class="{ 'is-invalid': form.errors.email }"
                    placeholder="pelda@email.hu"
                >
            </div>
            <div class="mb-3">
                <label class="form-label">Új jelszó</label>
                <input
                    v-model="form.password"
                    type="password"
                    class="form-control"
                    :class="{ 'is-invalid': form.errors.password }"
                    placeholder="••••••••"
                >
                <div v-if="form.errors.password" class="invalid-feedback">
                    {{ form.errors.password }}
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label">Jelszó megerősítése</label>
                <input
                    v-model="form.password_confirmation"
                    type="password"
                    class="form-control"
                    placeholder="••••••••"
                    @keyup.enter="submit"
                >
            </div>

            <button
                class="btn w-100 submit-btn"
                :disabled="form.processing"
                @click="submit"
            >
                {{ form.processing ? 'Mentés...' : 'Jelszó mentése' }}
            </button>
        </div>
    </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
    token: String,
    email: String,
})

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
})

const submit = () => {
    form.post('/reset-password')
}
</script>

<style scoped>
.reset-page {
    background: #f5f5f5;
}

.reset-card {
    background: #fff;
    border-radius: 14px;
    padding: 2.5rem;
    width: 100%;
    max-width: 420px;
    box-shadow: 0 4px 24px rgba(0,0,0,0.08);
}

.brand-icon {
    font-size: 2.5rem;
    line-height: 1;
    margin-bottom: 0.25rem;
}

.brand-name {
    font-family: 'Georgia', serif;
    font-size: 1.5rem;
    font-weight: 700;
    color: #1a1a1a;
    margin-bottom: 0.25rem;
}

.reset-title {
    font-size: 1rem;
    font-weight: 500;
    color: #666;
    margin-bottom: 0;
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
</style>
