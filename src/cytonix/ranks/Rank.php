<?php namespace cytonix\ranks;

class Rank {

    /** @var string */
    private string $name;

    /** @var string */
    private string $fancyName;

    /** @var array<string> */
    private array $permissions;

    public function __construct(string $name, string $fancyName, array $permissions) {
        $this->name = $name;
        $this->fancyName = $fancyName;
        $this->permissions = $permissions;
    }

    public function getName() : string {
        return $this->name;
    }

    public function getFancyName() : string {
        return $this->fancyName;
    }

    public function getPermissions() : array {
        return $this->permissions;
    }

}