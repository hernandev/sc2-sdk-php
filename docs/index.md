# SteemConnect PHP SDK

This is the documentation for **[SteemConnect](https://steemconnect.com) [PHP SDK](https://github.com/hernandev/sc2-php-sdk)**, which is available at **[https://github.com/hernandev/sc2-sdk-php](https://github.com/hernandev/sc2-sdk-php)**.

## How to Read the Code Examples.

This documentation was built with several code examples.

For code highlighting and easy to understand reasons, any PHP code will start with a `<?php` sign, and, on a given documentation page scope, when a given code example is a sequence of a previous example, it will be indicated by a `// ...` line, meaning that the current code is a continuation of the previous one.
## What this library is about?

This library allows for interaction with **[SteemConnect](https://steemconnect.com)** (v2) on PHP projects.

Meaning this library can achieve two things:

### Handle authentication flow: 

Authentication and Authorization flow though OAuth.

### Build and broadcast operations: 

Broadcast Steem blockchain operations through SteemConnect.

## What this library is NOT about:

This library is intended to be a SteemConnect client for PHP only. Meaning no additional scopes were incorporated.

Parsing transactions and content display is not a part of this project.

If you are looking into displaying Steem blockchain posts, you may use another project of mine, [LightRPC](https://github.com/hernandev/light-rpc).
