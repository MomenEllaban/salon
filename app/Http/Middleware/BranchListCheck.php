<?php

namespace App\Http\Middleware;

use App\Models\Branch;
use Closure;
use Illuminate\Http\Request;

class BranchListCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check()) {
            if (auth()->user()->hasRole('user')) {
                \Auth::logout();
                abort(403, 'Unauthorized action.');
            }
            $branchId = request()->session()->get('selected_branch');
            $branches = Branch::getAllBranches();
            $selected_branch = $branches->where('id', $branchId)->first();
            $auth = auth()->user();

            if (auth()->user()->hasRole('admin')) {
                if (str_contains($request->route()->getName(), 'backend.bookings')
                      && $request->route()->getName() !== 'backend.bookings.index_data'
                      && $request->route()->getName() !== 'backend.bookings.datatable_view'
                ) {
                    if (! isset($selected_branch) && count($branches) > 0) {
                        $selected_branch = $branches[0];
                    }
                }
            }

            if (auth()->user()->hasRole('employee')) {
                try {
                    $selected_branch = Branch::find(auth()->user()->branch->branch_id);
                } catch (\Exception $e) {
                    \Log::error($e->getMessage());
                }
            }

            $isSingleBranch = false;

            if (count($branches) == 1) {
                $isSingleBranch = true;
                $selected_branch = $branches[0];
            }

            $data = [
                'auth_user_branches' => $branches,
                'selected_branch' => $selected_branch,
                'selected_branch_id' => isset($selected_branch) ? $selected_branch->id : 0,
                'is_single_branch' => $isSingleBranch,
                'permissions' => auth()->user()->getAllPermissions()->pluck('name')->toArray(),
            ];

            $request->merge([
                'selected_session_branch_id' => isset($selected_branch) ? $selected_branch->id : null,
                'is_single_branch' => $isSingleBranch,
            ]);

            view()->share($data);
        }

        return $next($request);
    }
}
