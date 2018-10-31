<div class="popup-wrap dd-nn" id="signup-popup">
    <form method="post" action="{{ url('register') }}" id="register-form">
        <div class="popup-body">
            <h2 class="title">Create Account</h2>
            <ul class="reg-fields">
                <li>
                    <div class="col1">
                        <div class="input-wrap">
                            <input name="first_name" type="text" placeholder="First Name"  />
                        </div>
                    </div>
                    <div class="col2">
                        <div class="input-wrap">
                            <input name="last_name" type="text"   placeholder="Last Name"/>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="input-wrap">
                        <input name="email" type="email"   placeholder="Email Address"/>
                    </div>
                </li>
                <li>
                    <div class="col1">
                        <div class="input-wrap">
                            <input name="password" type="password" placeholder="Password" id="password" />
                        </div>
                    </div>
                    <div class="col2">
                        <div class="input-wrap">
                            <input name="confirm_password"  type="password" placeholder="Confirm Password" />
                        </div>
                    </div>
                </li>
                <li>
                    <div class="col1">
                        <div class="input-wrap">
                            <select name="country_id">
                                <option value="">Select Country</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country['id'] }}">{{ $country['name'] }}</option>
                                @endforeach
                            </select>
                        </div>

                    </div>
                    <div class="col2">
                        <div class="input-wrap">
                            <select name="state_id">
                                <option value="">Select State</option>
                            </select>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="col1">
                        {{ csrf_field() }}
                        <button class="create-button icons" type="submit">create your account</button>
                        }
                    </div>
                    <div class="col2 pos-rel">
                        <label class="check-term"><input name="agree" type="checkbox" />I accept 'terms and conditions'</label>
                    </div>
                </li>
                <li>
                    <div class="or"><span>or</span></div>
                </li>
                <li>
                    <div class="col1">
                        <a href="#" class="fb-btn"></a>
                    </div>
                    <div class="col2 ">
                        <a class="create-button icons login-btn">members sign in</a>
                    </div>
                </li>
            </ul>
            <div class="clear"></div>
        </div>
    </form>
</div>

<script>
    $(document).ready(function(){
        $('select[name="country_id"]').change(function(){
            var country_id = $(this).val();
            if(country_id == ''){
                $('select[name="state_id"]').html('<option value="0">Select State</option>');
            } else{ 
                $('.loader').show();
                $.ajax({
                    url : '{{ url("get-states") }}'+'/'+country_id,
                    method : "get",
                    success:function(resp){
                        $('select[name="state_id"]').html(resp);
                        $('.loader').hide();
                    }
                });
            }  
        });

        $('#register-form').validate({
            rules:{
                first_name:"required",
                last_name:"required",
                email:{
                    required :true,
                    email:true
                },
                password: {
                    required: true,
                    minlength: 5
                },
                confirm_password: {
                    required: true,
                    minlength: 5,
                    equalTo: "#password"
                },
                country_id:"required",
                state_id:"required",
                agree:"required"
            },
            messages: {
                agree: "Please accept our policy"
            }
        });
    });
</script>