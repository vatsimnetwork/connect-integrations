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

        /** @var ClientAccount $acct */
        $acct = ClientAccount::lookupByUsername($username);
        if ($acct && $acct->getId()) {
            $session = new ClientSession(new EndUser($acct->getUser()));
        } else {
            $request = new ClientCreateRequest($this, $username, [
                'name' => $resourceOwner->getFullName(),
                'email' => $resourceOwner->getEmail(),
                'cid' => $resourceOwner->getId(),
            ]);

            $session = $request->attemptAutoRegister();
        }

        // Delete any other accounts for the same user
        ClientAccount::objects()
            ->filter(['user_id' => $session->getId()])
            ->exclude(['username' => $username])
            ->delete();

        return $session;
    }

    private function redirectTo(): string
    {
        return osTicket::get_base_url();
    }
}
