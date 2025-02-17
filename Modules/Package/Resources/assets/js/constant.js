export const MODULE = 'package'
export const EDIT_URL = (id) => {return {path: `${MODULE}/${id}/edit`, method: 'GET'}}
export const STORE_URL = () => {return {path: `${MODULE}`, method: 'POST'}}
export const UPDATE_URL = (id) => {return {path: `${MODULE}/${id}`, method: 'POST'}}
export const BRANCH_LIST = () => {return {path: `employees/index_list`, method: 'GET'}}
export const EMPLOYEE_LIST = ({branch_id}) => {return {path: ` employees/employee_list?branch_id=${branch_id}`, method: 'GET'}}
export const SERVICE_LIST = ({ branch_id}) => {return {path: `${MODULE}/services-index_list?branch_id=${branch_id}`, method: 'GET'}}
export const CLIENT_PACKAGE=(id)=>{return{path:`${MODULE}/${id}/client_package`, method: 'GET'}}
