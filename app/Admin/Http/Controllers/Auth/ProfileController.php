<?php

namespace App\Admin\Http\Controllers\Auth;

use App\Admin\Http\Controllers\Controller;
use App\Admin\Http\Requests\Auth\ProfileRequest;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public function getView()
    {
        return [
            'index' => 'admin.auth.profile.index'
        ];
    }

    public function index()
    {
        $auth = auth('admin')->user();

        if (isset($this->crums)) {
            $this->crums->add(__('profile'));
        }

        return view($this->view['index'], [
            'auth' => $auth,
            'breadcrumbs' => $this->crums ?? null
        ]);
    }

    public function update(ProfileRequest $request)
    {
        $data = $request->validated();
        $admin = auth('admin')->user();

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $avatarFile = $request->file('avatar');
            $folder = 'uploads/files/admin';

            $name = Str::slug($admin->fullname ?? 'admin');
            $code = $admin->code ?? $admin->id;
            $datetime = now()->format('Y-m-d_H-i-s');
            $extension = $avatarFile->getClientOriginalExtension();
            $filename = "{$name}_{$code}_{$datetime}.{$extension}";

            $fullPath = public_path($folder);
            if (!file_exists($fullPath)) {
                mkdir($fullPath, 0755, true);
            }

            $avatarFile->move($fullPath, $filename);
            $data['avatar'] = $folder . '/' . $filename;
        } else {
            unset($data['avatar']);
        }

        $admin->update($data);
        return back()->with('success', __('Cập nhật thông tin thành công'));
    }
}
