<?php
namespace App\Services;

use App\Contracts\Action;

class ActionService implements Action\Service {

    /**
     * @var Action\Store;
     */
    private $storage;

    function __construct(Action\Store $driver) {
        $this->storage = $driver;
    }

    public function setRequestData(\App\Action $action, \Illuminate\Http\Request $request) {
        if (!$action->app) {
            $action->app = $request->getHttpHost();
        }
        if (!$action->http_referrer_raw) {
            $action->http_referrer_raw = $request->header('Referer');
        }
        if (!$action->user_agent_raw) {
            $action->user_agent_raw = $request->header('User-Agent');
        }
        if (!$action->ip) {
            // TODO make sure this accounts for CDNs/proxies
            $action->ip = $request->ip();
        }

        // TODO location
    }

    public function setDefaults(\App\Action $action) {
        if (!$action->timestamp) {
            // TODO explicit timezone or UTC
            $action->timestamp = date('Y-m-d::H:i:s');
        }
        if (!$action->uniqueness_key) {
            $action->uniqueness_key = join(':', [
                $action->category,
                $action->name,
                $action->app,
                $action->timestamp,
            ]);
        }
        if (!$action->counter_key) {
            $action->counter_key = join(':', [
                $action->category,
                $action->name,
                $action->app,
            ]);
        }
    }

    public function generateReferralCode(\App\Action $action) {
        do {
            $code = uniqid();
            try {
                $this->storage->storeReferralCode($code, $action->user_id, $action->timestamp);
            } catch (\Exception $e) {
                // TODO more specific duplicate exception
                $code = '';
            }
        } while (empty($code));

        return $code;
    }

    public function storeAction(\App\Action $action) {
        $this->storage->storeAction($action);
    }

    public function generateUserId() {
        return uniqid('', true);
    }
}
