<?php

require_once __DIR__ . '/vendor/autoload.php';

use Mardy\Hmac\Hmac;
use Mardy\Hmac\Config\Config as HmacConfig;
use Mardy\Hmac\Storage\NonPersistent as HmacStorage;

$klein = new \Klein\Klein();

$klein->respond(function($request, $response, $service, $app) {
    $app->register('db', function() {
        $db = new stdClass();

        return $db;
    });
});

$klein->with('/v2', function() use ($klein) {

    /**
     * File System
     *
     * Requires authentication
     */
    $klein->with('/fs', function() use ($klein) {

        $klein->respond(function(\Klein\Request $request, \Klein\Response $response, $service, $app) {
            $headers = $request->headers();

            // Pre-Auth
            $service->auth_key = $headers['auth-key'];
            $service->auth_secret = $headers['auth-secret'];

            if(empty($service->auth_key) || empty($service->auth_secret)) {
                return $response->json("Nope");
            }

            // Get user & key

            $app->register('hmac', function() use($request) {
                $hmac = new Hmac(new HmacConfig, new HmacStorage, new Mardy\Hmac\Headers\Values);

                $hmac->getConfig()->setAlgorithm("sha512");

                return $hmac;
            });

            $app->hmac->getStorage()
                ->setHmac($service->auth_key)
                ->setTimestamp($headers['auth-timestamp'])
                ->setUri($request->uri());

            // Test authentication
            if(!$app->hmac->check()) {
                return $response->json(array('errors' => array($app->hmac->getError())));
            }


        });

        /**
         * List our filesystem
         */
        $klein->respond('GET', '/?', function($request, $response, $service, $app) {

            return $response->json(array('stuff'));
        });

        $klein->respond('POST', '/?', function($request, $response, $service) {

        });

    });

});


$klein->dispatch();
