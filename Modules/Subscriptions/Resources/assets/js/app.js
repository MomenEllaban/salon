import { InitApp } from '@/helpers/main'
import PlanOffcanvas from './components/PlanOffcanvas.vue'
import PlanlimitOffcanvas from './components/PlanlimitOffcanvas.vue'

const app = InitApp()

//plan limt offcanvas

app.component('plan-offcanvas', PlanOffcanvas)
app.component('plan-limit-offcanvas', PlanlimitOffcanvas)


app.mount('[data-render="app"]');



