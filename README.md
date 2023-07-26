# libEco

**libEco is a PocketMine-MP virion that makes it easy to use the API of economy plugins!**

## Installation
You can get the compiled .phar file on poggit by clicking [here](https://github.com/Amitminer/libEco/releases/download/stable/libEco_v4.0.0.phar).

## Supported Economy Plugins
Currently, this library supports the following economy plugins:
- BedrockEconomy
- EconomyAPI

## Usage
LibEconomy makes using the economy plugin APIs easier!

### Check if any economy plugin is installed

```php
use davidglitch04\libEco\libEco;

if (libEco::isInstall()) {
    // An economy plugin is installed
} else {
    // No economy plugin found
}
```

### Get the money of a player

```php
use davidglitch04\libEco\libEco;

libEco::myMoney($player, static function(float $money) : void {
    var_dump($money);
});
```

### Add money to a player

```php
use davidglitch04\libEco\libEco;

libEco::addMoney($player, $amount);
```

### Reduce money from a player

```php
use davidglitch04\libEco\libEco;

libEco::reduceMoney($player, $amount, static function(bool $success) : void {
    if ($success) {
        // TODO: If reducing money was successful
    } else {
        // TODO: If reducing money failed
    }
});
```

### Retrieve sorted balances

```php
use davidglitch04\libEco\libEco;

// Parameters: $limit, $offset, $callback
libEco::getAllBalance(10, 0, static function(bool $success, array $data) : void {
    if ($success) {
        // Handle the sorted balances in the $data array
        // Each element in the $data array contains player name and balance
    } else {
        // Failed to retrieve balances
    }
});
```
