<?php

declare(strict_types=1);

namespace Duyler\EventBus\Service;

use Duyler\EventBus\Bus\Bus;
use Duyler\EventBus\Bus\CompleteAction;
use Duyler\EventBus\Collection\ActionCollection;
use Duyler\EventBus\Collection\SubscriptionCollection;
use Duyler\EventBus\BusConfig;
use Duyler\EventBus\Dto\Subscription;
use Duyler\EventBus\Enum\ResultStatus;
use InvalidArgumentException;

readonly class SubscriptionService
{
    public function __construct(
        private SubscriptionCollection $subscriptionCollection,
        private ActionCollection $actionCollection,
        private Bus $bus,
        private BusConfig $config,
    ) {}

    public function addSubscription(Subscription $subscription): void
    {
        if ($this->actionCollection->isExists($subscription->actionId) === false) {
            throw new InvalidArgumentException('Action ' . $subscription->actionId . ' not registered in the bus');
        }

        if ($this->actionCollection->isExists($subscription->subjectId) === false) {
            if ($this->config->enableTriggers === false) {
                throw new InvalidArgumentException(
                    'Subscribed action ' . $subscription->subjectId . ' not registered in the bus'
                );
            }
        }

        $this->subscriptionCollection->save($subscription);
    }

    public function subscriptionIsExists(Subscription $subscription): bool
    {
        return $this->subscriptionCollection->isExists($subscription);
    }

    public function resolveSubscriptions(CompleteAction $completeAction): void
    {
        if ($completeAction->action->silent) {
            return;
        }

        $subscriptions = $this->subscriptionCollection->getSubscriptions(
            $completeAction->action->id,
            $completeAction->result->status
        );

        foreach ($subscriptions as $subscription) {
            $action = $this->actionCollection->get($subscription->actionId);

            $this->bus->doAction($action);
        }
    }

    public function getSubscriptions(string $actionId, ResultStatus $status): array
    {
        return $this->subscriptionCollection->getSubscriptions($actionId, $status);
    }
}
