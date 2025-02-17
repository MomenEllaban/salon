export const STORE_URL = () => {return {path: `settings`, method: 'POST'}}
export const GET_URL = (data) => {return {path: `settings-data?fields=${data}`, method: 'GET'}}

export const GET_URL1 = () => {return {path: `settings-data`, method: 'GET'}}

export const CACHE_CLEAR = () => {return {path: `clear-cache`, method: 'GET'}}

export const GET_NOTIFICATION_URL = () => {return {path: `notifications-templates/index_list`, method: 'GET'}}

export const CHANNEL_UPDATE_URL = () => {return {path: `notifications-templates/channels-update`, method: 'POST'}}

export const TIME_ZONE_LIST = ({type = ''}) => {return {path: `get_search_data?type=${type}`, method: 'GET'}}

export const VERIFIED_EMAIL = (mailObject) => { return { path: `verify-email`, method: 'POST', request: mailObject  };};

export const CURRENCY_LIST = () => {return {path: `currencies/index_list`, method: 'GET'}}

// Menu Routes
export const MENU_LIST = ({type}) => {return {path: `menu?type=${type}`, method: 'GET'}}
export const MENU_STORE = () => {return {path: `menu`, method: 'POST'}}
export const MENU_UPDATE = (id) => {return {path: `menu/${id}`, method: 'PUT'}}
export const MENU_EDIT = (id) => {return {path: `menu/${id}/edit`, method: 'GET'}}

// menu list update
export const MENU_LIST_UPDATE = ({type}) => {return {path: `menu-sequance?type=${type}`, method: 'POST'}}

