<?php

namespace Modules\Employee\Http\Controllers\Backend\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Modules\Employee\Models\BranchEmployee;
use Modules\Employee\Models\EmployeeRating;
use Modules\Employee\Transformers\EmployeeDetailResource;
use Modules\Employee\Transformers\EmployeeResource;
use Modules\Employee\Transformers\EmployeeReviewResource;
use Modules\Service\Models\ServiceEmployee;

class EmployeeController extends Controller
{
    public function employeeList(Request $request)
    {
        $branchId = $request->input('branch_id');
        $perPage = $request->input('per_page', 10);

        $employee = User::role('employee')->with(['media', 'branches', 'services'])->where('status', 1);
        if ($branchId) {
            $employee = $employee->whereHas('branches', function ($query) use ($branchId) {
                $query->where('branch_id', $branchId);
            });
        }
        if (! empty($request->service_ids)) {
            $ids = ServiceEmployee::whereIn('service_id', explode(' ', $request->service_ids))->pluck('employee_id');
            $employee = $employee->whereIn('id', $ids);
        }
        if (! empty($request->order_by) && $request->order_by == 'top') {
            $employee = $employee->withCount('services')
                ->orderByDesc('services_count');
        }
        $employee = $employee->paginate($perPage);
        $responseData = EmployeeResource::collection($employee);

        return response()->json([
            'status' => true,
            'data' => $responseData,
            'message' => __('employee.employee_list'),
        ], 200);
    }

    public function employeeDetail(Request $request)
    {
        $branchId = $request->input('branch_id');
        $employeeId = $request->input('employee_id');

        if ($branchId && $employeeId) {
            // Fetch employee details based on both branch_id and employee_id
            $employee = User::role('employee')->with('media')->whereHas('branches', function ($query) use ($branchId) {
                $query->where('branch_id', $branchId);
            })->find($employeeId);
        } elseif ($branchId) {
            // Fetch employee details based on branch_id
            $employee = User::role('employee')->with('media')->whereHas('branches', function ($query) use ($branchId) {
                $query->where('branch_id', $branchId);
            })->first();
        } elseif ($employeeId) {
            // Fetch employee details based on employee_id
            $employee = User::role('employee')->with('media')->find($employeeId);
        } else {
            return response()->json(['status' => false, 'message' => __('employee.branch_employee_id')]);
        }

        if ($employee) {
            return response()->json(['status' => true, 'data' => new EmployeeDetailResource($employee), 'message' => __('employee.employee_detail')]);
        } else {
            return response()->json(['status' => false, 'message' => __('employee.employee_notfound')]);
        }
    }

    public function saveRating(Request $request)
    {
        $user = auth()->user();
        $rating_data = $request->all();
        $rating_data['user_id'] = $user->id;
        $result = EmployeeRating::updateOrCreate(['id' => $request->id], $rating_data);

        $message = __('employee.rating_update');
        if ($result->wasRecentlyCreated) {
            $message = __('employee.rating_add');
        }

        return response()->json(['status' => true, 'message' => $message]);
    }

    public function deleteRating(Request $request)
    {
        $user = auth()->user();
        $rating = EmployeeRating::where('id', $request->id)->where('user_id', $user->id)->first();
        if ($rating == null) {
            $message = __('employee.rating_notfound');

            return response()->json(['status' => false, 'message' => $message]);
        }
        $message = __('employee.rating_delete');
        $rating->delete();

        return response()->json(['status' => true, 'message' => $message]);
    }

    public function getRating(Request $request)
    {
        $employee_id = $request->employee_id;
        $perPage = $request->input('per_page');

        if (! empty($request->branch_id)) {
            $branch_employee = BranchEmployee::where('branch_id', $request->branch_id)->pluck('employee_id');
            $reviewsQuery = EmployeeRating::whereIn('employee_id', $branch_employee)->orderBy('updated_at', 'desc');
        } else {
            $reviewsQuery = EmployeeRating::where('employee_id', $employee_id)->orderBy('updated_at', 'desc');
        }

        if ($perPage === 'all') {
            $reviews = $reviewsQuery->get();
        } else {
            $reviews = $reviewsQuery->paginate($perPage);
        }
        $review = EmployeeReviewResource::collection($reviews);

        return response()->json([
            'status' => true,
            'data' => $review,
            'message' => __('employee.review_list'),
        ], 200);
    }
}
