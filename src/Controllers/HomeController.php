<?php 

Declare(strict_types=1);

namespace App\Controllers;

use GuzzleHttp\Psr7\Utils;
use GuzzleHttp\Psr7\Response as GuzzelResponse;
use Nyholm\Psr7\Response as NyholmResponse;
use Psr\Http\Message\ResponseInterface;

class HomeController
{
    public function index() : ResponseInterface
    {
        

$stream = Utils::streamFor("Homepage");

$response = new GuzzelResponse;

$response = $response->withBody($stream);

return $response;
    }
}