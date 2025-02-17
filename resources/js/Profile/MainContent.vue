<template>
  <div class="col-md-8 col-lg-9 navbar-expand-md">
    <div class="offcanvas offcanvas-end" id="offcanvas" data-bs-backdrop="false">
      <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasNavbarLabel">Setting</h5>
          <button type="button" class="btn-close" @click="toggle()"></button>
      </div>
        <div class="card card-accent-primary offcanvas-body">
          <div class="card-body">
            <router-view></router-view>
          </div>
      </div>
    </div>
  </div>
</template>
<script setup>
const toggle = () =>{
  const formOffcanvas = document.getElementById('offcanvas')
  formOffcanvas.classList.remove("show")
}
</script>

