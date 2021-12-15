<?php

namespace Marshmallow\Zipcode\Http;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Marshmallow\Zipcode\Facades\Zipcode;

class ZipcodePrefillController extends Controller
{
    public function __invoke(Request $request)
    {
        try {
            return Zipcode::flexibleIdPrefix($request->flexible_id)
                ->street($request->street)
                ->city($request->city)
                ->province($request->province)
                ->country($request->country)
                ->latitude($request->latitude)
                ->longitude($request->longitude)
                ->get(
                    $request->zipcode,
                    $request->housenumber
                );
        } catch (Exception $e) {
            return [
                'error' => $e->getMessage(),
            ];
        }
    }
}
