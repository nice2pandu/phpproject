@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-5">
                <form id="search_form_data" action="{{route('search')}}" method="POST">

                    <div class="card-header bg-transparent" id="search_form">
                        <div class="row">
                            <div class="col-md-4">
                                <h3 class="mb-0">Select DB</h3>
                            </div>

                            <div class="col-md-4">
                                <select onchange="dbselect(this.value)" name="db" class="form-control">
                                    <option value="">Select One</option>
                                    @foreach(explode(',',env('DB_used')) as $databse)
                                        <option value="{{$databse}}">{{$databse}}</option>

                                    @endforeach
                                </select>

                            </div>
                        </div>
                        <br>
                    </div>
                    <div class="form-group">
                        <h5>Search Filters</h5>
                        <form>
                            <div class="row">
                                <div class="col">
                                    <input type="text" class="form-control" placeholder="Name" name="cname" maxlength="100">
                                </div>
                                <div class="col">
                                    <input type="text" class="form-control" placeholder="First name" name="fname" maxlength="50">
                                </div>
                                <div class="col">
                                    <input type="text" class="form-control" placeholder="Mobile"  name="mobile" maxlength="13">
                                </div>
                                <div class="col">
                                    <input type="text" class="form-control" placeholder="Email" name="email" maxlength="90">
                                </div>
                                <div class="col">
                                    <input type="text" class="form-control" placeholder="Aadhar Number" name="adr" maxlength="20">
                                </div>
                                <div class="col">
                                    <input type="text" class="form-control" placeholder="UID" name="uid" maxlength="20">
                                </div>
                            </div><br/>
                            <div class="form-group row">
                                <div class="col-sm-1">
                                    <button type="submit" class="btn btn-primary" onclick="onsubmitdata()">Full Search</button>
                                </div>
                                <div class="col-sm-1">
                                    <button type="submit" class="btn btn-primary" onclick="onsubmitdata()" >Partial Search</button>
                                </div>
                                <div class="col-sm-">
                                    <button type ="button"  value="Clear" class="btn btn-primary" onclick="oncleardata()">Clear Data</button>
                                </div>

                            </div>
                        </form>
                    </div>


                    <input type='hidden' name='_token' value='{{csrf_token()}}'>
                </form>

            </div>
            <div class="col-md-4" style="margin-left: auto;margin-right: auto">
                <div id="loader" class="lds-dual-ring hidden overlay"></div>
            </div>
        </div>

    </div>


    <div class="dynamic_table">

    </div>

@endsection
@push('scripts')
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.4/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.4/js/buttons.flash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.4/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.4/js/buttons.print.min.js"></script>
{{--    <script src="https://code.jquery.com/jquery-1.11.1.min.js" crossorigin="anonymous"></script>--}}
{{--    <script src="//cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>--}}
    <script
        src="//cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js"></script>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/md5.js"></script>

    <script>
        var table_datables = ''

        function dbselect(db) {
            url = "{{route('getdbdata',':id')}}"
            url = url.replace(':id', db)
            $.ajax({
                type: "GET",
                url: url,
                beforeSend: function () { // Before we send the request, remove the .hidden class from the spinner and default to inline-block.
                    $('#loader').removeClass('hidden')
                },
                success: function (data) {
                    succData = JSON.parse(data)
                    $(".dyn").remove()
                    var html = '';
                    html += '<input type="hidden" name="tbl_name" value="' + succData[0]['TABLE_NAME'] + '"><div class="form-group row dyn"><div class="col-md-4"><select class="form-control" name="searchquery1"><option value="">Select One</option>'
                    for (var i = 0; i < succData.length; i++) {
                        html += '<option value="' + succData[i]['index'] + '_' + succData[i]['COLUMN_NAME'] + '">' + succData[i]['COLUMN_NAME'] + '</option>'
                    }
                    html += '</select></div> <div class="col-md-4"><input type="text" name="search1" class="form-control"></div></div>'
                    html += '<div class=" form-group row dyn"><div class="col-md-4"><select class="form-control" name="searchquery2"><option value="">Select One</option>'
                    for (var i = 0; i < succData.length; i++) {
                        html += '<option value="' + succData[i]['index'] + '_' + succData[i]['COLUMN_NAME'] + '">' + succData[i]['COLUMN_NAME'] + '</option>'
                    }
                    html += '</select></div> <div class="col-md-4"><input type="text" name="search2" class="form-control"></div></div>'
                    html += '<div class="form-group row dyn"><input type ="button"  value="search" class="btn btn-primary" onclick="onsubmitdata()">'
                    html += '&nbsp;&nbsp;<input type ="button"  value="Clear" class="btn btn-primary" onclick="oncleardata()"></div>'
                    $("#search_form").append(html)
                },
                complete: function () { // Set our complete callback, adding the .hidden class and hiding the spinner.
                    $('#loader').addClass('hidden')
                },

            });
        }

        function oncleardata() {
            url = "{{route('search')}}"
            window.location.href = url

        }

        function onsubmitdata() {
            url = "{{route('searchData')}}"
            var form = $("#search_form_data");
            $.ajax({
                type: "POST",
                url: url,
                data: form.serialize(), // serializes the form's elements.
                beforeSend: function () { // Before we send the request, remove the .hidden class from the spinner and default to inline-block.
                    $('#loader').removeClass('hidden')
                },
                success: function (data) {
                    succData = JSON.parse(data)
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
// console.log(field_res);
// console.log(my_columns);
// return false;

                        table_datables = $('#example').DataTable({
                            dom: 'Bfrtip',
                            bProcessing: true,
                            data: field_res,
                            columns: my_columns,

                            buttons: [
                                 'csv', 'excel'
                            ],
                            columnDefs: [
                                {
                                    targets: -1,
                                    className: 'dt-body-right'
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

            // $("#search_form_data").submit();
        }


    </script>
@endpush
