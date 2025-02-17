<template>
    <div class="mb-3">
      <label class="form-label"><strong>{{label}}</strong></label>
    </div>
      <div class="row">
        <div class="col-lg-8 order-1">
          <input type="file" class="form-control" id="feature_image" accept=".jpeg, .jpg, .png, .gif" multiple @change="handleImageUpload"/>
        </div>
        <div class="col-lg-4 order-0">
            <div class="card text-center inline-block">
                <div class="image-grid">
                  <div v-for="(imageUrl, index) in imageUrls" :key="index" class="image-container">
                    <button class="delete-button" @click="removeImage(index)"><svg width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="currentColor"><path d="M19.3248 9.46826C19.3248 9.46826 18.7818 16.2033 18.4668 19.0403C18.3168 20.3953 17.4798 21.1893 16.1088 21.2143C13.4998 21.2613 10.8878 21.2643 8.27979 21.2093C6.96079 21.1823 6.13779 20.3783 5.99079 19.0473C5.67379 16.1853 5.13379 9.46826 5.13379 9.46826" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path><path d="M20.708 6.23975H3.75" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path><path d="M17.4406 6.23973C16.6556 6.23973 15.9796 5.68473 15.8256 4.91573L15.5826 3.69973C15.4326 3.13873 14.9246 2.75073 14.3456 2.75073H10.1126C9.53358 2.75073 9.02558 3.13873 8.87558 3.69973L8.63258 4.91573C8.47858 5.68473 7.80258 6.23973 7.01758 6.23973" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg></button>
                    <img :src="imageUrl || defaultImage" alt="Selected Image" class="selected-image" />

                  </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
defineProps({
  label:{ type:String, default:'' },
  defaultImage: { type: File, default: 'https://dummyimage.com/600x300/cfcfcf/000000.png' }
})

const imageUrls = ref([])

// Function for Images in Array
const handleImageUpload = (event) => {
const files = event.target.files;

  for (let i = 0; i < files.length; i++) {
    const file = files[i];
    const reader = new FileReader();
    reader.readAsDataURL(file);
    reader.onload = () => {
      imageUrls.value.push(reader.result);
    }
  }
}
// Function to delete Images
const removeImage = (index) => {
  imageUrls.value.splice(index, 1)
}
</script>

<style>
.image-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  grid-gap: 5px;
}

.image-container {
  position: relative;
}

.delete-button {
  position: absolute;
  top: 0;
  z-index: 1;
  background-color: rgba(213, 210, 210, 0.972);
  color: #f10e0e;
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
  max-width: 125px;
  max-height: 150px;
  object-fit: contain
}
</style>
