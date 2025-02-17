<?php

namespace Modules\Package\Http\Controllers\Backend\API;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Package\Models\Package;
use Modules\Package\Models\PackageService;
use Modules\Service\Models\Service;
use Modules\Package\Models\UserPackage;
use Modules\Package\Transformers\UserPackagesResource;
use Modules\Package\Transformers\PackageResource;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Package\Trait\PackageTrait;

class PackagesController extends Controller
{
    use PackageTrait;
    
    public function Package(Request $request){
      $today = Carbon::today();
      $nextWeek = $today->copy()->addWeek();
      $data = Package::with('service','service.services','branch','media')->whereDate('end_date', '>=', $today);

      
         // Filter by service_id
         if ($request->has('service_id')) {
            $serviceId = explode(',', $request->service_id);
            $data->whereHas('service', function ($query) use ($serviceId) {
                $query->whereIn('service_id', $serviceId);
            });
        }

        // Filter by user_id
        if ($request->has('user_id') && $request->user_id != '') {
            $data = $data->whereHas('userPackage.booking', function ($q) use ($request) {
                $q->where('user_id', $request->user_id);
            })->with(['userPackage.booking.user', 'userPackage.userPackageServices.packageService']);

            // Check if any userPackageServices have quantity greater than 0
            $activePackage = $data->whereHas('userPackage.userPackageServices', function ($q) {
                $q->where('qty', '>', 0);
            })->exists();

            $data->whereHas('userPackage.bookingTransaction');
            if (!$activePackage) {
                return response()->json(['status' => true, 'data' => [], 'message' => 'No active package for this user' ], 200);
            }
        }else {
            // Normal get package API call - check for active status
            $data->where('status', 1);
        }
        // Filter by expiring_in_next_week
        if ($request->has('expiry')) {
            $data->whereHas('userPackage.package', function ($query) use ($nextWeek) {
                $query->where('end_date', '<=', $nextWeek);
            })->with(['userPackage.booking.user']);
        }
        $user_id = $request->user_id ?? null;

        // Check if there are any packages available
        if ($data->exists()) {
            $data=$data->get();
            $packageCollection = PackageResource::collection($data);
           // Send notification for packages that are about to expire
           if ($request->has('expiry')) {
           
            foreach ($data as $package) {
                $type = 'package_expiry';
                $messageTemplate = 'Your package #[[package_id]] is about to expire soon.';
                $notify_message = str_replace('[[package_id]]', $package->id, $messageTemplate);

                $this->sendPackageExpireNotification($type, $notify_message, $package, $user_id);
            }
        }

            return response()->json([
                'status' => true,
                'data' => $packageCollection,
                'message' => __('package.package_list'),
            ], 200);
        } else {
            $message = __('package.package_not_available');
            if ($request->has('service_id')) {
                $message = __('package.no_packages_with_service');
            } elseif ($request->has('user_id')) {
                $message = __('package.no_packages_for_user');
            } elseif ($request->has('expiring_in_next_week')) {
                $message = __('package.no_packages_expiring_soon');
            }

            return response()->json([
                'status' => true,
                'data' => [],
                'message' => $message,
            ], 200);
        }
        }
}
