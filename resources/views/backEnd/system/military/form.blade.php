@extends('backEnd.layouts.base')

<?php if(!isset($military)){
		$form_title  = 'Add';
		$form_action = url('admin/military/add');
	} else{
		$form_title  = 'Edit';
		$form_action = url('admin/military/edit/'.$military->id);
	}
?>
@section('title', $form_title.' Military')
@section('content')

<div class="content-area py-1">
    <div class="container-fluid">
    	<div class="box box-block bg-white">
            <a href="{{  url('admin/militaries') }}" class="btn btn-default pull-right"><i class="fa fa-angle-left"></i> Back</a>

			<h5 class="mb-2em">{{ $form_title }} Military</h5>

            <form class="form-horizontal" action="{{ $form_action }}" method="POST" role="form">
            	<div class="form-group row">
					<label for="name" class="col-md-2 col-xs-12 col-form-label">Military Name</label>
					<div class="col-xs-10">
						<input class="form-control" type="text" value="{{ (null !== (old('name'))) ? old('name') : @$military->name  }}" name="name" required id="name" placeholder="Military Name" maxlength="255">
					</div>
				</div>
				<div class="form-group row">
					<label  class="col-md-2 col-xs-12 col-form-label"></label>
					<div class="col-xs-10 ">
            			{{csrf_field()}}
						<button type="submit" class="btn btn-primary">Save</button>
						<a href="{{ url('admin/militaries')}}" class="btn btn-default">Cancel</a>
					</div>
				</div>
			</form>
		</div>
    </div>
</div>
@endsection