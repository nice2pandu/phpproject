{{--<select  name="db" id="searchByFilter" class="form-control" onchange="searchByFilterOption() ">--}}
{{--        <option value="">-- Select Filter --</option>--}}
{{--        @foreach($filtersDropdown as $key => $databse)--}}
{{--            <option value="{{$key}}">{{$databse}}</option>--}}
{{--        @endforeach--}}
{{--</select>--}}

<div class="form-group row">
    <label class="col-sm-6">Search By: </label>
    <div class="col-sm-10">
        @foreach($filtersDropdown as $key => $databse)
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadioOptions{{$key}}" value="{{$key}}" onchange="searchByFilterOption('{{$key}}') ">
            <label class="form-check-label" for="inlineRadio1">{{$databse}}</label>
        </div>
        @endforeach

    </div>
</div>


