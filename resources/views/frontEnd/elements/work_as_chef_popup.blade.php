        <div class="popup-wrap workas-chef dd-nn" id="workas-chef-popup">
            <div class="popup-body">
                <h2 class="title">Work as a chef</h2>
                <article>
                    <h3>Interested in working for At Click chefs?</h3>
                    <p>Seeking Highly Experienced Chefs to service part-time bookings.</p>
                    <p>
                        We are always looking for experienced, well presented, creative chefs with 
                        exceptional standards to join our team. The work is ad hoc and mostly weekends, servicing in-home dining bookings.</p>
                    <p>The ideal individuals are a chef working in a top restaurant that is interested in some additional part time work at competitive rates with less stress than a busy commercial kitchen and a lot more praise!</p>
                    <p>The suitable Chefs will have excellent culinary skills and experience working in top restaurants, cooking a variety of a la carte, fine dining quality cuisine with first class presentation skills.</p>
                    <div class="skills">
                        <h3>Skills</h3>
                        <ul>
                            <li>7+ years experience in restaurant or similar position</li>
                            <li>Strong interpersonal skills</li>
                            <li>Well presented</li>
                            <li>Strong budgeting skills</li>
                            <li>Must speak English</li>
                            <li>Must have own car and clean license</li>
                            <li>Must be able to work as a supplier to At Your Table Pty - ABN or equivalent</li>
                            <li>Personal liability insurance</li>
                            <li> An understanding of kitchen financials, budgets and targets</li>
                            <li>Planning, assigning and directing work</li>                           
                        </ul>
                    </div>
                </article>
                <aside>
                    <h3>Apply</h3>
                    <form method="post" action="{{ url('register/chef') }}" id="chef-register-form" enctype="multipart/form-data">
                        <ul class="reg-fields">
                            <li>
                                <div class="col1">
                                    <div class="input-wrap">
                                        <input name="first_name" type="text" placeholder="First Name" />
                                    </div>
                                </div>
                                <div class="col2">
                                    <div class="input-wrap">
                                        <input name="last_name" type="text" placeholder="Last Name"/>
                                    </div>
                                </div>
                                <div class="clear"></div>
                            </li>
                            <li>
                                <div class="input-wrap">
                                    <input name="email" type="email" placeholder="Email"/>
                                </div>
                            </li>
                            <li>
                                <div class="input-wrap">
                                    <input name="password" type="password" placeholder="Password" id="chef-password" />
                                </div>
                            </li>
                            <li>
                                <div class="input-wrap">
                                    <input name="confirm_password"  type="password" placeholder="Confirm Password" />
                                </div>
                            </li>
                            <li>
                                <div class="input-wrap">
                                    <input name="phone" type="text"   placeholder="Phone"/>
                                </div>
                            </li>
                            <li>
                                <div class="input-wrap">                               
                                    <select name="state_id">
                                        <option value="">Select State</option>
                                        @foreach($aus_states as $aus_state)
                                            <option value="{{ $aus_state['id'] }}">{{ $aus_state['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </li>
                            <li>
                                <div class="input-wrap">
                                    <input name="city" type="text" placeholder="city"/>
                                </div>
                            </li>
                            <li>
                                <div class="input-wrap file-upload">
                                    <input type="text" placeholder="Upload your CV" />
                                    <label for="browse" class="browse-btn">Browse</label>>
                                    <input name="cv" type="file" class="cv"  placeholder="city" style="display: none" id="browse"/>
                                </div>
                            </li>
                            <li>
                                {{ csrf_field() }}
                                <button class="create-button icons" type="submit" >sign in</button>
                            </li>
                            
                        </ul>
                    </form>
                </aside>
                <div class="clear"></div>
            </div>
        </div>

<script type="text/javascript">
    $('#chef-register-form').validate({
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
                equalTo: "#chef-password"
            },
            phone:"required",
            state_id:"required",
            city:"required"
            /*cv:{
                // required:true,
                extension: "pdf|doc|docx"
            }*/
        }
    });

    $('.cv').on('change',function(){
        var val = $(this).val();
        switch(val.substring(val.lastIndexOf('.') + 1).toLowerCase()){
            case 'doc': case 'docx': case 'pdf':
                // alert("an image");
                break;
            default:
                $(this).val('');
                // error message here
                alert("File extension should be .doc,docx,.pdf");                
                break;
        }
    })

</script>