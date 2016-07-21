# slack-pack
Move (some) items from Pocket to Slack on a regular schedule.

## Getting started

1. Clone this.
1. Run/install composer (composer install)
1. Set some env vars in .env and/or .htaccess (I use .htaccess when running locally)
  * SLACK_TOKEN= <You can get this at Slack>
  * POCKET_CONSUMER_KEY= (You can get this at <https://getpocket.com/developer/apps/new>)
  * POCKET_ACCESS_TOKEN= (You might not be able to set this right now. If you don't have this, see 'GET POCKET TOKEN' below)
  * SENDGRID_API_KEY= (for sending email reports)
  * TO_EMAIL= (email reports sent here using Sendgrid)
  * SIMULATION_CHANNEL= (This is the Slack channel that will be used when you do a simulation. I set this to my handle on Slack.)
1. Copy config-template.php to config.php
1. Update config.php with the channels and tags you want to use

## To get the POCKET_ACCESS_TOKEN, start with ...

* /pocket-auth.php
* Eventually you'll get an access token to add to the env vars.

## Normal operation

* just hit /index.php to simulate a run.
* hit /index.php?live= for the real deal
