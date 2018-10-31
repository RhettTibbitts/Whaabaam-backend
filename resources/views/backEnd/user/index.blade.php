@extends('backEnd.layouts.base')
@section('title', 'Users ')
@section('content')

    <div class="content-area py-1">
        <div class="container-fluid">
            
            <div class="box box-block bg-white">
                <h5 class="mb-1">List Users</h5>
                <a href="{{ url('admin/user/add') }}" style="margin-left: 1em;" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add New User</a>
                <table class="table table-striped table-bordered dataTable" id="table-2">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($users as $index => $user)
                        <tr>
                            <td>{{$index + 1}}</td>
                            <td>{{$user->first_name}}</td>
                            <td>{{$user->last_name}}</td>
                            <td>{{$user->email}}</td>
                            <td>{{ ($user->status == '1') ? 'Active' : 'Inactive' }}</td>
                            <td>
                                <form action="{{ url('admin/user/delete', $user->id) }}" method="post">    
                                    {{ csrf_field() }}
                                    <a href="{{ url('admin/user/edit', $user->id) }}" class="btn btn-info" title="Edit"><i class="fa fa-pencil"></i> </a>
                                
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this user? this user will not be able to sign up again ?')" title="Delete"><i class="fa fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                   
                </table>
            </div>
            
        </div>
    </div>
@endsection