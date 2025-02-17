export const MODULE = 'languages'
export const LANGUAGE_LIST = () => {return {path: `${MODULE}/index_list`, method: 'GET'}}
export const STORE_URL = () => {return {path: `${MODULE}`, method: 'POST'}}
export const LISTING_URL = ({language_id}) => {return {path: `${MODULE}/array_list?language_id=${language_id}`, method: 'GET'}}
export const FILE_DATA_URL = ({file_id, language_id= null}) => {
    let url = `${MODULE}/get_file_data?file_id=${file_id}`;
    if (language_id) {
      url += `&language_id=${language_id}`;
    }
    return { path: url, method: 'GET' };
  };
