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
                                <select name="db" id="portfolio" class="form-control "
                                        onchange="doportfolioChange(this.value)">
                                    <option value="">--Select Portfolio--</option>
                                    @foreach($porfolios as $databse)
                                        <option value="{{$databse->portfolio}}">{{$databse->portfolio}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="db" id="dbName" class="form-control">
                                    <option value="">--Select Database--</option>
                                    @foreach(explode(',',env('DB_used')) as $databse)
                                        <option value="{{$databse}}">{{$databse}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div id="searchFilter" class="col-md-3">


                            </div>
                            <div class="col-md-4">
                                <button type="button" class="btn btn-outline-primary" value="Toggle Filters"
                                        onclick="oncleardata()"><i class="fas fa-clean"></i> Clear Filters
                                </button>
                                <button type="button" class="btn btn-outline-danger" value="Toggle Filters"
                                        onclick="ondelete()"><i class="fas fa-clean"></i> Delete
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


        <div class="dynamic_table" style="background-color: white;width:100%"></div>
    </div>

    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <Label>Enter the Notes</Label>
                    <textarea id="id_val"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="saveNotes()">Save changes</button>
                </div>
            </div>
        </div>
    </div>

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
{{--    <script src="{{asset('assets/js/buttons.html5.min.js')}}"></script>--}}
{{--    <script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>--}}
{{--    <script src="https://cdn.datatables.net/buttons/1.6.4/js/dataTables.buttons.min.js"></script>--}}
{{--    <script src="https://cdn.datatables.net/buttons/1.6.4/js/buttons.flash.min.js"></script>--}}
{{--    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>--}}
{{--    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>--}}
{{--    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>--}}
{{--    <script src="https://cdn.datatables.net/buttons/1.6.4/js/buttons.html5.min.js"></script>--}}
{{--    <script src="https://cdn.datatables.net/buttons/1.6.4/js/buttons.print.min.js"></script>--}}
    {{--    <script src="https://code.jquery.com/jquery-1.11.1.min.js" crossorigin="anonymous"></script>--}}
    {{--    <script src="//cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>--}}
{{--    <script src="//cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js"></script>--}}


{{--    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/md5.js"></script>--}}

    <script type="text/javascript">
        var table_datables = ''
        var portfolio_id = ''
        var idval=''
        var userid = "{{\Illuminate\Support\Facades\Auth::user()->id}}"
        var roleid = "{{\Illuminate\Support\Facades\Auth::user()->getRoleNames()[0]}}"
        function toggleFilters() {
            $("#filters-div").toggle();
        }

        function doportfolioChange(portfolio) {
            portfolio_id = portfolio
            buildDataTable(portfolio)
        }

        function ondelete()
        {
            if(portfolio_id =='')
            {
                alert('Please selet the portfolio')
                return
            }
            url = "{{route('bulk.delete',':id')}}"
            url = url.replace(':id', portfolio_id)
            $.ajax({
                url: url,
                // serializes the form's elements.
                beforeSend: function () { // Before we send the request, remove the .hidden class from the spinner and default to inline-block.
                    $('#loader').removeClass('hidden')
                }, success: function (data) {
                    oncleardata()
                }, complete: function () { // Set our complete callback, adding the .hidden class and hiding the spinner.
                    $('#loader').addClass('hidden')

                },
            })

        }

        function oncleardata() {
            url = "{{route('bulk.search')}}"
            window.location.href = url

        }

        function searchval(val, searchtype) {
            dbname = $("#dbName").val()
            console.log(dbname)
            if (dbname == '') {
                alert("Please select the db")
                return
            }

            url = "{{route('search')}}?dbname=" + dbname + "&" + searchtype + "=" + val

            window.open(url, '_blank');


        }

        function buildDataTable(pfname) {

            var url = "{{route('bulk.searchData', ':id')}}"
            url = url.replace(':id', pfname)


            $.ajax({

                url: url,
                // serializes the form's elements.
                beforeSend: function () { // Before we send the request, remove the .hidden class from the spinner and default to inline-block.
                    $('#loader').removeClass('hidden')
                },
                success: function (data) {
                    succData = JSON.parse(data)

                    field_res = succData['res_arr']

                    if (table_datables != '') {
                        table_datables.destroy();
                        $("#example").remove()
                    }
                    if (field_res.length != 0) {
                        $(".dynamic_table").append('<table id=\"example\" class=\"display\" style=\"width:100%\"></table>')

                        var my_columns = [];
                        $.each(field_res[0], function (key, value) {
                            var my_item = {};
                            console.log(key)
                            if (key == 'id') {
                                my_item.data = key;
                                my_item.title = 'Ref_ID';
                            }  if (key == 'bank_account') {
                                my_item.data = key;
                                my_item.title = "Account";
                            }if (key == 'updated_at') {
                                my_item.data = key;
                                my_item.title = "Action";
                            }
                            else {
                                my_item.data = key;
                                my_item.title = key;
                            }

                            my_columns.push(my_item);
                        });

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
                                    targets: 3,
                                    render: function (data, type, full, meta) {
                                        var str = "mobile"
                                        var mobilearr = data.split(/,/g);
                                        var retstr = "";

                                        for(var i=0;i<mobilearr.length;i++)
                                        {
                                            if(mobilearr[i] != ''){
                                                retstr+= '<a href="#" onclick="searchval(' + mobilearr[i] + ', \'' + str + '\')">' + mobilearr[i] + '</a> &nbsp;'
                                                     '<a style="cursor:pointer" onclick="watsappcall('+mobilearr[i]+')"><i class="fab fa-whatsapp" aria-hidden="true"></i></a>';
                                            }

                                        }

                                        return retstr

                                    }
                                },
                                {
                                    targets: 4,
                                    render: function (data, type, full, meta) {
                                        var str = "cname"

                                        return '<a href="#" onclick="searchval(\'' + data + '\', \'' + str + '\')">' + data + '</a>';
                                    }
                                },
                                {
                                    targets:0 ,
                                    render: function (data, type, full, meta) {
                                        var btns = data+"<br\>"
                                        if(roleid == 'super-admin')
                                        {
                                            btns +='<button type="button" onclick="delete_portfolio(\''+data+'\')" class="btn btn-danger" > Delete</button>'
                                        }
                                        btns += '<button type="button" onclick="edit(\''+data+'\')" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">Edit</button>'
                                        return btns

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

        function edit(id) {
            idval = id
            url = "{{route('bulk.getlead',[':id'])}}"
            url = url.replace(':id', idval)
            $("#id_val").val('')
            $.ajax({
                url: url,
                // serializes the form's elements.
                beforeSend: function () { // Before we send the request, remove the .hidden class from the spinner and default to inline-block.
                    $('#loader').removeClass('hidden')
                }, success: function (data) {
                    portfolio = JSON.parse(data);
                    $("#id_val").val(portfolio.notes);
                }, complete: function () { // Set our complete callback, adding the .hidden class and hiding the spinner.
                    $('#loader').addClass('hidden')

                },
            })

        }

        function saveNotes()
        {

            notes = $("#id_val").val()
            url = "{{route('bulk.edit',[':id',':notes'])}}"

            url = url.replace(':id', idval)
            url = url.replace(':notes', notes)

            $.ajax({
                url: url,
                // serializes the form's elements.
                beforeSend: function () { // Before we send the request, remove the .hidden class from the spinner and default to inline-block.
                    $('#loader').removeClass('hidden')
                }, success: function (data) {

                    buildDataTable(portfolio_id)
                }, complete: function () { // Set our complete callback, adding the .hidden class and hiding the spinner.
                    $('#loader').addClass('hidden')

                },
            })
        }

        function delete_portfolio(id)
        {
            url = "{{route('bulk.delete',':id')}}"
            url = url.replace(':id', id)
            $.ajax({
                url: url,
                // serializes the form's elements.
                beforeSend: function () { // Before we send the request, remove the .hidden class from the spinner and default to inline-block.
                    $('#loader').removeClass('hidden')
                }, success: function (data) {

                    buildDataTable(portfolio_id)
                }, complete: function () { // Set our complete callback, adding the .hidden class and hiding the spinner.
                    $('#loader').addClass('hidden')

                },
            })
        }

    </script>
@endpush
