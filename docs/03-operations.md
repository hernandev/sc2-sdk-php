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

### Post & Comment.

On the Steem blockchain, a post is actually a comment. It means, that a Post is just a comment, without a parent.

But, since there is no parent on a post, we need to set the parent permlink, that one will be used as the category
for the post.

The example will make it a little bit easier to understand:

#### Post Example:

``` php
<?php

// ...

// alias classes:
use SteemConnect\Operations\Comment;

// create the operation:
$post = new Comment();
// set the author of the post.
$post->author('hernandev');
$post->category('introduceyourself');
// set the post title.
$post->title('Hello, this is Diego, but you can callme @hernandev');
// set the post body.
$post->body('You may insert the post content here, markdown is advised');
// optionally, you may set tags on the post:
$post->tags(['life', 'steem', 'steemdev']);

// broadcast the operation to Steem through SteemConnect.
$response = $sdk->broadcast($post);
```

Wait, what about the post URL?

The post URL is automatically extract from the title, using an internal slug function.

On the example, the title of the post was:

`Hello, this is Diego, but you can callme @hernandev`

The SDK will translate the title into a URL friendly slug:
 
`hello-this-is-diego-but-you-can-callme-at-hernandev`

But, if you want to customize the URL (which on Steem, is called `permlink`), you can do that by calling:

``` php
<?php

// ...

$post->permlink('this-is-a-custom-permlink-url-for-the-post');
```

The permlink does not need to match the title, the only rule here is that one author may not use the same permlink twice,
since that's the unique identifier for a post.

#### Comment / Reply Example:

To comment or reply on a given post, is also very simple:

On the example, we are going to reply to the post we just created on the previous example.

``` php
<?php

// ...

// alias classes:
use SteemConnect\Operations\Comment;

// create the operation:
$comment = new Comment();
// set the author of the post.
$comment->author('hernandev');
// set the parent post, you are replying to.
$comment->reply('hernandev', 'hello-this-is-diego-but-you-can-callme-at-hernandev');
// set the post body.
$post->body('You may insert the post content here, markdown is advised');
// optionally, you may set tags on the post:
$post->tags(['life', 'steem', 'steemdev']);

// broadcast the operation to Steem through SteemConnect.
$response = $sdk->broadcast($post);
```

Just as the post, a reply will have the permlink automatically filled from the body content, if you want to customize
the permlink, you can do the same you did for posts, by calling the `permlink()` method.

### Comment Options.

One important thing about comments, is that there are special options, like beneficiares, 50% SBD or 100% SP, etc.

Those special options are not a part of the comment operation itself. Instead those options must be set on a special
operation called **`comment_options`**.

!!! warning
    While is not required for a `comment` to have a `comment_options` operation, when they do, both operations **MUST**
    be broadcast at the same time, since they must be part of the same transaction.
    
#### Comment With Comment Options Example:

Here is an example, that creates a post, with options and broadcast the operations at the same time:

``` php
<?php

// ...

// alias classes:
use SteemConnect\Operations\Comment;
use SteemConnect\Operations\CommentOptions;

// create the operation:
$post = new Comment();
// set the author of the post.
$post->author('hernandev');
// set the category.
$post->category('testing');
// set the parent post, you are replying to.
$post->title('This is an example comment');
// set the post body.
$post->body('Hello dear Steemians...');
// optionally, you may set tags on the post:
$post->tags(['life', 'steem', 'steemdev']);


// create the comment options operation:
$options = new CommentOptions();
// now, we set the post that will own the options.
// this is where we link the two operations.
$options->of($post);
// you may disable votes.
$options->allowVotes(false);
// you may disable curation rewards.
$options->allowCurationRewards(false);
// don't wanna earn form your post, customize the max payout value.
$options->maxAcceptedPayout(0);
// set you only want 50% of the 50% SBD payout.
// for a 100% SP payout, set this value as 0 (zero).
$options->percentSteemDollars(5000);


// now, broadcast both operations at once.
$response = $sdk->broadcast($post, $options);
```