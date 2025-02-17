<template>
  <form>
    <div :class="`offcanvas offcanvas-end`" data-bs-scroll="true" tabindex="-1" id="booking-form" aria-labelledby="offcanvasBookingForm">
      <template v-if="SINLGE_STEP == 'MAIN' && status == 'completed'">
        <InvoiceComponent :booking_id="id"></InvoiceComponent>
      </template>
      <template v-else-if="SINLGE_STEP == 'MAIN' && status != 'checkout'">
        <div class="offcanvas-header">
          <BookingHeader :booking_id="id" :status="status" :is_paid="is_paid" @statusUpdate="updateStatus"></BookingHeader>
        </div>
        <p class="ps-3" v-if="id > 0">
          <strong>{{ $t('Appointment Id') }} :-{{ id }} </strong>
        </p>
        <BookingStatus v-if="id" :status="status" :booking_id="id" :status-list="statusList" :employee_id="employee_id" @statusUpdate="updateStatus"></BookingStatus>
        <div>
          <div class="d-flex text-center date-time">
            <div class="col-6 py-3">
              <i>On</i> <strong v-if="start_date_time && start_date_time !== 'Invalid date'">{{ moment(start_date_time).format('D, MMM YYYY') }}</strong>
              <strong v-else> {{ moment(current_date).format('D, MMM YYYY') }}</strong>
            </div>
            <div class="col-6 py-3">
              <i>At</i> <strong v-if="start_date_time && start_date_time !== 'Invalid date'">{{ moment(start_date_time).format('LT') }}</strong>
              <strong v-else>--:--</strong>
            </div>
          </div>
        </div>
        <div class="offcanvas-body border-top">
          <div class="form-group" v-if="bookingType !== 'CALENDER_BOOKING' && branch.options.length > 1">
            <Multiselect id="branch_id" placeholder="Select Branch" v-model="branch_id" :disabled="is_paid || filterStatus(status).is_disabled" :value="branch_id" v-bind="singleSelectOption" :options="branch.options" @select="branchSelect" @change="removeBranch" class="form-group"></Multiselect>
          </div>
          <div class="form-group" v-if="bookingType !== 'CALENDER_BOOKING' && branch_id">
            <Multiselect id="employee_id" placeholder="Select Staff" v-model="employee_id" :value="employee_id" :disabled="is_paid || filterStatus(status).is_disabled" v-bind="singleSelectOption" :options="employee.options" @select="employeeSelect" @change="removeEmployee" class="form-group"></Multiselect>
          </div>
          <div class="row">
            <div class="form-group col-6" v-if="bookingType !== 'CALENDER_BOOKING' && employee_id">
              <div class="booking-datepicker">
                <flat-pickr v-model="current_date" :disabled="is_paid || filterStatus(status).is_disabled" placeholder="Select Date" @change="dateChange" :config="config" class="form-control" />
              </div>
            </div>
            <div class="form-group col-6" v-if="bookingType !== 'CALENDER_BOOKING' && current_date && employee_id">
              <Multiselect id="star_time" placeholder="Select Time" v-model="start_date_time" :disabled="is_paid || filterStatus(status).is_disabled" :value="start_date_time" v-bind="singleSelectOption" :options="slots" @select="slotSelect" @change="removeSlot" class="form-group"></Multiselect>
            </div>
          </div>
          <div class="form-group border-bottom">
            <div v-if="selectedCustomer">
              <div class="d-flex align-items-start gap-3 mb-2">
                <img :src="selectedCustomer.profile_image" alt="avatar" class="img-fluid avatar avatar-60 rounded-pill" />
                <div class="flex-grow-1">
                  <div class="gap-2">
                    <strong>{{ selectedCustomer.full_name }}</strong>
                    <p class="m-0">
                      <small>Client since {{ moment(selectedCustomer.created_at).format('MMMM YYYY') }}</small>
                    </p>
                  </div>
                </div>
                <button type="button" v-if="!is_paid && !['check_in', 'checkout', 'confirmed'].includes(status)" @click="removeCustomer()" class="btn btn-sm text-danger"><i class="fa-regular fa-trash-can"></i></button>
              </div>
              <div class="row">
                <label class="col-3"
                  ><i>{{ $t('booking.lbl_phone') }}</i></label
                >
                <strong class="col">
                  <span v-if="selectedCustomer.mobile">{{ selectedCustomer.mobile }}</span>
                  <span v-else>-</span></strong
                >
              </div>
              <div class="row mb-3">
                <label class="col-3"
                  ><i>{{ $t('booking.lbl_e-mail') }}</i></label
                >
                <strong class="col">
                  <span v-if="selectedCustomer.email">{{ selectedCustomer.email }}</span>
                  <span v-else>-</span>
                </strong>
              </div>
            </div>
            <Multiselect id="user_id" v-else v-model="user_id" placeholder="Select Customer" :disabled="is_paid || filterStatus(status).is_disabled" :value="user_id" v-bind="singleSelectOption" :options="customer.options" @select="customerSelect" class="form-group"></Multiselect>
          </div>
          <ul class="form-group list-group list-group-flush">
            <li v-for="(service, index) in selectedService" :key="index" class="list-group-item py-3 px-1">
              <div class="d-flex flex-column gap-2">
                <div class="d-flex align-items-center justify-content-between">
                  <h6>{{ service.service_name }} ({{ formatCurrencyVue(service.service_price) }})</h6>
                  <button type="button" v-if="!['check_in', 'checkout', 'confirmed'].includes(status) && !is_paid" @click="removeService(service.service_id)" class="btn btn-sm text-danger"><i class="fa-regular fa-trash-can"></i></button>
                </div>
                <p class="m-0">
                  <label
                    ><i>{{ $t('booking.lbl_with') }}</i></label
                  >
                  <strong>{{ service.employee?.full_name || selectedEmployee?.name || '' }}</strong>
                </p>
                <div>
                  <label
                    ><i>{{ $t('booking.lbl_at') }}</i></label
                  >
                  <strong v-if="service.start_date_time !== 'Invalid date'">{{ moment(service.start_date_time).format('LT') }}</strong
                  ><strong v-else>--:--</strong> <span class="px-2">|</span> <label class="me-2"><i>For: </i></label><strong>{{ service.duration_min }} Min</strong>
                </div>
              </div>
            </li>
          </ul>
          <div v-if="selectPurchasePackages.length === 0 && services_id.length < service.options.length && selectedCustomer && employee_id" class="text-center">
            <Multiselect v-if="newService" :canClear="false" placeholder="Select Service" ref="serviceInput" class="" v-model="services_id" :value="services_id" v-bind="multipleSelectOption" :options="service.options" @select="serviceSelect" id="service_ids">
              <template v-slot:multiplelabel="{ values }">
                <div class="multiselect-multiple-label">Select Service</div>
              </template>
            </Multiselect>
            <template v-else>
              <a v-if="!filterStatus(status).is_disabled && !is_paid && start_date_time" href="javascript:void(0)" @click="addNewService" class="btnw-100"><i class="fa-solid fa-circle-plus"></i> {{ $t('booking.lbl_add_service') }}</a>
            </template>
          </div>
          <div v-if="selectPurchasePackages.length == 0 && selectedCustomer && employee_id" class="text-center bg-soft-primary p-5 iq-package mt-3" @click="purchasePackageModel">
            <div class="d-flex justify-content-center mb-3">
              <div class="avatar avatar-60 rounded-pill bg-soft-secondary">
                <i class="fa-solid fa-gift"></i>
              </div>
            </div>
            <h5>{{ filteredPackages.length }} {{ $t('booking.lbl_package_available') }}</h5>
            <h6 class="text-primary">View all</h6>
          </div>
          <div v-if="selectPurchasePackages.length > 0 && selectedCustomer && employee_id">
            <ul class="form-group list-group list-group-flush iq-package-list">
              <div class="d-flex align-items-center justify-content-between">
                <h6 class="mb-0">{{ $t('booking.lbl_packages_detail') }}</h6>
                <!-- <a href="#" @click="purchasePackageModel" class="pe-auto text-primary">{{ $t('booking.lbl_add_more') }}</a> -->
              </div>
              <li v-for="(packages, index) in selectPurchasePackages" :key="index" class="list-group-item py-3 px-3 bg-soft-primary rounded-3 m-2">
                <div class="d-flex flex-column gap-2">
                  <div class="d-flex align-items-center justify-content-between">
                    <h6>{{ packages.name }}</h6>
                    <button type="button" v-if="!['check_in', 'checkout', 'confirmed'].includes(status) && !is_paid" @click="removePurchasePackage(packages.package_id)" class="btn btn-sm text-danger"><i class="fa-regular fa-trash-can"></i></button>
                  </div>

                  <p class="mb-0">
                    <span class="text-primary">{{ formatCurrencyVue(packages.package_price) }}</span
                    >/{{ displayPackageDuration(packages.start_date, packages.end_date) }}
                  </p>
                  <div v-for="packageServices in packages.services" :key="packageServices.id">
                    <div class="mb-4">
                      <div class="d-flex align-items-center gap-2 mb-1">
                        <p class="mb-0">-> {{ packageServices.service_name }} -</p>
                        <h6 class="mb-0">60 mins</h6>
                      </div>
                      <div class="d-flex align-items-center gap-2 ms-3">
                        <p class="mb-0">Quantity:</p>
                        <h6 class="mb-0">{{ packageServices.quantity ?? packageServices.qty ?? packageServices.remaining_qty }}</h6>
                      </div>
                    </div>
                  </div>
                </div>
              </li>
            </ul>
          </div>
        </div>
        <div class="offcanvas-footer">
          <div v-if="userPackage && userPackage.length > 0 && selectPurchasePackages.length === 0">
            <a href="javascript:void(0)" @click="openUserPackage()" class="alert alert-success d-flex align-items-center justify-content-between mx-3" role="alert">
              <div class="d-flex gap-2 align-items-center">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <p class="mb-0">{{ $t('booking.lbl_package_active') }}</p>
              </div>
              <p class="mb-0">{{ $t('booking.lbl_view_all') }}</p>
            </a>
          </div>
          <div class="form-group px-3">
            <label class="form-label">{{ $t('booking.lbl_note') }}</label>
            <textarea name="note" :disabled="is_paid || filterStatus(status).is_disabled" v-model="note" cols="60" class="form-control"></textarea>
          </div>
          <div class="form-group m-0 p-3 d-flex justify-content-between border-top">
            <label for=""
              ><strong>{{ $t('booking.lbl_sub_tot') }} </strong>
            </label>
            <span>{{ formatCurrencyVue(SUB_TOTAL_SERVICE_AMOUNT) }}</span>
          </div>
          <div class="d-grid gap-3" v-if="status !== 'check_in' && !is_paid">
            <button :disabled="services_id.length > 0 || (selectPurchasePackages.length > 0 && status !== 'cancelled') ? false : true" :class="`btn ${services_id.length > 0 || (selectPurchasePackages.length > 0 && status !== 'cancelled') ? 'btn-primary' : 'disabled btn-gray'} btn-lg rounded-0 d-block`" @click="formSubmit">
              <template v-if="IS_SUBMITED">
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                Loading...
              </template>
              <span v-else><i class="fa-solid fa-floppy-disk me-2"></i>{{ $t('messages.save_appointment') }}</span>
            </button>
          </div>
        </div>
      </template>
      <template v-else-if="SINLGE_STEP == 'CHECK_OUT' && status == 'checkout'">
        <div class="offcanvas-header">
          <div class="d-flex gap-2 align-items-center">
            <h4 class="offcanvas-title" id="form-offcanvasLabel">Checkout</h4>
            <small class="badge bg-success" v-if="is_paid">{{ $t('booking.lbl_is_paid') }}</small>
          </div>

          <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <p class="ps-3" v-if="id > 0">
          <strong>{{ $t('Appointment Id') }} :-{{ id }} </strong>
        </p>
        <div class="offcanvas-body border-top">
          <div v-if="selectedCustomer" class="border-bottom mb-3">
            <div class="d-flex align-items-start gap-3 mb-3">
              <img :src="selectedCustomer.profile_image" alt="avatar" class="img-fluid avatar avatar-60 rounded-pill" />
              <div class="flex-grow-1">
                <div class="gap-2">
                  <strong>{{ selectedCustomer.full_name }}</strong>
                  <p class="m-0">
                    <small>Client since {{ moment(selectedCustomer.created_at).format('MMMM YYYY') }}</small>
                  </p>
                </div>
              </div>
            </div>
          </div>
          <div v-if="selectedService.length > 0" :class="selectedPackage.length > 0 ? 'border-bottom' : ''">
            <h5 class="mt-3">Services</h5>
            <ul class="form-group list-group list-group-flush">
              <li v-for="(service, index) in selectedService" :key="index" class="list-group-item py-3 px-1">
                <div class="d-flex flex-column gap-2">
                  <div class="d-flex align-items-center justify-content-between" :class="{ 'text-decoration-line-through text-danger': isPackageServiceSelected(service.service_id) }">
                    <h6>
                      {{ service.service_name }} <span :id="service.service_id">({{ formatCurrencyVue(service.service_price) }})</span><span :id="service.service_name"></span>
                    </h6>
                    <button type="button" v-if="!is_paid" @click="removeService(service.service_id)" class="btn btn-sm text-danger"><i class="fa-regular fa-trash-can"></i></button>
                  </div>
                  <p class="m-0">
                    <label
                      ><i>{{ $t('booking.lbl_with') }}</i></label
                    >
                    <strong>{{ service.employee?.full_name || selectedEmployee?.name || '' }}</strong>
                  </p>
                  <div>
                    <label
                      ><i>{{ $t('booking.lbl_at') }}</i></label
                    >
                    <strong>{{ moment(service.start_date_time).format('LT') }}</strong> <span class="px-2">|</span> <label class="me-2"> <i>For:</i></label
                    ><strong> {{ service.duration_min }} Min</strong>
                  </div>
                </div>
                <div v-if="!isPackageServiceSelected(service.service_id) && isServiceInFilteredPackages(service.service_id)">
                  <div class="btn w-100 btn-primary mt-2" @click="applyUserPackage(service.service_id)" :id="service.service_id" data-bs-target="#exampleModal">Apply a Package</div>
                </div>
                <div class="mt-2 d-none d-flex justify-content-between align-items-center" :id="service.service_id + '' + service.service_id">
                  <h6>{{ service.service_name }} Package</h6>
                  <!-- <div v-for="appliedServices in appliedServices">
                      <h6 class="text-danger" v-if="service.service_id === ">- {{ formatCurrencyVue(appliedServices.price) }}</h6>
                    </div> -->
                  <h6 class="text-danger" v-if="singleAppliedService(service.service_id)">- {{ formatCurrencyVue(singleAppliedServicePrice) }}</h6>
                </div>
                <!-- </div> -->
              </li>
            </ul>
          </div>
          <div v-if="selectedPackageService && selectedPackageService.length > 0">
            <div class="mt-3 mb-3">
              <h6>Package Service</h6>
              <div class="bg-soft-secondary p-3 rounded-3">
                <div v-for="selectedPackageService in selectedPackageService">
                  <div class="d-flex align-items-center justify-content-between gap-2">
                    <div class="d-flex align-items-center gap-2">
                      <p>-></p>
                      <p>{{ selectedPackageService.service_name }}</p>
                    </div>
                    <button type="button" v-if="!is_paid" @click="removeApplyPackageService(selectedPackageService.service_id)" class="btn btn-sm text-danger"><i class="fa-regular fa-trash-can"></i></button>
                  </div>
                  <div class="d-flex align-items-center justify-content-between ms-3">
                    <div class="d-flex align-items-center gap-3">
                      <p class="mb-0">Quantity:</p>
                      <h6 class="mb-0">{{ selectedPackageService.qty }}</h6>
                    </div>
                    <div>
                      <h6 class="text-danger">(Remainig {{ selectedPackageService.remaining_qty - selectedPackageService.qty }}/{{ selectedPackageService.total_qty }})</h6>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div v-if="selectPurchasePackages.length > 0 && selectedCustomer && employee_id">
            <ul class="form-group list-group list-group-flush mt-3">
              <div class="d-flex align-items-center justify-content-between">
                <h6>{{ $t('booking.lbl_packages_detail') }}</h6>
                <!-- <a href="#" @click="purchasePackageModel" class="pe-auto text-primary">add more</a> -->
              </div>
              <li v-for="(packages, index) in selectPurchasePackages" :key="index" class="list-group-item py-3 px-3 bg-soft-primary rounded-3 m-2">
                <div class="d-flex flex-column gap-2">
                  <div class="d-flex align-items-center justify-content-between">
                    <h6>{{ packages.name }}</h6>
                    <button type="button" v-if="!['check_in', 'checkout', 'confirmed'].includes(status) && !is_paid" @click="removePurchasePackage(packages.package_id)" class="btn btn-sm text-danger"><i class="fa-regular fa-trash-can"></i></button>
                  </div>
                  <p>
                    <span class="text-primary">{{ formatCurrencyVue(packages.package_price) }}</span
                    >/{{ displayPackageDuration(packages.start_date, packages.end_date) }}
                  </p>
                  <div v-for="packageServices in packages.services" :key="packageServices.id">
                    <div class="mb-4">
                      <div class="d-flex align-items-center gap-2 mb-1">
                        <p class="mb-0">-> {{ packageServices.service_name }} -</p>
                        <h6 class="mb-0">60 mins</h6>
                      </div>
                      <div class="d-flex align-items-center gap-2 ms-3">
                        <p class="mb-0">Quantity:</p>
                        <h6 class="mb-0">{{ packageServices.quantity ?? packageServices.qty ?? packageServices.remaining_qty }}</h6>
                      </div>
                    </div>
                  </div>
                </div>
              </li>
            </ul>
          </div>

          <div v-if="selectedProduct.length > 0">
            <h5 class="mt-3">Products</h5>
            <ul class="form-group list-group list-group-flush">
              <li v-for="(product, index) in selectedProduct" :key="index" class="list-group-item py-3 px-1">
                <div class="d-flex flex-column gap-2">
                  <div class="d-flex align-items-center justify-content-between">
                    <h6>{{ product.product_name }}</h6>
                    <button type="button" v-if="!is_paid" @click="removeProduct(product.product_variation_id)" class="btn btn-sm text-danger"><i class="fa-regular fa-trash-can"></i></button>
                  </div>
                  <div>
                    <div class="d-flex">
                      <label>Price:</label>
                      <template v-if="product.discounted_price">
                        <h5 class="ms-2">
                          <b>{{ formatCurrencyVue(product.discounted_price) }}</b>
                        </h5>
                        <h6 class="text-secondary" v-if="product.product_price != product.discounted_price">
                          <del class="me-2">{{ formatCurrencyVue(product.product_price) }}</del>
                          <small class="text-success" v-if="product.discount_type == 'percent'">{{ product.discount_value }}% OFF</small>
                          <small class="text-success" v-else>{{ formatCurrencyVue(product.discount_value) }} OFF</small>
                        </h6>
                      </template>
                      <template v-else>
                        <h1>{{ product.product_price }}</h1>
                        <h5 class="ms-2">
                          <b>{{ formatCurrencyVue(product.product_price) }}</b>
                        </h5>
                      </template>
                    </div>
                    <label>Quantity: </label> <QtyButton v-model="product.product_qty" :max="product.max_qty"></QtyButton>
                  </div>
                  <p class="m-0">
                    <label><i>Sold By: </i></label> <strong>{{ product.employee?.full_name || selectedEmployee?.name || '' }}</strong>
                  </p>
                </div>
              </li>
            </ul>
          </div>
          <div class="d-flex gap-3 justify-content-between flex-column">
            <div v-if="services_id.length < service.options.length" class="text-center">
              <Multiselect v-if="newService" :canClear="false" placeholder="Select Service" ref="serviceInput" class="" v-model="services_id" :value="services_id" v-bind="multipleSelectOption" :options="service.options" @select="serviceSelect" id="service_ids">
                <template v-slot:multiplelabel="{ values }">
                  <div class="multiselect-multiple-label">Select Service</div>
                </template>
              </Multiselect>
              <template v-else>
                <a href="javascript:void(0)" v-if="selectPurchasePackages.length === 0 && !is_paid" @click="addNewService" class="btnw-100"><i class="fa-solid fa-circle-plus"></i> {{ $t('booking.lbl_add_service') }}</a>
              </template>
            </div>
            <div v-if="product_variation_id.length < products.options.length" class="text-center">
              <Multiselect v-if="newProduct" :canClear="false" placeholder="Select Product" ref="productInput" class="" v-model="product_variation_id" :value="product_variation_id" v-bind="multipleSelectOption" :options="products.options" @select="selectProduct" id="product_variation_ids">
                <template v-slot:multiplelabel="{ values }">
                  <div class="multiselect-multiple-label">Select Product</div>
                </template>
              </Multiselect>
              <template v-else>
                <a href="javascript:void(0)" v-if="!is_paid" @click="addNewProduct" class="btnw-100"><i class="fa-solid fa-circle-plus"></i> Add Product</a>
              </template>
            </div>
          </div>
        </div>

        <div class="offcanvas-footer border-top">
          <div class="form-group m-0 p-3 d-flex justify-content-between">
            <label for=""
              ><strong>{{ $t('booking.lbl_sub_tot') }} </strong>
            </label>

            <span v-if="packageApplied">{{ formatCurrencyVue(GRAND_TOTAL) }}</span>

            <span v-else>{{ formatCurrencyVue(SUB_TOTAL_SERVICE_AMOUNT - PackageServiceSelectedPrice) }}</span>
          </div>
          <div class="d-grid gap-3">
            <button type="button" :disabled="IS_SUBMITED" v-if="selectPurchasePackages.length > 0 || selectedService.length > 0" class="btn btn-primary btn-lg rounded-0 d-block" @click="formSubmitCheckout">
              <template v-if="IS_SUBMITED">
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                Loading...
              </template>
              <template v-else>
                <template v-if="is_paid"> <i class="fa-solid fa-floppy-disk mx-2"></i>{{ $t('booking.lbl_complete_now') }} </template>
                <template v-else> <i class="fa-solid fa-floppy-disk mx-2"></i>{{ $t('booking.lbl_got_to_payment') }} </template>
              </template>
            </button>
          </div>
        </div>
      </template>
      <template v-else-if="SINLGE_STEP == 'PAYMENT'">
        <div class="offcanvas-header">
          <h4 class="offcanvas-title" id="form-offcanvasLabel">{{ $t('booking.lbl_payment') }}</h4>
          <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <p class="ps-3" v-if="id > 0">
          <strong>{{ $t('Appointment Id') }} :-{{ id }} </strong>
        </p>
        <div class="offcanvas-body border-top">
          <PaymentForm @updatePaymentData="updatePaymentData" :booking-id="id" :booking-status="status" :package-service="selectedPackageService"></PaymentForm>
        </div>
        <div class="offcanvas-footer">
          <div class="d-grid gap-3">
            <button type="button" :disabled="IS_SUBMITED" class="btn btn-primary btn-lg rounded-0 d-block" @click="formSubmitPaynow">
              <template v-if="IS_SUBMITED">
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                Loading...
              </template>
              <template v-else><i class="fa-solid fa-floppy-disk"></i> {{ $t('booking.lbl_pay_now') }}</template>
            </button>
          </div>
        </div>
      </template>
    </div>

    <div class="modal fade modal-lg" id="applyUserPackage" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-body">
            <div class="modal-header mb-3">
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="row">
              <div v-for="packages in filteredUserPackages" :key="packages.id">
                <div class="d-flex justify-content-between mb-3">
                  <h4>{{ packages.name }}</h4>
                </div>
                <div v-for="(packageServices, index) in packages.services" :key="packageServices.id" class="d-flex justify-content-between mb-3">
                  <div class="d-flex align-items-center gap-3">
                    <input type="checkbox" :id="'service-' + packageServices.id" :checked="isServiceSelected(packageServices.user_package_id, packageServices.service_id)" @change="updateSelectedServices(packageServices)" />
                    <p class="mb-0">-></p>
                    <label :for="'service-' + packageServices.id">{{ packageServices.service_name }} </label>
                    - <label :for="'service-' + packageServices.id" class="fw-semibold text-dark">{{ packageServices.duration_min }} Mins</label>
                  </div>
                  <div class="d-flex align-items-center gap-3">
                    <label :for="'qtybutton-' + packageServices.id">Quantity: </label>
                    <QtyButton :id="'qtybutton-' + packageServices.id" v-model="packageServices.qty" :max="packageServices.remaining_qty" @input="updateServiceQty(index, packageServices, $event)"></QtyButton>
                    <p class="mb-0">
                      Left -<span class="text-danger me-2">{{ packageServices.remaining_qty }}</span>
                    </p>
                    <p class="mb-0">
                      Used -<span class="text-success me-2">{{ packageServices.total_qty - packageServices.remaining_qty }}</span>
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer position-sticky fixed-bottom bg-white">
            <div class="d-flex justify-content-end gap-2">
              <button type="button" class="btn btn-secondary" @click="cancelSelectedPackageService()">Cancel</button>
              <button type="button" class="btn btn-primary" @click="saveSelectedPackageService()">Save</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade modal-lg" id="userPackage" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-body">
            <div class="modal-header mb-3">
              <h4 class="mb-0">Your existing package</h4>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="row">
              <div v-for="packages in userPackage" class="col-md-6">
                <div v-if="packages.payment_status === 1" class="card bg-soft-secondary">
                  <div class="card-body text-gray">
                    <div class="d-flex justify-content-between mb-3">
                      <div>
                        <small class="bg-soft-secondary rounded-pill py-2 px-4">{{ packages.branch_name }}</small>
                      </div>
                      <div class="d-flex align-items-center gap-1">
                        <h6 class="text-primary mb-0">{{ formatCurrencyVue(packages.package_price) }}</h6>
                        / <small>{{ displayPackageDuration(packages.start_date, packages.end_date) }}</small>
                      </div>
                    </div>
                    <div class="d-flex justify-content-between">
                      <h5 class="mb-3">{{ packages.name }}</h5>
                    </div>
                    <div class="iq-purchase-package">
                      <p class="border-bottom pb-3 mb-3">{{ packages.description }}</p>
                      <div>
                        <h5 class="mb-3">What's Included</h5>
                        <div v-for="packageServices in packages.services" :key="packageServices.id">
                          <div class="mb-4">
                            <div class="d-flex align-items-center gap-2 mb-1">
                              <p class="mb-0">-> {{ packageServices.service_name }} -</p>
                              <h6 class="mb-0">{{ packageServices.duration_min }} mins</h6>
                            </div>
                            <div class="d-flex align-items-center gap-2 ms-3">
                              <p class="mb-0">Quantity:</p>
                              <h6 class="mb-0">{{ packageServices.remaining_qty }}</h6>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="card-footer pt-0">
                    <div>
                      <div v-if="isPackagePurchased(packages.id)">
                        <div class="btn btn-secondary d-block" @click="removePurchasePackageId(packages.id)">Purchased</div>
                      </div>
                      <div v-else>
                        <div class="btn btn-outline-secondary d-block" @click="redeemPackage(packages.package_id)">Use</div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade modal-xl" id="purchasePackageModel" aria-labelledby="purchasePackageModelLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="mb-0">Packages Available</h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="row">
              <div v-if="filteredPackages.length > 0" v-for="packages in filteredPackages" :key="service.id" class="col-md-4">
                <div class="card bg-soft-secondary" :class="{ 'iq-card-package': isPackagePurchased(packages.id) }" id="iq-modal-package">
                  <div class="card-body text-gray">
                    <div class="d-flex justify-content-between mb-3">
                      <div>
                        <small class="bg-soft-secondary rounded-pill py-2 px-4">{{ packages.branch_name }}</small>
                      </div>
                      <div class="d-flex align-items-center gap-1">
                        <h6 class="text-primary mb-0">{{ formatCurrencyVue(packages.package_price) }}</h6>
                        / <small>{{ displayPackageDuration(packages.start_date, packages.end_date) }} </small>
                      </div>
                    </div>
                    <div class="d-flex justify-content-between">
                      <h5 class="mb-3">{{ packages.name }}</h5>
                    </div>
                    <div class="iq-purchase-package">
                      <p class="border-bottom pb-3 mb-3">{{ packages.description }}</p>
                      <h5 class="mb-3">What's Included :</h5>
                      <div v-for="packageServices in packages.services" :key="packageServices.id">
                        <div class="mb-4">
                          <div class="d-flex align-items-center gap-2 mb-1">
                            <p class="mb-0">-> {{ packageServices.service_name }} -</p>
                            <h6 class="mb-0">{{ packageServices.duration_min }} mins</h6>
                          </div>
                          <div class="d-flex align-items-center gap-2 ms-3">
                            <p class="mb-0">Quantity:</p>
                            <h6 class="mb-0">{{ packageServices.quantity }}</h6>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="card-footer p-2">
                    <div v-if="isPackagePurchased(packages.id)">
                      <div class="btn btn-secondary d-block" @click="removePurchasePackageId(packages.id)">Purchased</div>
                    </div>
                    <div v-else>
                      <div class="btn btn-secondary d-block" @click="purchasePackage(packages.id)">Purchase Now</div>
                    </div>
                  </div>
                </div>
              </div>
              <div v-else>
                <p>No package is available for the current service.</p>
              </div>
            </div>
          </div>
          <div class="modal-footer position-sticky fixed-bottom bg-white">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Close" @click="closeModel()">Cancel</button>
            <button type="button" class="btn btn-primary" @click="savePurchasePackage()">Save</button>
          </div>
        </div>
      </div>
    </div>
  </form>

  <CustomerCreate :data="newCustomerData" @submit="externalFormCreation"></CustomerCreate>
