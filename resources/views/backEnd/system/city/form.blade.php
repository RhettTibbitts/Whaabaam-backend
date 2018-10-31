@extends('backEnd.layouts.base')

<?php if(!isset($city)){
		$form_title  = 'Add';
		$form_action = url('admin/city/add/'.$state_id);
	} else{
		$form_title  = 'Edit';
		$form_action = url('admin/city/edit/'.$city->id);
	}
?>
@section('title', $form_title.' City')
@section('content')
<?php //echo '1'; die; ?>

<div class="content-area py-1">
    <div class="container-fluid">
    	<div class="box box-block bg-white">
            <a href="{{  url('admin/cities/'.$state_id) }}" class="btn btn-default pull-right"><i class="fa fa-angle-left"></i> Back</a>

			<h5 class="mb-2em">{{ $form_title }} City</h5>

            <form class="form-horizontal" action="{{ $form_action }}" method="POST" role="form">
            	
            	{{csrf_field()}}
            	<div class="form-group row">
					<label for="name" class="col-md-2 col-xs-12 col-form-label">City Name</label>
					<div class="col-xs-10">
						<input class="form-control" type="text" value="{{ (null !== (old('name'))) ? old('name') : @$city->name  }}" name="name" required id="name" placeholder="City Name" maxlength="255">
					</div>
				</div>
				<div class="form-group row">
					<label  class="col-md-2 col-xs-12 col-form-label"></label>
					<div class="col-xs-10 ">
						<input type="hidden" name="state_id" value="{{ $state_id }}"> 
						<button type="submit" class="btn btn-primary">Save</button>
						<a href="{{ url('admin/cities/'.$state_id)}}" class="btn btn-default">Cancel</a>
					</div>
				</div>
			</form>
		</div>
    </div>
</div>
@endsection