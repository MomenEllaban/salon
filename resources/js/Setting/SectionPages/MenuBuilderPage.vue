<template>
  <div>
    <CardTitle :title="$t('setting_sidebar.lbl_menu')" icon="fa fa-bars">
      <div class="d-flex align-items-center gap-2">
        <MenuBuilderForm :id="editId"></MenuBuilderForm>
        <button :disabled="IS_SUBMITED" class="btn btn-primary" name="submit" @click="submitMenuSequance">
          <template v-if="IS_SUBMITED">
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            Loading...
          </template>
          <template v-else> <i class="fa-solid fa-floppy-disk"></i> {{$t('dashboard.lbl_submit')}}</template>
        </button>
      </div>
    </CardTitle>
  </div>
  <BTabs v-model="activeTab" navClass="mb-4" class="tab-bottom-bordered">
    <BTab :title="$t('settings.horizontal')" active titleItemClass="nav-link">
      <nested-draggable :menus="menuitems" v-if="menuitems.length > 0" class="p-0" />
    </BTab>
    <BTab :title="$t('settings.vertical')" titleItemClass="nav-link">
      <nested-draggable :menus="hmenuitems" v-if="hmenuitems.length > 0" class="p-0" />
    </BTab>
  </BTabs>

  <button :disabled="IS_SUBMITED" class="btn btn-primary" name="submit" @click="submitMenuSequance">
    <template v-if="IS_SUBMITED">
      <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
      Loading...
    </template>
    <template v-else> <i class="fa-solid fa-floppy-disk"></i> {{$t('dashboard.lbl_submit')}}</template>
  </button>
</template>

<script setup>
import {computed, watch, onMounted, ref} from 'vue'
import CardTitle from '@/Setting/Components/CardTitle.vue'
import MenuBuilderForm from './Forms/MenuBuilderForm.vue'
import NestedDraggable from "../Components/Menu/NestedDraggable.vue";
import { MENU_LIST, MENU_LIST_UPDATE } from '@/vue/constants/setting'
import { useRequest } from '@/helpers/hooks/useCrudOpration'
import { useMenu } from "@/store/menu-state";

const store = useMenu()

const editId = computed(() => store.editId)

const menuitems = ref([])
const hmenuitems = ref([])
const activeTab = ref(0)

const IS_SUBMITED = ref(false)

const { listingRequest } = useRequest()
const getMenuList = () => {
  listingRequest({ url: MENU_LIST, data: { type: 'vertical' } }).then((res) => {
    if (res.status) {
      menuitems.value = res.data
    }
  })
}
const getHMenuList = () => {
  listingRequest({ url: MENU_LIST, data: { type: 'horizontal' } }).then((res) => {
    if (res.status) {
      hmenuitems.value = res.data
    }
  })
}

onMounted(() => {
  getMenuList()
  getHMenuList()
})

const submitMenuSequance  = () =>  {
  const { updateRequest } = useRequest()
  IS_SUBMITED.value = true
  updateRequest({url: MENU_LIST_UPDATE, id: {type: activeTab.value === 0 ? 'vertical' : 'horizontal' }, body: {menu: activeTab.value === 0 ? menuitems.value : hmenuitems.value } }).then((res) => {
    if(res.status) {
      window.successSnackbar(res.message)
      setTimeout(() => {
        IS_SUBMITED.value = false
        window.location.reload()
      }, 300);
    }
  })
}
</script>
