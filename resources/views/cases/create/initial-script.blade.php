<script>
    const inputsa1 = document.querySelector('#section-a-1-phone_number');

    const itisa1 = window.intlTelInput(inputsa1, {
        initialCountry: "{{  Helper::getIso2ByDialCode($introducer['dial_code'] ?? null)  }}",
        separateDialCode:true,
        nationalMode:false,
        preferredCountries: @json(\App\Models\Country::select('iso2')->pluck('iso2')->toArray()),
        utilsScript: "{{ asset('assets/js/intel-tel-2.min.js') }}"
    });

    inputsa1.addEventListener("countrychange", function() {
        if (itisa1.isValidNumber()) {
            $('#section-a-1-dial_code').val(itisa1.s.dialCode);
        }
    });
    inputsa1.addEventListener('keyup', () => {
        if (itisa1.isValidNumber()) {
            $('#section-a-1-dial_code').val(itisa1.s.dialCode);
        }
    });
</script>

<script>
    let autoSaveTimers = {};
    let isSaving = {};
    let lastSavedData = {};

    function showSavingStatus(status, message = '') {
        const container = $('#saving-container');

        switch (status) {
            case 'saving':
                container.html(`
            <div class="state-item">
                <div class="save-indicator saving">
                    <div class="spinner"></div>
                    <span>Saving...</span>
                </div>
            </div>
        `);
                break;
            case 'saved':
                container.html(`
            <div class="state-item">
                <div class="save-indicator saved">
                    <svg class="checkmark" viewBox="0 0 24 24">
                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
                    </svg>
                    <span>Changes saved at ${message}</span>
                </div>
            </div>
        `);
                break;
            case 'error':
                container.html(`
            <div class="state-item">
                <div class="save-indicator error">
                    <svg class="error-icon" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.58L19 8l-9 9z"/>
                        <path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z" fill="#d93025"/>
                    </svg>
                    <span>Unable to save</span>
                </div>
            </div>
        `);
                break;
        }
    }

    function collectFormData(sectionId) {
        const formData = {};
        const form = $(`#form-${sectionId}`);

        if (form.length) {

            if (sectionId == 'section-a-2' || sectionId == 'section-f-3' || sectionId == 'section-f-4' || sectionId ==
                'section-f-5' || sectionId == 'section-f-6' || sectionId == 'section-e-1') {
                $(form).serializeArray().forEach(({
                    name,
                    value
                }) => {
                    const keys = name.match(/[^\[\]]+/g);

                    keys.reduce((acc, key, i) => {
                        if (i === keys.length - 1) {
                            acc[key] = value;
                        } else {
                            acc[key] = acc[key] || {};
                        }
                        return acc[key];
                    }, formData);
                });
            } else if (sectionId == 'section-a-1') {

                $(form).serializeArray().forEach(({
                    name,
                    value
                }) => {
                    const keys = name.match(/[^\[\]]+/g);

                    keys.reduce((acc, key, i) => {
                        if (i === keys.length - 1) {
                            acc[key] = value;
                        } else {
                            acc[key] = acc[key] || {};
                        }
                        return acc[key];
                    }, formData);
                });

                formData.section_a_1_dial_code = itisa1.s.dialCode;
                

            } else if (sectionId == 'section-b-1' || sectionId == 'section-b-2') {
                $(form).serializeArray().forEach(({ name, value }) => {
                    const keys = name.match(/[^\[\]]+/g);

                    keys.reduce((acc, key, i) => {
                        if (i === keys.length - 1) {
                            if (Array.isArray(acc[key])) {
                                acc[key].push(value);
                            } else if (acc[key]) {
                                acc[key] = [acc[key], value];
                            } else {
                                acc[key] = value;
                            }
                        } else {
                            acc[key] = acc[key] || {};
                        }
                        return acc[key];
                    }, formData);
                });
            } else {
                form.find('input, select, textarea').each(function() {
                    const $field = $(this);
                    const fieldName = $field.attr('name');
                    const fieldType = $field.attr('type');

                    if (fieldName) {
                        let value = '';

                        if (fieldType === 'radio') {
                            value = form.find(`input[name="${fieldName}"]:checked`).val() || '';
                        } else if (fieldType === 'checkbox') {
                            value = $field.is(':checked') ? $field.val() : '';
                        } else {
                            value = $field.val() || '';
                        }

                        formData[fieldName] = value;
                    }
                });

                form.find('select').each(function() {
                    const $select = $(this);
                    const selectId = $select.attr('id');
                    if (selectId && $select.hasClass('select2-hidden-accessible')) {
                        const select2Value = $select.select2('val');
                        if (select2Value) {
                            formData[`${selectId}_select2`] = select2Value;
                        }
                    }
                });
            }
        }

        return formData;
    }

    function hasDataChanged(sectionId) {
        const currentData = JSON.stringify(collectFormData(sectionId));
        const lastData = JSON.stringify(lastSavedData[sectionId] || {});
        return currentData !== lastData;
    }

    function performAutoSave(sectionId) {
        if (isSaving[sectionId] || !hasDataChanged(sectionId)) {
            return;
        }

        isSaving[sectionId] = true;
        showSavingStatus('saving');

        const formData = collectFormData(sectionId);
        const policyId = currentCaseId;

        $.ajax({
            url: '{{ route('case.auto-save') }}',
            type: 'POST',
            data: {
                policy: policyId,
                section: sectionId,
                data: formData,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    lastSavedData[sectionId] = formData;
                    showSavingStatus('saved', response.timestamp);
                } else {
                    showSavingStatus('error');
                }
            },
            error: function(xhr) {
                showSavingStatus('error');
            },
            complete: function() {
                isSaving[sectionId] = false;
            }
        });
    }

    function initAutoSave(sectionId, autoSaveDelay = 3000) {
        if (!autoSaveTimers[sectionId]) {
            autoSaveTimers[sectionId] = null;
        }
        if (!isSaving[sectionId]) {
            isSaving[sectionId] = false;
        }
        if (!lastSavedData[sectionId]) {
            lastSavedData[sectionId] = {};
        }

        $(document).on('input change',
            `#form-${sectionId} input, #form-${sectionId} select, #form-${sectionId} textarea`,
            function() {
                if (autoSaveTimers[sectionId]) {
                    clearTimeout(autoSaveTimers[sectionId]);
                }

                autoSaveTimers[sectionId] = setTimeout(function() {
                    performAutoSave(sectionId);
                }, autoSaveDelay);
            });

        $(document).on('select2:select select2:unselect', `#form-${sectionId} select`, function() {
            if (autoSaveTimers[sectionId]) {
                clearTimeout(autoSaveTimers[sectionId]);
            }

            autoSaveTimers[sectionId] = setTimeout(function() {
                performAutoSave(sectionId);
            }, autoSaveDelay);
        });

        lastSavedData[sectionId] = collectFormData(sectionId);
    }

    $(document).ready(function() {
        initAutoSave('section-a-1');

        const sections = [
            'section-a-2', 'section-b-1', 'section-b-2', 'section-a-1',
            'section-g-2', 'section-e-1', 'section-e-2', 'section-e-3',
            'section-e-4', 'section-f-1', 'section-f-2', 'section-f-3',
            'section-f-4', 'section-f-5', 'section-f-6', 'section-f-7',
            'section-g-1'
        ];

        sections.forEach(function(sectionId) {
            if ($(`#form-${sectionId}`).length) {
                initAutoSave(sectionId);
            }
        });
    });

    function triggerAutoSave(sectionId) {
        if (hasDataChanged(sectionId)) {
            performAutoSave(sectionId);
        }
    }

    function clearAutoSaveTimer(sectionId) {
        if (autoSaveTimers[sectionId]) {
            clearTimeout(autoSaveTimers[sectionId]);
            autoSaveTimers[sectionId] = null;
        }
    }
