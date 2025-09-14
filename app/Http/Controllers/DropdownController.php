<?php

namespace App\Http\Controllers;

use App\Models\Position;

class DropdownController extends Controller
{
    public function positions()
    {
        $positions = Position::all()->toResourceCollection();

        return $positions;
    }
}
