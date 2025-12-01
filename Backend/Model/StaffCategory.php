<?php
class StaffCategory {
    public $id;
    public $slug;
    public $name;
    public $description;
    public $display_order;
    public $active;

    public function __construct($row) {
        $this->id = $row['id'] ?? null;
        $this->slug = $row['slug'] ?? null;
        $this->name = $row['name'] ?? null;
        $this->description = $row['description'] ?? null;
        $this->display_order = $row['display_order'] ?? null;
        $this->active = $row['active'] ?? 1;
    }
}
