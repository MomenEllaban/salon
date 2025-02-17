<template>
    <draggable
      class="dragArea list-group-flush w-100"
      tag="ul"
      v-bind="dragOptions"
      :list="menus"
      :group="{ name: 'g1' }"
      item-key="name"
      handle=".handle"
    >
      <template #item="{ element }">
        <li class="list-group-item default-flex-gap w-100 align-items-baseline">
          <i class="fa-solid fa-up-right-from-square" data-bs-toggle="tooltip" title="Opens in new window" v-if="element.target_type == '_blank'"></i>
          <i class="fa-solid fa-link" data-bs-toggle="tooltip" title="Opens in same tab" v-else-if="element.menu_item_type == 'link'"></i>
          <i class="fa-solid fa-ban" data-bs-toggle="tooltip" title="Only for view purpose" v-if="element.menu_item_type == 'static'"></i>
          <i class="fa-regular fa-rectangle-list" data-bs-toggle="tooltip" title="Parent menu Or Group of menus links" v-if="element.menu_item_type == 'parent'"></i>
          <div :class="`btn btn-border flex-column mb-3 w-100 ${element.menu_item_type == 'static' ? 'static-menu-button' : ''}`">
            <div :class="`default-flex-gap justify-content-between w-100 ${element.menu_item_type == 'parent' ? 'mb-3' : ''}`">
              <div class="default-flex-gap gap-4">
                <i class="fa-solid fa-up-down-left-right handle"></i>
                <span :class="`default-flex-gap`"><i :class="element.start_icon || 'fa-solid fa-circle'" v-if="element.menu_item_type !== 'static'"></i>{{ $t(element.title) }}</span>
              </div>
              <div class="default-flex-gap">
                <button class="btn btn-icon btn-secondary btn-sm rounded" v-if="hasPermissions('edit_menu_builder')" data-bs-toggle="modal" data-bs-target="#exampleModal" :title="$t('messages.edit')" aria-controls="form-modal" @click="store.setEditCurrentMenuId(element.id)"><i class="fa-solid fa-pen-clip"></i></button>
                <button class="btn btn-icon btn-danger btn-sm rounded" v-if="hasPermissions('delete_menu_builder')" :title="$t('messages.delete')" @click="store.deleteMenu(element.id, 'Are you sure you want to delete it?')"><i class="fa-solid fa-trash"></i></button>
              </div>
            </div>
            <nested-draggable :menus="element.children" v-if="element.menu_item_type == 'parent'" />
          </div>
          <i :class="`fa-solid fa-circle active-inactive-icon ${element.status == '0' ? 'text-danger' : 'text-success'}`"></i>
        </li>
      </template>
    </draggable>
  </template>
  <script>

  import draggable from "vuedraggable";
  import { useMenu } from "@/store/menu-state";
  export default {
    props: {
      menus: {
        required: true,
        type: Array
      }
    },
    setup() {
      const store = useMenu()

      const hasPermissions = (name) => {
          return window.auth_permissions.includes(name)
        }
      return {
        store,
        hasPermissions
      }
    },
    components: {
      draggable
    },
    name: "nested-draggable",
    mounted() {
      const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
      const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
    },
    computed: {
      dragOptions() {
        return {
          animation: 200,
          ghostClass: "ghost"
        };
      }
    },
    methods: {
    }
  };
  </script>
<style scoped>
/* Menu Items Css */
.active-inactive-icon {
  font-size: .75rem;
}
.list-group-item {
  margin-bottom: .5rem;
  margin-top: .5rem;
}

.list-group-item:last-child {
  margin-bottom: 0;
}

.btn-border {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.btn-border:focus,
.btn-border:active {
  background-color: transparent !important;
  color: inherit !important;
}

.default-flex-gap {
  display: flex;
  align-items: center;
  gap: .5rem;
}


/* Menu Items Css */
.list-group-item {
  margin-bottom: .5rem;
  margin-top: .5rem;
}

.list-group-item:last-child {
  margin-bottom: 0;
}

ul ul {
  border: 1px dashed var(--bs-body-color);
  padding: 10px 20px;
}

.static-menu-button {
  background-color: rgba(0, 0, 0, 0.11);
}
.dark .static-menu-button {
  background-color: rgba(255, 255, 255, 0.288);
}
.btn-border{
  color: var(--bs-secondary-color) !important;
}
.btn.btn-border:hover,
.btn.btn-border:active,
.btn.btn-border:focus {
  border-color: var(--bs-body-color) !important;
  color: var(--bs-secondary-color) !important;
  background-color: transparent !important;
}
.btn-border {
  --bs-secondary-color: #19235a;
}
.dark .btn-border{
  --bs-secondary-color: #d7dbdf;
}
.button {
  margin-top: 35px;
}

.flip-list-move {
  transition: transform 0.5s;
}

.no-move {
  transition: transform 0s;
}

.ghost {
  opacity: 0.5;
  background: transparent;
}

.list-group {
  min-height: 20px;
}

.btn-border {
  cursor: unset;
}
.handle{
  cursor: move !important;
}
.dark .list-group-item:not(.list-group-item-action){
  background-color: var(--bs-black);
}
</style>
