<?php

declare(strict_types = 1);

namespace davidglitch04\libEco;

use Closure;
use cooldogedev\BedrockEconomy\api\legacy\ClosureContext;
use cooldogedev\BedrockEconomy\api\version\BetaBEAPI;
use onebone\economyapi\EconomyAPI;
use pocketmine\player\Player;
use pocketmine\Server as PMServer;

class libEco
{
    public const ECONOMYAPI = "EconomyAPI";
    public const BEDROCKECONOMYAPI = "BedrockEconomyAPI";

    /**
    * Get the installed economy plugin and its API instance, if available.
    *
    * @return array|null [string|null, object|null]
    */
    private static function getEconomy(): ?array {
        $economyAPI = PMServer::getInstance()->getPluginManager()->getPlugin('EconomyAPI');
        $bedrockEconomyAPI = PMServer::getInstance()->getPluginManager()->getPlugin('BedrockEconomy');

        if ($economyAPI !== null) {
            return [self::ECONOMYAPI,
                $economyAPI];
        } elseif ($bedrockEconomyAPI !== null) {
            return [self::BEDROCKECONOMYAPI,
                $bedrockEconomyAPI];
        }

        return null;
    }

    /**
    * Check if any compatible economy plugin is installed.
    *
    * @return bool
    */
    public static function isInstall(): bool
    {
        return !is_null(self::getEconomy()[0]);
    }

    /**
    * Retrieve the player's balance and execute the callback with the result.
    *
    * @param Player $player
    * @param Closure $callback
    * @return void
    */
    public static function myMoney(Player $player, Closure $callback): void {
        $economy = self::getEconomy();

        if ($economy[0] === self::ECONOMYAPI) {
            $money = $economy[1]->myMoney($player);
            assert(is_float($money));
            $callback($money);
        } elseif ($economy[0] === self::BEDROCKECONOMYAPI) {
            $economy[1]->getAPI()->getPlayerBalance($player->getName(), ClosureContext::create(static function (?int $balance) use ($callback): void {
                $callback($balance ?? 0);
            }));
        }
    }

    /**
    * Add the specified amount of money to the player's balance.
    *
    * @param Player $player
    * @param int $amount
    * @return void
    */
    public static function addMoney(Player $player, int $amount): void {
        if (self::getEconomy()[0] === self::ECONOMYAPI) {
            self::getEconomy()[1]->addMoney($player, $amount);
        } elseif (self::getEconomy()[0] === self::BEDROCKECONOMYAPI) {
            self::getEconomy()[1]->getAPI()->addToPlayerBalance($player->getName(), (int) $amount);
        }
    }

    /**
    * Reduce the specified amount of money from the player's balance and execute the callback with the result.
    *
    * @param Player $player
    * @param int $amount
    * @param Closure $callback
    * @return void
    */
    public static function reduceMoney(Player $player, int $amount, Closure $callback): void {
        if (self::getEconomy()[0] === self::ECONOMYAPI) {
            $callback(self::getEconomy()[1]->reduceMoney($player, $amount) === EconomyAPI::RET_SUCCESS);
        } elseif (self::getEconomy()[0] === self::BEDROCKECONOMYAPI) {
            self::getEconomy()[1]->getAPI()->subtractFromPlayerBalance($player->getName(), (int) ceil($amount), ClosureContext::create(static function (bool $success) use ($callback): void {
                $callback($success);
            }));
        }
    }

    /**
    * Retrieve the sorted balances and execute the callback with the result.
    *
    * @param int $limit
    * @param int $offset
    * @param Closure $callback
    * @return void
    */
    public static function getAllBalance(int $limit, int $offset, Closure $callback): void {
        $accountManager = BetaBEAPI::getInstance();
        $accountManager->getSortedBalances($limit, $offset)->onCompletion(
            function (array $data) use ($callback) {
                $callback(true, $data);
            },
            function () use ($callback) {
                $callback(false, []);
            }
        );
    }
}