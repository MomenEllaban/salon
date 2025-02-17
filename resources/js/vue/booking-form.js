
import { InitApp } from '../helpers/main'
import BookingForm from '../../../Modules/Booking/Resources/assets/js/components/BookingForm.vue'

const app = InitApp()

app.component('booking-form' , BookingForm)

if(document.querySelector('[data-render="global-booking"]')) {
  app.mount('[data-render="global-booking"]');
}

$(document).on('click', '#appointment-button', function() {
  const bookingForm = document.getElementById('booking-form')
  const bsInstance = bootstrap.Offcanvas.getOrCreateInstance(bookingForm)
  bsInstance.hide()
  setTimeout(function() {
    bsInstance.show()
  }, 100)
})
