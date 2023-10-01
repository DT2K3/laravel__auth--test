<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class LoginHandler extends Controller
{

    public function login (Request $request){
        $user = User::where('email', $request->email)->first();

        // check (mk nguoi dung nhap , mk luu trong database)
        if(!$user || ! Hash::check( $request->password, $user->password ,[])){

        //trả về trường hợp không đúng
        return response()->json(
            [
                'message' => 'User not exist!'
            ],
            404
        );
    };
        // tạo token
        $token =$user->createToken('authToken')->plainTextToken;

       return response()->json([
        'access_token' =>$token,
        'type_token' => 'Bearer'
       ],
       200
    );
    }

    public function register (Request $request){
        // Định nghĩa ra biến lưu message lỗi

        $messages = [
             // định dạnh email bị sai
            'email.email' => "Error email",
                    // email chưa nhập
            'email.required' => "Vui lòng nhập email",
            // các lỗi tương ứng với các đối tượng
                    // mật khẩu chưa nhập 
            'password.required' => "Vui lòng nhập password"
        ];

        // Tạo ra biến để xử lí việc xử lí lỗi
        
        $validate = Validator::make(
            $request->all(),
            [
            'email' => 'email|required',
            // định nghĩa trường và kiểu dữ liệu của trường
            'password' => 'required'
            ],
            $messages
        );
         // xử lí validate
                 // xử lí việc hiển thị lỗi ra cho người dùng
        if($validate ->fails()){
            return response()->json(
                [
                    'message' => $validate->errors(),
                ],
                404
            );
        }
        // lưu vào trong DB
        User::create(
            [
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]
            );

        return response()->json(
            [
                'message' => "Created"
            ],
            200
        );
     }
}
