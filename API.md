# API References

## Filter

_**Description**_: Returns a stream consisting of the elements of this
stream that match the given predicate.

##### *Example*

~~~php
// Find users whose first names are "John"
$stream->filter(function(User $user) {
    return $user->getFirstName() === 'John';
});
~~~

## First

_**Description**_: Returns the first element of matched items, or null
if the stream is empty.

##### *Example*

~~~php
// Find the first user whose age is 28
$user = $stream->first(function(User $user) {
    return $user->getAge() === 28;
});
~~~


## Last

_**Description**_: Returns the last element of matched items, or null
if the stream is empty.

##### *Example*

~~~php
// Find the last user whose age is 28
$user = $stream->last(function(User $user) {
    return $user->getAge() === 28;
});
~~~
