# bagisto-npa-pay-processing

This package lets you use NPA (National Processing Alliance) as your payment processor.

See details about the NPA API here: https://npa.transactiongateway.com/merchants/resources/integration/integration_portal.php#dp_php

## Usage

Save this to packages/Iateadonut/NPAPayProcessing

add to your .env:

NPA_PUBKEY=

NPA_PRIVATE_KEY=

add to config/app.php in your 'providers' array:

Iateadonut\NPAPayProcessing\Providers\NPAServiceProvider::class,