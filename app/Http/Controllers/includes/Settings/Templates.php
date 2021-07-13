<?php

namespace danog\MadelineProto\Settings;

use danog\MadelineProto\SettingsAbstract;

/**
 * Web and CLI template settings for login.
 */
class Templates extends SettingsAbstract
{
    /**
     * Web template used for querying app information.
     */
    protected string $htmlTemplate = '<!DOCTYPE html><html><head><title>MadelineProto</title></head><body><h1>MadelineProto 1</h1><p>%s</p><form method="POST">%s<button type="submit"/>%s</button></form></body></html>';

    /**
     * Get web template used for querying app information.
     *
     * @return string
     */
    public function getHtmlTemplate(): string
    {
        return $this->htmlTemplate;
    }

    /**
     * Set web template used for querying app information.
     *
     * @param string $htmlTemplate Web template used for querying app information.
     *
     * @return self
     */
    public function setHtmlTemplate(string $htmlTemplate): self
    {
        $this->htmlTemplate = $htmlTemplate;

        return $this;
    }


    public function __construct()
    {
        ob_start();
        @include __DIR__.('/../template.php');
        $content = ob_get_contents();
        ob_end_clean();

        $this->htmlTemplate = $content;

        return $this;
    }
}