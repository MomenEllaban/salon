export const MODULE = 'city'
export const EDIT_URL = (id) => {
  return { path: `${MODULE}/${id}/edit`, method: 'GET' }
}
export const STORE_URL = () => {
  return { path: `${MODULE}`, method: 'POST' }
}
export const UPDATE_URL = (id) => {
  return { path: `${MODULE}/${id}`, method: 'PUT' }
}
export const STATE_URL = (id) => {
  return { path: `state/index_list?country_id=${id}`, method: 'GET' }
}
export const COUNTRY_URL = () => {
  return { path: `country/index_list`, method: 'GET' }
}
