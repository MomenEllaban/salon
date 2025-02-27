export const MODULE = 'expenses-categories'

export const STORE_URL = () => {return {path: `${MODULE}`, method: 'POST'}}
export const EDIT_URL = (id) => {return {path: `${MODULE}/${id}/edit`, method: 'GET'}}
export const UPDATE_URL = (id) => {return {path: `${MODULE}/${id}`, method: 'PUT'}}
export const INDEX_LIST_URL = (id) => {return {path: `${MODULE}/index_list`, method: 'GET'}}
