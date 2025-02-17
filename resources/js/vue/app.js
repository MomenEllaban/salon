import { InitApp } from '../helpers/main'

import AssignBranchEmployeeOffcanvas from './components/branch/AssignBranchEmployeeOffcanvas.vue'
import BranchFormOffcanvas from './components/branch/BranchFormOffcanvas.vue'
import BranchGalleryOffcanvas from './components/branch/BranchGalleryOffcanvas.vue'
import ModuleOffcanvas from './components/module/ModuleOffcanvas.vue'
import ManageRoleForm from './components/role_permission/ManageRoleForm.vue'

import VueTelInput from 'vue3-tel-input'
import 'vue3-tel-input/dist/vue3-tel-input.css'

const app = InitApp()

app.use(VueTelInput)
app.component('assign-branch-employee-offcanvas', AssignBranchEmployeeOffcanvas)
app.component('branch-form-offcanvas', BranchFormOffcanvas)
app.component('branch-gallery-offcanvas', BranchGalleryOffcanvas)
app.component('module-form-offcanvas', ModuleOffcanvas)
app.component('manage-role-form', ManageRoleForm)

app.mount('[data-render="app"]');
