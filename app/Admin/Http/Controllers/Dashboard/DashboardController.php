<?php

namespace App\Admin\Http\Controllers\Dashboard;

use App\Admin\Http\Controllers\Controller;

class DashboardController extends Controller
{
    //

    public function getView()
    {
        return [
            'index' => 'admin.dashboard.index'
        ];
    }
    public function index(){
        return view($this->view['index']);
    }

}
