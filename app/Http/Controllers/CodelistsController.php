<?php

namespace App\Http\Controllers;

use App\Exceptions\BadRequestException;
use App\Models\Codelist;
use Illuminate\Http\Request;

class CodelistsController extends Controller
{
    public function show(Request $request) {
        if (!empty($request->all())) {
            throw new BadRequestException();
        }

        $codelist = Codelist::getCodelist();

        return response()->json($codelist, 200);
    }

}