</template>
<script setup>
import { ref, reactive, watch, onMounted, computed } from 'vue'
import FlatPickr from 'vue-flatpickr-component'
import { useBookingStore } from '../store/booking'
// Select Options List Request
import { EMPLOYEE_LIST, CUSTOMER_LIST, SERVICE_LIST, SLOT_LIST, PAYMENT_PUT_URL, UPDATE_PAYMENT_DATA, CHECKOUT_URL, STRIPE_PAYMENT_DATA, EDIT_URL, STORE_URL, UPDATE_URL, UPDATE_STATUS, PRODUCT_LIST, PACKAGE_LIST, USER_PACKAGE_LIST } from '../constant/booking'
import { BRANCH_LIST, IS_HOLIDAY } from '@/vue/constants/branch'

import { useField, useForm } from 'vee-validate'
import * as yup from 'yup'

import { useRequest, useOnOffcanvasHide, useOnOffcanvasShow } from '@/helpers/hooks/useCrudOpration'

// Modals
import CustomerCreate from '@/vue/components/Modal/CustomerCreate.vue'

// Element Component
import BookingHeader from './BookingFormElements/BookingHeader.vue'
import BookingStatus from './BookingFormElements/BookingStatus.vue'
import PaymentForm from './Forms/PaymentForm.vue'
import InvoiceComponent from './Forms/InvoiceComponent.vue'

