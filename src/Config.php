<?php

namespace Vatsim\Osticket\Auth;

use ChoiceField;
use Plugin;
use PluginConfig;
use SectionBreakField;
use TextboxField;

class Config extends PluginConfig
{
    public function isClientEnabled(): bool
    {
        $enabled = $this->get('v-enabled');

        return $enabled === 'all' || $enabled === 'client';
    }

    public function isStaffEnabled(): bool
    {
        $enabled = $this->get('v-enabled');

        return $enabled === 'all' || $enabled === 'staff';
    }

    public function getClientId(): string
    {
        return $this->get('client_id');
    }

    public function getClientSecret(): string
    {
        return $this->get('client_secret');
    }

    private static function translate(): array
    {
        return Plugin::translate('auth-oauth');
    }

    public function getOptions(): array
    {
        [$__, $_N] = self::translate();

        return [
            'vatsim' => new SectionBreakField([
                'label' => $__('VATSIM Authentication'),
            ]),
            'client_id' => new TextboxField([
                'label' => $__('Client ID'),
                'required' => true,
                'configuration' => ['size' => 60, 'length' => 100],
            ]),
            'client_secret' => new TextboxField([
                'label' => $__('Client Secret'),
                'required' => true,
                'configuration' => ['size' => 60, 'length' => 100],
            ]),
            'v-enabled' => new ChoiceField([
                'label' => $__('Authentication'),
                'required' => true,
                'choices' => [
                    '0' => $__('Disabled'),
                    'staff' => $__('Staff Only'),
                    'client' => $__('Members Only'),
                    'all' => $__('Staff and Members'),
                ],
            ]),
        ];
    }
}
