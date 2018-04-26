<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 13.07.17
 * Time: 14:48
 */

namespace rollun\delovod;

use rollun\installer\Install\InstallerAbstract;

class ApiClientInstaller extends InstallerAbstract
{

    /**
     * install
     * @return array
     */
    public function install()
    {
        $config = [
            "delovod" => [
            ]
        ];

        $config["delovod"]["apiKey"] = $this->consoleIO->ask("Copy here the delovod apiKey: ");

        return $config;
    }

    /**
     * Clean all installation
     * @return void
     */
    public function uninstall()
    {
        // TODO: Implement uninstall() method.
    }

    /**
     * Return string with description of installable functional.
     * @param string $lang ; set select language for description getted.
     * @return string
     */
    public function getDescription($lang = "en")
    {
        switch ($lang) {
            case "ru":
                $description = "Предоставляет клиента для (api)системы delovod.";
                break;
            default:
                $description = "Does not exist.";
        }
        return $description;
    }

    public function isInstall()
    {
        $config = $this->container->get("config");
        return isset($config['delovod']['apiKey']);
    }

}
