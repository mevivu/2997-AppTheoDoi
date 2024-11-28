<?php

namespace App\Http\Controllers\Login;

use App\Admin\Http\Controllers\Controller;
use App\Repositories\User\UserRepositoryInterface;
use App\Services\Auth\AuthServiceInterface;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

class LoginController extends Controller
{
    public function __construct(
        UserRepositoryInterface $repository,
        AuthServiceInterface    $service
    )
    {
        parent::__construct();
        $this->repository = $repository;
        $this->service = $service;
    }

    public function getView(): array
    {
        return [
            'index' => 'public.login.index',
        ];
    }

    public function index(): Factory|View|Application
    {
        return view($this->view['index']);
    }


}
