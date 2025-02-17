export const MODULE = 'role'
export const STORE_URL = () => {return {path: `${MODULE}`, method: 'POST'}}
export const GET_ROLE_LIST = ({type = ''}) => {return {path: `get_search_data?type=${type}`, method: 'GET'}}
