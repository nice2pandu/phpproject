<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->route('home');
});

Auth::routes(['verify'=>true]);


Route::group(['middleware' => ['auth','verified']], function () {

    Route::get('/home', 'HomeController@index')->name('home');

    Route::get('/components', function(){
        return view('components');
    })->name('components');


    Route::resource('users', 'UserController');
    Route::get('/search', 'PostController@search')->name('search');
//Ajax credits
    Route::get('/ajax-credits', 'AjaxController@run')->name('ajax-credits');

    Route::get('/history', 'PostController@history')->name('history');
    Route::get('/bulk-upload', 'BulkUploadController@upload')->name('bulk.upload');
    Route::get('/bulk-search', 'BulkUploadController@search')->name('bulk.search');
    Route::get('/bulk-search/{id}', 'BulkUploadController@searchbyId')->name('bulk.getlead');
    Route::get('/bulk-edit/{id}/{notes}', 'BulkUploadController@edit')->name('bulk.edit');
    Route::get('/bulk-delete/{id}', 'BulkUploadController@delete')->name('bulk.delete');
    Route::post('/upload-data', 'BulkUploadController@uploadData')->name('bulk.upload_data');
    Route::get('/search-data/{pfname}', 'BulkUploadController@searchData')->name('bulk.searchData');

    Route::post('/searchData', 'PostController@searchData')->name('searchData');
    Route::post('/searchDataForState', 'PostController@searchDataForState')->name('searchDataForState');

    Route::post('/searchFilter', 'PostController@searchFilter')->name('searchFilter');
    Route::post('/searchByFilterForm', 'PostController@searchByFilterForm')->name('searchByFilterForm');

    Route::get('/getdbdata/{id}', 'PostController@getdbdata')->name('getdbdata');

    Route::get('/profile/{user}', 'UserController@profile')->name('profile.edit');
    Route::post('/resetCreditBalance', 'UserController@resetCreditBalance')->name('resetCreditBalance.edit');

    Route::post('/profile/{user}', 'UserController@profileUpdate')->name('profile.update');

    Route::resource('roles', 'RoleController')->except('show');

    Route::resource('permissions', 'PermissionController')->except(['show','destroy','update']);

    Route::resource('category', 'CategoryController')->except('show');

    Route::resource('post', 'PostController');

    Route::get('/activity-log', 'SettingController@activity')->name('activity-log.index');

    Route::get('/settings', 'SettingController@index')->name('settings.index');

    Route::post('/settings', 'SettingController@update')->name('settings.update');


    Route::get('media', function (){
        return view('media.index');
    })->name('media.index');
});
