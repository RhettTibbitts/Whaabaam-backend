<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\URL;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        // return parent::render($request, $exception);
        
        if(str_contains(URL::current(),'/api/')){ //if this is api
            // $exception->getMessage();

            return response()->json([
                'status' => 400,
                'message'=>__('messages.COMMON_ERR')
            ]);

        } else{ //if this is website 
            // return parent::render($request, $exception);

            if ($exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException){ 

                return response(view('backEnd.error.404'));
            } else if ($exception instanceof \Symfony\Component\Debug\Exception\FatalErrorException) {

                return response(view('backEnd.error.500'));
            } else{

                return response(view('backEnd.error.500'));
            }

        }

    }
}