import QtyButton from '@/vue/components/form-elements/QtyButton.vue'
import { useSelect } from '@/helpers/hooks/useSelect'
import moment from 'moment'
const shownServiceIds = ref([])

const { getRequest, storeRequest, updateRequest, listingRequest } = useRequest()
// Event Emits
const emit = defineEmits(['onSubmit'])

const formatCurrencyVue = (value) => {
  if (window.currencyFormat !== undefined) {
    return window.currencyFormat(value)
  }
  return value
}
// Props
const props = defineProps({
  statusList: { type: Object },
  bookingType: { type: String, default: 'GLOBAL_BOOKING' },
  bookingData: {
    default: () => {
      return {
        id: 0,
        employee_id: null,
        start_date_time: null,
        branch_id: null
      }
    }
  }
})
const IS_SUBMITED = ref(false)
const filterStatus = (value) => {
  if (props.statusList) {
    return props.statusList[value]
  }
  return { is_disabled: false }
}
const holidays = ref([])
const current_date = ref(moment().format('YYYY-MM-DD'))
const config = ref({
  dateFormat: 'Y-m-d',
  defaultDate: 'today',
  minDate: new Date(),
  static: true,
  disable: holidays.value
})
watch(holidays, () => {
  config.value.disable = holidays.value
})
watch(
  () => props.bookingType,
  (value) => {}
)

