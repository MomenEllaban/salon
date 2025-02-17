export const MODULE = 'locations'
export const EDIT_URL = (id) => {return {path: `${MODULE}/${id}/edit`, method: 'GET'}}
export const STORE_URL = () => {return {path: `${MODULE}`, method: 'POST'}}
export const UPDATE_URL = (id) => {return {path: `${MODULE}/${id}`, method: 'PUT'}}

export const GET_PRODUCT_VARIATIONS_LIST = ({location_id, product_id}) => {return {path: `get-variation-stocks?location_id=${location_id}&product_id=${product_id}`, method: 'GET'}}
export const STOCK_STORE_URL = () => {return {path: `stock-add`, method: 'POST'}}
export const COUNTRY_LIST = () => {return {path: `country/index_list`, method: 'GET'}}
export const STATE_LIST = ({country_id}) => {return {path: `state/index_list?country_id=${country_id}`, method: 'GET'}}
export const CITY_LIST = ({state_id}) => {return {path: `city/index_list?state_id=${state_id}`, method: 'GET'}}

export const BRAND_LIST = () => {return {path: `brands/index_list`, method: 'GET'}}
export const CATEGORY_LIST = ({brand_id}) => {return {path: `products-categories/index_list?brand_id=${brand_id}`, method: 'GET'}}
export const PRODUCT_LIST = ({category_id}) => {return {path: `products/index_list?category_id=${category_id}`, method: 'GET'}}
