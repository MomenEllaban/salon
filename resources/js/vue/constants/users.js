const MODULE = 'users'

export const USER_LIST = ({role = 'user'}) => {return {path: `${MODULE}/user-list?role=${role}`, method: 'GET'}}
export const CUSTOMER_STORE = () => {return {path: `users/create-customer`, method: 'POST'}}
export const EMPLOYEE_GET = () => {return {path: `employees`, method: 'GET'}}
export const EMPLOYEE_STORE = () => {return {path: `employees`, method: 'POST'}}
export const INFORMATION_STORE = () => {return {path: `users/information`, method: 'POST'}}
export const GET_URL = () => {return {path: `my-info`, method: 'GET'}}
export const EMAIL_UNIQUE_CHECK=()=>{return {path: `customers/unique_email`, method: 'POST'}}

export const CHANGE_PASSWORD_URL = () => {return {path: `users/change-password/`, method: 'POST'}}

export const PROFILE_CHANGE_PASSWORD_URL = () => {return {path: `my-profile/change-password/`, method: 'POST'}}
