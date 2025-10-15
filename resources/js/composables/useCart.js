import { computed } from 'vue'
import { usePage, useForm } from '@inertiajs/vue3'

/**
 * @typedef {Object} Price
 * @property {number} current - Текущая сумма
 * @property {number} base - Базовая сумма без скидок
 * @property {number} discount - Сумма со скидкой
 * @property {number} discount_amount - Размер скидки
 * @property {number} discount_percent - Процент скидки
 * @property {boolean} has_discount - Есть ли скидка
 */

/**
 * @typedef {Object} Product
 * @property {number} id ID товара
 * @property {string} name Название товара
 * @property {Price} price Цена за 1шт товара
 * @property {Object} preview Варианты превью изображения
 * @property {string} status Статус
 * @property {string} created_at Дата создания
 * @property {string} available_stock_items_count Количество доступных позиций на складе
 */

/**
 * Реактивный объект корзины
 * @typedef {Object} Cart
 * @property {Price} amount - Итоговые суммы
 * @property {number} count - Количество позиций
 * @property {number} quantity - Общее количество товаров
 * @property {Object.<string, CartItem>} items - Товары в корзине
 */

/**
 * Элемент корзины
 * @typedef {Object} CartItem
 * @property {Price} amount Общая цена за товар с учетом количества
 * @property {Product} product Товар
 * @property {string} quantity Количество позиций в корзине
 */

/**
 * Композиция для работы с корзиной
 * @returns {{
 *   cart: import('vue').ComputedRef<Cart>,
 *   inCart: (id: number) => boolean,
 *   getCartItem: (id: number) => CartItem | null,
 *   getCartItemQuantity: (id: number) => number,
 *   addToCart: (id: number) => Promise<void>,
 *   clearCart: () => Promise<void>,
 *   removeFromCart: (id: number) => Promise<void>,
 *   decreaseQuantity: (id: number) => Promise<void>,
 *   form: import('@inertiajs/vue3').useForm<{}>
 * }}
 */
export function useCart() {
    const page = usePage()
    const form = useForm({})
    const cart = computed(() => page.props.cart)

    /**
     * Проверка наличия товара в корзине
     * @param id
     * @returns {boolean}
     */
    const inCart = (id) => {
        return Object.keys(cart.value.items).some(
            key => cart.value.items[key].product.id === id
        )
    }

    /**
     * Получает объект конкретного товара в корзине
     * @param id
     * @returns {*|null}
     */
    const getCartItem = (id) => {
        const foundKey = Object.keys(cart.value.items).find(
            key => cart.value.items[key].product.id === id
        )
        return foundKey ? cart.value.items[foundKey] : null
    }

    /**
     * Возвращает количество позиций конкретного товара в корзине
     * @param id
     * @returns {number|*|number}
     */
    const getCartItemQuantity = (id) => {
        const item = getCartItem(id)
        return item ? item.quantity : 0
    }

    /**
     * Очищает корзину
     */
    const clearCart = () => {
        return form.delete(route('cart.clear'))
    }

    /**
     * Добавляет товар с ID в корзину
     * Или увеличивает количество на 1
     * @param id
     */
    const addToCart = (id) => {
        return form.post(route('cart.items.store', id))
    }

    /**
     * Уменьшает количество товара по ID
     * Или удаляет, если меньше 1
     * @param id
     */
    const decreaseQuantity = (id) => {
        return form.post(route('cart.items.decrease', id))
    }

    /**
     * Полностью удаляет товар из корзины независимо от количества
     * @param id
     */
    const deleteFromCart = (id) => {
        return form.delete(route('cart.items.destroy', id))
    }

    return {
        cart,
        getCartItem,
        getCartItemQuantity,
        inCart,
        clearCart,
        addToCart,
        deleteFromCart,
        decreaseQuantity,
        form
    }
}