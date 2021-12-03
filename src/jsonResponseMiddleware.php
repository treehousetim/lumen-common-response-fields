<?php namespace treehousetim\responseFields;

use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use Closure;

class jsonResponseMiddleware
{
    protected function getResponseStruct( int $statusCode ) : array
    {
        return [
            'httpCode' => $statusCode,
            'request_utc' => Carbon::now()->toDateTimeString()
        ];
    }
    //------------------------------------------------------------------------
    public function validationErrors( $errors )
    {
        $out = $this->getResponseStruct( 422 );
        $out['errors'] = $errors;

        return new JsonResponse( $out, $out['httpCode'] );
    }
    //------------------------------------------------------------------------
    /**
     * _AFTER_ middleware that enforces standard json responses
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle( $request, Closure $next )
    {
        // we capture the response because this is an "after" middleware.
        try
        {
            $response = $next( $request );
        }
        catch( \Illuminate\Validation\ValidationException $e )
        {
            return $this->validationErrors( $e->errors() );
        }

        if( is_string( $response ) || is_array( $response ) )
        {
            return new JsonResponse( $this->getResponseStruct( 200 ) + ['data' => $response ] );
        }

        $original = $response->original;
        if( $original instanceOf \Illuminate\Database\Eloquent\Model )
        {
            $class = 'Illuminate\Database\Eloquent\Model';
        }
        elseif( is_scalar( $original ) || is_array( $original ) )
        {
            $class = 'basic';
        }
        else
        {
            $class = get_class( $original );
        }

        $out = $this->getResponseStruct( $response->getStatusCode() );

        switch ( $class )
        {
        case 'basic':
            $out['data'] = $original;
            break;

        case 'Illuminate\Database\Eloquent\Model':
        case 'Illuminate\Pagination\LengthAwarePaginator':
             $out = $out + $original->toArray();
             break;

        case 'Illuminate\Database\Eloquent\Collection':
            foreach( $original as $item )
            {
                $out['items'][] = $item;
            }
            break;

        default:
            $out['message'] = 'unknown output type';
        }

        return new JsonResponse( $out, $out['httpCode'] );
    }
}