watch(
  () => props.bookingData,
  (value) => {
    status.value = 'pending'
    selectedPackageService.value = []
    store.updateStep('LOADER')

    if (value.id !== null && value.id !== undefined && value.id !== 0) {
      id.value = value.id

      getRequest({ url: EDIT_URL, id: id.value }).then((res) => {
        if (res.status) {
          store.updateStep('MAIN')
          setFormData(res.data)

          // Check for coupon_redeem and handle it
          if (res.data.coupon_redeem) {
            couponRedeem.value = res.data.coupon_redeem // Assuming you have a reactive couponRedeem
          }

          branchSelect(res.data.branch_id)
          employeeSelect(res.data.employee_id)
          getUserPackages(res.data.user_id)
          console.log(res.data);
        }
      })
    } else {
      store.updateStep('MAIN')
      setFormData(defaultData())
      branch_id.value = value.branch_id
      employee_id.value = value.employee_id
      start_date_time.value = moment(value.start_date_time).format('YYYY-MM-DD HH:mm:ss')
      if (value.start_date_time) {
        current_date.value = moment(value.start_date_time).format('YYYY-MM-DD')
      } else {
        current_date.value = moment().format('YYYY-MM-DD')
      }
      branchSelect(value.branch_id)
      employeeSelect(employee_id.value)
    }
  },
  { deep: true }
)