</script>

<script>
    let currentCaseId = "{{ $policy?->id }}";

    $(document).ready(function() {
        $('.each-options').click(function() {
            $('.case-section').addClass('d-none');

            var section = $(this).data('section');
            $(`#${section}`).removeClass('d-none');
        });
    });
</script>
{{-- Section C --}}
<script>
    $(document).ready(function() {
        $(document).on('click', '.section-b-1-add', function() {
            let newRow = `<div class="row mb-3 section-b-1-country-tax-residence-row">
            <label class="col-sm-3 col-form-label">Countries of Tax Residence:</label>
            <div class="col-sm-7">
                <input type="text" class="form-control section-b-1-country-tax-residence" name="all_countries[]" required>
            </div>
            <div class="col-sm-2">
                <button type="button" class="btn btn-success section-b-1-add">+</button>
                <button type="button" class="btn btn-danger section-b-1-remove">-</button>
            </div>
        </div>`;
            $('.section-b-1-country-tax-residence-row:last').after(newRow);
        });

        $(document).on('click', '.section-b-1-remove', function() {
            if ($('.section-b-1-country-tax-residence-row').length > 1) {
                $(this).closest('.section-b-1-country-tax-residence-row').remove();
            }
        });
    });
</script>
{{-- Section D --}}
<script>
    $(document).ready(function() {
        $(document).on('click', '.section-b-2-add', function() {
            let newRow = `<div class="row mb-3 section-b-2-country-tax-residence-row">
            <label class="col-sm-3 col-form-label">Countries of Tax Residence:</label>
            <div class="col-sm-7">
                <input type="text" class="form-control section-b-2-country-tax-residence" name="all_countries[]" required>
            </div>
            <div class="col-sm-2">
                <button type="button" class="btn btn-success section-b-2-add">+</button>
                <button type="button" class="btn btn-danger section-b-2-remove">-</button>
            </div>
        </div>`;
            $('.section-b-2-country-tax-residence-row:last').after(newRow);
        });

        $(document).on('click', '.section-b-2-remove', function() {
            if ($('.section-b-2-country-tax-residence-row').length > 1) {
                $(this).closest('.section-b-2-country-tax-residence-row').remove();
            }
        });
    });
