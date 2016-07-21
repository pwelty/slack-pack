# slack-pack
Move (some) items from Pocket to Slack on a regular schedule.

Set some env vars in .env and/or .htaccess

SLACK_TOKEN=

POCKET_CONSUMER_KEY=

POCKET_ACCESS_TOKEN= <you might not be able to set this right now. If you don't have this, see 'GET POCKET TOKEN' below)

SENDGRID_API_KEY=

TO_EMAIL=

SIMULATION_CHANNEL=


## To get the POCKET_ACCESS_TOKEN, start with ...

/pocket-auth.php

Eventually you'll get an access token to add to the env vars.

## Setup

Copy config-template.php to config.php

Make sure all env vars are populated

## Normal operation

just hit index.php to simulate a run.

hit index.php/?live= for the real deal
