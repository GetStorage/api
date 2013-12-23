<?php

require_once __DIR__ . '/vendor/autoload.php';

$klein = new \Klein\Klein();


$klein->with('/v2', function() use ($klein) {

    /**
     * File System
     *
     * Requires authentication
     */
    $klein->with('/fs', function() use ($klein) {

        $klein->respond(function($request, $response, $service) {
            // Authenticate
        });

        /**
         * List our filesystem
         */
        $klein->respond('GET', '/?', function($request, $response, $service) {

        });

        $klein->respond('POST', '/?', function($request, $response, $service) {

        });

    });

});


$klein->dispatch();
