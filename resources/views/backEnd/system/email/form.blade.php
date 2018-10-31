@extends('backEnd.layouts.base')
<?php 
	$form_title  = 'Edit';
	$form_action = url('admin/email/edit/'.$email->id);
?>
@section('title', $form_title.' Email Template')
@section('content')

<div class="content-area py-1">
    <div class="container-fluid">
    	<div class="box box-block bg-white">
            <a href="{{  url('admin/emails') }}" class="btn btn-default pull-right"><i class="fa fa-angle-left"></i> Back</a>

			<h5 class="mb-2em">{{ $form_title }} Email Template</h5>
			<script src="https://cdn.ckeditor.com/ckeditor5/11.0.1/classic/ckeditor.js"></script>
            <form class="form-horizontal" action="{{ $form_action }}" method="POST" role="form">
            	<div class="form-group row">
					<label for="name" class="col-md-2 col-xs-12 col-form-label">Name</label>
					<label for="name" class="col-md-2 col-xs-12 col-form-label">{{ $email->name  }}</label>
					<!-- <div class="col-xs-10">
						<input class="form-control" type="text" value="{{ (null !== (old('name'))) ? old('name') : @$email->name  }}" name="name" required id="name" placeholder="Email Name" maxlength="255">
					</div> -->
				</div>
            	<div class="form-group row">
					<label for="subject" class="col-md-2 col-xs-12 col-form-label">Subject</label>
					<div class="col-xs-10">
						<input class="form-control" type="text" value="{{ (null !== (old('subject'))) ? old('subject') : @$email->subject  }}" name="subject" required id="subject" placeholder="Subject" maxlength="255">
					</div>
				</div>
            	<div class="form-group row">
					<label for="content" class="col-md-2 col-xs-12 col-form-label">Content</label>
					<div class="col-xs-10">
						<textarea name="content" id="editor" length="6000">
						    {!! $email->content !!}
						</textarea>
						@if(!empty($email->note))
							<p class="m-t-10 m-b-0">Note: Please don't remove following keywords from the above email content:</p>
							<p >{{ $email->note }}</p>
						@endif
						<!-- <input class="form-control" type="text" value="{{ (null !== (old('content'))) ? old('content') : @$email->content  }}" name="content" required id="content" placeholder="Content" maxlength="255"> -->
					</div>
				</div>
				<div class="form-group row">
					<label  class="col-md-2 col-xs-12 col-form-label"></label>
					<div class="col-xs-10 ">
            			{{csrf_field()}}
						<button type="submit" class="btn btn-primary">Save</button>
						<a href="{{ url('admin/emails')}}" class="btn btn-default">Cancel</a>
					</div>
				</div>
			</form>
		</div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {

		ClassicEditor
		    .create( document.querySelector( '#editor' ) )
		    .then( editor => {
		        console.log( editor );
		    } )
		    .catch( error => {
		        console.error( error );
		    } );
	});
</script>

@endsection