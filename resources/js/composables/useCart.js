import { ref, computed, watch } from 'vue'

const STORAGE_KEY = 'csepelpizza_cart'

// Module-level singleton — shared across all component instances
const items = ref(JSON.parse(localStorage.getItem(STORAGE_KEY) ?? '[]'))

watch(items, (val) => {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(val))
}, { deep: true })

export function useCart() {
    const cartCount = computed(() =>
        items.value.reduce((sum, i) => sum + i.quantity, 0)
    )

    const cartTotal = computed(() =>
        items.value.reduce((sum, i) => sum + i.price * i.quantity, 0)
    )

    const addItem = (product) => {
        const existing = items.value.find(i => i.id === product.id)
        if (existing) {
            existing.quantity++
        } else {
            items.value = [
                ...items.value,
                {
                    id: product.id,
                    name: product.name,
                    price: product.price,
                    image: product.image ?? null,
                    quantity: 1,
                },
            ]
        }
    }

    const removeItem = (productId) => {
        items.value = items.value.filter(i => i.id !== productId)
    }

    const updateQuantity = (productId, quantity) => {
        if (quantity <= 0) {
            removeItem(productId)
            return
        }
        const item = items.value.find(i => i.id === productId)
        if (item) item.quantity = quantity
    }

    const clearCart = () => {
        items.value = []
    }

    return { items, cartCount, cartTotal, addItem, removeItem, updateQuantity, clearCart }
}
