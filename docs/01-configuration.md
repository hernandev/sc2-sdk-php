# Configuration

Before heading into SDK usage, we need to configure it, with credentials, return url and scopes.

## Configuration Class.

To start, we are going to create an instance of **`SteemConnect\Config\Config`**.

``` php
<?php

use SteemConnect\Config\Config;

// the client id and secret can be obtained on SteemConnect dashboard.
$clientId = 'your-steem-connect-client-id';
$clientSecret = 'your-steem-connect-client-secret';

// create the configuration instance, using  the credentials.
$config = new Config($clientId, $clientSecret);
```

### Return URL.

The return URL is a parameter for the OAuth authorization flow. This URL will receive
the temporary code from SteemConnect after the user authorizes your application
to act on the their behalf.

To configure the return URL, you just call the **`setReturnUrl`** method on the Config object:

``` php
<?php

// ...

// set the return URL on config.
$config->setReturnUrl('https://your-steem-app/auth/callback');
```

!!! note
    The URL being used on configuration must match the one configured on SteemConnect dashboard, otherwise the authorization flow will fail.

### Scopes.

Other required configuration are which scopes your application requires.

On the OAuth flow, a scope could be translated to which permissions your users will grant you.

There are several scopes available. The list of scopes presented here may change with time, so, an up-to-date reference can fe found at **[SteemConnect wiki](https://github.com/steemit/steemconnect/wiki/OAuth-2#scopes)**.

| Scope                    | Description
| -                        | -
| **login**                | Verify Steem identity
| **offline**              | Allow long-lived token
| **vote**                 | Upvote, downvote or unvote a post or comment
| **comment**              | Publish or edit a post or a comment
| **comment_delete**       | Delete a post or a comment
| **comment_options**      | Add options for a post or comment
| **custom_json**          | Follow, unfollow, ignore, reblog or any custom_json operation
| **claim_reward_balance** | Claim reward for user

!!! note
    If your application only needs to verify the user identity, the required scope **`login`** does not persist changes on the user account, so be sure to remember the user session to avoid the authorization dialog in every visit.
    
To configure the scopes your application will require, just call the `setScopes` method on the config object, passing those scopes as an array:

``` php
<?php

// ...

// set the required scopes on the configuration.
$config->setScopes([
    'login',
    'vote',
    'comment'
]);

```

### Application and Community Name.

An optional but interesting feature, is to configure both the application and community names. Those are used to indicate
what application was used to make the post, and some frontend will display that information.

If you don't know what I'm talking about, here is an example:

![app name example](https://ipfs.io/ipfs/Qmcesk7EDVRr1t1kgD7P4AfsycZcka7gafvNpMnKdq3APi)

The syntax for application name, as of right now, is **`lowercase-app-name/version`**, here's an example:

``` php
<?php

// ...

// set the application name and version.
$config->setApp('coolapp/2.4');
```

The same way, it's possible to set the community name:

``` php
<?php

// ...

// set the community name.
$config->setCommunity('CoolApp');
```

### Custom SteemConnect Servers.

If for some reason, you are using a custom install of SteemConnect (a development install, for example), you may
change the base URL so all calls will use that domain.

``` php
<?php

// ...

// customize the SteemConnect address.
$config->setBaseUrl('https://my-custom-steemconnect.com');

```