<?php
namespace Controller;

use Framework\Template\RendererInterface;

class ProductController
{
    private RendererInterface $renderer;

    public function __construct(RendererInterface $renderer)
    {
        $this->renderer = $renderer;
    }

    public function index()
    {
        return $this->renderer->render('product/index');
    }

    public function show(int $id)
    {
        return $this->renderer->render('product/show', ['id' => $id]);
    }
}