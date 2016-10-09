# slack-pack
Move (some) items from Pocket to Slack on a regular schedule.

**NOTE: You're going to need a Pocket account and admin access to a Slack group to do this.**

For each Pocket tag you put into config.php, Slack Pack will post that url to the corresponding Slack channel (also set in config.php).

Then, the tag in Pocket will be changed from 'tag' to 'tag-posted'.

You'll get an on-screen (if you're watching) and email report after.

## Getting started

1. Clone this. 'Git clone git@github.com:pwelty/slack-pack.git'
1. Install dependencies 'computer update; composer install'
1. Set some env vars in .env and/or .htaccess (I use .htaccess when running locally). NOTE: you can also put these in config.php.
  * SLACK_TOKEN. You can get this at Slack. I use a test token that I got here: <https://api.slack.com/docs/oauth-test-tokens>. But I think this could be registered as a real app, too.
  * POCKET_CONSUMER_KEY. You can get this at <https://getpocket.com/developer/apps/new>.
  * POCKET_ACCESS_TOKEN. (You might not be able to set this right now. If you don't have this, see 'To get the POCKET_ACCESS_TOKEN' below)
  * SENDGRID_API_KEY. (for sending email reports)
  * TO_EMAIL. (email reports sent here using Sendgrid)
  * SIMULATION_CHANNEL. (This is the Slack channel that will be used when you do a simulation. I set this to my handle on Slack.)
1. Copy config-template.php to config.php (NOTE: config.php is ignored by Git).
1. Update config.php with the channels, tags, and vars you want to use.

## To get the POCKET_ACCESS_TOKEN, start with ...

* Be sure you have a Pocket consumer key. You can get this here: <https://getpocket.com/developer/apps/new>
* Set that key in config.php or ENV vars.
* Run /index.php
* Eventually you'll get an access token to add to the env vars or config file.

## Normal operation

* just hit /index.php to simulate a run.
* hit /index.php?live= for the real deal
* Use /index.php?live=&doweekend= to force running on the weekend (otherwise it will skip Sat and Sun)
