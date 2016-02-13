<?php

namespace AppBundle\Imagine\Filter\Loader;

use Imagine\Image\ImageInterface;
use Liip\ImagineBundle\Imagine\Filter\Loader\LoaderInterface;
use Imagine\Image\ImagineInterface;

class TransparentMaskFilterLoader implements LoaderInterface
{
    /** @var ImagineInterface */
    private $imagine;

    /** @var string */
    private $rootPath;

    public function __construct(ImagineInterface $imagine, $rootPath)
    {
        $this->imagine = $imagine;
        $this->rootPath = $rootPath;
    }

    public function load(ImageInterface $image, array $options = array())
    {
        $mask = $this->imagine->open($this->rootPath . '/../web/bundles/app/img/' . $options['image']);

        $image->applyMask($mask);

        return $image;
    }
}