<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Twilio\Security\RequestValidator;

class TwilioRequestIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {


            $twilioToken = config('app.twilio.auth_token');

            if(empty($twilioToken)) {
                throw new \Exception('Token not found');
            }

            $requestValidator = new RequestValidator($twilioToken);

            $requestData = $request->toArray();

            // Switch to the body content if this is a JSON request.
            if (array_key_exists('bodySHA256', $requestData)) {
                $requestData = $request->getContent();
            }

            $isValid = $requestValidator->validate(
                $request->header('X-Twilio-Signature'),
                $request->fullUrl(),
                $requestData
            );

            if (!$isValid) {
                throw new \Exception();
            }


        return $next($request);
    }
}
