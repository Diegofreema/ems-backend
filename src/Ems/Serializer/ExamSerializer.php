<?php
declare(strict_types=1);

namespace App\Ems\Serializer;

use Cake\Datasource\EntityInterface;

/**
 * Wire shapes for the academics module (document.md §3.1–§3.4): exams, sittings,
 * the score matrix, papers, releases, the question bank and assessments. The
 * computed read models (gradesheet, broadsheet, report card, transcript) are
 * assembled by App\Ems\Academics — this file owns only the stored-entity shapes.
 */
final class ExamSerializer
{
    public static function exam(EntityInterface $e): array
    {
        return [
            'id' => (string)$e->id,
            'schoolId' => (string)$e->school_id,
            'title' => (string)$e->title,
            'session' => (string)$e->session,
            'term' => (string)$e->term,
            'startDate' => Wire::date($e->start_date),
            'endDate' => Wire::date($e->end_date),
            'status' => (string)$e->status,
            'caMax' => (int)$e->ca_max,
            'examMax' => (int)$e->exam_max,
        ];
    }

    public static function schedule(EntityInterface $s): array
    {
        return [
            'id' => (string)$s->id,
            'schoolId' => (string)$s->school_id,
            'examId' => (string)$s->exam_id,
            'subject' => (string)$s->subject,
            'subjectId' => (string)$s->subject_id,
            'level' => (string)$s->level,
            'date' => Wire::date($s->date),
            'startTime' => (string)$s->start_time,
            'endTime' => (string)$s->end_time,
            'venue' => (string)$s->venue,
        ];
    }

    public static function grade(EntityInterface $g): array
    {
        return [
            'id' => (string)$g->id,
            'schoolId' => (string)$g->school_id,
            'examId' => (string)$g->exam_id,
            'studentId' => (string)$g->student_id,
            'classGroupId' => (string)$g->class_group_id,
            'subject' => (string)$g->subject,
            'subjectId' => (string)$g->subject_id,
            'ca' => Wire::num($g->ca),
            'exam' => Wire::num($g->exam),
        ];
    }

    public static function paper(EntityInterface $p): array
    {
        $ids = $p->question_ids;
        if (is_string($ids)) {
            $ids = json_decode($ids, true);
        }

        return [
            'id' => (string)$p->id,
            'schoolId' => (string)$p->school_id,
            'examId' => (string)$p->exam_id,
            'subject' => (string)$p->subject,
            'subjectId' => (string)$p->subject_id,
            'level' => (string)$p->level,
            'questionIds' => array_values(array_map('strval', is_array($ids) ? $ids : [])),
        ];
    }

    public static function release(EntityInterface $r): array
    {
        $out = [
            'id' => (string)$r->id,
            'schoolId' => (string)$r->school_id,
            'examId' => (string)$r->exam_id,
            'version' => (int)$r->version,
            'releasedOn' => Wire::date($r->released_on),
            'releasedBy' => (string)$r->released_by,
            'status' => (string)$r->status,
        ];
        if ($r->superseded_on !== null) {
            $out['supersededOn'] = Wire::date($r->superseded_on);
        }
        if ($r->reason !== null && (string)$r->reason !== '') {
            $out['reason'] = (string)$r->reason;
        }
        if ($r->scheme_version !== null) {
            $out['schemeVersion'] = (int)$r->scheme_version;
        }

        return $out;
    }

    public static function question(EntityInterface $q): array
    {
        $options = $q->options;
        if (is_string($options)) {
            $options = json_decode($options, true);
        }

        return [
            'id' => (string)$q->id,
            'schoolId' => (string)$q->school_id,
            'subject' => (string)$q->subject,
            'subjectId' => (string)$q->subject_id,
            'level' => (string)$q->level,
            'type' => (string)$q->type,
            'difficulty' => (string)$q->difficulty,
            'topic' => (string)$q->topic,
            'text' => (string)$q->text,
            'options' => array_values(array_map('strval', is_array($options) ? $options : [])),
            'answer' => (string)$q->answer,
            'marks' => (int)$q->marks,
        ];
    }

    public static function assessment(EntityInterface $a): array
    {
        $out = [
            'id' => (string)$a->id,
            'schoolId' => (string)$a->school_id,
            'examId' => (string)$a->exam_id,
            'classGroupId' => (string)$a->class_group_id,
            'subject' => (string)$a->subject,
            'subjectId' => (string)$a->subject_id,
            'name' => (string)$a->name,
            'maximum' => (int)$a->maximum,
        ];
        if ($a->due_on !== null) {
            $out['dueOn'] = Wire::date($a->due_on);
        }
        $out['status'] = (string)$a->status;
        $out['createdBy'] = (string)$a->created_by;
        $out['createdOn'] = Wire::date($a->created_on);

        return $out;
    }

    /**
     * AssessmentSummary = Assessment + score coverage over the roster.
     */
    public static function assessmentSummary(EntityInterface $a, int $rosterCount, int $scoredCount): array
    {
        return self::assessment($a) + [
            'scoredCount' => $scoredCount,
            'rosterCount' => $rosterCount,
            'missingCount' => max(0, $rosterCount - $scoredCount),
        ];
    }

    private function __construct()
    {
    }
}