// Vee-Validation Validations
const validationSchema = yup.object({
  start_date_time: yup.string().required('Start Date Time is required'),
  branch_id: yup.string().required('Branch is required'),
  employee_id: yup.string().required('Employee is required'),
  services_id: yup.array().required('Services is required'),
  user_id: yup.string().required('User is required')
})

const { handleSubmit, errors, resetForm } = useForm({ validationSchema })
const { value: id } = useField('id')
const { value: note } = useField('note')
const { value: start_date_time } = useField('start_date_time')
const { value: employee_id } = useField('employee_id')
const { value: branch_id } = useField('branch_id')
const { value: user_id } = useField('user_id')
const { value: status } = useField('status')
const { value: services_id } = useField('services_id')
const { value: product_variation_id } = useField('product_variation_id')
const { value: is_paid } = useField('is_paid')
const { value: package_id } = useField('package_id')
status.value = 'pending'
product_variation_id.value = []
package_id.value = []
services_id.value = []
const userPackage = ref([])

const selectedPackageService = ref([])
const tempSelectedPackageService = ref([])
const errorMessages = ref({})
const couponRedeem = ref([])

// Default FORM DATA
const defaultData = () => {
  errorMessages.value = {}
  selectedPackageService.value = []
  return {
    id: null,
    branch_id: props.bookingData.branch_id || null,
    note: '',
    start_date_time: null,
    employee_id: props.bookingData.employee_id || null,
    status: 'pending',
    services_id: [],
    product_variation_id: [],
    is_paid: 0,
    package_id: []
  }
}

//  Reset Form
const setFormData = (data) => {
  IS_SUBMITED.value = false
  newService.value = false
  newSelectedServices.value = []
  packageApplied.value = false
  userPackage.value = []

  if (data.status == 'checkout') {
    store.updateStep('CHECK_OUT')
  }
  if (data.services !== undefined && data.services.length > 0) {
    selectedService.value = data.services
    getUserPackages(data.user_id)
  } else {
    resetServices()
  }

  if (data.products !== undefined && data.products.length > 0) {
    selectedProduct.value = data.products
  } else {
    resetProducts()
  }

  if (data.bookingPackages !== undefined && data.bookingPackages.length > 0) {
    selectPurchasePackages.value = data.bookingPackages
    if (data.userPackageServices) {
      selectPurchasePackages.value.forEach((packages) => {
        let userPackageService = data.userPackageServices.find((service) => service.package_id === packages.package_id)

        if (userPackageService && packages !== undefined) {
          if (Array.isArray(packages.services) && Array.isArray(data.userPackageServices)) {
            packages.services.forEach((service) => {
              data.userPackageServices.forEach((userService) => {
                if (userService.package_service.service_id === service.service_id) {
                  service.qty = userService.qty
                }
              })
            })

            packages.services = packages.services.filter((service) => {
              const matchedUserService = data.userPackageServices.find((userService) => userService.package_service.service_id === service.service_id)
              return matchedUserService && matchedUserService.qty > 0
            })
          }
        }
      })
    }
  } else {
    resetPurchasePackage()
    userPackage.value = null
  }
  if (data.packages !== undefined && data.packages.length > 0) {
    selectedPackage.value = data.packages
  } else {
    resetPackage()
  }
  resetForm({
    values: {
      id: data.id,
      branch_id: data.branch_id,
      note: data.note,
      start_date_time: data.start_date_time,
      employee_id: data.employee_id,
      user_id: data.user_id,
      status: data.status,
      services_id: data.services_id,
      is_paid: data.is_paid,
      product_variation_id: data.product_variation_id,
      package_id: data.package_id
    }
  })
}

// Emit Listner Functions
const externalFormCreation = (e) => {
  switch (e.type) {
    case 'create_customer':
      getCustomers(() => (user_id.value = e.value))
      break
  }
}

// Select Options
const singleSelectOption = ref({
  createOption: true,
  closeOnSelect: true,
  searchable: true
})

const multipleSelectOption = ref({
  mode: 'multiple',
  closeOnSelect: false,
  searchable: true
})

const branch = ref({ options: [], list: [] })
const employee = ref({ options: [], list: [] })
const customer = ref({ options: [], list: [] })
const service = ref({ options: [], list: [] })

const slots = ref([])

useOnOffcanvasHide('booking-form', () => setFormData(defaultData()))
useOnOffcanvasShow('booking-form', () => {
  useSelect({ url: BRANCH_LIST }, { value: 'id', label: 'name' }).then((data) => {
    branch.value = data
  })
  branch_id.value = props.bookingData.branch_id
  getCustomers()
  branchSelect(branch_id.value)
  getProducts()
  getpackages(branch_id.value)
})

const getCustomers = (cb) =>
  useSelect({ url: CUSTOMER_LIST }, { value: 'id', label: 'full_name' }).then((data) => {
    customer.value = data
    if (typeof cb == 'function') {
      cb()
    }
  })

const dateChange = () => {
  getSlots()
  start_date_time.value = null
}