</script>
{{-- Section D --}}
<script>
    $(document).ready(function() {
        $(document).on('click', '.section-c-1-add', function() {
            let newRow = `<div class="row section-c-1-country-tax-residence-row">
                <div class="col-sm-10">
                    <input type="text" class="form-control section-c-1-country-tax-residence" name="all_countries[]" >
                </div>
                <div class="col-sm-2">
                    <button type="button" class="btn btn-success section-c-1-add">+</button>
                    <button type="button" class="btn btn-danger section-c-1-remove">-</button>
                </div>
            </div>`;
            $('.section-c-1-country-tax-residence-row:last').after(newRow);
        });

        $(document).on('click', '.section-c-1-remove', function() {
            if ($('.section-c-1-country-tax-residence-row').length > 1) {
                $(this).closest('.section-c-1-country-tax-residence-row').remove();
            }
        });
    });
</script>
<script>
    $(document).ready(function() {

        $('#section-b-2-country_id, #section-b-2-country_issuance, #section-b-2-country_legal_residence, .section-b-2-countries-tax')
            .select2({
                allowClear: true,
                placeholder: 'Select country',
                width: '100%',
                theme: 'classic',
                ajax: {
                    url: "{{ route('country-list') }}",
                    type: "POST",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            searchQuery: params.term,
                            page: params.page || 1,
                            _token: "{{ csrf_token() }}"
                        };
                    },
                    processResults: function(data, params) {
                        params.page = params.page || 1;
                        return {
                            results: $.map(data.items, function(item) {
                                return {
                                    id: item.id,
                                    text: item.text
                                };
                            }),
                            pagination: {
                                more: data.pagination.more
                            }
                        };
                    },
                    cache: true
                }
            });

        $('#section-b-2-state_id').select2({
            allowClear: true,
            placeholder: 'Select state',
            theme: 'classic',
            width: '100%',
            ajax: {
                url: "{{ route('city-list') }}",
                type: "POST",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        searchQuery: params.term,
                        page: params.page || 1,
                        state_id: $('#country_id').val(),
                        _token: "{{ csrf_token() }}"
                    };
                },
                processResults: function(data, params) {
                    params.page = params.page || 1;
                    return {
                        results: $.map(data.items, function(item) {
                            return {
                                id: item.id,
                                text: item.text
                            };
                        }),
                        pagination: {
                            more: data.pagination.more
                        }
                    };
                },
                cache: true
            }
        });

        $('#section-b-2-city_id').select2({
            allowClear: true,
            placeholder: 'Select city',
            theme: 'classic',
            width: '100%',
            ajax: {
                url: "{{ route('city-list') }}",
                type: "POST",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        searchQuery: params.term,
                        page: params.page || 1,
                        state_id: $('#state_id').val(),
                        _token: "{{ csrf_token() }}"
                    };
                },
                processResults: function(data, params) {
                    params.page = params.page || 1;
                    return {
                        results: $.map(data.items, function(item) {
                            return {
                                id: item.id,
                                text: item.text
                            };
                        }),
                        pagination: {
                            more: data.pagination.more
                        }
                    };
                },
                cache: true
            }
        });

        $(document).on('click', '.section-b-2-add-tax', function() {
            var row = $(this).closest('.section-b-2-tax-residence-row').clone();
            row.find('select').val('').trigger('change');
            $('#section-b-2-tax-residence-wrapper').append(row);
        });

        $(document).on('click', '.section-b-2-remove-tax', function() {
            if ($('.section-b-2-tax-residence-row').length > 1) {
                $(this).closest('.section-b-2-tax-residence-row').remove();
            }
        });



    });
