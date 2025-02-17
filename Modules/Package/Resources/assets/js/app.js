import { InitApp } from '@/helpers/main'

import FormOffcanvas from './components/FormOffcanvas.vue'
import PackageServiceFormOffcanvas from './components/PackageServiceFormOffcanvas.vue'
import ClientPackagesOffcanvas from './components/ClientPackagesOffcanvas.vue'

const app = InitApp()

app.component('form-offcanvas', FormOffcanvas)
app.component('package-service-form-offcanvas', PackageServiceFormOffcanvas)
app.component('client-package-form-offcanvas',ClientPackagesOffcanvas)

app.mount('[data-render="app"]');
