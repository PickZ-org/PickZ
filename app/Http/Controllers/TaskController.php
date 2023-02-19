<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  string $direction
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request, $type = null)
    {
        switch ($type) {
            case 'putaway':
                $type = 1;
                break;
            case 'replenishment':
                $type = 2;
                break;
            case 'picking':
                $type = [3, 7];
                break;
            case 'shipping':
                $type = 4;
                break;
            case 'crossdock':
                $type = 6;
                break;
        }
        return view('task.index', [
            'type' => $type,
        ]);
    }
}
