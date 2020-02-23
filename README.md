# Stream.php

[![build status][travis-image]][travis-url]
[![Coverage Status][coveralls-image]][coveralls-url]

The stream.php library is a PHP implementation of collection query API similar to Java Stream API and .NET Linq.

# Table of Contents
-----
1. [Installing](#installing)
   * [Installation](#installation)
2. [Classes and methods](#classes-and-methods)
   * [Usage](#usage)
   * [Query API](#query-api)
   
-----

# Installing

## Installation

Via composer:

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
$stream = Stream::from([$user1, $user2, $user3]);
~~~

### Class NumberStream

_**Description**_: Instantiate a new NumberStream object

##### *Example*

~~~php
$stream = NumberStream::from([1, 2, 3]);
~~~

### Global functions

_**Description**_: Instantiate a new Stream objects via global functions: stream() and number_stream()

##### *Example*

~~~php
$stream = stream([$user1, $user2, $user3]);
$stream = number_stream([1, 2, 3]);
~~~

## Query API
 
* [Filter](#filter) - Filter elements that match the give predicate
* [First](#first) - Return the first element of matched items
* [Last](#last) - Return the last element of matched items
* [Max](#max) - TO BE DOCUMENTED
* [Min](#min) - TO BE DOCUMENTED
* [Any](#any) - TO BE DOCUMENTED
* [All](#all) - TO BE DOCUMENTED
* [Each](#each) - TO BE DOCUMENTED
* [Map](#map) - TO BE DOCUMENTED
* [MapToNumber](#maptonumber) - TO BE DOCUMENTED
* [Reduce](#reduce) - TO BE DOCUMENTED
* [Distinct](#distinct) - TO BE DOCUMENTED
* [Sort](#sort) - TO BE DOCUMENTED
* [SortByDescending](#sortbydescending) - TO BE DOCUMENTED
* [Skip](#skip) - TO BE DOCUMENTED
* [Limit](#limit) - TO BE DOCUMENTED
* [Count](#count) - TO BE DOCUMENTED
* [ToArray](#toarray) - TO BE DOCUMENTED

### Filter

_**Description**_: Returns a stream consisting of the elements of this
stream that match the given predicate.

##### *Example*

~~~php
// Find users whose first names are "John"
$stream->filter(function(User $user) {
    return $user->getFirstName() === 'John';
});
~~~

### First

_**Description**_: Returns the first element of matched items, or null
if the stream is empty.

##### *Example*

~~~php
// Find the first user whose age is 28
$user = $stream->first(function(User $user) {
    return $user->getAge() === 28;
});
~~~


### Last

_**Description**_: Returns the last element of matched items, or null
if the stream is empty.

##### *Example*

~~~php
// Find the first user whose age is 28
$user = $stream->last(function(User $user) {
    return $user->getAge() === 28;
});
~~~


[travis-url]: https://travis-ci.org/hunts/stream.php
[travis-image]: https://api.travis-ci.org/hunts/stream.php.svg
[coveralls-url]: https://coveralls.io/github/hunts/stream.php?branch=master
[coveralls-image]: https://coveralls.io/repos/hunts/stream.php/badge.svg?branch=master&service=github
