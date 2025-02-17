<div style="width: 25%;">
  @if(count($data->services) > 1 ||  $data->bookedPackageService->count() > 1)
    <a class="badge bg-info text-white" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#service-detail-modal-{{ $data->id }}">Multiple</a>

    <!-- Modal -->
    <div class="modal fade" id="service-detail-modal-{{ $data->id }}" tabindex="-1" aria-labelledby="service-detail-modal-{{ $data->id }}-Label" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="service-detail-modal-{{ $data->id }}-Label">Service Details</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <table class="table table-bordered m-0">
              <thead>
                <tr>
                  <th>No.</th>
                  <th>Service Name</th>
                  <th>Price</th>
                  <th>Duration (Min)</th>
                </tr>
              </thead>
              <tbody>

                @foreach ($data->services as $key => $service)
                  <tr>
                    <td>{{ ++$key }}</td>
                    <td>{{ $service->service_name }}</td>
                    <td>{{ Currency::format($service->service_price ?? 0) }}</td>
                    <td>{{ $service->duration_min }}</td>
                  </tr>
                @endforeach

                @if (!is_null($data->bookedPackageService) && $data->bookedPackageService->isNotEmpty())
                @foreach ($data->bookedPackageService as $key => $service)
                  <tr>
                    <td>{{ ++$key + $data->services->count() }}</td>
                    <td>{{ $service->service_name }}</td>
                    <td>{{ Currency::format($service->packageService->service_price ?? 0) }}</td>
                    <td>{{ $service->packageService->services->duration_min }}</td>
                  </tr>
              @endforeach
                @endif
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    @elseif ( !is_null($data->services) && $data->services->isNotEmpty())
    <small class="badge bg-primary">{{ $data->services->first()->service_name ?? '-' }}</small>
    @elseif (!is_null($data->bookedPackageService) && $data->bookedPackageService->isNotEmpty())
        <small class="badge bg-primary">{{ $data->bookedPackageService->first()->service_name ?? '-' }}</small>
    @endif

  @if ($data->services->isEmpty() && $data->bookedPackageService->count() == 0)
    <small class="badge bg-primary">{{ '-' }}</small>
  @endif
</div>
