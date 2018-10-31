@extends('backEnd.layouts.base')
@section('title', 'States ')
@section('content')

    <div class="content-area py-1">
        <div class="container-fluid">
            <div class="box box-block bg-white">
                <h5 class="mb-1">State's List</h5>
                <a href="{{ url('admin/state/add') }}" style="margin-left: 1em;" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add New</a>
                <table class="table table-striped table-bordered dataTable" id="table-2">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <!-- <th>Status</th> -->
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($states as $index => $state)
                        <tr>
                            <td>{{$index + 1}}</td>
                            <td>{{$state['name']}}</td>
                            <td>
                                <a href="{{ url('admin/state/edit', $state['id']) }}" class="btn btn-info" title="Edit"><i class="fa fa-pencil"></i> </a>
                                <a href="{{ url('admin/state/delete', $state['id']) }}" class="btn btn-danger" title="Delete"  onclick="return confirm('Are you sure?')"><i class="fa fa-trash"></i> </a>
                                <a href="{{ url('admin/cities', $state['id']) }}" class="btn btn-primary del-btn" title="Cities list"><i class="fa fa-globe"></i> </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection