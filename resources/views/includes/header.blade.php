<nav class="navbar navbar-top navbar-expand navbar-dark bg-primary border-bottom" style="padding-bottom: 0px;">
    <div class="container-fluid">
        <div class="navbar-header" id="navbarSupportedContent"></div>
{{--        <ul class="nav navbar-nav">--}}
{{--            <li class="active"><a href="#">Home</a></li>--}}
{{--            <li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#">Page 1 <span--}}
{{--                        class="caret"></span></a>--}}
{{--                <ul class="dropdown-menu">--}}
{{--                    <li><a href="#">Page 1-1</a></li>--}}
{{--                    <li><a href="#">Page 1-2</a></li>--}}
{{--                    <li><a href="#">Page 1-3</a></li>--}}
{{--                </ul>--}}
{{--            </li>--}}
{{--        </ul>--}}
{{--        <div class="alert alert-warning alert-dismissible fade show" role="alert">--}}
{{--            This is a success alert—check it out!--}}
{{--            <button type="button" class="btn btn-primary">--}}
{{--                Notifications <span class="badge badge-light">4</span>--}}
{{--            </button>--}}


{{--        </div>--}}

                    <div class="alert alert-warning alert-dismissible fade show " id="credit-box" style="margin: 0px;" role="alert">
                        <strong  >Your Credit Details!</strong>&nbsp;
                       <span class="badge badge-warning" id="usedCredits" style="font-size: larger">0</span>&nbsp;
                        <span class="badge badge-success" id="totalCredits"style="font-size: larger" >0</span>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>


                    <ul class="nav navbar-nav navbar-right">
                        <li class="nav-item dropdown">
                            <a class="nav-link pr-0" href="#" role="button" data-toggle="dropdown" aria-haspopup="true"
                               aria-expanded="false">
                                <div class="media align-items-center">
                              <span class="avatar avatar-sm rounded-circle">
                                @if (Auth::user()->profile_photo)
                                      <img width="45" height="45" class="img-fluid rounded-pill"
                                           src="{{ asset(Auth::user()->profile_photo) }}" alt="">
                                  @else
                                      <i class="far avatar avatar-sm rounded-circle fa-user"></i>

                                  @endif
                              </span>
                                    <div class="media-body  ml-2  d-none d-lg-block">
                                        <span class="mb-0 text-sm  font-weight-bold">{{ Auth::user()->name }}</span>
                                    </div>
                                </div>
                            </a>
                            <div class="dropdown-menu  dropdown-menu-right ">
                                <div class="dropdown-header noti-title">
                                    <h6 class="text-overflow m-0">Welcome!</h6>
                                </div>
                                <a href="{{ route('profile.edit', auth()->user()) }}" class="dropdown-item">
                                    <i class="ni ni-single-02"></i>
                                    <span>My profile</span>
                                </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="{{ route('logout') }}"
                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                        <i class="ni ni-user-run"></i>
                        <span>Logout</span>
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            </li>
        </ul>
    </div>
</nav>
@push('scripts')
<script type="text/javascript">
    function showCreditBalance()
    {
        url = "{{route('ajax-credits')}}";
        $.ajax({
            type: "GET",
            contentType: "application/json",
            url: url,
            data: {
                "_token": "{{ csrf_token() }}"
            },
            success: function (data) {
                var data = JSON.parse(data);
                if (data.usedCredits == data.totalCredits) {
                    $("#credit-box").html("Recharge your Credit Balance!");
                    $("#credit-box").addClass('text-danger');
                }
                $("#usedCredits").html( data.usedCredits);
                $("#totalCredits").html( data.totalCredits);
            },
            complete: function () { // Set our complete callback, adding the .hidden class and hiding the spinner.

            },
        });
    }
    // setInterval(function() {
    //     showCreditBalance();
    // }, 60 * 1000); //do call for 1 min
    showCreditBalance();
</script>

@endpush
