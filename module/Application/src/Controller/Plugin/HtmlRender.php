<?php
/**
 * Created by PhpStorm.
 * User: 400005
 * Date: 11/03/2019
 * Time: 12:19
 */

namespace Application\Controller\Plugin;


use Traversable;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\View\Model\ModelInterface;
use Zend\View\Renderer\PhpRenderer;
use Zend\View\Renderer\RendererInterface;

class HtmlRender extends AbstractPlugin
{
    /**
     * @var PhpRenderer
     */
    protected $renderer;

    /**
     * @param  string|ModelInterface $nameOrModel
     * @param  null|array|Traversable $values
     * @param  string|bool|ModelInterface $layout
     * @return string
     */
    public function __invoke($nameOrModel, $values = null, $layout = false)
    {
        $content = $this->getRenderer()->render($nameOrModel, $values);
        if (!$layout) {
            return $content;
        }

        if (true === $layout) {
            $layout = 'layout/layout';
        }
        return $this->getRenderer()->render($layout, [
            'content' => $content,
        ]);
    }

    /**
     * @return PhpRenderer
     */
    public function getRenderer()
    {
        return $this->renderer;
    }

    /**
     * @param  PhpRenderer|RendererInterface $renderer
     * @return self
     */
    public function setRenderer(RendererInterface $renderer)
    {
        $this->renderer = $renderer;
        return $this;
    }
}