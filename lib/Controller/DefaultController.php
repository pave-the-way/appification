<?php
declare(strict_types=1);
namespace Appification\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Route as R;

class DefaultController
{
    use ControllerTrait;

    #[Route('/')]
    public function defaultIndex(Request $r) : Response
    {
        return new Response('it works');
    }
}
