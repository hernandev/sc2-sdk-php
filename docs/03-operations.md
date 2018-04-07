# Broadcasting Operations

Considering the user already authorized your application, and you have the SDK client instance configured with the
user's access token, it's now time to broadcast operations.

## Broad-what, Ope-who?

If you are lost on those terms, just understand the basics:

An **operation**, is a given instruction transmitted to the Seem blockchain. The instruction could
be an upvote, downvote, comment, etc.

**Broadcast** is just a common name used to reference the act of signing and including the operation(s)
into the Steem blockchain.

Sometimes, a single operation will be broadcast, and, in other cases, like adding beneficiaries to a given
 comment, more than one operation will be broadcast at the same time.

Finally, we have the **transaction** concept. A transaction is the result of a successful broadcast of
a given number of operations.

Meaning, a transaction is a record within a Steem blockchain block, and a transaction contains all the
operations that were broadcast.

## A quick example.

Before we head on a reference of all types of operations available, We need to understand
the SDK flow for handling SteemConnect responses.

Here is a quick example, where we will upvote a given post:

``` php
<?php

// ...

// classes used on the example:

use SteemConnect\Operations\Vote;

// lets create a upvote operation:
$upvote = new Vote();
// set who is the user voting.
$upvote->voter('hernandev');
// let's vote on:
// https://busy.org/@utopian-io/utopian-io-reborn-smarter-simpler-better
$upvote->on('utopian-io', 'utopian-io-reborn-smarter-simpler-better');
// now, set the upvote at 50%
$upvote->percent(50);

// broadcast the operation to Steem through SteemConnect.
$response = $sdk->broadcast($operation)

// get the transaction from the broadcast response.
$transaction = $response->getTransaction();
```

The SDK api is really simple to understand, but, let's break down the concepts:

First, on that example, we created a **`Vote`** operation instance, and populated that vote
with the parameters we wanted.

It's important to notice that, given Steem blockchain data structures, we need to set who is the account
responsible for the operation, that's why the **`voter`** method was called.

!!! warning
    Notice that, on the SDK calls, the **`@`** should not be used, only the account names. On the example,
    the user **`@hernandev`** is voting on a post by **`@utopian-io`**, both accounts must
    be referenced as **`hernandev`** and **`utopian-io`** only, without the **`@`**. 

Now, to what matters:

The **`$response`** variable, returned from the **`broadcast()`** method on the SDK, is an instance of 
**`SteemConnect\Client\Response`**, this class is used to wrap the HTTP response from
SteemConnect.

In cases of errors, the broadcast method will not return a **`Response`** instance, instead, it will throw an exception.

On success cases, the transaction, that is the result of the broadcast, can be accessed though the **`getTransaction()`**
method on the **`Response`** instance.

The transaction, from that method, is an instance of **`SteemConnect\Transactions\Transaction`**, and an be converted to an
array, for storage purposes, or it's data can also be accessed using the Transaction getters. For a full list of the
available methods, consult the
[source code directly here](https://github.com/hernandev/sc2-sdk-php/blob/master/src/Transactions/Transaction.php).

## Available Operations.

Here we list valid operation examples, that can be adapted on your applications. Notice that each operation has it's
corresponding required scope, which the user must have previously allowed, on the authorization flow.

### Voting.

Both upvote and downvote are the same operation, the difference is the percent given on the vote. In other words, a vote
weight can vary between -100% and 100%.

On the numerous Steem frontend applications, the downvote is displayed as `flagged posts`, which means the weight itself
is rarely shown.

For this library, the weight of a given vote can be passed as argument using any percent notation:

- **Integer Notation**
   
    **`100`** represents **`100%`**, **`50`** represents **`50%`**, etc.
    
- **Default Notation**

    Used by most Steem clients, and the internal format, where a **`100%`** vote is represented by the number **`10000`**.

#### Upvote Example:

``` php
<?php

// ...

// alias classes:
use SteemConnect\Operations\Vote;

// lets create a upvote operation:
$upvote = new Vote();
// set who is the user voting.
$upvote->voter('hernandev');
// let's vote on:
// https://busy.org/@utopian-io/utopian-io-reborn-smarter-simpler-better
$upvote->on('utopian-io', 'utopian-io-reborn-smarter-simpler-better');
// now, set the upvote at 90%
$upvote->percent(90);

// broadcast the operation to Steem through SteemConnect.
$response = $sdk->broadcast($upvote);
```

#### Downvote Example:

``` php
<?php

// ...

// alias classes:
use SteemConnect\Operations\Vote;

// lets create a upvote operation:
$upvote = new Vote();
// set who is the user voting.
$upvote->voter('hernandev');
// let's vote on:
// https://busy.org/@utopian-io/utopian-io-reborn-smarter-simpler-better
$upvote->on('utopian-io', 'utopian-io-reborn-smarter-simpler-better');
// now, set the upvote at -100%
$upvote->percent(-100);

// broadcast the operation to Steem through SteemConnect.
$response = $sdk->broadcast($upvote);
```

If for some reason, you want to change the vote, all you need is to broadcast the new vote, Steem will consider the
last vote as valid and the previous ones as invalid.

### Follow & UnFollow.

Follow and Unfollow, are also, the same operation, meaning that the difference is only a internal flag 
inside it called **`what`**.

The what parameter of a follow operation is an array, and the value `"blog"` indicated the follow status.

#### Follow Example:

On this example, the user `@hernandev` starts following the user `@utopian-io`.

``` php
<?php

// ...

// alias classes:
use SteemConnect\Operations\Follow;

// create the operation:
$follow = new Follow();
// set the follower.
$follow->follower('hernandev');
// set who to follow.
$follow->follow('utopian-io')

// broadcast the operation to Steem through SteemConnect.
$response = $sdk->broadcast($follow);
```

#### UnFollow Example:

On this example, the user `@hernandev` stops following the user `@utopian-io`.

This means we are reverting the previous follow operation.

``` php
<?php

// ...

// alias classes:
use SteemConnect\Operations\Follow;

// create the operation:
$follow = new Follow();
// set the follower.
$follow->follower('hernandev');
// let's unfollow.
$follow->unfollow('utopian-io')

// broadcast the operation to Steem through SteemConnect.
$response = $sdk->broadcast($follow);
```

### Reblog.

Reblog is a simple operation, all you need to do is:

On the example, the user **`@hernandev`** is reblogging the post 
[https://steemit.com/@utopian-io/utopian-io-reborn-smarter-simpler-better](https://busy.org/@utopian-io/utopian-io-reborn-smarter-simpler-better)

``` php
<?php

// ...

// alias classes:
use SteemConnect\Operations\Reblog;

// create the operation:
$reblog = new Reblog();
// set the user that will reblog.
$reblog->account('hernandev');
// reblog a given post.
$reblog->reblog('utopian-io', 'utopian-io-reborn-smarter-simpler-better');

// broadcast the operation to Steem through SteemConnect.
$response = $sdk->broadcast($reblog);
```