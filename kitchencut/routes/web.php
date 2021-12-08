<?php
/** @var \Laravel\Lumen\Routing\Router $router */

use App\Models\invoice_headers;
use App\Models\locations;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    $locations = locations::all();
    $states = invoice_headers::select('status')->distinct()->get();
    return view('index',compact('locations','states'));
});

// groupLocationInvoicesByStatus
$router->get('/location/{location_id}', ['uses' => 'InvoiceHeadersController@groupLocationInvoicesByStatus', 'as' => 'groupLocationInvoicesByStatus']);

// filter
$router->post('/filter', ['uses' => 'InvoiceHeadersController@filter', 'as' => 'filter']);

// aggregates
$router->get('/aggregate', ['uses' => 'InvoiceHeadersController@aggregateTotals', 'as' => 'aggregateTotals']);
