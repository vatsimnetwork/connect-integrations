<?php

namespace Vatsim\Osticket\Auth;

use ExternalStaffAuthenticationBackend;
use osTicket;
use StaffSession;
use Vatsim\OAuth2\Client\Provider\VatsimResourceOwner;

class StaffBackend extends ExternalStaffAuthenticationBackend
{
    use BackendTrait;

    public static $id = 'vatsim';

    public static $name = 'VATSIM';

    public static $service_name = 'VATSIM';

    private function signIn(VatsimResourceOwner $resourceOwner): ?StaffSession
    {
        $username = 's'.$resourceOwner->getId();
        if (($staff = StaffSession::lookup($username)) && $staff->getId()) {
            return $staff;
        }

        $_SESSION['_staff']['auth']['msg'] = 'Your credentials are valid but you do not have a staff account.';

        return null;
    }

    private function redirectTo(): string
    {
        return osTicket::get_base_url().'scp/';
    }
}
