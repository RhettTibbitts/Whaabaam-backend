@extends('backEnd.layouts.base')
@section('title', 'Politicals')
@section('content')
    <div class="content-area py-1">
        <div class="container-fluid">
            <div class="box box-block bg-white">
                <h5 class="mb-1">Political's List</h5>
                <a href="{{ url('admin/political/add') }}" style="margin-left: 1em;" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add New</a>
                <table class="table table-striped table-bordered dataTable" id="table-2">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($politicals as $index => $value)
                        <tr>
                            <td>{{$index + 1}}</td>
                            <td>{{$value['name']}}</td>
                            <td>
                                <a href="{{ url('admin/political/edit', $value['id']) }}" class="btn btn-info" title="Edit"><i class="fa fa-pencil"></i> </a>
                                <a href="{{ url('admin/political/delete', $value['id']) }}" class="btn btn-danger" title="Delete" onclick="return confirm('Are you sure?')" ><i class="fa fa-trash"></i> </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection