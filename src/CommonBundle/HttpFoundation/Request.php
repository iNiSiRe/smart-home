<?php

namespace CommonBundle\HttpFoundation;

class Request extends \Symfony\Component\HttpFoundation\Request
{
    /**
     * @param \React\Http\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public static function createFromReactRequest(\React\Http\Request $request)
    {
        $result = self::create($request->getPath(), $request->getMethod(), $request->getQuery());
        $result->headers->add($request->headers->all());

        // Parse cookies
        $header = $result->headers->get('Cookie', '');
        $rawCookies = !empty($header) ? explode(';', $header) : [];
        foreach ($rawCookies as $cookie) {
            list ($key, $value) = explode('=', $cookie);
            $result->cookies->set(trim($key), $value);
        }

        return $result;
    }
}