</script>
{{-- Section E 1 --}}
<script>
    $(document).ready(function() {
        $('#section-e-1-type').select2({
            allowClear: true,
            placeholder: 'Select Type',
            theme: 'classic',
            width: '100%'
        }).on('change', function() {
            let selectedValue = $('option:selected', this).val();

            $('.all-cbox').addClass('d-none');
            if (selectedValue) {
                $(`#container-for-ommit-documents-e1-${selectedValue}`).removeClass('d-none');
            }
        });
    });
</script>
{{-- Section F 1 --}}
<script>
    $(document).ready(function() {
        $('#s-f-1-purpose').select2({
            placeholder: 'Select Purpose',
            theme: 'classic',
            width: '100%'
        });

        $('#sg2-date').datepicker({
            dateFormat: 'yy-mm-dd',
            // maxDate: '-1d',
            changeMonth: true,
            changeYear: true,
            yearRange: '-100:+0'
        });

        $('#sg1date').datepicker({
            dateFormat: 'yy-mm-dd',
            // maxDate: '-1d',
            changeMonth: true,
            changeYear: true,
            yearRange: '-100:+0'
        });

        $('#f3d1').datepicker({
            dateFormat: 'yy-mm-dd',
            // maxDate: '-1d',
            changeMonth: true,
            changeYear: true,
            yearRange: '-100:+0'
        });

        $('#f3d2').datepicker({
            dateFormat: 'yy-mm-dd',
            // maxDate: '-1d',
            changeMonth: true,
            changeYear: true,
            yearRange: '-100:+0'
        });

        $('#f3d3').datepicker({
            dateFormat: 'yy-mm-dd',
            // maxDate: '-1d',
            changeMonth: true,
            changeYear: true,
            yearRange: '-100:+0'
        });

        $('#dob').datepicker({
            dateFormat: 'yy-mm-dd',
            // maxDate: '-1d',
            changeMonth: true,
            changeYear: true,
            yearRange: '-100:+0'
        });

        $('.secction-b-2-date-birth').datepicker({
            dateFormat: 'yy-mm-dd',
            // maxDate: '-1d',
            changeMonth: true,
            changeYear: true,
            yearRange: '-100:+0'
        });

        $('.section-f-7-date-1').datepicker({
            dateFormat: 'yy-mm-dd',
            // maxDate: '-1d',
            changeMonth: true,
            changeYear: true,
            yearRange: '-100:+0'
        });

        $('.section-f-7-date-2').datepicker({
            dateFormat: 'yy-mm-dd',
            // maxDate: '-1d',
            changeMonth: true,
            changeYear: true,
            yearRange: '-100:+0'
        });

        $('.section-f-7-date-3').datepicker({
            dateFormat: 'yy-mm-dd',
            // maxDate: '-1d',
            changeMonth: true,
            changeYear: true,
            yearRange: '-100:+0'
        });

        $('#c1_date_of_birth').datepicker({
            dateFormat: 'yy-mm-dd',
            // maxDate: '-1d',
            changeMonth: true,
            changeYear: true,
            yearRange: '-100:+0'
        });

        $('#d-1-dob').datepicker({
            dateFormat: 'yy-mm-dd',
            // maxDate: '-1d',
            changeMonth: true,
            changeYear: true,
            yearRange: '-100:+0'
        });

        $('#policy_holder_id').select2({
            placeholder: 'Select Policy Holder',
            allowClear: true,
            theme: 'classic',
            width: '100%',
            ajax: {
                url: "{{ route('holder-list') }}",
                type: "POST",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        searchQuery: params.term,
                        page: params.page || 1,
                        _token: "{{ csrf_token() }}",
                        roles: ['policy-holder'],
                        addNewOption: 1,
                        includeUserData: true
                    };
                },
                processResults: function(data, params) {
                    params.page = params.page || 1;

                    return {
                        results: $.map(data.items, function(item) {
                            return {
                                id: item.id,
                                text: item.text,
                                user: item.user
                            };
                        }),
                        pagination: {
                            more: data.pagination.more
                        }
                    };
                },
                cache: true
            },
            templateResult: function(data) {
                if (data.loading) {
                    return data.text;
                }

                var $result = $('<span></span>');
                $result.text(data.text);
                return $result;
            }
        }).on('change', function() {
            var selectedData = $('#policy_holder_id').select2('data');
            if (selectedData.length > 0) {
                var userData = selectedData[0].user;
                var shouldEmpty = false;
                
                if (userData) {
                    $('#stts-entity').attr('checked', userData.type === 'entity');
                    $('#stts-individual').attr('checked', userData.type === 'individual');

                    $('.section-b-1-policyholder-name').val(userData.name || '');
                    $('.section-b-1-controlling-person').val(userData.name || '');
                    $('.section-b-1-place-birth').val(userData.place_of_birth || '');
                    $('.section-b-1-date-birth').val(userData.dob || '');
                    $('.section-b-1-country').val(userData.country || '')
                    $('.section-b-1-city').val(userData.city || '');
                    $('.section-b-1-zip').val(userData.zipcode || '');
                    $('.section-b-1-address').val(userData.address_line_1 || '');

                    if (userData.status === 'other') {
                        $('.section-b-1-other-status').val(userData.other_status || '');
                        $('.section-b-1-other-status').removeClass('d-none');
                    } else {
                        $('.section-b-1-other-status').addClass('d-none');
                        $('.section-b-1-other-status').val('');
                        $(`#stts-${userData.status}`).attr('checked', true);
                    }

                    $('#stts-male').attr('checked', userData.gender === 'male');
                    $('#stts-female').attr('checked', userData.gender === 'female');    

                    $('.section-b-1-nationality').val(userData.national_country_of_registration);
                    $('.section-b-1-legal-residence').val(userData.country_of_legal_residence);
                    $('.section-b-1-passport').val(userData.passport_number);
                    $('.section-b-1-passport-issue-country').val(userData.country_of_issuance);
                    $('.section-b-1-tin').val(userData.tin);
                    $('.section-b-1-lei').val(userData.lei);
                    $('.section-b-1-email').val(userData.email);
                } else {
                    shouldEmpty = true;
                }

            } else {
                shouldEmpty = true;
            }

            if (shouldEmpty) {
                $('#stts-entity').prop('checked', true);

                $('.section-b-1-policyholder-name').val('');
                $('.section-b-1-controlling-person').val('');
                $('.section-b-1-place-birth').val('');
                $('.section-b-1-date-birth').val('');
                $('.section-b-1-country').val('');
                $('.section-b-1-city').val('');
                $('.section-b-1-zip').val('');
                $('.section-b-1-address').val('');

                $(`#stts-single`).attr('checked', true);
                $('.section-b-1-other-status').addClass('d-none').val('');

                $('#stts-male').attr('checked', true);

                $('.section-b-1-nationality').val(null);
                $('.section-b-1-legal-residence').val(null);
                $('.section-b-1-passport').val(null);
                $('.section-b-1-passport-issue-country').val(null);
                $('.section-b-1-tin').val(null);
                $('.section-b-1-lei').val(null);
                $('.section-b-1-email').val(null);
            }
        });

        $('.section-b-1-status').on('change', function() {
            console.log($(this).val());
            
            if ($(this).val() === 'other') {
                $('.section-b-1-status-other').removeClass('d-none');
            } else {
                $('.section-b-1-status-other').addClass('d-none').val('');
            }
        });

        $(document).on('change', '.doc-upl', function() {
            let fileInput = $(this);
            let file = fileInput[0].files[0];
            let docId = fileInput.attr('name').match(/\d+/)[0];
            let viewBtn = $('#view\\[' + docId + '\\]');
            let chckBox = $('#doc-' + docId);
            let dtType = $('#doc-' + docId).data('type');

            if (!file) return;

            if (file.size > 10 * 1024 * 1024) {
                Swal.fire('Error', 'File size must be less than 10 MB', 'error');
                fileInput.val('');
                return;
            }

            let formData = new FormData();
            formData.append('file', file);
            formData.append('doc_id', docId);
            formData.append('policy_id', currentCaseId);
            formData.append('dt_type', dtType);
            formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

            $.ajax({
                url: "{{ route('upload-document') }}",
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    Swal.fire({
                        title: 'Uploading...',
                        text: 'Please wait while the file is uploading',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });
                },
                success: function(response) {
                    Swal.close();
                    if (response.status === 'success') {
                        Swal.fire('Success', 'File uploaded successfully', 'success');
                        viewBtn.removeClass('d-none').attr('href', response.url).attr(
                            'target', '_blank');

                        if (chckBox) {
                            chckBox.attr('checked', true)
                        }
                    } else {
                        Swal.fire('Error', response.message || 'Something went wrong',
                            'error');
                    }
                },
                error: function() {
                    Swal.close();
                    Swal.fire('Error', 'Server error while uploading file', 'error');
                }
            });
        });


    });
