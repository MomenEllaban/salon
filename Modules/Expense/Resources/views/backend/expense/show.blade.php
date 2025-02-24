@extends('backend.layouts.app')


@section('title')
    {{ $module_action }} {{ $module_title }}
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <x-backend.section-header>
                <x-slot name="subtitle">
                    @lang(':module_name Management Dashboard', ['module_name' => Str::title($module_name)])
                </x-slot>
                <x-slot name="toolbar">
                    <a href="{{ route('backend.' . $module_name . '.index') }}" class="btn btn-secondary mt-1 btn-sm"
                        data-bs-toggle="tooltip" title="{{ $module_name }} List">
                        <i class="fas fa-list"></i> List
                    </a>
                </x-slot>
            </x-backend.section-header>

            <hr>

            <div class="row mt-4">
                <div class="col">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th>Amount</th>
                                    <td>{{ number_format($data->amount, 2) }} {{ $data->currency ?? 'USD' }}</td>
                                </tr>
                                <tr>
                                    <th>Reference Number</th>
                                    <td>{{ $data->reference_number }}</td>
                                </tr>
                                <tr>
                                    <th>Date</th>
                                    <td>{{ $data->date }}</td>
                                </tr>
                                <tr>
                                    <th>Note</th>
                                    <td>{{ $data->note ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        <span class="badge bg-{{ $data->status === 1 ? 'success' : 'danger' }}">
                                            {{ ucfirst( $data->status === 1 ? 'Paid' : 'Unpaid') }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Branch Name</th>
                                    <td>{{ $data->branch->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Manager Name</th>
                                    <td>{{ $data->manager->fullName ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Category</th>
                                    <td>{{ $data->expenseCategory->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Subcategory</th>
                                    <td>{{ $data->expenseSubCategory->name ?? 'N/A' }}</td>
                                </tr>
                                @if ($data->featureImage)
                                    <tr>
                                        <th>Receipt Image</th>
                                        <td>
                                            <img src="{{ asset($data->featureImage) }}" alt="Receipt Image"
                                                class="img-thumbnail" style="max-width: 300px;">
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer">
            <div class="row">
                <div class="col">
                    <small class="float-end text-muted">
                        Updated: {{ $data->updated_at->diffForHumans() }},
                        Created at: {{ $data->created_at->isoFormat('LLLL') }}
                    </small>
                </div>
            </div>
        </div>
    </div>
@endsection
