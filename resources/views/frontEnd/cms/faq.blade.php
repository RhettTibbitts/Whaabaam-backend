@extends('frontEnd.layouts.master') 
@section('title',': Faq') 
@section('content')

<section class="chat-page inner-main faq-page" >
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div class="faqs-iin">
                    <h1>Frequently Asked Questions</h1>
                    <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                      
                      @foreach($faqs as $key => $faq)
                        <div class="panel panel-default">
                          <div class="panel-heading" role="tab" id="heading{{$key}}">
                            <h4 class="panel-title">
                              <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse{{$key}}" aria-expanded="true" aria-controls="collapse{{$key}}">
                                {{ $faq->que }} <span class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span>
                              </a>
                            </h4>
                          </div>
                          <div id="collapse{{$key}}" class="panel-collapse collapse {{ ($key == '0') ? 'in' : '' }}" role="tabpanel" aria-labelledby="heading{{$key}}">
                            <div class="panel-body">
                              {{ $faq->ans }}
                            </div>
                          </div>
                        </div>
                      @endforeach

                </div>
            </div>
        </div>
    </div>
</section>

@endsection