<?php
class Enrollment {
    public string $school_year;
    public string $period_text;
    public ?string $start_date;
    public ?string $end_date;
    public string $status;
    public ?string $notice;
    public array $documents;
    public ?string $mandatory_condition;
    public ?string $optional_condition;
    public ?string $signature_place_date;
    public ?string $signature_name;
    public ?string $signature_title;
    public ?string $updated_at;

    public function __construct(array $row) {
        $this->school_year          = $row['school_year'];
        $this->period_text          = $row['period_text'];
        $this->start_date           = $row['start_date'];
        $this->end_date             = $row['end_date'];
        $this->status               = $row['status'];
        $this->notice               = $row['notice'];
        $this->documents            = !empty($row['documents']) 
                                        ? explode("\n", $row['documents']) 
                                        : [];
        $this->mandatory_condition  = $row['mandatory_condition'];
        $this->optional_condition   = $row['optional_condition'];
        $this->signature_place_date = $row['signature_place_date'];
        $this->signature_name       = $row['signature_name'];
        $this->signature_title      = $row['signature_title'];
        $this->updated_at           = $row['updated_at'];
    }
}
