<?php

namespace App\Http\Controllers;

use App\Services\ThisCodeWorksService;

class ThisCodeWorksStatusController extends Controller
{
    public function __invoke(ThisCodeWorksService $thiscodeworks)
    {
        return response()->json([
            'online' => $thiscodeworks->isOnline(),
        ]);
    }
}