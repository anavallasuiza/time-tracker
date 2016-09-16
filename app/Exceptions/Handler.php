<?php

namespace App\Exceptions;

use ErrorException;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Response;
use RuntimeException;
use Symfony\Component\Debug\Exception\FatalThrowableError;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use View;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        HttpException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        return parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        if ($e instanceof ModelNotFoundException) {
            $e = new NotFoundHttpException($e->getMessage(), $e);
        }


        if($e instanceof NotFoundHttpException)
        {
            if($request->ajax())
            {
                return response()->json(['errors'=>[$e->getMessage()]],404);
            }else{
                return response()->view('web.pages.error.404',[],404);
            }
        }elseif($e instanceof Exception){
            \Log::alert($e->getMessage());

            if($request->ajax())
            {
                return response()->json(['errors'=>[$e->getMessage()]],404);

            }else{
                return response()->view('web.pages.error.500',[],500);
            }
        }

    }
}