const getSlots = () => {
  listingRequest({ url: SLOT_LIST, data: { branch_id: branch_id.value, date: current_date.value } }).then((res) => {
    if (res.status) {
      const now = new Date()
      slots.value = res.data.filter((slot) => {
        const slotDate = new Date(slot.value)
        return slotDate > now
      })
    }
  })
}
// On Select
const branchSelect = (value) => {
  useSelect({ url: EMPLOYEE_LIST, data: { branch_id: value } }, { value: 'id', label: 'name' }).then((data) => (employee.value = data))

  fetchHolidays(value)
  getSlots()
}

function fetchHolidays(value) {
  listingRequest({ url: IS_HOLIDAY, data: { branch_id: value } }).then((data) => {
    console.log(data)

    // Extract dates from the object
    holidays.value = Object.values(data.isHoliday)
      .map((day) => {
        const parsedDate = new Date(day.date)

        // Check if the parsed date is valid
        return isNaN(parsedDate.getTime()) ? null : parsedDate
      })
      .filter(Boolean)
    console.log(holidays.value)
  })
  // Assuming the API returns an array of { date }
}

const removeBranch = (value) => {
  employee_id.value = null
  start_date_time.value = null
  user_id.value = null
  selectedCustomer.value = null
  resetServices()
}
const employeeSelect = (value) => {
  useSelect({ url: SERVICE_LIST, data: { id: value, branch_id: branch_id.value } }, { value: 'service_id', label: 'service_name' }).then((data) => (service.value = data))
}
const removeEmployee = () => {
  resetServices()
}
const newCustomerData = ref(null)
const customerSelect = (value) => {
  getUserPackages(value)
  if (_.isString(value)) {
    newCustomerData.value = {
      first_name: value.split(' ')[0] || '',
      last_name: value.split(' ')[1] || ''
    }
    bootstrap.Modal.getOrCreateInstance($('#exampleModal')).show()
    user_id.value = null
  }
}
const slotSelect = () => {
  resetServiceTime()
}
const removeSlot = () => {
  resetServiceTime()
}

//  Customer Select & Unselect & Selected Values
const selectedCustomer = computed(() => customer.value.list.find((customer) => customer.id == user_id.value) ?? null)
const selectedEmployee = computed(() => employee.value.list.find((employee) => employee.id == employee_id.value) ?? null)

const removeCustomer = () => {
  user_id.value = null
  services_id.value = []
  selectedService.value = []
  userPackage.value = null
}

//------------------ Start:- Service Module Logic -----------------//
const selectedService = ref([])
const newSelectedServices = ref([])
const resetServices = () => {
  selectedService.value = []
  services_id.value = []
  newSelectedServices.value = []
}
const removeService = (id) => {
  const servicesIds = services_id.value
  services_id.value = servicesIds.filter((serviceid) => serviceid !== id)
  selectedService.value = selectedService.value.filter((BKservice) => BKservice.service_id !== id)
  newSelectedServices.value = newSelectedServices.value.filter((BKservice) => BKservice.service_id !== id)
  resetServiceTime()
}
const newService = ref(false)
const serviceInput = ref(null)
const addNewService = (value) => {
  newService.value = true
  setTimeout(() => {
    serviceInput.value.open()
  }, 100)
}

const serviceSelect = (value) => {
  const filteredService = service.value.list.find((ser) => ser.service_id == value)
  const bookingService = {
    id: null,
    start_date_time: null,
    service_name: filteredService.service_name,
    employee_id: employee_id.value,
    booking_id: null,
    service_id: value,
    branch_id: branch_id.value,
    service_price: filteredService.service_price,
    duration_min: filteredService.duration_min
  }
  selectedService.value.push(bookingService)
  newSelectedServices.value.push(bookingService)
  resetServiceTime()
  newService.value = false
}
const resetServiceTime = () => {
  let startTime = moment(start_date_time.value)
  selectedService.value.forEach((bookingService, index) => {
    if (index > 0) {
      const lastService = selectedService.value[index - 1]
      startTime = moment(lastService.start_date_time)
      startTime = startTime.add(lastService.duration_min, 'minutes')
    }
    bookingService.start_date_time = startTime.format('YYYY-MM-DD HH:mm:ss')
    selectedService.value[index] = bookingService
  })
}

//------------------ End:- Service Module Logic -----------------//

//------------------ Start:- Package Module Logic -----------------//

// redeem package
const packages = ref({ options: [], list: [] })
const selectedPackage = ref([])
const resetPackage = () => {
  selectedPackage.value = []
  package_id.value = []
}

const newPackage = ref(false)
const packageInput = ref(null)
const addNewPackage = (value) => {
  newPackage.value = true
  setTimeout(() => {
    packageInput.value.open()
  }, 100)
}

const getpackages = (branch_id) => {
  useSelect({ url: PACKAGE_LIST, data: { branch_id: branch_id } }, { value: 'id', label: 'name' }).then((data) => {
    packages.value = data
  })
}

const getUserPackages = (user_id) => {
  getRequest({ url: USER_PACKAGE_LIST, id: user_id }).then((data) => {
    if (branch_id.value) {
      userPackage.value = data.filter((pkg) => pkg.branch_id === branch_id.value)
    } else {
      userPackage.value = data
    }
  })
}

//------------------ End:- PackagePackage Module Logic -----------------//
//------------------ Start:- Product Module Logic -----------------//

const newProduct = ref(false)
const productInput = ref(null)
const addNewProduct = (value) => {
  newProduct.value = true
  setTimeout(() => {
    productInput.value.open()
  }, 100)
}

const resetProducts = () => {
  selectedProduct.value = []
  product_variation_id.value = []
}

const products = ref({ options: [], list: [] })
const getProducts = () => {
  useSelect({ url: PRODUCT_LIST }, { value: 'id', label: 'text' }).then((data) => {
    products.value = data
  })
}

const selectedProduct = ref([])

const selectProduct = (value) => {
  const filteredProduct = products.value.list.find((pr) => pr.id == value)

  const product_variation = JSON.parse(filteredProduct.extra_data)

  const bookingProduct = {
    id: null,
    product_name: filteredProduct.text,
    booking_id: id.value,
    employee_id: employee_id.value,
    order_id: null,
    product_id: product_variation.product_id,
    product_variation_id: value,
    product_price: product_variation.price,
    discounted_price: product_variation.discounted_price,
    discount_type: product_variation.discount_type,
    discount_value: product_variation.discount_value,
    product_qty: 1,
    variation_name: product_variation.variation_name,
    max_qty: product_variation.qty
  }
  selectedProduct.value.push(bookingProduct)
  newProduct.value = false
}

const removeProduct = (id) => {
  const productsIds = product_variation_id.value
  product_variation_id.value = productsIds.filter((productid) => productid !== id)
  selectedProduct.value = selectedProduct.value.filter((BKproduct) => BKproduct.product_variation_id !== id)
}

//------------------ End:- Product Module Logic -----------------//

