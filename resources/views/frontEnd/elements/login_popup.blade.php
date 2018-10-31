<div class="popup-wrap dd-nn" id="login-popup" > <!-- style="display: none" -->
    <form method="post" action="{{ url('login') }}" id="login-form">
        <div class="popup-body">
            <h2 class="title">members Login</h2>
            <ul class="reg-fields">
                <li>
                    <div class="col1">
                        <div class="input-wrap email-icon icons">
                            <input name="email" type="email" placeholder="Email Address"  />
                        </div>
                        <label class="check-term"><input type="checkbox" />Remember me</label>
                    </div>
                    <div class="col2">
                        <div class="input-wrap password-icon icons">
                            <input name="password" type="password"   placeholder="Password"/>
                        </div>
                        <a href="#" class="forgot"  >Forgot your password?</a>
                    </div>
                </li>
                <li>
                    <div class="col1">
                        {{ csrf_field() }}
                        <button class="create-button icons " type="submit">sign in</button>
                        }
                    </div>                        
                </li>
                <li>
                    <div class="or"><span>or</span></div>
                </li>
                <li>
                    <div class="col1">
                        <a href="#" class="fb-btn-signin"></a>
                    </div>
                    <div class="col2">
                        <a class="create-button icons signup-btn" href="javascript:void(0)">create your account</a>
                    </div>
                </li>
            </ul>
            <div class="clear"></div>
        </div>
    </form>
</div>


<script type="text/javascript">   
    $('#login-form').validate({
        rules:{
            email:{
                required :true,
                email:true
            },
            password: {
                required: true,
            }
        }
    });
</script>