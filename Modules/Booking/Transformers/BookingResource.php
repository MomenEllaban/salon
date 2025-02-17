<?php

namespace Modules\Booking\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Package\Models\UserPackageServices;

class BookingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $userPackageServices = [];
        $reclaimBookingPackages = $this->bookingPackages->filter(function ($bookingPackage) {
            return $bookingPackage->is_reclaim == 1;
        });
        foreach ($reclaimBookingPackages as $bookingPackage) {
            $userPackageServices = UserPackageServices::with('packageService')
                -> where('user_id', $bookingPackage->user_id)
                ->where('package_id', $bookingPackage->package_id)
                ->get();
        }

        return [
            'id' => $this->id,
            'note' => $this->note,
            'start_date_time' => $this->start_date_time,
            'branch_id' => $this->branch_id,
            'branch_name' => $this->branch->name,
            'employee_id' => optional($this->booking_service->first())->employee_id?? optional($this->bookingPackages->first())->employee_id?? '-',
            'employee_name' => optional($this->booking_service->first()?->employee)->full_name
            ?? optional($this->bookingPackages->first()?->employee)->full_name
            ?? default_user_name(),
            'services_id' => $this->services->pluck('service_id'),
            'services' => $this->services,
            'user_id' => $this->user_id,
            'user_name' => optional($this->user)->full_name ?? default_user_name(),
            'user_profile_image' => optional($this->user)->profile_image ?? default_user_avatar(),
            'user_created' => optional($this->user)->created_at ?? '-',
            'status' => $this->status,
            'is_paid' => $this->payment->payment_status ?? 0,
            'created_by_name' => optional($this->createdUser)->full_name ?? default_user_name(),
            'updated_by_name' => optional($this->updatedUser)->full_name ?? default_user_name(),
            'created_at' => date('d, M Y', strtotime($this->created_at)),
            'updated_at' => date('d, M Y', strtotime($this->updated_at)),
            'payment' => $this->payment,
            'products' => $this->products,
            'bookingPackages' => $this->bookingPackages,
            'userPackageServices' => $userPackageServices,
            'product_variation_id' => optional($this->products)->pluck('product_variation_id'),
            'coupon_redeem' => $this->userCouponRedeem->discount ?? 0 
                    ];
    }
}
