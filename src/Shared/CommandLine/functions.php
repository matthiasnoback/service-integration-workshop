<?php

namespace Shared\CommandLine;

function stdout(string $string)
{
    write_to(fopen('php://stdout', 'w'), $string);
}

function stderr(string $string)
{
    write_to(fopen('php://stderr', 'w'), $string);
}

function write_to($handle, string $string)
{
    fwrite($handle, date('H:i:s') . ' ' . $string);
}

function line(string... $strings) : string
{
    return implode('', $strings) . "\n";
}

function make_green(string $string) : string
{
    return start_green() . $string . reset_color();
}

function make_red(string $string) : string
{
    return start_red() . $string . reset_color();
}

function reset_color() : string
{
    return "\033[0m";
}

function start_green() : string
{
    return "\033[32m";
}

function start_red() : string
{
    return "\033[31m";
}
