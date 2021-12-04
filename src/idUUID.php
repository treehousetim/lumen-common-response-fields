<?php namespace treehousetim\lumen_middleware;

use Closure;
use Illuminate\Http\Request;

class idUUID
{
    use \Laravel\Lumen\Routing\ProvidesConvenienceMethods;
    /**
     * Validates that the very first restful segment url variable is a UUID
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle( Request $request, Closure $next, $guard = null )
    {
        $params = $request->route()[2];

        $key = '';
        $value = '';

        foreach( $params as $key => $value )
        {
            break;
        }

        $request[$key] = $value;

        $this->validate( $request, [$key => ['uuid','required']] );

        return $next( $request );
    }
}
