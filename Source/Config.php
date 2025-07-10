<?php

// define("URL_BASE", "http://phpmvc.tadsbr.local");

define("SITE", "#PHPTips");

function urlBase()
{
    return strtolower('http://' . $_SERVER['HTTP_HOST']);
}

function url(string $uri = null): string
{
    if ($uri)
        return urlBase() . "/{$uri}";

    return urlBase();
}




const DATA_LAYER_CONFIG = [
    "driver" => "pgsql",
    "host" => "localhost",
    "port" => "5432",
    "dbname" => "tprecos",
    "username" => "postgres",
    "passwd" => "admin",
    "options" => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
        PDO::ATTR_CASE => PDO::CASE_NATURAL
    ]
];
