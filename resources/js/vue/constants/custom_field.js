export const MODULE = 'customfield'
export const LISTING_URL = () => {return {path: `customfield/index_list`, method: 'GET'}}
export const DATATABLE_URL = () => {return {path: `customfield/index_data`, method: 'GET'}}
export const EDIT_URL = (id) => {return {path: `${MODULE}/${id}/edit`, method: 'GET'}}
export const STORE_URL = () => {return {path: `${MODULE}`, method: 'POST'}}
export const UPDATE_URL = (id) => {return {path: `${MODULE}/${id}`, method: 'PUT'}}
export const DELETE_URL = (id) => {return {path: `${MODULE}/${id}`, method: 'DELETE'}}
