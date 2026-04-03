<template>
    <AppLayout :auth="auth">
        <section class="profile-section">
            <div class="container">
                <h1 class="profile-heading">Profilom</h1>

                <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="profile-card">
                            <h2 class="card-section-title">Szállítási cím</h2>

                            <div
                                v-if="flash.address_saved"
                                class="alert alert-success py-2 small mb-3"
                            >
                                A cím sikeresen mentve!
                            </div>

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
                                <div class="col-12 mt-1">
                                    <button
                                        class="btn save-btn"
                                        :disabled="form.processing"
                                        @click="save"
                                    >
                                        {{ form.processing ? 'Mentés...' : 'Cím mentése' }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </AppLayout>
</template>

<script setup>
import { useForm, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

defineOptions({ layout: null })

const props = defineProps({
    auth:         { type: Object, default: () => ({ user: null }) },
    savedAddress: { type: Object, default: () => ({}) },
})

const page = usePage()
const flash = page.props.flash ?? {}

const form = useForm({
    zip:    props.savedAddress.zip    ?? '',
    city:   props.savedAddress.city   ?? '',
    street: props.savedAddress.street ?? '',
    note:   props.savedAddress.note   ?? '',
})

const save = () => form.post('/profil/cim')
</script>

<style scoped>
.profile-section {
    padding: 3rem 0 5rem;
    background: #f7f7f7;
    min-height: 100vh;
}

.profile-heading {
    font-family: 'Georgia', serif;
    font-size: 2rem;
    font-weight: 700;
    color: #1a1a1a;
    margin-bottom: 2rem;
}

.profile-card {
    background: #fff;
    border-radius: 14px;
    padding: 1.75rem;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
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

.save-btn {
    background: #e63946;
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 0.55rem 1.5rem;
    font-weight: 600;
    font-size: 0.9rem;
    transition: background 0.2s;
}

.save-btn:hover:not(:disabled) { background: #c1121f; color: #fff; }
.save-btn:disabled { opacity: 0.7; color: #fff; }
</style>
