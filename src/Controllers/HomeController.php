<?php
namespace Controller;

use Framework\Template\RendererInterface;

class HomeController
{
    private RendererInterface $renderer;

    public function __construct(RendererInterface $renderer)
    {
        $this->renderer = $renderer;
    }

    public function index()
    {
        $name = '<b>John Doe</b>';

        return $this->renderer->render('home/index', ['name' => $name]);
    }
}