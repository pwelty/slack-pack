# slack-pack
Move (some) items from Pocket to Slack on a regular schedule.

Set some env vars in .env and/or .htaccess

SLACK_TOKEN=
POCKET_CONSUMER_KEY=
POCKET_ACCESS_TOKEN=
SENDGRID_API_KEY=
TO_EMAIL=
SIMULATION_CHANNEL=

To get the POCKET_ACCESS_TOKEN, start with ...

/pocket-auth.php

Eventually you'll get an access token to add to the env vars.

Normal operation

just hit index.php
