<?php

namespace CL\Bundle\WindmillBundle\Twig;

use CL\Decoration\Factory\DecoratorFactoryInterface;
use CL\Decoration\AbstractDecorator;

class DecoratorExtension extends \Twig_Extension
{
    /**
     * @var DecoratorFactoryInterface
     */
    protected $decoratorFactory;

    /**
     * @param DecoratorFactoryInterface $decoratorFactory
     */
    public function __construct(DecoratorFactoryInterface $decoratorFactory)
    {
        $this->decoratorFactory = $decoratorFactory;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'twig_extension_decoration_decorator';
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            'decorate' => new \Twig_Function_Method($this, 'decorate'),
        );
    }

    /**
     * @param object|null $object
     *
     * @throws \InvalidArgumentException
     *
     * @return object
     */
    public function decorate($object)
    {
        if (null === $object) {
            return null;
        }

        if (!is_object($object)) {
            throw new \InvalidArgumentException(sprintf('You must pass an object to decorate, "%s" given', gettype($object)));
        }

        if ($object instanceof AbstractDecorator) {
            return $object;
        }

        return $this->decoratorFactory->create($object, $this->decoratorFactory);
    }
}
