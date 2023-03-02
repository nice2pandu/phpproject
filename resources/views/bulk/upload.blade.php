@extends('layouts.app')

@section('content')

    <div class="form-row bg-primary1 backgroup-white">
        <div class="col-md-12">
            <div class="card mb-3">

                <form id="search_form_data" action="{{route('bulk.upload_data')}}" method="POST"
                      enctype="multipart/form-data">
                    <input type='hidden' name='_token' value='<?php echo(csrf_token()); ?>'>

                    <div class="form-group">
                        <label for="portfolio">Portfolio</label>
                        <input type="text" class="form-control" name="portfolio" id="portfolio"
                               aria-describedby="emailHelp" placeholder="Enter portfolio">

                    </div>
                    <div class="form-group">
                        <label for="notes">Notes</label>
                        <input type="text" name="notes" class="form-control" id="notes" placeholder="Notes">
                    </div>
                    @if(Auth::user()->getRoleNames()[0]== 'super-admin' || Auth::user()->getRoleNames()[0]=='TL')
                        <div class="form-group">
                            <label for="notes">User</label>
                            <select name="user" id="user" class="form-control">
                                <option value="">--Select User--</option>
                                @foreach(\App\User::all() as $databse)
                                    <option value="{{$databse->id}}">{{$databse->name}}</option>
                                @endforeach
                            </select>
                        </div>
                @endif

                <!-- File Button -->
                    <div class="form-group">
                        <label class="col-md-4 control-label" for="filebutton">Select File</label>
                        <div class="col-md-4">
                            <input type="file" name="file" id="file" class="input-large">
                        </div>
                    </div>
                    <!-- Button -->
                    <div class="form-group">
                        <label class="col-md-4 control-label" for="singlebutton">Import data</label>
                        <div class="col-md-4">
                            <button type="submit" id="submit" name="Import" class="btn btn-primary button-loading"
                                    data-loading-text="Loading...">Import
                            </button>
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

@endsection
