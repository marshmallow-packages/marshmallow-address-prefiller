<?php

namespace Marshmallow\Zipcode\Http;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Marshmallow\Zipcode\Facades\Zipcode;

class ZipcodePrefillController extends Controller
{
    public function __invoke(Request $request)
    {
        return Zipcode::street($request->street)
            ->city($request->city)
            ->province($request->province)
            ->country($request->country)
            ->latitude($request->latitude)
            ->longitude($request->longitude)
            ->get(
                $request->zipcode,
                $request->housenumber
            );
    }
}
