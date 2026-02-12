<?php
namespace Framework\Template;

class Renderer implements RendererInterface
{
    public function render(string $template, array $data = []): string
    {
        $viewPath = __DIR__ . '/../../../views/' . $template . '.php';

        extract($data, EXTR_SKIP);

        ob_start();

        require $viewPath;

        return ob_get_clean();
    }
}