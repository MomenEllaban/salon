<template>
  <button v-if="hasPermissions('add_menu_builder')" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#exampleModal" aria-controls="form-modal"><i class="fa fa-plus"></i> {{ $t('messages.new') }}</button>
  <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen-md-down">
      <form @submit.prevent="formSubmit">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="offcanvas-title">
              <span>{{ $t('menu_builder.lbl_add_menu') }}</span>
            </h5>
            <div>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-12">
                <input type="hidden" name="menu_type" v-model="menu_type" />
                <div class="form-group">
                  <label for="">{{ $t('menu_builder.lbl_title') }} <span class="text-danger">*</span></label>
                  <Multiselect v-model="title" :value="title" :placeholder="$t('menu_builder.lbl_title')"
                    v-bind="singleSelectCreateOption" :options="titleLang.options" class="form-group"></Multiselect>
                    <span class="mt-3"><strong>Note: </strong>Type and press enter to create new title</span>
                  <span v-if="errorMessages['title']">
                    <ul class="text-danger">
                      <li v-for="err in errorMessages['title']" :key="err">{{ err }}</li>
                    </ul>
                  </span>
                  <span class="text-danger">{{ errors.title }}</span>
                </div>
                <InputField class="col-md-12" :is-required="false" :label="$t('menu_builder.lbl_icon')" placeholder=""
                  v-model="start_icon" :error-message="errors.start_icon" @input="updateIcon">
                  <span class="mt-3"><strong>Note: </strong>You can add Icon from library. <a href="https://fontawesome.com/search" target="_blank">Click here</a></span>
                </InputField>
                <div class="form-group">
                  <label class="form-label" for="menu_item_type">{{ $t('menu_builder.lbl_menu_type') }}<span
                      class="text-danger">*</span></label>
                  <Multiselect v-model="menu_item_type" :value="menu_item_type" v-bind="type_menu" id="menu_item_type"
                    autocomplete="off"></Multiselect>
                  <span v-if="errorMessages['menu_item_type']">
                    <ul class="text-danger">
                      <li v-for="err in errorMessages['menu_item_type']" :key="err">{{ err }}</li>
                    </ul>
                  </span>
                  <span class="text-danger">{{ errors.menu_item_type }}</span>
                </div>
              </div>
              <div class="form-group" v-if="isLinkMenuType">
                <div class="d-flex justify-content-between align-items-center">
                  <label class="form-label" for="is_route">{{ $t('menu_builder.lbl_is_route') }}</label>
                  <div class="form-check form-switch">
                    <input class="form-check-input" :value="is_route" :true-value="1" :false-value="0" :checked="is_route"
                      name="is_route" id="is_route" type="checkbox" v-model="is_route" />
                  </div>
                </div>
              </div>

              <div class="form-group" v-if="is_route && isLinkMenuType">
                <label for="menu-route" class="form-label">Route List <span class="text-danger">*</span></label>
                <Multiselect v-model="route" placeholder="Select Route" v-bind="singleSelectOption"
                  :options="routes.options" id="menu-route" class="form-group"></Multiselect>
                <span v-if="errorMessages['route']">
                  <ul class="text-danger">
                    <li v-for="err in errorMessages['route']" :key="err">{{ err }}</li>
                  </ul>
                </span>
                <span class="text-danger">{{ errors.route }}</span>
              </div>
              <div class="form-group" v-else-if="isLinkMenuType">
                <InputField class="col-md-12" :is-required="true" :label="$t('menu_builder.lbl_url')" placeholder=""
                  v-model="url" :error-message="errors.url"></InputField>
              </div>

              <div class="form-group" v-if="isLinkMenuType">
                <label class="form-label" for="target_type">{{ $t('menu_builder.lbl_target') }}<span
                    class="text-danger">*</span></label>
                <Multiselect v-model="target_type" :value="target_type" :options="type_target.options"
                  v-bind="singleSelectOption" id="target_type" autocomplete="off"></Multiselect>
                <span v-if="errorMessages['target_type']">
                  <ul class="text-danger">
                    <li v-for="err in errorMessages['target_type']" :key="err">{{ err }}</li>
                  </ul>
                </span>
                <span class="text-danger">{{ errors.target_type }}</span>
              </div>

              <div class="form-group">
                <div class="d-flex justify-content-between align-items-center">
                  <label class="form-label" for="status">{{ $t('menu_builder.lbl_status') }}</label>
                  <div class="form-check form-switch">
                    <input class="form-check-input" :true-value="1" :false-value="0" :value="status" :checked="status"
                      name="status" id="status" type="checkbox" v-model="status" />
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer justify-content-md-between flex-column flex-md-row">
            <div>
              <i :class="start_icon"></i> <template v-if="title">{{ $t(title) }}</template>
            </div>
            <div class="d-grid d-md-flex gap-3">
              <button class="btn btn-primary d-block">
                <i class="fa-solid fa-floppy-disk"></i>
                {{$t('messages.save')}}
              </button>
              <button class="btn btn-outline-primary d-block" type="button" data-bs-dismiss="modal">
                <i class="fa-solid fa-angles-left"></i>
                {{$t('messages.close')}}
              </button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>
<script setup>
import { ref, onMounted, watch, computed } from 'vue'
import { EDIT_URL, STORE_URL, UPDATE_URL, MENU_ROUTE_LIST, MENU_TITLE_LIST } from '@/vue/constants/menu_builder'
import { useField, useForm } from 'vee-validate'
import { useRequest, useOnModalHide } from '@/helpers/hooks/useCrudOpration'
import * as yup from 'yup'
import InputField from '@/vue/components/form-elements/InputField.vue'
import { useSelect } from '@/helpers/hooks/useSelect'
import { useMenu } from "@/store/menu-state";

