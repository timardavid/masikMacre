<?php
class EvesProgramItem {
    public ?int $id;
    public string $school_year;
    public ?int $month;          
    public ?string $section_title;
    public string $title;
    public ?string $details;
    public ?string $starts_on;    
    public ?string $ends_on;     
    public int $is_all_day;
    public int $sort_order;
    public string $created_at;

    public static function fromRow(array $r): self {
        $o = new self();
        $o->id = isset($r['id']) ? (int)$r['id'] : null;
        $o->school_year = $r['school_year'];
        $o->month = isset($r['month']) ? (int)$r['month'] : null;
        $o->section_title = $r['section_title'] ?? null;
        $o->title = $r['title'];
        $o->details = $r['details'] ?? null;
        $o->starts_on = $r['starts_on'] ?? null;
        $o->ends_on = $r['ends_on'] ?? null;
        $o->is_all_day = (int)($r['is_all_day'] ?? 1);
        $o->sort_order = (int)($r['sort_order'] ?? 0);
        $o->created_at = $r['created_at'] ?? '';
        return $o;
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'school_year' => $this->school_year,
            'month' => $this->month,
            'section_title' => $this->section_title,
            'title' => $this->title,
            'details' => $this->details,
            'starts_on' => $this->starts_on,
            'ends_on' => $this->ends_on,
            'is_all_day' => $this->is_all_day,
            'sort_order' => $this->sort_order,
            'created_at' => $this->created_at
        ];
    }
}
