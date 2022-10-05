<?php

namespace JsonTranslationHelper\Command;

use Illuminate\Console\Command;

class TranslationHelperCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'translation:scan';

    /**
     * @var string
     */
    protected $description = 'Searches for translation keys – inserts into JSON translation files.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $translationKeys  = $this->findProjectTranslationsKeys();
        $translationFiles = $this->getProjectTranslationFiles();

        foreach ($translationFiles as $file) {
            $translationData = $this->getAlreadyTranslatedKeys($file);
            $added           = [];

            $this->line('Language: ' . str_replace('.json', '', basename($file)));

            foreach ($translationKeys as $key) {
                if (!isset($translationData[$key])) {
                    $translationData[$key] = '';
                    $added[]               = $key;

                    $this->warn(" - Added: {$key}");
                }
            }

            if ($added) {
                $this->line('Updating translation file...');

                $this->writeNewTranslationFile($file, $translationData);

                $this->info('Translation file have been updated!');
            } else {
                $this->warn('Nothing new found for this language.');
            }

            $this->line('');
        }
    }

    /**
     * @return array
     */
    private function findProjectTranslationsKeys()
    {
        $allKeys          = [];
        $viewsDirectories = config('translation-helper.scan_directories');
        $fileExtensions   = config('translation-helper.file_extensions');

        foreach ($viewsDirectories as $directory) {
            foreach ($fileExtensions as $extension) {
                $this->getTranslationKeysFromDir($allKeys, $directory, $extension);
            }
        }

        ksort($allKeys);

        return $allKeys;
    }

    /**
     * @param array $keys
     * @param string $dirPath
     * @param string $fileExt
     */
    private function getTranslationKeysFromDir(&$keys, $dirPath, $fileExt = 'php')
    {
        $files = glob_recursive("{$dirPath}/*.{$fileExt}", GLOB_BRACE);

        foreach ($files as $file) {
            $content = $this->getSanitizedContent($file);

            foreach (config('translation-helper.translation_methods') as $translationMethod) {
                $this->getTranslationKeysFromFunction($keys, $translationMethod, $content);
            }
        }
    }

    /**
     * @param array $keys
     * @param string $functionName
     * @param string $content
     */
    private function getTranslationKeysFromFunction(&$keys, $functionName, $content)
    {
        $matches = [];

        preg_match_all("#{$functionName}\(\s*\'(.*?)\'\s*[\)\,]#", $content, $matches);

        if (! empty($matches)) {
            foreach ($matches[1] as $match) {
                $match = str_replace('"', "'", str_replace("\'", "'", $match));

                if (! empty($match)) {
                    $keys[$match] = $match;
                }
            }
        }
    }

    /**
     * @return array
     */
    private function getProjectTranslationFiles()
    {
        $path  = config('translation-helper.output_directory');
        $files = glob("{$path}/*.json", GLOB_BRACE);

        return $files;
    }

    /**
     * @param string $filePath
     * @return array
     */
    private function getAlreadyTranslatedKeys($filePath)
    {
        $current = json_decode(file_get_contents($filePath), true);

        ksort($current);

        return $current;
    }

    /**
     * @param string $filePath
     * @param array $translations
     */
    private function writeNewTranslationFile($filePath, $translations)
    {
        file_put_contents($filePath, json_encode($translations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    /**
     * @param string $filePath
     * @return string
     */
    private function getSanitizedContent($filePath)
    {
        return str_replace("\n", ' ', file_get_contents($filePath));
    }
}
