<?php

namespace Vatsim\Osticket\Auth;

use Plugin as BasePlugin;
use StaffAuthenticationBackend;
use UserAuthenticationBackend;

class Plugin extends BasePlugin
{
    public $config_class = Config::class;

    public function bootstrap(): void
    {
        /** @var Config $config */
        $config = $this->getConfig();

        if ($config->isStaffEnabled()) {
            StaffAuthenticationBackend::register(new StaffBackend($config));
        }

        if ($config->isClientEnabled()) {
            UserAuthenticationBackend::register(new ClientBackend($config));
        }
    }
}
