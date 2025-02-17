<template>
  <div class="col-md-4 col-lg-3">
    <div class="card">
      <div class="card-body">
        <div class="list-group list-group-flush">
          <div class="mb-3" @click="toggle()">
            <router-link :to="{ name: 'profile.info' }" class="btn btn-border"><i class="fa-solid fa-user"></i>{{ $t('messages.personal_information') }}</router-link>
          </div>
          <div class="mb-3" @click="toggle()">
            <router-link :to="{ name: 'profile.change.password' }" class="btn btn-border"><i class="fa-solid fa-key"></i>{{ $t('messages.change_password') }}</router-link>
          </div>
          <div class="mb-3" v-if="ROLES.includes('manager')" @click="toggle()">
            <router-link :to="{ name: 'profile.branchSetting' }" class="btn btn-border"><i class="fa-solid fa-code-branch"></i>{{ $t('messages.branch_setting') }}</router-link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup>
import { ref } from 'vue'
const toggle = () => {
  const formOffcanvas = document.getElementById('offcanvas')
  formOffcanvas.classList.add('show')
}
const ROLES = ref(JSON.parse(document.querySelector('meta[name="auth_user_roles"]')?.getAttribute('content')) || [])
</script>
<style scoped>
.btn-border {
  text-align: left;
  display: flex;
  align-items: center;
  gap: 0.75rem;
}
</style>
