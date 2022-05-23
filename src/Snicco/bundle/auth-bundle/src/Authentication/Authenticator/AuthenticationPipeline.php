<?php

declare(strict_types=1);

namespace Snicco\Enterprise\Bundle\Auth\Authentication\Authenticator;

use Closure;
use League\Tactician\Middleware;
use Snicco\Component\HttpRouting\Http\Psr7\Request;
use League\Tactician\Exception\InvalidMiddlewareException;

use function array_pop;

final class AuthenticationPipeline
{
    
    /**
     * @var Closure(Request):LoginResult
     */
    private Closure $pipeline;
    
    /**
     * @param  Authenticator[]  $authenticators
     */
    public function __construct(array $authenticators) {
        $this->pipeline = $this->buildBuildPipeline($authenticators);
    }
    
    public function attempt(Request $request) :LoginResult
    {
        return ($this->pipeline)($request);
    }
    
    /**
     * @param  Authenticator[]  $authenticators
     *
     * @return Closure(Request):LoginResult
     */
    private function buildBuildPipeline(array $authenticators) :Closure
    {
        $next = fn() :LoginResult => LoginResult::failed();
    
        while ($authenticator = array_pop($authenticators)) {
            $next = fn(Request $request) :LoginResult => $authenticator->attempt($request, $next);
        }
    
        return $next;
    }
    
}