# Installation

## Step 1) Get the bundle

First you need to get a hold of this bundle. There are two ways to do this:

### Method a) Using composer

Add the following to your ``composer.json`` (see http://getcomposer.org/)

    "require" :  {
        "cleentfaar/windmill-bundle": "*"
    }


### Method b) Using submodules

Run the following commands to bring in the needed libraries as submodules.

```bash
git submodule add https://github.com/cleentfaar/CLWindmillBundle.git vendor/bundles/CL/Bundle/WindmillBundle
```


## Step 2) Register the namespaces

If you installed the bundle by composer, use the created autoload.php  (jump to step 3).
Otherwise, add the following two namespace entries to the `registerNamespaces` call in your autoloader:

``` php
<?php
// app/autoload.php
$loader->registerNamespaces(array(
    // ...
    'JMS\SerializerBundle' => __DIR__.'/../vendor/bundles/jms/serializer-bundle',
    'CL\Bundle\WindmillBundle' => __DIR__.'/../vendor/bundles/cleentfaar/windmill-bundle',
    // ...
));
```


## Step 3) Register the bundle

To start using the bundle, register it in your Kernel (note the required `JMSSerializerBundle`).

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new JMS\SerializerBundle\JMSSerializerBundle(), // required for this bundle
        new CL\Bundle\WindmillBundle\CLWindmillBundle(),
        // ...
    );
}
```

## Step 4) Configure the bundle (optional)

The bundle does not require you to define any configuration, relying on default values instead.
Here is a reference of all the options that can be set and their default values:

```yaml
# app/config/config.yml
cl_windmill:
    move_route: cl_windmill_game_move # default
    move_registry:
        storage_method: json
        storage_target: %kernel.cache_dir%/moves.json
    storage:
        type: orm
        game_class: Game::class
        game_state_class: GameState::class
    templates:
        board: CLWindmillBundle:Partial:board.html.twig
        captures: CLWindmillBundle:Partial:captures.html.twig
        clocks: CLWindmillBundle:Partial:clocks.html.twig
        game: CLWindmillBundle:Partial:game.html.twig
        history: CLWindmillBundle:Partial:history.html.twig
        javascripts: CLWindmillBundle:Partial:javascripts.html.twig
        hidden_form: CLWindmillBundle:Partial:hidden_form.html.twig
        vs: CLWindmillBundle:Partial:vs.html.twig
```


# Ready?

Let's start playing chess! Check out the [usage documentation](https://github.com/cleentfaar/CLWindmillBundle/tree/master/Resources/doc/usage.md)!
