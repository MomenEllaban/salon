<template>
  <form @submit.prevent="formSubmit">
    <div class="offcanvas offcanvas-end" tabindex="-1" id="service-gallery-form" aria-labelledby="form-offcanvasLabel">
      <div class="offcanvas-header border-bottom" v-if="service">
        <h6 class="m-0 h5">
          {{ $t('service.singular_title') }}: <span>{{ service.name }}</span>
        </h6>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="d-flex flex-column border-bottom p-3">
        <div class="">
          <label class="form-label btn btn-info d-block my-0" for="service_feature_image">{{ $t('messages.upload_images') }}</label>
          <input ref="fileInputRef" type="file" class="form-control d-none" id="service_feature_image" accept=".jpeg, .jpg, .png, .gif" multiple @change="handleImageUpload" />
        </div>
      </div>
      <div class="offcanvas-body">
        <div v-if="featureImages.length === 0" class="text-center mb-0">{{ $t('messages.data_not_available') }}</div>
        <div v-else>
          <div class="gallery-images">
            <div v-for="(feature, index) in featureImages" :key="index" class="image-container col">
              <button class="delete-button" @click="removeImage(index)" type="button">
                <i class="fa-solid fa-xmark"></i>
              </button>
              <img :src="feature.full_url" alt="Selected Image" class="img-fluid selected-image" />
            </div>
          </div>
        </div>
      </div>
      <div class="offcanvas-footer">
        <p class="text-center mb-0"><small>{{ $t('messages.gallery_for_service') }}</small></p>
        <div class="d-grid gap-3 p-3">
          <button class="btn btn-primary d-block "><i class="fa-solid fa-floppy-disk me-2"></i>{{ $t('messages.update') }}</button>
          <button class="btn btn-outline-primary d-block" type="button" data-bs-dismiss="offcanvas"><i class="fa-solid fa-angles-left"></i>{{ $t('messages.close') }}</button>
        </div>
      </div>
    </div>
  </form>
</template>

<script setup>
import { ref } from 'vue'
import { GET_GALLERY_URL, POST_GALLERY_URL } from '../constant/service'
import { useRequest, useModuleId, useOnOffcanvasHide } from '@/helpers/hooks/useCrudOpration'
import { createRequestWithFormData } from '@/helpers/utilities'

// Request
const { getRequest } = useRequest()

const fileInputRef = ref(null);
const service = ref(null)
const serviceId = useModuleId(() => {
  getRequest({ url: GET_GALLERY_URL, id: serviceId.value }).then((res) => {
    if(res.status) {
      service.value = res.service
      featureImages.value = res.data
    }
  })
}, 'service_gallery')

// Select Images
let featureImages = ref([])

// Function for Images in Array
const handleImageUpload = (event) => {
  const files = event.target.files;

  for (let i = 0; i < files.length; i++) {
    const file = files[i];

    // Check for valid file types
    if (['image/jpeg', 'image/jpg', 'image/png'].includes(file.type)) {
      const reader = new FileReader();
      reader.readAsDataURL(file);
      reader.onload = () => {
        featureImages.value.push({ file: file, full_url: reader.result, id: null });
        fileInputRef.value.value = ''; 
      }
    } else {
      window.errorSnackbar('Only JPEG, JPG, and PNG files are allowed.');
    }
  }
}

// Function to delete Images
const removeImage = (index) => {
  featureImages.value.splice(index, 1)
}

// Reload Datatable, SnackBar Message, Alert, Offcanvas Close
const errorMessages = ref([])
const reset_close_offcanvas = (res) => {
  if (res.status) {
    window.successSnackbar(res.message)
    bootstrap.Offcanvas.getInstance('#service-gallery-form').hide()
  } else {
    window.errorSnackbar(res.message)
    errorMessages.value = res.all_message
  }
}

// Form Submit
const formSubmit = () => {
  let formData = new FormData();
  Object.keys(featureImages.value).map((index) => {
    Object.keys(featureImages.value[index]).map((fieldName) => {
      let value = featureImages.value[index][fieldName]
      if (fieldName == 'full_url') value = ''
      formData.append(`gallery[${index}][${fieldName}]`, value);
    })
  });
  createRequestWithFormData(POST_GALLERY_URL(serviceId.value), { 'Accept': 'application/json' }, formData).then((res) => reset_close_offcanvas(res));
}
</script>

<style scoped>
.gallery-images {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(104px, 1fr));
  grid-gap: 1rem;
  align-items: stretch;
}
.image-container {
  position: relative;
  max-width: 100%;
}

.delete-button {
  position: absolute;
  top: -8px;
  right: -8px;
  z-index: 1;
  color: var(--bs-white);
  background-color: var(--bs-danger);
  border: none;
  border-radius: 50%;
  width: 24px;
  height: 24px;
  font-size: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.selected-image {
  object-fit: cover;
  height: 100px;
  width: 100%;
}
</style>
