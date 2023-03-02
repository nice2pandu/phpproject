<?php

namespace App\Http\Controllers;

use App\Bulk_Insert;
use App\Category;
use App\Http\Requests\PostRequest;
use App\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\User;
use App\SearchCredits;
use Psy\Util\Json;

class BulkUploadController extends Controller
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

    public function upload(Request $request)
    {
        $title = 'Upload';
        return view('bulk.upload', compact('title'));
    }

    public function uploadData(Request $request)
    {
        $file = $request->file;
        $user = Auth::user()->id;
        $filename = $file->getClientOriginalName();

        $extension = $file->getClientOriginalExtension();
        $maxFileSize = 2097152;

        $tempPath = $file->getRealPath();
        $fileSize = $file->getSize();
        $mimeType = $file->getMimeType();
        $valid_extension = array("csv", "xlxs");
        if (in_array(strtolower($extension), $valid_extension)) {
            if ($fileSize <= $maxFileSize) {
                $location = 'uploads';

                // Upload file
                $file->move($location, $filename);

                // Import CSV to Database
                $filepath = public_path($location . "/" . $filename);
                $columnArray = array();
                $dataArray = array();
                $firstRule = true;

                // Reading file
                $file = fopen($filepath, "r");
                $importData_arr = array();
                $i = 0;
                while (($filedata = fgetcsv($file, 1000, ",")) !== FALSE) {
                    $num = count($filedata);
                    if ($firstRule) {
                        foreach ($filedata as $columnName) {
                            $columnArray[] = $columnName;
                        }

                        $firstRule = false;
                    } else {
                        $rule = array();
                        $data = '';
                        for ($i = 0; $i < count($filedata); $i++) {
                            if (str_contains($columnArray[$i], 'mobile')) {
                                $columnArray[$i] = "mobile";
                                if ($filedata[$i] != '') {
                                    $arr = preg_split("/\W+/", $filedata[$i], -1, PREG_SPLIT_NO_EMPTY);
                                    $str = implode(', ', $arr);
                                    $data .= $str . ",";
                                }
                                $rule[$columnArray[$i]] = $data;
                            } else {

                                $rule[$columnArray[$i]] = $filedata[$i];
                            }
                        }
                        $dataArray[] = $rule;
                    }

                    // Skip first row (Remove below comment if you want to skip the first row)
                    if ($i == 0) {
                        $i++;
                        continue;
                    }
//                    for ($c = 0; $c < $num; $c++) {
//                        $importData_arr[$i][] = $filedata [$c];
//                    }
                    $i++;
                }
                fclose($file);
//                dd($dataArray);

                if (isset($request->user)) {
                    $user = $request->user;
                }
                foreach ($dataArray as $col => $importData) {
                    $bulk_insert = new Bulk_Insert();

                    $bulk_insert->portfolio = $request->portfolio;
                    $bulk_insert->notes = $request->notes;
                    $bulk_insert->user_id = $user;
                    $bulk_insert->mobile = $importData['mobile'];
                    $bulk_insert->name = $importData['name'];
                    $bulk_insert->bank_account = $importData['bank_account'];
                    $bulk_insert->address = $importData['address'];
                    $bulk_insert->save();


                }

            } else {
                Session::flash('message', 'File too large. File must be less than 2MB.');
            }
        } else {
            Session::flash('message', 'Invalid File Extension.');
        }

        return redirect()->route('bulk.search');

    }

    public function searchbyId($id)
    {
        $porfolios = Bulk_Insert::find($id);
        return json_encode($porfolios);
    }

    public function search(Request $request)
    {
        $role = $role = Auth::user()->getRoleNames()[0];
        $title = 'Search';
        if ($role == 'super-admin' || $role == 'TL') {
            $porfolios = Bulk_Insert::select('portfolio')->distinct()->get();
        } else {
            $porfolios = Bulk_Insert::select('portfolio')->where('user_id', Auth::user()->id)->distinct()->get();
        }

        return view('bulk.search', compact('title', 'porfolios'));
    }

    public function searchData($pfname, Request $request)
    {
        $porfolios = Bulk_Insert::where('portfolio', $pfname)->get()->toArray();
        foreach ($porfolios as $porfolio) {
            $keys = array_keys($porfolio);
        }

        return json_encode(['data' => $porfolios, 'keys' => $keys, 'res_arr' => $porfolios]);
    }

    public function edit($id, $notes, Request $request)
    {
        $portfolio = Bulk_Insert::find($id);
        $portfolio->notes = $notes;
        $portfolio->save();
        return json_encode("success");
    }

    public function delete($id, Request $request)
    {
        if (is_numeric($id)) {
            $portfolio = Bulk_Insert::find($id)->delete();
        } else {
            $portfolio = Bulk_Insert::where('portfolio', $id)->delete();
        }
        return json_encode("success");
    }
}