</script>
<script>
$(document).ready(function () {
    $('.policy-dropdown-toggle').on('click', function (e) {
        e.preventDefault();
        e.stopPropagation();

        let $parentLi = $(this).closest('.child-dropdown');
        let $submenu = $parentLi.find('> .policy-dropdown-menu');

        $('.child-dropdown').not($parentLi).find('> .policy-dropdown-menu').slideUp();

        $submenu.stop(true, true).slideToggle();
    });

    $('.each-options').on('click', function (e) {
        e.preventDefault();
        let section = $(this).data('section');

        $('.each-options').removeClass('active');
        $(this).addClass('active');
    });
});
</script>
<script>
let currentEditId = null;

$(document).ready(function() {
    loadInsuredLives();    
    $('#save-add-new').click(function() {
        saveInsuredLife('add_new');
    });

});

function loadInsuredLives() {
    const policyId = "{{ $policy->id ?? '' }}";
    if (!policyId) return;
    
    $.ajax({
        url: '{{ route("case.getInsuredLives") }}',
        method: 'POST',
        data: {
            policy_id: policyId,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            $('#insured-life-accordion').html(response.html);
        }
    });
}

function saveInsuredLife(action) {
    const form = $('#form-section-c-1')[0];
    
    const formData = new FormData();
    formData.append('policy', "{{ $policy->id ?? '' }}");
    formData.append('section', 'section-c-1');
    formData.append('save', action === 'next' ? 'next' : 'draft');
    formData.append('_token', '{{ csrf_token() }}');
    
    const data = {
        controlling_person_name: $('#c1_controlling_person_name').val(),
        place_of_birth: $('#c1_place_of_birth').val(),
        date_of_birth: $('#c1_date_of_birth').val(),
        address: $('#c1address').val(),
        country: $('#c1country').val(),
        city: $('#c1city').val(),
        zip: $('#c1zip').val(),
        status: $('.c1sts:checked').val(),
        smoker_status: $('.c1-smsts:checked').val(),
        nationality: $('#c1nationality').val(),
        gender: $('.c1-gndr:checked').val(),
        country_of_legal_residence: $('#c1country_of_legal_residence').val(),
        passport_number: $('#c1passport_number').val(),
        country_of_issuance: $('#c1country_of_issuance').val(),
        relationship_to_policyholder: $('#c1relationship_to_policyholder').val(),
        email: $('#c1email').val()
    };
    
    if (currentEditId) {
        data.id = currentEditId;
    }
    
    formData.append('data', JSON.stringify(data));
        
    $.ajax({
        url: '{{ route("case.submission") }}',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.next_section) {
                if (action === 'add_new') {
                    resetForm();
                    loadInsuredLives();
                }
            }
        },
        error: function(xhr) {
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                Object.keys(xhr.responseJSON.errors).forEach(key => {
                    const field = $(`[name="${key}"]`);
                    field.addClass('is-invalid');
                    field.siblings('.invalid-feedback').remove();
                    field.after(`<div class="invalid-feedback">${xhr.responseJSON.errors[key][0]}</div>`);
                });
            }
        }
    });
}

