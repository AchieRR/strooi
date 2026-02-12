<?php
namespace Framework\Template;

interface RendererInterface
{
    public function render(string $template, array $data = []): string;
}