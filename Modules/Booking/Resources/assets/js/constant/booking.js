export const MODULE = 'bookings'
// export const INDEX_URL = () => {return {path: `${MODULE}/index_list`, method: 'GET'}}
export const INDEX_URL = ({ user_type, page = 1, per_page = 8 }) => {
  return {
    path: `${MODULE}/index_list?page=${page}&per_page=${per_page}`,
    method: 'GET',
  };
};
export const EDIT_URL = (id) => {return {path: `${MODULE}/${id}/edit`, method: 'GET'}}
export const BOOKING_DETAIL = (id) => {return {path: `${MODULE}/${id}`, method: 'GET'}}
export const STORE_URL = () => {return {path: `${MODULE}`, method: 'POST'}}
export const UPDATE_URL = (id) => {return {path: `${MODULE}/${id}`, method: 'PUT'}}
export const CHECKOUT_URL = (id) => {return {path: `${MODULE}/${id}/checkout`, method: 'PUT'}}
export const PAYMENT_CREATE_URL = ({ booking_id ,userPackageserviceIds }) => {return {path: `${MODULE}/payment-create?booking_id=${booking_id}&userPackageserviceIds=${userPackageserviceIds}`, method: 'GET'}}
export const PAYMENT_PUT_URL = (booking_id) => {return {path: `${MODULE}/booking-payment/${booking_id}`, method: 'PUT'}}
export const UPDATE_STATUS = (id) => {return {path: `${MODULE}/update-status/${id}`, method: 'POST'}}
export const CUSTOMER_LIST = () => {return {path: `users/user-list?role=user`, method: 'GET'}}
export const EMPLOYEE_LIST = ({branch_id, show_in_calender = 1}) => {return {path: ` employees/employee_list?branch_id=${branch_id}&show_in_calender=${show_in_calender}`, method: 'GET'}}
export const SERVICE_LIST = ({id: employee_id, branch_id}) => {return {path: `${MODULE}/services-index_list?employee_id=${employee_id}&branch_id=${branch_id}`, method: 'GET'}}
export const SLOT_LIST = ({date, branch_id}) => {return { path: `${MODULE}/slots?date=${date}&branch_id=${branch_id}`, method: 'GET',}}
export const UPDATE_PAYMENT_DATA = (booking_transaction_id) => {return {path: `${MODULE}/booking-payment-update/${booking_transaction_id}`, method: 'PUT'}}
export const STRIPE_PAYMENT_DATA = () => {return {path: `${MODULE}/stripe-payment`, method: 'POST'}}
export const coupon_validate = () => {return {path: `promotions/coupon-validate`, method: 'PUT'}}

// Product Module
export const PRODUCT_LIST = () => {return {path: `products/index_list_with_varient`, method: 'GET'}}
// Package Module
export const PACKAGE_LIST = ({branch_id}) => { return {path: `package/index_list?branch_id=${branch_id}`, method: 'GET'}};
export const USER_PACKAGE_LIST = (id) => ({ path: `package/user_package_list/${id}`, method: 'GET' });
