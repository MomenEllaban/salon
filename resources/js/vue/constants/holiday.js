export const MODULE = 'holidays'
export const LISTING_URL = ({branch_id}) => {return {path: `holidays/index_list?branch_id=${branch_id}`, method: 'GET'}}
export const STORE_URL = () => {return {path: `${MODULE}`, method: 'POST'}}
