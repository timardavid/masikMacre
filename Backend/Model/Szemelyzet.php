<?php
class Szemelyzet {
    private ?int $id;
    private string $name;
    private string $role_name;
    private string $slug;
    private int $is_active;
    private string $created_at;

    public function __construct(?int $id, string $name, string $role_name, string $slug, int $is_active, string $created_at) {
        $this->id = $id;
        $this->name = $name;
        $this->role_name = $role_name;
        $this->slug = $slug;
        $this->is_active = $is_active;
        $this->created_at = $created_at;
    }

    public static function fromArray(array $row): self {
        return new self(
            $row['id'] ?? null,
            $row['name'],
            $row['role_name'],
            $row['slug'],
            (int)($row['is_active'] ?? 1),
            $row['created_at'] ?? ''
        );
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'role_name' => $this->role_name,
            'slug' => $this->slug,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at
        ];
    }
}