const payment_data = ref(null)
const stripe_payment_data = ref(null)
const store = useBookingStore()
const SINLGE_STEP = computed(() => store.singleStep)
var SUB_TOTAL_SERVICE_AMOUNT = computed(() =>
  selectedService.value.reduce((total, service) => total + service.service_price, 0) +
  selectPurchasePackages.value.reduce((total, PurchasePackage) => total + PurchasePackage.package_price, 0) +
  selectedProduct.value.reduce((total, product) => total + (product.discounted_price ? product.discounted_price : product.product_price) * product.product_qty, 0) +
  selectedPackage.value.reduce((total, packages) => total + packages.package_price, 0) -
  (couponRedeem.value || 0) // Subtract coupon discount if it exists
)
const formSubmit = handleSubmit((values) => {
  if (!IS_SUBMITED.value) {
    IS_SUBMITED.value = true
    values['services'] = selectedService.value
    values['products'] = selectedProduct.value
    values['packages'] = selectedPackage.value
    values['purchase_packages'] = selectPurchasePackages.value
    if (id.value > 0) {
      updateRequest({ url: UPDATE_URL, id: id.value, body: values }).then((res) => {
        submiting_booking(res)
      })
    } else {
      storeRequest({ url: STORE_URL, body: values }).then((res) => {
        submiting_booking(res)
      })
    }
  }
})
const updateStatus = (data) => {
  setFormData(data)
  emit('onSubmit')
}
const submiting_booking = (res) => {
  IS_SUBMITED.value = false
  if (res.status) {
    window.successSnackbar(res.message)
    if (props.bookingType == 'CALENDER_BOOKING') {
      setFormData(res.data)
    } else {
      setFormData(defaultData())
      const elem = document.getElementById('booking-form')
      const form = bootstrap.Offcanvas.getOrCreateInstance(elem)
      form.hide()
      if (document.getElementById('booking-datatable') != null) {
        window.renderedDataTable.ajax.reload(null, false)
      }
    }
  } else {
    window.errorSnackbar(res.message)
  }
  emit('onSubmit')
}
const formSubmitCheckout = () => {
  if (!IS_SUBMITED.value) {
    const values = { services: selectedService.value, products: selectedProduct.value, packages: selectedPackage.value, packageApplied: selectedPackageService.value, purchase_package: selectPurchasePackages.value }

    IS_SUBMITED.value = true
    if (is_paid.value) {
      const data = {
        status: 'completed'
      }
      updateRequest({ url: UPDATE_STATUS, id: id.value, body: data }).then((res) => {
        if (res.status) {
          store.updateStep('MAIN')
          window.successSnackbar(res.message)
          updateStatus(res.data)
        }
      })
    } else {
      updateRequest({ url: CHECKOUT_URL, id: id.value, body: values }).then((res) => {
        if (res.status) {
          setFormData(res.data)
          submiting_booking(res)
          store.updateStep('PAYMENT')
        }
      })
    }
  }
}
const updatePaymentData = (data) => {
  payment_data.value = data
}
const formSubmitPaynow = () => {
  if (!IS_SUBMITED.value) {
    const values = { payment: payment_data.value, packageApplied: appliedServices.value }
    IS_SUBMITED.value = true
    updateRequest({ url: PAYMENT_PUT_URL, id: id.value, body: payment_data.value }).then((res) => {
      packageQtyReduce()

      switch (res.data.payment_method) {
        case 'razorpay':
          if (res.data.public_key != '') {
            openRazorpay(res.data)
          } else {
            window.errorSnackbar('Razorpay key does not exist')
            errorMessages.value = 'Razorpay key does not exist'
          }
          break

        case 'stripe':
          stripe_payment_data.value = {
            booking_transaction_id: res.data.booking_transaction_id,
            currency: res.data.currency,
            payment_method: res.data.payment_method,
            total_amount: res.data.total_amount
          }

          if (res.data.public_key != '') {
            openStripe(stripe_payment_data.value)
          } else {
            window.errorSnackbar('Stripe Secret key does not exist')
            errorMessages.value = 'Stripe Secret key does not exist'
          }

          break

        default:
          submiting_booking(res.data)
          setFormData(res.data.data)
          store.updateStep('MAIN')
          break
      }
    })
  }
}

const openRazorpay = (data) => {
  var options = {
    key: data.public_key, // Enter the Key ID generated from the Dashboard
    amount: data.total_amount * 100, // Amount is in currency subunits. Default currency is INR. Hence, 50000 refers to 50000 paise
    currency: data.currency,
    name: 'Acme Corp', //your business name
    description: 'Test Transaction',
    image: 'https://example.com/your_logo',

    handler: function (response) {
      response.razorpay_payment_id = response.razorpay_payment_id
      response.total_amount = data.total_amount
      response.currency = data.currency

      updateRequest({ url: UPDATE_PAYMENT_DATA, id: data.booking_transaction_id, body: { response } }).then((res) => {
        submiting_booking(res.data)
        setFormData(res.data.booking)
        store.updateStep('MAIN')
      })
    },

    notes: {
      address: 'Razorpay Corporate Office'
    },
    theme: {
      color: '#3399cc'
    }
  }
  var rzp1 = new Razorpay(options)
  rzp1.on('payment.failed', function (response) {
    window.errorSnackbar(response.error.description)
    errorMessages.value = response.error.description
  })

  rzp1.open()
}

const openStripe = (data) => {
  storeRequest({ url: STRIPE_PAYMENT_DATA, body: { data } }).then((res) => {
    if (res.status == true) {
      var newWindow = window.open(res.data_url, '_blank')
    } else {
      window.errorSnackbar(res.data.message)
      errorMessages.value = res.data.message
    }
  })
}

const packageApplied = ref(false)
const appliedServices = ref([])

var GRAND_TOTAL = computed(() => newSelectedServices.value.reduce((total, service) => total + service.service_price, 0) + selectedProduct.value.reduce((total, product) => total + (product.discounted_price ? product.discounted_price : product.product_price) * product.product_qty, 0) + selectedPackage.value.reduce((total, packages) => total + packages.package_price, 0))

const packageQtyReduce = () => {
  for (let singlePackage of packages.value.list) {
    for (let packageService of singlePackage.services) {
      for (let appliedServices of appliedServices.value)
        if (packageService.service_id === appliedServices.id && singlePackage.id === appliedServices.package_id) {
          packageService.quantity = packageService.quantity - 1
        }
    }
  }
  appliedServices.value = []
}

const packageCounts = computed(() => {
  const counts = {}
  for (let singlePackage of packages.value.list) {
    for (let packageService of singlePackage.services) {
      if (packageService.quantity > 0) {
        const serviceId = packageService.service_id
        counts[serviceId] = counts[serviceId] ? counts[serviceId] + 1 : 1
      }
    }
  }
  return counts
})

const singleService = ref(null)
const openModal = (service) => {
  singleService.value = service
  $('#exampleModal').modal('show')
}

const singleAppliedServicePrice = ref(null)
function singleAppliedService(service_id) {
  for (let singleServices of appliedServices.value) {
    if (service_id === singleServices.id) {
      singleAppliedServicePrice.value = singleServices.price
      return true
    }
  }
  return false
}

const selectPurchasePackages = ref([])
const selectPurchasePackageIds = ref([])
function purchasePackageModel() {
  $('#purchasePackageModel').modal('show')
  selectPurchasePackages.value.forEach((packages) => {
    selectPurchasePackageIds.value.push(packages.package_id)
  })
}

const isPackagePurchased = (packageId) => {
  return selectPurchasePackageIds.value.some((p) => p === packageId)
}

function purchasePackage(packagesId) {
  selectPurchasePackageIds.value = [packagesId]
}

const resetPurchasePackage = () => {
  selectPurchasePackages.value = []
}
function removePurchasePackageId(package_id) {
  let removePackageId = selectPurchasePackageIds.value.some((p) => p === package_id)
  if (removePackageId) {
    removePurchasePackage(package_id)
  }
  selectPurchasePackageIds.value = selectPurchasePackageIds.value.filter((packageId) => packageId !== package_id)
}

function removePurchasePackage(package_id) {
  selectPurchasePackages.value = selectPurchasePackages.value.filter((BKservice) => BKservice.package_id !== package_id)
}

function savePurchasePackage() {
  selectPurchasePackageIds.value.forEach((packagesId) => {
    const filteredPackage = packages.value.list.find((pa) => pa.id == packagesId)

    // Check if the package already exists in selectPurchasePackages
    const packageExists = selectPurchasePackages.value.some((packages) => packages.package_id === packagesId)

    if (!packageExists) {
      const bookingPackage = {
        id: null,
        name: filteredPackage.name,
        description: filteredPackage.description,
        user_id: user_id.value,
        employee_id: employee_id.value,
        booking_id: null,
        package_id: packagesId,
        package_price: filteredPackage.package_price,
        package_validity: filteredPackage.package_validity,
        services: filteredPackage.services,
        start_date: filteredPackage.start_date,
        end_date: filteredPackage.end_date,
        is_reclaim: false
      }
      selectPurchasePackages.value.push(bookingPackage)
      resetServices()
    }
  })

  $('#purchasePackageModel').modal('hide')
  selectPurchasePackageIds.value = []
}

