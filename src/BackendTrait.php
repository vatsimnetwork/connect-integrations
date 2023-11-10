<?php

namespace Vatsim\Osticket\Auth;

use AccessDenied;
use Http;
use osTicket;
use Vatsim\OAuth2\Client\Provider\Vatsim as VatsimOauthProvider;
use Vatsim\OAuth2\Client\Provider\VatsimResourceOwner;

trait BackendTrait
{
    private VatsimOauthProvider $oauth;

    public function __construct(Config $config)
    {
        $this->oauth = new VatsimOauthProvider([
            'domain' => 'https://auth.vatsim.net',
            'clientId' => $config->getClientId(),
            'clientSecret' => $config->getClientSecret(),
            'redirectUri' => osTicket::get_base_url().'api/auth/ext',
        ]);
    }

    /**
     * @throws AccessDenied
     */
    public function triggerAuth(): void
    {
        parent::triggerAuth();

        if (isset($_GET['error'])) {
            Http::redirect($this->redirectTo());
        }

        if (isset($_GET['code'])) {
            $expectedState = $_SESSION['vatsim:oauth2:state'] ?? null;
            if (! isset($_GET['state']) || $_GET['state'] !== $expectedState) {
                Http::redirect($this->redirectTo());
            }

            unset($_SESSION['vatsim:oauth2:state']);

            try {
                $accessToken = $this->oauth->getAccessToken('authorization_code', [
                    'code' => $_GET['code'],
                ]);

                /** @var VatsimResourceOwner $resourceOwner */
                $resourceOwner = $this->oauth->getResourceOwner($accessToken);
            } catch (\Exception $e) {
                Http::redirect($this->redirectTo());
            }

            $user = $this->signIn($resourceOwner);
            if ($user) {
                $this->login($user, $this);
                Http::redirect($this->redirectTo());
            }
        }

        $url = $this->oauth->getAuthorizationUrl([
            'scope' => ['full_name', 'email'],
        ]);

        $_SESSION['vatsim:oauth2:state'] = $this->oauth->getState();

        Http::redirect($url);
    }
}
