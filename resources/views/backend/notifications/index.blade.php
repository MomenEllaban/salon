@extends('backend.layouts.app')

@section('title', __($module_action . ' ' . $module_title))

@section('content')
    <div class="card mb-4">
        <div class="card-body">

            <div class="row mt-4">
                <div class="col">
                    <table id="datatable" class="table table-bordered table-responsive">
                        <thead>
                            <tr>
                                <th>
                                    {{ __('notification.lbl_text') }}
                                </th>
                                <th>
                                    {{ __('notification.lbl_module') }}
                                </th>
                                <th>
                                    {{ __('notification.lbl_update') }}
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($$module_name as $module_name_singular)
                                <?php
                                $row_class = '';
                                $span_class = '';
                                if ($module_name_singular->read_at == '') {
                                    $row_class = 'bg-soft-gray';
                                    $span_class = 'fw-bold';
                                }
                                ?>
                                <tr class="{{ $row_class }}">
                                    <td>
                                            <span class="{{ $span_class }}">
                                                {{ $module_name_singular->data['subject'] }}
                                            </span>
                                    </td>
                                    <td>
                                        {{-- {{ $module_name_singular->data['data']['service_name'] ?? '-' }} --}}
                                        @if(!empty($module_name_singular->data['data']['service_name']))
                                        {{ $module_name_singular->data['data']['service_name'] ?? 'no service name'}}
                                    @else
                                        {{ $module_name_singular->data['data']['package_name'] ?? '-' }}
                                    @endif
                                    </td>
                                    <td>
                                        {{ $module_name_singular->updated_at->diffForHumans() }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">{{ __('No data found') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <div class="row">
                <div class="col-7">
                    <div class="float-left">
                        {{ __('Total') }} {{ $$module_name->total() }} {{ __($module_name) }}
                    </div>
                </div>
                <div class="col-5">
                    <div class="float-end">
                        {!! $$module_name->render() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
