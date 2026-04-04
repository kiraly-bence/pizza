export function useFormatting() {
    const formatPrice = (price) => Number(price).toLocaleString('hu-HU')

    return { formatPrice }
}
