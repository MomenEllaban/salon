import { ref, watch, onUnmounted, onMounted } from "vue";

import { createRequest, createRequestWithFormData } from "@/helpers/utilities";

export const useModuleId = (callback, event_name = 'crud_change_id') => {
    // Module Current ID Logic
    const moduleId = ref(0);

    const updatecurrentId = (e) => {
      if(event_name == 'custom_form') {
        moduleId.value = e.detail.form_id;
      } else {
        moduleId.value = Number(e.detail.form_id);
      }
        callback()
    };

    document.addEventListener(event_name, updatecurrentId);

    onUnmounted(() => window.removeEventListener(event_name, updatecurrentId));

    return moduleId;
};

export const formatValue = (value) => {
    switch (value) {
        case null: return '';
        case true: return 1;
        case false: return 0;
        default: return value;
    }
}

export const useRequest = () => {
    const getRequest = async ({ url, id}) => {
        return createRequest(url(id));
    };

    // {
    //   method: 'GET',
    //   path: url,
    // };
    const listingRequest = async ({ url, data = {} }) => {
        return createRequest(url(data));
    };

    // {
    //   method: 'POST',
    //   path: url,
    // };
    const storeRequest = async ({ url, body, type }) => {
        if (type == "file") {
            let formData = new FormData();
            Object.keys(body).map((fieldName) => {
                formData.append(fieldName, formatValue(body[fieldName]));
            });
            return createRequestWithFormData(url(), {'Accept': 'application/json'}, formData);
        }
        return createRequest(url(), {}, body);
    };


    // {
    //   method: 'PUT|PATCH',
    //   path: url,
    // };
    const updateRequest = async ({ url, id, body, type }) => {
        if (type !== undefined && type == "file") {
            let formData = new FormData();
            Object.keys(body).map((fieldName) => {
                formData.append(fieldName, formatValue(body[fieldName]));
            });
            formData.append("_method", "PUT");
            return createRequestWithFormData(url(id), {'Accept': 'application/json'}, formData);
        } else {
            return createRequest(url(id), {}, body);
        }
    };

    // {
    //   method: 'DELETE',
    //   path: url,
    // };
    const deleteRequest = async ({ url, id}) => {
      return createRequest(url(id));
    };

    return {
        listingRequest,
        getRequest,
        storeRequest,
        updateRequest,
        deleteRequest
    };
};

export const useOnOffcanvasHide = (elemName, callback) => {
  onMounted(() => {
    const formOffcanvas = document.getElementById(elemName);
    formOffcanvas.addEventListener('hidden.bs.offcanvas', function (value) {
        if(typeof callback === 'function') {
            callback()
        }
    })
  })
}

export const useOnOffcanvasShow = (elemName, callback) => {
  onMounted(() => {
    const formOffcanvas = document.getElementById(elemName);
    formOffcanvas.addEventListener('shown.bs.offcanvas', function (value) {
        if(typeof callback === 'function') {
            callback()
        }
    })
  })
}

export const useOnModalHide = (elemName, callback) => {
  onMounted(() => {
    const modal = document.getElementById(elemName);
    modal.addEventListener('hide.bs.modal', function (value) {
        if(typeof callback === 'function') {
            callback()
        }
    })
  })
}
