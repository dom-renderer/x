@extends('layouts.app', ['title' => $title, 'subTitle' => $subTitle, 'editor' => true])

@section('content')
<div class="row justify-content-center">
    <div class="col-12">
        @if(session('success'))
            <div class="p2 p-2 alert alert-success">{{ session('success') }}</div>
        @endif
    </div>
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $setting->name ?? '') }}">
                    </div>

                    <div class="mb-3">
                        <label for="logo" class="form-label">Logo</label>
                        <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                        @if(!empty($setting->logo))
                            <div class="mt-2">
                                <img src="{{ asset('settings-media/' . $setting->logo) }}" alt="Logo" style="max-width:100px;max-height:100px;">
                            </div>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label for="theme_color" class="form-label"> Theme Colour </label>
                        <input type="color" class="form-control" id="theme_color" name="theme_color" value="{{ $setting->theme_color ?? '#28304e' }}">
                    </div>

                    <div class="mb-3">
                        <label for="favicon" class="form-label">Favicon</label>
                        <input type="file" class="form-control" id="favicon" name="favicon" accept="image/*">
                        @if(!empty($setting->favicon))
                            <div class="mt-2">
                                <img src="{{ asset('settings-media/' . $setting->favicon) }}" alt="Favicon" style="max-width:32px;max-height:32px;">
                            </div>
                        @endif
                    </div>

                    <button type="submit" class="btn btn-primary mt-4">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('css')
@endpush

@push('js')
<script>
    $(document).ready(function () {
        $('#local_country_for_tax').select2({
            placeholder: 'Select Country', 
            theme: 'classic',
            width: '100%'
        });

        $('#theme_color').on('input', function() {
            var selectedColor = $(this).val();
        });        
    });
</script>
@endpush
