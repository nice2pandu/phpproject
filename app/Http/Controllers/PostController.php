<?php

namespace App\Http\Controllers;

use App\Category;
use App\Http\Requests\PostRequest;
use App\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\User;
use App\SearchCredits;

class PostController extends Controller
{
    public $tableName;
    public $indexType;

    public function __construct()
    {
        $this->tableName = 'mdata';
        /*$this->middleware('permission:view-post');
        $this->middleware('permission:create-post', ['only' => ['create','store']]);
        $this->middleware('permission:update-post', ['only' => ['edit','update']]);
        $this->middleware('permission:destroy-post', ['only' => ['destroy']]);*/
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->has('search')) {
            $posts = Post::with(['user', 'category'])->where('post_title', 'like', '%' . $request->search . '%')->paginate(setting('record_per_page', 15));
        } else {
            $posts = Post::with(['user', 'category'])->paginate(setting('record_per_page', 15));
        }
        $title = 'Manage Posts';
        return view('post.search', compact('posts', 'title'));
    }

    /**
     * @param Request $request
     */
    public function searchFilter(Request $request)
    {
        $appendOptions = [];
        $fullIndexDB = explode(',', env('FULL_INDEX_DB'));

        if (in_array($request->db, $fullIndexDB)) {
            $appendOptions = array_merge($appendOptions, [
                'uid' => 'UID / ADR',
                'email' => 'Email',
                'altno' => 'ALT NO',]);
        }
        $filtersDropdown = [
            'mobile' => 'Mobile',
            'cname' => 'Customer Name',
            'ladd' => 'Adress',
            'cname_ladd' => 'Name & Address',
        ];
        $filtersDropdown = array_merge($filtersDropdown, $appendOptions);

        $options = view('post.searchFilter', ['filtersDropdown' => $filtersDropdown])->render();
        return $options;
    }

    public function searchByFilterForm(Request $request)
    {
        $filters = $request->post();
        $searchByFilter = $filters['searchByFilter'];
        $options = view('post.ajax_filter_form', ['filterSearch' => $searchByFilter])->render();
        return $options;
    }

    public function search(Request $request)
    {
        $query = "";
        $keys = [];
        $values = [];
        $res_arr = [];
//        $dbname_arr = explode(',', env('encoded_DB'));
//        $maxLimit = (int)env('max_limit');
//        $encoded = false;
//        $encoded_col = '';
//        if (in_array($request->db, $dbname_arr)) {
//            $encoded = true;
//            $encoded_col = explode(',', env('encoded_COLUMN'));
//        }
//
//        if ($request->has('db')) {
//            if ($encoded) {
//                $query .= "SELECT *, AES_DECRYPT(FROM_BASE64(mobile), 'MyNicotex@1') AS decode_mobile FROM
//                            $request->db.$request->tbl_name where ";
//            } else {
//                $query .= "SELECT * FROM $request->db.$request->tbl_name where ";
//            }
//        }
//        if (!empty($request->searchquery1)) {
//            $colname = explode('_',$request->searchquery1);
//            if($colname[1] == 'mobile' && $encoded)
//            {
//                $query .= " MATCH(mobile)  AGAINST((TO_BASE64(AES_ENCRYPT('$request->search1','MyNicotex@1'))) IN BOOLEAN MODE)";
//            }
//            else if (($colname[1] == 'cname' ||$colname[1] == 'ladd'|| $colname[1] == 'mobile'|| $colname[1] == 'adr'|| $colname[1] == 'uid'|| $colname[1] == 'email') && ($colname[0]=='FULLTEXT')) {
//                $query .= "MATCH($colname[1]) AGAINST('$request->search1' IN BOOLEAN MODE)";
//            }
//            else {
//                $query .= "$colname[1] = $request->search1";
//            }
//        }
//        if (!empty($request->searchquery2)) {
//                $colname = explode('_',$request->searchquery2);
//                $query .= " and ($colname[1]) like ('%$request->search2%')";
//
//        }
//        if ($request->has('db')) {
//
//            $query  .= " LIMIT $maxLimit";
////            dd($query);
//            $result = DB::select($query);
//
//            foreach ($result as $res) {
//                $keys = array_keys(json_decode(json_encode($res), true));
//                $values[] = array_values(json_decode(json_encode($res), true));
//                $res_arr[] = $res;
//            }
//
//        }
        $dbname = '';
        $altno = '';
        $mobile = '';
        $cname = '';
        $uid = '';
        $searchkey = '';
        $state = '';
        $statesWithDB = explode(';', env('statewise_DB_used'));
        $statesNames = [];
        $dbnames = [];
        foreach ($statesWithDB as $states) {
            $statesNames[] = explode(':', $states)[0];
            $dbnames[] = explode(':', $states)[1];
        }

        if (isset($request->dbname)) {
            $dbname = $request->dbname;
        }
        if (isset($request->statename)) {
            $state = $request->statename;
        }
        if (isset($request->mobile)) {
            $mobile = $request->mobile;
            $searchkey = 'mobile';
        }
        if (isset($request->cname)) {
            $cname = $request->cname;
            $searchkey = 'cname';
        }
        if (isset($request->uid)) {
            $uid = $request->uid;
            $searchkey = 'uid';
        }
        if (isset($request->altno)) {
            $mobile = $request->altno;
            $searchkey = 'mobile';
        }


        $title = 'Manage Search';

        return view('post.index2', ['keys' => $keys, 'values' => $values, 'result' => $res_arr, 'statesNames' => $statesNames,
            'state' => $state, 'dbname' => $dbname, 'mobile' => $mobile, 'cname' => $cname, 'uid' => $uid, 'searchkey' => $searchkey]);
    }

    protected function checkIsFullIndexDB($dbName)
    {
        $fullIndexDB = explode(',', env('FULL_INDEX_DB'));
        $dbNames = explode(',', env('DB_used'));
        if (in_array($dbName, $fullIndexDB)) {
            return true;
        } elseif (in_array($dbName, $dbNames)) {
            return false;
        } else {
            return new \Exception("DB Not Found");
        }
        return 0;
    }

    protected function prepareQuery()
    {
        $sql = '';
        return $sql;
    }

    public function doIndexDBSearch($dbName, $encoded, $maxLimit, $filter)
    {
        $query = '';
        $table = $dbName . "." . $this->tableName;

        if ($filter['searchByFilter'] == 'mobile') {
            list($column, $number) = explode("=", $filter['formData']);
            if ($encoded) {
                $query .= "SELECT AES_DECRYPT(FROM_BASE64 (mobile), 'MyNicotex@1') AS mobile ,cname, dob, fname,  ladd, padd, altno, email, uid, adr, doa FROM $table where ";
                $query .= " MATCH(mobile)  AGAINST((TO_BASE64(AES_ENCRYPT('$number','MyNicotex@1'))) IN BOOLEAN MODE)";
            } else {
                $query .= "SELECT * FROM $table where ";
                $query .= "MATCH(mobile) AGAINST('$number' IN BOOLEAN MODE)";
            }
            $query .= " LIMIT $maxLimit";
        }
        return $query;
    }

    public function doNonIndexDBSearch($dbName, $encoded, $maxLimit, $filter = [])
    {
        $table = $dbName . "." . $this->tableName;
        $query = '';
        $table = $dbName . "." . $this->tableName;

        if ($filter['searchByFilter'] == 'mobile') {
            list($column, $number) = explode("=", $filter['formData']);
            if ($encoded) {
                $query .= "SELECT AES_DECRYPT(FROM_BASE64 (mobile), 'MyNicotex@1') AS mobile ,cname, dob, fname,  ladd, padd, altno, email, uid, adr, doa FROM $table where ";
                $query .= " MATCH(mobile)  AGAINST((TO_BASE64(AES_ENCRYPT('$number','MyNicotex@1'))) IN BOOLEAN MODE)";
            } else {
                $query .= "SELECT * FROM $table where ";
                $query .= "MATCH(mobile) AGAINST('$number' IN BOOLEAN MODE)";
            }
            $query .= " LIMIT $maxLimit";
        }
        return $query;

    }

    public function searchData(Request $request)
    {
        if (!$this->isAllowToSearch()) {
            return json_encode([
                'stop' => true,
                'res_arr' => [],
            ]);
        }
        $dbname_arr = explode(',', env('encoded_DB'));
        $fullIndexDB = explode(',', env('FULL_INDEX_DB'));
        $maxLimit = (int)env('max_limit');
        $altcolumnSearch = false;
        $uidcolumnSearch = false;
        $altcolumn_db = explode(',', env('altsearch'));
        $uidcolumn_db = explode(',', env('uidsearch'));
        $encoded = false;
        $fullSearch = true;
        $maxLimit = (int)env('max_limit');
        $res_arr = [];
        $tableColumns = json_decode($this->getdbdata($request->db));

        $this->tableName = $tableColumns[0]->TABLE_NAME;
        // $this->indexType =     'FULLTEXT'     ;
        $query = '';

        if (in_array($request->db, $dbname_arr)) {
            $encoded = true;
        }
        $this->indexType = 0;
        $formSubmitted = $request->post();

        foreach ($tableColumns as $column) {
            if ($column->COLUMN_NAME == $formSubmitted['searchByFilter']) {
                $this->indexType = $column->index;
                break;
            }
        }

        //echo $this->indexType;die;
        if ($request->has('db')) {
            if (in_array($request->db, $altcolumn_db)) {
                $altcolumnSearch = true;
            }
            if (in_array($request->db, $uidcolumn_db)) {
                $uidcolumnSearch = true;
            }
            if ($encoded) {
                $query .= "SELECT AES_DECRYPT(FROM_BASE64 (mobile), 'MyNicotex@1') AS mobile ,cname, dob, fname,  ladd, padd, altno, email, uid, adr, doa FROM $request->db.$this->tableName where ";
            } else {
                $query .= "SELECT * FROM $request->db.$this->tableName where ";
            }
        }

        if ($request->searchType == 'partial') {
            $fullSearch = false;
        }

        if (!empty($request->searchByFilter)) {
            $formData1 = urldecode($request->formData);


            $formData = explode('=', $formData1);
            //dd($formData1);
            $columnName = $formData[0];
            $colValue = trim(urldecode($formData[1]));
            $column = trim($request->searchByFilter);


//Ejaz search start
            // btree -> col = val  ///
            // max fultext add like ,
            // dropdown should display only index columns,
            // address , name-pincode ,
            if ($column == 'uid') {
                $column = 'adr';
            }
            $multiColumnsSearch = ['ladd', 'cname_ladd']; //Adress, Name & Address
            if (!in_array($column, $multiColumnsSearch)) {
                //mobile, cname,email,altnum, uid, adr,
                if ($this->indexType === 'FULLTEXT') {
                    if ($encoded && $column == 'mobile') {
                        $query .= " MATCH($column)  AGAINST((TO_BASE64(AES_ENCRYPT('$colValue','MyNicotex@1'))) IN BOOLEAN MODE)";
                    } elseif ($column == 'adr' && !$uidcolumnSearch) {
                        return json_encode(['res_arr' => '']);
                    } elseif ($fullSearch) {
                        $query .= "MATCH($column) AGAINST('\"$colValue\"' IN BOOLEAN MODE)";
                    } else {
                        $query .= "MATCH($column) AGAINST('$colValue' IN BOOLEAN MODE)";
                    }
                    if ($altcolumnSearch && $column == 'mobile' && $encoded) {
                        $query .= "UNION ALL SELECT AES_DECRYPT(FROM_BASE64 (mobile), 'MyNicotex@1') AS mobile ,cname, dob, fname,  ladd, padd, altno, email, uid, adr, doa  FROM $request->db.$this->tableName WHERE MATCH(altno) AGAINST('\"$colValue\"' IN BOOLEAN MODE)";
                    } else if ($altcolumnSearch && $column == 'mobile' && !$encoded) {
                        $query .= "UNION ALL SELECT mobile ,cname, dob, fname,  ladd, padd, altno, email, uid, adr, doa  FROM $request->db.$this->tableName WHERE MATCH(altno) AGAINST('\"$colValue\"' IN BOOLEAN MODE)";
                    }
                } elseif ($this->indexType === 'BTREE') {
                    $query .= "$column = $colValue";
                } else {
                    $query .= "$column LIKE '%$colValue%'";
                }
            } elseif ($request->searchByFilter == 'ladd') {
                $formData = explode('&', $formData1);
                $ladd = explode('=', $formData[0]);
                $pincode = explode('=', $formData[1]);

                $pincode = array_values(array_filter(array_map('trim', $pincode), 'strlen'));
                $ladd = array_values(array_filter(array_map('trim', $ladd), 'strlen'));

                $addAnd = "";
                if ($this->indexType === 'FULLTEXT') {
                    if (isset($ladd[1]) && strlen($ladd[1]) > 0) {
                        $query .= "MATCH($ladd[0]) AGAINST('\"$ladd[1]\"' IN BOOLEAN MODE) ";
                        $addAnd = " AND ";
                    }

                    if (isset($pincode[1]) && strlen($pincode[1]) > 0) {
                        $query .= " $addAnd $ladd[0] LIKE '%$pincode[1]%' ";
                    }

                } else {
                    $query .= "MATCH($ladd[0]) AGAINST('$ladd[1]' IN BOOLEAN MODE)";
                    if (isset($pincode[1]) && strlen($pincode[1]) > 0) {
                        $query .= " AND $ladd[0]    LIKE '%$pincode[1]%' ";
                    }

                }
            } elseif ($request->searchByFilter == 'cname_ladd') {
                $formData = explode('&', $formData1);
                $cname = explode('=', $formData[0]);
                $ladd = explode('=', $formData[1]);
                $addAnd = "";

                if (isset($cname[1]) && strlen($cname[1]) > 0) {
                    $query .= "MATCH($cname[0]) AGAINST('\"$cname[1]\"' IN BOOLEAN MODE)";
                    $addAnd = " AND ";
                }
                if (isset($ladd[1]) && strlen($ladd[1]) > 0) {
                    $query .= " $addAnd $ladd[0] LIKE  '%$ladd[1]%' ";
                }
//
            } else {
                $query .= "$column = $colValue";
            }
        }


        if ($request->has('db')) {
            $query .= " LIMIT $maxLimit";
//            dd($query);

            $result = DB::select($query);

            foreach ($result as $res) {
                $res_arr[] = $res;
            }
            $result_arr = ['res_arr' => $res_arr];
        }
        $this->insertHistoryRecord($column, $colValue, $request->db, $query);
        return json_encode($result_arr);
    }

    public function searchDataForState(Request $request)
    {

        if (!$this->isAllowToSearch()) {
            return json_encode([
                'stop' => true,
                'res_arr' => [],
            ]);
        }
        $dbname_arr = explode(',', env('encoded_DB'));
        $fullIndexDB = explode(',', env('FULL_INDEX_DB'));
        $maxLimit = (int)env('max_limit');
        $uidcolumnSearch = false;
        $altcolumnSearch = false;
        $altcolumn_db = explode(',', env('altsearch'));
        $uidcolumn_db = explode(',', env('uidsearch'));
        $encoded = false;
        $fullSearch = true;
        $maxLimit = (int)env('max_limit');
        $res_arr = [];
        $state = $request->state;
        $stateDbs = [];
        $statesWithDB = explode(';', env('statewise_DB_used'));
        $statesNames = [];
        $dbnames = [];
        foreach ($statesWithDB as $states) {

            if (explode(':', $states)[0] == $state) {
                $dbnames = explode(',', explode(':', $states)[1]);
            }
        }

        // $this->indexType =     'FULLTEXT'     ;
        $query = '';

        foreach ($dbnames as $db) {


            $tableColumns = json_decode($this->getdbdata($db));

            $this->tableName = $tableColumns[0]->TABLE_NAME;
            $this->indexType = 0;
            $formSubmitted = $request->post();

            foreach ($tableColumns as $column) {
                if ($column->COLUMN_NAME == $formSubmitted['searchByFilter']) {
                    $this->indexType = $column->index;
                    break;
                }
            }

            //echo $this->indexType;die;
            if ($db) {
                if (in_array($db, $dbname_arr)) {
                    $encoded = true;
                } else {
                    $encoded = false;
                }
                if (in_array($db, $altcolumn_db)) {
                    $altcolumnSearch = true;
                } else {
                    $altcolumnSearch = false;
                }
                if (in_array($db, $uidcolumn_db)) {
                    $uidcolumnSearch = true;
                }
                if ($encoded) {
                    $query .= "SELECT AES_DECRYPT(FROM_BASE64 (mobile), 'MyNicotex@1') AS mobile ,cname, dob, fname,  ladd, padd, altno, email, uid, adr, doa FROM $db.$this->tableName where ";
                } else {
                    $query .= "SELECT mobile ,cname, dob, fname,  ladd, padd, altno, email, uid, adr, doa  FROM $db.$this->tableName where ";
                }
            }

//            var_dump($db, $altcolumn_db, $encoded);

            if ($request->searchType == 'partial') {
                $fullSearch = false;
            }

            if (!empty($request->searchByFilter)) {
                $formData1 = urldecode($request->formData);


                $formData = explode('=', $formData1);
                //dd($formData1);
                $columnName = $formData[0];
                $colValue = trim(urldecode($formData[1]));
                $column = trim($request->searchByFilter);

                if ($column == 'uid') {
                    $column = 'adr';
                }
                $multiColumnsSearch = ['ladd', 'cname_ladd']; //Adress, Name & Address
                if (!in_array($column, $multiColumnsSearch)) {
                    //mobile, cname,email,altnum, uid, adr,
                    if ($this->indexType === 'FULLTEXT') {
                        if ($encoded && $column == 'mobile') {
                            $query .= " MATCH($column)  AGAINST((TO_BASE64(AES_ENCRYPT('$colValue','MyNicotex@1'))) IN BOOLEAN MODE)";
                        } elseif ($fullSearch && $column == 'adr' && $uidcolumnSearch) {
                            $query .= "MATCH($column) AGAINST('\"$colValue\"' IN BOOLEAN MODE)";
                        } elseif ($fullSearch && $column != 'adr') {
                            $query .= "MATCH($column) AGAINST('\"$colValue\"' IN BOOLEAN MODE)";
                        } else {
                            $query .= "MATCH($column) AGAINST('$colValue' IN BOOLEAN MODE)";
                        }
                        if ($altcolumnSearch && $column == 'mobile' && $encoded) {
                            $query .= " UNION ALL SELECT AES_DECRYPT(FROM_BASE64 (mobile), 'MyNicotex@1') AS mobile ,cname, dob, fname,  ladd, padd, altno, email, uid, adr, doa  FROM $db.$this->tableName WHERE MATCH(altno) AGAINST('\"$colValue\"' IN BOOLEAN MODE)";
                        } else if ($altcolumnSearch && $column == 'mobile' && !$encoded) {
                            $query .= " UNION ALL SELECT mobile ,cname, dob, fname,  ladd, padd, altno, email, uid, adr, doa  FROM $db.$this->tableName WHERE MATCH(altno) AGAINST('\"$colValue\"' IN BOOLEAN MODE)";
                        }
                    } elseif ($this->indexType === 'BTREE') {
                        $query .= "$column = $colValue";
                    } elseif ($column != 'adr') {
                        $query .= "$column LIKE '%$colValue%'";
                    }
                } elseif ($request->searchByFilter == 'ladd') {
                    $formData = explode('&', $formData1);
                    $ladd = explode('=', $formData[0]);
                    $pincode = explode('=', $formData[1]);

                    $pincode = array_values(array_filter(array_map('trim', $pincode), 'strlen'));
                    $ladd = array_values(array_filter(array_map('trim', $ladd), 'strlen'));

                    $addAnd = "";
                    if ($this->indexType === 'FULLTEXT') {
                        if (isset($ladd[1]) && strlen($ladd[1]) > 0) {
                            $query .= "MATCH($ladd[0]) AGAINST('\"$ladd[1]\"' IN BOOLEAN MODE) ";
                            $addAnd = " AND ";
                        }

                        if (isset($pincode[1]) && strlen($pincode[1]) > 0) {
                            $query .= " $addAnd $ladd[0] LIKE '%$pincode[1]%' ";
                        }

                    } else {
                        $query .= "MATCH($ladd[0]) AGAINST('$ladd[1]' IN BOOLEAN MODE)";
                        if (isset($pincode[1]) && strlen($pincode[1]) > 0) {
                            $query .= " AND $ladd[0]    LIKE '%$pincode[1]%' ";
                        }

                    }
                } elseif ($request->searchByFilter == 'cname_ladd') {
                    $formData = explode('&', $formData1);
                    $cname = explode('=', $formData[0]);
                    $ladd = explode('=', $formData[1]);
                    $addAnd = "";

                    if (isset($cname[1]) && strlen($cname[1]) > 0) {
                        $query .= "MATCH($cname[0]) AGAINST('\"$cname[1]\"' IN BOOLEAN MODE)";
                        $addAnd = " AND ";
                    }
                    if (isset($ladd[1]) && strlen($ladd[1]) > 0) {
                        $query .= " $addAnd $ladd[0] LIKE  '%$ladd[1]%' ";
                    }
//
                } else {
                    $query .= "$column = $colValue";
                }
            }
            if (next($dbnames) == true) {

                $query .= " UNION ALL ";
            }

        }

        if ($request->has('state')) {
            $query .= " LIMIT $maxLimit";
//            dd($query);
            $result = DB::select($query);

            foreach ($result as $res) {
                $res_arr[] = $res;
            }
            $result_arr = ['res_arr' => $res_arr];
        }

        $this->insertHistoryRecord($column, $colValue, $request->db, $query);
        return json_encode($result_arr);
    }

    private function isAllowToSearch()
    {
        $id = Auth::user()->id;
        $user = User::find($id);
        $search_credits = (int)$user->search_credits;
        $credit = (int)$user->credits;
        if ($search_credits >= $credit) {
            return false;
        }
        return true;
    }

    private function insertHistoryRecord($column, $colValue, $db, $query = '')
    {
        $id = Auth::user()->id;

        //echo $column.'---------'.$colValue.'-----'.$query;
        //$user = User::where("email", '=', 'ejaz@mail.com')->get();
        $user = User::find($id);

        $search_credits = $user->search_credits;
        ++$search_credits;

        \DB::table('users')
            ->where('email', $user->email)
            ->update(['search_credits' => $search_credits]);

        $now = \Carbon\Carbon::now()->toDateTimeString();

        \DB::table('search_history')->insert([
            'user_id' => $id,
            'email' => $user->email,
            'db_name' => $db,
            'search_key' => $column,
            'search_value' => $colValue,
            'created_at' => $now,
        ]);
    }

    public function searchData_v1(Request $request)
    {

        $query = "";
        $keys = [];
        $values = [];
        $res_arr = [];
        $dbname_arr = explode(',', env('encoded_DB'));
        $maxLimit = (int)env('max_limit');
        $encoded = false;
        $encoded_col = '';
        $result_arr = [];
        if (in_array($request->db, $dbname_arr)) {
            $encoded = true;
            $encoded_col = explode(',', env('encoded_COLUMN'));
        }

        if ($request->has('db')) {
            if ($encoded) {
                $query .= "SELECT AES_DECRYPT(FROM_BASE64 (mobile), 'MyNicotex@1') AS mobile ,cname, dob, fname,  ladd, padd, altno, email, uid, adr, doa FROM $request->db.$request->tbl_name where ";
            } else {
                $query .= "SELECT * FROM $request->db.$request->tbl_name where ";
            }
        }
        if (!empty($request->searchquery1)) {
            $colname = explode('_', $request->searchquery1);
            if ($colname[1] == 'mobile' && $encoded) {
                $query .= " MATCH(mobile)  AGAINST((TO_BASE64(AES_ENCRYPT('$request->search1','MyNicotex@1'))) IN BOOLEAN MODE)";
            } else if (($colname[1] == 'cname' || $colname[1] == 'ladd' || $colname[1] == 'mobile'
                    || $colname[1] == 'adr' || $colname[1] == 'uid' || $colname[1] == 'email' || $colname[1] == 'altno') && ($colname[0] == 'FULLTEXT')) {
                $query .= "MATCH($colname[1]) AGAINST('$request->search1' IN BOOLEAN MODE)";
            } else {
                $query .= "$colname[1] = $request->search1";
            }
        }
        if (!empty($request->searchquery2)) {
            $colname = explode('_', $request->searchquery2);
            $query .= " and ($colname[1]) like ('%$request->search2%')";

        }
        if ($request->has('db')) {

            $query .= " LIMIT $maxLimit";

            $result = DB::select($query);

            foreach ($result as $res) {
                $keys = array_keys(json_decode(json_encode($res), true));
                $values[] = array_values(json_decode(json_encode($res), true));
                $res_arr[] = $res;
            }

            $result_arr = ['keys' => $keys, 'values' => $values, 'res_arr' => $res_arr];

        }

        return json_encode($result_arr);
    }

    public function getdbdata($db)
    {
        $sql = DB::select(DB::raw("SELECT * FROM INFORMATION_SCHEMA.columns WHERE TABLE_SCHEMA = '$db'"));
        $sql2 = DB::select(DB::raw("SELECT * FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = '$db'"));

        foreach ($sql as $data) {
            $data->index = 0;
            foreach ($sql2 as $a) {
                if ($a->COLUMN_NAME == $data->COLUMN_NAME) {
                    $data->index = $a->INDEX_TYPE;
                }
            }
        }

        return json_encode($sql);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = 'Create post';
        $categories = Category::pluck('category_name', 'id');
        return view('post.create', compact('categories', 'title'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(PostRequest $request)
    {
        $request->merge(['user_id' => Auth::user()->id]);
        $post = $request->except('featured_image');
        if ($request->featured_image) {
            $post['featured_image'] = parse_url($request->featured_image, PHP_URL_PATH);
        }
        Post::create($post);
        flash('Post created successfully!')->success();
        return redirect()->route('post.index');
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Post $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        $title = "Post Details";
        $post->with(['category', 'user']);
        return view('post.show', compact('title', 'post'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Post $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        $title = "Post Details";
        $post->with(['category', 'user']);
        $categories = Category::pluck('category_name', 'id');
        return view('post.edit', compact('title', 'categories', 'post'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Post $post
     * @return \Illuminate\Http\Response
     */
    public function update(PostRequest $request, Post $post)
    {
        $postdata = $request->except('featured_image');
        if ($request->featured_image) {
            $postdata['featured_image'] = parse_url($request->featured_image, PHP_URL_PATH);
        }
        $post->update($postdata);
        flash('Post updated successfully!')->success();
        return redirect()->route('post.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Post $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        $post->delete();
        flash('Post deleted successfully!')->info();
        return back();
    }

    public function history(Request $request)
    {
        $startDate = '';
        $endDate = '';
        if (isset($request->start_date)) {
            $startDate = $request->start_date;
        }
        if (isset($request->end_date)) {
            $endDate = $request->end_date;
        }
        $historyList = [];
        /*if ($request->has('search')) {
            $historyList = SearchCredits::where('email', 'like', '%' . $request->search . '%')->orderBy('id', 'DESC')->paginate(30);
        } else {*/
        if (isset($request->start_date)) {

            $historyList = SearchCredits::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . " 23:59:59"])->orderBy('id', 'DESC')->get();
        }
//        }


        $title = 'Search History';
        return view('post.history', compact('historyList', 'startDate', 'endDate', 'title'));
    }
}
