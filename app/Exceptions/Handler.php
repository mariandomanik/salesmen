<?php

namespace App\Exceptions;

use App\Models\Salesman;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (AccessDeniedHttpException $e, $request) {
            if ($request->is('api/salesmen*')) {
                return response()->json([
                    'errors' => [
                        'code' => Salesman::RESPONSE_CODE_FORBIDDEN,
                        'message' => __('response.forbidden', ['object_name' => 'Salesman'])
                    ]
                ], 403);
            }

            if ($request->is('api/codelist*')) {
                return response()->json([
                    'errors' => [
                        'code' => Salesman::RESPONSE_CODE_FORBIDDEN,
                        'message' => __('response.forbidden', ['object_name' => 'Codelist'])
                    ]
                ], 403);
            }
        });

        $this->renderable(function (QueryException $e, $request) {
            if ($request->is('api/salesmen*')) {
                return response()->json([
                    'errors' => [
                        'code' => Salesman::RESPONSE_CODE_BAD_REQUEST,
                        'message' => __('response.bad_request')
                    ]
                ], 400);
            }
        });

        $this->renderable(function (NotFoundHttpException $e, $request) {
            if ($request->is('api/salesmen*')) {
                return response()->json([
                    'errors' => [
                        'code' => Salesman::RESPONSE_CODE_NOT_FOUND,
                        'message' => __('response.salesman_not_found', ['uuid' => $request->salesman ?? ''])
                    ]
                ], 404);
            }
        });

        $this->renderable(function (BadRequestException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'errors' => [
                        'code' => BadRequestException::BAD_REQ_EXC_CODE,
                        'message' => __('response.bad_request')
                    ]
                ], 400);
            }
        });


    }

    protected function handleValidationException(ValidationException $exception)
    {
        $errors = $this->formatErrorBlock($exception->validator);
        $first = '';
        if (!empty($errors) && !empty($errors[0]))
            $first = $errors[0]['message'];
        $json = new \stdClass;
        $json->success = false;
        $json->success_code = 400;
        $json->message = $first;
        $json->errors = $errors;
        return response()->json($json, 400);
    }

    public function formatErrorBlock($validator)
    {
        $errors = $validator->errors()->toArray();
        $return = array();
        foreach ($errors as $field => $message) {
            $r = ['field' => $field];
            foreach ($message as $key => $msg) {
                if ($key) {
                    $r['message'.$key] = $msg;
                } else {
                    $r['message'] = $msg;
                }
            }
            $return[] = $r;
        }
        return $return;
    }
}
