<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Bulk_Insert extends Model
{
    use LogsActivity;

    protected $table = 'bulk_insert';


}
