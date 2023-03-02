@if($filterSearch == 'mobile')
<div class="col-md-6">
    <div class="card">
        <div class="card-body" style="padding:10px;">
            <h3 class="card-title">Search By Mobile Number</h3>
            <form class="form-inline1" id="search-filter" action="{{route('search')}}" method="POST">
                <div class="form-row">
                    <div class="form-group col-md-8">
                        <input type="text" maxlength="13" class="form-control"  name="mobile" id="inputMobileNumber" placeholder="Mobile Number">
                    </div>
                    <div class="form-group col-md-2">
                        <button type="button"  title="Search by Mobile" onclick="searchData()" class="btn btn-primary form-control"><i class="fas fa-search"></i></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@elseif ($filterSearch == 'cname')
<div class="col-md-6">
    <div class="card">
        <div class="card-body" style="padding:10px;">
            <h3 class="card-title">Search By Customer Name</h3>

            <form class="form-inline1" id="search-filter" action="{{route('search')}}" method="POST">
                <div class="form-row">
                    <div class="form-group col-md-8">
                        <input type="text" maxlength="100" class="form-control"  name="cname" id="inputMobileNumber" placeholder="Enter Customer Name">
                    </div>
                    <div class="form-group col-md-2">
                        <button type="button" title="Full Search" onclick="searchData()" class="form-control btn btn-primary "><i class="fas fa-search"></i></button>
                    </div>
                    <div class="form-group col-md-2">
                        <button type="button" title="Partial Search" onclick="searchData('partial')" class="form-control btn   btn-dark  "><i class="fas fa-search "></i></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@elseif ($filterSearch == 'uid')
    <div class="col-md-6">
        <div class="card">
            <div class="card-body" style="padding:10px;">
                <h3 class="card-title">Search By UID/ADR</h3>

                <form class="form-inline1" id="search-filter" action="{{route('search')}}" method="POST">
                    <div class="form-row">
                        <div class="form-group col-md-8">
                            <input type="text" maxlength="80" class="form-control"  name="uid" id="inputMobileNumber" placeholder="UID/ADR">
                        </div>
                        <div class="form-group col-md-2">
                            <button type="button" title="Full Search" onclick="searchData()" class="form-control btn btn-primary "><i class="fas fa-search"></i></button>
                        </div>
                        <div class="form-group col-md-2">
                            <button type="button" title="Partial Search" onclick="searchData()" class="form-control btn   btn-dark  "><i class="fas fa-search "></i></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

@elseif ($filterSearch == 'email')
    <div class="col-md-6">
        <div class="card">
            <div class="card-body" style="padding:10px;">
                <h3 class="card-title">Search By Email</h3>

                <form class="form-inline1" id="search-filter" action="{{route('search')}}" method="POST">
                    <div class="form-row">
                        <div class="form-group col-md-8">
                            <input type="text" maxlength="70" class="form-control"  name="email" id="inputMobileNumber" placeholder="Email">
                        </div>
                        <div class="form-group col-md-2">
                            <button type="button" title="Full Search" onclick="searchData()" class="form-control btn btn-primary "><i class="fas fa-search"></i></button>
                        </div>
                        <div class="form-group col-md-2">
                            <button type="button" title="Partial Search" onclick="searchData('partial')" class="form-control btn   btn-dark  "><i class="fas fa-search "></i></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

@elseif ($filterSearch == 'altno')
    <div class="col-md-6">
        <div class="card">
            <div class="card-body" style="padding:10px;">
                <h3 class="card-title">Search By ALT NO</h3>

                <form class="form-inline1" id="search-filter" action="{{route('search')}}" method="POST">
                    <div class="form-row">
                        <div class="form-group col-md-8">
                            <input type="text" maxlength="90" class="form-control"  name="altno" id="inputMobileNumber" placeholder="ALT NO">
                        </div>
                        <div class="form-group col-md-2">
                            <button type="button" title="Full Search" onclick="searchData()" class="form-control btn btn-primary "><i class="fas fa-search"></i></button>
                        </div>
                        <div class="form-group col-md-2">
                            <button type="button" title="Partial Search" onclick="searchData('partial')" class="form-control btn   btn-dark  "><i class="fas fa-search "></i></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>


@elseif ($filterSearch == 'ladd')
<div class="col-sm-6 " id="uid-email-altnumber">
    <div class="card">
        <div class="card-body"  style="padding:10px;">
            <h3 class="card-title">Search By Adress Keywords</h3>

            <form class="form-inline1" id="search-filter" action="{{route('search')}}" method="POST">
                <div class="form-row">
                    <div class="form-group col-md-7">
                        <input type="text" maxlength="90" class="form-control"  name="ladd" id="inputUID" placeholder="Area/ Street/ Building Name">
                    </div>
                    <div class="form-group col-md-3">
                        <input type="text" maxlength="6" class="form-control"  name="pincode" id="inputAltNo" placeholder="Pincode" >
                    </div>
                    <div class="form-group col-md-2">
                        <button type="button" onclick="searchData()" class="btn btn-primary form-control"><i class="fas fa-search"></i></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@elseif ($filterSearch == 'cname_ladd')
<div class="col-md-6">
    <div class="card">
        <div class="card-body" style="padding:10px;">
            <h3 class="card-title">Search By Name/Father/Surname & Area/Pincode</h3>

            <form class="form-inline1" id="search-filter" action="{{route('search')}}" method="POST">
                <div class="form-row">
                    <div class="form-group col-md-7">
                        <input type="text" maxlength="70" class="form-control"  name="cname" id="inputUID" placeholder="Name/ Father/ Surname Name">
                    </div>
                    <div class="form-group col-md-3">
                        <input type="text" maxlength="90" class="form-control "  name="ladd" id="inputAltNo" placeholder="Area/ Pincode">

                    </div>
                    <div class="form-group col-md-2">
                        <button type="button" onclick="searchData()" class="btn btn-primary form-control "><i class="fas fa-search"></i></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
