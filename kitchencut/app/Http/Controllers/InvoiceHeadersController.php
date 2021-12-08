<?php

namespace App\Http\Controllers;

use App\Models\invoice_headers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceHeadersController extends Controller
{

	/**
	 * filter
	 *
	 * @param  mixed $request
	 * @return array $records
	 */
	public function filter(Request $request)
	{

		// lets get the params from the request
		$data = $request->all();

		// the empty version of the returned variabel
		$invoice_headers = array();

		// check the params 
		$validated = $this->validate($request, [
			'from' => 'nullable|date',
			'to' => 'nullable|date',
			'status' => 'nullable|string',
			'location_id' => 'nullable|integer'
		]);

		if ($validated) {
			$from           = (isset($data['from']) && !empty($data['from'])) ? Date('Y-m-d', strtotime($data['from'])) : Date('Y-m-d', 0);
			$to             = (isset($data['to']) && !empty($data['to'])) ? Date('Y-m-d', strtotime($data['to'])) : Date('Y-m-d', strtotime('now'));
			$status         = (isset($data['status']) && !empty($data['status'])) ? filter_var($data['status'], FILTER_SANITIZE_STRING) : false;
			$location_id    = (isset($data['location_id']) && !empty($data['location_id'])) ? (int)$data['location_id'] : false;

			// Eloquent : build a query up to get the required data
			// using the params collected above - I dont like the look of this bit...
			$invoice_headers = invoice_headers::whereBetween('date', [$from, $to])
				->where(function ($query) use ($location_id) {
					if ($location_id) {
						$query->where('location_id', '=', $location_id);
					}
				})
				->where(function ($query) use ($status) {
					if ($status) {
						$query->where('status', '=', $status);
					}
				})->get();

		}

		return response()->json($invoice_headers);
	}

	/**
	 * groupLocationInvoicesByStatus
	 *
	 * @param  mixed $location_id
	 * @return array $result
	 */
	public function groupLocationInvoicesByStatus($location_id = false)
	{

		// the empty version of the returned variabel
		$invoice_lines = array();

		// checking the provided location id, if it's not int or > 0 make it false
		$location_id    = (int)$location_id > 0 ? (int)$location_id : false;

		// if the location id is valid
		if ($location_id) {

			// get all the model info together
			$headers = invoice_headers::where('location_id', $location_id)->get();

			// itterate thru to get what we want
			foreach ($headers as $header) {
				$invoice_lines[] = array(
					'status' => $header->status,
					'total' => $header->summed_invoice
				);
			}
		}


		return response()->json($invoice_lines);
	}

	
	/**
	 * aggregateTotals
	 *
	 * @return array mixed
	 */
	public function aggregateTotals() {
		return DB::select("SELECT ih.id as InvoiceID, sum(value) as Total, count(il.id) as LineCount FROM invoice_headers ih join invoice_lines il on il.invoice_header_id = ih.id group by ih.id");
	}
}
