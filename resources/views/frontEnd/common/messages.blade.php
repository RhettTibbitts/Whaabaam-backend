@if(Session::has('success'))
	<div class="resp-msg"><b>Success:</b> {{ Session::get('success') }}</div>
	<!-- <script type="text/javascript">
		$.toaster({ priority : 'success', title : 'Success', message : "{{ Session::get('success') }}" });
	</script> -->
@endif
@if(Session::has('error'))
	<div class="resp-msg"><b>Error:</b> {{ Session::get('error') }}</div>
	<!-- <script type="text/javascript">
		$.toaster({ priority : 'danger', title : 'Error', message : "{{ Session::get('error') }}" });
	</script> -->
@endif

<script type="text/javascript">
	setTimeout( function(){
		$('.resp-msg').hide();
	}, 3000);
</script>