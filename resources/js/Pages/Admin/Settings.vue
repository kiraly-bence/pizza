<template>
    <AdminLayout :auth="auth" title="Beállítások">

        <!-- Rendelés szüneteltetése -->
        <div class="admin-card settings-card mb-4" style="max-width: 600px;">
            <p class="section-title">Rendelések fogadása</p>

            <div class="pause-row">
                <div>
                    <div class="pause-label">{{ paused ? 'Szüneteltetve' : 'Aktív' }}</div>
                    <div class="pause-sub">A nyitvatartástól függetlenül azonnal hatályba lép.</div>
                </div>
                <button
                    class="pause-btn"
                    :class="paused ? 'pause-btn--resume' : 'pause-btn--pause'"
                    :disabled="toggling"
                    @click="togglePause"
                >
                    {{ paused ? 'Folytatás' : 'Szüneteltetés' }}
                </button>
            </div>
        </div>

        <!-- Nyitvatartás -->
        <div class="admin-card settings-card mb-4" style="max-width: 600px;">
            <p class="section-title">Nyitvatartás</p>

            <div v-if="hoursSaved" class="alert-success mb-3">Nyitvatartás sikeresen mentve.</div>

            <table class="hours-table">
                <thead>
                    <tr>
                        <th>Nap</th>
                        <th>Nyitás</th>
                        <th>Zárás</th>
                        <th>Zárva</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="d in dayOrder" :key="d" :class="{ 'row-closed': hoursForm[d].closed }">
                        <td class="day-name">{{ dayNames[d] }}</td>
                        <td>
                            <input
                                type="time"
                                class="form-control form-control-sm"
                                v-model="hoursForm[d].open"
                                :disabled="hoursForm[d].closed"
                            >
                        </td>
                        <td>
                            <input
                                type="time"
                                class="form-control form-control-sm"
                                v-model="hoursForm[d].close"
                                :disabled="hoursForm[d].closed"
                            >
                        </td>
                        <td class="text-center">
                            <input type="checkbox" v-model="hoursForm[d].closed">
                        </td>
                    </tr>
                </tbody>
            </table>

            <button class="btn-primary mt-3" :disabled="savingHours" @click="saveHours">
                {{ savingHours ? 'Mentés...' : 'Mentés' }}
            </button>
        </div>

        <!-- Díjak -->
        <div class="admin-card settings-card" style="max-width: 480px;">
            <p class="section-title">Díjak</p>

            <div v-if="feesSaved" class="alert-success mb-4">Beállítások sikeresen mentve.</div>

            <div class="mb-3">
                <label class="form-label">Kiszállítási díj (Ft)</label>
                <input
                    v-model.number="feesForm.delivery_fee"
                    type="number"
                    min="0"
                    class="form-control"
                    :class="{ 'is-invalid': feesErrors.delivery_fee }"
                >
                <div class="invalid-feedback">{{ feesErrors.delivery_fee }}</div>
            </div>

            <div class="mb-4">
                <label class="form-label">Szolgáltatási díj (Ft)</label>
                <input
                    v-model.number="feesForm.service_fee"
                    type="number"
                    min="0"
                    class="form-control"
                    :class="{ 'is-invalid': feesErrors.service_fee }"
                >
                <div class="invalid-feedback">{{ feesErrors.service_fee }}</div>
            </div>

            <button class="btn-primary" :disabled="savingFees" @click="saveFees">
                {{ savingFees ? 'Mentés...' : 'Mentés' }}
            </button>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    auth:         { type: Object, required: true },
    fees:         { type: Object, required: true },
    openingHours: { type: Object, required: true },
    paused:       { type: Boolean, default: false },
})

// Display order: Mon(1) … Sat(6) Sun(0)
const dayOrder = [1, 2, 3, 4, 5, 6, 0]
const dayNames = { 0: 'Vasárnap', 1: 'Hétfő', 2: 'Kedd', 3: 'Szerda', 4: 'Csütörtök', 5: 'Péntek', 6: 'Szombat' }

// --- Pause ---
const toggling = ref(false)
const togglePause = () => {
    toggling.value = true
    router.post('/admin/settings/pause', {}, {
        preserveScroll: true,
        onFinish: () => { toggling.value = false },
    })
}

// --- Opening hours ---
const hoursForm = ref(
    Object.fromEntries([0,1,2,3,4,5,6].map(i => [i, { ...props.openingHours[i] }]))
)
const savingHours = ref(false)
const hoursSaved  = ref(false)

const saveHours = () => {
    savingHours.value = true
    hoursSaved.value  = false
    const hours = { ...hoursForm.value }
    router.post('/admin/settings/hours', { hours }, {
        preserveScroll: true,
        onSuccess: () => { savingHours.value = false; hoursSaved.value = true },
        onError:   ()  => { savingHours.value = false },
    })
}

// --- Fees ---
const feesForm  = ref({ delivery_fee: props.fees.delivery_fee, service_fee: props.fees.service_fee })
const savingFees = ref(false)
const feesErrors = ref({})
const feesSaved  = ref(false)

const saveFees = () => {
    savingFees.value = true
    feesSaved.value  = false
    router.post('/admin/settings', feesForm.value, {
        preserveScroll: true,
        onSuccess: () => { savingFees.value = false; feesSaved.value = true; feesErrors.value = {} },
        onError:   (e) => { feesErrors.value = e; savingFees.value = false },
    })
}
</script>

<style scoped>
@import './admin-table.css';

.settings-card { padding: 1.75rem; }

.section-title {
    font-size: 0.8rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #888;
    margin-bottom: 1.25rem;
}

.alert-success {
    background: #d1fae5;
    color: #065f46;
    border-radius: 8px;
    padding: 0.6rem 1rem;
    font-size: 0.875rem;
    font-weight: 500;
}

/* Pause */
.pause-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
}

.pause-label {
    font-weight: 700;
    font-size: 0.95rem;
    color: #1a1a1a;
}

.pause-sub {
    font-size: 0.8rem;
    color: #888;
    margin-top: 2px;
}

.pause-btn {
    border: none;
    border-radius: 8px;
    font-size: 0.85rem;
    font-weight: 700;
    padding: 0.45rem 1.2rem;
    cursor: pointer;
    transition: background 0.15s;
    white-space: nowrap;
}

.pause-btn--pause  { background: #fef2f2; color: #b91c1c; }
.pause-btn--resume { background: #d1fae5; color: #065f46; }
.pause-btn:disabled { opacity: 0.6; cursor: default; }

/* Hours table */
.hours-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.875rem;
}

.hours-table th {
    text-align: left;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    color: #888;
    padding: 0 0.5rem 0.6rem;
}

.hours-table th:last-child { text-align: center; }

.hours-table td {
    padding: 0.35rem 0.5rem;
    vertical-align: middle;
}

.hours-table input[type="time"] { max-width: 120px; }

.day-name {
    font-weight: 600;
    min-width: 90px;
}

.row-closed td { opacity: 0.45; }
.row-closed .day-name { opacity: 1; }
</style>
