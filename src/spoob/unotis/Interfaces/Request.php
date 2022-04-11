<?php namespace Spoob\Unotis\Interfaces;

interface Request
{
    function post(string $url, array $data): string;
}