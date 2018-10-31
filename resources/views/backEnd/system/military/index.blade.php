@extends('backEnd.layouts.base')
@section('title', 'Militaries ')
@section('content')

    <div class="content-area py-1">
        <div class="container-fluid">
            <div class="box box-block bg-white">
                <h5 class="mb-1">Militaries's List</h5>
                <a href="{{ url('admin/military/add') }}" style="margin-left: 1em;" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add New</a>
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
                    @foreach($militaries as $index => $value)
                        <tr>
                            <td>{{$index + 1}}</td>
                            <td>{{$value['name']}}</td>
                            <td>
                                <a href="{{ url('admin/military/edit', $value['id']) }}" class="btn btn-info" title="Edit"><i class="fa fa-pencil"></i> </a>
                                <a href="{{ url('admin/military/delete', $value['id']) }}" class="btn btn-danger" title="Delete" onclick="return confirm('Are you sure?')" ><i class="fa fa-trash"></i> </a>
                            </td>
                        </tr>
                    @endforeach
                        <!-- <form action="{{ url('admin/military/delete', $value['id']) }}" method="post">    
                                    {{ csrf_field() }}
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')" title="Delete"><i class="fa fa-trash"></i></button>
                                </form> -->
                        <!-- <td> ($value['status'] == '1') ? 'Active' : 'Inactive' </td> -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection