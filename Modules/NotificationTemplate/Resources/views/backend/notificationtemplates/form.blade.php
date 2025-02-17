@section('title')
    {{ __($module_action) }} {{ __($module_title) }}
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-block card-stretch">
                <div class="card-body p-0">
                    <div class="d-flex justify-content-between align-items-center p-3 flex-wrap gap-3">
                        <h5 class="font-weight-bold">{{ $pageTitle ?? __('messages.edit') }}</h5>
                        <a href="{{ route('backend.notification-templates.index') }}"
                            class="float-right btn btn-sm btn-primary"><i class="fa fa-angle-double-left"></i>
                            {{ __('messages.back') }}</a>

                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            {{ Form::model($data, ['route' => ['backend.notification-templates.update', $data->id], 'method' => 'patch', 'button-loader' => 'true']) }}
            {{ Form::hidden('id', null) }}
            {{ Form::hidden('type', $data->type ?? null) }}
            {{ Form::hidden('defaultNotificationTemplateMap[template_id]', $data->id ?? null) }}

            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>{{ __('Type') }} : <span class="text-danger">*</span></label>
                        <select name="type" class="form-control select2js" id="type"
                            data-ajax--url="{{ route('backend.notificationtemplates.ajax-list', ['type' => 'constants_key', 'data_type' => 'notification_type']) }}"
                            data-ajax--cache="true" required disabled>
                            @if (isset($data->type))
                                <option value="{{ $data->type }}" selected>{{ $data->constant->name ?? '' }}</option>
                            @endif
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label>{{ __('To') }} : <span class="text-danger">*</span></label><br>
                        <select name="to[]" id="toSelect" class="form-control select2"
                            data-ajax--url="{{ route('backend.notificationtemplates.ajax-list', ['type' => 'constants_key', 'data_type' => 'notification_to']) }}"
                            data-ajax--cache="true" multiple required>
                            @if (isset($data) && $data->to != null)
                                @foreach (json_decode($data->to) as $to)
                                    <option value="{{ $to }}" selected>{{ $to }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        @php
                            $toValues = json_decode($data->to, true) ?? [];
                        @endphp
                        {{ Form::label('user_type', __('messages.user_type') . ' <span class="text-danger">*</span>', ['class' => 'form-control-label'], false) }}
                        {{ Form::select('defaultNotificationTemplateMap[user_type]', [], null, ['id' => 'userTypeSelect', 'class' => 'form-control select2js', 'required']) }}
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label" for="status">{{ __('messages.status') }} :</label>
                        <select class="form-control" name="status" id="status">
                            <option value="1" {{ $data->status == 1 ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ $data->status == 0 ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-12 mt-3">
                    <div class="form-group">
                        <label>{{ __('Parameters') }} :</label><br>
                        <div class="main_form">
                            @if (isset($buttonTypes))
                                @include(
                                    'notificationtemplate::backend.notificationtemplates.perameters-buttons',
                                    ['buttonTypes' => $buttonTypes]
                                )
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mt-3">
                    <div class="form-group mb-3">
                        <h4>{{ __('messages.notification_template') }}</h4>
                    </div>
                    <div class="form-group">
                        <label class="float-left">{{ __('messages.subject') }} :</label>
                        {{ Form::text('defaultNotificationTemplateMap[notification_subject]', null, ['class' => 'form-control']) }}
                    </div>
                    <div class="form-group">
                        <label>{{ __('messages.template') }} :</label>
                        {{ Form::hidden('defaultNotificationTemplateMap[language]', 'en') }}
                        {{ Form::textarea('defaultNotificationTemplateMap[notification_template_detail]', null, ['class' => 'form-control textarea tinymce-template', 'id' => 'mytextarea_mail']) }}
                    </div>
                </div>

                <div class="col-md-6 mt-3">
                    <div class="form-group mb-3">
                        <h4>{{ __('messages.mail_template') }}</h4>
                    </div>
                    <div class="form-group">
                        <label class="float-left">{{ __('messages.subject') }} :</label>
                        {{ Form::text('defaultNotificationTemplateMap[subject]', null, ['class' => 'form-control']) }}
                        {{ Form::hidden('defaultNotificationTemplateMap[status]', 1, ['class' => 'form-control']) }}
                    </div>
                    <div class="form-group">
                        <label>{{ __('messages.template') }} :</label>
                        {{ Form::hidden('defaultNotificationTemplateMap[language]', 'en') }}
                        {{ Form::textarea('defaultNotificationTemplateMap[template_detail]', null, ['class' => 'form-control textarea tinymce-template', 'id' => 'mytextarea']) }}
                    </div>

                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 pt-2">
                <button id="saveButton" type="submit" class="btn btn-primary">
                    <span id="buttonText">
                        <i class="far fa-save"></i> {{ __('save') }}
                    </span>
                    <span id="loader" class="d-none">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Loading...
                    </span>
                </button>
                
                {{-- <button type="submit" class="btn btn-primary"><i class="far fa-save"></i> {{ __('save') }}<i
                        class="md md-lock-open"></i></button> --}}
                <button onclick="window.history.back();" class="btn btn-outline-primary"><i
                        class="fa-solid fa-angles-left"></i> {{ __('close') }}<i class="md md-lock-open"></i></button>
            </div>
        </div>
    </div>
@endsection

@push('after-scripts')
    <script>
         $(document).ready(function() {
            // Initialize TinyMCE
            const saveButton = $("#saveButton");
            const buttonText = $("#buttonText");
            const loader = $("#loader");

        // Disable save button on form submission
        $("form").on("submit", function () {
            saveButton.prop("disabled", true);
            buttonText.addClass("d-none");
            loader.removeClass("d-none");
        });

            (function($) {
                $(document).ready(function() {
                    tinymceEditor('.tinymce-templates', ' ', function(ed) {
                    }, 450)
                });

            })(jQuery);

            // Initialize Select2
            $('.select2js').select2();
            $('.select2-tag').select2({
                tags: true,
                createTag: function(params) {
                    if (params.term.length > 2) {
                        return {
                            id: params.term,
                            text: params.term,
                            newTag: true
                        };
                    }
                    return null;
                }
            });


            // Handle change event for 'user_type' select
            $('select[name="defaultNotificationTemplateMap[user_type]"]').on('change', function() {
                var userType = $(this).val();
                var type = $('select[name="type"]').val();
                $.ajax({
                    url: "{{ route('backend.notificationtemplates.fetchnotification_data') }}",
                    method: "GET",
                    data: {
                        user_type: userType,
                        type: type
                    },
                    success: function(response) {
                        if (response.success) {
                            var data = response.data;
                            $("input[name='defaultNotificationTemplateMap[subject]']").val(data
                                .subject);
                            tinymce.get('mytextarea').setContent(data.template_detail || '');
                            $("input[name='defaultNotificationTemplateMap[notification_message]']")
                                .val(data.notification_message);
                            $("input[name='defaultNotificationTemplateMap[notification_link]']")
                                .val(data.notification_link);

                            $("input[name='defaultNotificationTemplateMap[notification_subject]']").val(
                                data.notification_subject || '');
                            tinymce.get('mytextarea_mail').setContent(data
                                .notification_template_detail || '');
                        } else {
                            $("input[name='defaultNotificationTemplateMap[subject]']").val('');
                            tinymce.get('mytextarea').setContent('');
                            $("input[name='defaultNotificationTemplateMap[notification_message]']")
                                .val('');
                            $("input[name='defaultNotificationTemplateMap[notification_link]']")
                                .val('');

                            $("input[name='defaultNotificationTemplateMap[notification_subject]']").val(
                                '');
                            tinymce.get('mytextarea_mail').setContent('');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });
            });

            var toSelect = $('#toSelect');
            var userTypeSelect = $('#userTypeSelect');

            function updateUserTypeOptions(selectedValues) {
                userTypeSelect.empty();
                if (selectedValues) {
                    selectedValues.forEach(function(value) {
                        userTypeSelect.append(new Option(value, value));
                    });
                }
                userTypeSelect.trigger('change');
            }

            var initialSelectedValues = toSelect.val();
            updateUserTypeOptions(initialSelectedValues);

            toSelect.on('change', function() {
                var selectedValues = $(this).val();
                updateUserTypeOptions(selectedValues);
            });
        });
    </script>
@endpush
