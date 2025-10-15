/**
 * Генерирует уникальный ID с указанным префиксом
 * @param {string} [prefix='id'] - Префикс для ID
 * @returns {string} Уникальный идентификатор
 */
export function generateUniqueId(prefix = 'id') {
    return `${prefix}-${Math.random().toString(36).slice(2, 9)}`
}

/**
 * Преобразует объект {key: value} в массив объектов с настраиваемыми ключами
 * @param {Object} values - Исходный объект для преобразования
 * @param {string} [keyName='id'] - Название свойства для ключей исходного объекта
 * @param {string} [valueName='name'] - Название свойства для значений исходного объекта
 * @returns {Array} Массив объектов вида [{keyName: key, valueName: value}]
 */
export function normalizeKeyValuePairs(
    values,
    keyName = 'id',
    valueName = 'name'
) {
    return Object.entries(values).map(([key, value]) => ({
        [keyName]: key,
        [valueName]: value
    }))
}