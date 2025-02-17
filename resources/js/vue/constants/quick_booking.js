export const SERVICE_LIST = ({branch_id, employee_id}) => {return {path: `api/quick-booking/services-list?branch_id=${branch_id}&employee_id=${employee_id}`, method: 'GET'}}
export const BRANCH_LIST = ({employee_id, service_id, start_date_time}) => {return {path: `api/quick-booking/branch-list?employee_id=${employee_id}&service_id=${service_id}&start_date_time=${start_date_time}`, method: 'GET'}}
export const EMPLOYEE_LIST = ({branch_id,service_id, start_date_time}) => {return {path: `api/quick-booking/employee-list?branch_id=${branch_id}&service_id=${service_id}&start_date_time=${start_date_time}`, method: 'GET'}}
export const SLOT_TIME_LIST = ({branch_id, date, service_id, employee_id,}) => {return {path: `api/quick-booking/slot-time-list?branch_id=${branch_id}&date=${date}&employee_id=${employee_id}&service_id=${service_id}`, method: 'GET'}}
export const STORE_URL = () => {return {path: `api/quick-booking/store`, method: 'POST'}}
export const HOLIDAY_SLOT_LIST = ({branch_id, date, service_id, employee_id}) => {return {path: `api/quick-booking/slot-date-list?branch_id=${branch_id}&date=${date}&employee_id=${employee_id}&service_id=${service_id}`, method: 'GET'}}