function resetForm() {
    $('#form-section-c-1')[0].reset();
    currentEditId = null;
    $('#save-add-new').text('Save & Add New');
}

function editInsuredLife(id) {
    const policyId = "{{ $policy->id ?? '' }}";
    
    $.ajax({
        url: '{{ route("case.getInsuredLife") }}',
        method: 'POST',
        data: {
            policy_id: policyId,
            insured_life_id: id,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.data) {
                const data = response.data;
                currentEditId = data.id;
                
                $('#c1_controlling_person_name').val(data.name);
                $('#c1_place_of_birth').val(data.place_of_birth);
                $('#c1_date_of_birth').val(data.date_of_birth);
                $('#c1address').val(data.address);
                $('#c1country').val(data.country);
                $('#c1city').val(data.city);
                $('#c1zip').val(data.zip);
                $(`input.c1sts[value="${data.status}"]`).prop('checked', true);
                $(`input.c1-smsts[value="${data.smoker_status}"]`).prop('checked', true);
                $('#c1nationality').val(data.nationality);
                $(`input.c1-gndr[value="${data.gender}"]`).prop('checked', true);
                $('#c1country_of_legal_residence').val(data.country_of_legal_residence);
                $('#c1passport_number').val(data.passport_number);
                $('#c1country_of_issuance').val(data.country_of_issuance);
                $('#c1relationship_to_policyholder').val(data.relationship_to_policyholder);
                $('#c1email').val(data.email);
                                
                $('#save-add-new').text('Update & Add New');
                
                $('html, body').animate({
                    scrollTop: $('#form-section-c-1').offset().top - 100
                }, 500);
            }
        }
    });
}