const store = useMenu()

useOnModalHide('exampleModal', () => {
  setFormData(defaultData())
  store.setEditCurrentMenuId(0)
})

const hasPermissions = (name) => {
  return window.auth_permissions.includes(name)
}

const { getRequest, storeRequest, updateRequest, listingRequest } = useRequest()

const isLinkMenuType = computed(() => menu_item_type.value === 'link')

const singleSelectOption = ref({
  closeOnSelect: true,
  searchable: true,
  canDeselect: false
})

const singleSelectCreateOption = ref({
  closeOnSelect: true,
  searchable: true,
  createOption: true
})

const routes = ref({ options: [], list: [] })

const titleLang = ref({ options: [], list: [] })

const type_menu = ref({
  searchable: true,
  options: [
    { label: 'Static Label', value: 'static' },
    { label: 'Main Menu', value: 'parent' },
    { label: 'Link/Route', value: 'link' }
  ],
  closeOnSelect: true
})

const type_target = ref({
  searchable: true,
  options: [
    { label: 'New', value: '_blank' },
    { label: 'Existing', value: '_self' }
  ],
  closeOnSelect: true
})
const updateIcon = (value) => {
  const doc = new DOMParser().parseFromString(value.target.value, "text/html");
  if (doc.body.firstChild?.classList?.length > 0) {
    start_icon.value = doc.body.firstChild.classList.value
  } else {
    start_icon.value = value.target.value
  }
}
onMounted(() => {
  useSelect({ url: MENU_ROUTE_LIST }, { value: 'route', label: 'title' }).then((data) => (routes.value = data))

  listingRequest({ url: MENU_TITLE_LIST, data: { file_id: 'sidebar', language_id: 'en' } }).then((res) => {
    titleLang.value.list = res
    titleLang.value.options = res.map((item) => { return { value: item['key'], label: item['value'] } })
  })

  setFormData(defaultData())
})

const emit = defineEmits(['onSubmit'])

// props
const props = defineProps({
  id: { type: Number, default: 0 }
})

const IS_SUBMITED = ref(false)
const currentId = ref(props.id)
// Edit Form Or Create Form
watch(
  () => props.id,
  (value) => {
    currentId.value = value
    if (value > 0) {
      getRequest({ url: EDIT_URL, id: value }).then((res) => {
        if (res.status && res.data) {
          setFormData(res.data)
        }
      })
    } else {
      setFormData(defaultData())
    }
  }
)

// Default FORM DATA
const defaultData = () => {
  errorMessages.value = {}
  return {
    title: '',
    start_icon: null,
    menu_type: 'vertical',
    menu_item_type: 'link',
    is_route: 0,
    url: '',
    target_type: '_self',
    status: 1,
    route: ''
  }
}

//  Reset Form
const setFormData = (data) => {
  resetForm({
    values: {
      title: data.title,
      start_icon: data.start_icon,
      menu_type: data.menu_type,
      menu_item_type: data.menu_item_type,
      is_route: data.is_route,
      url: data.url || '',
      target_type: data.target_type,
      status: data.status,
      route: data.route || ''
    }
  })
}

// Reload Datatable, SnackBar Message, Alert, Offcanvas Close
const reset_datatable_close_offcanvas = (res) => {
  if (res.status) {
    window.successSnackbar(res.message)
    bootstrap.Modal.getInstance('#exampleModal').hide()
    setFormData(defaultData())
    setTimeout(() => {
      IS_SUBMITED.value = false
      window.location.reload()
    }, 300)
  } else {
    window.errorSnackbar(res.message)
    errorMessages.value = res.all_message
  }
  store.setEditCurrentMenuId(0)
}

// const numberRegex = /^\d+$/;
// Validations
const validationSchema = yup.object({
  title: yup.string().required('Title is a required field'),
  menu_item_type: yup.string().required('Menu Type is a required field'),
  target_type: yup.string().required('Menu Type is a required field'),
  url: yup.string().test('required', 'URL is a required field', function (value) {
    if (menu_item_type.value == 'link' && is_route.value === 0 && !value) {
      return false;
    }
    return true
  }).url(),
  route: yup.string().test('required', 'Route is a required field', function (value) {
    if (menu_item_type.value == 'link' && is_route.value === 1 && !value) {
      return false;
    }
    return true
  }),
})

const { handleSubmit, errors, resetForm } = useForm({
  validationSchema
})
const { value: title } = useField('title')
const { value: start_icon } = useField('start_icon')
const { value: menu_type } = useField('menu_type')
const { value: menu_item_type } = useField('menu_item_type')
const { value: is_route } = useField('is_route')
const { value: url } = useField('url')
const { value: target_type } = useField('target_type')
const { value: status } = useField('status')
const { value: route } = useField('route')
const errorMessages = ref({})

// Form Submit
const formSubmit = handleSubmit((values) => {
  IS_SUBMITED.value = true
  if (props.id > 0) {
    updateRequest({ url: UPDATE_URL, id: props.id, body: values }).then((res) => reset_datatable_close_offcanvas(res))
  } else {
    storeRequest({ url: STORE_URL, body: values }).then((res) => reset_datatable_close_offcanvas(res))
  }
})
</script>
