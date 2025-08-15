<script>

    const submission = (section, form, submitButton) => {
        $.ajax({
            url: "{{ route('case.submission') }}",
            method: 'POST',
            data: {
                policy: currentCaseId,
                section: section,
                data: form,
                save: $(submitButton).data('type')
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function () {
                showSavingStatus('saving');
            },
            success: function(response) {
                showSavingStatus('saved', response.timestamp || (new Date().toLocaleTimeString('en-US', { hour12: true, hour: '2-digit', minute: '2-digit', second: '2-digit' })));

                if ('type' in response && response.type == 'save' && 'next_section' in response && response.next_section != '' && response.next_section != null) {
                    var nextSection = $(submitButton).data('next');
                    var currentSection = section;

                    if (!nextSection && !currentSection) return;

                    $('.policy-dropdown-item[data-section="' + currentSection + '"]').removeClass('active');
                    $('.policy-dropdown-item[data-section="' + currentSection + '"]').parent().parent().css('display', 'none');

                    $('.policy-dropdown-item[data-section="' + nextSection + '"]').addClass('active');
                    $('.policy-dropdown-item[data-section="' + nextSection + '"]').parent().parent().css('display', 'block');

                    $(`#${currentSection}`).addClass('d-none');
                    $(`#${nextSection}`).removeClass('d-none');                        
                    
                } else {
                    window.location.href = "{{ route('cases.index') }}";
                }
            },
            error: function(xhr) {
                showSavingStatus('error');
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    let errorList = '';

                    $.each(errors, function(key, messages) {
                        messages.forEach(function(message) {
                            errorList += `<li>${message}</li>`;
                        });
                    });

                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Errors',
                        html: `<ul style="text-align: left;">${errorList}</ul>`
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Something went wrong. Please try again.'
                    });
                }
            },
            complete: function (response) {
                if (section == 'section-c-1') {
                    resetForm();
                }

                if (section == 'section-d-1') {
                    $('#d-1-edit-id').val('');
                    $('#d-1-insured-life').val(null).trigger('change');
                    $('#form-section-d-1')[0].reset();
                    d1TaxResidences = []; updateD1TaxList();
                    refreshBeneficiariesAccordion();
                }                
            }
        });
    }

    /**
     * Handle form submission for section A-1
     **/
        $('#form-section-a-1').validate({
            errorPlacement: function(error, element) {
                if (element.attr('id') === 'section-a-1-phone_number') {
                    error.insertAfter(element.parent());
                } else {
                    error.appendTo(element.parent());
                }
            },
            submitHandler: function(form, event) {
                event.preventDefault();

                $('#section-a-1-dial_code').val(itisa1.s.dialCode);

                let formData = $(form).serializeArray().reduce((acc, item) => {
                    acc[item.name] = item.value;
                    return acc;
                }, {});

                let actionType = event.originalEvent.submitter;
                
                submission('section-a-1', formData, actionType);
            }
        });

    /**
     * Handle form submission for section A-1
     **/

    /**
     * Handle form submission for section A-2
     **/

        $('#form-section-a-2').validate({
            errorPlacement: function(error, element) {
                if (element.attr('id') === 'section-a-1-phone_number') {
                    error.insertAfter(element.parent());
                } else {
                    error.appendTo(element.parent());
                }
            },
            submitHandler: function(form, event) {
                event.preventDefault();

                let formData = {};
                $(form).serializeArray().forEach(({ name, value }) => {
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
                
                let actionType = event.originalEvent.submitter;
                
                submission('section-a-2', formData, actionType);
            }
        });        

    /**
     * Handle form submission for section A-2
     **/


    /**
     * Handle form submission for section B-1
     **/

        $('#form-section-b-1').validate({
            submitHandler: function(form, event) {
                event.preventDefault();

                let formData = {};
                $(form).serializeArray().forEach(({ name, value }) => {
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
                
                let actionType = event.originalEvent.submitter;
                
                submission('section-b-1', formData, actionType);
            }
        });        

    /**
     * Handle form submission for section B-1
     **/ 
    
    /**
     * Handle form submission for section B-2
     **/

        $('#form-section-b-2').validate({
            submitHandler: function(form, event) {
                event.preventDefault();

                let formData = {};
                $(form).serializeArray().forEach(({ name, value }) => {
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
                
                let actionType = event.originalEvent.submitter;
                
                submission('section-b-2', formData, actionType);
            }
        });        

    /**
     * Handle form submission for section B-2
     **/

    /**
     * Handle form submission for section C-1
     **/

        $('#form-section-c-1').validate({
            submitHandler: function(form, event) {
                event.preventDefault();

                let formData = {};
                $(form).serializeArray().forEach(({ name, value }) => {
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
                
                let actionType = event.originalEvent.submitter;
                
                submission('section-c-1', formData, actionType);
            }
        });        

    /**
     * Handle form submission for section C-1
     **/    
    
    /**
     * Handle form submission for section D-1
     **/

        $('#form-section-d-1').validate({
            submitHandler: function(form, event) {
                event.preventDefault();

                let formData = {};
                $(form).serializeArray().forEach(({ name, value }) => {
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
                
                let actionType = event.originalEvent.submitter;
                
                submission('section-d-1', formData, actionType);
            }
        });        

    /**
     * Handle form submission for section D-1
     **/         
    
    /**
     * Handle form submission for section E-1
     **/

        $('#form-section-e-1').validate({
            submitHandler: function(form, event) {
                event.preventDefault();

                let formData = {};
                $(form).serializeArray().forEach(({ name, value }) => {
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
                
                let actionType = event.originalEvent.submitter;
                
                submission('section-e-1', formData, actionType);
            }
        });        

    /**
     * Handle form submission for section E-1
     **/  
    
    /**
     * Handle form submission for section E-2
     **/

        $('#form-section-e-2').validate({
            submitHandler: function(form, event) {
                event.preventDefault();

                let formData = {};
                $(form).serializeArray().forEach(({ name, value }) => {
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
                
                let actionType = event.originalEvent.submitter;
                
                submission('section-e-2', formData, actionType);
            }
        });        

    /**
     * Handle form submission for section E-2
     **/

    /**
     * Handle form submission for section E-3
     **/

        $('#form-section-e-3').validate({
            submitHandler: function(form, event) {
                event.preventDefault();

                let formData = {};
                $(form).serializeArray().forEach(({ name, value }) => {
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
                
                let actionType = event.originalEvent.submitter;
                
                submission('section-e-3', formData, actionType);
            }
        });        

    /**
     * Handle form submission for section E-3
     **/

    /**
     * Handle form submission for section E-4
     **/

        $('#form-section-e-4').validate({
            submitHandler: function(form, event) {
                event.preventDefault();

                let formData = {};
                $(form).serializeArray().forEach(({ name, value }) => {
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
                
                let actionType = event.originalEvent.submitter;
                
                submission('section-e-4', formData, actionType);
            }
        });        

    /**
     * Handle form submission for section E-4
     **/  
    
    /**
     * Handle form submission for section F-1
     **/

        $('#form-section-f-1').validate({
            submitHandler: function(form, event) {
                event.preventDefault();

                let formData = {};
                $(form).serializeArray().forEach(({ name, value }) => {
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
                
                let actionType = event.originalEvent.submitter;
                
                submission('section-f-1', formData, actionType);
            }
        });        

    /**
     * Handle form submission for section F-1
     **/  
    
    /**
     * Handle form submission for section F-2
     **/

        $('#form-section-f-2').validate({
            submitHandler: function(form, event) {
                event.preventDefault();

                let formData = {};
                $(form).serializeArray().forEach(({ name, value }) => {
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
                
                let actionType = event.originalEvent.submitter;
                
                submission('section-f-2', formData, actionType);
            }
        });        

    /**
     * Handle form submission for section F-2
     **/


    /**
     * Handle form submission for section F-3
     **/

        $('#form-section-f-3').validate({
            submitHandler: function(form, event) {
                event.preventDefault();

                let formData = {};
                $(form).serializeArray().forEach(({ name, value }) => {
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
                
                let actionType = event.originalEvent.submitter;
                
                submission('section-f-3', formData, actionType);
            }
        });        

    /**
     * Handle form submission for section F-3
     **/
    
    /**
     * Handle form submission for section F-4
     **/

        $('#form-section-f-4').validate({
            submitHandler: function(form, event) {
                event.preventDefault();

                let formData = {};
                $(form).serializeArray().forEach(({ name, value }) => {
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
                
                let actionType = event.originalEvent.submitter;
                
                submission('section-f-4', formData, actionType);
            }
        });        

    /**
     * Handle form submission for section F-4
     **/  
    
     
    /**
     * Handle form submission for section F-5
     **/

        $('#form-section-f-5').validate({
            submitHandler: function(form, event) {
                event.preventDefault();

                let formData = {};
                $(form).serializeArray().forEach(({ name, value }) => {
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
                
                let actionType = event.originalEvent.submitter;
                
                submission('section-f-5', formData, actionType);
            }
        });        

    /**
     * Handle form submission for section F-4
     **/  
    
     
    /**
     * Handle form submission for section F-6
     **/

        $('#form-section-f-6').validate({
            submitHandler: function(form, event) {
                event.preventDefault();

                let formData = {};
                $(form).serializeArray().forEach(({ name, value }) => {
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
                
                let actionType = event.originalEvent.submitter;
                
                submission('section-f-6', formData, actionType);
            }
        });        

    /**
     * Handle form submission for section F-6
     **/     
    
     
    /**
     * Handle form submission for section F-7
     **/

        $('#form-section-f-7').validate({
            submitHandler: function(form, event) {
                event.preventDefault();

                let formData = {};
                $(form).serializeArray().forEach(({ name, value }) => {
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
                
                let actionType = event.originalEvent.submitter;
                
                submission('section-f-7', formData, actionType);
            }
        });        

    /**
     * Handle form submission for section F-7
     **/    
    
     
    /**
     * Handle form submission for section G-1
     **/

        $('#form-section-g-1').validate({
            submitHandler: function(form, event) {
                event.preventDefault();

                let formData = {};
                $(form).serializeArray().forEach(({ name, value }) => {
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
                
                let actionType = event.originalEvent.submitter;
                
                submission('section-g-1', formData, actionType);
            }
        });        

    /**
     * Handle form submission for section G-1
     **/    
    
    /**
     * Handle form submission for section G-2
     **/

        $('#form-section-g-2').validate({
            submitHandler: function(form, event) {
                event.preventDefault();

                let formData = {};
                $(form).serializeArray().forEach(({ name, value }) => {
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
                
                let actionType = event.originalEvent.submitter;
                
                submission('section-g-2', formData, actionType);
            }
        });        

    /**
     * Handle form submission for section G-2
     **/         


    /**
     * Handle form submission for section G-1
     **/

     $('#form-section-g-1').validate({
            submitHandler: function(form, event) {
                event.preventDefault();

                let formData = $(form).serializeArray().reduce((acc, item) => {
                    acc[item.name] = item.value;
                    return acc;
                }, {});
                
                let actionType = event.originalEvent.submitter;
                
                $.ajax({
                    url: "{{ route('case.submission') }}",
                    method: 'POST',
                    data: {
                        policy: currentCaseId,
                        section: 'section-g-1',
                        data: formData,
                        save: $(actionType).data('type')
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function () {
                        showSavingStatus('saving');
                    },
                    success: function(response) {
                        showSavingStatus('saved', response.timestamp || (new Date().toLocaleTimeString('en-US', { hour12: true, hour: '2-digit', minute: '2-digit', second: '2-digit' })));

                        if (response.type === 'save-and-add') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: 'Communication entry saved successfully. You can now add another entry.',
                                allowOutsideClick: true, 
                                allowEscapeKey: true
                            }).then(() => {
                                $('#form-section-g-1')[0].reset();
                                refreshCommunicationAccordion();
                            });
                        } else {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.message || 'Saved successfully.',
                                allowOutsideClick: true, 
                                allowEscapeKey: true
                            }).finally(() => {
                                if ('type' in response && response.type == 'save' && 'next_section' in response && response.next_section != '') {
                                    var nextSection = $(actionType).data('next');
                                    var currentSection = 'section-g-1';

                                    if (!nextSection && !currentSection) return;

                                    $('.policy-dropdown-item[data-section="' + currentSection + '"]').removeClass('active');
                                    $('.policy-dropdown-item[data-section="' + currentSection + '"]').parent().parent().css('display', 'none');

                                    $('.policy-dropdown-item[data-section="' + nextSection + '"]').addClass('active');
                                    $('.policy-dropdown-item[data-section="' + nextSection + '"]').parent().parent().css('display', 'block');

                                    $(`#${currentSection}`).addClass('d-none');
                                    $(`#${nextSection}`).removeClass('d-none');                        
                                    
                                } else if ('type' in response && response.type == 'draft') {
                                    window.location.href = "{{ route('cases.index') }}";
                                }
                            });
                        }
                    },
                    error: function(xhr) {
                        showSavingStatus('error');
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            let errorList = '';

                            $.each(errors, function(key, messages) {
                                messages.forEach(function(message) {
                                    errorList += `<li>${message}</li>`;
                                });
                            });

                            Swal.fire({
                                icon: 'error',
                                title: 'Validation Errors',
                                html: `<ul style="text-align: left;">${errorList}</ul>`
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Something went wrong. Please try again.'
                            });
                        }
                    }
                });
            }
        });        

        function refreshCommunicationAccordion() {
            $.ajax({
                url: "{{ route('case.get-communications') }}",
                method: 'GET',
                data: {
                    policy: currentCaseId
                },
                success: function(response) {
                    if (response.html) {
                        $('#communication-entries-container').html(response.html);
                    }
                },
                error: function() {
                    console.log('Failed to refresh communication accordion');
                }
            });
        }

        function refreshCaseFileNotesAccordion() {
            $.ajax({
                url: "{{ route('case.get-case-file-notes') }}",
                method: 'GET',
                data: {
                    policy: currentCaseId
                },
                success: function(response) {
                    if (response.html) {
                        $('#case-file-notes-container').html(response.html);
                    }
                },
                error: function() {
                    console.log('Failed to refresh case file notes accordion');
                }
            });
        }

    /**
     * Handle form submission for section G-1
     **/

    /**
     * Handle form submission for section G-2
     **/

     $('#form-section-g-2').validate({
            submitHandler: function(form, event) {
                event.preventDefault();

                let formData = $(form).serializeArray().reduce((acc, item) => {
                    acc[item.name] = item.value;
                    return acc;
                }, {});
                
                let actionType = event.originalEvent.submitter;
                
                $.ajax({
                    url: "{{ route('case.submission') }}",
                    method: 'POST',
                    data: {
                        policy: currentCaseId,
                        section: 'section-g-2',
                        data: formData,
                        save: $(actionType).data('type')
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function () {
                        showSavingStatus('saving');
                    },
                    success: function(response) {
                        showSavingStatus('saved', response.timestamp || (new Date().toLocaleTimeString('en-US', { hour12: true, hour: '2-digit', minute: '2-digit', second: '2-digit' })));

                        if (response.type === 'save-and-add') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: 'Case file note saved successfully. You can now add another note.',
                                allowOutsideClick: true, 
                                allowEscapeKey: true
                            }).then(() => {
                                $('#form-section-g-2')[0].reset();
                                refreshCaseFileNotesAccordion();
                            });
                        } else {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.message || 'Saved successfully.',
                                allowOutsideClick: true, 
                                allowEscapeKey: true
                            }).finally(() => {
                                if ('type' in response && response.type == 'save' && 'next_section' in response && response.next_section != '') {
                                    var nextSection = $(actionType).data('next');
                                    var currentSection = 'section-g-2';

                                    if (!nextSection && !currentSection) return;

                                    $('.policy-dropdown-item[data-section="' + currentSection + '"]').removeClass('active');
                                    $('.policy-dropdown-item[data-section="' + currentSection + '"]').parent().parent().css('display', 'none');

                                    $('.policy-dropdown-item[data-section="' + nextSection + '"]').addClass('active');
                                    $('.policy-dropdown-item[data-section="' + nextSection + '"]').parent().parent().css('display', 'block');

                                    $(`#${currentSection}`).addClass('d-none');
                                    $(`#${nextSection}`).removeClass('d-none');                        
                                    
                                } else if ('type' in response && response.type == 'draft') {
                                    window.location.href = "{{ route('cases.index') }}";
                                }
                            });
                        }
                    },
                    error: function(xhr) {
                        showSavingStatus('error');
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            let errorList = '';

                            $.each(errors, function(key, messages) {
                                messages.forEach(function(message) {
                                    errorList += `<li>${message}</li>`;
                                });
                            });

                            Swal.fire({
                                icon: 'error',
                                title: 'Validation Errors',
                                html: `<ul style="text-align: left;">${errorList}</ul>`
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Something went wrong. Please try again.'
                            });
                        }
                    }
                });
            }
        });        

    /**
     * Handle form submission for section G-2
     **/
    

</script>