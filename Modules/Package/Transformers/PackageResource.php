<?php

namespace Modules\Package\Transformers;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class PackageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {


        $filteredUserPackages = $this->userPackage->filter(function ($userPackage) use ($request) {
            return $userPackage->user->id == $request->user_id;
        });
        // Filter services with quantity greater than 0
        $filteredServices = $this->service->filter(function ($service) {
            return $service->qty > 0;
        });
        $uniqueUsers = $filteredUserPackages->unique(function ($userPackage) {
            return $userPackage->booking->user->id;
        });


         // Determine which services to include
         $services = [];
         if ($request->has('user_id')) {
             $userPackageServices = $filteredUserPackages->flatMap(function ($userPackage) {
                return $userPackage->userPackageServices->filter(function ($userPackageServices) {
                    return $userPackageServices->qty > 0;
                });
            });
            $services = UserPackageServiceResource::collection($userPackageServices);
           } else {
             $filteredServices = $this->service->filter(function ($service) {
                 return $service->qty > 0;
             });
             $services = PackageServiceResource::collection($filteredServices);
         }

            return [
            'id' => $this->id,
            'name' => $this->name,
            'package_image' => $this->media->pluck('original_url')->first(),
            'description' => $this->description,
            'branch_id' => $this->branch_id,
            'branch_name'=>$this->branch->name,
            'package_price' => $this->package_price,
            'status' => $this->status,
            'start_date'=>$this->start_date??'',
            'end_date'=>$this->end_date??'',
            'is_featured' => $this->is_featured,
            'services' => $services,
            'userPackage' => UserPackageResource::collection($filteredUserPackages),
            'user' => UserResource::collection($uniqueUsers),
            ];
    }
}
    class PackageServiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'package_id' => $this->package_id,
            'service_id' => $this->service_id,
            'qty' => $this->qty,
            'service_price' => $this->service_price,
            'discounted_price' => $this->discounted_price,
            'duration_min'=>$this->services->duration_min,
            'service_name' => $this->service_name,
        ];
    }
}

class UserPackageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'booking_id' => $this->booking_id,
            'employee_id' => $this->employee_id,
            'package_price' => $this->package_price,
            'package_id' => $this->package_id,
            'purchase_date'=>$this->purchase_date,
        ];
    }
}

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->booking->user->id,
            'first_name' => $this->booking->user->first_name,
            'last_name' => $this->booking->user->last_name,
            'email' => $this->booking->user->email,
            'mobile' => $this->booking->user->mobile,
            'gender' => $this->booking->user->gender,
        ];
    }
}


class UserPackageServiceResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'package_id' => $this->package_id,
            'service_id' => $this->packageService->service_id,
            'total_qty' => $this->packageService->qty,
            'remaining_qty' => $this->qty,
            'service_price' => $this->packageService->service_price,
            'discounted_price' => $this->packageService->discounted_price,
            'duration_min'=>$this->packageService->services->duration_min,
            'service_name' => $this->packageService->service_name,
        ];
    }
}


