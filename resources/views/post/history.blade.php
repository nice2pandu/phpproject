@extends('layouts.app')
@push('pg_btn')
@can('create-user')
    <a href="{{ route('users.create') }}" class="btn btn-sm btn-neutral">Create New User</a>
@endcan
@endpush
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-5">
                <div class="card-header bg-transparent">
                    <div class="row">
                        <div class="col-lg-8">
                            <h3 class="mb-0">Search History </h3>
                        </div>
                        <div class="col-lg-8">
                    {!! Form::open(['route' => 'history', 'method'=>'get']) !!}
                            <div class="form-group">
                                <label for="example-date-input" class="form-control-label">Start Date</label>
                                <input class="form-control" name="start_date" value="{{$startDate}}" type="date"  id="example-date-input">
                            </div>
                            <div class="form-group">
                                <label for="example-date-input" class="form-control-label">End Date</label>
                                <input class="form-control" name="end_date" type="date" value="{{$endDate}}" id="example-date-input">
                            </div>
                            <input class="btn btn-primary" type="submit">
                    {!! Form::close() !!}
                </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <div>
                            <table id="example" class="table table-hover align-items-center">
                                <thead class="thead-light">
                                <tr>
                                    <th scope="col">SNO</th>
                                    <th scope="col">User Id</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">DB Name</th>
                                    <th scope="col">Search Key</th>
                                    <th scope="col">Search Value</th>
                                    <th scope="col">Created On</th>
                                </tr>
                                </thead>
                                <tbody class="list">
                                @foreach($historyList as $key => $history)
                                    <tr>
                                        <th scope="row">
                                            {{$history->id}}
                                        </th>
                                        <th scope="row">
                                            {{$history->user_id}}
                                        </th>
                                        <td class="budget">
                                            {{$history->email}}
                                        </td>
                                        <td class="budget">
                                            {{$history->db_name}}
                                        </td>
                                        <td class="budget">
                                            {{$history->search_key}}
                                        </td>
                                        <td class="budget">
                                            {{$history->search_value}}
                                        </td>
                                        <td class="budget">
                                            {{$history->created_at}}
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
    <script type="text/javascript">

        $('#example').DataTable( {
            dom: 'Bfrtip',
            buttons: [
                 'csv', 'excel', 'pdf'
            ]
        } );
    </script>
@endpush

