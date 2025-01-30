<?php

namespace Frontkom\SemesterAndYear;

/**
 * Hopefully a useful value object.
 */
class SemesterAndYear {

  /**
   * These are accepted.
   */
  private const SEMESTER_VALUES = [
    'H',
    'V',
  ];

  /**
   * Full names.
   */
  private const SEMESTER_NAMES = [
    'H' => 'HØST',
    'V' => 'VÅR',
  ];

  /**
   * The actual 4-digit year.
   *
   * @var int
   */
  private int $year;

  /**
   * The semester.
   *
   * Currently, either H or V, meaning autumn or spring.
   *
   * @var string
   */
  private string $semester;

  /**
   * Construct one.
   */
  public function __construct($year, $semester) {
    // Support passing the "short form" of the year.
    $year = (int) $year;
    if ($year < 100) {
      $year = 2000 + $year;
    }
    $this->year = $year;

    // Also allow some other formats of the semesters.
    if (in_array($semester, self::SEMESTER_NAMES, TRUE)) {
      $semester = array_flip(self::SEMESTER_NAMES)[$semester];
    }

    assert(self::isAcceptableSemesterFormat($semester));
    $this->semester = $semester;
  }

  /**
   * Create from short format.
   */
  public static function createFromShortFormat(string $short_format): self {
    $parts = explode('-', $short_format);
    return new self($parts[1], $parts[0]);
  }

  /**
   * Calculate and compare.
   */
  public function isAfter(SemesterAndYear $semesterAndYear) : bool {
    if ($this->getYear() > $semesterAndYear->getYear()) {
      return TRUE;
    }
    if ($this->getYear() < $semesterAndYear->getYear()) {
      return FALSE;
    }
    // Same year, check semester.
    if ($this->getSemester() === 'H' && $semesterAndYear->getSemester() === 'V') {
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Check if two are the same.
   */
  public function equals(SemesterAndYear $semesterAndYear) : bool {
    return $this->getYear() === $semesterAndYear->getYear() && $this->getSemester() === $semesterAndYear->getSemester();
  }

  /**
   * Useful format we use from time to time.
   */
  public function getShortFormat(): string {
    return sprintf('%s-%d', $this->getSemester(), $this->getYear());
  }

  /**
   * Helper for validating format for semester.
   */
  public static function isAcceptableSemesterFormat($semester): bool {
    return in_array($semester, self::SEMESTER_VALUES, TRUE);
  }

  /**
   * Decrement one semester.
   */
  public function decrementSemester(): static {
    return $this->decrementSemesterWithSteps(1);
  }

  /**
   * Decrement helper.
   */
  public function decrementSemesterWithSteps(int $number_of_steps) : self {
    while ($number_of_steps) {
      $number_of_steps--;
      if ($this->semester === 'V') {
        $this->semester = 'H';
        $this->year--;
      }
      else {
        $this->semester = 'V';
      }
    }
    return $this;
  }

  /**
   * Increment with one.
   */
  public function incrementSemester(): static {
    return $this->incrementSemesterWithSteps(1);
  }

  /**
   * Increment helper.
   */
  public function incrementSemesterWithSteps(int $number_of_steps) : self {
    while ($number_of_steps) {
      $number_of_steps--;
      if ($this->semester === 'V') {
        $this->semester = 'H';
      }
      else {
        $this->semester = 'V';
        $this->year++;
      }
    }
    return $this;
  }

  /**
   * Semester code getter.
   *
   * @return string
   *   The semester code (single letter - H or V).
   */
  public function getSemester(): string {
    return $this->semester;
  }

  /**
   * Year getter.
   */
  public function getYear() : int {
    return $this->year;
  }

  /**
   * Full semester name getter.
   *
   * @return string
   *   The long semester code in Norwegian - VÅR or HØST
   */
  public function getSemesterName(): string {
    return self::SEMESTER_NAMES[$this->semester];
  }

}
