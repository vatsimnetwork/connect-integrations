<?php

namespace Vatsim\Osticket\Auth;

use ClientAccount;
use ClientCreateRequest;
use ClientSession;
use EndUser;
use ExternalUserAuthenticationBackend;
use osTicket;
use Vatsim\OAuth2\Client\Provider\VatsimResourceOwner;

class ClientBackend extends ExternalUserAuthenticationBackend
{
    use BackendTrait;

    public static $id = 'vatsim.client';

    public static $name = 'VATSIM';

    public static $service_name = 'VATSIM';

    public function supportsInteractiveAuthentication(): bool
    {
        return false;
    }

    private function signIn(VatsimResourceOwner $resourceOwner): ?ClientSession
    {
        $username = 'c'.$resourceOwner->getId();
        $acct = ClientAccount::lookupByUsername($username);
        if ($acct && $acct->getId()) {
            return new ClientSession(new EndUser($acct->getUser()));
        } else {
            $info['name'] = $resourceOwner->getFullName();
            $info['email'] = $resourceOwner->getEmail();
            $info['cid'] = $resourceOwner->getId();

            $client = new ClientCreateRequest($this, $username, $info);

            return $client->attemptAutoRegister();
        }
    }

    private function redirectTo(): string
    {
        return osTicket::get_base_url();
    }
}
