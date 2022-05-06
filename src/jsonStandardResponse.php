<?php namespace treehousetim\lumen_middleware;

use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use Closure;

class jsonStandardResponse
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

        $firstError = array_shift( $errors );
        if( is_array( $firstError ) )
        {
            $firstError = array_shift( $firstError );
        }

        $out['error'] = $firstError;

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

        if( is_string( $response ) )
        {
            return new JsonResponse( $this->getResponseStruct( 200 ) + ['data' => $response ] );
        }
        elseif( is_array( $response ) )
        {
            return new JsonResponse( $this->getResponseStruct( 200 ) + $response );
        }

        $original = $response->original;
        if( $original instanceOf \Illuminate\Database\Eloquent\Model )
        {
            $class = 'Illuminate\Database\Eloquent\Model';
        }
        elseif( $original instanceOf \Illuminate\Http\Resources\Json\JsonResource )
        {
            $class = 'Illuminate\Http\Resources\Json\JsonResource';
        }
        elseif( $original instanceOf \JsonSerializable )
        {
            $class = 'JsonSerializable';
        }
        elseif( is_array( $original ) )
        {
            $class= 'array';
        }
        elseif( is_scalar( $original ) )
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
        case 'array':
            $out = $out + $original;
            break;

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

        case 'Illuminate\Http\Resources\Json\JsonResource';
            $out = $out + $original->toArray( $request );
            break;

        case 'JsonSerializable':
            $out = $out + (array)$original->jsonSerialize();
            break;

        default:
            throw new \Exception( 'Unknown response type' . PHP_EOL . print_r( $original, true ) );
        }

        return new JsonResponse( $out, $out['httpCode'] );
    }
}
