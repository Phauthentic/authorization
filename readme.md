# Authorization

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.txt)
[![Build Status](https://img.shields.io/travis/Phauthentic/authorization/master.svg?style=flat-square)](https://travis-ci.org/Phauthentic/authorization)
[![Coverage Status](https://img.shields.io/codecov/c/github/Phauthentic/authorization.svg?style=flat-square)](https://codecov.io/github/Phauthentic/authorization)

A framework agnostic authorization library based on policies.

## Authorization not Authentication

This library intends to provide a policy based framework around authorization and access
control. Authentication is a [separate concern](https://en.wikipedia.org/wiki/Separation_of_concerns) that has been
packaged into a separate [authentication library](https://github.com/Phauthentic/authentication).

## Installation

Install it via [Composer](http://getcomposer.org):

```
composer require Phauthentic/authorization
```

## Documentation

 * [Quick Start and Introduction to the basics](docs/Quick-start-and-introduction.md)
 * [Policies](docs/Policies.md)
 * [Policy Resolver](docs/Policy-Resolvers.md)
 * [Middleware](docs/Middleware.md)
 * [Checking Authorization](docs/Checking-Authorization.md)

## Copyright & License

Licensed under the [MIT license](LICENSE.txt).

* Copyright (C) [Phauthentic](https://github.com/Phauthentic)
* Copyright (C) [Cake Software Foundation, Inc.](https://cakefoundation.org)
