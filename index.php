<?php
error_reporting(E_ERROR | E_PARSE);

if (include __DIR__ . '/vendor/autoload.php')
{
    Appification\getApp()->run();
}
