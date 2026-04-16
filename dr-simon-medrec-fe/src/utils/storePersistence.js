// Storage persistence utility for managing store data during page refreshes or temporary storage needs.
const TEMP_STORAGE_KEY = 'temp_app_store_data'

export const saveToTempStorage = (storeData) => {
  try {
    const serialized = JSON.stringify(storeData)
    localStorage.setItem(TEMP_STORAGE_KEY, serialized)
    return true
  } catch (error) {
    console.error('Error saving to temp storage:', error)
    return false
  }
}

export const loadFromTempStorage = () => {
  try {
    const serialized = localStorage.getItem(TEMP_STORAGE_KEY)
    if (serialized === null) {
      return null
    }
    return JSON.parse(serialized)
  } catch (error) {
    console.error('Error loading from temp storage:', error)
    return null
  }
}

export const clearTempStorage = () => {
  try {
    localStorage.removeItem(TEMP_STORAGE_KEY)
    return true
  } catch (error) {
    console.error('Error clearing temp storage:', error)
    return false
  }
}
