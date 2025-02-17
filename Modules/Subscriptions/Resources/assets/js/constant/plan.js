export const MODULE = 'plans'
export const EDIT_URL = (id) => {return {path: `${MODULE}/${id}/edit`, method: 'GET'}}
export const STORE_URL = () => {return {path: `${MODULE}`, method: 'POST'}}
export const UPDATE_URL = (id) => {return {path: `${MODULE}/${id}`, method: 'POST'}}
export const LIMITATIONDATA_LIST = ({type = 'select',id = null}) => {return {path: `plans/index_list?type=${type}&parent_id=${id}`, method: 'GET'}}