function deleteInsuredLife(id) {
    const policyId = "{{ $policy->id ?? '' }}";
    
    Swal.fire({
        title: 'Are you sure?',
        text: 'Do you really want to delete this insured life record?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'No, keep it',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ route("case.deleteInsuredLife") }}',
                method: 'POST',
                data: {
                    policy_id: policyId,
                    insured_life_id: id,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.status) {
                        Swal.fire('Deleted!', 'Insured life record has been deleted.', 'success');

                        if ($(`#ins-life-reco-${id}`).length) {
                            $(`#ins-life-reco-${id}`).remove();
                        }
                    }
                },
                error: function() {
                    Swal.fire('Error!', 'Something went wrong. Please try again.', 'error');
                }
            });
        }
    });
}

</script>
<script>

$(function() {
	refreshBeneficiariesAccordion();
	$('.d-1-save-add-new').on('click', function() { saveBeneficiary('save-and-add'); });

	$('#d-1-insured-life').select2({
		allowClear: true,
		placeholder: 'Select policy holder',
		theme: 'classic',
		width: '100%',
		ajax: {
			url: "{{ route('insured-list') }}",
			type: "POST",
			dataType: 'json',
			delay: 250,
			data: function(params) {
				return {
					searchQuery: params.term,
					page: params.page || 1,
					policy_id: "{{ $policy->id }}",
					_token: "{{ csrf_token() }}"
				};
			},
			processResults: function(data, params) {
				params.page = params.page || 1;
				return {
					results: $.map(data.items, function(item) {
						return {
							id: item.id,
							text: item.text
						};
					}),
					pagination: {
						more: data.pagination.more
					}
				};
			},
			cache: true
		}
	});

});

function resetD1Form() {
	$('#d-1-edit-id').val('');
	$('#d-1-insured-life').val(null).trigger('change');
	$('#form-section-d-1')[0].reset();
}

function buildD1Payload() {
	return {
		insured_life_id: $('#d-1-insured-life').val(),
		name: $('#d-1-name').val(),
		place_of_birth: $('#d-1-place-of-birth').val(),
		date_of_birth: $('#d-1-dob').val(),
		address: $('#d-1-address').val(),
		country: $('#d-1-country').val(),
		city: $('#d-1-city').val(),
		zip: $('#d-1-zip').val(),
		status: $('input[name="d-1-status"]:checked').val(),
		smoker_status: $('input[name="d-1-smoker"]:checked').val(),
		nationality: $('#d-1-nationality').val(),
		gender: $('input[name="d-1-gender"]:checked').val(),
		country_of_legal_residence: $('#d-1-legal-residence').val(),
		passport_number: $('#d-1-passport').val(),
		country_of_issuance: $('#d-1-issuance').val(),
		relationship_to_policyholder: $('#d-1-relationship').val(),
		email: $('#d-1-email').val(),
		beneficiary_death_benefit_allocation: $('#d-1-allocation').val(),
		designation_of_beneficiary: $('input[name="d-1-designation"]:checked').val(),
		id: $('#d-1-edit-id').val() || undefined
	};
}

