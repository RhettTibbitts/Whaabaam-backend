@extends('frontEnd.layouts.master') 
@section('title',': Privacy Policy') 
@section('content')

<section>
    <div class="select-prefrences-page privacy-policy-page">
        <div class="container">
            <div class="row">
                <div class="col-md-12">

                    <div class="about-us-page-view">
                        <h2>{{ $page->heading }}</h2>
                        <div class="settings-managed about-us-text">
                            {!! $page->content !!}
                            
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</section>  

@endsection