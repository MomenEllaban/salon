export const MODULE = 'promotions'
export const EDIT_URL = (id) => {return {path: `${MODULE}/${id}/edit`, method: 'GET'}}
export const STORE_URL = () => {return {path: `${MODULE}`, method: 'POST'}}
export const UPDATE_URL = (id) => {return {path: `${MODULE}/${id}`, method: 'POST'}}
export const TIME_ZONE_LIST = ({type = ''}) => {return {path: `get_search_data?type=${type}`, method: 'GET'}}
export const UNIQUE_CHECK= () => {return {path: `${MODULE}/unique_coupon`, method: 'POST'}}
