import { InitApp } from '@/helpers/main'
import PageOffcanvas from './components/PageOffcanvas.vue'

const app = InitApp()

app.component('page-offcanvas', PageOffcanvas)

app.mount('[data-render="app"]');


