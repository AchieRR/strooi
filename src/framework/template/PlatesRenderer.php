<?php
namespace Framework\Template;

use League\Plates\Engine;

class PlatesRenderer implements RendererInterface
{
    private Engine $engine;

    public function __construct()
    {
        $this->engine = new Engine(__DIR__ . '/../../../views');
    }

    public function render(string $template, array $data = []): string
    {
        return $this->engine->render($template, $data);
    }
}