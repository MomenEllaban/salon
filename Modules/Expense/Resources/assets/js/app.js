import { InitApp } from '@/helpers/main'

import ExpenseFormOffcanvas from './components/ExpenseFormOffcanvas.vue'
import ExpenseCategoryFormOffcanvas from './components/ExpenseCategoryFormOffcanvas.vue'


const app = InitApp()

app.component('expense-form-offcanvas', ExpenseFormOffcanvas)
app.component('expense-category-form-offcanvas', ExpenseCategoryFormOffcanvas)


app.mount('[data-render="app"]');
