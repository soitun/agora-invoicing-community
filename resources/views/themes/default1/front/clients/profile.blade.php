@extends('themes.default1.layouts.front.master')
@section('title')
Profile
@stop
@section('nav-profile')
active
@stop
@section('page-heading')
Profile
@stop
@section('breadcrumb')
@if(Auth::check())
        <li><a class="text-primary" href="{{url('my-invoices')}}">Home</a></li>
    @else
         <li><a class="text-primary" href="{{url('login')}}">Home</a></li>
    @endif
     <li class="active text-dark">Profile</li>
@stop
@section('content')
<style>
.switch {
  position: relative;
  display: inline-block;
  width: 60px;
  height: 34px;
}

.switch input {display:none;}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: .4s;
  transition: .4s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 26px;
  width: 26px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  -webkit-transition: .4s;
  transition: .4s;
}

input:checked + .slider {
  background-color: #2196F3;
}

input:focus + .slider {
  box-shadow: 0 0 1px #2196F3;
}

input:checked + .slider:before {
  -webkit-transform: translateX(26px);
  -ms-transform: translateX(26px);
  transform: translateX(26px);
}

/* Rounded sliders */
.slider.round {
  border-radius: 34px;
}

.slider.round:before {
  border-radius: 50%;
}
.scrollit {
    overflow:scroll;
    height:600px;
}
</style>
<style>

    .required:after{
        content:'*';
        color:red;
        padding:0px;
    }


        .bootstrap-select.btn-group .dropdown-menu li a {
    margin-left: -12px !important;
}
 .btn-group>.btn:first-child {
    margin-left: 0;
    background-color: white;

   }
   .open>.dropdown-menu {
  display: block;
}
.bootstrap-select.btn-group .dropdown-toggle .filter-option {
    color:#555;
}
</style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/css/bootstrap-select.min.css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>


     <div id= "alertMessage"></div>
     <div id= "error"></div>
  @include('themes.default1.user.2faModals')


        <div class="container pt-3 pb-2">

            <div class="row pt-2">

                <div class="col-lg-3 mt-4 mt-lg-0">


                    <aside class="sidebar mt-2 mb-5">

                        <ul class="nav nav-list flex-column">

                            <li class="nav-item">

                                <a class="nav-link active" id="profile_detail" href="#profile" data-bs-toggle="tab" data-hash data-hash-offset="0" data-hash-offset-lg="120" data-hash-delay="500">My Profile</a>
                            </li>

                            <li class="nav-item">

                                <a class="nav-link" id="change_password" href="#password" data-bs-toggle="tab" data-hash data-hash-offset="0" data-hash-offset-lg="120" data-hash-delay="500">Change Password</a>
                            </li>

                            <li class="nav-item">

                                <a class="nav-link" id="two_fa" href="#twofa" data-bs-toggle="tab" data-hash data-hash-offset="0" data-hash-offset-lg="120" data-hash-delay="500">Setup 2 Step Verification</a>
                            </li>
                        </ul>
                    </aside>
                </div>

                <div class="col-lg-9">

                    <div class="tab-pane tab-pane-navigation active" id="profile" role="tabpanel">
                       
                           <div class="row">
                             {!! Form::model($user,['url'=>'my-profile', 'method' => 'PATCH','files'=>true]) !!}
                                                <div class="d-flex justify-content-center mb-4" id="profile_img">

                                            <div class="profile-image-outer-container">
                                                  <?php
                                            $user = \DB::table('users')->find(\Auth::user()->id);
                                            ?>
                                           

                                                <div class="profile-image-inner-container bg-color-primary">
                                                     @if($user && $user->profile_pic)
                                                  
                                                      <img src="{{ asset('storage/common/images/users/' . Auth::user()->profile_pic) }}">

                                                    @else
                                                       <img src="{{ Auth::user()->profile_pic }}">

                                                    @endif


                                                    <span class="profile-image-button bg-color-dark">

                                                        <i class="fas fa-camera text-light"></i>
                                                    </span>
                                                </div>

                                                <input type="file"  name="profile_pic" id="profilePic" class="form-control profile-image-input">
                                            </div>
                                        </div>


                            <div class="col-lg-12 order-1 order-lg-2">

                                    <div class="form-group row {{ $errors->has('first_name') ? 'has-error' : '' }}">
                                        <label class="col-lg-3 col-form-label form-control-label line-height-9 pt-2 text-2 required">First name</label>
                                        <div class="col-lg-9">
                                        {!! Form::text('first_name',null,['class' => 'form-control text-3 h-auto py-2 ','id'=>'firstName']) !!}
                                        <h6 id="firstNameCheck"></h6>
                                        </div>
                                    </div>
                                    <div class="form-group row {{ $errors->has('last_name') ? 'has-error' : '' }}">
                                        <label class="col-lg-3 col-form-label form-control-label line-height-9 pt-2 text-2 required">Last name</label>
                                        <div class="col-lg-9">
                                         {!! Form::text('last_name',null,['class' => 'form-control text-3 h-auto py-2','id'=>'lastName']) !!}
                                         <h6 id="lastNameCheck"></h6>
                                        </div>
                                    </div>
                                    <div class="form-group row {{ $errors->has('email') ? 'has-error' : '' }}">
                                        <label class="col-lg-3 col-form-label form-control-label line-height-9 pt-2 text-2 required">Email</label>
                                        <div class="col-lg-9">
                                             {!! Form::text('email',null,['class' => 'form-control text-3 h-auto py-2','id'=>'Email']) !!}
                                             <h6 id="emailCheck"></h6>
                                        </div>
                                    </div>
                                    <div class="form-group row {{ $errors->has('mobile_code') ? 'has-error' : '' }}">
                                        <label class="col-lg-3 col-form-label form-control-label line-height-9 pt-2 text-2 required">Mobile</label>
                                        <div class="col-lg-9">
                                              {!! Form::hidden('incode',null,['id'=>'code_hidden']) !!}
                                               <!--<input class="form-control selected-dial-code"  id="mobile_code" value="{{$user->mobile}}" name="mobile" type="tel"> -->
                                                
                                            {!! Form::text('mobile',$user->mobile,['class'=>'form-control selected-dial-code', 'type'=>'tel','id'=>'incode']) !!}
                                            <span id="invalid-msg" class="hide"></span>
                                               <span id="inerror-msg" class="hide"></span>
                                        </div>
                                    </div>
                                    <div class="form-group row {{ $errors->has('company') ? 'has-error' : '' }}">
                                        <label class="col-lg-3 col-form-label form-control-label line-height-9 pt-2 text-2">Company</label>
                                        <div class="col-lg-9">
                                            {!! Form::text('company',null,['class' => 'form-control text-3 h-auto py-2','id'=>'Company']) !!}
                                             <h6 id="companyCheck"></h6>
                                        </div>
                                    </div>
                                    <div class="form-group row {{ $errors->has('address') ? 'has-error' : '' }}">
                                        <label class="col-lg-3 col-form-label form-control-label line-height-9 pt-2 text-2">Address</label>
                                        <div class="col-lg-9">
                                        {!! Form::textarea('address',null,['class' => 'form-control text-3 h-auto py-2','id'=>'Address']) !!}
                                           <h6 id="addressCheck"></h6>

                                        </div>
                                    </div>
                                    <div class="form-group row {{ $errors->has('town') ? 'has-error' : '' }}">
                                        <label class="col-lg-3 col-form-label form-control-label line-height-9 pt-2 text-2"></label>
                                        <div class="col-lg-6">
                                         {!! Form::text('town',null,['class' => 'form-control text-3 h-auto py-2','id'=>'Town']) !!}

                                        </div>
                                        <div class="col-lg-3 {{ $errors->has('state') ? 'has-error' : '' }}">
                                           <select name="state" class="form-control text-3 h-auto py-2">
                       
                                                @if(count($state)>0)
                                                
                                                    <option value="{{$state['id']}}">{{$state['name']}}</option>
                                                
                                                <option value="">Select State</option>
                                                @foreach($states as $key=>$value)
                                                
                                                    <option value="{{$key}}">{{$value}}</option>
                                                @endforeach
                                               @endif
                                            </select>
                                            </div>
                                    </div>
                                     <div class="form-group row {{ $errors->has('=country') ? 'has-error' : '' }}">
                                        <label class="col-lg-3 col-form-label form-control-label line-height-9 pt-2 text-2">Country</label>
                                        <div class="col-lg-9">
                                         {!! Form::text('country',$selectedCountry,['class' => 'form-control input-lg','onChange'=>'getCountryAttr(this.value);','readonly'=>'readonly']) !!}
                                            {!! Form::hidden('country',null,['class' => 'form-control input-lg', 'id'=>'country']) !!}
                                         <h6 id="countryCheck"></h6>

                                        </div>
                                    </div>
                                    <div class="form-group row {{ $errors->has('timezone_id') ? 'has-error' : '' }}">
                                        <label class="col-lg-3 col-form-label form-control-label line-height-9 pt-2 text-2">Time Zone</label>
                                        <div class="col-lg-9">
                                            <div class="custom-select-1">
                                            {!! Form::select('timezone_id',[Lang::get('message.choose')=>$timezones],null,['class' => 'form-control input-lg','id'=>'timezone']) !!}

                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="form-group col-lg-9">

                                        </div>
                                        <div class="form-group col-lg-3">
                                            <button type="submit" id="submit" class="btn btn-dark font-weight-bold text-3 btn-modern float-end" data-loading-text="Loading...">Update</button>
                                        </div>
                                    </div>
                               

                            </div>
                            {!! Form::close() !!}
                        </div>
                         

                    </div>

                    <div class="tab-pane tab-pane-navigation" id="password" role="tabpanel">

                        <div class="row">

                            <div class="col-lg-12 order-1 order-lg-2">

                                {!! Form::model($user,['url'=>'my-password' , 'method' => 'PATCH']) !!}

                                    <div class="form-group row {{ $errors->has('old_password') ? 'has-error' : '' }}">
                                        <label class="col-lg-3 col-form-label form-control-label line-height-9 pt-2 text-2 required">Password</label>
                                        <div class="col-lg-9">
                                            {!! Form::password('old_password',['class' => 'form-control text-3 h-auto py-2','id'=>'old_password']) !!}
                                             <h6 id="oldpasswordcheck"></h6>
                                        </div>
                                    </div>

                                    <div class="form-group row {{ $errors->has('new_password') ? 'has-error' : '' }}">
                                        <label class="col-lg-3 col-form-label form-control-label line-height-9 pt-2 text-2 required">New Password</label>
                                        <div class="col-lg-9">
                                            {!! Form::password('new_password',['class' => 'form-control text-3 h-auto py-2','id'=>'new_password']) !!}

                                         <h6 id="newpasswordcheck"></h6>
                                        </div>
                                    </div>

                                    <div class="form-group row {{ $errors->has('confirm_password') ? 'has-error' : '' }}">
                                        <label class="col-lg-3 col-form-label form-control-label line-height-9 pt-2 text-2 required">Confirm password</label>
                                        <div class="col-lg-9">
                                            {!! Form::password('confirm_password',['class' => 'form-control text-3 h-auto py-2','id'=>'confirm_password']) !!}
                                            <h6 id ="confirmpasswordcheck"></h6>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="form-group col-lg-9">

                                        </div>
                                        <div class="form-group col-lg-3">
                                            <button class="btn btn-dark font-weight-bold text-3 btn-modern float-end" data-loading-text="Loading..." id="password">Update</button>
                                        </div>
                                    </div>
                               {!! Form::close() !!}
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane tab-pane-navigation" id="twofa" role="tabpanel">

                        <div class="row pt-5">

                            <div class="d-flex">

                                <div class="pe-3 pe-sm-5 pb-3 pb-sm-0 mt-2">


                                    @if($is2faEnabled ==0)
                                    <img src="{{asset('storage/common/images/authenticator.png')}}" alt="Authenticator" style="margin-top: -6px!important;height:26px;" class="img-responsive img-circle img-sm">&nbsp;Authenticator App
                                @else
                                    <img src="{{asset('storage/common/images/authenticator.png')}}" alt="Authenticator" style="margin-top: -6px!important;height:26px;" class="img-responsive img-circle img-sm">&nbsp;2-Step Verification is ON since {{getTimeInLoggedInUserTimeZone($dateSinceEnabled)}}
                                    <br><br><br>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <button class="btn btn-primary" id="viewRecCode">View Recovery Code</button>
                                        </div>
                                    </div>
                                @endif
                                </div>

                                <div class="form-check form-switch">

                                    <input value="{{$is2faEnabled}}" id="2fa" class="form-check-input" style="padding-right: 2rem;padding-left: 2rem;padding-top: 1rem!important;padding-bottom: 1rem!important;" type="checkbox" role="switch">

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>


