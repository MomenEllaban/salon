import { InitApp } from '@/helpers/main'
import SliderFormOffcanvas from './components/SliderFormOffcanvas.vue'

const app = InitApp()

app.component('slider-form-offcanvas', SliderFormOffcanvas)



app.mount('[data-render="app"]');
