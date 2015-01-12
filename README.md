# Windmill Bundle [![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](https://github.com/cleentfaar/CLWindmillBundle/tree/master/LICENSE.md)

Implements the Windmill Chess Engine into the Symfony Framework.

[![Build Status](https://img.shields.io/travis/cleentfaar/CLWindmillBundle/master.svg?style=flat-square)](https://travis-ci.org/cleentfaar/CLWindmillBundle)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/cleentfaar/CLWindmillBundle.svg?style=flat-square)](https://scrutinizer-ci.com/g/cleentfaar/CLWindmillBundle/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/cleentfaar/CLWindmillBundle.svg?style=flat-square)](https://scrutinizer-ci.com/g/cleentfaar/CLWindmillBundle)
[![Latest Version](https://img.shields.io/github/release/cleentfaar/CLWindmillBundle.svg?style=flat-square)](https://github.com/cleentfaar/CLWindmillBundle/releases)
[![Total Downloads](https://img.shields.io/packagist/dt/cleentfaar/windmill-bundle.svg?style=flat-square)](https://packagist.org/packages/cleentfaar/CLWindmillBundle)


# Features

- Play and store chess games through easy-to-use services (check out the [GameController](https://github.com/cleentfaar/CLWindmillBundle/tree/master/Controller/GameController.php))
- Storage currently only has `file` and `orm` adapters, but you can easily add your own (check out the [OrmAdapter](https://github.com/cleentfaar/windmill/tree/master/Storage/Adapter/OrmAdapter.php) for an example).
- Custom Twig extension with some useful methods so you can display chess games anywhere in your templates (check out the [game-template](https://github.com/cleentfaar/CLWindmillBundle/tree/master/Resources/views/Game/index.html.twig)).
- [Console commands](https://github.com/cleentfaar/CLWindmillBundle/tree/master/Resources/doc/commands.md) so you can even play a game of chess through the Symfony Console!

Check out the documentation below for more information on using these features.


## Documentation

Check out [the index](https://github.com/cleentfaar/CLWindmillBundle/tree/master/Resources/doc/index.md).

Don't forget to also check out the [library's documentation](https://github.com/cleentfaar/windmill) which this bundle
implements! It contains more detailed information on the many components (or will do so soon anyway!).


## Why the bundle?

The aim of this bundle is to make it easier to work with the many (decoupled, but related) services inside the engine by
using the same DIC and templating patterns that are already provided by the framework. Since I use the Symfony Framework
for almost all of my own projects, it has become much easier to just adopt my work on their fundamentals.

Like my other projects, I'm also using to learn working with some techniques I haven't used before.
For this project, a few of them are:
- Make it easy to configure different templates for different sections of a chess game
- Check out other goals in the library's documentation [here](https://github.com/cleentfaar/windmill).
