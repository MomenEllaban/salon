(function ($) {
    ("use strict");

    function checkOffcanvasInstance(element) {


        return bootstrap.Offcanvas.getOrCreateInstance(element)
    }

    function createCustomEvent(eventName, data) {
        document.dispatchEvent(new CustomEvent(eventName, { detail: data }))
    }
    function setEditID({data, resetData}, cb) {
        if (data.form_id !== '') {
            createCustomEvent('crud_change_id', data)
        } else {
            removeEditID(resetData)
        }
        cb()
    }
    function removeEditID(resetData) {
        createCustomEvent('crud_change_id', resetData)
    }

    const formOffcanvas = document.getElementById('form-offcanvas')

    if(formOffcanvas) {
        const instance = checkOffcanvasInstance(formOffcanvas)
        const resetData = {
            form_id: 0
        }

        $(document).on('click', '[data-crud-id]', function() {

            const data = {

                form_id: $(this).attr('data-crud-id')
            }
            setEditID({data: data, resetData: resetData}, () => instance.show())
        })
        formOffcanvas?.addEventListener('hidden.bs.offcanvas', event => {
            removeEditID(resetData)
        })
    }

    $(document).on('click', '[data-assign-module]', function() {
        const offcanvas = document.querySelector($(this).data('assign-target'))
        const eventName = $(this).data('assign-event')
        const data = $(this).data('assign-module')
        if(offcanvas) {
            const instance = checkOffcanvasInstance(offcanvas)
            createCustomEvent(eventName, {form_id: data})
            instance.show()
        }
    })

    $(document).on('click', '[data-gallery-module]', function() {
      const offcanvas = document.querySelector($(this).data('gallery-target'))
      const eventName = $(this).data('gallery-event')
      const data = $(this).data('gallery-module')
      if(offcanvas) {
          const instance = checkOffcanvasInstance(offcanvas)
          createCustomEvent(eventName, {form_id: data})
          instance.show()
      }
    })

    $(document).on('click', '[data-custom-module]', function() {
      const offcanvas = document.querySelector($(this).data('custom-target'))
      const eventName = $(this).data('custom-event')
      const data = $(this).data('custom-module')
      if(offcanvas) {
          const instance = checkOffcanvasInstance(offcanvas)
          createCustomEvent(eventName, {form_id: data})
          instance.show()
      }
    })
})(window.$)
