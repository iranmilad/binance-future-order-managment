<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function store(CreateUserRequest $request)
    {
        $user = User::create($request->all());

        return $this->responseJson("کاربر با موفقیت ذخیره شد.", $user, 201);
    }

    public function update(UpdateUserRequest $request)
    {
        $user = User::where("id", $request->input("id"))->update($request->all());

        return $this->responseJson("کاربر با موفقیت ویرایش شد.", $user, 200);
    }

    public function changeActive(Request $request)
    {
        $user = User::where("id", $request->input("id"))->update(["active"=>$request->input("active")]);

        return $this->responseJson("وضعیت با موفقیت تغییر کرد.", $user, 200);
    }

    public function delete(Request $request)
    {
        $user = User::where("id", $request->input("id"))->delete();

        return $this->responseJson("کاربر با موفقیت حذف شد.", null, 200);
    }

}