function closeModel() {
  $('#purchasePackageModel').modal('hide')
  selectPurchasePackageIds.value = []
}

function openUserPackage() {
  $('#userPackage').modal('show')
}

const filteredUserPackages = computed(() => {
  if (userPackage.value && userPackage.value.length > 0) {
    return userPackage.value.filter(
      (packages) =>
        packages.services &&
        packages.services.some((service) =>
          selectedService.value.some((selected) => {
            if (selected.service_id === service.service_id) {
              service.employee_id = selected.employee_id // Push employee_id into the service
              return true
            }
            return false
          })
        )
    )
  }
  return []
})

function applyUserPackage() {
  $('#applyUserPackage').modal('show')
}

function isUserPackage(packageId) {
  if (!userPackage.value || userPackage.value.length === 0) {
    return false
  }
  return userPackage.value.some((userPackage) => userPackage.package_id === packageId)
}

const filteredPackages = computed(() => {
  const selectedServiceIds = selectedService.value.map((service) => service.service_id)
  if (selectedServiceIds.length === 0) {
    return packages.value.list.filter((pkg) => !isUserPackage(pkg.id))
  }
  const filtered = packages.value.list.filter((pkg) => !isUserPackage(pkg.id) && pkg.services && pkg.services.some((service) => selectedServiceIds.includes(service.service_id)))
  return filtered.length > 0 ? filtered : []
})

// Function to check if service is selected
function isServiceSelected(userPackageId, serviceId) {
  return tempSelectedPackageService.value.some((service) => service.user_package_id === userPackageId && service.service_id === serviceId)
}

// Function to update selected services
function updateSelectedServices(packageServices) {
  const exactMatchIndex = tempSelectedPackageService.value.findIndex((service) => service.user_package_id === packageServices.user_package_id && service.service_id === packageServices.service_id)
  if (exactMatchIndex !== -1) {
    tempSelectedPackageService.value.splice(exactMatchIndex, 1)
  } else {
    const serviceIdMatchIndex = tempSelectedPackageService.value.findIndex((service) => service.service_id === packageServices.service_id)
    if (serviceIdMatchIndex !== -1) {
      tempSelectedPackageService.value.splice(serviceIdMatchIndex, 1)
    }

    tempSelectedPackageService.value.push({
      ...packageServices,
      qty: packageServices.qty ?? 1
    })
  }
}
// Function to update service quantity
function updateServiceQty(index, packageServices, newQty) {
  const service = tempSelectedPackageService.value[index]
  if (service) {
    service.qty = newQty
  }
}

const isPackageServiceSelected = computed(() => {
  return (serviceId) => {
    return selectedPackageService.value.some((service) => service.service_id === serviceId)
  }
})

const PackageServiceSelectedPrice = computed(() => {
  return selectedPackageService.value.reduce((total, packageService) => total + (selectedService.value.some((service) => service.service_id === packageService.service_id) ? packageService.service_price : 0), 0)
})

function removeApplyPackageService(service_id) {
  selectedPackageService.value = selectedPackageService.value.filter((service) => service.service_id !== service_id)
}
// Function to save selected package services
function saveSelectedPackageService() {
  tempSelectedPackageService.value.forEach((packageService) => {
    const exists = selectedPackageService.value.some((service) => service.id === packageService.id)
    if (!exists) {
      selectedPackageService.value.push(packageService)
    }
  })
  tempSelectedPackageService.value = []
  $('#applyUserPackage').modal('hide')
}

// Function to cancel and reset selections
function cancelSelectedPackageService() {
  tempSelectedPackageService.value = []
  $('#applyUserPackage').modal('hide')
}

function displayPackageDuration(startDate, endDate) {
  const start = new Date(startDate)
  const end = new Date(endDate)

  const diffInMonths = (end.getFullYear() - start.getFullYear()) * 12 + (end.getMonth() - start.getMonth())
  const diffInDays = Math.floor((end - start) / (1000 * 60 * 60 * 24))
  let diffInHours = Math.floor((end - start) / (1000 * 60 * 60))
  if (diffInDays === 0) {
    const endOfDay = new Date(start)
    endOfDay.setHours(23, 59, 59, 999)
    diffInHours = Math.floor((endOfDay - start) / (1000 * 60 * 60)) + 1
  }
  if (diffInMonths > 0) {
    return `${diffInMonths} Mo`
  } else if (diffInDays > 0) {
    return `${diffInDays} Days`
  } else {
    return `${diffInHours} Hours`
  }
}

function redeemPackage(package_id) {
  if (userPackage.value || userPackage.value.length > 0) {
    const userPackagedata = userPackage.value.find((pa) => pa.package_id == package_id)
    selectPurchasePackages.value = []
    const bookingPackage = {
      id: null,
      name: userPackagedata.name,
      description: userPackagedata.description,
      user_id: user_id.value,
      employee_id: employee_id.value,
      booking_id: null,
      package_id: package_id,
      package_price: 0,
      package_validity: userPackagedata.package_validity,
      services: userPackagedata.services,
      start_date: userPackagedata.start_date,
      end_date: userPackagedata.end_date,
      is_reclaim: true
    }
    selectPurchasePackages.value.push(bookingPackage)
    $('#userPackage').modal('hide')
  }
}

watch(filteredUserPackages, () => {
  shownServiceIds.value = [] // Reset shownServiceIds when filteredUserPackages change
})

// Compute unique service IDs
const uniqueServiceIds = computed(() => {
  const serviceIds = new Set()
  filteredUserPackages.value.forEach((filteredPackage) => {
    if (filteredPackage.services) {
      filteredPackage.services.forEach((serviceItem) => {
        if (serviceItem.service_id) {
          serviceIds.add(serviceItem.service_id)
        }
      })
    }
  })
  return Array.from(serviceIds)
})

// Determine if the button should be shown
// Method to check if a service ID is in filtered packages
const isServiceInFilteredPackages = (serviceId) => {
  return uniqueServiceIds.value.includes(serviceId)
}
</script>

<style scoped>
.offcanvas {
  box-shadow: none;
}
.service-duration {
  position: absolute;
  /* padding: 2px 8px; */
  bottom: -16px;
  border-radius: 0;
  border-bottom-left-radius: 4px;
  border-bottom-right-radius: 4px;
  right: 0;
}

.border-br-radius-0 {
  border-bottom-right-radius: 0;
}

[dir='rtl'] .border-br-radius-0 {
  border-bottom-left-radius: 0;
}
.date-time {
  border-top: 1px solid var(--bs-border-color);
}
.date-time > div:not(:first-child) {
  border-left: 1px solid var(--bs-border-color);
}
.list-group-flush > .list-group-item {
  color: var(--bs-body-color);
}
.text-muted {
  text-decoration: line-through;
}
.iq-package {
  cursor: pointer;
}

.iq-card-package {
  background-color: #fcf2e3 !important;
}

.dark .iq-card-package {
  background-color: #2e2c2c !important;
}
.iq-package-list {
  .list-group-item {
    background-color: unset;
  }
}
.iq-purchase-package {
  height: 250px;
  overflow: auto;
}
.iq-purchase-package::-webkit-scrollbar {
  width: 8px; /* Width of the scrollbar */
}
.iq-purchase-package::-webkit-scrollbar-track {
  background: #f1f1f1; /* Color of the scrollbar track */
}

.iq-purchase-package::-webkit-scrollbar-thumb {
  background: #888; /* Color of the scrollbar thumb */
  border-radius: 4px; /* Rounded corners of the thumb */
}

.iq-purchase-package::-webkit-scrollbar-thumb:hover {
  background: #555; /* Color of the thumb on hover */
}
</style>
