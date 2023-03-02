<?php

namespace App\Http\Controllers;

use App\Category;
use App\Http\Requests\PostRequest;
use App\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function __construct()
    {

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

    public function search(Request $request)
    {
        $query = "";
        $keys = [];
        $values = [];
        $res_arr = [];
        $dbname_arr = explode(',', env('encoded_DB'));
        $maxLimit = (int)env('max_limit');
        $encoded = false;
        $encoded_col = '';
        if (in_array($request->db, $dbname_arr)) {
            $encoded = true;
            $encoded_col = explode(',', env('encoded_COLUMN'));
        }

        if ($request->has('db')) {
            if ($encoded) {
                $query .= "SELECT *, AES_DECRYPT(FROM_BASE64(mobile), 'MyNicotex@1') AS decode_mobile FROM
                            $request->db.$request->tbl_name where ";
            } else {
                $query .= "SELECT * FROM $request->db.$request->tbl_name where ";
            }
        }
        if (!empty($request->searchquery1)) {
            $colname = explode('_',$request->searchquery1);
            if($colname[1] == 'mobile' && $encoded)
            {
                $query .= " MATCH(mobile)  AGAINST((TO_BASE64(AES_ENCRYPT('$request->search1','MyNicotex@1'))) IN BOOLEAN MODE)";
            }
            else if (($colname[1] == 'cname' ||$colname[1] == 'ladd'|| $colname[1] == 'mobile'|| $colname[1] == 'adr'|| $colname[1] == 'uid'|| $colname[1] == 'email') && ($colname[0]=='FULLTEXT')) {
                $query .= "MATCH($colname[1]) AGAINST('$request->search1' IN BOOLEAN MODE)";
            }
            else {
                $query .= "$colname[1] = $request->search1";
            }
        }
        if (!empty($request->searchquery2)) {
                $colname = explode('_',$request->searchquery2);
                $query .= " and ($colname[1]) like ('%$request->search2%')";

        }
        if ($request->has('db')) {

            $query  .= " LIMIT $maxLimit";
//            dd($query);
            $result = DB::select($query);

            foreach ($result as $res) {
                $keys = array_keys(json_decode(json_encode($res), true));
                $values[] = array_values(json_decode(json_encode($res), true));
                $res_arr[] = $res;
            }

        }

        $title = 'Manage Search';
        return view('post.index', ['keys' => $keys, 'values' => $values, 'result' => $res_arr]);
    }

    public function searchData(Request $request)
    {

        $query = "";
        $keys = [];
        $values = [];
        $res_arr = [];
        $dbname_arr = explode(',', env('encoded_DB'));
        $maxLimit = (int)env('max_limit');
        $encoded = false;
        $encoded_col = '';
        $result_arr=[];
        if (in_array($request->db, $dbname_arr)) {
            $encoded = true;
            $encoded_col = explode(',', env('encoded_COLUMN'));
        }

        if ($request->has('db')) {
            if ($encoded) {
                $query .= "SELECT *, AES_DECRYPT(FROM_BASE64(mobile), 'MyNicotex@1') AS decode_mobile FROM
                            $request->db.$request->tbl_name where ";
            } else {
                $query .= "SELECT * FROM $request->db.$request->tbl_name where ";
            }
        }
        if (!empty($request->searchquery1)) {
            $colname = explode('_',$request->searchquery1);
            if($colname[1] == 'mobile' && $encoded)
            {
                $query .= " MATCH(mobile)  AGAINST((TO_BASE64(AES_ENCRYPT('$request->search1','MyNicotex@1'))) IN BOOLEAN MODE)";
            }
            else if (($colname[1] == 'cname' ||$colname[1] == 'ladd'|| $colname[1] == 'mobile'
                    || $colname[1] == 'adr'|| $colname[1] == 'uid'|| $colname[1] == 'email'|| $colname[1] == 'altno') && ($colname[0]=='FULLTEXT')) {
                $query .= "MATCH($colname[1]) AGAINST('$request->search1' IN BOOLEAN MODE)";
            }
            else {
                $query .= "$colname[1] = $request->search1";
            }
        }
        if (!empty($request->searchquery2)) {
            $colname = explode('_',$request->searchquery2);
            $query .= " and ($colname[1]) like ('%$request->search2%')";

        }
        if ($request->has('db')) {

            $query  .= " LIMIT $maxLimit";
//            dd($query);
            $result = DB::select($query);

            foreach ($result as $res) {
                $keys = array_keys(json_decode(json_encode($res), true));
                $values[] = array_values(json_decode(json_encode($res), true));
                $res_arr[] = $res;
            }

            $result_arr = ['keys'=>$keys, 'values'=>$values, 'res_arr'=>$res_arr];

        }

        return json_encode($result_arr);
    }

    public function getdbdata($db)
    {
        $sql = DB::select(DB::raw("SELECT * FROM INFORMATION_SCHEMA.columns WHERE TABLE_SCHEMA = '$db'"));
        $sql2 = DB::select(DB::raw("SELECT * FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = '$db'"));

        foreach($sql as $data)
        {
            $data->index = 0;
            foreach($sql2 as $a)
            {
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
}
