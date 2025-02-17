export const MODULE = 'module'
export const EDIT_URL = (id) => {return {path: `${MODULE}/${id}/edit`, method: 'GET'}}
export const STORE_URL = () => {return {path: `${MODULE}`, method: 'POST'}}
export const UPDATE_URL = (id) => {return {path: `${MODULE}/${id}`, method: 'PATCH'}}
export const GET_PERMISSION_MODULE = ({type = ''}) => {return {path: `get_search_data?type=${type}`, method: 'GET'}}