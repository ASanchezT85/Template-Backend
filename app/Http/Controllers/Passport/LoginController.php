<?php

namespace App\Http\Controllers\Passport;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends Controller
{
    public function login(LoginRequest $request)
    {
        $remember = $request->remember;

        $credentials = [
            'email'     => (string) $request->email,
            'password'  => (string) $request->password
        ];

        if (auth()->attempt($credentials)) {

            $token = auth()->user()->createToken((string) $request->email);
        
            return $this->respondWithToken($token, $remember);

        } else {

            return response()->json([
                'cod'       => Response::HTTP_UNAUTHORIZED,
                'message'   => __('Your credentials are incorrect. Please try again.')
            ], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function logout()
    {
        auth()->user()->tokens->each(function ($token, $key) {
            $token->delete();
        });
        
        return response()->json([
            'cod'       => Response::HTTP_OK,
            'message'   => __('Logged out successfully.')
        ], Response::HTTP_OK);
    }


    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token, $remember)
    {
        return response()->json([
            'cod'           => Response::HTTP_OK,
            'token_type'    => 'Bearer',
            'token'         => $token->accessToken,
            'remember'      => $remember,
            'expires_in'    => (int) 86400,
            'user'          => auth()->user(),
            'message'       => __('Successful login...'),
        ], Response::HTTP_OK);
    }
}
