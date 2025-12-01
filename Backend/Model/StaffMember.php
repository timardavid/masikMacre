<?php
class StaffMember {
    public $id;
    public $category_id;
    public $category_name;
    public $name;
    public $role_title;
    public $bio;
    public $photo_url;
    public $email;
    public $phone;
    public $is_featured;
    public $sort_order;
    public $active;

    public function __construct($row) {
        $this->id = $row['id'] ?? null;
        $this->category_id = $row['category_id'] ?? null;
        $this->category_name = $row['category_name'] ?? null;
        $this->name = $row['name'] ?? null;
        $this->role_title = $row['role_title'] ?? null;
        $this->bio = $row['bio'] ?? null;
        $this->photo_url = $row['photo_url'] ?? null;
        $this->email = $row['email'] ?? null;
        $this->phone = $row['phone'] ?? null;
        $this->is_featured = $row['is_featured'] ?? null;
        $this->sort_order = $row['sort_order'] ?? null;
        $this->active = $row['active'] ?? 1;
    }
}
