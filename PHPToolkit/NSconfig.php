<?php
define("NS_ENDPOINT", "2021_2");
define("NS_HOST", "https://644154.suitetalk.api.netsuite.com/"); // Web Services URL - The service URL can be found in Setup -> Company -> Company Information -> Company URLs under SUITETALK (SOAP AND REST WEB SERVICES). E.g. https://ACCOUNT_ID.suitetalk.api.netsuite.com
define("NS_ACCOUNT", "644154");

// Token Based Authentication data
define("NS_CONSUMER_KEY", "7746c6125dd22a59d3b8d12de828d99b421fcd8a66d4d3351a539f076290cf1f"); // Consumer Key shown once on Integration detail page
define("NS_CONSUMER_SECRET", "2797da9d3187df2e35342904ae501125027d82d2c998db65b460a06ad8c8e141"); // Consumer Secret shown once on Integration detail page
// following token has to be for role having those permissions: Log in using Access Tokens, Web Services
define("NS_TOKEN", "be16276ef69370b78f3cca05d13b765afc540693caa4b53733df5ed2f13686b8"); // Token Id shown once on Access Token detail page
define("NS_TOKEN_SECRET", "1febb6ad6a1f2620da0f0eef0b5632c94dfb7dcdb55f7652b101f067447f6870"); // Token Secret shown once on Access Token detail page
?>
