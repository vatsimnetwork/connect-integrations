# VATSIM Connect Authentication for osTicket

## Installation

Download `auth-vatsim.phar` from the
[latest release](https://github.com/vatsimnetwork/osticket-auth-vatsim/releases/latest)
and place it in the `include/plugins` folder in your osTicket installation.

Sign into your osTicket staff control panel, enable the plugin, and create a
plugin instance. You'll then be able to configure the plugin.

## Configuration

You'll need to add a new client to your organization in Connect. For more
information on this process, [please see here](https://vatsim.dev/services/connect/)

For the Redirect URL, specify `https://<hostname>/api/auth/ext`.

* **Client ID**: Your application's Client ID from auth.vatsim.net.
* **Client Secret**: Your application's Client Secret from auth.vatsim.net.
* **Authentication**: Whether to use VATSIM auth for clients, staff, both, or none.
