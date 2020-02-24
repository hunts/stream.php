# Stream.php

[![build status][travis-image]][travis-url]
[![Coverage Status][coveralls-image]][coveralls-url]

The stream.php library is a PHP implementation of collection query API similar to Java Stream API and .NET Linq.

# Table of Contents
-----
1. [Installation](#Installation)
   * [Composer](#composer)
2. [Classes and methods](#classes-and-methods)
   * [Usage](#usage)
   * [Query API](#query-api)
   
-----

# Installation

## Composer

```shell script
composer require hunts/stream.php
```

# Classes and methods

## Usage

1. [Class Stream](#class-stream)
2. [Class NumberStream](#class-numberstream)
3. [Global Functions](#global-functions)

### Class Stream

_**Description**_: Instantiate a new Stream object

##### *Example*

~~~php
use Hunts\Stream;
...
$stream = Stream::from([$user1, $user2, $user3]);
~~~

### Class NumberStream

_**Description**_: Instantiate a new NumberStream object

##### *Example*

~~~php
use Hunts\Stream;
...
$stream = NumberStream::from([1, 2, 3]);
~~~

### Global functions

_**Description**_: Instantiate a new Stream object via global function: stream() or number_stream()

##### *Example*

~~~php
$stream = stream([$user1, $user2, $user3]);
$stream = number_stream([1, 2, 3]);
~~~

## Query API
 
* [Filter](API.md#filter) - Filter elements that match the give predicate
* [First](API.md#first) - Return the first element of matched items
* [Last](API.md#last) - Return the last element of matched items
* [Max](API.md#max)
* [Min](API.md#min)
* [Any](API.md#any)
* [All](API.md#all)
* [Each](API.md#each)
* [Map](API.md#map)
* [MapToNumber](API.md#maptonumber)
* [Reduce](API.md#reduce)
* [Distinct](API.md#distinct)
* [Sort](API.md#sort)
* [SortByDescending](API.md#sortbydescending)
* [Skip](API.md#skip)
* [Limit](API.md#limit)
* [Count](API.md#count)
* [ToArray](API.md#toarray)


[travis-url]: https://travis-ci.org/hunts/stream.php
[travis-image]: https://api.travis-ci.org/hunts/stream.php.svg
[coveralls-url]: https://coveralls.io/github/hunts/stream.php?branch=master
[coveralls-image]: https://coveralls.io/repos/hunts/stream.php/badge.svg?branch=master&service=github
