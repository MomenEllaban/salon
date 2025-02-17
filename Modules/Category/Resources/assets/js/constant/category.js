export const MODULE = 'categories'
export const INDEX_LIST_URL = (id) => {return {path: `${MODULE}/index_list`, method: 'GET'}}
export const EDIT_URL = (id) => {return {path: `${MODULE}/${id}/edit`, method: 'GET'}}
export const STORE_URL = () => {return {path: `${MODULE}`, method: 'POST'}}
export const UPDATE_URL = (id) => {return {path: `${MODULE}/${id}`, method: 'POST'}}
