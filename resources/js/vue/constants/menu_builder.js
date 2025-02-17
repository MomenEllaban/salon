export const MODULE = 'menu'
export const EDIT_URL = (id) => {return {path: `${MODULE}/${id}/edit`, method: 'GET'}}
export const STORE_URL = () => {return {path: `${MODULE}`, method: 'POST'}}
export const UPDATE_URL = (id) => {return {path: `${MODULE}/${id}`, method: 'PUT'}}
export const DELETE_URL = (id) => {return {path: `${MODULE}/${id}`, method: 'DELETE'}}
export const MENU_ROUTE_LIST = (id) => {return { path: `${MODULE}/route_list`, method: 'GET' }}
export const MENU_TITLE_LIST = ({file_id, language_id}) => {return { path: `${MODULE}/title_list?language_id=${language_id}&file_id=${file_id}`, method: 'GET' }}