<?php

namespace Vatsim\Osticket\Auth;

use ClientAccount;
use ClientSession;
use EndUser;
use ExternalUserAuthenticationBackend;
use osTicket;
use User;
use UserAccount;
use UserAccountStatus;
use Vatsim\OAuth2\Client\Provider\VatsimResourceOwner;

class ClientBackend extends ExternalUserAuthenticationBackend
{
    use BackendTrait;

    private const USER_STATUS = UserAccountStatus::CONFIRMED | UserAccountStatus::FORBID_PASSWD_RESET;

    public static $id = 'vatsim.client';

    public static $name = 'VATSIM';

    public static $service_name = 'VATSIM';

    public function supportsInteractiveAuthentication(): bool
    {
        return false;
    }

    private function signIn(VatsimResourceOwner $resourceOwner): ?ClientSession
    {
        // Get all the details ready.
        $username = 'c'.$resourceOwner->getId();
        $userDetails = [
            'name' => $resourceOwner->getFullName(),
            'email' => $resourceOwner->getEmail(),
            'cid' => $resourceOwner->getId(),
        ];

        // First, try to find a UserAccount by VATSIM ID.
        /** @var UserAccount $acct */
        $acct = UserAccount::lookupByUsername($username);
        if ($acct) {
            // If we found one, get the User.
            /** @var User $user */
            $user = $acct->getUser();

            // Upsert the User.
            $user->setAll($userDetails);
            $user->save(true);
        } else {
            // If we didn't find one, find or create a User by email.
            /** @var User $user */
            $user = User::fromVars($userDetails);

            // Now that we have a User, try to get its UserAccount.
            /** @var UserAccount $acct */
            $acct = $user->getAccount();
        }

        // If we still don't have a UserAccount, instantiate a new one.
        if (! $acct) {
            /** @var osTicket $ost */
            global $ost;

            $acct = new UserAccount([
                'user_id' => $user->getId(),
                'timezone' => $ost->getConfig()->getDefaultTimezone(),
            ]);
        }

        // Upsert the UserAccount.
        $acct->setAll([
            'status' => $acct->get('status', 0) | self::USER_STATUS,
            'username' => $username,
            'passwd' => null,
        ]);
        $acct->save();

        return new ClientSession(new EndUser($user));
    }

    private function redirectTo(): string
    {
        return osTicket::get_base_url();
    }
}
