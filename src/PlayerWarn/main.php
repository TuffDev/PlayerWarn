<?php
namespace playerwarn;

use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\command\Command;
use pocketmine\utils\Config;

class Main extends PluginBase implements Listener{
    public $temp = [];
    public function onEnable(){
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->config = new Config($this->getDataFolder(). "config.yml", Config::YAML);
        $this->getConfig()->setDefaults(array("maximum number of warnings:" => 3));
        $this->getConfig()->save();
    }

    public function onCommand(CommandSender $sender, Command $command, $label, array $args) {
        switch ($command->getName()) {
            case "warn":
                if ($sender->isOp()) {
                    if (isset($args[0]) and isset($args[1])) { //DEBUG
                        $players = $this->getServer()->getOnlinePlayers(); 
                        foreach($players as $f) {
                        if ($f->getName() == $args[0]){
                          $this->warn($args[0], $args[1], $sender, $f); 
                          break; // DEBUG
                            }
                            else {
                         $sender->sendMessage("Player does not exist"); //DEBUG
                        break;
                            }
                        }
                    }
                    else {
                        $sender->sendMessage("Usage: /warn [playername] [reason]");
                        break;
                    }
                }
                else {
                    $sender->sendMessage("You do not have permissions to run this command");
                    break;
                }
                break;
            case "warninglist";
                    if (!isset($args[0])) {
                        $sender->sendMessage("Usage: /warninglist [online player name]");
                    }
                    else {
                        if(($this->getConfig()->get(strtolower($args[0]))) !== false){
                            $warnings = $this->getConfig()->get(strtolower($args[0]));
                            $list = implode(", ", $warnings);
                            $sender->sendMessage("Warnings:" . $list);
                        }
                        else {
                            $sender->sendMessage("Player does not exist or has no warnings");
                        }
                    }
                    break;
            case "warningclear";
                if (isset($args[0])) {
                    if ($sender->isOp()) {
                    $this->getConfig()->reload();
                    $this->getConfig()->set(strtolower($args[0]), array());
                    $sender->sendMessage("Player's warning's cleared!");
                    $this->getConfig()->save();
                    }
                    else {
                        $sender->sendMessage("You do not have permissions to run this command");
                    }
                }
                else {
                    $sender->sendMessage("Usage: /warningclear [full player name]");
                }
                break;
        }       
    }
    public function warn($player, $reason, $sender, $playerobj){
        $maxwarn = $this->getConfig()->get("maximum number of warnings:");
        $sender->sendMessage("got maxwarn"); //debug
        // $ip = $playerobj->getAddress(); DEBUG
            for ($i = 0; $i < $maxwarn - 1; $i++) {
                $sender->sendMessage("loop " . $i);
                if ($i > $maxwarn - 1) {
                    $sender->getServer()->getIPBans()->addBan($ip, $reason, null, $sender->getName());
                    $sender->sendMessage("Player banned");
                    break;
                }
                else {
                    $warning = "Warnings";
                    $config = $this->getConfig()->getAll();
                    if ($config[$warning][$player][strval($i)] == false) {
                    $this->getConfig()->set([$warning][$player][strval($i)], $reason);
                    $playerobj->sendMessage("You have been issued a warning");
                    $playerobj->sendMessage("Reason: " . $reason);
                    $playerobj->sendMessage("You have: " . $i + 1 . " warnings!");
                    $sender->sendMessage($player . " has been warned!");
                    $this->getConfig()->save();
                    break;
                    }
              }
            }
    }
    public function _isset($val) {
        return isset($val);
    }
    public function onDisable() {
        $this->getConfig()->save();
    }
}
