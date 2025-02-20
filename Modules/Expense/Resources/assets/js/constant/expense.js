export const MODULE = 'expense'

export const STORE_URL = () => {return {path: `${MODULE}`, method: 'POST'}}
export const EDIT_URL = (id) => {return {path: `${MODULE}/${id}/edit`, method: 'GET'}}
export const UPDATE_URL = (id) => {return {path: `${MODULE}/${id}`, method: 'PUT'}}
export const EMPLOYEE_LIST = ({ role = '' }) => {
    return { path: `employees/employee_list?role=${role}`, method: 'GET' }
}

export const CATEGORY_LIST = () => {
    return { path: `expenses-categories/index_list`, method: 'GET' };
};

export const SUBCATEGORY_LIST = ({ parent_id} = {}) => {
    return { path: `expenses-categories/index_list?parent_id=${parent_id}`, method: 'GET' };
};

export const BRANCH_LIST = () => {
    return { path: `employees/index_list`, method: 'GET' }
  }