<script src="{{asset('common/js/2fa.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/js/bootstrap-select.min.js"></script>

                    <script>

                //Password Validation
                   function oldpasswordcheck(){
                    var oldpassword_val = $('#old_password').val();
                    if(oldpassword_val.length == ''){
                        $('#oldpasswordcheck').show();
                        $('#oldpasswordcheck').html("This field is Required");
                        $('#oldpasswordcheck').focus();
                        $('#old_password').css("border-color","red");
                        $('#oldpasswordcheck').css({"color":"red","margin-top":"5px"});
                        // userErr =false;



                    }

                    else{
                         $('#oldpasswordcheck').hide();
                         $('#old_password').css("border-color","");
                         return true;
                    }
                   }

              function newpasswordcheck(){
              var pattern = new RegExp(/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*(_|[^\w])).+$/);
              if (pattern.test($('#new_password').val())){
                 $('#newpasswordcheck').hide();
                  $('#new_password').css("border-color","");
                 return true;

              }
              else{
                 $('#newpasswordcheck').show();
                $('#newpasswordcheck').html("Password must contain Uppercase/Lowercase/Special Character and Number");
                 $('#newpasswordcheck').focus();
                $('#new_password').css("border-color","red");
                $('#newpasswordcheck').css({"color":"red","margin-top":"5px"});

                   // mail_error = false;
                return false;

              }

            }

                 function confirmpasswordcheck(){
        var confirmPassStore= $('#confirm_password').val();
         var passwordStore = $('#new_password').val();
         if(confirmPassStore != passwordStore){
            $('#confirmpasswordcheck').show();
            $('#confirmpasswordcheck').html("Passwords Don't Match");
            $('#confirmpasswordcheck').focus();
             $('#confirm_password').css("border-color","red");
            $('#confirmpasswordcheck').css("color","red");

         }
        else{
             $('#confirmpasswordcheck').hide();
             $('#confirm_password').css("border-color","");
               return true;
        }
  }



               function updatePassword()
             {
                 $('#oldpasswordcheck').hide();
                   $('#newpasswordcheck').hide();
                    $('#confirmpasswordcheck').hide();
                    if(oldpasswordcheck() && newpasswordcheck() && confirmpasswordcheck() ){
                $("#password").html("<i class='fa fa-circle-o-notch fa-spin fa-1x fa-fw'></i>Updating...");
                 var data = {
                                        "old_password":   $('#old_password').val(),
                                        "new_password" :    $('#new_password').val(),
                                        "confirm_password":  $('#confirm_password').val(),


                            };
                                $.ajax({
                                        url: '{{url('my-password')}}',
                                        type: 'PATCH',
                                        data: data,
                                        success: function (response) {
                                        if(response.type == 'success'){
                                             var result =  '<div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><strong><i class="far fa-thumbs-up"></i>Well Done! </strong>'+response.message+'!</div>';
                                              $('#error').hide();
                                            $('#alertMessage').html(result);
                                            // $('#alertMessage2').html(result);
                                            $("#password").html("Update");
                                              $('html, body').animate({scrollTop:0}, 1000);

                                              // response.success("Success");
                                           } else {
                                             var result =  '<div class="alert alert-warning alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><strong><i class="fas fa-exclamation-triangle"></i>Whoops! Something went wrong..</strong>'+response.message+'!</div>';
                                              $('#error').html(result);
                                            $('#alertMessage').hide();
                                            // $('#alertMessage2').html(result);
                                            $("#password").html("Update");
                                              $('html, body').animate({scrollTop:0}, 1000);
                                           }
                                        },
                                        error: function (data) {
                                             var html = '<div class="alert alert-warning alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><strong><i class="fas fa-exclamation-triangle"></i>Oh Snap! </strong>'+data.responseJSON.message+' <br><ul>';
                                            $("#password").html("Update");
                                              $('html, body').animate({scrollTop:0}, 500);
                                              for (var key in data.responseJSON.errors)
                                            {
                                                html += '<li>' + data.responseJSON.errors[key][0] + '</li>'
                                            }
                                            html += '</ul></div>';
                                           $('#alertMessage').hide();

                                            $('#error').show();
                                             document.getElementById('error').innerHTML = html;

                                        }
                                    });
                            }
                            else{
                                return false;
                            }
             }

                                </script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script src="{{asset('common/js/intlTelInput.js')}}"></script>

