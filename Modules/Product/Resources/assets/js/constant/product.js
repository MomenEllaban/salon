export const MODULE = 'products'
export const EDIT_URL = (id) => {return {path: `${MODULE}/${id}/edit`, method: 'GET'}}
export const STORE_URL = () => {return {path: `${MODULE}`, method: 'POST'}}
export const UPDATE_URL = (id) => {return {path: `${MODULE}/${id}`, method: 'POST'}}


// Gallery Images
export const GET_GALLERY_URL = (id) => {return {path: `${MODULE}/gallery-images/${id}`, method: 'GET'}}
export const POST_GALLERY_URL = (id) => {return {path: `${MODULE}/gallery-images/${id}`, method: 'POST'}}


// Additional Data
export const CATEGORY_LIST = ({brand_id}) => {return {path: `products-categories/index_list?brand_id=${brand_id}`, method: 'GET'}}
export const BRAND_LIST = () => {return {path: `brands/index_list`, method: 'GET'}}
export const UNITS_LIST = () => {return {path: `units/index_list`, method: 'GET'}}
export const TAGS_LIST = () => {return {path: `tags/index_list`, method: 'GET'}}
export const VARIATIONS_LIST = () => {return {path: `variations/index_list`, method: 'GET'}}
