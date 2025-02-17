export const MODULE = 'customers'
export const EDIT_URL = (id) => {return {path: `${MODULE}/${id}/edit`, method: 'GET'}}
export const STORE_URL = () => {return {path: `${MODULE}`, method: 'POST'}}
export const UPDATE_URL = (id) => {return {path: `${MODULE}/${id}`, method: 'POST'}}
export const CHANGE_PASSWORD_URL = () => {return {path: `${MODULE}/change-password/`, method: 'POST'}}
export const EMAIL_UNIQUE_CHECK=()=>{return {path: `${MODULE}/unique_email`, method: 'POST'}}