<script type="text/javascript">

// get the country data from the plugin
     $(document).ready(function(){
           $(function () {
             //Initialize Select2 Elements
             $('.select2').select2()
         });
 
    var incountry = $('#country').val();

    getCode(incountry);

    var intelInput = $('#incode');
    inaddressDropdown = $("#country");
    proerrorMsg = document.querySelector("#inerror-msg"),
    provalidMsg = document.querySelector("#invalid-msg");
    var inerrorMap = [ "Invalid number", "Invalid country code", "Number Too short", "Number Too long", "Invalid number"];
     let currentCountry="";
    intelInput.intlTelInput({
        initialCountry: "auto",
        geoIpLookup: function (callback) {

            $.get("http://ipinfo.io", function () {}, "jsonp").always(function (resp) {
                resp.incountry = incountry;

                var countryCode = (resp && resp.incountry) ? resp.incountry : "";
                    currentCountry=countryCode.toLowerCase()
                    callback(countryCode);
            });
        },
        separateDialCode: true,
      utilsScript: "{{asset('js/intl/js/utils.js')}}"
    });
     var reset = function() {
      proerrorMsg.innerHTML = "";
      proerrorMsg.classList.add("hide");
      provalidMsg.classList.add("hide");
    };
    setTimeout(()=>{
         intelInput.intlTelInput("setCountry", currentCountry);
    },500)
     $('.intl-tel-input').css('width', '100%');
    intelInput.on('blur', function () {
        reset();
        if ($.trim(intelInput.val())) {
            if (intelInput.intlTelInput("isValidNumber")) {
              $('#incode').css("border-color","");
              provalidMsg.classList.remove("hide");
              $('#submit').attr('disabled',false);
            } else {
              var inerrorCode = intelInput.intlTelInput("getValidationError");
             proerrorMsg.innerHTML = inerrorMap[inerrorCode];
             $('#incode').css("border-color","red");
             $('#inerror-msg').css({"color":"red","margin-top":"5px"});
             proerrorMsg.classList.remove("hide");
             $('#submit').attr('disabled',true);
            }
        }
    });

     inaddressDropdown.change(function() {
     intelInput.intlTelInput("setCountry", $(this).val());
             reset();
             if ($.trim(intelInput.val())) {
            if (intelInput.intlTelInput("isValidNumber")) {
              $('#incode').css("border-color","");
              proerrorMsg.classList.add("hide");
              $('#submit').attr('disabled',false);
            } else {
              var inerrorCode = intelInput.intlTelInput("getValidationError");
             proerrorMsg.innerHTML = inerrorMap[errorCode];
             $('#incode').css("border-color","red");
             $('#inerror-msg').css({"color":"red","margin-top":"5px"});
             proerrorMsg.classList.remove("hide");
             $('#submit').attr('disabled',true);
            }
        }
    });
    $('input').on('focus', function () {
        $(this).parent().removeClass('has-error');
    });

    $('form').on('submit', function (e) {
        $('input[name=sds]').attr('value', $('.selected-dial-code').text());
    });


           


});
    

   function getCountryAttr(val) {
        if(val == 'IN') {
            $('#gstin').show()
        } else {
            $('#gstin').hide()
        }
        getState(val);
        getCode(val);
//        getCurrency(val);

    }



       function getState(val) {


        $.ajax({
            type: "GET",
              url: "{{url('get-state')}}/" + val,
            data: 'country_id=' + val,
            success: function (data) {
                $("#state-list").html(data);
            }
        });
    }
    function getCode(val) {
        $.ajax({
            type: "GET",
            url: "{{url('get-code')}}",
            data: 'country_id=' + val,
            success: function (data) {
            
                // $("#mobile_code").val(data);
                $("#code_hidden").val(data);
            }
        });
    }


</script>
<!-- <script src="{{asset('common/js/licCode.js')}}"></script> -->

@stop


