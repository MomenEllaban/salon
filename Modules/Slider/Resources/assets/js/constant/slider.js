export const MODULE = 'app-banners'

export const STORE_URL = () => {return {path: `${MODULE}`, method: 'POST'}}

export const EDIT_URL = (id) => {return {path: `${MODULE}/${id}/edit`, method: 'GET'}}

export const UPDATE_URL = (id) => {return {path: `${MODULE}/${id}`, method: 'POST'}}

export const TYPE_LIST = () => {return {path: `${MODULE}/index_list`, method: 'GET'}}

export const CATEGORY_LIST = ({type = ''}) => {return {path: `categories/index_list?type=${type}`, method: 'GET'}}

export const SERVICE_LIST = ({type = ''}) => {return {path: `service/index_list?type=${type}`, method: 'GET'}}

export const SLIDERS_UNIQUE_CHECK = () =>{return {path: `${MODULE}/uniqueSliders`, method: 'POST'}}
