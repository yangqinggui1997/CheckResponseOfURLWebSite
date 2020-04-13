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

use Illuminate\Http\Request;
Route::get('/', function () {
    return view('welcome');
});
Route::post('/check', function(Request $request)
{
	try
	{
	    $urlContent = file_get_contents($request->_url);
		$dom = new DOMDocument();
		@$dom->loadHTML($urlContent);
		$xpath = new DOMXPath($dom);
		$hrefs = $xpath->evaluate("/html/body//a");
		$urls = array();
		$_url = NULL;
		$response = NULL;
		$httpCode = NULL;
		$handle = NULL;
		for($i = 0; $i < $hrefs->length; $i++)
		{
		    $href = $hrefs->item($i);
		    $url = $href->getAttribute('href');
		    $url = filter_var($url, FILTER_SANITIZE_URL);
		    if(filter_var($url, FILTER_VALIDATE_URL))
		    {
		    	$_url = new stdClass();
		    	$_url->url = $url;
				$handle = curl_init($url);
		    	curl_setopt($handle,  CURLOPT_RETURNTRANSFER, TRUE);
				$response = curl_exec($handle);
				$httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
		    	$_url->status = $httpCode;
				curl_close($handle);
		        $urls[] = $_url;
		    }
		}
		return response()->json(array('flag' => TRUE, 'urls' => $urls));
	}
	catch(Exception $e)
	{
		return response()->json(array('flag' => FALSE, 'error' => $e->getMessage()));
	}
})->name('checkUrl');