<?php

/*
 *
 *  ____  _             _         _____
 * | __ )| |_   _  __ _(_)_ __   |_   _|__  __ _ _ __ ___
 * |  _ \| | | | |/ _` | | '_ \    | |/ _ \/ _` | '_ ` _ \
 * | |_) | | |_| | (_| | | | | |   | |  __/ (_| | | | | | |
 * |____/|_|\__,_|\__, |_|_| |_|   |_|\___|\__,_|_| |_| |_|
 *                |___/
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the MIT License.
 *
 * @author  Blugin team
 * @link    https://github.com/Blugin
 * @license https://www.gnu.org/licenses/mit MIT License
 *
 *   (\ /)
 *  ( . .) ♥
 *  c(")(")
 */

declare(strict_types=1);

namespace blugin\tool\datacleaner;

use pocketmine\command\Command;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class DataCleaner extends PluginBase implements Listener{
    public function onEnable() : void{
        $this->getServer()->getCommandMap()->register(strtolower($this->getName()), $this->makeCommand('', new class() implements CommandExecutor{
            public function onCommand(CommandSender $sender, Command $command, $label, array $params) : bool{
                if((bool) Server::getInstance()->getProperty("plugins.legacy-data-dir", true)){
                    $sender->sendMessage(TextFormat::YELLOW . "It doesn't work if the legacy folder setting is turned on");
                    return true;
                }
                $dataFolder = Server::getInstance()->getDataPath() . "plugin_data" . DIRECTORY_SEPARATOR;
                foreach(array_diff(scandir($dataFolder), [".", ".."]) as $folderName){
                    $dirname = $dataFolder . $folderName;
                    if(is_readable($dirname) && count(scandir($dirname)) == 2){
                        rmdir($dirname);
                    }
                }
                $sender->sendMessage("Removed empty plugin data folder");
                return true;
            }
        }, "Remove empty plugin data folder"));
    }

    public function makeCommand(string $name, CommandExecutor $executor, string $description = "") : PluginCommand{
        $command = new PluginCommand($name, $this);
        $command->setExecutor($executor);
        $command->setDescription($description);
        return $command;
    }
}
