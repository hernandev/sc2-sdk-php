# Authentication

This library wraps all authentication functionality built on **[oauth2-sc2](https://github.com/hernandev/oauth2-sc2)**, if you are using the SDK, there's no need for separate authentication configuration, since all logic is wrapped on the SDK.

## Before Authentication.

Before we head into the actual authentication, we must have a SDK client instance.

The class that handle all SDK features is **`SteemConnect\Client\Client`** and for the examples on this section, we will create one instance and name the variable **`$sdk`**:

Also, we are assuming, you have the **`$config`** variable from the configuration section available.

``` php
<?php

// ...

// alias the client.
use SteemConnect\Client\Client;

// create a sdk client instance, using the
// config instance we previously created.
$sdk = new Client($config);
```

## The OAuth Flow.

Those those who are not so familiar with OAuth, the basic flow is this:

1. Application redirects the user the the OAuth Provider, in this case, SteemConnect.
2. The user, grants, on the SteemConnect page, the permissions so your application can act on the user behalf.
3. After the authorization, the provider, in this case SteemConnect, will redirect the user back to your application, with a temporary code that will be exchanged with an access token.

All of those steps are wrapped by the SDK.

This means, that you only need to do two SDK calls in order to authenticate and get authorization from users.

## Redirecting To SteemConnect.

When the users access your application, you will need to do a redirect to SteemConnect.

Now, using the SDK we can build the URL used to redirect the users to:


``` php
<?php

// ...

// get the authorization URL.
$authorizationURL = $sdk->auth()->getAuthorizationUrl();
```

Now, you can actually redirect the user to that URL. Be free to use any method you want.

``` php
<?php

// ...


// using pure PHP:
header("Location: {$authorizationURL}");


// using Laravel inside a controller:
return redirect($authorizationURL);
```

By doing that for the first time, your users will be faced with the following authorization page:

![http://ipfs.io/ipfs/QmShgahjCzUbbdZPrG4rHGcJL8ugeR2XspfFwrDw9wy6zj](http://ipfs.io/ipfs/QmShgahjCzUbbdZPrG4rHGcJL8ugeR2XspfFwrDw9wy6zj)


!!! note
    Notice on this example, the application requesting permission is [Busy.org](https://busy.org). Both the logo and application name will change on your own applications, and can be customized through SteemConnect dashboard.

!!! note
    Also, the scopes required may change by your own requirements, be sure to read all and request only the ones you actually need. Avoid having more powers over your users accounts than you actually need.
    
## Handling the Return.

In some places referred to as `callback`, the return from an authorization flow is just the practice of exchanging the temporary grant code
with a real access token, that is required to operate your users accounts after the authorization.

The SDK also abstracts all that parsing, so you only need to do one call at callback phase.

``` php
<?php

// ...


// exchanging the authorization code by a access token.
$token = $sdk->auth()->parseReturn();
```

The token returned on the callback / return page is an instance of **`SteemConnect\Auth\Token`**, which we will discuss on the next section.

## Storing and Using Access Tokens.

Each access token is specific to a given user. Meaning when you need to the access token every time you need to broadcast an operation
to the Steem blockchain.

Since the tokens as an instance of **`SteemConnect\Auth\Token`**, it's easy to serialize and factory it's instance:

### Serialization for Storage:

When you have a **`Token`** instance, and you need to store it, you can transform the token into a JSON string by doing:

``` php
<?php

// ...

// create a json representation of the token for storage.
$tokenJson = json_encode($token);
```

### Parsing Stored Tokens.

Now, on a late time, when a given user returns to your application, you can just factory the token instance back:

``` php
<?php

// ...

use SteemConnect\Auth\Token;

// the fromJsonString method will decode 
// the token json string into a token instance.
$token = Token::fromJsonString($tokenJson);
```

## Using Tokens.

Since all operations needs to be authenticated, the SDK requires a `Token` instance to work.

To configure the access token on the SDK, all you need to do is:

``` php 
<?php

// ...

// set the token instance on the SDK, 
// so operations can be authenticated.
$sdk->setToken($token);
```