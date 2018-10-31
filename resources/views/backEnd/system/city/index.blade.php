@extends('backEnd.layouts.base')
@section('title', 'Cities ')
@section('content')

    <div class="content-area py-1">
        <div class="container-fluid">
            <div class="box box-block bg-white">
                <h5 class="mb-1">Cities's List</h5>
                <a href="{{ url('admin/city/add/'.$state_id) }}" style="margin-left: 1em;" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add New</a>
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
                    @foreach($cities as $index => $city)
                        <tr>
                            <td>{{$index + 1}}</td>
                            <td>{{$city['name']}}</td>
                            <td>
                                <a href="{{ url('admin/city/edit', $city['id']) }}" class="btn btn-info" title="Edit"><i class="fa fa-pencil"></i> </a>
                                <a href="{{ url('admin/city/delete', $city['id']) }}" class="btn btn-danger" title="Delete" onclick="return confirm('Are you sure?')" ><i class="fa fa-trash"></i> </a>
                            </td>
                        </tr>
                    @endforeach
                        <!-- <form action="{{ url('admin/city/delete', $city['id']) }}" method="post">    
                                    {{ csrf_field() }}
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')" title="Delete"><i class="fa fa-trash"></i></button>
                                </form> -->
                        <!-- <td> ($city['status'] == '1') ? 'Active' : 'Inactive' </td> -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection