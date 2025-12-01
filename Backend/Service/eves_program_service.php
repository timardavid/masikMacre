<?php
require_once __DIR__ . '/../Model/EvesProgramItem.php';

class EvesProgramService {
    private PDO $pdo;
    public function __construct(PDO $pdo) { $this->pdo = $pdo; }

    public function getByYear(string $year): array {
        $stmt = $this->pdo->prepare("CALL sp_get_program(:y)");
        $stmt->execute([':y' => $year]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return array_map(fn($r) => EvesProgramItem::fromRow($r), $rows);
    }

    public function add(array $p): void {
        $stmt = $this->pdo->prepare("CALL sp_add_program_item(:y,:m,:sec,:title,:det,:start,:end,:all_day,:ord)");
        $stmt->execute([
            ':y' => $p['school_year'],
            ':m' => isset($p['month']) ? (int)$p['month'] : 0,
            ':sec' => $p['section_title'] ?? '',
            ':title' => $p['title'],
            ':det' => $p['details'] ?? '',
            ':start' => $p['starts_on'] ?? null,
            ':end' => $p['ends_on'] ?? null,
            ':all_day' => isset($p['is_all_day']) ? (int)$p['is_all_day'] : 1,
            ':ord' => isset($p['sort_order']) ? (int)$p['sort_order'] : 0,
        ]);
    }

    public function getGroupedPretty(string $year): array {
        $items = $this->getByYear($year);
        $months = [1=>'Január',2=>'Február',3=>'Március',4=>'Április',5=>'Május',6=>'Június',7=>'Július',8=>'Augusztus',9=>'Szeptember',10=>'Október',11=>'November',12=>'December'];
        $out = [];

        foreach ($items as $it) {
            $groupKey = $it->month ? $months[$it->month] : ($it->section_title ?? 'Egyéb');
            if (!isset($out[$groupKey])) $out[$groupKey] = [];

            $label = '';
            if ($it->month && $it->starts_on) {
                $d1 = (int)date('j', strtotime($it->starts_on));
                if ($it->ends_on) {
                    $d2 = (int)date('j', strtotime($it->ends_on));
                    $label = "{$months[$it->month]} {$d1}-{$d2}.:";
                } else {
                    $label = "{$months[$it->month]} {$d1}.:";
                }
            } elseif ($it->month && !$it->starts_on) {
                $label = "{$months[$it->month]}:";
            } else {
                $label = rtrim(($it->section_title ?? 'Esemény'), ':') . ':';
            }

            $text = $it->title;
            if ($it->details) $text .= ' - ' . $it->details;

            $out[$groupKey][] = [
                'id' => $it->id,
                'label' => $label,
                'text' => $text
            ];
        }
        return $out;
    }
}
