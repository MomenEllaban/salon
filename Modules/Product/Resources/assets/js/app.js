import { InitApp } from '@/helpers/main'

import FormOffcanvas from './components/FormOffcanvas.vue'
import CategoryFormOffcanvas from './components/CategoryFormOffcanvas.vue'
import BrandFormOffcanvas from './components/BrandsFormOffcanvas.vue'
import UnitsFormOffcanvas from './components/UnitsFormOffcanvas.vue'
import VariationsFormOffcanvas from './components/VariationsFormOffcanvas.vue'
import ProductGalleryOffcanvas from './components/ProductGalleryOffcanvas.vue'
import FormOffcanvasStock from '../../../../Location/Resources/assets/js/components/FormOffcanvasStock.vue'
import { QuillEditor } from '@vueup/vue-quill'
import '@vueup/vue-quill/dist/vue-quill.snow.css';

const app = InitApp()

app.component('product-form-offcanvas', FormOffcanvas)
app.component('category-form-offcanvas', CategoryFormOffcanvas)
app.component('brand-form-offcanvas', BrandFormOffcanvas)
app.component('units-form-offcanvas', UnitsFormOffcanvas)
app.component('variations-form-offcanvas', VariationsFormOffcanvas)
app.component('product-gallery-offcanvas', ProductGalleryOffcanvas)
app.component('stock-offcanvas', FormOffcanvasStock)
app.component('QuillEditor', QuillEditor)
app.mount('[data-render="app"]');
