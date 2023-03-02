@extends('layouts.app')

@section('content')

    <div class="form-row bg-primary1 backgroup-white">
        <div class="col-md-12">
            <div class="card mb-3">
                <form id="search_form_data" action="{{route('search')}}" method="POST">
                    <div class="card-header bg-transparent" id="search_form" style="padding:2px;">
                        <div class="form-row text-center d-flex justify-content-center">
                            {{--                            <div class="col-md-2 ">--}}
                            {{--                                <h3 class="mb-0">Select DB</h3>--}}
                            {{--                            </div>--}}
                            <div class="col-md-2">
                                <select name="db" id="stateName" class="form-control " onchange="doDBChange('state')">
                                    <option value="">--Select State--</option>
                                    @foreach($statesNames as $databse)
                                        <option value="{{$databse}}">{{$databse}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <select name="db" id="dbName" class="form-control " onchange="doDBChange('db')">
                                    <option value="">--Select Database--</option>
                                    @foreach(explode(',',env('DB_used')) as $databse)
                                        <option value="{{$databse}}">{{$databse}}</option>
                                    @endforeach
                                </select>
                            </div>
                            {{--                            <div class="col-md-4">--}}

                            {{--                                        <form lass="form-inline" id="search-mobile" action="{{route('search')}}" method="POST">--}}
                            {{--                                            <div class="form-group mx-sm-3 mb-2 row">--}}
                            {{--                                                <input type="text" maxlength="13" class="form-control col-md-7"  name="mobile" id="inputMobileNumber" placeholder="Mobile Number">--}}
                            {{--                                                <button type="button" onclick="searchData('search-mobile', 'mobile')" class="btn btn-primary card-margin-10"><i class="fas fa-search"></i></button>--}}
                            {{--                                            </div>--}}
                            {{--                                        </form>--}}
                            {{--                            </div>--}}
                            <div id="searchFilter" class="col-md-3">


                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-outline-danger" value="Toggle Filters"
                                        onclick="oncleardata()"><i class="fas fa-clean"></i> Clear Filters
                                </button>
                            </div>
                            {{--                            <div class="col-md-2">--}}
                            {{--                                <button type="button" class="btn btn-outline-info" value="Toggle Filters" onclick="toggleFilters()"> <i class="fas fa-eye"></i> Toggle Filters </button>--}}
                            {{--                            </div>--}}
                        </div>
                    </div>
                </form>
            </div>
            <!-- FILTERS START -->
            <div id="filters-div">
                <div class="row card-margin-12 text-center d-flex justify-content-center" id="searchByFilterOption">

                </div>
            </div>
            <!-- FILTERS END -->
            <div class="col-md-4" style="margin-left: auto;margin-right: auto">
                <div id="loader" class="lds-dual-ring hidden overlay"></div>
            </div>
        </div>


        <div class="dynamic_table" style="background-color: white;"></div>
    </div>

    <input type="hidden" id="searchByFilter"/>

@endsection
@push('scripts')
    <script src="{{asset('assets/js/jquery-3.5.1.js')}}"></script>
    <script src="{{asset('assets/js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('assets/js/dataTables.buttons.min.js')}}"></script>
    <script src="{{asset('assets/js/buttons.flash.min.js')}}"></script>
    <script src="{{asset('assets/js/jszip.min.js')}}"></script>
    <script src="{{asset('assets/js/pdfmake.min.js')}}"></script>
    <script src="{{asset('assets/js/vfs_fonts.js')}}"></script>
    <script src="{{asset('assets/js/buttons.html5.min.js')}}"></script>
    <script src="{{asset('assets/js/buttons.print.min.js')}}"></script>
    <script src="{{asset('assets/js/dataTables.bootstrap4.min.js')}}"></script>
    <script src="{{asset('assets/js/md5.js')}}"></script>

    <script type="text/javascript">
        var table_datables = ''
        var bulk_search=0
        db_request = "{{$dbname}}"
        state_request = "{{$state}}"

        if (db_request != '') {
            $("#stateName").prop('disabled', 'disabled');
            $("#dbName").val(db_request)
            bulk_search = 1
            var currenturl = window.location.href;
            var newurl = currenturl.split("?")

            window.history.pushState("", "Title", newurl[0]);

            buildDataTable('FullSearch', 'get')
        }
        if (state_request != '') {
            $("#dbName").prop('disabled', 'disabled');
            $("#stateName").val(state_request)
            bulk_search = 1
            var currenturl = window.location.href;
            var newurl = currenturl.split("?")

            window.history.pushState("", "Title", newurl[0]);

            buildDataTable('FullSearch', 'get')
        }

        function toggleFilters() {
            $("#filters-div").toggle();
        }

        function doDBChange(type) {
            if(type=='state')
            {
                $("#dbName").prop('disabled', 'disabled');
                var db = $("#stateName").val();
            }else {
                $("#stateName").prop('disabled', 'disabled');
                var db = $("#dbName").val();
            }
            var detail_search = "{{\Illuminate\Support\Facades\Auth::user()->detail_search}}"

            if(detail_search == 0 && bulk_search==1)
            {
                return false
            }
            var url = "{{route('searchFilter')}}"

            $.ajax({
                type: "POST",
                url: url,
                data: {
                    "_token": "{{ csrf_token() }}",
                    "db": db
                },
                success: function (data) {

                    $("#searchByFilterOption").html('');
                    $("#dynamic_table").html('');
                    $("#searchFilter").html(data);
                }
            });
            return false;
        }

        function searchByFilterOption(searchby) {
            var db = $("#dbName").val();
            var searchByFilter = searchby;
            var url = "{{route('searchByFilterForm')}}"
            $("#searchByFilter").val(searchby)
            $.ajax({
                type: "POST",
                url: url,
                data: {
                    "_token": "{{ csrf_token() }}",
                    "db": db,searchByFilter

                },
                success: function (data) {
                    $("#searchByFilterOption").html(data);
                }
            });
            return false;
        }

        function oncleardata() {
            url = "{{route('search')}}"
            window.location.href = url

        }

        function searchData(searchType = 'FullSearch') {
            showCreditBalance();
            buildDataTable(searchType);

        }

        function searchval(val, searchtype) {
            dbname = $("#dbName").val()
            stateName = $("#stateName").val()
            if (dbname == '' && stateName=='') {
                alert("Please select the db or state")
                return
            }
            if($("#dbName").is(':disabled'))
            {
                url = "{{route('search')}}?statename=" + stateName + "&" + searchtype + "=" + val

            }else{
                url = "{{route('search')}}?dbname=" + dbname + "&" + searchtype + "=" + val
            }

            window.open(url, '_blank');


        }

        function buildDataTable(searchType, filter = 'post') {
            var db = $("#dbName").val();
            var stateName = $("#stateName").val();
            var db_request = "{{ $dbname }}"
            var state_request = "{{ $state }}"
            var searchByFilter = $("#searchByFilter").val();
            var formData = $("#search-filter").serialize();
            var state=false
            var serchval=''
            if($("#dbName").is(':disabled'))
            {
                state = true;
            }


            if (db_request != '' && filter == 'get') {
                db = db_request
                serachkey = "{{$searchkey}}"
                mobile = "{{ $mobile }}"
                cname = "{{ $cname }}"
                uid = "{{ $uid }}"
                searchType = 'FullSearch'
                if (serachkey == 'mobile') {
                    serchval = mobile
                } else if (serachkey == 'cname') {
                    serchval = cname
                }else if (serachkey == 'uid') {
                    serchval = uid
                }

                formData = serachkey + "=" + serchval
                searchByFilter = serachkey
            }
            if (state_request != '' && filter == 'get') {
                stateName = state_request
                serachkey = "{{$searchkey}}"
                mobile = "{{ $mobile }}"
                cname = "{{ $cname }}"
                uid = "{{ $uid }}"

                searchType = 'FullSearch'
                if (serachkey == 'mobile') {
                    serchval = mobile
                } else if (serachkey == 'cname') {
                    serchval = cname
                }else if (serachkey == 'uid') {
                    serchval = uid
                }
                formData = serachkey + "=" + serchval
                searchByFilter = serachkey
            }

            var url = "{{route('searchData')}}"
            if(state)
            {
                var url = "{{route('searchDataForState')}}"

                var data = {
                    "_token": "{{ csrf_token() }}",
                    'formData': formData,
                    "state": stateName,
                    "searchByFilter": searchByFilter,
                    "searchType": searchType
                };
            }else{

                var data = {
                    "_token": "{{ csrf_token() }}",
                    'formData': formData,
                    "db": db,
                    "searchByFilter": searchByFilter,
                    "searchType": searchType
                };
            }
            searchkey = formData.split("=")[1]
            // console.log(data);return false;

            $.ajax({
                type: "POST",
                url: url,
                data: data, // serializes the form's elements.
                beforeSend: function () { // Before we send the request, remove the .hidden class from the spinner and default to inline-block.
                    $('#loader').removeClass('hidden')
                },
                success: function (data) {
                    succData = JSON.parse(data)
                    if (succData['stop'] == true) {
                        alert("Recharge Your Credit Balance!");
                        // jQuery.confirm({
                        //     icon: 'fas fa-wind-warning',
                        //     closeIcon: true,
                        //     title: 'Are you sure Reload Blance!',
                        //     content: 'You can not undo this action.!!',
                        //     type: 'red',
                        //     typeAnimated: true,
                        //     buttons: {
                        //         cancel: function () {
                        //         }
                        //     }
                        // });
                        return false;
                    }

                    field_res = succData['res_arr'];

                    if (table_datables != '') {
                        table_datables.destroy();
                        $("#example").remove()
                    }
                    if (field_res.length != 0) {
                        $(".dynamic_table").append('<table id=\"example\" class=\"display\" style=\"width:100%\"></table>')

                        var my_columns = [];
                        $.each(field_res[0], function (key, value) {
                            var my_item = {};
                            my_item.data = key;
                            my_item.title = key;
                            my_columns.push(my_item);
                        });

                        var columnName = ''
                        table_datables = $('#example').DataTable({
                            dom: 'lBfrtip',
                            bProcessing: true,
                            data: field_res,
                            columns: my_columns,
                            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
                            buttons: [
                                'csv', 'excel'
                            ],
                            columnDefs: [
                                {
                                    targets: '_all',
                                    render: function (data, type, full, meta) {
                                        columnName = meta.settings.aoColumns[meta.col].data;
                                        var str = columnName
                                        if(columnName == 'mobile'){

                                            if(data == searchkey)
                                            {
                                                return '<a href="#" style="background-color:yellow" onclick="searchval(' + data + ', \'' + str + '\')">' + data + '</a> &nbsp;'+
                                                    '<a style="cursor:pointer" onclick="watsappcall('+data+')"><i class="fab fa-whatsapp" aria-hidden="true"></i></a>';
                                            }else{
                                                return '<a href="#"  onclick="searchval(' + data + ', \'' + str + '\')">' + data + '</a> &nbsp;'+
                                                    '<a style="cursor:pointer" onclick="watsappcall('+data+')"><i class="fab fa-whatsapp" aria-hidden="true"></i></a>';
                                            }

                                        }
                                        else if(columnName == 'altno'){
                                            if(data == searchkey)
                                            {
                                                return '<a href="#" style="background-color:yellow" onclick="searchval(' + data + ', \'' + str + '\')">' + data + '</a> &nbsp;'+
                                                    '<a style="cursor:pointer" ><i class="fab fa-whatsapp" aria-hidden="true"></i></a>';
                                            }else {
                                                return '<a href="#"  onclick="searchval(' + data + ', \'' + str + '\')">' + data + '</a> &nbsp;'+
                                                    '<a style="cursor:pointer" ><i class="fab fa-whatsapp" aria-hidden="true"></i></a>';
                                            }
                                        }
                                        else if(columnName == 'cname'){

                                            if(data.toLowerCase().includes(searchkey.toLowerCase())){
                                                return '<a href="#" style="background-color:yellow" onclick="searchval(\''+ data +'\', \'' + str + '\')">' + data + '</a>';
                                            }else{
                                                return '<a href="#" onclick="searchval(\''+ data +'\', \'' + str + '\')">' + data + '</a>';
                                            }
                                        }else if(columnName == 'adr'){
                                            str = 'uid'
                                            return '<a href="#" onclick="searchval(\''+ data +'\', \'' + str + '\')">' + data + '</a>';
                                        }else if(columnName == 'fname'){
                                            str = 'cname'
                                            if(data != null) {
                                                if (data.toLowerCase().includes(searchkey.toLowerCase())) {
                                                    return '<a href="#" style="background-color:yellow" onclick="searchval(\'' + data + '\', \'' + str + '\')">' + data + '</a>';
                                                } else {
                                                    return '<a href="#" onclick="searchval(\'' + data + '\', \'' + str + '\')">' + data + '</a>';
                                                }
                                            }else{
                                                return data
                                            }
                                        }

                                        else {
                                            return data
                                        }

                                    }
                                }

                            ],
                            error: function (jqXHR, textStatus, errorThrown) {
                                console.log(errorThrown)
                            }

                        });
                    }
                }, complete: function () { // Set our complete callback, adding the .hidden class and hiding the spinner.
                    $('#loader').addClass('hidden')
                },

            });
            return false;
            // $("#search_form_data").submit();
        }

        function watsappcall(mobilenum) {

            window.open(
                "https://api.whatsapp.com/send/?phone=91" + mobilenum + "&text=hi",
                "_blank"
            )
        }


    </script>
@endpush
