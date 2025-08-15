@extends('layouts.app', ['title' => $title, 'subTitle' => $subTitle])

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header"> {{  $subTitle  }} </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="name" class="form-label">Title <span class="text-danger"> * </span> </label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $role->title) }}" disabled>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Select Permissions</div>
            <div class="card-body">
                
                <div class="row">
                    @forelse($permissions as $key => $permission)
                        <div class="col-6">
                            <div class="card">
                                <div class="card-body">
                                    <h3>
                                        <label for="{{ Str::slug($key) }}"> {{  ucwords(str_replace('-', ' ', $key))  }} </label>
                                    </h3>
                                    @forelse($permission as $row)
                                    <input type="checkbox" name="permissions[]" data-parent="{{ Str::slug($key) }}" id="{{ $row->name }}" value="{{ $row->id }}" @if(in_array($row->id, $existingPermissions)) checked @endif disabled>
                                    <label for="{{ $row->name }}">{{ $row->title }}</label> <br>
                                    @empty
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    @empty
                    @endforelse
                </div>

            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <a href="{{ route('roles.index') }}" class="btn btn-secondary">Back to List</a>
            </div>
        </div>
    </div>
</div>
@endsection 
