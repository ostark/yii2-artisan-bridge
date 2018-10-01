<?php

namespace ostark\Yii2ArtisanBridge;

class ActionGroup
{
    public $name;

    protected $actions = [];

    protected $optionAliases = [];

    protected $helpSummary;

    protected $defaultAction;


    public function __construct(string $name, string $summary)
    {
        $this->name        = $name;
        $this->helpSummary = $summary;
    }

    public function setActions(array $actions = []): ActionGroup
    {
        $this->actions = $actions;

        return $this;
    }

    public function setOptions(array $options = []): ActionGroup
    {
        $this->optionAliases = $options;

        return $this;

    }


    public function setDefaultAction(string $name): ActionGroup
    {
        if (!isset($this->getActions()[$name])) {
            throw new \InvalidArgumentException("Unknown action name '$name'");
        }

        $this->defaultAction = $name;

        return $this;

    }


    public function getActions(): array
    {
        return $this->actions;
    }

    public function getOptions(): array
    {
        return $this->optionAliases;
    }

    public function getHelpSummary(): string
    {
        return $this->helpSummary;
    }

    public function getDefaultAction(): string
    {
        return $this->defaultAction;
    }

}
