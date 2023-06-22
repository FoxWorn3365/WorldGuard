<?php
/*
 * WorldGuard for PocketMine-MP
 * Copyright (C) 2023-now FoxWorn3365 (https://github.com/FoxWorn3365)
 * 
 * This file is apart of the WorldGuard for PMMP project!
 * 
 * Contributors:
 * 
 * Relased under the AGPL-3.0 license (https://github.com/FoxWorn3365/WorldGuard/blob/main/LICENSE)
 * Please respect the license!
 * 
 * File: /Translator.php
 * Description: Manage translations
 */

namespace FoxWorn3365\WorldGuard;

class Translator {
    protected object $globaltranslations;
    protected object $translations;
    protected string $language;
    protected array $languages = [
        'it',
        'en'
    ];

    function __construct(string $language) {
        if (!in_array($language, $this->languages)) {
            $this->language = 'en';
        } else {
            $this->language = $language;
        }
        $this->load();
    }

    protected function load() : void {
        $this->globaltranslations = json_decode(file_get_contents(__DIR__ . "/languages.json"));
        $this->translations = $this->globaltranslations->{$this->language};
    }

    public function get(string $key, array $replacements = []) : string {
        $translation = @$this->translations->{$key} ?? "NOT FOUND FOR LANGUAGE {$this->language}";
        if ($replacements !== []) {
            foreach ($replacements as $key => $value) {
                $translation = str_replace("%{$key}%", $value, $translation);
            }
        }
        return $translation;
    }
}