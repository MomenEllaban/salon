import { defineStore } from 'pinia'

export const useQuickBooking = defineStore('quickBooking', {
    state: () => ({
        user : {
            "email": null,
            "first_name": null,
            "last_name": null,
            "mobile": null,
            "gender" : null
        },
        booking: {
            "branch_id": null,
            "start_date_time": null,
            "note": "",
            "category_id": null,
            "employee_id": null,
            "services": [
                {
                    "employee_id": null,
                    "service_id": null,
                    "service_price": null,
                    "duration_min": null,
                    "start_date_time": null
                }
            ]
        },
        bookingResponse: null
    }),
    actions: {
        updateUserValues(payload) {
            this.user[payload.key] = payload.value
        },
        updateBookingValues(payload) {
            this.booking[payload.key] = payload.value
        },
        updateBookingEmployeeValues(payload) {
            this.booking['services'][0].employee_id = payload
        },
        updateBookingServiceTimeValues(payload) {
            this.booking['services'][0].start_date_time = payload
        },
        updateBookingResponse (payload) {
            this.bookingResponse = payload
        },
        resetState () {
            this.bookingResponse = null
            this.booking = {
                "branch_id": null,
                "start_date_time": null,
                "note": "",
                "category_id": null,
                "employee_id": null,
                "services": [
                    {
                        "employee_id": null,
                        "service_id": null,
                        "service_price": null,
                        "duration_min": null,
                        "start_date_time": null
                    }
                ]
            }
            this.user = {
                "email": null,
                "first_name": null,
                "last_name": null,
                "mobile": null,
                "gender" : null
            }
        }
    }
  })