function saveBeneficiary(saveType) {
	const formEl = document.getElementById('form-section-d-1');

	$.ajax({
		url: "{{ route('case.submission') }}",
		method: 'POST',
		data: {
			policy: {{ $policy->id ?? 'null' }},
			section: 'section-d-1',
			data: buildD1Payload(),
			save: saveType
		},
		headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
		success: function(resp) {
			if (resp.type === 'save-and-add') { resetD1Form(); refreshBeneficiariesAccordion(); }
			else if (resp.type === 'save') {
				refreshBeneficiariesAccordion();
				$('.policy-dropdown-item[data-section="section-d-1"]').removeClass('active').parent().parent().hide();
				$('.policy-dropdown-item[data-section="section-e-1"]').addClass('active').parent().parent().show();
				$('#section-d-1').addClass('d-none');
				$('#section-e-1').removeClass('d-none');
				$('#d-1-insured-life').val(null).trigger('change');
			}
		},
		error: function() {}
	});
}

function refreshBeneficiariesAccordion() {
	$.ajax({
		url: "{{ route('case.getBeneficiaries') }}",
		method: 'POST',
		data: { policy_id: {{ $policy->id ?? 'null' }}, _token: '{{ csrf_token() }}' },
		success: function(res) { $('#d-1-beneficiaries-accordion').html(res.html); }
	});
}

function d1EditBeneficiary(id) {
	$.ajax({
		url: "{{ route('case.getBeneficiary') }}",
		method: 'POST',
		data: { policy_id: {{ $policy->id ?? 'null' }}, beneficiary_id: id, _token: '{{ csrf_token() }}' },
		success: function(res) {
			if (res.data) {
				$('#d-1-edit-id').val(res.data.id);

                if (res.data.insured) {
                    let newOption = new Option(res.data.insured.name, res.data.insured.id, true, true);
                    $('#d-1-insured-life').append(newOption).trigger('change');
                }
                
				$('#d-1-name').val(res.data.name);
				$('#d-1-place-of-birth').val(res.data.place_of_birth);
				$('#d-1-dob').val(res.data.date_of_birth);
				$('#d-1-address').val(res.data.address);
				$('#d-1-country').val(res.data.country);
				$('#d-1-city').val(res.data.city);
				$('#d-1-zip').val(res.data.zip);
				$(`input[name="d-1-status"][value="${res.data.status}"]`).prop('checked', true);
				$(`input[name="d-1-smoker"][value="${res.data.smoker_status}"]`).prop('checked', true);
				$('#d-1-nationality').val(res.data.nationality);
				$(`input[name="d-1-gender"][value="${res.data.gender}"]`).prop('checked', true);
				$('#d-1-legal-residence').val(res.data.country_of_legal_residence);
				$('#d-1-passport').val(res.data.passport_number);
				$('#d-1-issuance').val(res.data.country_of_issuance);
				$('#d-1-relationship').val(res.data.relationship_to_policyholder);
				$('#d-1-email').val(res.data.email);
				$('#d-1-allocation').val(res.data.beneficiary_death_benefit_allocation);
				$(`input[name="d-1-designation"][value="${res.data.designation_of_beneficiary}"]`).prop('checked', true);
				$('html, body').animate({ scrollTop: $('#form-section-d-1').offset().top - 100 }, 400);
			}
		}
	});
}

function d1DeleteBeneficiary(id) {
    const policyId = "{{ $policy->id ?? '' }}";
    
    Swal.fire({
        title: 'Are you sure?',
        text: 'Do you really want to delete this beneficiary record?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'No, keep it',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ route("case.deleteBeneficiary") }}',
                method: 'POST',
                data: {
                    policy_id: policyId,
                    beneficiary_id: id,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.status) {
                        Swal.fire('Deleted!', 'Beneficiary record has been deleted.', 'success');

                        if ($(`#bene-reco-${id}`).length) {
                            $(`#bene-reco-${id}`).remove();
                        }
                    }
                },
                error: function() {
                    Swal.fire('Error!', 'Something went wrong. Please try again.', 'error');
                }
            });
        }
    });
}
</script>